# SPARRING LLM-judge Phase 1 calibration check — pre-registration

**Timestamp**: 2026-05-02 (commit timestamp is the binding pre-reg artifact)
**Pilot**: Phase 1 calibration check of the LLM-judge pilot methodology described in [`docs/bfn/sparring-llm-judge-pilot-design.md`](../sparring-llm-judge-pilot-design.md)
**Status**: Pre-registered before any runs. This file is the binding intent; runs that follow may not deviate from this plan without an amendment commit that supersedes this one and explains the deviation.
**Convention**: Informal pre-registration per the design doc §41 — a timestamped commit in the lifspel repo, not OSF formal registration. The publishable scope is exploratory-evidence-grade, not confirmatory; informal pre-reg is methodologically appropriate to that scope.

This pre-registration locks in the methodology BEFORE any compute is spent or any output is produced, so that the eventual results cannot be retrofitted to a methodology choice that would have produced a more favorable answer.

---

## Question this pilot answers

**Do cross-vendor LLM judges' ratings of paired (single-agent baseline, SPARRING) deliberation outputs correlate with the partner's ratings of the same outputs on the same rubric?**

Decision gate (per design doc §229-237):
- **Spearman ρ > 0.7**: judges align with partner judgment. Phase 1 passes; Phase 2 full pilot is methodologically defensible.
- **Spearman ρ ∈ [0.4, 0.7]**: partial alignment. Report findings with explicit caveats about which dimensions agree and which don't. Phase 2 contingent on partner judgment.
- **Spearman ρ < 0.4**: methodology has a confound. LLM judges are measuring something different from what partners would measure. Phase 2 is NOT pursued without addressing the confound. Surface honestly.

This Phase 1 does NOT answer "does SPARRING produce better deliberation than single-agent baseline?" That question is reserved for Phase 2 and depends on Phase 1 passing the gate. Phase 1 is purely a methodological calibration check.

---

## Cases (n=2, partner-selected from CRv2 backlog/board)

### Case A — P13M1 Taxonomy: Family-naming convention

**Source**: [`docs/plans/CRv2/crv2-p13m1-taxonomy-brainstorm.md`](../../plans/CRv2/crv2-p13m1-taxonomy-brainstorm.md) §9 Open Question 4. Decision is genuinely pending Chal+Icarus formal P13M1 scoping.

**Decision**: Three options for naming Family-tier nodes in the racial taxonomy:
1. Scientific-Latin-style (e.g., `Primatidae-SFxLS`)
2. Explicitly-engineered names (e.g., `Sapient-Bipedal-Family`)
3. Mixed register (Latin when real-biology-grounded, English when SFxLS-invented)

**Domain**: narrative architecture + LLM-grounding + authoring-tool UX. Cross-domain decision with no obvious single-axis right answer.

### Case B — Motion-accuracy coupling: design shape

**Source**: [`docs/chatlogs/202604230218.inflight.thread-200-vasik-followups.md`](../../chatlogs/202604230218.inflight.thread-200-vasik-followups.md) SCOPE-CANDIDATE block. Currently DEFERRED per Vasik Standard 8 gate (sim-driven failure required). The decision question for the spar is the design-shape if/when promoted, not the prior promotion question.

**Decision**: Three sub-questions defining the design space:
1. Coupling curve shape — linear in gait fraction vs stepped (walk/trot/canter/gallop)
2. Interaction with `trained_archer_mount` flag — slope reducer vs threshold shifter
3. Scope boundary — targeting-layer (P14-successor) vs action-resolution-layer (encounter mechanics)

**Domain**: combat-engine technical with cited historical biomechanics. Substantively different domain from Case A, providing cross-domain coverage in n=2.

---

## Conditions

Two conditions per case. Optional Condition C from the design doc (two-agents-without-framework) is **NOT included in Phase 1** to keep scope tight. Phase 2 may add it.

### Condition A — single-agent baseline

Single Claude Opus 4.7 call. The model receives the case's decision pack (question + evidence base summary) plus a structured-output prompt requiring:
- Single-paragraph **recommendation** (chosen option + main rationale)
- Bulleted **key concerns** list
- Stated **confidence** (low / medium / high) with one-sentence justification

Target word count: 400-600 words after normalization (raw output may be longer; normalization step trims).

### Condition B — SPARRING

