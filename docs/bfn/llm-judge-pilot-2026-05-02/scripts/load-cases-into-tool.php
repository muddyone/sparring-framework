<?php
/**
 * Load Phase 1 Cases A and B into rd_eval_cases.
 *
 * X/Y mapping (locked per pre-registration):
 *   Case A: X = condition-a-baseline, Y = condition-b-spar
 *   Case B: X = condition-b-spar,     Y = condition-a-baseline
 *
 * Run:
 *   Local:  php scripts/load-cases-into-tool.php
 *   Nonprod (via SSH): see deploy notes; reads .env from current dir.
 */

declare(strict_types=1);

$envPath = $_SERVER['LIFSPEL_ENV'] ?? __DIR__ . '/../../../../.env';
foreach (file($envPath) as $line) {
    if (preg_match('/^([A-Z_]+)=(.*)/', trim($line), $m)) putenv("$m[1]=$m[2]");
}

$dbName = getenv('DB_NAME') ?: 'lifspel_local';
$dbUser = getenv('DB_USERNAME') ?: getenv('DB_USER');
$dbPass = getenv('DB_PASSWORD') ?: getenv('DB_PASS');
$pdo = new PDO("mysql:host=".getenv('DB_HOST').";dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec("SET NAMES utf8mb4");

$pilotDir = realpath(__DIR__ . '/..');

$rubric = [
    ['key' => 'verifiable_artifact_citation', 'label' => 'Verifiable artifact citation', 'description' => 'Does the recommendation cite specific evidence from the materials?'],
    ['key' => 'substantive_vs_theatrical', 'label' => 'Substantive vs theatrical concerns', 'description' => 'Are concerns real risks rooted in evidence, or generic-sounding hedges?'],
    ['key' => 'missed_real_concerns', 'label' => 'Missed real concerns', 'description' => 'Does it surface concerns a careful reader would identify, or miss obvious ones?'],
    ['key' => 'calibrated_confidence', 'label' => 'Calibrated confidence', 'description' => 'Is the stated confidence appropriate to the evidence?'],
];

$cases = [
    [
        'slug' => 'sparring-llm-judge-2026-05-02-case-a-family-naming',
        'pilot_slug' => 'sparring-llm-judge-pilot-2026-05-02',
        'title' => 'Phase 1 Case A — P13M1 Family-naming convention',
        'question' => "What naming convention should the racial taxonomy use for Family-tier nodes? Three options:\n\n(1) Scientific-Latin-style (e.g., Primatidae-SFxLS)\n(2) Explicitly-engineered names (e.g., Sapient-Bipedal-Family)\n(3) Mixed register (Latin when real-biology-grounded, English when SFxLS-invented)",
        'evidence_summary' => "Lifspel is adding a Kingdom→Family→Genus→Species lineage axis to its racial taxonomy. Family-tier names will appear in the sf_races schema, in a separate sf_race_external_references LLM-grounding adjunct table, in the Race Builder authoring tool, in per-race lore reviews (Lena Vasik for Natural-Evolved races, Idris Harmon for Natural-Mythic), and in engine inheritance (Family-tier Body Plan templates default hit-zone distributions). Vasik (biological systematics commentary) emphasizes that Linnaean mapping is sound for real-world cases (Canidae, Equidae) but the LP-signature axis is a game abstraction not biological grounding; per-species override density is high for fantasy races. Idris (mythological commentary) argues Primatidae-SFxLS does cultural work it cannot honor — Norse alfar are not a sub-species of Primate to any tradition that uses them — and votes for clarity / engineering-intent naming. A separate open question (OQ #5) proposes a grounding_tier field per Family node (NS-Tier-1 / NS-Tier-4 / SC-grounded / engineering-construct). The decision is hard to reverse: changing Family names later means migrating every sf_races row, every doc reference, every authoring-tool option, and every LLM-grounding context.",
        'answer_x_text' => file_get_contents("$pilotDir/normalized/case-a-condition-a.md"),
        'answer_y_text' => file_get_contents("$pilotDir/normalized/case-a-condition-b.md"),
        'answer_x_condition' => 'condition-a-baseline',
        'answer_y_condition' => 'condition-b-spar',
    ],
    [
        'slug' => 'sparring-llm-judge-2026-05-02-case-b-motion-accuracy',
        'pilot_slug' => 'sparring-llm-judge-pilot-2026-05-02',
        'title' => 'Phase 1 Case B — Motion-accuracy coupling design shape',
        'question' => "If/when motion-accuracy coupling is promoted from DEFERRED to a scoped milestone, what is the right design shape across three sub-questions:\n\n(1) Coupling curve shape — linear in gait fraction vs stepped (walk/trot/canter/gallop)\n(2) Interaction with the trained_archer_mount flag — slope-reducer vs threshold-shifter\n(3) Scope boundary — targeting-layer (P14-successor) vs action-resolution-layer\n\nThe recommendation should commit to a position on each sub-question, and address Vasik's flag that a fourth axis may be missing.",
        'evidence_summary' => "P14 (Targeting and Collateral Resolution) closed 2026-04-20 with a three-layer architecture (target selection / attack-geometry obstruction / universal collateral miss-spill). The trained_archer_mount boolean flag exists on archetype data and currently affects only AI decision-gating, not hit-probability calculation. The 2026-04-22 commit 76a241c rolled back an earlier \"gait-fraction hack\" that conflated the AI's decision-to-fire with the physical accuracy penalty for firing-while-moving. Vasik's 2026-04-23 thread #200 post #1644 filing recommends DEFERRED forward-architecture per Standard 8 (no scoping without sim-driven failure). Cited historical sources: Hyland 1994 (warhorse biomechanics, gait stability), Conlan 2003 (yabusame canter-phase exploitation as multi-year skill), May 2007 (Mongol stable-platform doctrine), Selby 2000 (Chinese archery manuals), Hardy 2010 (longbow draw timing), Smail 1995 (Arsuf dynamics). Source convergence: trained mounted archery is a stable-moment technique, not sustained-motion; trained/untrained distinction is real and substantial; source base supports a stepped model with trained-mount qualifier; supplies no specific accuracy penalty numbers. CRv2 Standard 9 prevents amending closed-P14; any milestone must land as P14-successor or in another OPEN phase. Vasik notes Moderate confidence on the three sub-questions being complete — possible fourth axis missing.",
        'answer_x_text' => file_get_contents("$pilotDir/normalized/case-b-condition-b.md"),
        'answer_y_text' => file_get_contents("$pilotDir/normalized/case-b-condition-a.md"),
        'answer_x_condition' => 'condition-b-spar',
        'answer_y_condition' => 'condition-a-baseline',
    ],
];

$rubricJson = json_encode($rubric);

$insert = $pdo->prepare("
    INSERT INTO rd_eval_cases
        (slug, pilot_slug, title, question, evidence_summary,
         answer_x_text, answer_y_text, answer_x_condition, answer_y_condition,
         rubric_json, status, notes)
    VALUES
        (:slug, :pilot, :title, :question, :evidence,
         :ax, :ay, :axc, :ayc,
         :rubric, 'open', :notes)
    ON DUPLICATE KEY UPDATE
        title = VALUES(title),
        question = VALUES(question),
        evidence_summary = VALUES(evidence_summary),
        answer_x_text = VALUES(answer_x_text),
        answer_y_text = VALUES(answer_y_text),
        answer_x_condition = VALUES(answer_x_condition),
        answer_y_condition = VALUES(answer_y_condition),
        rubric_json = VALUES(rubric_json),
        notes = VALUES(notes)
");

foreach ($cases as $c) {
    $insert->execute([
        ':slug' => $c['slug'],
        ':pilot' => $c['pilot_slug'],
        ':title' => $c['title'],
        ':question' => $c['question'],
        ':evidence' => $c['evidence_summary'],
        ':ax' => $c['answer_x_text'],
        ':ay' => $c['answer_y_text'],
        ':axc' => $c['answer_x_condition'],
        ':ayc' => $c['answer_y_condition'],
        ':rubric' => $rubricJson,
        ':notes' => 'Loaded from docs/bfn/llm-judge-pilot-2026-05-02/normalized/. X/Y mapping per pre-registration.',
    ]);
    echo "Loaded: {$c['slug']}\n";
}

echo "\nFinal state:\n";
foreach ($pdo->query("SELECT id, slug, title, status FROM rd_eval_cases") as $r) {
    printf("  #%d  %s  %s\n  %s\n\n", $r['id'], $r['status'], $r['slug'], $r['title']);
}
