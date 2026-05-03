# V2 SPARRING LLM-Judge Pilot — Pre-Registration

**Status**: PRE-REGISTRATION LOCKED at commit `a20e7272` (tag `v2-prereg-2026-05-03`; originating Lifspel-private commit `a26fc103`).
**Pilot identifier**: `sparring-llm-judge-pilot-2026-05-03-v2`.
**Parent design**: [`v2-design.md`](./v2-design.md) (locked 2026-05-03).
**V1 reference**: [`00-pre-registration.md`](./00-pre-registration.md), [`preprint-draft.md`](./preprint-draft.md).
**Pre-reg commit hash**: `a20e7272` (committed 2026-05-03; tag `v2-prereg-2026-05-03`).

This document is the binding pre-registration for V2. It specifies the methodology, the analysis plan, and the decision gate **before any V2 compute is spent**. Once committed, it is immutable until V2 results are produced; deviations from this plan after compute begins must be reported as protocol amendments per [Nosek et al. 2018](https://www.pnas.org/doi/10.1073/pnas.1708274114) preregistration discipline.

---

## 1. Research question

**Does the SPARRING two-role pressure-test framework produce decision-quality outputs that are measurably different from a single-agent baseline on domain-independent decision tasks, and do cross-vendor LLM-judge consensus ratings track human-partner ratings closely enough to serve as a calibration anchor under a revised rubric and gate structure?**

V1 found (preprint §4-§5) that on two SFxLS-internal cases:
- All raters (1 partner + 3 LLM judges) preferred SPARRING over single-agent baseline on every criterion (directional unanimity).
- Cross-vendor judge consensus ρ vs partner ratings was 0.351 — below V1's pre-registered PASS threshold of 0.4.
- The rubric and partner-anchor calibration premise itself surfaced structural concerns (preprint §5.2-§5.4) that V1 framed as forward-design questions for V2.

V2 extends V1 in five ways: domain-independent cases, expanded rater pool, expanded rubric (factuality + engagement dimensions), expanded scale (1-5 → 1-7), paired-comparison parallel instrument, and required rater-condition declarations. V2 keeps the same substrate (Claude on both conditions); cross-substrate is explicitly deferred to Phase 2.

---

## 2. Conditions

**Condition A (single-agent baseline)**: One Claude Opus 4.7 agent receives the decision pack and produces a recommendation, free-form, no specific framework discipline applied. Token budget: ~8000 output tokens. Single iteration. Same prompt template as V1 Condition A.

**Condition B (SPARRING)**: A `/spar` ceremony with the V1-default settings (2 iterations, two divergent specialist personas with explicit evidence-base specification, both-must-agree convergence rule, verifiable-artifact requirement on the Challenger). Both Generator and Challenger sub-agents run on Claude Opus 4.7. Same `/spar` skill specification as V1 Condition B.

**Substrate**: Claude Opus 4.7 underneath both conditions (matches V1; V2 §5 cross-substrate deferred to Phase 2 per [`v2-design.md`](./v2-design.md)).

---

## 3. Cases

Two newly-authored decision packs, both from domains outside SPARRING, SFxLS, and Lifspel. Drafted alongside this pre-registration; locked at the same commit:

- **Case A — Organizational vocabulary in an employee handbook**
  - Domain: organizational behavior / HR policy.
  - Decision-shape: choice among three lexical conventions for canonical address terms in a values document.
  - File: [`v2-decision-packs/case-a-organizational-vocabulary.md`](./v2-decision-packs/case-a-organizational-vocabulary.md).
  - Pack-fidelity status: independently fact-checked at lock per V2 design rule (`v2-design.md` "Process changes" §1).

- **Case B — Introductory programming curriculum architecture**
  - Domain: computer-science education / curriculum design.
  - Decision-shape: choice between concepts-first and language-first architectural paths for a community-college Intro to Programming course.
  - File: [`v2-decision-packs/case-b-introductory-programming-curriculum.md`](./v2-decision-packs/case-b-introductory-programming-curriculum.md).
  - Pack-fidelity status: independently fact-checked at lock.

Both cases are partner-rateable without specialized domain expertise. Both have public evidence bases (organizational-behavior research; CS-education research) so the C5a factuality criterion can be exercised by raters who choose to verify externally.

The two-case structure mirrors V1's two-case structure for analytical comparability where applicable. Sample size remains "calibration-check" not "effect-size-estimation."

---

## 4. Rubric (six criteria, 1-7 anchored scale)

Six criteria, each scored 1-7 on an anchored scale with behavioral anchors at every level. Per-criterion anchors live in [`v2-design.md`](./v2-design.md) under "V2 rubric." Reproduced summary:

| ID | Name | Type |
|---|---|---|
| C1 | Verifiable artifact citation | Direct-inspection |
| C2 | Substantive vs theatrical concerns | Direct-inspection |
| C3 | Missed real concerns | Counterfactual-inference |
| C4 | Calibrated confidence | Counterfactual-inference |
| C5a | Factuality | Factuality (new in V2) |
| C5b | Engagement-with-source | Engagement (new in V2) |

The anchored 1-7 scale carries behavioral anchors at every level. The full per-level / per-criterion anchor tables are reproduced in [`v2-design.md`](./v2-design.md) and rendered in the rating tool form. The rubric criteria themselves are the V1 four (C1-C4 inherited unchanged in semantics, re-anchored for the wider scale) plus the V2 two (C5a, C5b — the factuality / engagement split addressing the V1 §5.4 pack-fidelity-vs-ground-truth-fidelity gap).

---

## 5. Two instruments per rater per case

V2 collects two instruments per rater per case:

### 5a. Absolute scoring (1-7 per criterion, per side X and Y)

For each case, each rater scores Answer X and Answer Y independently on the six-criterion 1-7 anchored scale. 6 criteria × 2 sides = 12 score points per rater per case. Across 4 raters × 2 cases × 12 score points = 96 absolute-score data points.

### 5b. Paired comparison (per criterion)

For each case, each rater also indicates a per-criterion paired-comparison preference: "X better," "Y better," or "tied" — one of three for each of the six criteria. Across 4 raters × 2 cases × 6 criteria = 48 paired-comparison data points.

Both instruments are required. The rating tool blocks submission until both passes are complete.

---

## 6. Rater pool and rater-condition declaration

V2 uses a four-rater pool:

| Rater | Identifier | Role / SPARRING-author status |
|---|---|---|
| Bart Niedner | "Breegarra" / user_id=1 | RF partner; SPARRING framework author |
| Matthew Niedner | "Icarus" / user_id=7 | RF partner; conceptual architecture co-author |
| Kelly Cope | "Worthless Wizard" / user_id=4 | RF partner; not a SPARRING-framework author |
| Michelle Niedner | `michelle.niedner@gmail.com` / user_id TBD at provisioning | External; not RF, not SPARRING-author |

Three of four raters are RF partners; one is fully external. Two of four raters are SPARRING-framework non-authors (Kelly + Michelle). This expanded pool addresses the V1 §6 author-as-rater limitation directly.

**Rater-condition declaration is a required field per CONSORT 2010 item 11a affirmative-declaration discipline.** Each rater declares their rating condition before submission:
- **Unaided** — no AI assistance, no co-reader, no parallel session.
- **AI-assisted** — used Claude / ChatGPT / Gemini / other LLM during the rating session. Free-text required: which AI, what for.
- **Co-reader-aided** — discussed the materials with another person during the rating session. Free-text required: who, what for.
- **External-source-checked** — verified the decision pack's claims against external sources (papers, references, codebases, etc.). Free-text required: what was checked, what was found.
- **Multiple of the above** — free-text required for each that applies.

This closes V1's silence-as-permission inference. Per CONSORT 2010 default, "blind rating" without further specification is read as blind-AND-unaided; V2 makes the rater's condition explicit so analysis can either restrict to a comparable-conditions subset or treat the asymmetry as a declared confound.

---

## 7. Cross-vendor LLM judge panel (V1 panel, inherited unchanged)

Three independent LLM judges, each receiving the same six-criterion rubric and rating Answer X and Answer Y on the 1-7 scale per criterion:

- **Anthropic** — `claude-sonnet-4-6` (matching V1; if a newer Sonnet is the current frontier at run time, V2 substitutes per the same pre-reg discipline V1 used for model substitutions).
- **OpenAI** — `gpt-4o`.
- **xAI** — `grok-3` (or current frontier Grok).

Each judge runs each case TWICE, with X/Y positions flipped between passes (V1 position-randomization protocol, inherited unchanged, addresses Wang et al. 2023 position bias). Per-judge per-criterion per-condition score = mean of the two passes. Total LLM judge calls: 2 cases × 3 judges × 2 position-passes = 12 (same as V1).

Cross-vendor consensus is computed as the mean across the three judges for each (case, condition, criterion) cell.

Note on the V1 finding (preprint §5.1): the cross-vendor structure cost more in calibration noise than it saved in self-preference mitigation when both conditions shared a substrate (Claude). Because V2 keeps both conditions on Claude (per Q5 Phase-2 deferral), this cost-benefit asymmetry is expected to persist. We retain the cross-vendor structure for V2 anyway because (a) it preserves V1-vs-V2 methodology comparability on this dimension, and (b) Phase 2 cross-substrate work depends on cross-vendor calibration evidence collected here.

---

## 8. Analysis plan

### 8a. Cross-rater statistics (within-rater-condition only)

Cross-rater statistics are computed only across raters in comparable rating conditions. Per the rater-condition declarations:

- If all four raters are unaided → cross-rater statistics computed across n=4.
- If a subset of raters is unaided → cross-rater statistics computed on the unaided subset only; non-unaided raters reported as exploratory standalone (analogous to V1 §4.8).
- If no rater is unaided → cross-rater statistics computed across the largest comparable-condition subset; report condition explicitly.

**Reported per-criterion**: Spearman ρ across raters, mean across raters, stdev across raters.

### 8b. Per-judge / cross-vendor alignment vs partner ratings

Per V1's analysis plan, computed against the *primary partner reference* defined here as the unaided RF-partner subset (Bart, Matthew, Kelly — to the extent each rates unaided). If only some are unaided, the primary reference is restricted to the unaided ones.

**Reported per-criterion**:
- Spearman ρ between each LLM judge's scores and the primary-reference partner mean.
- Spearman ρ between cross-vendor consensus (mean of 3 judges) and the primary-reference partner mean.
- Per-judge per-criterion absolute alignment.

### 8c. Two-tier per-criterion gate classification

Per the locked Q4 decision in `v2-design.md`:

- **High-tier criteria (C1, C2, C5a, C5b)**: ρ ≥ 0.7 PASS, 0.4-0.7 BORDERLINE, < 0.4 FAIL.
- **Lower-tier criteria (C3, C4)**: ρ ≥ 0.5 PASS, 0.2-0.5 BORDERLINE, < 0.2 FAIL.

**Aggregate V2 gate classification**:
- **PASS**: ≥ 5 of 6 criteria PASS at their respective tier AND no criterion FAIL.
- **BORDERLINE**: ≥ 4 of 6 criteria PASS AND no more than 1 criterion FAIL.
- **FAIL**: any other outcome.

### 8d. Paired-comparison aggregate analysis

For each criterion, count "X better" / "Y better" / "tied" votes across raters and across cases. Report:

- Per-criterion vote distribution (counts and proportions).
- Per-criterion sign agreement: did the per-criterion paired-comparison majority match the per-criterion absolute-score B − A direction?
- A binomial test on the per-criterion paired vote distribution (against a null of P(X better) = P(Y better) = 0.5, ties excluded). Report p-values and effect sizes.

The paired-comparison analysis is the ceiling-resistant signal V1 lacked. It produces a signal even if absolute scores saturate (per the V1 §4.7 ceiling effect).

### 8e. Per-criterion delta (B − A; framework minus baseline)

Inherited from V1 §4.4. Reported per partner, per LLM judge, per cross-vendor consensus, per criterion. Mean across criteria reported. The partner-ratings-of-condition-α delta (Bart in V1 was +1.125; V2 reports the unaided-partner-subset mean delta).

### 8f. Direction-of-preference unanimity check

V1 found unanimous directional preference for SPARRING across all raters and criteria. V2 reports the same metric: across raters × judges × criteria × cases, what fraction prefer SPARRING vs baseline? Pure descriptive; no statistical test attached, since direction-of-preference unanimity is interpreted in §5 of the V2 preprint, not as a confirmatory result.

---

## 9. Pre-registered hypotheses

These are the V2 hypotheses, locked before any compute is spent:

**H1 (replication of V1 directional finding)**: SPARRING will be rated higher than single-agent baseline on the majority of criterion-cells (across criteria × raters × judges). Pre-registered direction.

**H2 (cross-vendor consensus tracks unaided partner reference under two-tier gate)**: At least 4 of 6 criteria will pass their respective tier in §8c (i.e., V2 will be classified as PASS or BORDERLINE on the aggregate gate).

**H3 (paired-comparison shows direction even if absolute scores saturate)**: For at least 4 of 6 criteria, the paired-comparison per-criterion majority will be statistically distinguishable from a 50/50 null (p < 0.05 binomial, ties excluded).

**H4 (rater-condition asymmetry, if any, is bounded)**: If raters split between unaided and AI-assisted conditions, the cross-condition Spearman ρ on absolute scores within each criterion will be ≥ 0.4 (i.e., the condition asymmetry does not produce wholesale rater-pool incoherence). This hypothesis is contingent on at least one rater being non-unaided; if all raters declare unaided, H4 is vacuously satisfied and not reported.

H1, H2, H3 are the primary hypotheses. H4 is the methodology-validation hypothesis on rater-condition.

---

## 10. Stopping rule and protocol-amendment policy

**Stopping rule**: V2 compute completes when all four raters have submitted both instruments (absolute scoring + paired comparison) on both cases AND all 12 LLM judge calls have completed (2 cases × 3 judges × 2 position-passes). After that point, the dataset is locked and analysis runs.

**Protocol-amendment policy**: Any deviation from this pre-registration after compute begins must be reported as a protocol amendment, with the deviation, the date, the rationale, and the affected analysis steps recorded in the V2 preprint per [Nosek 2018](https://www.pnas.org/doi/10.1073/pnas.1708274114) and [APS *Observer* on preregistration deviations](https://www.psychologicalscience.org/publications/observer/methods-preregistration-deviations.html). Deviations include: rater drop-out, model substitutions, rubric anchor changes, gate-threshold adjustments, analysis-plan additions or removals.

If a rater drops out, the V2 dataset reports n=3 raters with explicit attribution of which rater dropped and at what stage. Cross-rater statistics restrict to the comparable subset.

---

## 11. What this study will and will not be able to conclude

**Will be able to conclude**:
- Whether SPARRING produces directionally-preferred outputs over single-agent baseline on domain-independent decision tasks, with a four-rater pool and cross-vendor judge consensus (descriptive, not inferential, given the calibration-check sample size).
- Whether the V2 rubric — six criteria with two-tier gates — produces a more interpretable signal than V1's single-tier gate.
- Whether the paired-comparison instrument produces a ceiling-resistant signal that complements absolute scoring.
- Whether the rater-condition declaration discipline produces interpretable cross-rater statistics under conditions of mixed rater-condition.

**Will not be able to conclude**:
- "SPARRING is better than single-agent baseline" as a confirmatory claim with effect-size estimate. n=2 cases, n=4 raters, no power calculation.
- Generalization to other deliberation frameworks, other domains, or other rater pools.
- Substrate-independence of the framework (cross-substrate Phase 2 work required).
- Long-term stability of the rubric or judge panel (Phase 2+ replication required).

---

## 12. Pre-registration commit + tag flow

This document is the V2 pre-registration. It locks the design and analysis plan. The lock procedure:

1. **Author finalizes** `v2-design.md`, this document, the two decision packs, and the schema migration. Independent fact-check pass on the decision packs (by Bart in this case, since the cases are non-SFxLS and Bart is not the author of the case-content domain knowledge).
2. **Single atomic commit** containing:
   - `v2-design.md` (locked)
   - `v2-00-pre-registration.md` (this document)
   - `v2-decision-packs/case-a-organizational-vocabulary.md`
   - `v2-decision-packs/case-b-introductory-programming-curriculum.md`
   - `database/migrations/198_v2_eval_schema.sql`
   - Commit message naming "v2-prereg lock" explicitly for git-log retrievability.
3. **Tag** the commit with `v2-prereg-2026-05-XX` (XX is commit date).
4. **Capture the commit SHA** in a one-line edit to this document, replacing the "Pre-reg commit hash: TBD" line at the top with the actual hash. That one-line edit goes in a separate immediately-following commit so the original lock commit is preserved unmodified.

After step 4, no edits to this document, the design doc, the decision packs, or the migration are permitted without a protocol amendment commit that explicitly notes the deviation.

V2 compute begins after step 4. The schema migration is run on nonprod. The rating-tool form/API updates are deployed. The four raters are briefed and rate. The 12 LLM judge calls are spent. Analysis runs against the locked plan.

---

## 12.1. Protocol amendments

Amendments to this pre-registration after lock are appended below in chronological order, per the Nosek 2018 / APS *Observer* deviation-disclosure discipline named in §10. Each entry records: amendment date, scope, rationale, what changed, and whether any rater data had been collected at the time of amendment (the integrity-relevant question).

### Amendment 2026-05-03 — format symmetry between Cond A and Cond B outputs

**Date**: 2026-05-03 (same calendar day as pre-reg lock).
**Pre-rater-data**: YES — no rater data had been collected at the time of this amendment. Amendment was applied during the operational deployment phase (§13 logistical follow-ups), specifically immediately after `rd_eval_cases` was populated with the V2 condition outputs and before the first rater session opened.
**Scope**: Display format of the Condition B outputs in the rating tool, for both cases.
**Rationale**: A blinding-integrity defect was observed during pre-rater operational review. The Condition A outputs (single-agent baseline) rendered as clean decision-quality recommendations with the structure `## Recommendation / ## Reasoning chain / ## Concerns / ## Confidence`. The Condition B outputs (SPARRING ceremony) rendered as the full `/spar` ceremony record, including `## Personas`, `## Iteration log` with `### Round 1` / `### Round 2` headers, persona signal blocks (`{agree: true|false, reasoning: "..."}`), `## Final evaluation`, `## Artifacts surfaced`, and `## Recommendation for parent` sections. This format asymmetry is visually distinctive at first glance (before any content is read) and breaks the blind: a rater can identify which side is the SPARRING output purely from structural cues, regardless of the X/Y position-randomization that anonymizes condition assignment. Per Schulz & Grimes (*Lancet* 2002), blinding requires that allocation be concealed at the level of the comparison instrument, not merely at the level of the labels on the comparison. Format-level cue-leakage falls within the scope of the Schulz & Grimes blind-integrity standard.
**What changed**: For both cases, a new format-symmetric recommendation file was authored alongside the existing ceremony record:
- Case A: [`v2-condition-b-spar/case-a-recommendation.md`](./v2-condition-b-spar/case-a-recommendation.md) (new) alongside [`v2-condition-b-spar/case-a-spar.md`](./v2-condition-b-spar/case-a-spar.md) (preserved, ceremony audit trail).
- Case B: [`v2-condition-b-spar/case-b-recommendation.md`](./v2-condition-b-spar/case-b-recommendation.md) (new) alongside [`v2-condition-b-spar/case-b-spar.md`](./v2-condition-b-spar/case-b-spar.md) (preserved, ceremony audit trail).

The new recommendation files preserve the substantive content of the SPARRING ceremony output (recommendations, citations, residual concerns, confidence calibration) but match Condition A's section structure (`## Recommendation / ## Reasoning chain / ## Concerns / ## Confidence`). The original ceremony records are retained unchanged as the audit trail of what the `/spar` ceremony actually produced — they remain the methodologically authoritative artifact for any post-hoc analysis questions about the ceremony itself. The recommendation files are the rater-presented form.

The `rd_eval_cases` rows on nonprod were updated to point to the new recommendation files: case id=3 (Case A) `answer_y_text` (Y=sparring per the locked X/Y assignment) replaced; case id=4 (Case B) `answer_x_text` (X=sparring per the locked X/Y assignment) replaced.

**Affected analysis steps**: None of the §8 analysis plan steps depend on the Condition B output format — they depend on rater scores and judge scores, which are produced from the rater/judge view of the materials. The amendment changes the rater/judge view of the Condition B side from "ceremony record" to "recommendation," which is the methodologically correct view for the comparison the pre-registration specifies (decision-quality output of the framework vs. decision-quality output of the baseline).

**Tag**: `v2-prereg-amendment-2026-05-03-format-symmetry` (applied to the amendment commit).

---

## 13. Logistical follow-ups (must complete before V2 compute begins)

These are operational tasks, not design decisions. They are tracked in [`v2-design.md`](./v2-design.md) "Logistical follow-ups." Summary:

1. Provision Michelle Niedner with rating-tool access (`michelle.niedner@gmail.com`).
2. Brief Kelly on the V2 rating methodology.
3. Brief Michelle similarly, with an introductory note on what SPARRING is at a high level.
4. Run the V2 schema migration on nonprod and prod.
5. Deploy the rating-tool form/API updates to support the V2 schema.
6. Run V2 conditions against the two decision packs and load the outputs into `rd_eval_cases` (anonymized X/Y assignment).

Once 1-6 are complete, raters can begin V2 ratings. The four raters ideally rate within the same calendar week to minimize time-of-rating drift, though this is a soft target rather than a pre-registered constraint.

---

## 14. Acknowledgments and references

V2 inherits V1's reference list (preprint §9). New V2-specific external references locked at this pre-reg:

- **Cox, E.P. (1980)**. "The Optimal Number of Response Alternatives for a Scale: A Review." *Journal of Marketing Research* 17(4), 407-422. *Justifies 1-7 scale choice over 1-5 or 1-10.*
- **Krippendorff, K. (2004)**. *Content Analysis: An Introduction to Its Methodology*, 2nd ed. Sage. §11 on rubric construction. *Justifies the C5a/C5b split over a combined C5; supports the rater-pool n ≥ 3 minimum for inter-rater reliability.*
- **Liang, P., et al. (2023)**. "Holistic Evaluation of Language Models (HELM)." *arXiv*:2211.09110. *Source for faithfulness vs. factuality split convention.*
- **Es, S., James, J., Espinosa-Anke, L., & Schockaert, S. (2023)**. "RAGAS: Automated Evaluation of Retrieval Augmented Generation." *arXiv*:2309.15217. *Source for engagement-with-source dimension construction.*
- **Schulz, K.F., & Grimes, D.A. (2002)**. "Blinding in randomised trials: hiding who got what." *Lancet* 359(9307), 696-700. *Source for the CONSORT-aligned default reading of "blind" as blind-AND-unaided absent explicit permission.*
- **Calderon, N., Reichart, R., & Dror, R. (2025)**. "The Alternative Annotator Test for LLM-as-a-Judge." *ACL 2025*. *Source for the rater-pool n ≥ 3 minimum and within-condition cross-rater statistics discipline.*

---

**End of pre-registration. Ready to lock.**
