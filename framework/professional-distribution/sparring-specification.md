# The SPARRING Framework: Specification

**Author:** Barton Niedner
**Affiliation:** Resource Forge LLC
**Version:** 1.0 (preprint)
**Date:** 2026-04-30
**License:** CC-BY-4.0
**Companion documents:** *The SPARRING Framework: Reference Implementation* [@niedner2026implementation]; *The SPARRING Framework: Reader's Guide* [@niedner2026readersguide] (recommended for new readers seeking orientation)

---

## Abstract

Agentic systems built on large language models (LLMs) exhibit characteristic failure modes that compound across agent handoffs: pleasing bias, confirmation bias, anchoring, misread questions, specialization blind spots, hallucinated detail, confidently-wrong outputs, and bandwagon contamination. In single-agent interactions these failure modes are tractable; in chains of agents handing outputs to each other, one agent's softened "yes" or invented specific becomes the next agent's premise, and the system reinforces its own assumptions while sounding coherent. This paper presents the SPARRING Framework, a method for designing multi-agent decision-making systems that pair two specialists with genuinely different evidence in a structured cross-evidence pressure-test. The framework specifies four phases (SPARK, Pattern Lock, the Cut, PNP), a two-role mechanic (Generator and Challenger) with role-shifting Challenger function across phases, nine operational disciplines that prevent collapse to theatrical adversariality, and an explicit response protocol for unresolved disagreement. The framework's scope is high-quality decision-making where iterative pushback produces quality benefits the single-shot path cannot reach; it is not proposed as a default discipline for every prompt. The contribution is structural: the framework specifies what must be true of a multi-agent decision system for its quality leverage to be real, and what to do when those conditions cannot be satisfied.

---

## 1. Introduction

### 1.1 The compounding-failure problem in agentic systems

Large language models (LLMs) exhibit several documented behavioral failure modes when deployed as decision-making agents. The most-cited is *sycophancy* or *pleasing bias* -- the tendency to agree with users and produce outputs that signal helpfulness rather than accuracy [@sharma2023sycophancy; @perez2022evaluations]. @sharma2023sycophancy measures sycophancy via a *preference-flip protocol* -- presenting models with factual questions accompanied by user-stated incorrect views and measuring how often the model's answer flips toward the user. @perez2022evaluations introduces *model-written evaluations* as a methodology for discovering behavioral failure modes via LM-generated test cases. These methodologies inform the empirical evaluation proposed in Section 5.4. Related failure modes include confirmation bias (interpreting ambiguous evidence consistent with prior expectations), anchoring (gravitating toward refining initial outputs rather than reconsidering), and hallucinated detail (inventing specifics that go unchecked).

In single-agent interactions, these failure modes are tractable through prompt engineering, training-time alignment, and human-in-the-loop review. In *agentic systems* -- systems composed of multiple LLM-driven agents that hand outputs to each other -- the same failure modes acquire a new structural property: they *compound*. One agent's softened "yes" becomes the next agent's premise. One agent's hallucinated specific is cited as established fact by downstream agents. Specialization blind spots in one agent are not corrected by another agent with the same blind spot. The system reinforces its own assumptions while producing coherent-sounding outputs that no individual agent ever explicitly endorsed.

The compounding-failure problem is *not* solved by the structural defenses that work for single-agent systems. Adding more agents does not, by itself, add cross-validation; correlated agents produce correlated errors. Specialization through role labels alone does not produce genuine cross-evidence challenge; two agents with the same training data and source pool, given different role names, produce more correlated outputs than they appear to. The structural property of the failure mode (compounding) requires a structural defense.

### 1.2 Contribution

This paper presents the **SPARRING Framework**, a method for designing multi-agent decision-making systems that defends against the family of compounding LLM failure modes through structured cross-evidence challenge. The framework's contribution is structural: it specifies operational disciplines that must hold for the multi-agent quality leverage to be real, and a response protocol for cases when those disciplines cannot be satisfied.

Specifically, the framework specifies:

- **Four phases** of decision-making (SPARK, Pattern Lock, the Cut, PNP), each with distinct objectives and iteration semantics.
- **A two-role mechanic** (Generator and Challenger) where the Challenger's function shifts across phases (expand, filter, close, stress-test).
- **Nine operational disciplines** that prevent the structure from collapsing into theatrical adversariality.
- **A failure-mode applicability boundary** with three explicit cases the framework does not address (the model ceiling, pure judgment-shaped questions, low-stakes routine work).
- **A disagreement-at-cap response protocol** with five canonical responses (pick-a-side-with-tradeoffs, defer, reframe, escalate, synthesize) for cases when the iteration cap is reached without convergence.

### 1.3 Scope and non-goals

The framework's target is **high-quality decision-making where iterative pushback produces quality benefits the single-shot path would miss**. Token cost is not the optimization metric; decision quality is. The framework is not proposed as a default discipline for every prompt; routine factual lookups, mechanical edits, and single-shot work do not benefit from the structure and may be slowed by it.

The framework's claim is bounded by three conditionalities, discussed in Section 5: (1) careful design of personas, evidence bases, and operational rules is doing significant work in the framework's value claim; naive multi-agent applications can produce outputs worse than single-agent baselines. (2) The framework's quality leverage requires that paired agents have access to *genuinely different evidence*; persona-only specialization with shared evidence is real but smaller than the structural framing suggests. (3) The framework reduces single-shot variance and bias toward the underlying model's quality ceiling; it does not transcend that ceiling. These conditionalities are statements about *when* the framework's quality leverage is real and when it shrinks toward zero; they do not invalidate the framework but bound its applicability.

This paper is a specification of the framework. A companion *Reference Implementation* document [@niedner2026implementation] provides a concrete reference architecture for deploying the framework as a CLI tool, including agent topology, persona library structure, schema specifications, and a phased build sequence.

---

## 2. Background and related work

### 2.1 Multi-agent systems and orchestration patterns

Multi-agent systems (MAS) is the umbrella term for systems composed of multiple agents that interact to produce outputs. Within MAS, two coordination patterns dominate: *orchestration* (centralized control, where a coordinator decides what each agent does and when) and *choreography* (decentralized control, where each agent decides independently based on shared signals or environment state) [@dorri2018multiagent].

In the LLM-agent landscape, three frameworks dominate at the multi-agent coordination layer; engaging each at the level of what its primitives actually provide grounds the comparison.

