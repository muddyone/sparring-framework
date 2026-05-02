# When the human anchor is not the gold standard: an exploratory case study in cross-vendor LLM-as-judge calibration

**Author**: Bart Niedner, Resource Forge LLC
**Drafted**: 2026-05-02 (working draft for partner review and revision)
**Type**: Exploratory-evidence preprint, intended for arXiv (cs.AI / cs.HC) with a blog companion
**Word count target**: ~4,000 words; please trim or expand at sections marked `[length call]`
**Pre-registration**: Available in the public artifact bundle (linked at submission)
**Conflict of interest**: The author is the developer of the SPARRING framework being evaluated. This study is exploratory case study; the author conducted both the deliberation runs and the rating step that the cross-vendor LLM judges were calibrated against. The findings are scoped accordingly.

---

## Abstract

We report an exploratory n=2 case study evaluating a structured AI-deliberation framework ("SPARRING") against a single-agent baseline using cross-vendor LLM-as-judge consensus, with partner-anchor calibration. We pre-registered a methodology gate (Spearman ρ ≥ 0.7 PASS, 0.4–0.7 BORDERLINE, < 0.4 FAIL) before any compute was spent. The cross-vendor consensus achieved ρ = 0.351 against the partner's ratings, formally failing the gate. However, three findings emerged that complicate that headline:

**Directional finding**. All four raters (one human partner; three LLM judges across Anthropic Claude Sonnet 4.6, OpenAI GPT-4o, and xAI Grok-3) rated the framework's output higher than the single-agent baseline on every one of the four cross-condition-fair rubric criteria. Partner B−A delta averaged +1.125 (1–5 scale); cross-vendor consensus +0.417. Direction was unanimous; magnitude differed.

**Methodological inversion**. After unblinding, the human partner explicitly stated greater confidence in the LLM judges' rubric scoring than in their own on the two rubric criteria where partner-judge alignment was weakest (ρ = 0.000 and 0.236). This is consistent with the partner under-discriminating on subjective rubric dimensions where humans are known to have specific blind spots, rather than the LLM judges being mis-calibrated against a human gold standard. The partner-anchor calibration premise — that human judgment is the gold standard — therefore warrants per-criterion specification rather than blanket application.

**Pack-fidelity vs. ground-truth-fidelity gap**. A factual error in one decision pack (a fictional engine data structure) was discovered post-hoc by a partner reading the pack and outputs critically with the engine source available — not by either condition, despite the SPARRING Generator persona being explicitly configured with the engine codebase as its evidence base. This characterizes a class of failure mode in author-curated decision-pack methodologies and a corresponding gap in the evaluated framework's verifiable-artifact discipline (which mandates citation but not source-fetch-and-verify). The instance is small but operationally informative: the framework's stated partner-residual-safety position became load-bearing in practice.

We argue that small-n exploratory pilots like this one should be reported in the literature even when their pre-registered gates fail, because the methodology observations (the partner-anchor inversion and the pack-fidelity gap) have value independent of the substantive comparative claim. We outline four follow-up paths and explicitly do not advance the substantive claim that the framework outperforms single-agent deliberation; that claim is reserved for confirmatory work this pilot was not designed to conduct.

---

## 1. Introduction

The growing practice of using AI deliberation systems for partner-facing recommendations — code review, design sign-off, scientific or business strategy — has produced a corresponding interest in evaluating *whether* such systems produce higher-quality output than simpler baselines. Two evaluation traditions exist: (a) human-rated comparative studies, which produce strong evidence at high cost (typically $40k+ in contracted-rater labor for a defensible RCT at modest n), and (b) LLM-as-judge methods, which produce cheaper signal but introduce their own measurement biases (Zheng et al. 2023; Wang et al. 2023; Panickssery et al. 2024).

A natural compromise is *cross-vendor LLM-as-judge consensus with human-anchor calibration*: have multiple LLMs from different model families score paired outputs against a structured rubric, mitigating per-vendor self-preference bias by averaging across vendors, and validate the result against a small human-rated sample to confirm the LLM consensus tracks human judgment (the "calibration step" or "anchor step").

This paper reports an n=2 exploratory case study applying that methodology to a structured AI-deliberation framework called SPARRING (briefly described below). The study was conducted as a Phase 1 calibration check before any larger-n investment, with a pre-registered methodology gate locking the conditions under which Phase 2 (full corpus pilot) would proceed.

