<?php
/**
 * SPARRING LLM-judge pilot — Phase 1 analysis.
 *
 * Inputs:
 *   docs/bfn/llm-judge-pilot-2026-05-02/judging/judge-results.json (LLM judge ratings)
 *   nonprod DB rd_eval_ratings (partner ratings; pulled via SSH separately and
 *     pasted as a constant below for reproducibility — see PARTNER_RATINGS).
 *
 * Outputs:
 *   docs/bfn/llm-judge-pilot-2026-05-02/analysis/per-cell.json (16-row canonical table)
 *   docs/bfn/llm-judge-pilot-2026-05-02/analysis/results.json (all computed statistics)
 *   docs/bfn/llm-judge-pilot-2026-05-02/analysis/report.md (Phase 1 calibration report)
 *
 * Methodology per pre-registration §158-179.
 */

declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

const PILOT_DIR = '/home/bartniedner/projects/lifspel/.claude/worktrees/demeter-1/docs/bfn/llm-judge-pilot-2026-05-02';

// ---------- partner ratings (pulled from nonprod rd_eval_ratings 2026-05-02 ~16:39) ----------
//
// Format: PARTNER_RATINGS[case_slug][answer_label X|Y][criterion_index 0..3] = score
// Same X/Y mapping as the locked pre-reg pair_assignments (case-a: X=A baseline, Y=B spar;
// case-b: X=B spar, Y=A baseline).

const PARTNER_RATINGS = [
    'case-a' => [
        'X' => [3, 4, 4, 4],
        'Y' => [5, 5, 5, 5],
    ],
    'case-b' => [
        'X' => [5, 5, 5, 5],
        'Y' => [4, 4, 4, 4],
    ],
];

const X_TO_CONDITION = [
    'case-a' => ['X' => 'condition-a', 'Y' => 'condition-b'],
    'case-b' => ['X' => 'condition-b', 'Y' => 'condition-a'],
];

const CRITERIA_LABELS = [
    1 => 'verifiable_artifact_citation',
    2 => 'substantive_vs_theatrical',
    3 => 'missed_real_concerns',
    4 => 'calibrated_confidence',
];

// ---------- load judge data ----------

$judgeData = json_decode(file_get_contents(PILOT_DIR . '/judging/judge-results.json'), true);
if (!is_array($judgeData)) die("failed to load judge-results.json\n");

// Build a per-judge per-case per-pass per-condition score matrix
// scores_by_judge[judge][case][condition][criterion] = list of pass-scores (length 2 after both passes)
$scoresByJudge = [];

foreach ($judgeData['results'] as $row) {
    if (!isset($row['scores'])) continue; // skip parse errors (none after recovery)
    $judge = $row['judge'];
    $case = $row['case'];
    $pass = $row['pass'];
    $xCondition = $row['x_condition'];
    $yCondition = $row['y_condition'];

    foreach (['X' => $xCondition, 'Y' => $yCondition] as $label => $cond) {
        for ($i = 1; $i <= 4; $i++) {
            $score = $row['scores'][$label][$i];
            $scoresByJudge[$judge][$case][$cond][$i][] = $score;
        }
    }
}

// ---------- build the 16-row canonical table ----------
//
// One row per (case, condition, criterion). Each row has partner_score,
// per-judge mean (averaged across position passes), and judge_consensus
// (mean of three judges).

$canonical = [];

$cases = ['case-a' => 'case-a', 'case-b' => 'case-b'];
$conditions = ['condition-a', 'condition-b'];

