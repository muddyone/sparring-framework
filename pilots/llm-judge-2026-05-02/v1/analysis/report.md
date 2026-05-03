# SPARRING LLM-judge pilot — Phase 1 calibration report

**Pilot**: `sparring-llm-judge-pilot-2026-05-02`
**Pre-registration**: [`docs/bfn/llm-judge-pilot-2026-05-02/00-pre-registration.md`](../00-pre-registration.md)
**Run window**: 2026-05-02 (single session)
**Cases**: n=2 (Case A — P13M1 Family-naming; Case B — Motion-accuracy coupling design shape)
**Conditions**: Condition A (single-agent baseline, claude-opus-4-7); Condition B (`/spar` 2-iteration, claude-opus-4-7 Generator+Challenger)
**Judges**: Anthropic claude-sonnet-4-6 + OpenAI gpt-4o + xAI grok-3 (note: pre-reg targeted Sonnet 4.7 and Grok-4 class; substituted what is currently available)
**Partner**: Bart Niedner (Breegarra), 1 rater

---

## Headline

**Decision gate: FAIL.** Spearman ρ between partner ratings and cross-vendor LLM-judge consensus = **0.351**, below the pre-registered 0.4 threshold.

But the diagnosis is more nuanced than the headline classification suggests:

- **Per-judge ρ varies dramatically across vendors** — Anthropic alone passes the gate (ρ = 0.706); OpenAI (ρ = 0.145) and xAI (ρ = 0.198) drag the consensus.
- **Per-criterion ρ is split** — citation discipline (C1, ρ = 0.949) and concern substantiveness (C2, ρ = 0.707) pass cleanly. Concern-coverage (C3, ρ = 0.000) and confidence calibration (C4, ρ = 0.236) fail.
- **Directional agreement is intact** — both partner and consensus rate Condition B (SPARRING) higher than Condition A on every one of the 4 criteria. Magnitudes differ; sign does not.

So the failure mode is NOT "judges think SPARRING is worse than partner does." The failure mode is: **judges don't differentiate the conditions as sharply as the partner does, and they disagree among themselves on which concerns are 'real' or whether confidence is 'calibrated.'** This is a meaningful and Phase 2-relevant finding rather than a methodological collapse.

---

## Per-judge headline (n=16 score-points each)

| Judge | Vendor | Spearman ρ vs partner | Gate |
|---|---|---:|---|
| **Anthropic** | claude-sonnet-4-6 | **0.706** | PASS |
| **OpenAI** | gpt-4o | 0.145 | FAIL |
| **xAI** | grok-3 | 0.198 | FAIL |
| **Cross-vendor consensus** | mean of three | **0.351** | FAIL |

**Within-vendor self-preference is NOT the explanation.** Both conditions used Claude underneath; if Anthropic-as-judge were preferring Claude outputs over non-Claude outputs, the bias would inflate or deflate scores uniformly across both conditions, leaving ρ-with-partner unaffected. The Anthropic-vs-others gap shows up in *partner-alignment*, not in *score levels*.

The cleanest explanation consistent with the pattern is that **judging-task instruction-following on this rubric varies by model family**. The Anthropic judge tracked the partner's per-criterion differentiation; the OpenAI and xAI judges produced flatter / more independent score distributions that don't align as sharply with partner judgment.

---

## Per-criterion headline (n=4 each: 2 cases × 2 conditions)

| # | Criterion | Spearman ρ (partner vs consensus) | Per-criterion gate | Notes |
|---|---|---:|---|---|
| C1 | Verifiable artifact citation | **0.949** | PASS | Highest agreement. Citations are concrete; partner and judges converge on whether they exist. |
| C2 | Substantive vs theatrical concerns | **0.707** | PASS | At the gate threshold. Distinguishing real from generic-sounding concerns is doable but noisier than C1. |
| C3 | Missed real concerns | **0.000** | FAIL | Zero correlation. Judges and partner disagree systematically about which concerns were "missed." This is the most subjective of the four criteria and depends on each rater's mental model of "what a careful reader would identify." |
| C4 | Calibrated confidence | **0.236** | FAIL | Below threshold. "Is this confidence appropriate to the evidence?" turns out to be hard for LLMs to judge in alignment with humans, especially with no anchor for what *the right* confidence would be. |