The pre-registered gate failed at the headline level (cross-vendor consensus ρ = 0.351 against partner judgment, below the 0.4 threshold). But the structure of the failure — and a post-hoc partner observation about the partner-anchor premise itself — produced two findings of methodological interest:

1. Directional agreement across all four raters was unanimous: the framework was rated higher than baseline on every criterion. The disagreement was about magnitude, not direction.

2. The partner explicitly identified rubric criteria on which they trusted LLM judgment more than their own. This produces an asymmetry in the partner-anchor premise that is not addressed by the standard methodology.

We report these honestly under exploratory-evidence framing, acknowledge the substantial limits (small n, single rater, partner ceiling effect, model substitutions), and outline what would be needed for a confirmatory follow-up.

---

## 2. The SPARRING framework (briefly)

SPARRING is a structured deliberation protocol that pairs two LLM specialist agents with explicitly disjoint evidence-base specifications: a *Generator* proposes an evaluation; a *Challenger* applies a rigorous critique discipline ("PNP — polite, not pleasing") with a verifiable-artifact requirement (every concern must cite a specific source). Both agents emit structured agreement signals at the end of each round; convergence requires both to signal agreement explicitly. If the iteration cap is hit without convergence, the protocol returns "unresolved at cap" rather than papering over disagreement.

The framework's design intent is to mitigate well-documented failure modes in agentic AI deployment: pleasing-bias compounding (Sharma et al. 2023), specialization theater (two correlated agents that look distinct), self-citation circularity, and theatrical adversariality (challenger producing manufactured concerns to look rigorous). Full framework documentation is available in the public artifact bundle.

For the purposes of this paper, the framework is the *case* under evaluation rather than the contribution. The contribution is the methodology observation about partner-anchor calibration that emerged from evaluating it.

---

## 3. Methods

### 3.1 Pre-registration

All methodology choices were locked in a public pre-registration document committed to a version-controlled repository before any compute was spent. The pre-registration specified: cases, conditions, models, rubric, normalization protocol, position randomization, statistical analysis plan, decision-gate thresholds, and explicit scope-limiting language for the eventual write-up. Informal pre-registration (timestamped commit) was used in lieu of OSF formal registration, appropriate to exploratory-evidence framing.

### 3.2 Cases

Two real, currently-pending product decisions selected from the partner organization's project backlog. Both had documented questions, documented evidence bases (project memos, expert assessments, cited historical sources), and decision stakes — neither was a manufactured exercise.

- **Case A** — A taxonomy-naming-convention decision in a product's racial classification system. Three options on the table; cross-domain considerations (LLM grounding, narrative tradition, authoring tooling, brand voice). ~5,300-word evidence pack.
- **Case B** — A combat-engine architectural decision about modeling motion-accuracy coupling. Three sub-decisions; cited historical biomechanics literature; explicit "not yet ready to scope" status from the in-house specialist. ~5,200-word evidence pack.

Cases were selected with mixed domain (one narrative-architectural, one engine-technical) to provide cross-domain coverage at small n.

### 3.3 Conditions

Two conditions per case:

- **Condition A — Single-agent baseline.** A single Claude Opus 4.7 API call with the case's decision pack and a structured-output prompt requiring (a) a single-paragraph recommendation, (b) a bulleted key-concerns list, (c) a stated confidence (low/medium/high) with one-sentence justification.

- **Condition B — SPARRING.** A `/spar` ceremony with iteration cap = 2, both Generator and Challenger using Claude Opus 4.7. Two divergent specialist personas with explicitly different evidence-base specifications generated per case. Iteration loop with structured agreement signals; "converged" or "unresolved at cap" outcomes both treated as valid results rather than failures.

### 3.4 Output normalization

Both conditions' outputs were normalized to identical structural shape (single-paragraph recommendation + bulleted concerns + confidence statement; word count target 400–600) before being shown to judges. For Condition B, the normalization step extracted the substantive recommendation from the spar artifact's "Final evaluation" and "Recommendation for parent" sections and reformatted to match Condition A's shape. Iteration log, role-shift language, agreement signals, and disagreement-at-cap menu were *not* shown to judges. This output normalization is a load-bearing methodology commitment: it prevents format-preference confound (Zheng 2023) from contaminating the result, since SPARRING's natural artifacts are visually distinguishable from single-agent outputs.

