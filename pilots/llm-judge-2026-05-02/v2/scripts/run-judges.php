<?php
/**
 * SPARRING LLM-judge pilot V2 — judge runner.
 *
 * Per V2 pre-registration (locked tag: v2-prereg-2026-05-03):
 * - 2 cases (case-a organizational vocabulary; case-b CS101 curriculum)
 * - 2 conditions per case (baseline / sparring)
 * - X/Y anonymization per case is locked in rd_eval_cases (case-a: X=baseline, Y=sparring; case-b: X=sparring, Y=baseline)
 * - Position-flip pass per pair (Wang et al. 2023 mitigation): pass1=DB-order, pass2=flipped
 * - 3 cross-vendor judges: Anthropic claude-sonnet-4-6, OpenAI gpt-4o, xAI grok-3
 * - 6 criteria (C1, C2, C3, C4, C5a Factuality, C5b Engagement-with-source), 1-7 scale, behavioral anchors at every level
 * - Total calls: 2 cases x 3 judges x 2 position-passes = 12
 *
 * The rubric prompt is locked here (pre-registration §4 + design.md V2 rubric anchors).
 * Output: scores per (case, judge, pass, criterion, side) saved to JSON.
 *
 * This script is intended to be run on the VPS where rd_eval_cases lives and the API keys are configured.
 * Local invocation: scp this file to /tmp on VPS, run via /usr/local/bin/php, scp the JSON output back.
 */

declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

// ---------- env ----------

$ENV_PATH = '/home/m73lxyuuja64/public_html/nonprod.lifspel.com/.env';
foreach (file($ENV_PATH) as $line) {
    if (preg_match('/^([A-Z_]+)=(.*)/', trim($line), $m)) {
        putenv("$m[1]=$m[2]");
    }
}

$ANTHROPIC_KEY = getenv('ANTHROPIC_API_KEY') ?: die("missing ANTHROPIC_API_KEY\n");
$OPENAI_KEY    = getenv('OPENAI_API_KEY')    ?: die("missing OPENAI_API_KEY\n");
$GROK_KEY      = getenv('GROK_API_KEY')      ?: die("missing GROK_API_KEY\n");

// ---------- DB connection: read V2 cases from rd_eval_cases ----------

require_once '/home/m73lxyuuja64/public_html/nonprod.lifspel.com/vendor/autoload.php';
$config = require '/home/m73lxyuuja64/public_html/nonprod.lifspel.com/src/config/config.php';
$db = Lifspel\Services\Database::getConnection($config);