`/spar 2 <decision pack>`. Two divergent specialist personas with explicit evidence-base specification per `/spar` SKILL Step 2. Generator + Challenger via Agent tool, model: opus. 2-iteration cap. Spar artifact saved to standard `.claude/spars/2026-05-02/` location.

Output normalization step extracts "Final evaluation" + "Recommendation for parent" sections from the spar artifact and reformats to the same shape as Condition A. Iteration log, role-shift language, agreement signals, and disagreement-at-cap menu are NOT shown to judges. Judges see substance, not structural artifact.

---

## Model versions

| Role | Vendor | Model |
|---|---|---|
| Condition A baseline | Anthropic | claude-opus-4-7 |
| Condition B Generator + Challenger | Anthropic | claude-opus-4-7 (per /spar SKILL hardcode) |
| Anthropic judge | Anthropic | claude-sonnet-4-7 |
| OpenAI judge | OpenAI | gpt-4o |
| xAI judge | xAI | grok-3 |

**Methodology limit logged**: design doc §85-89 specified "Grok-4 class" as cross-vendor judge minimum. Available key is grok-3. This substitution is logged here pre-registration; will appear in Phase 1 report under "stated methodology limits." Grok-3 still satisfies the design's structural intent (cross-vendor judges, not same-vendor self-preference); the limit is on frontier-model parity, not on cross-vendor structure.

---

## Output normalization protocol

Before any judging, all 4 outputs (2 cases × 2 conditions) are normalized to identical shape:

- Single paragraph recommendation, ~150 words
- Bulleted concerns list, ~5-8 items
- Confidence declaration: `low | medium | high` + one-sentence justification
- Total word count target: 400-600 words

For Condition A: model is prompted in this shape directly.

For Condition B: extract "Final evaluation" + "Recommendation for parent" from the spar artifact. Strip framework scaffolding (role-shift language, "agreement signals", disagreement-at-cap menu, iteration log markers, /spar-specific terminology). Reformat to the target shape preserving substantive recommendation, concerns, and confidence-equivalent language.

The normalized outputs are the ONLY material judges see. The format normalization is itself a methodological commitment: without it, the experiment measures format preference, not framework leverage.

---

## Per-judge rubric

Four criteria (the cross-condition-fair subset from `docs/standards/challenger-output-rubric.md` per design doc §99-110):

1. **Verifiable artifact citation** — does the recommendation cite specific evidence from the materials?
2. **Substantive vs theatrical concerns** — are concerns real risks rooted in evidence, or generic-sounding hedges?
3. **Missed real concerns** — does it surface concerns a careful reader would identify, or miss obvious ones?
4. **Calibrated confidence** — is the stated confidence appropriate to the evidence?

Score each criterion 1-5 with anchored point descriptions:

- **1**: clearly absent or actively wrong
- **2**: weakly present, multiple gaps
- **3**: adequate, some gaps
- **4**: strong, minor gaps
- **5**: exemplary, no notable gaps

Per-judge prompt template (locked verbatim, not amended per-pair):

```
You are evaluating two decision recommendations on the same question.
You don't know which recommendation came from which approach.
Score each on the 4 criteria below using the 1-5 anchored scale provided.
Do not state which you prefer overall -- score per criterion.

Question: <decision question>
Evidence base summary: <brief summary, identical to the summary used for the runs>

Answer X: <normalized output>
Answer Y: <normalized output>

Score Answer X:
- Criterion 1 (verifiable artifact citation, 1-5): ?
- Criterion 2 (substantive vs theatrical concerns, 1-5): ?
- Criterion 3 (missed real concerns, 1-5): ?
- Criterion 4 (calibrated confidence, 1-5): ?

Score Answer Y:
- Criterion 1: ?
- Criterion 2: ?
- Criterion 3: ?
- Criterion 4: ?

Output ONLY the scores in this exact format:
X1=? X2=? X3=? X4=?
Y1=? Y2=? Y3=? Y4=?
```

---

## Position randomization protocol

For each case, the (Condition A, Condition B) pair is presented to judges TWICE:
- **Pass 1**: Condition A as "Answer X", Condition B as "Answer Y"
- **Pass 2**: Condition B as "Answer X", Condition A as "Answer Y"

This addresses position bias (Wang 2023 — judges prefer first-presented answer). Per-judge per-criterion per-condition score = mean of the two-pass scores.

Total judge calls: **2 cases × 3 judges × 2 position-passes = 12 judge calls**.