### 3.5 Cross-vendor LLM judges

Three judges from three different model families:
- **Anthropic** — claude-sonnet-4-6 (pre-reg targeted Sonnet 4.7, which is not yet released; Sonnet 4.6 is the current frontier Sonnet)
- **OpenAI** — gpt-4o
- **xAI** — grok-3 (pre-reg targeted "Grok-4 class"; only grok-3 was available)

Cross-vendor selection mitigates self-preference bias (Panickssery et al. 2024 — judges from the same model family as the agents being judged tend to favor same-family outputs). Both conditions used Claude underneath, so within-condition Claude self-preference cancels; the cross-vendor structure protects against any judge-vendor specifically preferring one condition's prose patterns.

### 3.6 Rubric

Four cross-condition-fair criteria adapted from a per-output rubric used internally for SPARRING evaluation:

1. **Verifiable artifact citation** — does the recommendation cite specific evidence from the materials?
2. **Substantive vs theatrical concerns** — are the concerns real risks rooted in the evidence, or generic-sounding hedges?
3. **Missed real concerns** — does it surface concerns a careful reader would identify, or miss obvious ones?
4. **Calibrated confidence** — is the stated confidence appropriate to the evidence?

Each scored 1–5 with an anchored scale (1 = clearly absent / actively wrong; 5 = exemplary, no notable gaps). Per-judge prompt template was locked verbatim in the pre-registration; identical prompt sent to all three judges.

### 3.7 Position randomization

For each case, the (Condition A, Condition B) pair was presented to judges *twice*: once with Condition A as "Answer X" / Condition B as "Answer Y", and once with the positions flipped. This addresses position bias (Wang et al. 2023 — judges prefer first-presented answer). Per-judge per-criterion per-condition score = mean of the two-pass scores. Total judge calls: 2 cases × 3 judges × 2 position-passes = 12.

### 3.8 Partner rating

The same four-criterion rubric, on the same anonymized X/Y pairs, was administered to one human partner (the author). Partner saw the question + evidence summary + both answers, blind to the X/Y → condition mapping. Partner ratings were collected via a multi-rater audit-trail-preserving web tool (described in the artifact bundle). Single rating per pair (no position-flip pass for the partner; partner time was the binding constraint). Partner did not see judge scores until partner ratings were complete.

### 3.9 Decision gate

Pre-registered:
- ρ > 0.7 → PASS (Phase 2 full pilot is methodologically defensible)
- 0.4 ≤ ρ ≤ 0.7 → BORDERLINE (Phase 2 contingent on partner judgment; report findings with explicit caveats)
- ρ < 0.4 → FAIL (Phase 2 NOT pursued without addressing the confound; surface honestly)

---

## 4. Results

### 4.1 Headline

**Cross-vendor LLM-judge consensus Spearman ρ vs partner ratings = 0.351 (n=16 score points).** Below the 0.4 PASS threshold. Pre-registered gate classification: **FAIL**.

### 4.2 Per-judge alignment (all n = 16 score points)

| Judge | Vendor | Spearman ρ vs partner |
|---|---|---:|
| Anthropic | claude-sonnet-4-6 | **0.706** |
| OpenAI | gpt-4o | 0.145 |
| xAI | grok-3 | 0.198 |
| Cross-vendor consensus | mean of three | 0.351 |

The Anthropic judge alone aligned with partner judgment at ρ = 0.706, above the 0.7 PASS threshold. The other two vendors fell well below the FAIL threshold individually. Cross-vendor averaging — designed to mitigate self-preference bias — pulled the consensus below the gate at this n. We return to this in §5.1.

### 4.3 Per-criterion alignment (n = 4 each: 2 cases × 2 conditions)

| Criterion | Spearman ρ (partner vs consensus) | Per-criterion gate |
|---|---:|---|
| C1 Verifiable artifact citation | **0.949** | PASS |
| C2 Substantive vs theatrical | **0.707** | PASS |
| C3 Missed real concerns | 0.000 | FAIL |
| C4 Calibrated confidence | 0.236 | FAIL |

The four rubric criteria split sharply: citation discipline and concern substantiveness aligned cleanly; concern-coverage and confidence-calibration did not. We argue in §5.2 that this split is itself substantively meaningful for thinking about when partner-anchor calibration is and isn't a defensible premise.

### 4.4 Per-criterion delta (B − A; framework minus baseline)