**LangGraph** (built on LangChain) ships graph-based stateful orchestration with two named multi-agent patterns: a *Supervisor* pattern (centralized delegator) and a *Swarm* pattern (decentralized peer-to-peer handoffs via the `langgraph-swarm` package). Termination semantics in both patterns center on the active agent completing its work or handing control back, not on mutual agreement between paired agents [@langchain2026benchmarking]. The framework provides observability (checkpointing, time-travel) and the LangSmith ecosystem layers in pairwise-comparison evaluation as plumbing, but none of it constitutes a built-in defense against theatrical disagreement: nothing structurally enforces distinct evidence bases between agents (the framework's D2), nothing requires agent claims to cite verifiable artifacts (D3), and convergence-by-mutual-agreement (D4) is not a primitive.

**The AutoGen / Microsoft Agent Framework lineage** [@wu2023autogen] entered maintenance mode in 2025; the official successor, Microsoft Agent Framework 1.0, GA'd April 3, 2026 [@microsoft2026maf]. MAF replaces AutoGen's implicit GroupChat with explicit typed graph-based Workflows and ships five orchestration patterns (sequential, concurrent, handoff, group-chat, Magentic-One). Magentic-One is the lineage's flagship multi-agent shape: an orchestrator agent maintains a Task Ledger (facts, guesses, plan) and a Progress Ledger that gates termination -- structurally a single-orchestrator-judgment pattern, not paired both-must-agree. Like LangGraph, MAF provides observability and replay primitives but no built-in mechanism enforcing distinct evidence bases between paired agents, no verifiable-artifact requirement on agent claims, no two-role mutual-agreement convergence, and no rubric-with-anchored-definitions evaluator targeting theatrical disagreement specifically.

**CrewAI** ships a role-based crew model with three documented process types: *Sequential* (pipeline), *Hierarchical* (manager delegates to workers), and *Consensual* -- which is documented as planned and explicitly unimplemented as of 2026-05 [@crewai2026processes]. The Consensual process is the industry's closest acknowledgment of the SPARRING-shape gap; the framework's own documentation describes a process where agents must reach consensus, ships no implementation, and notes the future-work status. The active processes are single-judge: Hierarchical centralizes authority in a manager; Sequential is non-judgmental pipeline. CrewAI ships a `crewai test` CLI that produces 1-10 numeric scores per task, agent, and crew, but the methodology is not exposed, the scale is not anchored per criterion, and the test does not target theatrical-disagreement detection.

**OpenAI Agents SDK** and **Anthropic Claude Agent SDK** operate at a different layer: they provide tool-use-loop primitives for building agents, not coordination frameworks for multi-agent decision flows. A SPARRING-shaped deployment can be built on either SDK as the agent runtime; neither ships SPARRING's coordination disciplines as built-in primitives.

These frameworks provide the *plumbing* for multi-agent systems -- the runtime, the coordination primitives, the observability layer. They do not provide the *method* SPARRING specifies: a discipline-bundled approach that any of them could be used to implement, but none of them ships out-of-box. The closest near-miss in the landscape is CrewAI's Consensual process [@crewai2026processes], which is named but unimplemented; the gap is acknowledged in the field, not yet bridged.

### 2.2 Sycophancy and pleasing bias in language models

The sycophancy failure mode in LLMs has been documented extensively. @sharma2023sycophancy establish that LLMs trained with reinforcement learning from human feedback (RLHF) exhibit systematic preferences for outputs that align with user-stated views, even when those views are incorrect. @perez2022evaluations demonstrate that LLMs can be steered toward agreement through framing alone, without changes to the underlying question. Subsequent work on Constitutional AI [@bai2022constitutional] proposes training-time interventions to reduce sycophancy by training models to push back rather than agree.

These training-time and prompt-time defenses are partial. RLHF-trained models can be made less sycophantic through targeted training, but the residual risk is the agent pleasing the user's *expectation of seeing structured challenge* -- a meta-sycophancy that is harder to counter via training alone. Prompt-engineering defenses are vulnerable to the agent producing fake disagreements to satisfy a "find flaws" success criterion.

The SPARRING Framework operates at a layer above these training-time and prompt-time defenses: it specifies the *structure* of the multi-agent interaction so that disagreement, when produced, is grounded in evidence the agents could not synthesize from a shared training distribution alone. The defenses at the model layer and the framework layer are complementary; the framework adds structural defense-in-depth without replacing the model-level work.

### 2.3 Structural inspirations

The framework draws structural inspiration from three traditions in dialectic philosophy and adversarial methods:

**Popper-style falsification** [@popper1959logic]. Scientific theories are never proven, only falsified by counterevidence. The relationship between proposal and critique is *adversarial elimination*: the critique attempts to break the proposal; if it cannot, the proposal stands; if it can, the proposal is replaced. The framework's base loop is Popperian: the Generator proposes, the Challenger attempts falsification with cited evidence, convergence either preserves or replaces the proposal. The output is always one side or the other -- never a hybrid.

**Hegelian dialectic** [@hegel1807phenomenology]. A position generates its opposing position; the conflict resolves into a *synthesis* that integrates insights from both and transcends both. The synthesis becomes the next thesis, and the cycle repeats. (The explicit thesis-antithesis-synthesis terminology is more accurately attributed to Heinrich Moritz Chalybaus in 1837 than to Hegel directly; the formulation is widely used as Hegelian shorthand and is used in that loose sense here.) The framework's disagreement-at-cap response protocol (Section 3.5) extends the base Popperian loop with synthesis as a named response option, giving the framework a Hegelian extension at one boundary while preserving the Popperian shape for in-loop convergence.

**Red-team / blue-team and adversarial learning**. The security-engineering tradition of pairing offensive (red) and defensive (blue) teams is structurally similar to the framework's two-role mechanic. Adversarial learning in machine learning [@goodfellow2014generative] uses the same shape at the training-data layer. The framework adopts this two-role structure for decision-making rather than for security audits or training.

### 2.4 What existing frameworks do not provide

Per the per-framework analysis in Section 2.1, no multi-agent framework in current production use ships an out-of-box defense for theatrical adversariality. The closest equivalents in the technique literature are:

- Anti-pattern prompting (cheap, weak): instructing agents to "find flaws" or "play devil's advocate."
- Asymmetric prompting (cheap, slightly stronger): giving Challenger and Generator different success criteria.
- Tool-grounded verification (medium, real): requiring concerns to cite specific artifacts.
- Domain-grounded specialization (medium-high, robust): pairing agents with genuinely different domain expertise.
- Multiple Challengers (medium-high, robust): pairing N challengers with different specializations.
- Eval harness with rubric scoring (high, strong): measuring whether challenge is real or theatrical.
- Training-layer mitigations: RLHF / Constitutional AI work on sycophancy reduction.

These are tools, not a method. The SPARRING Framework integrates these tools into a coherent operational discipline with explicit structural commitments, a phased iteration loop, and a response protocol for the cases the structural commitments cannot satisfy.

---

## 3. Framework definition

### 3.1 The four phases

A SPARRING-shaped decision moves through four phases. Each phase has a default iteration cap; the defaults are starting points and the right cap is dynamic, adjustable per topic, with the *ability* to iterate mattering more than the specific count.

**SPARK (default ~5 iterations).** Divergent generation. Produce many possible options, framings, or approaches. The Challenger's function in this phase is *expand* -- pressuring the Generator toward more options, less premature filtering, broader coverage of the option space.

**Pattern Lock (default ~2 iterations).** False-novelty detection. The transition between divergent generation and convergent evaluation: identify whether continued SPARK is producing genuinely new options or repetitive variants. Naming false-novelty detection as a discrete phase grants social permission to stop brainstorming without prematurely closing on a single option.

**The Cut (transition, not a generative phase).** Mode shift from generation to evaluation. The Cut is structurally a transition rather than an iterative phase; some implementations carry an iteration count for the Cut, but the count reflects how many evaluations the practitioner runs to choose from the SPARK options, not how many times the Cut itself iterates.

**PNP -- Polite, Not Pleasing (default ~3 iterations).** Convergent pressure-testing. Apply structured challenge to the chosen option to surface flaws, hidden costs, missed concerns, and confidently-wrong claims. The Challenger's function here is *stress-test*: actively attempt to break the chosen option using cited evidence.

The phases are named to be operationally distinct. SPARK is not "the same as PNP but earlier"; it is a different mode with different success criteria. Pattern Lock is not "the same as the Cut"; it is a phase that runs *before* the Cut to test whether SPARK has saturated.

### 3.2 The SPARRING mechanic: Generator and Challenger roles

Two roles run throughout the four phases:

**Generator.** Proposes options, framings, or evaluations of the topic. Reads the topic plus (in iteration rounds 2 and later) the Challenger's prior pressure-test. Produces a structured proposal plus an explicit agreement signal indicating whether the Generator considers the current state converged.

**Challenger.** Pressure-tests the Generator's output. Reads the Generator's current proposal plus the topic. Produces a structured pressure-test with cited concerns plus an explicit agreement signal.

The Challenger's *function* shifts per phase: expand in SPARK, filter in Pattern Lock, close in the Cut, stress-test in PNP. The role label (Challenger) is the same; the operational behavior differs.

**Convergence.** The framework requires *both* roles to signal `agree: true` for the loop to converge. Generator-only convergence is structurally suspect: it would reintroduce pleasing-bias drift toward the user's apparent expectation of completion, one of several failure modes the two-role structure exists to defeat. Convergence requires explicit agreement from both roles.

**Disagreement at iteration cap.** When the iteration cap is reached without both-role agreement, the unresolved question returns to the human or parent agent. This is information, not failure. Section 3.5 specifies the response protocol for this case.

### 3.3 The nine disciplines

The framework's structural commitments are encoded in nine disciplines. Without these, the structure collapses into theater.

**D1: Apply to decisions, not every prompt.** SPARRING is for high-quality decision-making where iterative pushback genuinely adds value. Factual lookups, mechanical edits, and routine questions do not benefit from the structure. Section 4.3 specifies the applicability boundary.

**D2: Generator and Challenger must draw on genuinely different evidence.** Two agents with the same training data and the same source pool, given different role labels, produce more correlated outputs than they appear to. The strongest specialization is when each role has access to evidence the other does not -- different domains, different data sources, different tools. The framework requires distinct *Role + Domain Knowledge* between roles (distinct expertise, distinct evidence-base scope, distinct operational rules); the optional Persona layer (voice, style, presentation) need not differ. Manufacturing tonal contrast across roles does not satisfy D2.

**D3: Concerns must cite verifiable evidence.** Every challenge raised must point to a specific artifact -- a fact, a source, a measurement, a concrete failure mode. Concerns without a citable artifact are dismissed as theatrical. If an honest concern exists but no artifact is yet citable, the Challenger states this explicitly rather than pretending or suppressing.

**D4: Both roles must agree to converge.** Convergence requires explicit agreement from both Generator and Challenger. Generator-only "we are done" is structurally suspect. Disagreement at iteration cap returns the unresolved question to the human or parent agent per the response protocol.

**D5: Self-invocation triggers must be observable.** Agents are unreliable at assessing their own uncertainty. When an agent decides "this needs sparring," the trigger should be a concrete observable condition (file pattern, command flag, partner-passed hint, stakes/complexity/confidence/authority signal), not a feeling.

**D6: Measurability.** The framework's quality claim is testable. The minimum viable form is rubric-scored sampling of Challenger outputs on a 5-point scale with per-criterion anchored point descriptions (criteria like artifact citation, substantive vs. theatrical, missed concerns, calibrated agreement). The mature form is automated rubric scoring with held-out test cases. Without measurement, quality claims are aspirational.

**D7: Observability.** Every spar produces a structured artifact recording the topic, both roles with their evidence bases, the iteration log, both roles' agreement signals, the artifacts cited, and the converged result or unresolved disagreement. The artifact is human-readable, machine-parseable, and persists. It serves three functions: audit, debugging, and measurement input.

**D8: Dialectic Surface.** The framework requires an asynchronous communication channel where humans and agents exchange escalation requests, corrections, and ongoing dialogue. The surface must be persistent within the active-communication horizon, shared (humans and agents have equivalent read/write access), asynchronous, threadable, and equipotent for participants. Implementation is tool-agnostic (chat platforms with thread discipline, message boards, issue trackers, email lists with structured threading).

**D9: Reference Record.** The framework requires a persistent, structured record of decisions, spar artifacts, resolved questions, and accumulated institutional knowledge. The record must be persistent for the long term, structured, auditable, searchable, and curated. The record's optimization differs from D8's: long-term retrieval rather than active turn-taking. Both functions are required; whether they share one physical surface (with discipline) or are separated depends on deployment scale.

### 3.4 Variants and operational patterns

The framework's base form (Generator + Challenger, four phases, both-must-agree convergence) admits several variants and operational patterns documented as part of the framework rather than as future extensions.

**Phase-isolation modes.** SPARK alone (for hypothesis generation when the question is "what are the options"), Pattern Lock alone (as ideation-hygiene tool when divergent thinking has saturated), PNP alone (for single-pass pressure-test of an already-chosen option). Each phase carries enough structure to run independently when the full four-phase loop is not warranted.

**Role variants.** Human-in-the-Generator with AI Challenger (for decisions where the human's framing is the proposal and the AI provides cited pressure-test). AI-Generator with Human-in-the-Challenger (for decisions where the AI's proposal is being audited by a human with deeper domain context). Multi-Challenger ensemble (single Generator, N Challengers each grounding in distinct evidence; convergence requires all N or a configurable threshold). Watching-role Challenger (a long-running agent monitoring an ongoing system rather than a one-shot decision).

**Substrate variants.** Cross-model SPARRING runs Generator and Challenger on different model families (e.g., Generator on one vendor's model, Challenger on another's) to extend Discipline 2's disjointness one layer below the evidence-base layer. Same-vendor pairings satisfy Discipline 2 at the evidence-base layer (different sources to read) but share model substrate -- training distribution, RLHF approach, sycophancy-reduction tuning, reasoning-pattern biases. Cross-model pairings reduce the residual coordinated-theater risk discussed in Section 5.2 because agents that do not share trained-in pleasing patterns cannot tacitly coordinate to look like they are disagreeing. Tradeoffs: API integration cost across multiple vendors, roughly multiplicative per-spar cost, latency variance across vendor SLAs, output-format normalization in the orchestrator. Cross-model SPARRING is orthogonal to the Multi-Challenger ensemble role variant -- the variants compose: Multi-Challenger ensembles can run with same-substrate Challengers, cross-substrate Challengers, or both, at the deployment's discretion. *(Added v1.1.)*

**Deployment patterns.** Domain-specific SPARRING templates (pre-built configurations for recurring decision types: code review, plan review, vendor selection, hire decisions). Pre-emptive SPARRING for decision archives (running the framework against past decisions to identify whether they would survive present-day pressure-testing).

### 3.5 Disagreement-at-cap response protocol

When the iteration cap is reached without both roles signaling agreement, D4 returns the unresolved question to the human or parent agent. That handoff is information, not failure. The receiving party has multiple legitimate responses; naming them prevents the rarer ones from being skipped. The five canonical responses:

**Pick a side with explicit tradeoffs.** Accept the Generator's proposal or the Challenger's strongest counter, documenting what the other side surfaced as a known and accepted cost. Often the right move when the disagreement is real but the costs of one direction are demonstrably more acceptable than the costs of the other.

**Defer the decision.** The inconclusiveness is the answer: not enough evidence yet, decide later. Schedule a follow-up trigger (a date, a milestone, an event that will surface more evidence) and let the disagreement remain explicitly open. Often the right move when the cost of premature commitment exceeds the cost of waiting.

**Reframe the question.** The loop revealed the question itself was malformed -- two valid evidence bases pointed at different problems, not the same problem with different answers. Restart with a sharper question. Often signaled by both roles raising legitimate concerns that do not actually conflict.

**Escalate.** The decision needs more stakeholders than the original human, or a stakeholder with different authority. The disagreement may be tractable but not at this level. Often signaled by one role citing constraints the original human cannot adjudicate.

**Synthesize.** Produce a third option neither role proposed, integrating insights from both. The most ambitious response and the most likely to be skipped because it is hardest. Real synthesis is structurally distinct from compromise: compromise averages the two positions; synthesis transcends both by integrating their evidence into a position neither alone reached. The discipline is recognizing the difference -- if the proposed third option is the midpoint between Generator and Challenger, it is not synthesis.

These responses are canonical, not exhaustive. Situations may warrant a response not on the list; the menu's purpose is to make the rarer moves visible, not to constrain the receiving party's judgment.

A note on synthesis specifically: a one-shot synthesis at the disagreement-at-cap boundary is a hybrid of Popper-style falsification (the framework's base shape) and Hegelian dialectic (where synthesis becomes the next thesis and gets antithesized in turn). True Hegelian iteration would feed the synthesis back into a fresh SPARRING round to test it the same way the original proposal was tested. That second-pass option is available -- "synthesize, then re-spar the synthesis" -- but is not the framework's default because it doubles cost for a relatively rare class of decision. When stakes are very high and the synthesis itself contains novel claims, the second pass is worth considering.

---

## 4. Failure modes addressed

### 4.1 The family of compounding failure modes

The framework defends against a family of single-perspective failure modes that compound when agents hand outputs to each other. Pleasing bias is the most-cited member of the family in the literature [@sharma2023sycophancy] but is one item in the family rather than its center; framing the framework as a sycophancy defense understates what it does.

The taxonomy below is deliberately level-spanning: it includes model-behavior failures (pleasing bias, misreading the question, hallucinated detail, confidently wrong outputs), cognitive-bias borrowings applied to model behavior (confirmation bias, anchoring), and multi-agent coordination failures (specialization blind spots, bandwagon contamination). The compounding-failure thesis (Section 1.1) motivates the cross-level coverage: in agentic systems, a model-behavior failure becomes a coordination failure on the next handoff -- one agent's softened "yes" becomes the next agent's premise; one agent's hallucinated specific is cited as established fact downstream. Defenses that span only one level cannot meet the compounding-failure shape. The closest reference in the literature, @perez2022evaluations, organizes its evaluation suite by behavior content (persona, sycophancy, advanced-AI-risk, gender bias) rather than by analytical level; this framework's level-spanning organization is a deliberate departure that serves the multi-agent-compounding use case rather than the single-model-evaluation use case content-based taxonomies are built for.

The full family the framework's structural commitments address:

**Pleasing bias / sycophancy.** Agents agree too easily, especially under social or framing pressure. In agent chains, one agent's softened "yes" becomes the next agent's premise. The two-role structure with both-must-agree convergence (D4) and the verifiable-artifact requirement (D3) directly defend against this.

**Confirmation bias.** A single agent interprets ambiguous evidence in line with what it expects to see. Two agents with different evidence have different priors; D2's distinct-evidence-bases requirement is the structural defense.

**Anchoring.** Once a first answer is generated, subsequent thinking gravitates toward refining rather than reconsidering. The Pattern Lock phase is named specifically to surface anchoring effects in SPARK; the role-shifting Challenger function across phases prevents the loop from settling into a single anchor.

**Misreading the question.** A single-prompt-interpretation error propagates downstream. Two agents with different evidence bases are more likely to surface a misread because they may interpret the prompt against different reference contexts.

**Specialization blind spots.** Single specialists miss cross-domain risks. Different-evidence specialists have different blind spots; cross-domain failure modes become visible in the disagreement.

**Hallucinated detail.** Invented specifics go unchecked in single-agent settings. The verifiable-artifact requirement (D3) catches them at the Challenger level; the optional Verification Rule (a deployment-level discipline that requires agents to *read* an artifact rather than just cite it) catches the residual case where a path is cited that the agent never opened.

**Confidently wrong outputs.** Disagreement between two specialists is itself a signal that certainty is unwarranted. The both-must-agree convergence requirement (D4) prevents single-agent overconfidence from propagating as system-level certainty.

**Bandwagon contamination.** Single agents absorb user enthusiasm or prior conversational tone. The Challenger's role introduces a counter-pressure; the verifiable-artifact requirement constrains the Challenger from drifting toward agreement under social signal.

**Self-citation circularity.** A claim cited as evidence where the citation refers to the topic-owner's own working notes or specification. The citation is verifiable in the literal sense (the cited text exists and says what it claims) but not externally validated -- the same author authored both the claim and the evidence for it. Most likely to surface when the spar topic IS the framework itself, or when an agent's primary evidence base is the topic-owner's own materials. The verifiable-artifact requirement (D3) catches manufactured concerns but does not by itself distinguish externally-corroborated artifacts from author-internal ones; the operational defense is to tag claims grounded in the topic-owner's own materials as `internally consistent / externally unvalidated` rather than treating them as load-bearing. The structural fix is the empirical evaluation work proposed in Section 5.4; until that ships, the framework remains in `internally consistent, externally untested` territory and self-citation circularity is one specific symptom of that broader gap. *(Added v1.1: surfaced during a documented spar in which the Generator's load-bearing case for adding Discipline 6 to a derivative artifact rested on citing the framework's own Appendix D as evidence.)*

No single failure mode is the framework's sole target. The leverage from structured cross-evidence challenge applies across the whole list.

### 4.2 What the framework does not address

Three boundary cases the framework does not address:

**The model ceiling.** Multi-agent ceremonies do not produce outputs better than the underlying model is capable of; they reduce single-shot variance toward the ceiling by attenuating biases, blind spots, and pleasing drift. The framework's quality leverage is bounded above by the underlying model's capability. The trade is single-shot-ceiling-or-below for multi-shot-closer-to-ceiling, which is the right trade -- but it is a real bound. Ceiling-hit cannot be reliably detected during a run without ground truth; only symptoms are detectable (convergence reached without artifact citations, both roles producing identical reasoning shapes).

**Pure judgment-shaped questions** where there is no checkable evidence and no domain expert with disjoint sources. The framework's leverage is bounded by the availability of evidence. When both roles are reduced to opinion without artifacts, D3's verifiable-artifact requirement cannot fire and the framework reduces to weak prompting.

**Low-stakes, single-shot, or routine work.** The framework's structure adds cost without quality gain when the decision does not warrant the structure. Routine factual lookups, mechanical edits, and one-shot tasks fall in this category.

### 4.3 Recognizing applicability boundaries in deployment

The three boundaries above are real but unevenly detectable. A deployment should recognize and call out each one, varying the response by what is mechanically available.

Low-stakes, single-shot, or routine work is detectable at entry by topic shape. A pre-flight classifier can flag the topic and surface a warn-and-proceed prompt: "this looks routine; SPARRING will likely add cost without quality gain -- proceed anyway?" The check is a soft gate, not refusal.

Pure judgment-shaped questions are detectable at entry as a verifiable-artifact-channel check: would any artifact -- a fact, a source, a measurement, a concrete failure mode -- settle this question, even partially? If the honest answer is "no, it is pure preference," the deployment flags the topic and routes to a degraded mode (single-Challenger pressure-testing per the D2 fallback path) rather than spawning two correlated specialists pretending to disjoint evidence. Many topics admit partial artifacts (precedent, prior data) and degrade gracefully; full-preference questions are rarer than they look but real when they appear.

The model ceiling cannot be reliably detected at entry. Ceiling-hit only manifests in retrospect against ground truth the system does not have. What is detectable at run time are *symptoms*: convergence reached without artifact citation, both roles producing the same reasoning shape, an LLM-as-judge component (when present) flagging that the convergence reasoning is shallow. A deployment surfaces these as "ceiling-hit candidate" findings in the spar artifact and post-run report, where they can be reviewed against the converged decision -- not as entry gates, since false positives would block real work.

The general posture: warn-and-proceed for the two detectable-at-entry cases; instrument-and-surface for the in-run case. The framework does not prescribe specific thresholds; those are deployment-tuning decisions. The framework requires that the recognition behavior exist and produce visible signals to the receiving party.

---

## 5. Discussion

### 5.1 Conditionalities on the framework's quality leverage

The framework's strongest claim is that **carefully-designed multi-agent interactions with thoughtful specialization should produce significantly higher decision quality than single-agent or single-shot approaches on tasks where iterative pushback has meaningful leverage**. That claim carries three real conditionalities, each of which bounds the framework's applicability rather than invalidating it.

**Careful design is doing significant work.** Specialization gives leverage, not quality. Naive multi-agent with vague role definitions can be worse than single-agent. The framework's value scales with design quality. Persona authoring, evidence-base specification, and operational-rule precision are the load-bearing engineering work.

**Different ground truth compounds; same ground truth does not.** Two specialized agents with the same training data, the same evidence pool, and only different system prompts produce more correlated outputs than they appear to. The strongest specialization is when agents have access to *different evidence*. Persona-only specialization with shared evidence is real but smaller than the structural framing suggests.

**Quality is bounded by the model ceiling and by the evidence available to the agents.** Multi-agent reduces single-shot variance and bias; it does not transcend the underlying model. The framework reduces variance toward the ceiling rather than raising the ceiling.

These conditionalities are statements about *when* the multi-agent quality leverage is real and when it shrinks toward zero. They do not invalidate the framework; they bound its applicability to the cases where the conditions hold.

**Value and cost stated together.** The framework's value proposition must be stated alongside its cost. What it gives: structural defense against compounding failure modes in agent chains (cascading pleasing-bias, hallucinated detail propagation, anchored exploration, theatrical convergence); decision auditability through structured spar artifacts; operational vocabulary for distinguishing real challenge from theater; permission for the "no answer yet" outcome via the disagreement-at-cap response menu (Section 3.5); four Challenger functions across the loop's phases rather than the single stress-test function generic adversarial frameworks provide. What it costs: substantially more LLM calls per decision than single-agent paths; partner cognitive overhead for evidence-base specification and artifact review; misapplication risk if the Applicability Gate (Section 4.3) is not honored; unmeasured quality leverage pending the empirical evaluation in Section 5.4. The framework reduces *what the partner has to verify* by structuring the inputs, but does not remove the partner from the loop -- residual safety remains partner judgment of the surfaced artifacts. The cleanest one-line characterization: *the cheapest structural defense available against multi-agent compounding failures, paid for in tokens and partner attention, on a quality claim that is structurally argued but not yet empirically measured.* *(Added v1.1.)*

**Substrate independence.** The framework's claims are structural, not model-dependent. The structural commitments -- two roles with role-shifting Challenger function across four phases, disjoint evidence (D2), verifiable artifacts (D3), both-must-agree convergence (D4), the disagreement-at-cap response protocol -- are claims about how to organize multi-agent decision-making, not claims about a specific vendor, model family, generation, or capability tier. The "model ceiling" boundary in Section 4.2 is the operative limit: the framework cannot operate on a model too small to maintain persona discipline, ground citations reliably, or produce structured agreement signals. Within frontier-class models, the structural argument propagates. What changes when the substrate changes: per-spar cost (varies dramatically across vendors and tiers), magnitude of measured leverage (a higher capability ceiling means smaller marginal gain from variance reduction toward it), and the prominence of specific failure modes (different RLHF approaches leave different residual failure-mode signatures). What does not change: the structural argument, the nine disciplines, the disagreement-at-cap protocol, the verifiable-artifact and self-citation circularity rules, the applicability boundaries. Cross-model SPARRING (Section 3.4 Substrate variants) is the framework's deliberate design move addressing the residual coordinated-theater risk that same-substrate pairings carry by extending Discipline 2's disjointness one layer below evidence base to model substrate. **Falsifiability:** the substrate-independence claim is testable. Empirical evidence that, when the same SPARRING ceremony runs on different model families against the same decision corpus, leverage manifests *only* on a specific vendor or model class would be evidence the leverage was model-specific behavior rather than structural. The Phase D cross-model integration in the eval-harness roadmap is the direct test. Until that eval runs, substrate independence is a structurally-argued claim, not an empirically-validated one -- naming it explicitly is the discipline against pleasing-bias drift toward a "use Opus and these prompts" framing of what is positioned as a method. *(Added v1.1.)*

### 5.2 Theatrical adversariality and its mitigations

The framework's most subtle failure mode is *theatrical adversariality*: two agents producing manufactured disagreement that looks like cross-validation but contains no real challenge. Without measurement (D6), theatrical adversariality is not detectable from inside the loop -- agents producing correlated outputs while signaling disagreement appear, to a single-pass reader, to have engaged in real challenge.

Mitigations work in layers, in roughly increasing rigor and cost:

1. Anti-pattern prompting (cheap, weak): instructing agents to "find flaws."
2. Asymmetric prompting (cheap, slightly stronger): different success criteria per role.
3. Tool-grounded verification (medium cost, real strength): D3's verifiable-artifact requirement.
4. Domain-grounded specialization with disjoint evidence (medium-high, robust): D2's distinct-evidence requirement, which is the framework's load-bearing structural defense.
5. Multiple Challengers (medium-high, robust): the Multi-Challenger ensemble variant.
6. Eval harness with rubric scoring (high cost, strong): D6 and D7's measurement and observability requirements.
7. Architectural mitigations: holding out the Generator's identity from the Challenger so the Challenger sees the artifact, not the discussion that produced it.
8. Training-layer mitigations: RLHF and Constitutional AI work on sycophancy reduction at the model layer.

The framework specifies the structural mitigations (1-7) and recommends the model-layer choice (8) be informed by published research on sycophancy reduction. No single mitigation is sufficient; production deployments combine several.

**Residual case: self-citation circularity.** When the spar topic concerns the framework itself, agents may ground their evidence bases primarily in the framework's own working notes or this specification. Citations to those materials are verifiable but not externally validated -- the same author authored both the claim and the evidence. Mitigations 1-7 above are calibrated to detect manufactured disagreement *between agents*; they do not distinguish externally-corroborated citations from author-internal ones. The operational refinement: when an agent's primary evidence base is the topic-owner's own materials, the Challenger should tag load-bearing claims grounded in those materials as `internally consistent / externally unvalidated` rather than treating them as decisive. Closing this gap structurally requires the empirical validation work proposed in Section 5.4; until that ships, the operational tag is the residual safety. *(Added v1.1.)*

### 5.3 Honest critique applied to the framework itself

A specification that praised its own contribution would exhibit one of the failure modes the framework is built to prevent. PNP applied to the framework itself surfaces several concerns the contribution must acknowledge.

**The Cut is structurally inconsistent in graphical depictions** that carry an iteration count for it. The Cut is fundamentally a transition rather than a generative phase; depicting it as a peer to SPARK and PNP invites confusion about what it produces. This is a taxonomy issue rather than a fundamental flaw, but worth naming.

**The dialectic citation overreaches when the framework is described as "dialectic" without qualification.** The base loop is closer to Popperian falsification than Hegelian dialectic; synthesis appears only as a named response option at the disagreement-at-cap boundary. The framework is *Popper-style at the loop's base, with Hegelian thesis-antithesis-synthesis available at the boundary*. The fuller dialectic claim requires the iterative-synthesis future work named in Section 6. *(Update for v1.0: Section 2.3's framing was softened from "draws on three dialectic traditions" to "draws structural inspiration from" in response to this concern; the structural observation about Popperian base + Hegelian boundary stands.)*

**Multi-agent designs have their own failure modes** beyond the family the framework addresses: coordination overhead, handoff drift, integration of conflicting outputs, delayed convergence. A poorly-designed multi-agent system can produce *worse* results than a single well-designed agent. This is not a critique of the framework per se -- the framework is precisely a method for designing multi-agent systems that avoid these failure modes -- but the "carefully designed" qualifier in Section 5.1 is doing real work.

**Persona-only specialization is weaker than role-labels suggest.** Two agents with the same training data and the same source pool, given different role names, produce more correlated outputs than the role-label structure implies. D2's distinct-evidence requirement is exactly the response to this concern; it is the design problem the discipline was constructed to answer, not a residual flaw.

**Pleasing bias in the model weights remains a residual risk.** Specialization through a carefully-crafted system prompt can substantially constrain pleasing bias -- more than naive critique credits -- but the residual risk (the agent pleasing the user's *expectation of seeing structured challenge*) is harder to counter via prompt alone. This is the sharper version of the concern; D6 (measurability) is the framework's response, since meta-sycophancy is detectable at the rubric layer when training-layer defenses are insufficient.

**The highest-leverage Challenger is sometimes the human partner.** The framework treats Generator and Challenger as agent roles by default, but the role variants (Section 3.4) include Human-in-the-Generator and Human-in-the-Challenger configurations. The clarification: the human partner is highest-leverage on framing, premise, scope, and stance -- the broad judgment calls. Specialized agents are highest-leverage on domain-specific pressure-testing the human cannot do (e.g., biomechanical plausibility, security surface, implementation risk). Both are needed in different roles, not in competition.

### 5.4 Proposed empirical evaluation

The framework's quality leverage claim (Section 5.1) rests on structural argument supplemented by one production reference implementation [@niedner2026lifspel]. Empirical validation against established LLM-behavior benchmarks is open work. The most directly available evaluation, given the framework's named failure modes, adapts the preference-flip methodology of @sharma2023sycophancy to multi-agent settings.

**Baseline.** Run the Sharma 2023 preference-flip benchmark against a single agent: present a factual question with a known correct answer, prompt the agent with a user-stated opposing view, measure how often the agent flips its answer to align with the user. This is the established sycophancy measurement protocol.

**Treatment.** Run the same benchmark through a SPARRING ceremony (Generator + Challenger with disjoint Role + Domain layers per Discipline 2, both-must-agree convergence per Discipline 4, verifiable-artifact requirement per Discipline 3). Record the converged answer and measure its flip rate against the baseline.

**Headline metric.** Difference in flip rate between SPARRING-converged answers and single-agent answers on the same prompts. The framework predicts a meaningful reduction; the absence of that reduction would falsify the structural-defense claim against pleasing bias specifically (other failure modes from Section 4.1 would require their own evaluations).

**Secondary metrics.** (a) Agreement-signal calibration: does the Challenger withhold `agree: true` when the converged answer would have flipped under single-agent conditions? (b) Verifiable-artifact citation rate when the user-stated opposing view is unsubstantiated -- the Discipline 3 expectation is that Challenger concerns either cite an artifact disproving the user's view or honestly flag the absence. (c) Behavior under the Discipline 2 fallback: when distinct evidence cannot be articulated and the framework degrades to single-Challenger, does the flip rate revert toward baseline?

**Controlled confounds.** Model family across Generator and Challenger (held constant or varied per arm); presence and disjointness of evidence bases (single-evidence vs disjoint-evidence ablation); iteration cap; persona depth (full Role + Domain spec vs role-tag-only ablation, isolating the contribution of Discipline 2's depth requirement). The Reference Implementation's `.spar/config.toml` provides the configuration knobs; ablation runs are configuration changes rather than code changes.

**Known caveats.** This evaluation tests the framework's defense against pleasing bias as defined in @sharma2023sycophancy. It does not test defenses against the other failure modes named in Section 4.1 (confirmation bias, anchoring, hallucinated detail, etc.); those require their own targeted evaluations. The LLM-as-judge calibration concerns of @zheng2023judging and @liu2023geval (see also the discussion of measurability discipline in Section 3.3 D6) apply if any subjective scoring layer is added; the headline metric here is binary flip / no-flip and does not require LLM-as-judge.

This evaluation is the cheapest empirical test most directly motivated by the framework's own cited literature and is recommended as the first empirical work undertaken after framework adoption.

---

## 6. Limitations and future work

The framework as specified has known limitations that bound its current applicability and motivate future work.

**Iterative synthesis (true Hegelian dialectic).** Synthesis appears as one of five disagreement-at-cap response options, giving the framework a one-shot Hegelian extension at the cap boundary. True Hegelian iteration -- where each synthesis becomes a new thesis and gets antithesized in a fresh SPARRING round, with the cycle repeating until convergence -- is not the framework's default. Automating multi-pass synthesis loops, with governance for when to stop iterating, is a substantive extension that requires production data to define stopping criteria defensibly.

**N-specialist decisions** (multi-Generator). Genuine cross-domain decisions sometimes need three or more specialists who each generate proposals from their own evidence base, with a separate convergence layer. This is distinct from the Multi-Challenger ensemble (which keeps the Generator unitary) and requires governance for how N proposals converge -- unanimity, majority, weighted, etc.

**Cross-time pressure-testing.** A Challenger reviewing decisions made months ago against what is now known. The Watching-role Challenger applies the function to ongoing systems; cross-time extends it to retrospective review (post-mortems, periodic architecture audits, milestone closeouts).

**Calibration training.** Using Challenger feedback over time to improve Generator self-assessment. Essentially RL-from-Challenger-feedback applied to the Generator's predictions of where its work will fail. Requires a Phase-3-style eval harness to provide the feedback signal.

**Adversarial scenario generation.** Pointing SPARRING at existing plans or systems to generate realistic failure scenarios and edge cases. Different from in-loop pressure-testing because the target is an existing artifact rather than a decision being made now.

**Bias-aware Challenger lenses.** Specific Challenger configurations tuned to detect specific cognitive biases (anchoring, availability, framing, sunk cost, optimism) the Generator might exhibit. A library of pre-built Challenger personas, each specialized in a single bias-detection lens.

**Production validation.** The framework as specified rests on structural argument and one production reference implementation. Broader empirical validation across multiple deployments, decision types, and project shapes is required to establish the framework's quality leverage quantitatively. The empirical evaluation proposed in Section 5.4 (preference-flip adapted from @sharma2023sycophancy to multi-agent settings) is the cheapest first test directly motivated by the framework's own cited literature; broader validation across the failure modes from Section 4.1 remains required. The companion Reference Implementation document specifies a phased build sequence whose Phase 3 includes the eval-harness instrumentation that would generate this validation data.

---

## 7. Conclusion

This paper has presented the SPARRING Framework, a method for designing multi-agent decision-making systems that defends against the family of compounding LLM failure modes through structured cross-evidence challenge. The framework's contribution is structural: it specifies four phases, a two-role mechanic with role-shifting Challenger function, nine operational disciplines that prevent collapse to theatrical adversariality, an explicit applicability boundary with three cases the framework does not address, and a response protocol for unresolved disagreement.

The framework's claim is bounded by three conditionalities -- careful design, distinct evidence bases, and the model ceiling -- which describe when the multi-agent quality leverage is real and when it shrinks toward zero. The framework is not proposed as a default discipline for every prompt; it targets high-quality decision-making where iterative pushback produces quality benefits the single-shot path would miss.

A companion Reference Implementation document specifies a concrete CLI-based deployment of the framework, including agent topology, persona library structure (with the two-layer Role+Domain-mandatory plus Persona-optional model and the three-class persistent / returning / temporary lifecycle), schema specifications, and a phased build sequence from MVP through enterprise-scale deployment. The two documents together provide both the formal specification and the concrete reference architecture for adopters.

Production validation across multiple deployments and decision types remains future work. The framework's strongest current evidence is structural: the operational disciplines specified in Section 3.3 close concrete failure modes documented in Section 4.1, and the applicability boundary in Section 4.3 specifies what the framework does not claim. Deployments adopting the framework should treat the Section 5.1 conditionalities as load-bearing rather than aspirational, and the Section 5.3 critique as a baseline for honest internal evaluation of their own implementation.

---

## References

::: {#refs}
:::

---

## Appendix A: Glossary

**Agent.** A loop of LLM calls with access to tools, autonomous within bounds defined by its system prompt and tool set. Distinct from a single-shot prompt-response, which is not an agent under this definition.

**Agentic system.** A system composed of multiple agents that interact to produce outputs. Used interchangeably with multi-agent system (MAS) in current practice.

**Challenger.** One of the two roles in the SPARRING mechanic. Produces structured pressure-tests of the Generator's proposals with cited concerns. Function shifts per phase: expand (SPARK), filter (Pattern Lock), close (the Cut), stress-test (PNP).

**Compounding failure mode.** A failure mode that worsens across agent handoffs in a chain because each agent's output becomes the next agent's premise. Distinct from single-shot failure modes that are tractable at the individual-agent level.

**Convergence.** The state where both Generator and Challenger have signaled `agree: true`. Required for the SPARRING loop to terminate as resolved; absence of both-role agreement at iteration cap returns the unresolved question to the human or parent agent.

**Cut, the.** The transition phase between divergent generation (SPARK + Pattern Lock) and convergent evaluation (PNP). Structurally a mode shift rather than an iterative phase.

**Dialectic Surface.** Discipline 8. The asynchronous, persistent, shared, threadable communication channel where humans and agents exchange escalation, correction, and ongoing dialogue.

**Disagreement at cap.** The state where the iteration cap has been reached without both-role agreement. The framework's response protocol specifies five canonical responses for this case.

**Generator.** One of the two roles in the SPARRING mechanic. Produces proposals, framings, or evaluations of the topic. Reads the topic plus (in iteration rounds 2+) the Challenger's prior pressure-test.

**Iteration cap.** The maximum number of rounds the loop runs before disagreement returns to the human or parent agent. Defaults are starting points; the right cap is dynamic per topic.

**Pattern Lock.** The second phase of the SPARRING loop. False-novelty detection: identifying whether continued SPARK is producing genuinely new options or repetitive variants.

**PNP (Polite, Not Pleasing).** The fourth phase of the SPARRING loop. Convergent pressure-testing of the chosen option using cited evidence.

**Reference Record.** Discipline 9. The persistent, structured, auditable, searchable, curated record of decisions, spar artifacts, and accumulated institutional knowledge.

**Role + Domain Knowledge.** The mandatory layer of a persona: expertise scope, evidence-base scope, behavioral invariants, operational conventions, handoff authority. Specified in the companion Reference Implementation document.

**Persona layer.** The optional, lightweight layer of a persona: voice, tone, structural output conventions, optional anthropomorphization. Adopted per project fit; skippable when project context does not warrant it.

**SPARK.** The first phase of the SPARRING loop. Divergent generation of options, framings, or approaches.

**SPARRING.** The two-role pressure-testing mechanic that runs across all four phases. Generator and Challenger with role-shifting Challenger function.

**Spar artifact.** The structured record produced by every spar invocation. Records the topic, both roles with evidence bases, the iteration log, agreement signals, artifacts cited, and the converged result or unresolved disagreement. Required by D7.

**Theatrical adversariality.** The failure mode where two agents produce manufactured disagreement that looks like cross-validation but contains no real challenge. The framework's most subtle failure mode and the target of D3, D4, and D6.

---

## Appendix B: Notation

The framework as specified does not require formal mathematical notation. The following notation is used for precision in places:

- *Iteration round n* for the n-th round of the Generator-Challenger exchange within a phase.
- *Iteration cap k* for the maximum n at which the loop terminates if both-role agreement has not been signaled.
- *Convergence at round n* if both `agree: true` signals are produced at round n.
- *Disagreement at cap* if no convergence by round k.

The agreement signal `{agree: bool, reasoning: text}` is the framework's only required structured output beyond the proposal/pressure-test itself; everything else (artifact citations, spar artifact emission, eval rubric scoring) is a deployment-level concern specified in the Reference Implementation document.
