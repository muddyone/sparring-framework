# SPARRING LLM-judge pilot design -- comparative-evidence path

*Sibling working draft of `sparring-framework-notes.md` and `sparring-monte-carlo-design.md`. Captures a comparative-evidence pilot methodology that uses cross-vendor LLM judges as the structural skeptic, sidestepping the operational impossibility of human adversarial co-curators that the `spar-historical-case-corpus` pressure-test surfaced.*

*Status: DRAFT (2026-05-02). Not yet a milestone; this is the design doc that would back one. Surfaced when partner observation pointed out that both prior spar agents had missed LLM-as-judge as a third evidence base neither was reading.*

## Purpose

The Monte Carlo design doc (`sparring-monte-carlo-design.md`) ended at "comparative framework-vs-no-framework testing requires $40-60k contracted labor + 18 months at lifspel scale, blocked on adversarial-co-curator structural impossibility for an unpublished framework." That conclusion was reached by two pressure-test spars, both of which anchored on academic-publication standards and human-rater methodology.

**This design proposes a different path:** preserve the historical-case corpus insight (real decisions with real stakes, no manufacturing) while replacing the human adversarial-co-curator with **cross-vendor LLM judges** as the structural skeptic. The result is a comparative-evidence pilot that fits under $400 cash, takes 2-4 weeks of partner time, and produces preliminary-evidence-grade output — not RCT-grade validation, but a real comparative claim defensible under ASA-statement framing.

## How this differs from the Monte Carlo design

| Dimension | Monte Carlo design (prior) | LLM-judge pilot (this design) |
|---|---|---|
| Primary question | Statistical confidence on framework leverage across N replications | Cross-vendor judge consensus on whether SPARRING produces measurably different deliberation than single-agent baseline on a corpus of historical decisions |
| Corpus | Manufactured cases / archived /spar / Sharma 2023 / historical (deferred to Phase E) | Historical decisions with predominantly contemporaneous materials, NOT hindsight-stripped |
| Adversarial-skeptic role | Human co-curator (structurally impossible at lifspel today) | 3 cross-vendor LLM judges (Claude + GPT + Grok class) |
| Ground truth | Required for correctness scoring | Not required -- the comparison is "which deliberation is higher quality?" not "did either reach the right answer?" |
| Rater pool | Contracted blind raters at $50-300/hr (not in lifspel partner pool) | LLM judges + small partner-anchor calibration step |
| Cost (cash) | $40-60k contracted labor + 18 months | $100-300 in API + 100-200 partner hours, 2-4 weeks |
| Publishable claim | "SPARRING produces X effect with 95% CI [Y, Z]" with statistical-significance framing | "Cross-vendor LLM-judge preliminary evidence consistent with SPARRING producing measurably different deliberation than single-agent baseline on N historical decisions, with stated bias-mitigation procedures" |
| Methodology tradition | Hertzog 2008 pilot-study + Yin theory-testing case study + ASA statement | LLM-as-judge literature (Zheng 2023, Wang 2023, Liu 2023) + ASA statement + bounded historical-case corpus |
| Substrate-independence claim | Tested directly in Phase D cross-model integration | Implicitly tested -- if cross-vendor judges agree, the framework's leverage is substrate-robust at the judging layer; the runs themselves can also be cross-substrate as a secondary ablation |

The LLM-judge pilot is **complementary** to the Monte Carlo design, not a replacement. Phase D (cross-model integration) of the Monte Carlo design is still the direct empirical test of substrate independence at the framework-runs layer; this pilot tests cross-substrate at the judging layer instead, which answers a different but related question.

## The corpus

**10-15 historical decisions** across 2-3 domains (e.g., business strategy, engineering decisions, scientific funding, military strategy). For each, a curated **decision pack** containing:

- The decision question as it was framed at the time
- The evidence base available to decision-makers (memos, reports, briefings, intelligence summaries, market data)
- Optionally: known constraints, deadlines, stakeholder positions

**Not required:** perfect hindsight stripping. The spar's 60-150 hours per case was driven by the methodologist's requirement to scrub all post-cutoff retrospective material because the test was framed as "did the framework reach the historically-correct answer?" This pilot's question is different: "*given the same evidence base, did the SPARRING framework produce a higher-quality deliberation than single-agent?*" The historical "right answer" is not the metric. Both conditions see the same materials; comparison between conditions is what matters.