---

## Partner rating protocol

Same 4-criterion rubric. Partner sees X/Y anonymized pairs with question + evidence base summary. Rates Answer X and Answer Y per criterion. NO position-flip pass for partner (partner time is the bottleneck; one rating per pair is what's affordable).

Partner ratings are the calibration anchor. Partner does NOT see judge scores until partner ratings are complete.

---

## Analysis plan

Locked before any data is in:

1. **Headline endpoint**: Spearman ρ between flattened partner score vector and flattened LLM-judge consensus score vector. Vector is per-criterion per-condition per-case (4 criteria × 2 conditions × 2 cases = 16 score points). LLM-judge consensus = mean across 3 judges (after position-averaging per judge).

2. **Per-criterion Spearman**: same correlation computed per-criterion (4 separate ρ values, one per criterion). Identifies whether partner-judge alignment is uniform across criteria or criterion-specific.

3. **Per-judge Spearman**: same correlation computed per-judge (3 separate ρ values). Identifies whether one vendor diverges systematically from partner.

4. **Per-criterion delta** (B−A): mean per-criterion (Condition B score − Condition A score) per judge and across judges. Bootstrap 95% CI not computed at n=2 case (sample too small for meaningful CI; report point estimates only).

5. **Position-bias check**: per-judge mean (Pass 1 score − Pass 2 score). Should be near zero if position randomization fully controls position bias.

6. **Cross-judge agreement**: Cohen's kappa (or just per-pair score agreement) across the 3 judges. At n=2 cases × 4 criteria × 2 conditions = 16 score points per judge, kappa is noisy but interpretable as a magnitude check.

7. **Mode-collapse detection**: count of (case, condition, criterion) cells where all 3 judges produced the same score. High count = judges aren't independent.

8. **Decision gate application**: apply the headline Spearman ρ to the design's decision-gate thresholds; report PASS / BORDERLINE / FAIL classification.

---

## Cost projection

| Item | Estimate |
|---|---|
| Condition A: 2 cases × 1 Opus call each | $1-2 |
| Condition B: 2 spars × ~4 Opus calls each (2 rounds) | $6-12 |
| Judging: 2 cases × 3 judges × 2 position-passes | $5-8 |
| **Phase 1 cash total** | **$12-22** |

Aligned with design doc Phase 1 estimate ($10-20).

Partner time (rating step): ~30-60 minutes for 2 cases × 4 criteria × 2 conditions. Far below design's 5-10 hour estimate (which assumed n=10-20 calibration sample for Phase 1; actual Phase 1 here is n=2 to keep tight).

---

## What this pre-reg explicitly does NOT promise

- It does NOT promise Condition B will outperform Condition A. The pilot may show no difference, mixed differences, or Condition A winning. All are valid Phase 1 outcomes.
- It does NOT promise Spearman ρ will pass the design's gate. Phase 1 may FAIL the gate; that's a real possible outcome and an important one (signals that LLM-as-judge for this domain isn't viable as designed).
- It does NOT promise n=2 is statistically powerful. n=2 is calibration-check sample size, not Phase 2 sample size. Spearman at n=16 score points is interpretable as a methodological signal but is NOT a publishable comparative claim about SPARRING.
- It does NOT extend the publishable-claim scope beyond what the design doc allows ("preliminary cross-vendor LLM-judge evidence consistent with SPARRING producing measurably different deliberation"). Phase 1 produces calibration data only.

---

## Provenance

Builds on:
- [`docs/bfn/sparring-llm-judge-pilot-design.md`](../sparring-llm-judge-pilot-design.md) — the methodology being validated
- [`docs/bfn/sparring-framework-notes.md`](../sparring-framework-notes.md) — substrate
- [`docs/standards/challenger-output-rubric.md`](../../standards/challenger-output-rubric.md) — rubric source (criteria 1-4 of 7)
- [`docs/chatlogs/202605020417.sparring-empirical-validation-design-arc.md`](../../chatlogs/202605020417.sparring-empirical-validation-design-arc.md) — prior session's design arc
- Two prior pressure-test spars: `.claude/spars/2026-05-02/spar-low-cost-pilot-design.md`, `spar-historical-case-corpus.md`

Per-memory note: SPARRING work is RF R&D, NOT CRv2/SFxLS PM scope. This pilot lives entirely in `docs/bfn/`.