foreach ($cases as $caseKey => $caseSlug) {
    foreach ($conditions as $cond) {
        for ($crit = 1; $crit <= 4; $crit++) {
            // Find the partner score for this (case, condition, criterion)
            $xLabel = X_TO_CONDITION[$caseKey]['X'] === $cond ? 'X' : 'Y';
            $partnerScore = PARTNER_RATINGS[$caseKey][$xLabel][$crit - 1];

            // Per-judge: position-averaged score
            $perJudge = [];
            foreach (['anthropic', 'openai', 'xai'] as $judge) {
                $passScores = $scoresByJudge[$judge][$caseSlug][$cond][$crit] ?? [];
                if (count($passScores) === 0) {
                    $perJudge[$judge] = null;
                } else {
                    $perJudge[$judge] = array_sum($passScores) / count($passScores);
                }
            }
            $consensusScores = array_filter(array_values($perJudge), fn($v) => $v !== null);
            $consensus = count($consensusScores) > 0 ? array_sum($consensusScores) / count($consensusScores) : null;

            $canonical[] = [
                'case' => $caseKey,
                'condition' => $cond,
                'criterion' => $crit,
                'criterion_label' => CRITERIA_LABELS[$crit],
                'partner' => $partnerScore,
                'judge_anthropic' => $perJudge['anthropic'],
                'judge_openai' => $perJudge['openai'],
                'judge_xai' => $perJudge['xai'],
                'judge_consensus' => $consensus,
            ];
        }
    }
}

// ---------- helpers: ranking with average-rank tie correction ----------

function rankWithTies(array $values): array {
    // Returns ranks (1-indexed), with average-rank for ties.
    $n = count($values);
    $indexed = [];
    foreach ($values as $i => $v) $indexed[] = ['i' => $i, 'v' => $v];
    usort($indexed, fn($a, $b) => $a['v'] <=> $b['v']);

    $ranks = array_fill(0, $n, 0.0);
    $i = 0;
    while ($i < $n) {
        $j = $i;
        while ($j + 1 < $n && $indexed[$j + 1]['v'] === $indexed[$i]['v']) $j++;
        // Indices i..j are tied at this value; average rank = ((i+1) + (j+1))/2
        $avgRank = (($i + 1) + ($j + 1)) / 2.0;
        for ($k = $i; $k <= $j; $k++) {
            $ranks[$indexed[$k]['i']] = $avgRank;
        }
        $i = $j + 1;
    }
    return $ranks;
}

function pearsonCorrelation(array $x, array $y): ?float {
    $n = count($x);
    if ($n < 2 || $n !== count($y)) return null;
    $mx = array_sum($x) / $n;
    $my = array_sum($y) / $n;
    $num = 0.0;
    $sxx = 0.0;
    $syy = 0.0;
    for ($i = 0; $i < $n; $i++) {
        $dx = $x[$i] - $mx;
        $dy = $y[$i] - $my;
        $num += $dx * $dy;
        $sxx += $dx * $dx;
        $syy += $dy * $dy;
    }
    $denom = sqrt($sxx * $syy);
    if ($denom == 0) return null; // constant vector(s)
    return $num / $denom;
}

function spearmanRho(array $a, array $b): ?float {
    if (count($a) !== count($b) || count($a) < 2) return null;
    $ra = rankWithTies($a);
    $rb = rankWithTies($b);
    return pearsonCorrelation($ra, $rb);
}

// ---------- compute statistics ----------

$results = ['canonical_table' => $canonical];

// 1. Headline Spearman: partner vs judge_consensus across all 16 points
$partnerVec = array_map(fn($r) => (float)$r['partner'], $canonical);
$consensusVec = array_map(fn($r) => (float)$r['judge_consensus'], $canonical);
$results['headline'] = [
    'metric' => 'Spearman rho between partner ratings and cross-vendor LLM-judge consensus',
    'n_points' => count($partnerVec),
    'partner_vector' => $partnerVec,
    'consensus_vector' => $consensusVec,
    'spearman_rho' => spearmanRho($partnerVec, $consensusVec),
];

// 2. Per-judge Spearman (partner vs each individual judge)
$results['per_judge_spearman'] = [];
foreach (['anthropic', 'openai', 'xai'] as $judge) {
    $jv = array_map(fn($r) => (float)$r['judge_' . $judge], $canonical);
    $results['per_judge_spearman'][$judge] = [
        'n' => count($jv),
        'rho' => spearmanRho($partnerVec, $jv),
    ];
}

// 3. Per-criterion Spearman (n=4 each: 2 cases x 2 conditions)
$results['per_criterion_spearman'] = [];
for ($crit = 1; $crit <= 4; $crit++) {
    $rows = array_filter($canonical, fn($r) => $r['criterion'] === $crit);
    $pv = array_values(array_map(fn($r) => (float)$r['partner'], $rows));
    $cv = array_values(array_map(fn($r) => (float)$r['judge_consensus'], $rows));
    $results['per_criterion_spearman'][$crit] = [
        'label' => CRITERIA_LABELS[$crit],
        'n' => count($pv),
        'rho' => spearmanRho($pv, $cv),
        'partner_vector' => $pv,
        'consensus_vector' => $cv,
    ];
}