| Criterion | Partner Δ | Consensus Δ | Anth Δ | OpenAI Δ | xAI Δ |
|---|---:|---:|---:|---:|---:|
| C1 Verifiable artifact citation | +1.50 | +0.75 | +1.25 | +0.25 | +0.75 |
| C2 Substantive vs theatrical | +1.00 | +0.42 | +0.75 | +0.25 | +0.25 |
| C3 Missed real concerns | +1.00 | +0.25 | +1.00 | +0.00 | −0.25 |
| C4 Calibrated confidence | +1.00 | +0.25 | +0.00 | +0.50 | +0.25 |
| **Mean across criteria** | **+1.125** | **+0.417** | +0.75 | +0.25 | +0.25 |

**The directional finding is unanimous and informative.** Across all four rubric criteria, both partner and cross-vendor consensus rated SPARRING higher than single-agent baseline. The xAI judge produced the only negative cell (C3 = −0.25), but the magnitude is small. The partner's average B−A magnitude (+1.125) is roughly 3× the consensus magnitude (+0.417); we discuss this in §5.3.

### 4.5 Position bias

| Judge | Mean abs(Pass 1 − Pass 2) per cell | Max single-cell diff |
|---|---:|---:|
| Anthropic | 0.38 | 1 |
| OpenAI | 0.38 | 1 |
| xAI | 0.50 | 1 |

No judge showed systematic position bias above 0.5 of a rating point. Maximum single-cell difference between position passes was 1 point (out of 5). Position randomization is doing its job at acceptable signal-to-noise.

### 4.6 Cross-judge agreement

| Metric | Value |
|---|---:|
| Mean stdev across 3 judges per cell | 0.32 |
| Max stdev across 3 judges per cell | 0.62 |
| Cells where all 3 judges within 0.5 (mode collapse) | 1 / 16 (6.2%) |

Cross-judge consensus has internal coherence — judges are within ~⅓ of a rating point of each other on average. The "mode collapse" diagnostic (cells where all three judges produced the same score, indicating they may not be independent) fired on 1 of 16 cells, low. Judges have real signal independent of each other.

### 4.7 Partner rating distribution and ceiling effect

The partner rated the framework condition at the rubric ceiling (5 out of 5) on every criterion in both cases. Single-agent baseline received scores between 3 and 4. This ceiling effect substantially constrains what the headline correlation can detect, since within-framework variance on the partner side is structurally zero. We discuss the implications in §5.3.

---

## 5. Discussion

### 5.1 Why cross-vendor consensus failed the gate when one vendor passed

The pre-registered methodology gate failed (consensus ρ = 0.351), but Anthropic alone passed (ρ = 0.706). The naïve interpretation — "average of three is worse than the best of three because two are worse" — is technically correct but methodologically incomplete.

The cross-vendor structure was selected to mitigate self-preference bias (Panickssery et al. 2024), which would manifest as a same-family judge inflating same-family agent outputs. Both conditions used Claude underneath, so this specific bias source cancels. The cross-vendor structure was thus designed to defend against a bias that, in this study, was structurally absent. What it cost — averaging in two judges (OpenAI gpt-4o and xAI grok-3) that produced flatter, less-discriminating score distributions — was not anticipated to be a meaningful cost at design time.

Practical implication: **for paired-comparison studies where both conditions share the same model substrate, the cost of cross-vendor consensus may exceed its bias-mitigation benefit.** Single-vendor judging with a different model family from the agents under study is a defensible alternative. The cross-vendor structure remains valuable when conditions use different model substrates (e.g., comparing Anthropic vs OpenAI agents), or when self-preference is the suspected bias source.

### 5.2 The per-criterion split is substantively meaningful

The four rubric criteria split cleanly between *highly aligned* (C1 citation discipline ρ = 0.949, C2 substantive concerns ρ = 0.707) and *poorly aligned* (C3 missed concerns ρ = 0.000, C4 calibrated confidence ρ = 0.236).

This is not a measurement-noise pattern. The well-aligned criteria share a property: they ask about something the rater can directly inspect in the output. C1 asks "does this cite specific evidence?" — anyone can see whether citations exist. C2 asks "are the concerns rooted in evidence?" — the rater can read each concern and assess. The poorly-aligned criteria share a different property: they ask about something the rater must *infer* by comparison with a counterfactual. C3 asks "did this miss real concerns?" — the rater must hold a model of "what concerns a careful reader would identify" and compare. C4 asks "is the stated confidence appropriate?" — the rater must compare the stated confidence against an outcome distribution that does not exist for one-shot deliberation.

