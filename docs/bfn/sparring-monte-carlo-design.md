# SPARRING Monte Carlo eval-harness -- design draft

*Sibling working draft of `sparring-framework-notes.md`. Bridges the deferred Phase 2/3 eval-harness roadmap (Appendix D) into a concrete milestone shape, modeled on the project's existing Monte Carlo machinery in the combat engine.*

*Status: DRAFT (2026-05-02). Not yet a milestone; this is the design doc that would back one.*

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

The corpus is the single biggest pre-shipping investment. Three candidate construction paths:

- **Archived `/spar` runs whose outcomes have been validated.** Partner judgment on past spars (was this the right call?) plus any production outcomes that closed the loop on a decision. Cheapest source; constrained to decisions actually run through the framework.
- **Sharma 2023 preference-flip prompts adapted to multi-agent settings (per spec Section 5.4).** Established sycophancy benchmark with known correct answers and known opposing-view formulations. Tests pleasing-bias defense specifically.
- **Lifspel-internal deliberately-constructed test cases.** Decision shapes the framework should handle well (multi-agent, verifiable artifacts, disjoint evidence available) and decision shapes it should *not* handle well (routine work, pure judgment) -- the latter to validate the Applicability Gate's behavior, not the framework's quality leverage.

A first-pass corpus at small N (say 20 cases across the three categories) is sufficient to demonstrate the methodology and surface the first ablation results. Scale-up follows once the harness shape is validated.

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

A 2-iteration `/spar` is 2-4 LLM calls. At N=100 per cell, that's 200-400 calls per (topic x configuration) data point. A first-pass corpus of 20 topics x 5 configurations (no-ablation baseline + 4 ablations) at N=100 is 20 * 5 * 100 * 3 = 30,000 LLM calls.

At Claude Sonnet 4.6 pricing roughly, that's a meaningful but bounded cost -- substantially less than the cost of one production deployment going subtly wrong because pleasing-bias compounded undetected. The cost ceiling is the same shape as the combat-engine Monte Carlo's: tunable per experiment, with the option to cap at lower N when statistical power isn't the binding constraint.

## Phasing

The work decomposes into staged milestones rather than monolithic deployment:

- **Phase A: harness shell + corpus seed.** Build the runner that takes (topic, configuration, N) and produces a per-cell artifact with the statistical shape above. Seed a 20-case corpus. Ship without ablations -- measure baseline convergence consistency only. ~1-2 weeks of focused work.
- **Phase B: ablation toggles.** Add the configuration knobs (`--no-d2`, `--cross-model`, `--persona-only`, `--cap=N`, etc.) and run the table above against the seed corpus. ~2-3 weeks.
- **Phase C: LLM-as-judge for rubric scoring at scale.** Calibrate against partner-scored anchor sets per `docs/standards/challenger-output-rubric.md`. Replaces partner-only sampling for criterion 1-4 (the artifact-side criteria); criteria 5-7 may need to remain partner-applied. ~3-4 weeks.
- **Phase D: cross-model integration.** API integration with Grok and OpenAI (or other vendors) for the cross-substrate variant. Output normalization in the orchestrator. ~2-3 weeks.

Phases A-B together would produce the first empirical signal on framework leverage and would close the largest gap in the value-proposition claim: "structurally argued but not yet empirically measured" becomes "empirically measured at small N with stated CIs."

## Open questions

- **Ground-truth source for the seed corpus.** Partner judgment on archived spars vs. constructed test cases vs. Sharma 2023 adaptations -- which mix produces the most defensible first-pass corpus?
- **Mode-collapse measurement methodology.** "Both agents converged on the same wrong answer" requires knowing the right answer; without ground truth, mode collapse is invisible. Does the harness need to require ground-truth corpus only, or are there proxy measures (rubric-score outliers, reasoning-shape similarity to baseline) that detect mode collapse without ground truth?
- **Reproducibility under stochastic LLM outputs.** Combat sims are deterministic given seeds; LLM calls are not. Does the harness target reproducibility (via prompt-fingerprint hashing + seed-of-record per run) or accept stochasticity as a feature?
- **Scope of "the framework's value claim."** The current value proposition spans seven goals. Which are testable in this harness? Goals 1, 4, 5 are directly testable with the right corpus. Goal 2 (variance reduction toward ceiling) needs single-agent baselines. Goals 3, 6, 7 are deployment-level and harder to capture in per-run measurement.
- **When to use cross-model in the harness vs. as a deployment variant.** Cross-model adds cost but reduces theatrical-adversariality residual. Is cross-model the harness baseline, the upgraded ablation, or a deployment-only feature?

## Connection to existing infrastructure

- **Combat-engine Monte Carlo** (`storyforge/tests/api/monte-carlo.php`) -- methodology precedent, configuration-toggle pattern, per-cell-distribution shape.
- **Per-output rubric** (`docs/standards/challenger-output-rubric.md`) -- the measurement instrument the harness scales out.
- **Spar artifact format** (per `.claude/spars/<date>/spar-*.md` examples) -- the structured output the harness reads as input data.
- **`/spar` skill** (`.claude/skills/spar/SKILL.md`) -- the runtime the harness invokes.
- **Spec Section 5.4** -- proposed Sharma 2023 preference-flip benchmark adaptation, which slots into the corpus-construction work.
- **Spec Appendix D Phase 2/3 roadmap** (in framework notes) -- the strategic frame this design doc operationalizes.

## What this design doc is NOT

- It is not a milestone scope. The phasing above is a candidate decomposition; partner approval is needed on which phase ships first and at what fidelity.
- It is not a commitment to specific N values, model vendors, or ablation priorities -- those are tuning decisions per phase.
- It is not a substitute for the Phase 1 partner-applied rubric. Partner sampling produces calibration data the LLM-as-judge layer needs in Phase C.
- It is not a claim that the framework's quality leverage will be validated by these experiments. The experiments may produce null results or surface unexpected weaknesses; that's the point of measurement.