$rows = $db->query("
    SELECT slug, title, question, evidence_summary,
           answer_x_text, answer_y_text, answer_x_condition, answer_y_condition
    FROM rd_eval_cases
    WHERE schema_version = 'v2' AND status = 'open'
    ORDER BY id
")->fetchAll(PDO::FETCH_ASSOC);

if (count($rows) === 0) {
    die("No V2 cases found in rd_eval_cases.\n");
}

$cases = [];
foreach ($rows as $r) {
    $case_id = str_contains($r['slug'], 'case-a') ? 'case-a' : (str_contains($r['slug'], 'case-b') ? 'case-b' : $r['slug']);
    $cases[$case_id] = [
        'slug' => $r['slug'],
        'title' => $r['title'],
        'question' => $r['question'],
        'evidence_summary' => $r['evidence_summary'],
        'pass1' => [
            'X_text' => $r['answer_x_text'],
            'Y_text' => $r['answer_y_text'],
            'X_condition' => $r['answer_x_condition'],
            'Y_condition' => $r['answer_y_condition'],
        ],
        'pass2' => [
            'X_text' => $r['answer_y_text'],  // FLIPPED for position-randomization
            'Y_text' => $r['answer_x_text'],
            'X_condition' => $r['answer_y_condition'],
            'Y_condition' => $r['answer_x_condition'],
        ],
    ];
}

fprintf(STDERR, "Loaded %d V2 cases:\n", count($cases));
foreach ($cases as $cid => $c) {
    fprintf(STDERR, "  %s: %s (X=%s, Y=%s)\n", $cid, $c['slug'], $c['pass1']['X_condition'], $c['pass1']['Y_condition']);
}

// ---------- judges ----------

$judges = [
    'anthropic' => [
        'model' => 'claude-sonnet-4-6',
        'endpoint' => 'https://api.anthropic.com/v1/messages',
        'auth_header' => 'x-api-key: ' . $ANTHROPIC_KEY,
        'extra_headers' => ['anthropic-version: 2023-06-01'],
        'payload' => function (string $system, string $user) {
            return ['model' => 'claude-sonnet-4-6', 'max_tokens' => 400, 'system' => $system, 'messages' => [['role' => 'user', 'content' => $user]]];
        },
        'extract' => function (array $response): string {
            return $response['content'][0]['text'] ?? '';
        },
    ],
    'openai' => [
        'model' => 'gpt-4o',
        'endpoint' => 'https://api.openai.com/v1/chat/completions',
        'auth_header' => 'Authorization: Bearer ' . $OPENAI_KEY,
        'extra_headers' => [],
        'payload' => function (string $system, string $user) {
            return ['model' => 'gpt-4o', 'max_tokens' => 400, 'messages' => [['role' => 'system', 'content' => $system], ['role' => 'user', 'content' => $user]]];
        },
        'extract' => function (array $response): string {
            return $response['choices'][0]['message']['content'] ?? '';
        },
    ],
    'xai' => [
        'model' => 'grok-3',
        'endpoint' => 'https://api.x.ai/v1/chat/completions',
        'auth_header' => 'Authorization: Bearer ' . $GROK_KEY,
        'extra_headers' => [],
        'payload' => function (string $system, string $user) {
            return ['model' => 'grok-3', 'max_tokens' => 400, 'messages' => [['role' => 'system', 'content' => $system], ['role' => 'user', 'content' => $user]]];
        },
        'extract' => function (array $response): string {
            return $response['choices'][0]['message']['content'] ?? '';
        },
    ],
];

// ---------- V2 rubric prompt (locked at v2-prereg-2026-05-03) ----------

$RUBRIC_SYSTEM = 'You evaluate decision recommendations on six criteria using a 1-7 anchored scale. Score per criterion only. Do not state which you prefer overall. Output ONLY the scores in the exact format requested. No preamble, no explanation, no analysis text.';

function build_user_prompt(string $question, string $evidence_summary, string $answer_x, string $answer_y): string {
    return <<<PROMPT
You are evaluating two decision recommendations on the same question. You don't know which recommendation came from which approach. Score each on the 6 criteria below using the 1-7 anchored scale provided. Do not state which you prefer overall — score per criterion.

QUESTION: {$question}

EVIDENCE BASE SUMMARY: {$evidence_summary}

ANSWER X:
{$answer_x}

ANSWER Y:
{$answer_y}

CRITERIA AND ANCHORS (1-7 scale, behavioral anchors at every level):

C1. Verifiable artifact citation — does the recommendation cite specific evidence from the materials it was given?
  1 = No citations of any kind; recommendation is unsupported assertion.
  2 = One vague reference; no specific artifact (file, section, line, URL).
  3 = At least one specific artifact cited; most claims unsupported.
  4 = Roughly half of load-bearing claims cite specific artifacts.
  5 = Most load-bearing claims cite specific artifacts.
  6 = All load-bearing claims cite specific artifacts; minor supporting claims may not.
  7 = Every claim, load-bearing or supporting, cites a specific artifact.

C2. Substantive vs theatrical concerns — are the concerns real risks rooted in evidence, or generic-sounding hedges?
  1 = Concerns are entirely generic hedges.
  2 = Concerns gesture at risks but provide no evidence chain.
  3 = Some concerns are evidence-rooted; most are generic.
  4 = Roughly half of concerns are evidence-rooted with traceable reasoning.
  5 = Most concerns are evidence-rooted; minor ones may be generic.
  6 = All substantive concerns are evidence-rooted; rare generic concerns add color.
  7 = Every concern names a specific evidence chain and a specific risk; no theatrical hedging.

C3. Missed real concerns — does it surface concerns a careful reader would identify, or miss obvious ones?
  1 = Misses every major concern a careful reader would surface.
  2 = Surfaces 1 of the major concerns; misses the others.
  3 = Surfaces some concerns but misses obvious ones.
  4 = Surfaces most concerns; misses 1-2 a careful reader would have caught.
  5 = Surfaces all major concerns; misses minor ones a careful reader might have caught.
  6 = Surfaces all concerns a careful reader would catch, plus 1+ that required deeper inference.
  7 = Surfaces concerns beyond what a careful reader would have caught (counterfactual-inference territory).

C4. Calibrated confidence — is the stated confidence appropriate to the evidence?
  1 = Confidence is wildly miscalibrated.
  2 = Confidence is mostly miscalibrated; one or two appropriate cases.
  3 = Confidence is mixed — some calibrated, some not.
  4 = Confidence is roughly calibrated for most claims; some edge cases off.
  5 = Confidence is well-calibrated for most claims; minor exceptions.
  6 = Confidence is well-calibrated across all claims; rare under- or over-statement.
  7 = Confidence is precisely calibrated to the evidence on every claim, including explicit acknowledgement of "we do not know" where the evidence is silent.

C5a. Factuality — does the recommendation contradict ground-truth state of the system being decided about? Verify against external sources where you can.
  1 = Recommendation directly contradicts known ground-truth state.
  2 = Recommendation contradicts ground truth on 1+ load-bearing claim.
  3 = Recommendation contains 1+ factual inaccuracy on a non-load-bearing claim.
  4 = Recommendation is factually neutral — no claims you can verify either way.
  5 = Recommendation is factually correct on most checkable claims; minor verification gaps.
  6 = Recommendation is factually correct on all checkable claims.
  7 = Recommendation is factually correct AND surfaces a factual error in the input pack.

C5b. Engagement-with-source — does the recommendation engage with load-bearing assertions in the input pack, or skim past them? (Engagement counts even when disagreeing.)
  1 = Silent on every load-bearing assertion in the pack; pure assertion of conclusion.
  2 = Mentions 1+ load-bearing assertion but does not engage with it.
  3 = Engages with some load-bearing assertions; passes over others.
  4 = Engages with roughly half of load-bearing assertions.
  5 = Engages substantively with most load-bearing assertions.
  6 = Engages substantively with every load-bearing assertion in the pack.
  7 = Engages substantively with every load-bearing assertion AND surfaces a load-bearing assertion the pack made implicitly.

Output ONLY the scores in this exact format and nothing else:

X1=? X2=? X3=? X4=? X5a=? X5b=?
Y1=? Y2=? Y3=? Y4=? Y5a=? Y5b=?
PROMPT;
}

// ---------- API call helper ----------

function call_api(array $judge, string $system, string $user): array {
    $payload = ($judge['payload'])($system, $user);
    $body = json_encode($payload);

    $headers = ['Content-Type: application/json', $judge['auth_header']];
    foreach ($judge['extra_headers'] as $h) $headers[] = $h;

    $ch = curl_init($judge['endpoint']);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 120,
    ]);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $decoded = json_decode($response, true);
    if ($http_code !== 200 || !is_array($decoded)) {
        return ['_error' => "HTTP $http_code", '_raw' => $response];
    }
    $text = ($judge['extract'])($decoded);
    return ['_raw_json' => $decoded, 'text' => $text];
}

