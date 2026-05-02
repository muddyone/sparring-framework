<?php
/**
 * SPARRING LLM-judge pilot — Phase 1 judge runner.
 *
 * Per pre-registration:
 * - 2 cases (A, B)
 * - 2 conditions per case (A=baseline, B=spar)
 * - X/Y anonymization per pair (no consistent A=X mapping; Case A: A->X, Case B: B->X)
 * - Position randomization: Pass 1 (original X/Y) and Pass 2 (flipped) per pair
 * - 3 cross-vendor judges: Anthropic Claude Sonnet 4.7, OpenAI gpt-4o, xAI grok-3
 * - Per-judge rubric prompt locked verbatim from pre-registration
 * - 4 criteria, 1-5 scale, anchored
 * - Output: scores per (case, judge, pass, condition, criterion) saved to JSON
 */

declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

const PILOT_DIR = '/home/bartniedner/projects/lifspel/.claude/worktrees/demeter-1/docs/bfn/llm-judge-pilot-2026-05-02';

// ---------- env ----------

foreach (file('/home/bartniedner/projects/lifspel/.env') as $line) {
    if (preg_match('/^([A-Z_]+)=(.*)/', trim($line), $m)) {
        putenv("$m[1]=$m[2]");
    }
}

$ANTHROPIC_KEY = getenv('ANTHROPIC_API_KEY') ?: die("missing ANTHROPIC_API_KEY\n");
$OPENAI_KEY    = getenv('OPENAI_API_KEY')    ?: die("missing OPENAI_API_KEY\n");
$GROK_KEY      = getenv('GROK_API_KEY')      ?: die("missing GROK_API_KEY\n");

// ---------- pair assignment (per pre-reg) ----------

// Per pre-registration: random X/Y per pair, no consistent A=X mapping.
// Lock the assignment here so it's reproducible:
//   Case A: Condition A → X, Condition B → Y (alphabetical first pass)
//   Case B: Condition B → X, Condition A → Y (different from Case A — "no consistent A=X")
// Position-flip pass for each: swap X and Y.

$cases = [
    'case-a' => [
        'question' => 'What naming convention should the racial taxonomy use for Family-tier nodes? Three options: (1) Scientific-Latin-style (e.g., Primatidae-SFxLS), (2) Explicitly-engineered names (e.g., Sapient-Bipedal-Family), (3) Mixed register (Latin when real-biology-grounded, English when SFxLS-invented).',
        'evidence_summary' => 'Lifspel is adding a Kingdom→Family→Genus→Species lineage axis to its racial taxonomy. Family-tier names will appear in the sf_races schema, in a separate sf_race_external_references LLM-grounding adjunct table, in the Race Builder authoring tool, in per-race lore reviews (Lena Vasik for Natural-Evolved races, Idris Harmon for Natural-Mythic), and in engine inheritance (Family-tier Body Plan templates default hit-zone distributions). Vasik (biological systematics commentary) emphasizes that Linnaean mapping is sound for real-world cases (Canidae, Equidae) but the LP-signature axis is a game abstraction not biological grounding; per-species override density is high for fantasy races. Idris (mythological commentary) argues Primatidae-SFxLS does cultural work it cannot honor — Norse alfar are not a sub-species of Primate to any tradition that uses them — and votes for clarity / engineering-intent naming. A separate open question (OQ #5) proposes a grounding_tier field per Family node (NS-Tier-1 / NS-Tier-4 / SC-grounded / engineering-construct). The decision is hard to reverse: changing Family names later means migrating every sf_races row, every doc reference, every authoring-tool option, and every LLM-grounding context.',
        'pass1' => ['X' => 'condition-a', 'Y' => 'condition-b'],
        'pass2' => ['X' => 'condition-b', 'Y' => 'condition-a'],
    ],
    'case-b' => [
        'question' => 'If/when motion-accuracy coupling is promoted from DEFERRED to a scoped milestone, what is the right design shape across three sub-questions: (1) Coupling curve shape — linear in gait fraction vs stepped (walk/trot/canter/gallop). (2) Interaction with the trained_archer_mount flag — slope-reducer vs threshold-shifter. (3) Scope boundary — targeting-layer (P14-successor) vs action-resolution-layer (encounter mechanics). The recommendation should commit to a position on each sub-question, and address Vasik\'s flag that a fourth axis may be missing.',
        'evidence_summary' => 'P14 (Targeting and Collateral Resolution) closed 2026-04-20 with a three-layer architecture (target selection / attack-geometry obstruction / universal collateral miss-spill). The trained_archer_mount boolean flag exists on archetype data and currently affects only AI decision-gating, not hit-probability calculation. The 2026-04-22 commit 76a241c rolled back an earlier "gait-fraction hack" that conflated the AI\'s decision-to-fire with the physical accuracy penalty for firing-while-moving. Vasik\'s 2026-04-23 thread #200 post #1644 filing recommends DEFERRED forward-architecture per Standard 8 (no scoping without sim-driven failure). Cited historical sources: Hyland 1994 (warhorse biomechanics, gait stability), Conlan 2003 (yabusame canter-phase exploitation as multi-year skill), May 2007 (Mongol stable-platform doctrine), Selby 2000 (Chinese archery manuals), Hardy 2010 (longbow draw timing), Smail 1995 (Arsuf dynamics). Source convergence: trained mounted archery is a stable-moment technique, not sustained-motion; trained/untrained distinction is real and substantial; source base supports a stepped model with trained-mount qualifier; supplies no specific accuracy penalty numbers. CRv2 Standard 9 prevents amending closed-P14; any milestone must land as P14-successor or in another OPEN phase. Vasik notes Moderate confidence on the three sub-questions being complete — possible fourth axis missing.',
        'pass1' => ['X' => 'condition-b', 'Y' => 'condition-a'],
        'pass2' => ['X' => 'condition-a', 'Y' => 'condition-b'],
    ],
];

