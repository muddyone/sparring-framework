# SPARRING Monte Carlo eval-harness -- design draft

*Sibling working draft of `sparring-framework-notes.md`. Bridges the deferred Phase 2/3 eval-harness roadmap (Appendix D) into a concrete milestone shape, modeled on the project's existing Monte Carlo machinery in the combat engine.*

*Status: DRAFT (2026-05-02, rev. 2 after two spars). Not yet a milestone; this is the design doc that would back one. Two pressure-test spars on this design landed in the same session: `spar-low-cost-pilot-design` (UNRESOLVED at cap on the framework-vs-no-framework comparative path under $400) and `spar-historical-case-corpus` (substantive convergence + narrow packaging hold on whether historical-case corpus can substitute for missing internal corpus). The findings from both have been incorporated in this rev.*

## Purpose

The framework's quality leverage rests on structural argument plus one production reference implementation. The Phase 1 measurement instrument (`docs/standards/challenger-output-rubric.md`) measures Challenger output quality at the per-output level, sampled by partners on a regular cadence. What's missing -- and what this design proposes -- is the empirical layer that converts per-output rubric scores into statistical confidence intervals on framework leverage: same decision run N times, varied inputs, ground-truth outcomes where available, ablation against single-agent baselines and across the framework's named disciplines.

This is the deferred Appendix D Phase 2/3 work, scoped explicitly so it can ship as one or more milestones rather than remaining indefinitely deferred.

## Precedent: the combat-engine Monte Carlo

The project already runs Monte Carlo simulations as a measurement methodology. The combat engine's `storyforge/tests/api/monte-carlo.php` runs N simulated combats (capped at 500, default 100) per (combatant A x combatant B) configuration, with toggle-able feature flags (eyes, guard stance, tempo norm, power concentration, focus bonus, engagement range, durability, scale factors), aggregates win/loss/draw distributions, and posts to Claude Haiku for balance analysis. The methodology is well-understood and the cost ceiling (500 sims per cell) is tunable per experiment.

The SPARRING harness inherits the same shape: N runs per (topic x configuration) cell, distribution rather than point estimate, configurable ablation toggles, statistical confidence intervals in the output. The difference is the unit -- combat sims are deterministic-given-seed dice rolls; SPARRING runs are stochastic LLM calls -- but the per-cell-distribution methodology is the same.

## Unit of measurement

Three candidate units, each answering a different question:

1. **Convergence consistency.** Run `/spar` N times on the same topic with personas freshly generated each iteration. Measure: convergence rate, distribution of revisions surfaced, distribution of final recommendations. Question answered: *does SPARRING produce stable outputs for a given decision shape, or are convergences a function of which agents got rolled?*

2. **Correctness against ground truth.** Hold out a corpus of decisions where the right answer is known (in retrospect, by partner judgment, or by deliberately constructed test cases). Run `/spar` and score converged outputs against ground truth. Question answered: *does SPARRING-converged correctness exceed single-agent-baseline correctness, and by how much?*

3. **Per-criterion rubric score distributions.** Run the existing 7-criterion rubric (`docs/standards/challenger-output-rubric.md`) at scale, automated via LLM-as-judge calibration against partner-scored anchor sets. Question answered: *which Challenger surfaces produce the highest-quality challenges; which criteria show the most variance; where are the regressions?*

The harness should support all three; the milestone shape determines which to ship first.

## Test corpus

The corpus is the single biggest pre-shipping investment. Four candidate construction paths -- with honest assessments updated by the two pressure-test spars:

- **Archived `/spar` runs whose outcomes have been validated.** Partner judgment on past spars plus any production outcomes that closed the loop. **Status (2026-05-02): only 2 archived spars exist, BOTH framework-on-itself meta-decisions** (submission-worthiness; post-6 closer-readiness). Per the self-citation circularity rule (`sparring-framework-notes.md` failure-mode catalog), using these as comparative-eval corpus would trip the rule the framework adopted yesterday. Honest treatment: tag as dogfooding artifacts, do not use as comparative-eval corpus. Will become useful as corpus only when many non-framework-internal spars accumulate organically (3-6+ months by usage projection).