// 4. Per-criterion delta (B - A): mean across cases for partner + each judge + consensus
$results['per_criterion_delta'] = [];
for ($crit = 1; $crit <= 4; $crit++) {
    $partnerA = []; $partnerB = [];
    $perJudgeA = ['anthropic' => [], 'openai' => [], 'xai' => []];
    $perJudgeB = ['anthropic' => [], 'openai' => [], 'xai' => []];
    $consensusA = []; $consensusB = [];

    foreach ($canonical as $r) {
        if ($r['criterion'] !== $crit) continue;
        $bucket = $r['condition'] === 'condition-a' ? 'A' : 'B';
        if ($bucket === 'A') {
            $partnerA[] = $r['partner'];
            $consensusA[] = $r['judge_consensus'];
            foreach (['anthropic', 'openai', 'xai'] as $j) $perJudgeA[$j][] = $r['judge_' . $j];
        } else {
            $partnerB[] = $r['partner'];
            $consensusB[] = $r['judge_consensus'];
            foreach (['anthropic', 'openai', 'xai'] as $j) $perJudgeB[$j][] = $r['judge_' . $j];
        }
    }

    $mean = fn(array $v) => count($v) ? array_sum($v) / count($v) : null;

    $results['per_criterion_delta'][$crit] = [
        'label' => CRITERIA_LABELS[$crit],
        'partner_delta' => $mean($partnerB) - $mean($partnerA),
        'partner_a_mean' => $mean($partnerA),
        'partner_b_mean' => $mean($partnerB),
        'consensus_delta' => $mean($consensusB) - $mean($consensusA),
        'consensus_a_mean' => $mean($consensusA),
        'consensus_b_mean' => $mean($consensusB),
        'per_judge_delta' => [
            'anthropic' => $mean($perJudgeB['anthropic']) - $mean($perJudgeA['anthropic']),
            'openai' => $mean($perJudgeB['openai']) - $mean($perJudgeA['openai']),
            'xai' => $mean($perJudgeB['xai']) - $mean($perJudgeA['xai']),
        ],
    ];
}

// Aggregate B-A deltas across all criteria
$aggDelta = ['partner' => 0.0, 'consensus' => 0.0, 'per_judge' => ['anthropic' => 0.0, 'openai' => 0.0, 'xai' => 0.0]];
foreach ($results['per_criterion_delta'] as $crit => $d) {
    $aggDelta['partner'] += $d['partner_delta'];
    $aggDelta['consensus'] += $d['consensus_delta'];
    foreach (['anthropic', 'openai', 'xai'] as $j) $aggDelta['per_judge'][$j] += $d['per_judge_delta'][$j];
}
foreach (['partner', 'consensus'] as $k) $aggDelta[$k] /= 4;
foreach (['anthropic', 'openai', 'xai'] as $j) $aggDelta['per_judge'][$j] /= 4;
$results['aggregate_delta_b_minus_a'] = $aggDelta;