This relaxation drops curation from 60-150 hours per case to ~5-15 hours per case. 10-15 cases at 10 hours each = 100-150 hours total partner time, doable in 2-4 weeks.

**Selection criteria (pre-registered before any runs):**
- Decision must have a documented question and a documented evidence base
- Contemporaneous materials should dominate the evidence base (post-hoc commentary is allowed but should not be the bulk)
- Mix of domains -- avoid all-business or all-military, which would limit generalization
- Mix of complexity -- some cases with clear best answer, some genuinely contested
- **Pre-registered selection criteria written down before any case is processed**, with date stamp. Informal pre-reg (a timestamped commit in the lifspel repo), not OSF formal -- the publishable claim is exploratory, not confirmatory, so the pre-reg discipline is "documented intent" not "binding stamp."

**Candidate cases (illustrative, not prescriptive):** a software architecture decision (Amazon monolith→services); a business strategy call (Netflix's pivot from DVD to streaming); a scientific funding decision (LIGO baseline approval); a vendor selection (a documented historical RFP outcome); a public-health policy call (COVID-era documented decision with public deliberation record); etc. Final selection happens during the pre-registration step.

## Conditions

Three conditions per case. The third is optional but methodologically valuable:

1. **Condition A: single-agent baseline.** Single LLM (Sonnet, matched to the model used in /spar runs) is given the question + evidence base, prompted to produce a decision recommendation with reasoning, key concerns, and stated confidence. ~1 LLM call per case.

2. **Condition B: SPARRING (the framework).** Run `/spar` on the same question + evidence base. Personas generated per the standard `/spar` Step 2 process. 2-iteration cap. Final recommendation extracted from the spar artifact. ~2-4 LLM calls per case.

3. **Condition C (optional): two-agents-without-framework.** Two agents with disjoint evidence-base specifications discuss the question and reach a recommendation, but WITHOUT the SPARRING ceremony -- no role-shifting, no both-must-agree, no verifiable-artifact requirement, just "two specialists discuss." This isolates the SPARRING ceremony's contribution from the mere presence-of-two-agents effect. ~2-3 LLM calls per case.

If budget is tight, run only A and B. If methodologically defensible isolation matters more, run all three.

## Output normalization

This is the load-bearing methodology step that prevents format-preference confound from contaminating the result.

**The problem:** /spar outputs LOOK like good deliberation -- they have explicit citations, structured agreement signals, role-shift language, four-phase scaffolding. Single-agent outputs typically don't. LLM judges have well-documented format-preference bias and would prefer /spar outputs simply because they look like what good deliberation looks like.

**The mitigation:** strip both outputs to the same shape before judging. Both conditions must produce, after normalization:
- A single-paragraph **recommendation** (decision + main rationale)
- A bulleted **key concerns** list (what risks the decision-maker should know about)
- A stated **confidence** (low / medium / high) with one-sentence justification

Same approximate length target (~400-600 words). Same structure. Same prose register.

For Condition B (SPARRING), the normalization step extracts the substantive recommendation from the spar artifact's "Final evaluation" and "Recommendation for parent" sections, then reformats. The iteration log, role-shift language, agreement signals, and disagreement-at-cap menu are NOT shown to judges. Judges see the substance, not the structural artifact.

For Condition A (single-agent), the model is prompted to produce output in this format directly.

For Condition C (two-agents-without-framework), the agents' final consolidated output is reformatted the same way.

**The format normalization is itself a methodological commitment that must be pre-registered.** Without it, the experiment measures format preference, not framework leverage. The discipline of stripping framework-shaped scaffolding from the output is the same discipline that the framework asks of its agents (verifiable artifact, not vibes; substantive concern, not theatrical adversariality) applied to the meta-level evaluation.

## Cross-vendor LLM judge protocol

**Three judges, three different vendors:**
- **Anthropic:** Claude Sonnet 4.6 or 4.7
- **OpenAI:** GPT-4 class (GPT-4.1 or current frontier)
- **xAI:** Grok-4 class

Cross-vendor mitigates **self-preference bias** -- a judge from the same family as the agents being judged will be biased toward those outputs (Panickssery et al. 2024 "LLM Evaluators Recognize and Favor Their Own Generations"). Both A (single-agent) and B (SPARRING) use Claude underneath, so within-condition Claude self-preference cancels; the cross-vendor structure protects against any judge-vendor specifically preferring one condition's prose patterns.

**Per-pair judging procedure:**

1. Take the normalized outputs of A and B for one case.
2. Anonymize: label as "Answer X" and "Answer Y" with random assignment per pair (no consistent A=X mapping).
3. Present the pair to each of the 3 judges with a structured rubric prompt (see below).
4. Each judge scores both answers on each rubric criterion.
5. Run the same pair AGAIN with positions flipped (now A is "Y" and B is "X"). This addresses position bias -- LLM judges tend to prefer the first-presented option (Wang et al. 2023 "Large Language Models are not Fair Evaluators").
6. Average the two-position scores per judge per criterion per condition.

**Rubric (4 cross-condition-fair criteria adapted from `docs/standards/challenger-output-rubric.md`):**

1. **Verifiable artifact citation:** does the recommendation cite specific evidence from the materials?
2. **Substantive vs theatrical concerns:** are the concerns raised real risks rooted in the evidence, or generic-sounding hedges?
3. **Missed real concerns:** does the recommendation surface concerns a careful reader would identify, or miss obvious ones?
4. **Calibrated confidence:** is the stated confidence appropriate to the evidence, or over/under-stated?

Score each criterion on a 5-point scale with anchored point descriptions. Per-criterion mean per condition is the per-judge measure; cross-judge consensus is the headline.

**Why these 4 criteria specifically:** the prior spar (`spar-low-cost-pilot-design`) found that criteria 5 (genuine evidence disjointness), 6 (calibrated holds-up declaration), and 7 (external corroboration of citations) on the existing 7-criterion rubric are framework-specific and would be tautologically scored. Criteria 1, 2, 3, 4 are cross-condition fair. (Criterion 6 was originally also on the cross-condition list, but the prior spar's Challenger correctly observed it measures a SPARRING-specific "Holds under PNP" declaration that single-agent baseline has no structural reason to produce. Criterion 4's "calibrated confidence" is a more cross-condition-fair version of the same intent.)

**Per-judge prompt template:**

```
You are evaluating two decision recommendations on the same question.
You don't know which recommendation came from which approach.
Score each on the 4 criteria below using the 1-5 anchored scale provided.
Do not state which you prefer overall -- score per criterion.

Question: [decision question]
Evidence base summary: [brief summary]

Answer X: [normalized output]
Answer Y: [normalized output]

Score Answer X:
- Criterion 1 (verifiable citation): [1-5]
- Criterion 2 (substantive vs theatrical): [1-5]
- Criterion 3 (missed real concerns): [1-5]
- Criterion 4 (calibrated confidence): [1-5]

Score Answer Y:
- Criterion 1: [1-5]
- Criterion 2: [1-5]
- Criterion 3: [1-5]
- Criterion 4: [1-5]
```

## Partner calibration anchor step

LLM-as-judge results without partner-anchored calibration are uninterpretable -- if the judges disagree with how partners would have rated the same outputs, the experiment doesn't conclude what it appears to. This step is small and cheap.

**Procedure:**
1. Pick 10-20 paired outputs from the pilot (or pre-pilot if running it first).
2. Have 1-2 partners independently rate each pair on the same 4-criterion rubric the LLM judges use.
3. Compute correlation between partner scores and LLM-judge consensus.
4. Report the correlation alongside the pilot results.

**Interpretation:**
- High correlation (Spearman ρ > 0.7): LLM judges align with partner judgment; the pilot's findings are calibrated.
- Moderate correlation (0.4-0.7): partial alignment; report findings with explicit caveat that LLM judges and partners agree on some dimensions but not others.
- Low correlation (< 0.4): LLM judges are measuring something different from what partners would. The pilot doesn't conclude what it appears to. Surface honestly; do not publish the comparative finding without addressing this.

**Cost:** 2-5 hours of partner time across 1-2 partners. No cash cost.

**The calibration step is non-optional.** Without it, the LLM-judge pilot is theatrical adversariality at the meta-level: the judges look like neutral evaluators but their alignment with human judgment is unverified.

## Statistical analysis plan

**Pre-register before runs:**

1. **Headline endpoint:** mean per-criterion difference (Condition B − Condition A) across cases, per judge and across judges, with bootstrap 95% CIs.

2. **Position-bias check:** does score depend on which position the answer was presented in? (Should not, after position randomization.) If yes, the position randomization didn't fully control for it -- report and adjust.

3. **Cross-judge agreement:** Cohen's kappa or ICC across the 3 judges per criterion. Low agreement = judges disagree about what they're seeing = harder to interpret consensus.

4. **Per-criterion vs aggregate:** report per-criterion deltas separately. The aggregate is informative but the per-criterion pattern is more so -- if SPARRING wins on Criterion 1 (citation discipline) but loses on Criterion 4 (calibrated confidence), that's substantively different from winning across the board.

5. **Mode-collapse detection:** how often did all 3 judges produce identical scores? If it's high, judges aren't genuinely independent. If it's low, the cross-judge consensus has real signal.

6. **Optional Condition C comparison:** if running, compare B vs C separately to isolate SPARRING's specific contribution from mere two-agents effect.

7. **Partner-anchor correlation:** report Spearman ρ between LLM-judge consensus and partner ratings on the calibration sample.

**Headline framing (for the eventual write-up):**

> "On a corpus of N historical decisions, cross-vendor LLM judges (Claude + GPT + Grok) consistently rated SPARRING-produced deliberation higher than single-agent baseline on [these criteria], with [stated effect size and CI]. Partner-anchored calibration showed [correlation with partner judgment]. This is preliminary evidence consistent with SPARRING producing measurably different deliberation than single-agent on this corpus, under the bias-mitigation procedures specified. It is not RCT-grade validation; the publishable claim is bounded to deliberation-quality pattern-matching against an LLM-judge consensus, not to AI-deployment generalization or to historical-correctness validation."

## Cost projection

| Item | Cost |
|---|---|
| Compute -- runs (Condition A + B + optional C) | 10-15 cases × 2-3 conditions × ~$1-2 per run at Sonnet = **$30-90** |
| Compute -- judging | 10-15 cases × 3 judges × 2 position-flips × ~$0.50-1 per call = **$30-90** |
| Compute -- pre-pilot calibration step | 10-20 paired outputs × 3 judges = **$15-30** |
| Cross-vendor API setup (Grok, OpenAI accounts if not already established) | Time only; minimal cash |
| Partner curation time (corpus selection + decision packs) | ~100-150 hours over 2-4 weeks |
| Partner calibration time | 2-5 hours |
| Pre-registration document drafting | ~5-10 hours |
| Write-up time | ~20-40 hours |
| **Total cash cost** | **$75-210, with buffer to ~$300** |
| **Total partner time** | **~130-200 hours over 1-2 months** |

**This fits under $400 cash with comfortable margin.** The binding constraint is partner time, not API spend.

## How the spar's 5 disciplines relax under this approach

The historical-case-corpus spar's 5 non-negotiable disciplines were calibrated to peer-reviewed-journal publication standards. Under exploratory-evidence framing per ASA principles, each relaxes:

1. **OSF pre-registration** → informal pre-registration (timestamped commit) is sufficient for exploratory framing.
2. **Blinded two-reviewer packet certification** → not required when the question is comparative-quality not historical-correctness; both conditions see the same materials so leakage of post-hoc info affects both equally.
3. **Four-cell process-x-outcome stratification + adversarial co-curator** → cross-vendor LLM judges are the structural skeptic; four-cell stratification is desirable but not load-bearing without correctness scoring.
4. **Process-quality + concern-coverage as headline (Janis-1989 vigilant-problem-solving rubric)** → the existing 7-criterion challenger output rubric (subset to 4 cross-condition-fair criteria) substitutes; partner calibration anchor confirms or denies alignment with the rubric's intent.
5. **Explicit scope-limiting of publishable claim** → still applies, fully. The publishable claim is "preliminary cross-vendor LLM-judge evidence" not "framework validation."

This is not the spar's 5 disciplines applied weakly -- it's a different methodology with its own discipline structure. The trade-off is honest: the LLM-judge pilot produces preliminary-evidence-grade output, not RCT-grade. The publishable scope shrinks accordingly.

## Known LLM-judge biases and mitigations

| Bias | Source | Mitigation in this design |
|---|---|---|
| **Length bias** | LLM judges prefer longer answers regardless of quality (Zheng 2023, Wang 2023) | Output normalization to same length target; per-criterion structured rubric instead of "which is better?" |
| **Position bias** | Judges prefer first-presented answer (Wang 2023) | Position randomization with both A-first and B-first runs averaged |
| **Self-preference bias** | Judges from the same model family prefer same-family outputs (Panickssery et al. 2024) | Cross-vendor judges (Claude + GPT + Grok); within-condition self-preference cancels because both A and B use Claude underneath |
| **Format-preference bias** | Judges prefer structured-looking outputs | Output normalization strips framework-shaped scaffolding; both conditions presented in identical format |
| **Confidence bias** | Judges may prefer confident-sounding answers | Criterion 4 (calibrated confidence) explicitly scores confidence-vs-evidence match, neutralizing the bias as a scoring factor |
| **Sycophancy at the judging layer** | Judges might agree with framing in the prompt | Prompt template prohibits stating overall preference; per-criterion scoring only |
| **All-judges-share-bias** | Some biases are universal across LLMs | Partner calibration anchor catches systematic LLM-vs-human divergence |

**Residual risk:** even with all mitigations, LLM judges may share a bias toward whatever "good deliberation looks like" in their training data. The partner calibration anchor is the operational defense -- if partner judgment correlates with LLM-judge consensus, the residual is bounded; if it doesn't, the experiment doesn't conclude what it appears to.

## Phasing

Two-phase execution:

### Phase 1: Calibration check (1-2 weeks, ~$10-20 cash + 5-10 partner hours)

**Goal:** establish whether LLM judges align with partner judgment on the rubric, before investing in the full corpus.

1. Pick 1-2 cases from the candidate corpus (could even be one of the 2 existing /spar topics, used as methodology pilot rather than corpus point -- per the prior synthesis, the existing spars are dogfooding artifacts and using them in a methodology calibration is consistent with that role).
2. Run Conditions A, B, C if applicable, on each case.
3. Have 1-2 partners rate the paired outputs on the 4-criterion rubric.
4. Run the 3 cross-vendor LLM judges on the same paired outputs with the same rubric.
5. Compute partner-vs-LLM-judge correlation.
6. **Decision gate:** if correlation is strong (Spearman ρ > 0.7), proceed to Phase 2. If weak (< 0.4), the methodology has a confound -- surface honestly; do not run the full corpus until the confound is addressed.

This phase is cheap, fast, and answers the only question that matters before investing more: do the LLM judges measure what we think they measure?

### Phase 2: Full pilot (3-6 weeks, ~$100-300 cash + 100-150 partner hours)

If Phase 1 calibration check passes:

1. Finalize corpus (10-15 cases) with pre-registered selection criteria.
2. Curate decision packs for each case (~10 hours per case × 12 cases = 120 hours).
3. Run all conditions on all cases.
4. Judge all paired outputs with 3 cross-vendor judges, position-randomized.
5. Compute statistics per the analysis plan.
6. Write up as exploratory-evidence preprint.

If Phase 1 calibration check fails:

1. Stop. Surface the finding (what the judges measured did not align with what partners would measure).
2. Either redesign the rubric, or accept that LLM-as-judge for this question is not viable, and revisit the Monte Carlo design's contracted-rater path with the corrected $40-60k estimate.

## Open questions

- **Should Condition C (two-agents-without-framework) be included from the start, or added in a follow-up if Phase 2 produces interesting Phase A→Phase B signal?** Including it from the start is methodologically cleaner; deferring it keeps Phase 2 simpler. Partner judgment.
- **Which exact judges?** Claude Sonnet (4.6 or 4.7), GPT-4-class, Grok-4-class is the cross-vendor minimum. Adding Gemini as a 4th judge increases cross-vendor robustness but adds cost and complexity. Defer to budget.
- **Single-agent baseline model choice.** Should match what /spar uses (Sonnet, when /spar is configured for Sonnet; Opus, when Opus). The prior spar's substrate-independence finding applies: the result should be reported per-substrate, not generalized across substrates without explicit per-substrate runs.
- **How much output normalization is too much?** Stripping framework scaffolding entirely might over-correct -- arguably the structured output IS part of the framework's contribution. The right test is "what would a decision-maker actually receive from each approach?" Single-agent and SPARRING produce structurally different artifacts; normalizing to identical format may not be the right comparison. Worth deciding consciously, not by default.
- **Partner-anchor sample size.** 10-20 paired outputs is a starting point. If correlations are noisy, the calibration sample should grow. Not a fixed parameter.
- **Publication venue.** AIES, FAccT, NeurIPS workshop tracks (not main D&B), or arXiv-only preprint with blog companion. Lower-stakes venues are honest match for exploratory-evidence framing. Decide closer to write-up.

## Connection to existing infrastructure

- **Combat-engine Monte Carlo** (`storyforge/tests/api/monte-carlo.php`) -- methodology precedent for distribution-not-point-estimate framing; less directly applicable here because per-cell N is small (single run per condition per case + judging passes) but the per-cell-artifact discipline applies.
- **Per-output rubric** (`docs/standards/challenger-output-rubric.md`) -- 4 of 7 criteria adapted as the LLM-judge rubric (1, 2, 3, 4). Criteria 5, 6, 7 omitted as cross-condition-unfair per the prior spar's analysis.
- **Spar artifact format** (`.claude/spars/<date>/spar-*.md`) -- the source material for Condition B's normalized output (extract "Final evaluation" + "Recommendation for parent" sections, reformat).
- **`/spar` skill** (`.claude/skills/spar/SKILL.md`) -- the runtime for Condition B. No SKILL changes required for this pilot; output normalization happens after the spar artifact is produced.
- **Substrate independence** (`sparring-framework-notes.md` Substrate independence section) -- the structural claim this pilot's cross-vendor judging implicitly tests at the judging layer. The Monte Carlo design's Phase D is the more direct test at the runs layer.
- **Prior pressure-test spars:**
  - `.claude/spars/2026-05-02/spar-low-cost-pilot-design.md` -- surfaced corpus, rater-pool, and rubric-circularity problems that this design addresses
  - `.claude/spars/2026-05-02/spar-historical-case-corpus.md` -- surfaced labor-pool and adversarial-co-curator problems that this design addresses (cross-vendor LLM judges replace human adversarial co-curator)
- **Monte Carlo design doc** (`sparring-monte-carlo-design.md`) -- sibling design; complementary, not replacement. Phase D of Monte Carlo (cross-model integration at the runs layer) tests the substrate-independence claim directly; this pilot tests the framework-leverage claim with a much smaller methodology footprint.

## What this design IS

- A comparative-evidence pilot achievable at lifspel scale today, under $400 cash + 100-200 partner hours
- Methodologically defensible as **preliminary exploratory evidence** under ASA-statement framing
- A genuine first comparative test of the framework's quality leverage that produces a publishable artifact
- A cheaper alternative to the contracted-rater Monte Carlo path that doesn't require academic partnership or post-publication critic pool
- A useful input for the eventual fuller study the partner triumvirate might fund later -- the Phase 1 calibration check alone produces real signal about whether LLM-as-judge is viable for this domain

## What this design IS NOT

- An RCT-grade validation study
- Proof the framework "works" in production AI deployment
- A substitute for cross-model SPARRING (Phase D of Monte Carlo design) -- which tests substrate independence at the runs layer, a different question
- A path to a "framework better than no framework in general" claim that would survive peer-reviewed-journal scrutiny
- A replacement for the partner-applied per-output rubric in `docs/standards/challenger-output-rubric.md` -- partner sampling at small N is still the calibration data Phase 2 needs
- A claim about correctness against historical ground truth -- the comparison is deliberately scoped to relative deliberation quality, not absolute correctness

## Methodological honesty: why both prior spar agents missed this

The Generator in both prior spars was anchored to academic methodology corpora (pilot-study design; case-study research). The Challenger in both was anchored to lifspel infrastructure files. Neither read the LLM-as-judge subliterature, because neither's evidence base included it. Both correctly applied the framework's discipline -- pressure-testing within the bounds of their genuinely-disjoint evidence -- and both produced rigorous findings that nonetheless missed a third option both their evidence bases excluded.

This is honest information about the framework's discipline: **disjoint-evidence specialization can miss something important when the right answer requires a third evidence base neither agent has.** The framework's response should not be to widen every persona's evidence base (which would erode disjointness, the load-bearing structural defense). The response is to recognize that partner judgment after the spar is itself a third evidence base, and to use it. That's exactly what happened here -- partner observation surfaced LLM-as-judge, the spar agents accepted the reframe, and the design improved.

This is the framework working as designed. It is also evidence that "always run another spar" is not always the right response to a partial-convergence outcome. Sometimes the partner has the missing piece.