- **Sharma 2023 preference-flip prompts adapted to multi-agent settings (per spec Section 5.4).** Established sycophancy benchmark with known correct answers and known opposing-view formulations. Tests pleasing-bias defense specifically. **Status:** narrow but defensible -- tests one named failure mode against an external benchmark. Cleanest single-failure-mode test available at low cost.

- **Lifspel-internal deliberately-constructed test cases.** Decision shapes the framework should handle well, plus shapes it should not (Applicability Gate validation). **Status revised (per `spar-low-cost-pilot-design` Generator R2 acknowledgment): manufacturing 8+ "real-stakes" cases on demand destroys ecological validity** -- you cannot manufacture authentic high-stakes decisions on a study schedule. Useful only for Applicability Gate validation (where artificiality is actually appropriate), not for quality-leverage testing.

- **Historical case studies (Yin/Janis/Allison case-study research tradition).** Per `spar-historical-case-corpus` finding: methodologically defensible IN PRINCIPLE as theory-testing multiple-case design with structured-focused comparison + within-case process tracing. **Status at lifspel scale: NOT TRACTABLE today.** Five non-negotiable disciplines required (OSF pre-registration; blinded two-reviewer packet certification; four-cell process-x-outcome stratification; process-quality scoring as headline; scope-limited publishable claim). Two of the five (#2 blinded packet certification; #3 adversarial co-curator with the four-cell stratification) cannot be met at lifspel scale -- #3 is *structurally* impossible for an unpublished framework with no external critic pool. Realistic cost when feasible: $40-60k contracted labor + 18-month timeline + requires academic partnership. Becomes a viable Phase E only after the framework has external touchpoints (workshop paper, blog drawing critics, academic collaborator).

**No first-pass corpus is currently buildable at lifspel scale that would support a methodologically-defensible framework-vs-no-framework comparative claim.** The honest near-term path is Phase A variance checking on individual non-framework-internal real Lifspel decisions, not a comparative-corpus study. See "Phasing" below.

## Ablations

The framework's structural commitments become testable once the corpus and harness exist. Each ablation answers a "which discipline is doing the work" question:

| Ablation | Holding constant | Varying | Answers |
|---|---|---|---|
| Single-Challenger vs disjoint-evidence Challenger | Topic, personas | Whether D2 (distinct evidence) is satisfied | How much leverage does D2 contribute? |
| Same-model vs cross-model | Topic, personas, evidence bases | Model substrate (single-vendor vs cross-vendor) | How much theatrical-adversariality residual does cross-model close? |
| Persona-only vs Role+Domain specialization | Topic, evidence bases, model | Whether personas have explicit Role+Domain layers or only voice/tone | How much leverage does the Role+Domain layer add over persona-only? |
| Iteration cap = 1 vs 2 vs 3 vs 5 | Topic, personas, model | Iteration count | What's the convergence-quality vs cost curve? |
| Verifiable-artifact-required vs not | Topic, personas, model, evidence | Whether D3 is enforced | How much leverage does the artifact requirement contribute? |
| Both-must-agree vs Generator-may-converge | Topic, personas, model, evidence, D3 | Whether D4 is enforced | Does the framework collapse without D4? |

Each ablation produces a measured delta. Together they specify which disciplines are load-bearing in measurable terms rather than structural-argument terms.

## Statistical shape

The harness should produce, per (topic x configuration) cell:

- Convergence rate (% of N runs that converged)
- Mean + 95% CI of the metric being measured (correctness, rubric score, etc.)
- Mode-collapse detection: how often did N runs all converge on the *same* (possibly wrong) answer? Mode collapse with high apparent confidence is a worse failure shape than visible disagreement.
- Per-criterion rubric score distributions when rubric is the metric
- Cross-cell deltas with statistical significance for ablation comparisons

The framework's inability to detect "converged on the wrong answer with both agents agreeing" is the residual safety hole the spec acknowledges. Mode-collapse detection is the harness's contribution to closing that hole.

## Cost projection

**Compute cost is the smallest line item; rater/curation labor is the dominant cost.**

A 2-iteration `/spar` is 2-4 LLM calls. At N=100 per cell, that's 200-400 calls per (topic x configuration) data point. A first-pass corpus of 20 topics x 5 configurations (no-ablation baseline + 4 ablations) at N=100 is 20 * 5 * 100 * 3 = 30,000 LLM calls.

**Compute pricing depends on substrate (per the substrate-independence claim in `sparring-framework-notes.md`):**

- All Sonnet (~$3/$15 per MTok): ~$7-8k for 30,000 calls
- All Opus (~$15/$75 per MTok; the current `/spar` SKILL default): ~$30-40k for 30,000 calls
- Mixed Sonnet bulk + Opus calibration: ~$10-15k
- With prompt caching at scale: ~50% reduction on input

**Rater/curation labor (the binding constraint, not compute):**

For ANY methodologically-defensible RCT path, lifspel must source four labor categories none of which exist in the partner pool (per `spar-historical-case-corpus` Challenger R1):

- **Blinded second reviewer for packet certification** (~40-80 hours)
- **Cohen's kappa-calibrated process-quality raters** (~40-80 hours each plus calibration training)
- **Adversarial co-curator** (structurally impossible for an unpublished framework with no external critic pool -- requires waiting until after framework publication exposes critics)
- **Methodologist for OSF pre-registration pre-work** (~6-12 weeks of onboarded-methodologist time at lifspel scale, not 40-80 hours)

At contracted labor rates ($50-300/hr depending on category), realistic all-in cost for a methodologically-defensible RCT at lifspel: **$40-60k + 6-18 months timeline.** The compute cost is a rounding error against this.

**Smaller, honestly-scoped paths cost much less but produce narrower claims:**

- **Phase A variance check** on a single non-framework-internal real Lifspel decision: ~$10-30 at Sonnet pricing. Produces internal instrumentation telemetry only -- explicit publishability ceiling applied (see Phasing below). Not a comparative claim.
- **Sharma 2023 preference-flip narrow test:** ~$200-500 at Sonnet pricing for a specific test of pleasing-bias defense against an external benchmark. Single-failure-mode claim, not framework-wide.

The combat-engine Monte Carlo cost-ceiling shape (tunable per experiment) still applies for the compute side. It does NOT solve the labor-pool side, which is the binding constraint at lifspel.

## Phasing

The work decomposes into staged milestones rather than monolithic deployment. Order updated per the two pressure-test spars to reflect what is actually executable at lifspel scale.

- **Phase A: variance check on individual non-framework-internal decisions.** Build a minimal runner that takes (topic, configuration, N) and produces a per-cell artifact with statistical shape (convergence rate, revision-set similarity, recommendation consistency). Run on **single** real non-framework-internal Lifspel decisions, NOT on a comparative corpus. **Publishability-ceiling fence** applies: results are internal instrumentation telemetry, never written up as a finding, never cited in external material, never given a methodology designation. It is dogfooding the harness, not producing evidence about the framework. ~$10-30 per run at Sonnet pricing; ~1-2 weeks of harness-shell work.
- **Phase B: ablation toggles -- DEFERRED until labor-pool exists.** Add configuration knobs (`--no-d2`, `--cross-model`, `--persona-only`, `--cap=N`, etc.) for the ablation table below. **Cannot ship at lifspel scale without contracted rater labor** -- per `spar-historical-case-corpus` Challenger findings. Realistic timeline contingent on either (a) academic partnership sourcing the four labor categories, or (b) external touchpoints producing genuine adversarial critics. Realistic cost when feasible: $40-60k + 6-18 months.
- **Phase C: LLM-as-judge for rubric scoring at scale -- DEFERRED.** Calibrate against partner-scored anchor sets per `docs/standards/challenger-output-rubric.md`. Replaces partner-only sampling for criteria 1-4 (artifact-side); criteria 5-7 may need to remain partner-applied. Same labor-pool constraint as Phase B.
- **Phase D: cross-model integration.** API integration with Grok and OpenAI (or other vendors) for the cross-substrate variant. The substrate-independence claim in `sparring-framework-notes.md` is the structural argument; Phase D is the empirical test of that claim. Cheaper than Phases B-C on the labor side (cross-model variance can be measured at the convergence-rate level without rater pool, similar to Phase A's instrumentation shape) but still benefits from Phase B's ablation infrastructure when available. ~2-3 weeks of API-integration work; per-spar cost depends on vendor pricing mix.
- **Phase E: historical-case-study corpus -- FUTURE CONTINGENT, requires academic partnership.** Per `spar-historical-case-corpus` Generator R1 design: theory-testing multiple-case design with structured-focused comparison + within-case process tracing. Corpus of 12-20 historical decisions across military/political/business/scientific domains, four-cell process-x-outcome stratified, materials packets hindsight-stripped, scored on Janis-1989 vigilant-problem-solving rubric. **Becomes feasible only when:** (a) framework has external touchpoints producing adversarial-co-curator candidates, (b) academic partnership or grant funding sources contracted labor, (c) lifspel resources permit 18-month execution. Realistic cost when feasible: $40-60k + 18 months. Output: NeurIPS Evaluations & Datasets corpus-as-artifact paper + JBDM/AIES evaluation paper.

**Operational reality at lifspel today:** Phase A is the only phase executable with current resources (and even Phase A is bounded as internal instrumentation, not external claim). Phases B-E are blocked on the labor-pool constraint that no spend on compute can solve. The honest near-term path is Phase A + escalate Phases B-E to partner triumvirate as a multi-year R&D investment decision.

## Operational constraints at lifspel scale

Surfaced explicitly by the two pressure-test spars on this design. The framework's discipline is to name these honestly rather than design around them implicitly.

**Labor-pool constraint (binding):** the lifspel partner pool (3 partners, framework author is one) cannot supply blinded raters, kappa-calibrated process-quality raters, or adversarial co-curators. ALL of these positions require contracted external labor. Per the `spar-historical-case-corpus` Challenger R1 finding, this pushes any methodologically-defensible RCT into the contracted-labor cost scenario regardless of corpus path.

**Adversarial-co-curator structural impossibility (until post-publication):** for an unpublished framework with no external critic pool, "someone whose interest is *not* aligned with the framework's success" cannot be sourced -- paid skeptics have an incentive problem; unpaid genuine adversaries do not exist for unpublished work; the closest analog (general AI-multi-agent skeptics) lacks specific stakes in SPARRING failing. This is a structural condition that resolves itself only after the framework has external touchpoints (a workshop paper that draws critics; a blog post that surfaces objections; an academic collaborator with their own methodological priors). Until then, Phase B-E disciplines that require this role are unmeetable.

**`/spar` SKILL.md does not yet support corpus-runner orchestration or curated-materials-packet evidence bases.** Per Challenger R1 reading of SKILL.md lines 42-62 and 69-71: the persona evidence-base spec format is pointer-to-in-repo-files; no support for hindsight cutoff dates, certification hashes, "two-agents-without-framework" condition, or corpus-runner orchestration. Phase A's harness shell needs these additions before any phase can run. They are not in any current SKILL milestone.

## Defensibility rubric for any future eval path

Surfaced by `spar-historical-case-corpus` Generator R1. These are the disciplines that would make any future comparative eval defensible -- whether on historical cases, manufactured cases, accumulated archived spars, or some hybrid. Useful both as a forward-pointer for partner-triumvirate escalate decisions AND as the rubric against which any cheaper alternative should be measured.

1. **OSF pre-registration before materials processing.** Public timestamp; cannot be retrofitted; locks selection criteria, materials cutoff rules, scoring rubric, condition specifications, statistical analysis plan, stopping rules.
2. **Blinded two-reviewer packet certification** (or analogous discipline preventing post-hoc-knowledge leakage into the corpus that the framework reads).
3. **Four-cell process-x-outcome stratification** with adversarial co-curator: corpus must include the rare bad-process/good-outcome cell to defend against Baron & Hershey 1988 outcome bias.
4. **Process-quality + concern-coverage as headline scoring** (Janis-1989 vigilant-problem-solving rubric is one defensible instrument); decision-correctness reported but de-emphasized to avoid outcome-bias contamination of headline finding.
5. **Explicit scope-limiting of publishable claim.** Whatever empirical signal the eval produces, the publishable claim must be narrower than "framework better than no-framework in production AI deployment" -- structural argument and limited corpus do not support that scope.

If a proposed eval path cannot meet any of these five disciplines, the publication shape must shrink accordingly OR the path must be deferred until the discipline can be met. This is the operational expression of the framework's own discipline-against-its-own-claims (the same shape as Discipline 2's single-Challenger-fallback rule, applied to the meta-level eval design).

## Open questions

- **Ground-truth source for the seed corpus.** Partner judgment on archived spars vs. constructed test cases vs. Sharma 2023 adaptations -- which mix produces the most defensible first-pass corpus?
- **Mode-collapse measurement methodology.** "Both agents converged on the same wrong answer" requires knowing the right answer; without ground truth, mode collapse is invisible. Does the harness need to require ground-truth corpus only, or are there proxy measures (rubric-score outliers, reasoning-shape similarity to baseline) that detect mode collapse without ground truth?
- **Reproducibility under stochastic LLM outputs.** Combat sims are deterministic given seeds; LLM calls are not. Does the harness target reproducibility (via prompt-fingerprint hashing + seed-of-record per run) or accept stochasticity as a feature?
- **Scope of "the framework's value claim."** The current value proposition spans seven goals. Which are testable in this harness? Goals 1, 4, 5 are directly testable with the right corpus. Goal 2 (variance reduction toward ceiling) needs single-agent baselines. Goals 3, 6, 7 are deployment-level and harder to capture in per-run measurement.
- **When to use cross-model in the harness vs. as a deployment variant.** Cross-model adds cost but reduces theatrical-adversariality residual. Is cross-model the harness baseline, the upgraded ablation, or a deployment-only feature?

## Connection to existing infrastructure

- **Combat-engine Monte Carlo** (`storyforge/tests/api/monte-carlo.php`) -- methodology precedent, configuration-toggle pattern, per-cell-distribution shape.
- **Per-output rubric** (`docs/standards/challenger-output-rubric.md`) -- the measurement instrument the harness scales out. Updated 2026-05-02 with criterion 7 (external corroboration of load-bearing citations) addressing self-citation circularity.
- **Spar artifact format** (per `.claude/spars/<date>/spar-*.md` examples) -- the structured output the harness reads as input data.
- **`/spar` skill** (`.claude/skills/spar/SKILL.md`) -- the runtime the harness invokes. Updated 2026-05-02 with self-citation circularity check in Step 3.
- **Substrate independence** (`sparring-framework-notes.md` Substrate independence section) -- the structural claim this design doc's Phase D would empirically test.
- **Spec Section 5.4** -- proposed Sharma 2023 preference-flip benchmark adaptation, which slots into the corpus-construction work as the cleanest single-failure-mode test.
- **Spec Appendix D Phase 2/3 roadmap** (in framework notes) -- the strategic frame this design doc operationalizes.
- **Prior pressure-test spars on this design:**
  - `.claude/spars/2026-05-02/spar-low-cost-pilot-design.md` (UNRESOLVED at cap on framework-vs-no-framework comparative path under $400; surfaced cost-baseline / corpus-availability / rubric-circularity / rater-pool problems)
  - `.claude/spars/2026-05-02/spar-historical-case-corpus.md` (substantive convergence + narrow packaging hold on whether historical-case corpus solves the corpus problem; surfaced lifspel-scale operational impossibilities for 2 of the 5 defensibility disciplines)

## What this design doc is NOT

- It is not a milestone scope. The phasing above is a candidate decomposition; partner approval is needed on which phase ships first and at what fidelity.
- It is not a commitment to specific N values, model vendors, or ablation priorities -- those are tuning decisions per phase.
- It is not a substitute for the Phase 1 partner-applied rubric. Partner sampling produces calibration data the LLM-as-judge layer needs in Phase C.
- It is not a claim that the framework's quality leverage will be validated by these experiments. The experiments may produce null results or surface unexpected weaknesses; that's the point of measurement.