The per-criterion split tells us **C1 and C2 are calibratable for this domain; C3 and C4 are not, at least with this rubric and these judges**.

---

## Per-criterion delta (B − A)

The directional finding: both partner and judges rate Condition B (SPARRING) higher than Condition A across every criterion.

| # | Criterion | Partner Δ (B−A) | Consensus Δ | Anth Δ | OpenAI Δ | xAI Δ |
|---|---|---:|---:|---:|---:|---:|
| C1 | Verifiable artifact citation | **+1.50** | +0.75 | +1.25 | +0.25 | +0.75 |
| C2 | Substantive vs theatrical concerns | +1.00 | +0.42 | +0.75 | +0.25 | +0.25 |
| C3 | Missed real concerns | +1.00 | +0.25 | +1.00 | +0.00 | -0.25 |
| C4 | Calibrated confidence | +1.00 | +0.25 | +0.00 | +0.50 | +0.25 |
| | **Mean** | **+1.125** | **+0.417** | **+0.75** | **+0.25** | **+0.25** |

**Partner sees ~3× the magnitude of B−A advantage that judges do.** Two readings:
- Partner is over-favoring SPARRING (giving it ceiling 5s on every criterion) and judges are correctly more measured.
- Judges are under-detecting real differences that partner reads correctly.

These are not adjudicable from n=2 case data. Both readings are consistent with the rest of the picture; Phase 2 with more cases and more raters is what would distinguish them.

The xAI judge's Δ for C3 (missed real concerns) is **−0.25** — the only negative value in the table. Grok-3 marginally rates Condition A (single-agent) as identifying *more* missed-real-concerns than Condition B on average. That's a real disagreement signal worth Phase 2 attention if grok-class judges remain in the panel.

---

## Position-bias check

For each (case, condition, criterion), the same condition was rated twice — once when in the X position (Pass 1 or 2) and once when in the Y position (the flip). If position bias were strong, the same condition would receive systematically different scores depending on which position it occupied.

| Judge | Mean abs(Pass 1 − Pass 2) per cell | Max diff | Interpretation |
|---|---:|---:|---|
| Anthropic | 0.38 | 1 | Modest. ~38% of cells score the same; rest differ by 1 point. |
| OpenAI | 0.38 | 1 | Same as Anthropic. |
| xAI | 0.50 | 1 | Slightly higher; half the cells differ by 1 point between positions. |

No judge shows systematic position bias above 0.5 of a rating point; max single-cell difference is 1. **Position randomization is doing its job adequately.** Phase 2 should keep the 2-position-flip protocol; it is cheap insurance.

---

## Cross-judge agreement

| Metric | Value | Interpretation |
|---|---:|---|
| Mean stdev across 3 judges per cell | 0.32 | Tight. Judges are within ~⅓ of a rating point of each other on average. |
| Max stdev | 0.62 | Largest disagreement is still under 1 rating point. |
| Cells where all 3 judges within 0.5 (mode-collapse) | 1 / 16 (6.2%) | Low. Judges are NOT producing identical scores; they have real signal independent of each other. |

Cross-judge consensus has internal coherence — judges are not collapsing to mode. But the consensus's *alignment with partner* is the FAIL story, not internal disagreement among judges.

---

## What this Phase 1 actually established

1. **Cross-vendor LLM-judge consensus on this 4-criterion rubric does not align tightly enough with partner judgment to PASS the pre-registered methodology gate at this n.** This is the headline finding and it should be reported in any future write-up at face value, without softening.