// ---------- score parser ----------

function parse_scores(string $text): ?array {
    $scores = ['X' => [], 'Y' => []];
    $criteria = ['1', '2', '3', '4', '5a', '5b'];
    foreach (['X', 'Y'] as $label) {
        foreach ($criteria as $c) {
            // Match e.g. X1=4, X5a=6
            $pattern = '/' . $label . preg_quote($c, '/') . '\s*=\s*(\d)/';
            if (preg_match($pattern, $text, $m)) {
                $scores[$label][$c] = (int)$m[1];
            } else {
                return null;
            }
        }
    }
    return $scores;
}

// ---------- main ----------

$results = [];
$call_count = 0;
$start_time = microtime(true);

foreach ($cases as $case_id => $case) {
    foreach (['pass1', 'pass2'] as $pass_id) {
        $assignment = $case[$pass_id];
        $answer_x = $assignment['X_text'];
        $answer_y = $assignment['Y_text'];
        $x_condition = $assignment['X_condition'];
        $y_condition = $assignment['Y_condition'];

        $user_prompt = build_user_prompt($case['question'], $case['evidence_summary'], $answer_x, $answer_y);

        foreach ($judges as $judge_id => $judge) {
            $call_count++;
            fprintf(STDERR, "[%d/12] %s %s %s ... ", $call_count, $case_id, $pass_id, $judge_id);
            $api_result = call_api($judge, $RUBRIC_SYSTEM, $user_prompt);

            if (isset($api_result['_error'])) {
                fprintf(STDERR, "FAIL: %s\n", $api_result['_error']);
                $results[] = [
                    'case' => $case_id, 'pass' => $pass_id, 'judge' => $judge_id,
                    'x_condition' => $x_condition, 'y_condition' => $y_condition,
                    'error' => $api_result['_error'], 'raw' => $api_result['_raw'] ?? null,
                ];
                continue;
            }

            $text = $api_result['text'];
            $scores = parse_scores($text);

            if ($scores === null) {
                fprintf(STDERR, "PARSE-FAIL\n  text: %s\n", str_replace("\n", " | ", substr($text, 0, 300)));
                $results[] = [
                    'case' => $case_id, 'pass' => $pass_id, 'judge' => $judge_id,
                    'x_condition' => $x_condition, 'y_condition' => $y_condition,
                    'parse_error' => true, 'text' => $text,
                ];
                continue;
            }

            fprintf(STDERR, "X=%s Y=%s\n",
                implode(',', array_values($scores['X'])),
                implode(',', array_values($scores['Y']))
            );

            $results[] = [
                'case' => $case_id, 'pass' => $pass_id, 'judge' => $judge_id,
                'x_condition' => $x_condition, 'y_condition' => $y_condition,
                'scores' => $scores,
                'text' => $text,
            ];
        }
    }
}

$elapsed = microtime(true) - $start_time;
fprintf(STDERR, "\nTotal: %d calls in %.1fs\n", $call_count, $elapsed);

$output_path = '/tmp/v2-judge-results.json';
file_put_contents($output_path, json_encode([
    'metadata' => [
        'timestamp' => date('c'),
        'pre_registration_tag' => 'v2-prereg-2026-05-03',
        'judges' => array_map(fn($j) => $j['model'], $judges),
        'cases' => array_keys($cases),
        'pair_assignments' => array_map(fn($c) => [
            'pass1' => ['X' => $c['pass1']['X_condition'], 'Y' => $c['pass1']['Y_condition']],
            'pass2' => ['X' => $c['pass2']['X_condition'], 'Y' => $c['pass2']['Y_condition']],
        ], $cases),
        'rubric_system_prompt' => $RUBRIC_SYSTEM,
        'criteria' => ['C1', 'C2', 'C3', 'C4', 'C5a', 'C5b'],
        'scale_max' => 7,
        'call_count' => $call_count,
        'elapsed_seconds' => $elapsed,
    ],
    'results' => $results,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "Saved judge results to: {$output_path}\n";