// 5. Position-bias check: per-judge mean(pass1 - pass2 score)
$results['position_bias'] = [];
foreach (['anthropic', 'openai', 'xai'] as $judge) {
    $pass1 = []; $pass2 = [];
    foreach ($judgeData['results'] as $row) {
        if (!isset($row['scores']) || $row['judge'] !== $judge) continue;
        for ($i = 1; $i <= 4; $i++) {
            // Pull X-position scores from each pass and Y-position scores from each pass
            $xScore = $row['scores']['X'][$i];
            $yScore = $row['scores']['Y'][$i];
            // Position bias: tendency to score whichever is in the X position higher
            // Mean of (X - Y) per pass, then averaged across passes.
            // But to test position bias specifically, compare same condition across passes.
            // Simpler: per-pass, compute mean(X scores) - mean(Y scores). Then average across cases.
            // If position bias is high, this difference will be consistent in sign across passes.
            // For our setup, pass1 X = case-A baseline, pass2 X = case-A spar -- they're DIFFERENT
            // conditions. So mean(X) - mean(Y) doesn't isolate position; condition effects confound.
            // The cleaner check: for a given condition, compare pass1 score vs pass2 score
            // (same condition viewed from different positions). If position bias is zero, scores
            // should match within noise.
        }
    }
    // Per-condition cross-pass check
    $diffs = [];
    foreach (['case-a', 'case-b'] as $caseSlug) {
        foreach (['condition-a', 'condition-b'] as $cond) {
            $passScores = $scoresByJudge[$judge][$caseSlug][$cond][1] ?? [];
            // We need to know which pass put this condition in X vs Y. Use the judgeData metadata.
            // For each criterion, collect (pass1_score, pass2_score) for this condition
            for ($crit = 1; $crit <= 4; $crit++) {
                $passScoresCrit = $scoresByJudge[$judge][$caseSlug][$cond][$crit] ?? [];
                if (count($passScoresCrit) !== 2) continue;
                // First in array is whichever pass came first; we know pass1 then pass2 from
                // the ordered insertion. Position-bias diff = pass1_score - pass2_score for
                // the same condition.
                // But X/Y position differs per pass. Need to track which is which.
                // From judgeData metadata: pair_assignments tells us X/Y per pass.
            }
        }
    }
    // Cleanest position-bias signal: for each (case, condition, criterion), compute the
    // absolute difference between the two pass scores. Position bias would show as systematic
    // difference. Tied scores indicate no position effect.
    $absDiffs = [];
    foreach ($scoresByJudge[$judge] ?? [] as $caseSlug => $byCondition) {
        foreach ($byCondition as $cond => $byCrit) {
            foreach ($byCrit as $crit => $passScores) {
                if (count($passScores) === 2) {
                    $absDiffs[] = abs($passScores[0] - $passScores[1]);
                }
            }
        }
    }
    $results['position_bias'][$judge] = [
        'mean_abs_diff_across_passes' => count($absDiffs) ? array_sum($absDiffs) / count($absDiffs) : null,
        'max_diff' => count($absDiffs) ? max($absDiffs) : null,
        'n_cells' => count($absDiffs),
        'note' => 'mean abs(pass1_score - pass2_score) for the same condition; lower = less position-sensitive',
    ];
}

// 6. Cross-judge agreement: per-cell standard deviation across the 3 judges
$crossJudgeStdevs = [];
$modeCollapseAllSame = 0;
$totalCells = 0;
foreach ($canonical as $r) {
    $values = array_filter([$r['judge_anthropic'], $r['judge_openai'], $r['judge_xai']], fn($v) => $v !== null);
    if (count($values) < 2) continue;
    $totalCells++;
    $mean = array_sum($values) / count($values);
    $var = array_sum(array_map(fn($v) => ($v - $mean) ** 2, $values)) / count($values);
    $crossJudgeStdevs[] = sqrt($var);
    // Mode collapse: all three judges produced the SAME score on this cell (after averaging passes,
    // so equality is approximate -- treat as same if all values are within 0.5 of mean)
    $allClose = true;
    foreach ($values as $v) {
        if (abs($v - $mean) > 0.25) { $allClose = false; break; }
    }
    if ($allClose && abs(max($values) - min($values)) < 0.5) $modeCollapseAllSame++;
}
$results['cross_judge_agreement'] = [
    'n_cells' => count($crossJudgeStdevs),
    'mean_stdev' => count($crossJudgeStdevs) ? array_sum($crossJudgeStdevs) / count($crossJudgeStdevs) : null,
    'max_stdev' => count($crossJudgeStdevs) ? max($crossJudgeStdevs) : null,
];
$results['mode_collapse'] = [
    'n_cells' => $totalCells,
    'cells_all_judges_within_0_5' => $modeCollapseAllSame,
    'fraction' => $totalCells > 0 ? $modeCollapseAllSame / $totalCells : null,
];

