<?php
// Recover any parse_error rows in judge-results.json by parsing positionally.
// Documented fallback: if labeled parse fails, extract the first 8 single-digit numbers
// and assume the first 4 are X scores and the next 4 are Y scores.

declare(strict_types=1);

const PILOT_DIR = '/home/bartniedner/projects/lifspel/.claude/worktrees/demeter-1/docs/bfn/llm-judge-pilot-2026-05-02';

$path = PILOT_DIR . '/judging/judge-results.json';
$data = json_decode(file_get_contents($path), true);

$recoveries = [];

foreach ($data['results'] as &$row) {
    if (!isset($row['parse_error']) || $row['parse_error'] !== true) continue;

    // Positional extraction: get all single digits in order
    preg_match_all('/=\s*(\d)/', $row['text'], $m);
    $digits = array_map('intval', $m[1]);

    if (count($digits) >= 8) {
        $row['scores'] = [
            'X' => [1 => $digits[0], 2 => $digits[1], 3 => $digits[2], 4 => $digits[3]],
            'Y' => [1 => $digits[4], 2 => $digits[5], 3 => $digits[6], 4 => $digits[7]],
        ];
        $row['parse_error'] = false;
        $row['parse_recovery'] = 'positional fallback (labeled parse failed due to LLM label typo)';
        $recoveries[] = ['case' => $row['case'], 'pass' => $row['pass'], 'judge' => $row['judge'], 'recovered_scores' => $row['scores']];
    }
}
unset($row);

if (!empty($recoveries)) {
    $data['metadata']['parse_recoveries'] = $recoveries;
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
    foreach ($recoveries as $r) {
        printf("Recovered %s %s %s: X=%s Y=%s\n",
            $r['case'], $r['pass'], $r['judge'],
            implode(',', $r['recovered_scores']['X']),
            implode(',', $r['recovered_scores']['Y'])
        );
    }
} else {
    echo "No parse_error rows to recover.\n";
}