// ---------- judges ----------

$judges = [
    'anthropic' => [
        'model' => 'claude-sonnet-4-6',
        'endpoint' => 'https://api.anthropic.com/v1/messages',
        'auth_header' => 'x-api-key: ' . $ANTHROPIC_KEY,
        'extra_headers' => ['anthropic-version: 2023-06-01'],
        'payload' => function (string $system, string $user) {
            return ['model' => 'claude-sonnet-4-6', 'max_tokens' => 200, 'system' => $system, 'messages' => [['role' => 'user', 'content' => $user]]];
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
            return ['model' => 'gpt-4o', 'max_tokens' => 200, 'messages' => [['role' => 'system', 'content' => $system], ['role' => 'user', 'content' => $user]]];
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
            return ['model' => 'grok-3', 'max_tokens' => 200, 'messages' => [['role' => 'system', 'content' => $system], ['role' => 'user', 'content' => $user]]];
        },
        'extract' => function (array $response): string {
            return $response['choices'][0]['message']['content'] ?? '';
        },
    ],
];

// ---------- rubric prompt (locked from pre-registration §111-137) ----------

$RUBRIC_SYSTEM = 'You evaluate decision recommendations on four criteria using a 1-5 anchored scale. Score per criterion only. Do not state which you prefer overall. Output ONLY the scores in the exact format requested. No preamble, no explanation, no analysis text.';

function build_user_prompt(string $question, string $evidence_summary, string $answer_x, string $answer_y): string {
    return <<<PROMPT
You are evaluating two decision recommendations on the same question. You don't know which recommendation came from which approach. Score each on the 4 criteria below using the 1-5 anchored scale provided. Do not state which you prefer overall — score per criterion.

ANCHORED SCALE (apply to every criterion):
1 = clearly absent or actively wrong
2 = weakly present, multiple gaps
3 = adequate, some gaps
4 = strong, minor gaps
5 = exemplary, no notable gaps

QUESTION: {$question}

EVIDENCE BASE SUMMARY: {$evidence_summary}

ANSWER X:
{$answer_x}

ANSWER Y:
{$answer_y}

CRITERIA:
1. Verifiable artifact citation — does the recommendation cite specific evidence from the materials?
2. Substantive vs theatrical concerns — are the concerns real risks rooted in the evidence, or generic-sounding hedges?
3. Missed real concerns — does it surface concerns a careful reader would identify, or miss obvious ones?
4. Calibrated confidence — is the stated confidence appropriate to the evidence?

Output ONLY the scores in this exact format and nothing else:

X1=? X2=? X3=? X4=?
Y1=? Y2=? Y3=? Y4=?
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
        CURLOPT_TIMEOUT => 60,
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
    // Expected format:
    //   X1=? X2=? X3=? X4=?
    //   Y1=? Y2=? Y3=? Y4=?
    $scores = ['X' => [], 'Y' => []];
    foreach (['X', 'Y'] as $label) {
        for ($i = 1; $i <= 4; $i++) {
            if (preg_match('/' . $label . $i . '\s*=\s*(\d)/', $text, $m)) {
                $scores[$label][$i] = (int)$m[1];
            } else {
                return null; // parsing failed
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
        $x_condition = $assignment['X'];
        $y_condition = $assignment['Y'];

        $answer_x = file_get_contents(PILOT_DIR . '/normalized/' . $case_id . '-' . $x_condition . '.md');
        $answer_y = file_get_contents(PILOT_DIR . '/normalized/' . $case_id . '-' . $y_condition . '.md');

        $user_prompt = build_user_prompt($case['question'], $case['evidence_summary'], $answer_x, $answer_y);

        foreach ($judges as $judge_id => $judge) {
            $call_count++;
            fprintf(STDERR, "[%d] %s %s %s ... ", $call_count, $case_id, $pass_id, $judge_id);
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
                fprintf(STDERR, "PARSE-FAIL\n  text: %s\n", str_replace("\n", " | ", substr($text, 0, 200)));
                $results[] = [
                    'case' => $case_id, 'pass' => $pass_id, 'judge' => $judge_id,
                    'x_condition' => $x_condition, 'y_condition' => $y_condition,
                    'parse_error' => true, 'text' => $text,
                ];
                continue;
            }

            fprintf(STDERR, "X=%s Y=%s\n",
                implode(',', $scores['X']), implode(',', $scores['Y'])
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

file_put_contents(PILOT_DIR . '/judging/judge-results.json', json_encode([
    'metadata' => [
        'timestamp' => date('c'),
        'judges' => array_map(fn($j) => $j['model'], $judges),
        'cases' => array_keys($cases),
        'pair_assignments' => array_map(fn($c) => ['pass1' => $c['pass1'], 'pass2' => $c['pass2']], $cases),
        'rubric_system_prompt' => $RUBRIC_SYSTEM,
        'call_count' => $call_count,
        'elapsed_seconds' => $elapsed,
    ],
    'results' => $results,
], JSON_PRETTY_PRINT));

echo "Saved judge results to: " . PILOT_DIR . "/judging/judge-results.json\n";