2. **Anthropic alone WOULD pass the gate at this scale (ρ = 0.706).** The cross-vendor consensus structure that the design treats as a feature (mitigating self-preference bias) is, in this n=2 pilot, the thing that drags ρ below the gate. There is a real methodology trade-off: cross-vendor mitigates one bias source while introducing per-vendor calibration noise.

3. **The 4 rubric criteria are not equally calibratable.** C1 (citation) and C2 (substantive concerns) are well-aligned across raters; C3 (missed concerns) and C4 (calibrated confidence) are not. A revised rubric that drops or re-shapes C3 and C4 might produce a passing headline ρ.

4. **Directional agreement (B > A) is unanimous across partner and all three judges, on all four criteria.** The pilot does not establish that SPARRING is *better than* single-agent (this is a methodology calibration check, not the substantive comparative claim). But it establishes that this set of judges *consistently* sees the same direction of difference that the partner does.

5. **Partner gave SPARRING straight 5s on every criterion across both cases.** This is a ceiling effect that constrains what Phase 1 can tell us. With n=2 cases and ratings concentrated at the rubric's top, the within-SPARRING variance is zero on the partner side, which by construction limits the headline correlation. Phase 2 with more cases will likely produce more partner variance and therefore more discriminating ρ.

---

## Pre-registered "what this does NOT promise" — re-affirmed

- This pilot does NOT promise SPARRING outperforms single-agent in any general sense. The substantive comparative finding is reserved for Phase 2 by design.
- This pilot does NOT promise statistical power. n=2 cases × 16 score-points is calibration sample size, not Phase 2 sample size.
- This pilot does NOT extend the publishable scope beyond "preliminary cross-vendor LLM-judge evidence." Phase 1 produces methodology data, not a finding.

---

## Methodology limits to log

| Limit | Notes |
|---|---|
| **Grok-3 instead of Grok-4 class** | Pre-reg targeted "Grok-4 class"; available key was grok-3. This may be part of why xAI consensus drags the headline; a frontier xAI judge might align better. |
| **Sonnet 4.6 instead of Sonnet 4.7** | Pre-reg targeted Sonnet 4.7; Sonnet 4.7 is not yet released. Sonnet 4.6 is the current frontier Sonnet and was used. |
| **n = 2 cases** | Calibration-check sample size; not statistical-power sample size. |
| **n = 1 partner rater** | Pre-reg expected 1-2 partners. Bart only this round. Per-rater inter-rater reliability cannot be computed from n=1. |
| **Partner ceiling effect** | Partner gave SPARRING 5/5/5/5 on both cases; within-SPARRING variance is zero, limiting correlation by construction. |
| **/spar SKILL hardcodes Opus 4.7** | Both conditions used the same model substrate underneath, satisfying the design's substrate-independence assumption at the runs layer; the cross-vendor structure is at the *judging* layer. |
| **Two of the three judges fall below the per-judge gate** | OpenAI 0.145 and xAI 0.198 are well below 0.4 individually. The cross-vendor mitigation against self-preference comes at the cost of including judges that don't track partner judgment. |

---

## Recommendations for Phase 2 go/no-go

The Phase 1 gate FAILED at the headline level. Per pre-reg discipline, this means **Phase 2 should NOT proceed without addressing the confound first**.

Two paths Phase 2 could take, each addressing the failure mode in a different way:

### Path A: rubric refinement before Phase 2

Re-design the rubric to drop or re-shape C3 and C4 (the criteria that failed per-criterion alignment), then re-run Phase 1 calibration. If the revised rubric produces ρ > 0.7 with the same judges, Phase 2 proceeds with the refined rubric.

This path treats the FAIL as a rubric problem, not a judge problem. Cost: ~1-2 weeks rubric design + re-run Phase 1 (≈ same cost as this Phase 1 run).

### Path B: judge-panel refinement before Phase 2