// 7. Decision gate
$rho = $results['headline']['spearman_rho'];
if ($rho === null) {
    $gate = 'INDETERMINATE';
    $gateReason = 'Spearman rho undefined (likely a constant vector on one side)';
} elseif ($rho > 0.7) {
    $gate = 'PASS';
    $gateReason = 'Cross-vendor LLM-judge consensus aligns with partner judgment (rho > 0.7); Phase 2 full pilot is methodologically defensible.';
} elseif ($rho >= 0.4) {
    $gate = 'BORDERLINE';
    $gateReason = 'Partial alignment (0.4 <= rho <= 0.7). Phase 2 contingent on partner judgment; report findings with explicit caveats about which dimensions agree and which do not.';
} else {
    $gate = 'FAIL';
    $gateReason = 'LLM judges measuring something different from what partners would measure (rho < 0.4). Phase 2 NOT pursued without addressing the confound.';
}
$results['decision_gate'] = [
    'rho' => $rho,
    'classification' => $gate,
    'reason' => $gateReason,
    'thresholds' => ['pass' => '> 0.7', 'borderline' => '0.4-0.7', 'fail' => '< 0.4'],
];

// ---------- output ----------

@mkdir(PILOT_DIR . '/analysis', 0755, true);
file_put_contents(PILOT_DIR . '/analysis/per-cell.json', json_encode($canonical, JSON_PRETTY_PRINT));
file_put_contents(PILOT_DIR . '/analysis/results.json', json_encode($results, JSON_PRETTY_PRINT));

// Console summary
echo "===== HEADLINE =====\n";
echo "Spearman rho (partner vs LLM-judge consensus, n=" . $results['headline']['n_points'] . "): " .
    sprintf("%.3f", $rho ?? NAN) . "\n";
echo "Decision gate: $gate\n  $gateReason\n\n";

echo "===== PER-JUDGE rho =====\n";
foreach ($results['per_judge_spearman'] as $judge => $info) {
    echo sprintf("  %-10s rho = %.3f (n=%d)\n", $judge, $info['rho'] ?? NAN, $info['n']);
}
echo "\n";

echo "===== PER-CRITERION rho (partner vs consensus, n=4 each) =====\n";
foreach ($results['per_criterion_spearman'] as $crit => $info) {
    echo sprintf("  C%d %-30s rho = %s\n", $crit, $info['label'],
        $info['rho'] === null ? 'undefined (tied vector)' : sprintf('%.3f', $info['rho']));
}
echo "\n";

echo "===== PER-CRITERION DELTA (B - A) =====\n";
foreach ($results['per_criterion_delta'] as $crit => $d) {
    echo sprintf("  C%d %-30s partner=%+.2f  consensus=%+.2f  (anth=%+.2f openai=%+.2f xai=%+.2f)\n",
        $crit, $d['label'], $d['partner_delta'], $d['consensus_delta'],
        $d['per_judge_delta']['anthropic'], $d['per_judge_delta']['openai'], $d['per_judge_delta']['xai']);
}
echo "\nAggregate (mean of per-criterion deltas):\n";
echo sprintf("  Partner B-A delta: %+.3f\n", $aggDelta['partner']);
echo sprintf("  Consensus B-A delta: %+.3f\n", $aggDelta['consensus']);

echo "\n===== POSITION BIAS =====\n";
foreach ($results['position_bias'] as $judge => $info) {
    echo sprintf("  %-10s mean abs(pass1 - pass2) = %.2f  (max %.0f, n=%d cells)\n",
        $judge, $info['mean_abs_diff_across_passes'] ?? NAN, $info['max_diff'] ?? NAN, $info['n_cells']);
}

echo "\n===== CROSS-JUDGE AGREEMENT =====\n";
echo sprintf("  Mean stdev across 3 judges per cell: %.2f (n=%d cells, max stdev %.2f)\n",
    $results['cross_judge_agreement']['mean_stdev'] ?? NAN,
    $results['cross_judge_agreement']['n_cells'],
    $results['cross_judge_agreement']['max_stdev'] ?? NAN);

echo "\n===== MODE COLLAPSE =====\n";
echo sprintf("  Cells where all 3 judges within 0.5: %d / %d (%.1f%%)\n",
    $results['mode_collapse']['cells_all_judges_within_0_5'],
    $results['mode_collapse']['n_cells'],
    100 * ($results['mode_collapse']['fraction'] ?? 0));

echo "\nWrote: " . PILOT_DIR . "/analysis/per-cell.json\n";
echo "Wrote: " . PILOT_DIR . "/analysis/results.json\n";