**The two well-aligned criteria are direct-inspection criteria. The two poorly-aligned criteria are counterfactual-inference criteria.** Counterfactual inference is exactly the cognitive task humans are well-documented to be poor at (Tversky and Kahneman; the planning fallacy; the unknown unknowns problem in safety engineering). LLMs trained on broad corpora may have an advantage on counterfactual-inference rubric items — they have seen many more deliberation patterns than any single rater can hold in mind, which is precisely the comparison space C3 requires.

### 5.3 The partner-anchor calibration premise: an observed inversion

After unblinding, we asked the partner to characterize their own confidence in the LLM judges' ratings versus their own. The partner's response (verbatim, with permission):

> "In truth, I trust the LLMs ability to rate the rubric better than my own. I miss items in those last two categories that the LLMs are much better at catching. My 'all 5s' ratings was basically because I could not see a way to improve it. But the LLMs are better than me at this — they see nuance and misses that I don't."

This is a substantive partner-disclosed observation about the partner-anchor premise itself, and it is the most novel methodological contribution of this case study.

The standard partner-anchor calibration step (Hertzog 2008; standard human-in-the-loop AI evaluation practice) treats human judgment as the calibration target against which AI judgment is measured. The implicit assumption is *human = gold standard*. The partner's observation produces a counter-example: the partner explicitly identified a domain (the C3/C4 counterfactual-inference criteria) where they trusted AI judgment *more* than their own.

Three implications:

**(a) The headline gate failure may be measuring partner under-discrimination, not LLM mis-calibration.** Partner ceiling ratings (5/5/5/5 for the framework on both cases) on criteria where partner explicitly defers to LLM judgment are consistent with two readings: standard ("judges aren't tracking partner") or inverted ("partner isn't tracking what LLMs correctly detect"). The pre-registered gate, designed under the standard reading, cannot distinguish between them. We do not retroactively reclassify the gate result — the FAIL stands as recorded — but we surface honestly that the *interpretation* of what the gate failure means depends on which reading is correct.

**(b) Partner-anchor calibration may be valid for some criteria and invalid for others.** The split observed in §5.2 maps cleanly onto a partner-confidence split: partner trusts their own judgment for direct-inspection criteria (C1, C2) and defers to LLM judgment for counterfactual-inference criteria (C3, C4). A revised methodology that anchors on partner judgment for C1/C2 and on LLM-consensus judgment for C3/C4 would be more honest about the asymmetry of who-is-calibrating-whom.

**(c) The "human-in-the-loop AI evaluation" convention has an asymmetric trust assumption baked in.** Mainstream practice treats human judgment as the gold standard and AI judgment as what's being calibrated. The partner's observation is a real-world counter-example. We do not claim this generalizes — n=1 partner observation in n=2 cases is not a population-level finding. But it is unusual enough for it to be surfaced explicitly during a calibration study that we argue it merits honest reporting and follow-up examination.

### 5.4 Pack-fidelity vs. ground-truth-fidelity: a layered finding from the case-b factual error