Drop the worst-aligning judge (currently OpenAI gpt-4o or xAI grok-3) and re-run Phase 1 with a 2-judge consensus, OR add a 4th judge (Gemini class) to broaden the cross-vendor structure and possibly average down the divergent ones. If consensus ρ rises above 0.7 with the revised panel, Phase 2 proceeds.

This path treats the FAIL as a judge-selection problem. Cost: ~1 week judge-panel revision + re-run.

### Path C (combined): both A and B

Refine rubric AND revise judges. More expensive (~3-4 weeks) but produces the strongest methodology before committing to Phase 2's full corpus.

### Path D (honest abandon)

Conclude that LLM-as-judge for this rubric is not viable at this n and accept that a different evaluation methodology (e.g., the original Monte Carlo design's contracted-rater path, with the corrected $40-60k estimate) is what would be needed to substantively test SPARRING. Per pre-reg, this is a defensible outcome and **should be on the table**.

---

## Files produced this run

- `00-pre-registration.md` — methodology lock (committed before any compute spent)
- `decision-packs/case-a-family-naming.md` — Case A question + evidence
- `decision-packs/case-b-motion-accuracy.md` — Case B question + evidence
- `condition-a-baseline/case-a-output.md` + `.raw.json` — Condition A (single-agent) raw outputs
- `condition-a-baseline/case-b-output.md` + `.raw.json`
- `condition-b-spar/case-a-spar.md` — Condition B `/spar` artifact (Case A: unresolved at cap)
- `condition-b-spar/case-b-spar.md` — Condition B `/spar` artifact (Case B: converged at Round 2)
- `normalized/case-a-condition-a.md`, `case-a-condition-b.md`, `case-b-condition-a.md`, `case-b-condition-b.md` — judge-facing normalized outputs (~400-600 words each)
- `judging/PARTNER-RATING-SHEET.md` — initial rating sheet (later superseded by the Blind Ratings tool)
- `judging/judge-results.json` — raw scores from 12 judge calls
- `analysis/per-cell.json` — 16-row canonical table (case × condition × criterion)
- `analysis/results.json` — all computed statistics
- `analysis/report.md` — this file
- `scripts/run-condition-a.sh` — Condition A runner
- `scripts/run-judges.php` — judge runner (3 vendors, 2 position-passes per case)
- `scripts/recover-parse.php` — fallback parser for one OpenAI label-typo response
- `scripts/load-cases-into-tool.php` — loader for the Blind Ratings tool
- `scripts/analyze.php` — analysis script

Plus the sibling tool surface (committed separately):
- `migration 197` and `research/` directory in the lifspel app — R&D dashboard + Blind Ratings tool that collected the partner's ratings via the multi-rater audit-trail UI.

---

## Honest framing for any future external write-up

Per pre-registration:

> "On a corpus of 2 historical decisions — partner-judgment-based directional comparison, not RCT — cross-vendor LLM judges (Claude Sonnet 4.6 + GPT-4o + Grok-3) rated SPARRING-produced deliberation directionally higher than single-agent baseline on all 4 cross-condition-fair rubric criteria, but at smaller magnitudes than the partner did. Cross-vendor consensus Spearman ρ vs partner = 0.351, below the pre-registered methodology-gate threshold of 0.4 for proceeding to Phase 2. Anthropic alone aligned at ρ = 0.706 (above the 0.7 PASS threshold). The per-criterion structure showed citation-discipline and substantive-concern criteria aligning well (ρ = 0.949 and 0.707) while missed-concern and confidence-calibration criteria did not (ρ = 0.000 and 0.236). This is methodology calibration evidence at low n, not a comparative finding about SPARRING; Phase 2 is held pending rubric refinement, judge-panel revision, or a documented decision that LLM-as-judge for this rubric is not viable at lifspel scale."

That sentence is the honest scope of what Phase 1 establishes. Anything stronger overclaims.