A second partner-disclosed observation from a post-pre-reg blind-rating session by Matthew Niedner (the framework author's partner) surfaces a methodological gap distinct from the partner-anchor inversion in §5.3. We separate this from the §6 limitation entry because it has positive contribution beyond a "limitation": it characterizes a specific class of failure mode in author-curated decision-pack methodologies and a corresponding gap in the framework being evaluated.

**The error.** The case-b decision pack asserted that the engine uses a discrete `{walk, trot, canter, gallop}` gait enum. It does not — the actual structure is action-keyed continuous fractions per species, verified by the partner against the engine source after the pilot's runs were complete. The pack assertion was not a deliberate construction; it was the framework author's misremembering of the engine state.

**Three layers of failure / non-failure.** Both pilot conditions were faithful to the input they received. Condition A (single-agent baseline) reasoned over the fictional enum without flagging uncertainty. Condition B (SPARRING) did the same, despite its Generator persona being explicitly configured with the engine codebase as its evidence base. Neither caught the discrepancy. The error was caught by Matthew, a partner reading the pack and outputs critically *outside* the conditions, with the engine source available to verify against.

**The framework-discipline gap.** The SPARRING framework's *verifiable-artifact requirement* mandates that every concern an agent raises must cite a specific artifact (file path, source citation, edge case). It does NOT mandate that agents fetch and verify cited artifacts, nor that they verify the input pack's assertions about external artifacts. There is a real distinction between "this concern cites a real source" and "this concern's source actually says what the agent claims it says" — and an even larger distinction between either of those and "the input pack's framing of the engine state actually matches the engine state." The framework's discipline addresses the first; this study revealed it does not address the second or third.

**The partner-residual-safety as load-bearing.** The framework's substrate documentation positions partner judgment of surfaced artifacts as the residual safety against coordinated theater and shared agent blind spots. The case-b error is a small but clean instance of that residual safety actually firing: a partner reading the deliberation outputs *with the actual ground truth available* caught what the conditions could not. We argue this should be reported in the literature as evidence that the partner-residual-safety position is not theoretical — it is operationally load-bearing and can catch real factual errors in author-curated decision-pack methodologies.

**Two implications for Phase 2 and for similar pilots elsewhere:**

1. **Author-curated decision packs need an independent fact-check pass before any conditions run.** Standard scientific-paper hygiene applied to deliberation methodology. This is the §6 limitation framing. Cheap; defensible; should be standard.

2. **Frameworks claiming verifiable-artifact discipline should consider extending the discipline to source-fetch-and-verify, not just source-citation.** This is a real cost-benefit decision: every additional Read call lengthens iterations and increases cost, but the gap demonstrated here is real. A defensible compromise is "fetch-and-verify required when the cited artifact is core to a load-bearing claim; citation-only sufficient for supporting context." Future framework iterations should think about which of their artifact-discipline rules apply to cited evidence vs. assumed-as-given input.

We do not commit either implication as a Phase 2 design rule here; both are calls for the eventual scoper.

### 5.5 What this study can and cannot conclude

**Can:**
- Direction: all four raters (1 partner + 3 LLM judges) rated the framework higher than baseline on every one of 4 criteria, on n=2 cases. This is preliminary, exploratory evidence consistent with the framework producing measurably different and consistently-preferred deliberation. It is not a confirmatory effect-size estimate.
- Methodology observation: the cross-vendor consensus structure cost more (in calibration noise) than it saved (in self-preference mitigation) in this paired-comparison design.
- Methodology observation: the partner explicitly inverted the partner-anchor premise on subjective rubric criteria, which is a methodological data point worth reporting.

**Cannot:**
- "The framework is better than single-agent." Too strong; n=2, no causal claim, no power calculation, single rater.
- A specific effect-size estimate. n=2 case data is calibration-sample size, not effect-size estimation size.
- "Cross-vendor LLM-as-judge tracks human judgment." Failed the gate as recorded; one judge passed alone, two did not.
- Generalization to other deliberation frameworks, other domains, other rubrics, or other partner pools.

### 5.6 Future work — four paths

We outline four follow-up paths, ordered from most-conservative-of-this-study's-methodology to least:

**Path A (rubric refinement)**: Drop or reshape C3 and C4 (the criteria that failed per-criterion alignment), then re-run Phase 1 calibration. Treats the FAIL as a rubric problem, not a judge problem.

**Path B (judge-panel revision)**: Drop the worst-aligning judge (currently OpenAI or xAI), or add a fourth (Gemini-class) to broaden the cross-vendor structure. Re-run with the revised panel.

**Path C (combined)**: Both A and B.

**Path D (per-criterion split methodology)**: Adopt the partner's reframe formally — anchor on partner judgment for direct-inspection criteria and on LLM-consensus judgment for counterfactual-inference criteria. Different criteria, different gold standards. Most novel methodology, least precedent.

**Path E (honest abandon)**: Conclude that LLM-as-judge for this rubric is not viable at this scale, and accept that a different evaluation methodology (e.g., contracted-rater RCT at the documented $40–60k+ cost for ~12-month timeline) is what would be needed for a confirmatory comparative claim. Per pre-registration, this is a defensible outcome.

We do not commit to any path here; the costs and benefits depend on whether the eventual goal is a peer-reviewed-journal claim or continued internal use of the framework with bounded confidence.

---

## 6. Limitations

- **n = 2 cases.** Calibration-check sample size; not statistical-power sample size. No effect-size claim.
- **n = 1 partner.** Pre-registration anticipated 1–2 partners; only one rated. Inter-rater reliability cannot be computed.
- **Partner ceiling effect.** Partner gave the framework 5/5/5/5 on every criterion in both cases. Within-framework variance on the partner side is structurally zero, limiting the headline correlation by construction.
- **Author-as-rater.** The partner who rated is also the author of the framework being evaluated. This is acknowledged as a substantial limit; a Phase 2 design would need raters with no framework-author conflict.
- **Pre-registered model substitutions.** Pre-reg targeted Anthropic Sonnet 4.7 (used Sonnet 4.6, the current frontier Sonnet) and "Grok-4 class" (used Grok-3, the available key). Substantive substitutions; effect on results unknown.
- **Both conditions use Claude underneath.** Conditional on this design choice, cross-vendor consensus structure does not provide the self-preference defense it was designed for. A Phase 2 cross-substrate design (e.g., Condition A on GPT, Condition B on Claude) would test substrate independence at the agents layer, which this study does not.
- **Rubric not validated independently.** The four criteria were adapted from an internal SPARRING evaluation rubric; their validity for cross-condition comparison was not separately validated.
- **Case B decision pack contained a factual error about engine state.** The pack stated that the combat engine uses a discrete `{walk, trot, canter, gallop}` gait enum in its species locomotion data, framing the "stepped vs linear" sub-question as a choice over an existing structure. In fact, verified post-hoc against the engine source, the actual structure is *action-keyed continuous fractions per species* — there is no such enum. The framing in the pack was the author's misremembering of the engine state, not a deliberate construction. Both conditions received the same incorrect framing, so comparative validity is preserved (no condition was advantaged), but the substantive recommendations both conditions produced are recommendations against a fictional code surface. Discovered during a post-pre-reg blind-rating session by partner Matthew Niedner; preserved as an architectural in-flight note for the eventual motion-accuracy coupling milestone scoper. The fact that this error was discovered during the rating session and not before is itself a reflection of how decision-pack curation under partner-author conditions can leak factual errors into the evaluation surface; an independent fact-check pass on case packs would be appropriate Phase 2 hygiene.

---

## 7. Data and code availability

All artifacts from this study are committed in a public bundle, including: the pre-registration (timestamped commit before any compute spent), both cases' decision packs, both conditions' raw outputs and normalized texts, all 12 LLM judge calls (raw API responses), the Spearman analysis script, partner ratings (audit-trail-preserving), and this preprint draft. Bundle URL provided at submission.

The deliberation framework's source documentation is available in the same artifact bundle.

---

## 8. Acknowledgments

The cross-vendor LLM judges were Anthropic Claude Sonnet 4.6 (anthropic.com), OpenAI GPT-4o (openai.com), and xAI Grok-3 (x.ai). Both conditions' agent calls used Anthropic Claude Opus 4.7. The author thanks Resource Forge LLC's project partners for cases and prior framework-development collaboration. AI tooling (Anthropic Claude) was used in drafting this preprint and is disclosed accordingly.

---

## 9. References

[length call: short reference list, real citations only]

- Hertzog, M.A. (2008). Considerations in determining sample size for pilot studies. *Research in Nursing & Health* 31(2), 180-191.
- Panickssery, A., Bowman, S.R., & Feng, S. (2024). LLM Evaluators Recognize and Favor Their Own Generations. *arXiv*:2404.13076.
- Sharma, M., et al. (2023). Towards Understanding Sycophancy in Language Models. *arXiv*:2310.13548.
- Wang, P., Li, L., Chen, L., Cai, Z., Zhu, D., Lin, B., Cao, Y., Liu, Q., Liu, T., & Sui, Z. (2023). Large Language Models are not Fair Evaluators. *arXiv*:2305.17926.
- Wasserstein, R.L., & Lazar, N.A. (2016). The ASA Statement on p-Values: Context, Process, and Purpose. *The American Statistician* 70(2), 129-133.
- Yin, R.K. (2014). *Case Study Research: Design and Methods*, 5th ed. Sage.
- Zheng, L., Chiang, W.-L., Sheng, Y., Zhuang, S., Wu, Z., Zhuang, Y., Lin, Z., Li, Z., Li, D., Xing, E.P., Zhang, H., Gonzalez, J.E., & Stoica, I. (2023). Judging LLM-as-a-Judge with MT-Bench and Chatbot Arena. *arXiv*:2306.05685.

Plus: ICZN Code (4th ed., 1999); de Queiroz & Gauthier (1990, 1992); Mayr (1969); Hyland (1994); Conlan (2003); May (2007); Selby (2000); Hardy (2010); Smail (1995, 2nd ed.); Karasulas (2004); Tolkien (1981, Carpenter ed., Letters); Shippey (2000); Frye (1957); Campbell (1949) — see decision-pack and spar-artifact citations in the artifact bundle for full evidence chains.

---

## Appendix A — per-cell canonical table

[length call: include the 16-row per-cell.json as a formatted markdown table; available in the artifact bundle as machine-readable JSON]

| Case | Condition | Criterion | Partner | Anthropic | OpenAI | xAI | Consensus |
|---|---|---|---:|---:|---:|---:|---:|
| A | baseline | C1 | 3 | 3.5 | 4.5 | 4.5 | 4.17 |
| A | baseline | C2 | 4 | 4.0 | 4.0 | 4.0 | 4.00 |
| A | baseline | C3 | 4 | 4.0 | 4.0 | 3.5 | 3.83 |
| A | baseline | C4 | 4 | 3.5 | 3.0 | 4.0 | 3.50 |
| A | SPARRING | C1 | 5 | 4.5 | 4.5 | 4.5 | 4.50 |
| A | SPARRING | C2 | 5 | 4.5 | 4.0 | 3.5 | 4.00 |
| A | SPARRING | C3 | 5 | 4.5 | 3.5 | 3.0 | 3.67 |
| A | SPARRING | C4 | 5 | 3.5 | 3.0 | 4.0 | 3.50 |
| B | baseline | C1 | 4 | 4.0 | 4.5 | 4.5 | 4.33 |
| B | baseline | C2 | 4 | 4.5 | 4.5 | 4.0 | 4.33 |
| B | baseline | C3 | 4 | 4.5 | 4.0 | 3.0 | 3.83 |
| B | baseline | C4 | 4 | 4.0 | 3.5 | 3.5 | 3.67 |
| B | SPARRING | C1 | 5 | 5.0 | 5.0 | 5.0 | 5.00 |
| B | SPARRING | C2 | 5 | 5.0 | 4.0 | 5.0 | 4.67 |
| B | SPARRING | C3 | 5 | 4.0 | 4.5 | 4.0 | 4.17 |
| B | SPARRING | C4 | 5 | 4.5 | 3.5 | 4.0 | 4.00 |

(Numbers are position-averaged means across the two pass scores per judge per cell.)

---

## Appendix B — pre-registration as posted

[link; full text in artifact bundle]

---

## Appendix C — sample of normalized output (one case, one condition)

[length call: include one normalized output verbatim — probably Case A SPARRING — as a worked example so reviewers can see what judges actually scored]

---

## Editorial notes for the partner reviewing this draft

- Author affiliation: I have you as Resource Forge LLC; confirm or revise.
- Author byline: single-author or do you want a multi-author byline? Idris Harmon, Lena Vasik, or other partners contributed to the case content (decision packs draw on their thread #200 and §6/§7 commentary respectively); they could be acknowledged in §8 instead of bylined.
- Anthropic disclosure: the §8 acknowledgments mention AI tooling was used in drafting. Anthropic publishes guidance on AI-tooling disclosure (https://www.anthropic.com/responsible-disclosure or equivalent — please verify the current URL); follow that standard.
- Conflict-of-interest note: the §0 author block names the partner as both framework developer and rater; this is honest but you may want to expand it.
- Numbers in Appendix A are pulled from the analysis canonical table (`docs/bfn/llm-judge-pilot-2026-05-02/analysis/per-cell.json`) and should be sanity-checked once more before submission — specifically that consensus values match the JSON's `judge_consensus` column.
- "[length call]" tags mark sections where I made a judgment call that you may want to revisit.
- Suggested venue order: arXiv preprint first (cs.AI / cs.HC), then optionally a workshop track at AIES, FAccT, or NeurIPS. Preprint can be cited as prior work in any later confirmatory paper.
- The blog companion (you mentioned) should probably be ~800-1,200 words, written in plain language, with the key visualization being the per-criterion delta table. Happy to draft that as a follow-up if useful.

End of working draft.
