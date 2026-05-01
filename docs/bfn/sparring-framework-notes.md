# SPARRING Framework -- Working Notes

*Working-notes / scratchpad for Bart Niedner's SPARRING Framework (originally "SPARK/PNP," developed under ResourceForge), and the larger ongoing conversation around it -- including how it has been applied to the Lifspel agentic ecosystem.*

*This is a **living working document**, not a single-source-of-truth artifact. Mature content is extracted from this doc into standalone docs in `/docs/bfn/`. See "Documentation workflow" below.*

*Last updated: 2026-05-01 (rev. 20)*
*Status: **five integrations adopted 2026-04-28**. The full SPARRING ceremony (`/sparring`) is deferred pending a brainstorm on theatrical-adversariality risk. See "Adoption status" below.*

## Documentation workflow

The `/docs/bfn/` namespace follows a deliberate scratchpad-to-mature pattern:

- **This file (`sparring-framework-notes.md`)** is the working notes / scratchpad. The full ongoing conversation about the SPARRING Framework lives here -- brainstorming, revisions, alternative framings, applied history, and any in-flight thinking. Treat the *most recent* revision as the current best read on any given subtopic; treat earlier revisions as history.
- **Standalone documents in `/docs/bfn/`** are the single source of truth for any topic that has matured enough to extract. When a section here stabilizes, it is moved into its own document, and this file replaces it with a link stub. From that point forward, the standalone document is canonical -- if the working notes and the standalone document conflict, the standalone document wins, and the working notes should be updated (or the conflict resolved into the standalone).
- **Extraction discipline:** when a topic is extracted, leave a one-paragraph link stub in this doc pointing to the standalone, so the working notes remain navigable as a single coherent artifact. Don't silently delete; don't keep duplicating content in both places.

### External documents (extracted from these notes)

| Document | Topic | Extracted on |
|---|---|---|
| [`sparring-reference-deployment.md`](sparring-reference-deployment.md) | The CLI-based reference deployment of the SPARRING Framework -- architectural spec, what to build | 2026-04-29 (from rev. 17 of these notes) |
| [`sparring-deployment-walkthrough.md`](sparring-deployment-walkthrough.md) | Staged getting-started guide for deploying SPARRING -- opinionated path through one common project shape, how to actually do this | 2026-04-30 (during the Phase 1 v1 release pass) |

(More to come as topics mature -- the full framework Overview, the Lifspel adoption history, and the appendices on agent fundamentals, multi-agent terminology, deployment patterns, and theatrical-adversariality mitigations are all candidates for future extraction.)

### Sibling working drafts

Working drafts that are NOT extracted from these notes -- they cover adjacent topics with their own scope, but follow the same scratchpad-to-mature pattern. Each is the load-bearing source for its own downstream extractions (LinkedIn copy, formal documents, etc.).

| Document | Status | Topic |
|---|---|---|
| [`sparring-and-decision-frameworks.md`](sparring-and-decision-frameworks.md) | WORKING DRAFT | SPARRING in relation to existing decision frameworks. First comparison (Torres / Continuous Discovery Habits / OST) sketched 2026-05-01; further frameworks (Lean Startup, premortems, ADRs, design thinking, RAT) deferred. Source for downstream LinkedIn posts and an eventual v1.1 professional-distribution companion document. |

---

*Revision history:*
*- 2026-05-01 r20 -- documentation workflow expanded to handle sibling working drafts (not just extracted standalones). New "Sibling working drafts" subsection added under "Documentation workflow," with first entry: `sparring-and-decision-frameworks.md`, the staging ground for cross-framework comparisons. First comparison (Torres / Continuous Discovery Habits / OST) sketched, originating from an external suggestion that the SPARRING Framework rhymes structurally with Torres's product-discovery work. The new working draft is the source for downstream LinkedIn posts (planned, multiple) and an eventual v1.1 professional-distribution companion document (deferred until multiple framework comparisons stabilize). Header bumped from rev. 18 to rev. 20 to fix prior stale revision-line bug (r19 had updated the body but not the "Last updated" line).*
*- 2026-04-30 r19 -- v1 release pass on the deployment docs. (1) Pleasing-bias rebalance across 8 places in these working notes: the framework no longer pins on pleasing-bias as primary purpose; pleasing-bias is named as the most-cited member of a family of compounding LLM failure modes (sycophancy, confirmation bias, anchoring, misread questions, specialization blind spots, hallucinated detail, confidently-wrong outputs, bandwagon contamination). (2) Added "Recognizing these situations in a deployment" subsection in the Overview, naming the Applicability Gate behavior for routine work, pure-judgment topics, and ceiling-hit symptoms. (3) Added "Disagreement-at-cap response protocol" subsection with five canonical responses (pick-a-side-with-tradeoffs, defer, reframe, escalate, synthesize) plus non-canonical acknowledgment; clarified Popperian base + Hegelian extension at the cap boundary. (4) Sharpened Discipline 2 to name the Role + Domain Knowledge layer (mandatory) vs Persona layer (optional, lightweight) distinction explicitly; persona-only specialization (manufactured tonal contrast) does not satisfy Discipline 2. (5) Coherence pass on Part 3 with structural-resolution Status notes per surviving concern; Future Extensions Synthesis entry rewritten to reflect partial shipping (one of five disagreement-at-cap responses) with iterative synthesis as the remaining ambition; Inspirations citation sharpened (Popper-style falsification at base + Hegelian thesis-antithesis-synthesis at the boundary). (6) Reference deployment doc gained: "What this deployment defends against" section, Applicability Gate component, Disagreement-at-cap menu surfacing in iteration controller and artifact emitter, Lessons-from-Lifspel section with three subsections (Role+Domain mandatory + Persona lightweight optional layer model with bucket-labeled do/don't examples; Verification discipline beyond artifact-citation; Partner-in-the-loop as first-class workspace participant), three-class persona lifecycle (persistent / returning / temporary) with full lifecycle structure section. (7) Walkthrough doc created -- staged getting-started guide for small-team Phase 1 deployments, sibling to the reference doc. Includes PNP'd Anthropic Claude Agent SDK recommendation (Sharma et al. 2023 sycophancy research; recent Claude versions target sycophancy reduction; operational fit; defense-in-depth not replacement; revisit annually). Lifspel and LineMind Novelist worked examples. End-to-end alignment audits ran twice during the pass; cross-doc alignment confirmed at session close.*
*- 2026-04-29 r18 -- restructured working notes for documentation workflow. Moved file from `docs/reference/spark-pnp-and-sfxls.md` to `docs/bfn/sparring-framework-notes.md` and renamed H1 to clarify it is working notes, not a marketing artifact. Added "Documentation workflow" section + "External documents" table at the top. Extracted the Reference Deployment section into standalone `docs/bfn/sparring-reference-deployment.md` (sourced from rev. 17 content). Replaced the Reference Deployment section body with a one-paragraph link stub. The 14 inbound references (CLAUDE.md, five `/`-skills, marcus-agent.js, three agent personas, challenger-output-rubric.md) updated to point at the new path.*
*- 2026-04-28 r1 -- initial concept doc + brainstorm.*
*- 2026-04-28 r2 -- post-adoption: Part 3 critique revised after Bart clarified the framework's intended scope (high-quality decisions, not every prompt), iteration-count semantics (dynamic / task-driven, not fixed), and that quality is the metric, not token cost. Added Appendix A on agent fundamentals (what is an agent, sub-agent, specialization) as foundational background.*
*- 2026-04-28 r3 -- integrated Part 3 + Part 5 sharpening: concrete Idris-Lena evidence-disjoint example as the canonical illustration of the ground-truth conditionality; sharper model-ceiling phrasing; forward-looking note in Part 5 deferred section setting the focus for the future `/sparring` brainstorm.*
*- 2026-04-28 r4 -- added Appendix B on multi-agent terminology (MAS as umbrella term, orchestration vs choreography distinction, vocabulary fragmentation in the LLM-agent field, mapping of the StoryForge ecosystem to industry-standard patterns).*
*- 2026-04-28 r5 -- added Appendix C on agent deployment patterns: the eight architectural patterns dominating the LLM-agent field, the framework brand names that embody each, where Lifspel sits in the landscape (a deliberate hybrid), what is distinctive about Lifspel, and what it lacks vs. mature industry practice with priority ranking for what to add first.*
*- 2026-04-28 r6 -- revised the Part 5 forward-looking note for the deferred `/sparring` brainstorm. Earlier framing defaulted to "partner gates at phase transitions" as a safety mechanism, which conflicts with the autonomy goal. Replaced with: orchestrated escalation paths for high-stakes-by-design phases + choreographed escalation criteria encoded in each agent's system prompt for agent-determined cases. Separated the transition-declaration-reliability concern (handled by iteration caps, ground-truth-exhaustion signals, or inter-agent agreement) from the partner-involvement question. Notes residual concerns: LLM self-assessed uncertainty is unreliable; theatrical adversariality can fire without the agents detecting it.*
*- 2026-04-28 r7 -- added Appendix D on theatrical-adversariality mitigation: the eight-pattern tool kit (anti-pattern prompting, asymmetric prompting, tool-grounded verification, domain-grounded specialization, multiple Challengers, eval harness with rubric scoring, architectural mitigations, training-layer), what is well-solved vs. not, and the Lifspel mapping table showing where the architecture is strong (#1, #4, #8, partial #3) and weak (#2, #5, #6, #7).*
*- 2026-04-28 r8 -- extended Appendix D #3 (tool-grounded verification) to claim-of-concern across all five Challenger surfaces (`/pnp`, `/act-as-agent` Idris-Lena Challenger prompt, `/plan-review --pnp` Challenger prompt, `/thread-assessment` close-decision PNP, Marcus's self-PNP). Verifiable-artifact requirement: every concern must cite a specific file/line, source citation, test/commit, or concrete edge case; concerns without an artifact are dismissed as theatrical adversariality. Also added two named candidates for the remaining eval-harness leverage move: divergent-domain dual judges, and on-the-fly specialized agents with genuinely-different-evidence injection routed by a detection orchestrator.*
*- 2026-04-28 r9 -- added Appendix D "Implementation roadmap" section with Phase 1 / Phase 2 / Phase 3 staging for the eval-harness work. Phase 1 (rubric + partner sampling) shipped concurrently as `docs/standards/challenger-output-rubric.md`. Phase 2 (smoke-test corpus + before/after testing) and Phase 3 (automated scoring, divergent-domain dual judges, on-the-fly specialists with detection orchestrator) documented as deferred with the discipline that prevents premature investment.*
*- 2026-04-28 r10 -- shipped `/spar [iterations] <topic>` skill (`.claude/skills/spar/SKILL.md`) on best-guess assumptions given project-timeline pressure that does not support full Phase 1 measurement first. Shipped with three design tightenings: (a) persona generation requires explicit evidence-base specification with single-Challenger fallback when distinct sources cannot be articulated; (b) convergence requires both agents to emit explicit `agree: true` signals (not Generator's self-judgment alone); (c) self-invocation triggers documented as observable conditions, not LLM-self-assessed uncertainty. Iterations default to 2 if omitted; minimum 1. Scope today: partner-invokable and Claude-Code-invokable. Agent self-invocation deferred (requires SDK-level work to add Agent tool to automated agents' kits, or marker-based orchestration).*
*- 2026-04-28 r11 -- enabled in-loop self-sparring for **Marcus only** (`scripts/tasks/agent-reactions/marcus-agent.js`). Adds the SDK's `Task` tool to Marcus's allowed-tools list (production runs only -- dry-run mode unaffected) and defines two sub-agent templates (`spar-generator` and `spar-challenger`) in the `agents` parameter. New SELF-SPAR DISCIPLINE rule in Marcus's system prompt with the same three tightenings as the `/spar` skill (evidence-base distinction, both-must-agree, observable triggers) plus a 1-iteration cap to bound cost. Sub-agents run on Sonnet for cost control. Documented as Operating Principle 5 in `docs/agents/marcus-kowalski.md`. Other automated agents (Diane, Zoe, Dani, Lena, Idris, Promise Verifier) NOT given Task tool -- the analysis showed only Marcus's auto-fix workflow has the substantive-decision shape that benefits; broader rollout deferred until evidence shows it pays back.*
*- 2026-04-28 r12 -- doc body updates to reflect everything shipped since the original 2026-04-28 partner Cut: added "Subsequent additions" subsection in Part 5 listing post-Cut additions in chronological order; rewrote the "What is deferred" entry to acknowledge that `/spar` (rev. 10) and Marcus self-spar (rev. 11) operationalized parts of the originally-deferred `/sparring` brainstorm; added "Shape analysis: which agents benefit from in-loop sparring" subsection to Appendix D's implementation roadmap; added a brief "Implementation caveats" note about SDK tool-name uncertainty (`Task` vs `Agent`).*
*- 2026-04-29 r13 -- added top-level standalone "Overview" section (right after frontmatter, before Part 1) with a comprehensive plain-language description suitable for marketing use. Integrates: marketing name (SPARRING Framework), iteration defaults with dynamic-adjustment caveat, Challenger model-question per phase, comprehensive observable-triggers list categorized by Stakes / Complexity / Confidence / Authority / Domain fit, two new disciplines (Measurability, Observability) added as #6 and #7, broader failure-mode coverage beyond sycophancy (confirmation bias, anchoring, misreading, blind spots, hallucinated detail, confidently-wrong outputs, bandwagon effects), future-extension directions (N-specialist decisions, cross-time pressure-testing, continuous monitoring, hypothesis generation mode, synthesis, calibration training, adversarial scenario generation, bias-aware lenses), and the parent-agent edit to discipline #4 (disagreement returns to the human OR parent agent, preserving the autonomy framing).*
*- 2026-04-29 r14 -- expanded the Overview section to integrate the variants/operational-patterns and an additional discipline that had been latent in the framework. Added a new "Variants and operational patterns" section between SPARRING and the disciplines, with three subsections: phase-isolation modes (SPARK alone for hypothesis generation; Pattern Lock alone as ideation-hygiene tool; PNP alone for single-pass pressure-test); role variants (Human-in-the-Generator with AI Challenger; AI-Generator with Human-in-the-Challenger; Multi-Challenger ensemble per phase; Watching-role Challenger for ongoing systems); deployment patterns (domain-specific SPARRING templates; pre-emptive SPARRING for decision archives). Added Discipline 8 "Dialectic Surface" -- the persistent, shared, asynchronous, threadable, searchable, auditable, equipotent communication surface where humans and agents interact over time (escalation / correction / reference). Implementation tool-agnostic (message board, chat platform with thread discipline, issue tracker, wiki, shared document) -- the discipline is "what must exist," not "which tool." Restructured Future Extensions to reflect what moved into base: removed continuous monitoring (became Watching-role Challenger in Variants) and hypothesis generation (became phase-isolation in Variants); kept N-specialist decisions, cross-time pressure-testing, synthesis (true dialectic), calibration training, adversarial scenario generation, bias-aware Challenger lenses.*
*- 2026-04-29 r15 -- split rev. 14's Discipline 8 into two disciplines after partner PNP-ed the conflation. The previous Discipline 8 mixed two functionally distinct things: active dialogue (escalation, correction, ongoing turn-taking) and reference record (persistent curated material for retrieval long after the fact). The two functions differ on volume / signal density, curation requirements, write privileges, revision norms, decay curves, and audience -- mixing them on one surface is a real failure mode (active chatter drowns reference signal; curation discipline kills active dialogue). Now: Discipline 8 "Dialectic Surface" covers active communication only; new Discipline 9 "Reference Record" covers persistent curated retrieval. Both required. Scale-dependent note explicitly added: small deployments can serve both with one physical surface plus discipline (e.g., the Lifspel Round Table uses thread resolution states); at scale, separation usually becomes necessary. "The eight disciplines" header updated to "The nine disciplines"; one-sentence version updated to reflect both surfaces.*
*- 2026-04-29 r16 -- added a new top-level "Reference Deployment" section between the Overview and Part 1, describing what to build to deploy the SPARRING Framework as fully aligned as possible for general business use from a CLI. Covers: discipline-to-component mapping (every one of the nine disciplines to a concrete build component); architecture (entry point, agent runtime, persona library, evidence-base resolver, iteration controller, artifact emitter, persistence, dialectic surface integration, eval harness, trigger registry); CLI surface (concrete `spar` commands modeled after git/kubectl); configuration and data model (`.spar/` directory layout and spar artifact schema); four integration adapters (agent SDK, persistence, dialectic surface, reference record); variant support (CLI flags and commands per Variant); phased build sequence (Phase 1 MVP, Phase 2 maturity, Phase 3 enterprise); honest tradeoffs (evidence-base specification is the hardest problem; persona generation second-hardest; convergence-quality requires measurement); general business considerations (cost, privacy, multi-user, versioning, auditability); and the explicit framing that this is one valid reference deployment, not the only one.*
*- 2026-04-29 r17 -- added "Agent topology" subsection in Reference Deployment, between Architecture and CLI surface. Specifies what agents actually run when CLI commands fire: Generator and Challenger always (per-round, sequential, alternating); N Challengers when multi-challenger variant invoked; human-in-the-Generator or human-in-the-Challenger per role variant; persona/evidence resolver as Phase 2 infrastructure; watching-role Challenger as long-running daemon for ongoing systems; LLM-as-judge as Phase 3 eval-harness automation. Names the code-orchestrator vs agent-orchestrator architectural choice (code-orchestrator recommended as default for determinism / cost / debuggability; agent-orchestrator available for advanced reasoning-shaped orchestration). Per-agent specs: model, system prompt sketch, tools, input, output, lifespan, concurrency. Summary table and concrete cost picture for a typical 2-iteration default run (2-4 LLM calls per spar in baseline mode).*

---

## Overview: The SPARRING Framework

*Plain-language description suitable for partner communication, ResourceForge content, and external presentation. Standalone -- readable without reference to other parts of this doc. For technical detail and Lifspel-specific implementation, see Parts 2-5 below and Appendices A-D.*

The **SPARRING Framework** is a method for designing multi-agent decision-making systems that pair two specialists with genuinely different evidence in a structured pressure-test against the family of compounding LLM failure modes -- pleasing bias, hallucinated detail, specialization blind spots, misread questions, confidently-wrong outputs, bandwagon contamination -- with explicit operational disciplines that prevent the structure from collapsing into theater.

### The problem it solves

AI agents trained to be helpful tend to agree too easily. In a single conversation, that's manageable. In a chain of agents handing outputs to each other, one agent's softened "yes" becomes the next agent's premise, and the system reinforces its own assumptions while sounding thoughtful. The SPARRING Framework exists to prevent that compounding by building structured disagreement into the workflow.

### The four phases

A decision moves through four functions, each with a default iteration cap. The defaults are starting points -- the right cap is dynamic, adjustable per topic, and the *ability* to iterate matters more than the specific count.

1. **SPARK** (~5 iterations) -- Open the space. Generate as many possibilities, framings, and angles as the question can support. The goal is to avoid locking in too early. *Challenger's question: "What else could this be?"*

2. **Pattern Lock** (~2 iterations) -- Detect when generation has stopped producing real novelty. The framework's most overlooked move: new ideas often *look* new but turn out to be rephrasings of earlier ones. Pattern Lock names the moment to stop expanding without prematurely closing. *Challenger's question: "Are these actually different?"*

3. **The Cut** (~2 iterations) -- Transition from exploration to evaluation. Stop generating; commit to evaluating what you have. Structurally a transition rather than a generative phase, but it earns its name because the move from exploration to evaluation is where most decision processes get stuck. *Challenger's question: "Is more generation still useful?"*

4. **PNP** -- Polite, Not Pleasing (~3 iterations) -- Pressure-test the strongest option for weaknesses, edge cases, and hidden assumptions. Politeness governs *how* concerns are raised; the substance never softens to make anyone feel better about a weak answer. *Challenger's question: "Does this actually hold up?"*

The iteration cap is a mechanical fallback. When agents don't converge within the cap, the unresolved question returns to the human or parent agent -- that's information, not failure. The receiving party has five named response options at that handoff (pick-a-side-with-tradeoffs, defer, reframe, escalate, synthesize); see "Disagreement-at-cap response protocol" below.

### SPARRING -- the operating mechanic

Two roles persist through all four phases: a **Generator** that produces work and a **Challenger** that pressure-tests it. The Challenger's *function* shifts per phase -- pushing for breadth in SPARK, calling out repetition in Pattern Lock, forcing convergence at The Cut, stress-testing the chosen option in PNP. Same two roles, four different jobs. That role-shift is the framework's sharpest contribution; standard adversarial setups (red team / blue team, GAN-style) give you one Challenger function, not four.

### Variants and operational patterns

The default ceremony is two-agent, four-phase, full-loop. Practical use surfaces variants that share the framework's discipline without running every phase or both roles every time.

**Phase-isolation modes** -- using a single phase outside the full loop:

- **SPARK alone (hypothesis generation mode).** Pure ideation. Strip the loop to its first phase: generate as many possibilities as the question can support, no convergence requirement. Useful early in a problem space where the goal is breadth, not decision.
- **Pattern Lock alone (ideation-hygiene tool).** Apply Pattern Lock to a brainstorm, a meeting transcript, a backlog: flag where novelty has stopped and ideas are rephrasings of earlier ones. Decoupled from the four-phase loop. Useful for individual reviewers or teams running their own brainstorms.
- **PNP alone (single-pass pressure-test).** One pressure-test pass on an existing proposal. Lighter than full SPARRING; appropriate when the topic has already converged and only validation remains. (The Lifspel `/pnp` skill is exactly this -- single-reply self-pressure-test by Claude Code.)

**Role variants** -- restructured pairings of who plays which role:

- **Human-in-the-Generator with AI Challenger.** Human proposes, AI Challenger pressure-tests. The standard "AI as second pair of eyes" pattern, but with the SPARRING discipline (verifiable artifacts, evidence-base specification, both-must-agree convergence). Useful when the human has irreplaceable context but wants the structured challenge.
- **AI-Generator with Human-in-the-Challenger.** AI proposes, human pressure-tests. AI does divergent generation; human does the substantive challenge. Useful when AI has breadth advantage but the human's judgment carries the decision.
- **Multi-Challenger ensemble per phase.** Single Generator, multiple Challengers each surfacing concerns from their own evidence base. Convergence requires all Challengers to agree, OR the framework reports the dimension-specific disagreement explicitly. Cross-validates concerns and catches blind spots in any single Challenger. Distinct from N-specialist decisions (in Future Extensions) -- this keeps the Generator unitary; that splits the Generator role too.
- **Watching-role Challenger for ongoing systems.** Apply the Challenger function to *existing* decisions (deployments, architectural choices, ongoing systems) by flagging when a previously-converged decision's premises no longer hold. The classic case: a deployment that worked when the system was small now scales poorly; the watching Challenger notices the changed conditions before failure forces the issue.

**Deployment patterns** -- operational shapes:

- **Domain-specific SPARRING templates.** Pre-built configurations for common decision types (security review, code review, plan review, design review, hire decision, vendor selection). Each template includes recommended persona pairings, evidence-base specifications, and Challenger questions tuned to the domain. Reduces per-spar setup cost and produces consistency across recurring decision types.
- **Pre-emptive SPARRING for decision archives.** Run SPARRING on decisions before they're acutely needed. Build a structured archive of "we considered X and resolved it this way, with these tradeoffs" -- essentially Architecture Decision Records (ADRs) generated through structured SPARRING rather than ad-hoc documentation. When live decisions arise, the archive provides reference points; prior spar artifacts also feed the Observability discipline.

These variants share the nine disciplines below. They differ in *which* phases run, *who* plays which role, and *what* operational shape the ceremony takes -- not in the underlying disciplines.

### The nine disciplines that make it work

Without these operational disciplines, the framework collapses into theater:

1. **Apply it to decisions, not every prompt.** SPARRING is for high-quality decision-making where iterative pushback genuinely adds value. Factual lookups, mechanical edits, and routine questions don't need it.

2. **Generator and Challenger must draw on genuinely different evidence.** Two agents with the same training data and the same source pool, given different role labels, will produce more correlated outputs than they appear to. The strongest specialization is when each role has access to evidence the other doesn't -- different domains, different data sources, different tools. Persona-only specialization (different voice, different tone, different framing) is weaker than it looks. The framework requires distinct **Role + Domain Knowledge** between roles -- distinct expertise, distinct evidence-base scope, distinct operational rules. The **Persona layer** (voice, style, presentation) is optional and need not differ between Generator and Challenger; manufacturing tonal contrast across roles does not satisfy Discipline 2.

3. **Concerns must cite verifiable evidence.** Every challenge raised must point to a specific artifact -- a fact, a source, a measurement, a concrete failure mode. Vague concerns get dismissed as theater. If an honest concern exists but no artifact is yet citable, the Challenger says so explicitly rather than pretending or suppressing.

4. **Both roles must agree to converge.** Generator declaring "we're done" alone is structurally suspect -- it would reintroduce pleasing-bias drift toward the user's apparent expectation of completion, one of several failure modes the two-role structure exists to defeat. Convergence requires explicit agreement from both. **Disagreement at iteration cap returns the unresolved question to the human or parent agent** -- that's information, not failure.

5. **Self-invocation triggers must be observable.** Agents are bad at assessing their own uncertainty. When an agent decides "this needs sparring," the trigger should be a concrete observable condition, not a feeling.

6. **Measurability.** Without some way to validate that the structured challenge is producing real challenge -- versus longer outputs that look thoughtful -- you're flying blind. The minimum viable form is rubric-scored sampling of Challenger outputs on a 5-point scale with per-criterion anchored point descriptions (criteria like artifact citation, substantive vs. theatrical, missed concerns, calibrated agreement). The mature form is automated rubric scoring with held-out test cases. Quality claims are testable; without testing, they're aspirational.

7. **Observability.** Every spar produces a structured artifact recording: the topic, the two personas with their evidence bases, the iteration log, both agents' agreement signals, the artifacts cited, the converged result or the unresolved disagreement. The artifact is human-readable, machine-parseable, and persists. It serves three functions -- audit (was the decision made well?), debugging (when something went wrong, what was the reasoning?), and measurement input (the artifacts feed Discipline 6).

8. **Dialectic Surface (active communication).** The framework requires an asynchronous communication channel where humans and agents exchange escalation requests, corrections, and ongoing dialogue. Optimized for fluid turn-taking and equipotent participation between current actors.

   Two primary functions:
   - **Escalation** -- an agent flags something to a human or parent agent for asynchronous response without blocking the agent's loop.
   - **Correction** -- a human or another agent posts feedback that subsequent agents read and incorporate without losing the original context.

   Discipline 5 (observable triggers for self-invocation) presupposes this surface; without it, escalation has no asynchronous channel.

   What makes a surface a dialectic surface (abstract requirements, tool-agnostic):
   - **Persistent** within the active-communication time horizon -- posts don't evaporate when a session ends.
   - **Shared** -- humans and agents have equivalent read/write access.
   - **Asynchronous** -- participants don't need to be present simultaneously.
   - **Threadable** -- turn-taking is organized; conversations can be referenced.
   - **Equipotent for participants** -- humans and agents are first-class on the surface; neither is a second-class citizen.

   Less weighted on this surface: long-term auditability, deep search across years of content, and curation discipline -- those concerns belong to Discipline 9.

   Implementation examples: chat platforms with thread discipline (Slack, Discord, Teams), message boards (the Lifspel Round Table), issue trackers (GitHub Issues, Linear, Jira), email lists with structured threading.

9. **Reference Record (persistent curated record).** The framework requires a persistent, structured record of decisions, spar artifacts, resolved questions, and accumulated institutional knowledge. Optimized for retrieval long after the fact, by audiences who weren't part of the original conversation.

   Disciplines 6 (measurability) and 7 (observability) presuppose this surface; without it, validation data and spar artifacts have nowhere durable to live and the framework's outputs are session-bound rather than accumulated knowledge.

   What makes a surface a reference record (abstract requirements, tool-agnostic):
   - **Persistent** for the long term -- beyond the active-communication horizon, into months and years.
   - **Structured** -- consistent format that supports retrieval.
   - **Auditable** -- changes are traceable; corrections preserve history rather than overwriting it.
   - **Searchable** -- past content is retrievable when relevant questions recur.
   - **Curated** -- entries meet a discipline of correctness and completeness; what enters the canonical record matters. Higher write discipline than the dialectic surface.

   Less weighted on this surface: equipotent open participation -- the reference record is mostly read; writing to it carries higher curation requirements than open posting on the dialectic surface.

   Implementation examples: wikis (Confluence, Notion, MediaWiki), structured docs in a repo, Architecture Decision Records, post-mortem archives, dedicated reference databases.

   **Scale-dependent surface separation.** The framework requires both functions (Discipline 8 + Discipline 9) be served, not necessarily on separate physical surfaces. In small deployments -- like the Lifspel Round Table -- a single physical surface can host both with discipline (e.g., open threads as active dialogue; resolved/archived threads with curated content as reference). At larger scale, separation usually becomes necessary because (a) active chatter drowns reference signal at retrieval time, (b) curation discipline kills active dialogue, and (c) revision norms conflict (active communication preserves what-was-said-when; reference revises for correctness). Whether implemented as two surfaces or one-with-conventions is a deployment decision; the framework's discipline is that both functions are served and their conflicting demands are explicitly managed.

### Disagreement-at-cap response protocol

When the iteration cap is reached without both roles signaling agreement, Discipline 4 returns the unresolved question to the human or parent agent. That handoff is information, not failure -- but the receiving party has multiple legitimate responses, and naming them prevents the rarer ones from being skipped. The five canonical responses:

1. **Pick a side with explicit tradeoffs.** Accept the Generator's proposal OR the Challenger's strongest counter, documenting what the other side surfaced as a known and accepted cost. Often the right move when the disagreement is real but the costs of one direction are demonstrably more acceptable than the costs of the other.

2. **Defer the decision.** The inconclusiveness IS the answer: not enough evidence yet, decide later. Schedule a follow-up trigger (a date, a milestone, an event that will surface more evidence) and let the disagreement remain explicitly open. Often the right move when the cost of premature commitment exceeds the cost of waiting.

3. **Reframe the question.** The loop revealed that the question itself was malformed -- two valid evidence bases pointed at different problems, not the same problem with different answers. Restart with a sharper question. Often signaled by both roles raising legitimate concerns that don't actually conflict, just aim past each other.

4. **Escalate.** This decision needs more stakeholders than the original human, or a stakeholder with different authority (legal, security, partner-level). The disagreement may be tractable, but not at this level. Often signaled by one role citing constraints the original human cannot adjudicate.

5. **Synthesize.** Produce a third option neither role proposed, integrating insights from both. The most ambitious response and the most likely to be skipped because it is hardest. Real synthesis is structurally distinct from compromise: compromise averages the two positions; synthesis transcends both by integrating their evidence into a position neither alone reached. The discipline here is recognizing the difference -- if the proposed third option is just the midpoint between Generator and Challenger, it is not synthesis.

These are canonical, not exhaustive. If the situation warrants a response not on the list, take it -- the menu's purpose is to make the rarer moves visible, not to constrain the receiving party's judgment.

A note on synthesis specifically: a one-shot synthesis at the disagreement-at-cap boundary is a hybrid of Popper-style falsification (the framework's base shape -- propose, try to falsify, keep what survives) and Hegelian dialectic (thesis-antithesis-synthesis, where synthesis becomes the next thesis and gets antithesized in turn). True Hegelian iteration would feed the synthesis back into a fresh SPARRING round to test it the same way the original proposal was tested. That second-pass option is available -- "synthesize, then re-spar the synthesis" -- but is not the default because it doubles cost for a relatively rare class of decision. When stakes are very high and the synthesis itself contains novel claims, the second pass is worth considering.

### Observable triggers for self-invocation

A SPARRING-capable agent invokes the ceremony when one or more of these observable conditions fires. The discipline is using the conditions, not the agent's self-reported uncertainty.

**Stakes (cost of being wrong):**
- Decision affects irreversible commitments (deployments, sent communications, deleted data).
- Decision touches the production system or critical path.
- Substantial resource commitment (cost, time, headcount).

**Complexity (multi-dimensional):**
- Decision spans multiple domains (e.g., security AND code AND UX).
- Decision conflicts with documented standards or principles.
- Decision sets a long-lived precedent that affects future work.

**Confidence (epistemic):**
- Current evidence is contested or low-confidence.
- Decision is novel -- no clear precedent in the agent's history.
- Decision is being made under time pressure where the safety net matters.
- Multiple stakeholders have known conflicting preferences.

**Authority (escalation):**
- A human has explicitly flagged the topic with "spar before deciding" or equivalent.
- Decision touches a domain where the agent has had documented errors before.
- Decision would override an earlier human-made decision.

**Domain fit (specialization):**
- Decision touches a domain outside the agent's standing expertise.
- Multiple plausible approaches exist with real downstream impact.
- Decision involves a tradeoff between two named values (speed vs. quality, security vs. ergonomics).

The unifying property: each is something detectable from outside the agent without asking the agent how confident it feels. The list is not exhaustive -- it should be extended per the deploying organization's context.

### Failure modes the framework addresses

Structured challenge with disjoint evidence defends against a family of single-perspective failure modes that compound when agents hand outputs to each other:

- **Sycophancy / pleasing bias** -- agents agreeing too easily, especially under social or framing pressure; the most-cited LLM failure mode in the literature, but one item in the family rather than its center.
- **Confirmation bias** -- a single agent interprets ambiguous evidence in line with what it expects to see; two agents with different evidence have different priors.
- **Anchoring** -- once a first answer is generated, subsequent thinking gravitates toward refining rather than reconsidering.
- **Misreading the question** -- a single-prompt-interpretation error propagates downstream; two interpretations are more likely to surface a misread.
- **Specialization blind spots** -- single specialists miss cross-domain risks; different-evidence specialists have different blind spots, so cross-domain failure modes become visible.
- **Hallucinated detail** -- invented specifics go unchecked in single-agent settings; the verifiable-artifact requirement catches them.
- **Confidently wrong outputs** -- disagreement between two specialists is itself a signal that certainty is unwarranted.
- **Bandwagon / social-signal contamination** -- single agents absorb user enthusiasm or prior conversational tone; the Challenger introduces a counter-pressure.

No single failure mode is the framework's sole target. Pleasing-bias compounding is the most easily named and most often referenced, but the leverage from structured cross-evidence challenge applies across the whole list. Framing the framework as "a sycophancy defense" understates what it does; the disciplines are calibrated against the full family.

### What the framework does not address

- **The model ceiling.** Multi-agent ceremonies don't produce outputs better than the underlying model is capable of; they reduce single-shot variance toward the ceiling. You can't multi-agent your way past the model's underlying capability.
- **Pure judgment-shaped questions** where there's no checkable evidence and no domain expert with disjoint sources. The framework's leverage is bounded by the availability of evidence.
- **Low-stakes, single-shot, or routine work** where the structure adds cost without quality gain.

### Recognizing these situations in a deployment

The three boundaries above are real but unevenly detectable. A deployment should recognize and call out each one, varying the response by what's mechanically available.

- **Low-stakes, single-shot, or routine work** is detectable at entry by topic shape. A pre-flight check classifies the topic against the routine pattern (bug fix, rename, dep bump, simple refactor, factual lookup, mechanical edit) and surfaces a warn-and-proceed prompt: "this looks routine; SPARRING will likely add cost without quality gain -- proceed anyway?" Soft gate, not refusal -- the partner retains the call.

- **Pure judgment-shaped questions** are detectable at entry as a verifiable-artifact-channel check: would any artifact -- a fact, a source, a measurement, a concrete failure mode -- settle this question, even partially? If the honest answer is "no, it's pure preference," the deployment flags the topic and routes to a degraded mode: single-Challenger pressure-testing per the Discipline 2 fallback, rather than spawning two correlated specialists pretending to disjoint evidence. Many topics admit partial artifacts (precedent, prior data) and degrade gracefully; full-preference questions are rarer than they look but real when they appear.

- **The model ceiling** cannot be reliably detected at entry. Ceiling-hit only manifests in retrospect against ground truth the system doesn't have. What is detectable at run time are *symptoms*: convergence reached without artifact citation, both roles producing the same reasoning shape, an LLM-as-judge component (when present) flagging that the convergence reasoning is shallow. The deployment surfaces these as "ceiling-hit candidate" findings in the spar artifact and post-run report, where they can be reviewed against the converged decision -- not as entry gates, since false positives would block real work.

The general posture: warn-and-proceed for the two detectable-at-entry cases; instrument-and-surface for the in-run case. The framework does not prescribe specific thresholds -- those are deployment-tuning decisions -- but it does require the recognition behavior exist and produce visible signals to the partner. The reference deployment realizes this as an **Applicability Gate** component (see [`sparring-reference-deployment.md`](sparring-reference-deployment.md)).

### Where it pays back

Decisions where:
- The cost of being wrong is meaningful.
- The question has genuine domain dimensions.
- You can pair specialists with disjoint evidence bases (different domains, different data, different tools).

Examples: code architecture reviewed by an engineer and a security specialist with different evidence; creative design pressure-tested by domain experts whose source bases don't overlap; plan scoping with reviewers from genuinely different lenses.

The leverage compounds when the specialization is real. When it's not -- when distinct evidence bases cannot be articulated for the two roles -- the discipline is to fall back to single-Challenger pressure-testing rather than spawning two correlated agents pretending to be specialists.

### Future extensions worth pursuing

Beyond the variants and operational patterns above (which are part of the base framework), several more ambitious directions extend the framework's reach. These are research- or infrastructure-level rather than configuration-level.

- **N-specialist decisions** (multi-Generator). Genuine cross-domain decisions sometimes need three or more specialists who each generate proposals from their own evidence base, with a separate convergence layer. Distinct from the Multi-Challenger ensemble in Variants (which keeps the Generator unitary). Requires governance for how N proposals converge -- unanimity, majority, weighted, etc. -- which is itself a design problem worth research.
- **Cross-time pressure-testing.** A Challenger reviewing decisions made months ago against what's now known. The Watching-role Challenger in Variants applies the function to ongoing systems; cross-time extends it to retrospective review (post-mortems, periodic architecture audits, milestone closeouts).
- **Synthesis (true dialectic).** Synthesis is now *partially shipped* as one of five named responses in the disagreement-at-cap protocol -- the receiving party (human or parent agent) can produce a third option neither role proposed, integrating insights from both positions, when the disagreement reveals two valid evidence bases neither alone resolves. That is a one-shot synthesis at the Popper/Hegel boundary. The remaining ambition is **iterative synthesis**: each synthesis becomes a new thesis that gets antithesized in a fresh SPARRING round, with the cycle repeating until convergence. The "synthesize, then re-spar the synthesis" path is named in the protocol but is not first-class infrastructure -- automating multi-pass synthesis loops, with governance for when to stop iterating, is the genuine future-extension work. Closer to Hegelian thesis-antithesis-synthesis than the current Popper-style falsification shape.
- **Calibration training.** Using Challenger feedback over time to improve Generator self-assessment. Essentially RL-from-Challenger-feedback applied to the Generator's predictions of where its work will fail. Heavy infrastructure; requires the Phase 3 eval-harness layer to provide the feedback signal.
- **Adversarial scenario generation.** Pointing SPARRING at existing plans or systems to generate realistic failure scenarios and edge cases. Different from in-loop pressure-testing because the target is an existing artifact (a deployed system, a written plan), not a decision being made now.
- **Bias-aware Challenger lenses.** Specific Challenger configurations tuned to detect specific cognitive biases (anchoring, availability, framing, sunk cost, optimism) the Generator might exhibit. A library of pre-built Challenger personas, each specialized in a single bias-detection lens.

### The one-sentence version

The SPARRING Framework says: when an AI system has to make a decision that matters, structure the work as a divergent-then-convergent loop with two roles whose Challenger function shifts as the loop progresses, require that both roles draw on genuinely different evidence, demand that every concern cite a verifiable artifact, only call the decision converged when both roles independently agree, return unresolved disagreement to the human or parent agent with a named response protocol (pick-a-side-with-tradeoffs, defer, reframe, escalate, synthesize) when the iteration cap is reached, instrument the system so you can tell whether the structured challenge is producing real challenge or theater, and host the work on two surfaces (one for active dialogue between humans and agents, one for the curated record that future readers will rely on) -- recognizing that those two surfaces serve genuinely different purposes and may need to be physically separate at scale.

---

## Reference Deployment: A CLI-Based SPARRING System

*Extracted on 2026-04-29 (from rev. 17 of these notes) into its own standalone document. Single source of truth: **[`sparring-reference-deployment.md`](sparring-reference-deployment.md)**.*

That document covers: discipline-to-component mapping, the four-layer architecture, agent topology, full CLI surface, configuration and data model, four pluggable adapters, variant support, the phased build sequence (Phase 1 MVP -> Phase 2 maturity -> Phase 3 enterprise), honest tradeoffs, and general business considerations.

When the deployment plan evolves, edit the standalone document, not this stub. New deployment discussion / scratchpad work can resume in this section if it is in flight; once it stabilizes, fold it back into the standalone.

---

## Part 1 — Origin

Bart developed this framework as ResourceForge content (LinkedIn, two posts and two graphics, April 2026). On 2026-04-28 the partner Cut adopted five Lifspel integrations (Part 5). The framework is **not** intended as a default discipline for every prompt; its target is **high-quality decisions** where iterative pushback and structured challenge produce qualitative or accuracy benefits the single-shot path would miss. Token cost is not the optimization metric -- decision quality is.

---

## Part 2 — The framework

### Core thesis

In agentic systems, the dominant failure shape is **not hallucination as a single-shot event** — it is the **family of single-perspective failure modes compounding across handoffs**. Pleasing bias is the most-often-cited (LLMs agree too easily, soften critique, optimize for sounding helpful), but misread questions, invented specifics, specialization blind spots, confidently-wrong outputs, anchoring, and bandwagon contamination compound through agent chains the same way. In a single interaction any one of these is manageable; in a chain of agents handing outputs to each other, one agent's softened "yes" (or invented specific, or missed cross-domain risk) becomes the next agent's premise, and the system reinforces its own assumptions while sounding coherent.

PNP — **Polite, Not Pleasing** — is the antidote: structured challenge instead of structured agreement.

### The four phases

| Phase | Purpose | Iteration limit | End condition |
|---|---|---|---|
| **SPARK** | Diverge — generate as many possibilities and perspectives as possible | up to ~5 | ends on Generator with the largest useful set of possibilities |
| **Pattern Lock** | Detect structure — separate signal from noise; find real patterns | up to ~2 | ends on Generator with a structured set of patterns and ideas |
| **The Cut** | Mode transition — stop expanding, begin elimination, force convergence | up to ~2 | ends on agreement between roles, or Challenger enforcement |
| **PNP** | Validate — pressure-test the strongest option for assumptions, logic, feasibility, risks, edge cases | up to ~3 | ends on Challenger with confidence the decision holds |

If a phase hits its iteration limit without meeting its criteria, the **Challenger enforces the transition** rather than letting the loop drift.

### SPARRING — the operating mechanic

Two persistent roles run through all four phases: **Generator (G)** and **Challenger (C)**. Same two seats, but the Challenger's *function* changes per phase.

| Phase | What the Challenger asks |
|---|---|
| SPARK | "What else could this be?" |
| Pattern Lock | "Are these actually different?" |
| The Cut | "Is more generation still useful?" |
| PNP | "Does this actually hold up?" |

This role-shift is the framework's sharpest contribution. Standard adversarial frameworks (red team / blue team, GAN-style) give you one Challenger function. SPARRING gives you four — expand, filter, close, stress-test. Same seat, different job per phase.

### Inspirations Bart cites

Divergent / convergent thinking; dialectic (Popper-style falsification at the loop's base, with Hegelian thesis-antithesis-synthesis available as a named response at the disagreement-at-cap handoff); red team / blue team; adversarial learning (GANs).

---

## Part 3 — Honest critique (PNP applied to the framework itself)

A document that just praised the concept would be exactly the pleasing-bias artifact the framework is trying to prevent. The original critique included several points that did not survive Bart's clarifications about scope, iteration semantics, and the metric (quality, not token cost). What follows is the revised set -- what holds, what was softened, what was retracted -- with the reasoning explicit.

### What still holds

1. **The Cut is structurally inconsistent in the visual treatment.** It carries an iteration count (~2 in the graphic) but is fundamentally a transition, not a generative phase. Listing it as a peer to SPARK / PNP invites confusion about what it produces. Minor taxonomy issue, not a fundamental flaw.
   - *Status (2026-04-29)*: textual treatment in these notes already explains the Cut as a mode transition; the open work is at the marketing-graphic level, not the framework level. Defer to graphic refresh; not an active doc concern.

2. **The dialectic citation overreaches.** The loop eliminates and validates; it does not synthesize. Closer to Popper-style falsification than Hegel. Small, intellectual-honesty point.
   - *Status (2026-04-29)*: **partially resolved** via the Disagreement-at-cap response protocol (above). Synthesis is now a named response option at the disagreement-at-cap handoff, giving the framework a Hegelian extension at one boundary while preserving the Popperian base for the in-loop convergence. The remaining ambition (iterative synthesis as automated infrastructure) is documented in "Future extensions worth pursuing."

3. **Multi-agent designs have their own failure modes.** Coordination overhead, handoff drift, integration of conflicting outputs, delayed convergence. A poorly-designed multi-agent system can produce *worse* results than a single well-designed agent. This is not a critique of the framework -- it is a critique of *naive* applications of the framework. The "carefully considered and crafted" qualifier is doing significant work in the framework's value claim.
   - *Status (2026-04-29)*: addressed structurally by the reference deployment -- code-orchestrator default minimizes coordination overhead; the spar artifact records each round so handoff drift is auditable; the Disagreement-at-cap response protocol handles integration of conflicting outputs; the iteration cap bounds delayed convergence. The "carefully designed" qualifier remains load-bearing -- the deployment makes "careful" mechanical, not optional.

4. **The model ceiling still bounds quality.** Multi-agent ceremonies don't produce outputs better than the underlying model is capable of; they produce outputs *closer to the model's ceiling* by reducing the single-shot variance from biases, blind spots, and pleasing drift. The trade is single-shot-ceiling-or-below for multi-shot-closer-to-ceiling -- which is the right trade, and what you want -- but it is a real bound. You can't multi-agent your way past Opus's underlying capability.
   - *Status (2026-04-29)*: addressed structurally via the Applicability Gate's **ceiling-hit symptom detector** in the reference deployment (convergence-without-artifact-citation, identical reasoning shapes between roles, LLM-as-judge low-substance flag). The ceiling itself is not eliminated -- the bound is real and unchanged -- but ceiling-hit candidates are now flagged in the spar artifact so the receiving party can review the converged decision against the suspicion that the model topped out rather than genuinely converged.

5. **Specialization is strongest when agents have genuinely different ground truth, not just different role labels.** Two specialized agents with the same training data, the same evidence pool, and only different system prompts will produce more correlated outputs than they appear to. The strongest specialization is when agents have access to *different evidence*: Marcus reading code Lena hasn't read; Lena citing biomechanical research Marcus doesn't know; Idris citing mythology Lena doesn't have. Persona-only specialization with shared evidence is real but smaller than it looks. The Idris-Lena pairing adopted in Part 5 is strong specifically because their evidence bases are *genuinely disjoint* (mythology / literary tradition vs historical / biomechanical / combat-sports research) -- the Challenger has substance to draw on that the Generator does not. A generic Generator/Challenger pairing with shared evidence is weaker -- still useful, but not as much as the structural framing suggests. (This is also the strongest residual case for partner involvement: the partner has lived experience and project history that agents don't.)
   - *Status (2026-04-29)*: this concern *is* Discipline 2; the framework's structural response is the **evidence-base resolver** with explicit fallback to single-Challenger when distinct evidence cannot be articulated. The deployment enforces that personas commit to specific evidence sources at generation time. Concern stands as written; it is the design concern Discipline 2 was built to answer, not a residual flaw.

### What was softened (still real, but smaller than originally framed)

6. **Pleasing bias in the model weights.** The original framing was "role assignment is a partial fix, not a structural one." The clarification that survives: specialization through a carefully-crafted system prompt can *substantially* constrain pleasing bias -- more than the original critique credited. The residual risk is narrower than originally stated: the agent pleasing the user's *expectation of seeing structured challenge*, which is harder to counter via prompt alone but real. This is the sharper version of the concern.
   - *Status (2026-04-29)*: structural defenses in the deployment (specialization with disjoint evidence, verifiable-artifact discipline rejecting unsubstantiated concerns, "calibrated agreement" rubric criterion in the LLM-as-judge eval) cover the broad case. Residual narrow risk -- the agent pleasing the user's *expectation of seeing structured challenge* -- is partially addressed by the rubric's "substantive vs theatrical" criterion, which catches manufactured-rigor outputs. Not eliminable via prompt alone, but visible to measurement.

7. **Theatrical adversariality.** Original framing presented this as a major implementation risk. Specialization -- especially **domain-grounded persona specialization** like Lena pressure-testing Idris's lore -- is a much stronger defense than the original critique credited. When the Challenger has genuine domain expertise the Generator lacks, the challenge has substance to draw on, not just adversarial posture. The risk persists specifically for *non-domain-grounded Challengers* -- a generic "Challenger" agent told to "pressure-test this" without distinct expertise is more susceptible to manufactured rigor than a Lena or a Marcus would be.
   - *Status (2026-04-29)*: addressed structurally via domain-grounded specialization (Discipline 2 + persona library + evidence-base resolver), the verifiable-artifact requirement (Discipline 3 + Challenger output schema), and the "substantive vs theatrical" rubric criterion in the LLM-as-judge. The residual risk for non-domain-grounded Challengers is recognized in the deployment: when the evidence-base resolver cannot articulate distinct evidence, it falls back to single-Challenger PNP rather than spawning a generic "Challenger" with no substance to draw on.

8. **The highest-leverage Challenger is often the human partner.** Original framing risked treating the framework as agent-to-agent when the human is the more useful adversary. The clarification: the partner is highest-leverage on *framing, premise, scope, stance* -- the broad judgment calls. Specialized agents can be highest-leverage on *domain-specific pressure-testing* the partner cannot do (Lena on biomechanical plausibility; Dani on security surface; Marcus on implementation risk). Both are needed in different roles, not in competition.
   - *Status (2026-04-29)*: supported structurally via the `--human-challenger` (and `--human-generator`) role variants in the deployment. The CLI prompts the human in the appropriate round and parses their input into the structured signal format. Both roles can be human, agent, or mixed -- the "best Challenger for this decision" is a deployment-time choice, not a framework constraint.

### What was retracted

9. **"Iteration counts are arbitrary."** The original critique read the graphic's "up to ~5 / ~2 / ~2 / ~3" as fixed defaults. Bart clarified the counts are flexible and dynamically applied based on the task and the results of each phase. The bigger point is the *ability* to iterate to better quality. **Retracted.**

10. **"Not every prompt is decision-shaped."** The original critique said the framework needs an "doesn't apply" gate. Bart clarified the framework was never intended for every prompt -- it targets high-quality decision-making where iterative pushback produces meaningful gains. The critique was a misreading of the framework's intended scope. **Retracted as a flaw**; preserved as a *scoping observation* (apply it where quality gains justify the design effort, not as a default discipline).

11. **"Cost. 2-4x token cost is expensive theater for low-stakes decisions."** Bart clarified that quality is the optimization metric, not token cost. The critique conflated cost with the framework's stated value. **Retracted in its original framing.** The legitimate residue: even on the quality dimension, scoping still matters -- a careful design pays back where the quality difference matters most, which is the same scoping observation as point 10.

### What survives as the framework's load-bearing contribution

Three contributions are real and non-commodity:

- **Role-shift per phase.** The Challenger's function changing across phases (expand → filter → close → stress-test) is a sharper tool than generic adversariality.
- **Pattern Lock as a named middle phase.** Most divergent/convergent frameworks jump from "expand" to "evaluate" and miss the transition where expansion has stopped producing real novelty. Naming false-novelty detection as a discrete job is a real contribution and grants social permission to stop brainstorming without prematurely closing.
- **PNP as a transferable atom.** "Polite, Not Pleasing" compresses into a single directive in a way most frameworks cannot. Even pulled out of the formal loop, prepending PNP to a prompt meaningfully shifts behavior.

### A new claim worth pressure-testing in its own right

After the clarifications, the framework's strongest claim is roughly: **carefully-designed multi-agent interactions with thoughtful specialization should produce significantly higher decision quality than single-agent or single-shot approaches on tasks where iterative pushback has meaningful leverage.**

That claim is largely correct, with three real conditionalities:

1. **"Carefully designed" is doing heavy lifting.** Specialization gives leverage, not quality. Naive multi-agent with vague role definitions can be worse than single-agent. The framework's value scales with design quality.
2. **Different ground truth compounds; same ground truth doesn't.** See point 5 above.
3. **Quality is bounded by the model ceiling and by the evidence available to the agents.** Multi-agent reduces single-shot variance and bias; it does not transcend the underlying model.

These are conditions on *when* the framework works, not arguments against it.

---

## Part 4 — Applicability to Lifspel

The brainstorm covers three surfaces, ordered from lightest to heaviest.

### Surface A — Quick single-prompt invocation

A way for the lead developer to invoke the approach inside a normal chat with Claude Code, without spinning up a ceremony.

| Option | Shape | Cost | Notes |
|---|---|---|---|
| `pnp:` inline prefix | Mirrors existing `Marcus:` / `Diane:` agent-prefix pattern. One reply: brief SPARK on alternate readings, Cut to chosen reading, then PNP-style self-pressure-test. | Trivial | Plays well with existing inline-agent vocabulary. |
| `spar:` prefix | Same shape, names the dialogue mechanic. | Trivial | Naming variant of `pnp:`. |
| `challenger:` prefix | Recasts as Claude wearing the Challenger role for one reply: pushes back on the *premise* before answering. | Trivial | Useful when the user suspects their own framing. |
| `no pleasing` directive embedded in prose | Gentler than syntax; triggers self-PNP before responding. | Trivial | Closer to natural speech. |
| Tail-position phrase: `...PNP this.` | At the end of the prompt rather than the front. | Trivial | Easier on prompts that contain colons. |
| Slash skill `/pnp <prompt>` | Full skill plumbing. | Medium | Probably overkill for "quick." |

**Pattern Lock observation:** these cluster into two families — *prefix that names a stance* (plays well with existing inline-agent vocabulary), and *directive embedded in prose* (closer to natural speech). A separate dimension worth flagging: at single-prompt scale, **most of the value is in PNP alone** — one extra self-pressure-test pass on whatever Claude was going to say. The other phases get visibly thin when squeezed into one reply.

### Surface B — Deep two-agent run

A multi-turn ceremony where two distinct voices actually spar through the four phases.

| Option | Shape | Cost | Notes |
|---|---|---|---|
| New `/sparring` skill | Spawns Generator + Challenger subagents, drives the four phases with iteration caps, returns transcript and recommendation. | High | Most reusable but most expensive to build, and most prone to theatrical adversariality. |
| Round Table thread as orchestration medium | Topic posted as a thread; two agents alternate per phase. Phase labels in post titles (`[SPARK pass 1]`, `[PNP pass 1]`). Partners can read live, intervene, vote on transitions. | Medium | Plays to existing infrastructure: threads, voices, reads, checkoffs. |
| Pair existing agents as natural sparring partners | Domain expertise becomes the role assignment. Idris (lore) sparred by Lena (plausibility); Marcus (code) sparred by Dani (security/risk); Zoe (UIX) sparred by Marcus (technical feasibility). | Low | No new personas; SPARRING lands on top of existing expertise. Most resistant to theatrical adversariality because the Challenger has real domain skin in the game. |
| Inline single-session both-roles | Claude plays Generator and Challenger in alternating turns within one reply, with explicit phase headers. | Trivial | Cheapest, lowest fidelity. |
| Hybrid: Claude drives the loop, subagents only for the heaviest phases | SPARK and Pattern Lock inline; spin up a Challenger subagent for PNP only. | Low-medium | Concentrates token spend where it matters. |

**Pattern Lock observation:** the question "who decides when a phase is over?" has three viable answers — partner approves transition (highest fidelity), iteration cap auto-transitions (deterministic), Challenger-agent declares (riskiest, prone to theatrical close). Without a partner gate, the loop is most likely to produce manufactured rigor.

### Surface C — Integration into existing agentic workflows

Where compounding LLM failure modes (pleasing bias, hallucinated detail, missed cross-domain concerns, single-perspective drift) can stack across handoffs in the Lifspel ecosystem today, and where a small added step would have measurable effect.

Surfaces, grouped by tier:

**Tier 1 — automated agents with no human in the loop.** Highest compounding-failure risk (pleasing bias most visible, but also unchecked hallucinated detail, missed cross-domain concerns, single-perspective drift), highest leverage from a structured pressure-test, cheapest to add (one extra step per agent).

- Marcus's daily auto-fix workflow — self-PNP on "what would I miss if I am being too agreeable to the audit?"
- Dani's security digest — self-PNP on "what false positives am I likely flagging?"
- Zoe's UIX audits — self-PNP on "which findings am I overstating because they pattern-match prior issues rather than this page's actual context?"
- Promise Verifier — already adversarial in design; could be phase-labeled rather than restructured.

**Tier 2 — decision ceremonies.** Decision-shaped, structurally aligned with the four-phase loop.

- `/plan-review` — currently parallel reviews + Diane consolidation, no Challenger pass on the synthesis. PNP phase after consolidation, voiced by Marcus or Dani (Diane cannot pressure-test her own consolidation without role conflict).
- CRv2 milestone scoping — explicit SPARK ("here are 3-5 scoping shapes") and PNP ("here is how each fails") before partner sign-off.
- `/thread-assessment` close decisions — 1-iteration PNP at the close decision: "am I closing this because it is stale or because it is resolved?"
- `/hygiene-cleanup` judgment bucket — PNP pass on each judgment-bucket item before partner review.
- Diane's session-close chatlogs — PNP pass on "what claims in this chatlog might not be true?" before close, since chatlog claims propagate into the next session's memory.
- `/capture-in-flight` — 1-iteration PNP at capture time on "is this actually a bug, or am I misreading the code?"

**Tier 3 — creative / divergent work.** Where SPARK and Pattern Lock matter more than PNP.

- Idris's lore work — SPARK fits naturally; pair with Lena as automatic sparring partner for plausibility, internal consistency, source grounding.
- Scoping discussions for new milestones — explicit SPARK before convergence.

**Already-instantiated.** `/coach-prompt` already does PNP applied to a draft prompt before it goes to another agent. The framework would generalize what is already built locally.

### Cross-cutting observation

The integration surfaces share a useful property: nearly all of them can be added as **one extra step inside an existing skill or workflow**, not as new ceremonies. That is the right shape for a low-friction first adoption — small additions to surfaces that already exist, where the compounding-failure risks are concrete rather than hypothetical.

---

## Part 5 — Adoption status

On 2026-04-28, partner Cut adopted **five integrations across all partners and all computers**, plus ambient framework awareness in CLAUDE.md so every Claude Code session inherits the vocabulary regardless of where it runs.

### What is live

| # | Surface | Mechanism | Implementation |
|---|---|---|---|
| 1 | `/pnp` skill (flexible invocation) | Single-reply compressed SPARK -> Cut -> PNP pass. Triggers on `/pnp <idea>`, `/pnp` (no arg), `pnp:` prefix, `... PNP this.` tail, or any prompt containing `no pleasing`. | `.claude/skills/pnp/SKILL.md` plus the **Triggering forms** section in `CLAUDE.md` |
| 2 | Marcus's auto-fix self-PNP | A self-pressure-test step inside Marcus's automated workflow before he composes his board response. Internal discipline, not theater -- the post itself does not mention PNP. | `scripts/tasks/agent-reactions/marcus-agent.js` (RULES block + workflow step in both UIX and General task prompts) |
| 3 | `/thread-assessment` close-decision PNP | A one-iteration pressure-test on the recommendation in Step 3, required when the recommendation is CLOSE. Switches to KEEP-OPEN or CONSULT-AGENT if no specific resolution evidence can be cited. | `.claude/skills/thread-assessment/SKILL.md` Step 3 item 6 |
| 4 | Idris-Lena automatic sparring | When `/act-as-agent` is invoked for either of them on substantive new work in their adjacent domains, the skill auto-triggers the other as Challenger via the Agent tool. Both outputs are presented to the partner. | `.claude/skills/act-as-agent/SKILL.md` Step 3c + Step 5; Sparring Partnership sections in both persona files |
| 5 | `/plan-review --pnp` opt-in | Adds a Challenger pass on Diane's consolidated findings before her synthesis post. Marcus or Dani voices the pass depending on the plan's domain. Recommended for high-stakes plans. | `.claude/skills/plan-review/SKILL.md` Step 5b + Step 6 item 5b |

Plus a sixth, free of cost: **Pattern Lock as shared vocabulary** in scoping discussions. No code; just shared language for naming false novelty mid-brainstorm.

### Subsequent additions (post-Cut, same day)

The original five integrations shipped on 2026-04-28 produced enough partner traction to justify additional work the same day under the project's 4-6 week timeline pressure. The following were shipped on best-guess assumptions (Phase 3 roadmap territory per Appendix D) rather than after the staged Phase 1 measurement period the roadmap recommends:

| Rev. | Addition | Surface |
|---|---|---|
| r8 | **Verifiable-artifact requirement** extended to claim-of-concern (Appendix D #3) across all five Challenger surfaces. Every concern raised by a Challenger function must cite a specific verifiable artifact or be dismissed as theatrical adversariality. | `/pnp`, `/act-as-agent` Idris-Lena Challenger prompt, `/plan-review --pnp` Challenger prompt, `/thread-assessment` close-decision PNP, Marcus's self-PNP |
| r9 | **Phase 1 eval harness**: lightweight rubric + partner-sampling cadence. Six criteria scored 1-3, applied to a sample of Challenger outputs. The minimum-viable measurement layer to validate that the SPARK/PNP discipline is delivering quality vs. theater. | `docs/standards/challenger-output-rubric.md` |
| r10 | **`/spar [iterations] <topic>` skill** -- partner-invokable structured ceremony with two divergent specialist sub-agents, both-must-agree convergence, persona-evidence-base requirement (single-Challenger fallback when distinct evidence cannot be articulated). Default 2 iterations; minimum 1. | `.claude/skills/spar/SKILL.md` |
| r11 | **Marcus self-spar** in his auto-fix workflow. The Claude Agent SDK's Task tool added to Marcus's allowed tools (production runs only); two sub-agent templates (`spar-generator`, `spar-challenger`) defined in his runner; SELF-SPAR DISCIPLINE rule added to his system prompt with five observable triggers, the three tightenings, and a 1-iteration cap. Sub-agents run on Sonnet for cost control. | `scripts/tasks/agent-reactions/marcus-agent.js`, Operating Principle 5 in `docs/agents/marcus-kowalski.md` |

The discipline that prevents these from collapsing into theater (per Appendix D): verifiable-artifact requirement for every concern; persona-evidence-base specification with fallback when distinct evidence cannot be articulated; both-must-agree convergence; observable triggers for self-invocation rather than feelings of uncertainty.

### What is deferred

- **Full `/sparring` four-phase ceremony** (SPARK -> Pattern Lock -> The Cut -> PNP, with multiple iterations per phase and Challenger function shifting per phase). The lightweight `/spar` skill (rev. 10) operationalizes the simplified one-or-two-iteration version of the same idea -- two divergent specialists, both-must-agree convergence, verifiable-artifact requirement -- and addresses much of the original brainstorm's intent. The full four-phase ceremony with Pattern Lock as a discrete phase, separate iteration caps per phase, and explicit Challenger-function shifting per phase remains future work.

  Three of the original brainstorm's design threads were partially or fully operationalized via `/spar` and Marcus self-spar:

  1. **Ground-truth disjointness** (Part 3 conditionality #2) -- partially operationalized in `/spar` and Marcus self-spar via the persona-evidence-base specification requirement (with single-Challenger fallback when distinct evidence cannot be articulated). The full four-phase version would need a routing layer (the detection orchestrator from Appendix D Phase 3) that the current implementation doesn't have.

  2. **Autonomy with principled escalation** (rev. 6) -- operationalized in `/spar`'s self-invocation triggers (observable conditions documented in `.claude/skills/spar/SKILL.md` and Marcus's Operating Principle 5) and in `/plan-review --pnp`'s opt-in orchestrated escalation. The brainstorm's framing of orchestrated-vs-choreographed escalation is now reflected in actual code.

  3. **Transition-declaration reliability** -- operationalized in `/spar`'s both-must-agree convergence rule (replaces the structurally-suspect "Generator declares agreement" path) plus iteration cap as mechanical fallback. The third option from the brainstorm -- ground-truth-exhaustion as a completion signal -- is not yet implemented; would require eval-harness instrumentation (Phase 3) to detect when an agent has exhausted its evidence base.

  Residual concerns the brainstorm and current implementations have not closed: (a) LLM self-assessed uncertainty is unreliable, so the choreographed triggers documented in skill files and persona files must be re-examined when production data shows whether they fire appropriately; (b) theatrical adversariality can fire without the agents detecting it -- agents inside the loop are not positioned to notice they are producing correlation rather than challenge, which makes the Phase 3 eval harness from Appendix D more important once it exists.

### How partners get this

All five integrations are repo-tracked. Partners on any machine inherit them via `/sync-now`. The framework awareness in `CLAUDE.md` is also repo-tracked, so every Claude Code session loaded against any clone of the project gets it.

---

## Appendix A — Agent fundamentals (background reference)

This appendix captures foundational concepts about agents and agentic AI that the framework presupposes. It exists so readers landing on this doc do not need to chase down the concepts elsewhere.

### What is an agent

An **agent** in agentic AI is an LLM running in a loop with tools, where the model decides what to do next at each step until it decides it is done (or hits a budget or turn limit). Three required properties:

- A **loop**, not a single-shot prompt-response.
- **Tools** -- file reads, shell commands, API calls. The agent takes actions in the world, not just generates text.
- **Autonomy within the loop** -- the model picks the next move based on what it just saw, not a fixed pipeline.

Claude Code (the CLI/IDE assistant) is itself an agent under this definition. So is Marcus running via `scripts/tasks/agent-reactions/marcus-agent.js`. So is any subagent spawned via the `Agent` tool. A single-shot ChatGPT-style prompt is *not* an agent -- no loop, no tools, no autonomy.

### Same foundation -- shared and not shared

The Claude Agent SDK (`@anthropic-ai/claude-agent-sdk`) is Anthropic's public toolkit for building agents that work the way Claude Code works. The migration plan at `docs/standards/sfxls.agents.migration-plan.md` is explicit about converting fixed PHP pipelines into SDK-based agents. Marcus is an instance.

What is shared between Claude Code and SDK agents:

- **Same model family.** Both call Claude (Opus / Sonnet / Haiku, configurable per agent).
- **Same loop pattern.** Receive prompt -> model picks tools -> execute -> result back to model -> next decision.
- **Same tool primitives.** Tools have a name, schema, and executor. Same shapes.
- **Same hooks pattern.** PreToolUse hooks, safety wrappers -- Marcus's safety hooks are the same shape Claude Code's harness uses.

What is *not* shared:

- **Different system prompt.** Claude Code's defines an interactive role; Marcus's defines the senior-engineer persona. Different personality, different behavior, *same model*.
- **Different tool sets.** Claude Code has the full kit (Read, Edit, Write, Bash, Skill, Agent, plus deferred tools). Marcus has a curated subset (Read, Edit, Bash, Grep, Glob), restricted further by his hooks.
- **Different harness.** Claude Code is interactive; partner can interrupt, partner can clarify, partner-context persists. Marcus is non-interactive -- one-shot per task, no human in the loop, terminates after posting.
- **Different lifecycle.** Claude Code's context lives across the whole session. Marcus's context is fresh per task.

The key practical difference: **Claude Code can ask the partner questions mid-task; Marcus cannot.** Marcus must encode all the discipline he needs in his system prompt because there is no in-loop check.

### Sub-agents

A **sub-agent** is an agent another agent spawns during its own run, usually to delegate a sub-task. Claude Code's `Agent` tool is exactly that interface. The `/plan-review --pnp` Challenger pass is a concrete example: Diane (or the orchestrator) spawns a Marcus or Dani sub-agent, hands it the consolidated findings, gets back a JSON of concerns.

Properties:

- **Isolated context.** The sub-agent does not see the parent's conversation. It starts fresh with whatever prompt the parent writes.
- **One-shot from the parent's POV.** Parent writes prompt, sub-agent runs autonomously to completion, returns one result, terminates. Parent cannot reach into its loop mid-run.
- **Does not pollute the parent's context.** A search sub-agent reading 30 files keeps those reads in *its* context window. The parent gets back a summary.
- **Same SDK foundation.** Sub-agents are themselves agents -- same loop, same tool pattern.

### Specialization

A **specialized agent** is one whose system prompt + tool set + sometimes model choice are tuned for a specific kind of work. Two distinct kinds of specialization matter for Lifspel:

**Specialization by task type.** Optimizing the agent for a specific kind of work:

- An Explore-shaped agent -- restricted to read-only tools, system prompt focused on search, optimized for "find X in the codebase."
- A Plan-shaped agent -- system prompt focused on designing implementation plans.
- A general-purpose agent -- broad capability, no restrictions, used when the task does not fit a specialty.

**Specialization by Role + Domain Knowledge (the agent's expertise lens).** Shaping how the agent reasons rather than what tasks it does. The StoryForge agents are this kind:

- Marcus thinks like a senior engineer.
- Lena thinks like a research scientist.
- Idris thinks like a comparative-mythology scholar.
- Dani thinks like a security/quality reviewer.

Role+Domain specialization shapes *how the agent reasons* about whatever is in front of it -- expertise scope, evidence-base scope, operational conventions, behavioral invariants. Two Role+Domain-specialized agents handed the same input can produce meaningfully different judgments because the system prompt has programmed different reasoning lenses (different evidence bases, different conventions, different priorities). Note: the optional Persona layer (voice, tone) does NOT carry this kind of specialization weight -- two agents with the same Role+Domain but different voice rules will produce stylistically different but substantively similar output. The lens is in the WHAT layer, not the HOW.

### Why specialization matters

Four things specialization gives you:

1. **Better outputs on the specialty.** A focused 200-token "you are doing X" system prompt produces better results on X than a general 2000-token prompt that has to also cover Y, Z, and W. The model is the same; the prompt shapes the behavior. Specialization is essentially programming the model via the system prompt.
2. **Hard restrictions you can rely on.** A read-only sub-agent literally cannot write files. Specialization-by-restriction enforces properties at the tool layer, not via prompt politeness.
3. **Smaller context footprint for the parent.** Sub-agent work happens in the sub-agent's context window. The parent gets a summary instead of 30 file reads.
4. **Cost and speed control** (when relevant). A simple specialized task can run on Haiku; a deep one can insist on Opus. Note this is the "cost" axis -- not the framework's metric, but a practical knob.

### Why not just spawn the same agent N times

You can. If N independent searches need to run, spawning N general-purpose agents in parallel is exactly right. Parallelism does not require specialization.

The reason specialization wins when tasks are *different in shape*: an LLM's behavior is dominated by its system prompt. The same model with two different system prompts produces different outputs on the same task. A general-purpose agent on a focused search task produces "fine" results -- it does the search but also over-thinks, brings in irrelevant context, narrates a lot. A specialized search agent on the same task is faster, cleaner, stays in lane.

Rule of thumb: **specialize when the task type has a distinct shape worth optimizing for; parallelize the same agent when the tasks are the same shape but multiple instances.** They combine -- the Generator/Challenger pairing in SPARRING is itself a specialization pattern that can also be parallelized across multiple decision points.

### Why this matters for SPARK/PNP

The framework's claim that multi-agent designs produce higher decision quality rests on specialization. Specifically:

- The Generator and Challenger roles are *structurally* specialized (they have different jobs even when operating from the same Role+Domain Knowledge layer).
- The Challenger's function shifts per phase -- a *temporal* specialization.
- The most powerful version pairs structural / temporal specialization with **Role+Domain specialization where the agents have genuinely different domain expertise and genuinely different evidence bases** (Lena pressure-testing Idris). That is where the framework's quality leverage compounds the strongest. Persona-layer specialization (different voice, different tone, different framing) does NOT add to this leverage and can subtract from it if it manufactures contrast that masks identical evidence bases.

The conditionalities in Part 3 -- careful design, different ground truth, model ceiling -- are statements about *when* the multi-agent quality leverage is real and when it shrinks toward zero.

---

## Appendix B — Multi-agent terminology (MAS, orchestration vs choreography)

Vocabulary background for talking about multi-agent systems precisely. The LLM-agent field is too young to have a single canonical vocabulary; this appendix names what is established, what is fragmented, and what cleanest terms map to the patterns in the Lifspel ecosystem.

### The umbrella term: Multi-Agent System (MAS)

**"Multi-agent system" (MAS)** is the established academic term, dating to distributed-AI research in the 1980s-90s -- well before LLMs. It just means: a system composed of multiple agents that operate in a shared environment. Wooldridge's *Introduction to MultiAgent Systems* is the canonical textbook.

In the LLM era, vocabulary has fragmented:

- **"Agentic system"** -- Anthropic's preferred term. Implies a system built around agents.
- **"Agent ecosystem"** -- looser, more colloquial (the Lifspel doc `docs/reference/agentic-ai-ecosystem.md` uses this).
- **"Compound AI system"** -- Berkeley / Databricks term, broader than just multi-agent.
- **"Agent network"** / **"agent society"** -- older academic terms still in use.

Vendors have invented their own naming (OpenAI's "Swarm" framework, CrewAI's "crews", AutoGen's "multi-agent conversations"). MAS is the only term that is universally understood across academic and industry contexts. The rest is in flux.

### The interact-vs-independent distinction: orchestration vs choreography

The cleanest existing vocabulary borrows from microservices / SOA, where this exact distinction was solved a decade earlier:

- **Orchestration** -- a central coordinator (like an orchestra conductor) directs the flow. Agents are dispatched to do work, return results, the orchestrator decides what happens next. The intelligence about *what to do when* lives in the orchestrator.
- **Choreography** -- agents follow their own logic and react to events / other agents (like dancers each knowing their part of the routine). No central conductor; coordination emerges from each agent's local rules and message exchanges.

This distinction is well-established in microservices. It has started being adopted in the LLM-agent context but is **not yet universally used** -- different vendors use different terms. The conceptual distinction is solid; whether "orchestration vs choreography" specifically is the term that wins industry adoption is uncertain.

### Other related terms

- **"Hierarchical agents"** / **"manager-worker pattern"** -- orchestration-style, with explicit delegation.
- **"Peer-to-peer agents"** -- choreography-style.
- **"Agent topology"** -- describes the *shape* of relationships (hierarchical, hub-and-spoke, peer-to-peer, mesh).
- **"Agent fleet"** / **"worker pool"** -- typically describes many agents running independently on a queue of tasks (from distributed systems).
- **"Swarm"** -- connotes many similar agents with somewhat independent behavior; OpenAI's framework uses this name.
- **"Embarrassingly parallel"** (from distributed computing) -- when agent work is so independent that no coordination is needed.

### A separate axis: cooperative vs competitive

Distinct from the interact-vs-independent axis, MAS literature also distinguishes by *goal alignment*:

- **Cooperative MAS** -- agents work toward a shared goal.
- **Competitive MAS** -- agents have opposing goals (game-theory roots).
- **Mixed** -- both elements present.

This is about *goal alignment*, not coordination pattern. Adversarial setups like the SPARRING ceremony are a kind of structured competitive interaction *inside* a cooperative ceremony -- the Generator and Challenger have opposing local roles but a shared goal of producing a higher-quality decision.

### Mapping to the Lifspel ecosystem

Both major coordination patterns appear in the StoryForge agent ecosystem. Naming them in standard vocabulary clarifies what is there:

- **Choreographed surfaces.** The Round Table auto-react chain is choreography: Marcus commits -> Dani auto-reacts to review (triggered by the `<!-- marcus-commit -->` marker) -> Marcus may auto-respond if findings are addressable. No central orchestrator says "now Dani goes" -- Dani's reaction logic fires on the marker. Each agent has its own trigger rules.
- **Orchestrated surfaces.** `/plan-review` is orchestration: the skill is the conductor, dispatching reviewer sub-agents in parallel, collecting their JSON, optionally spawning the Challenger sub-agent (with `--pnp`), then composing Diane's synthesis. Clear conductor, clear flow.
- **Independent / fleet surfaces.** The Task Runner (`storyforge-runner.php`) firing scheduled cron tasks is closer to a worker fleet -- independent agent invocations on a queue, no inter-agent coordination during a single run.
- **Small-orchestration surfaces.** The Idris-Lena auto-spar pairing in `/act-as-agent` is a minimal orchestration -- the skill is the conductor, dispatching the second agent as a follow-up Challenger after the first agent's deliverable.

### Honest note on the terminology

For external writing (ResourceForge, public-facing posts, partner communication outside Lifspel):

- **MAS** or **"agentic system"** is safe for the umbrella term.
- For the interact-vs-independent distinction, **explicitly defining your own terms** (e.g. "orchestrated vs choreographed agents" with a one-sentence definition each) is more reliable than assuming a reader already maps the words to the same meanings. Different audiences map the same words differently because the field's vocabulary has not converged.
- If a single pairing must be chosen, **orchestration vs choreography** has the cleanest industry pedigree (microservices / SOA, conceptually clean, increasingly adopted in agent contexts).

---

## Appendix C — Agent deployment patterns and where Lifspel sits

How other people deploy multi-agent systems, with the Lifspel architecture mapped into the landscape. The pattern level matters more than the brand level -- most frameworks are variations on a smaller set of architectural patterns. Appendix B covers the *vocabulary* (orchestration, choreography, fleet, etc.); this appendix covers the *patterns* and the frameworks that embody each.

Caveat: the LLM-agent field has been moving fast. Knowledge cutoff is January 2026; specific framework details may have shifted by the time this is read.

### The eight major patterns

#### 1. Graph / state-machine

**Examples**: LangGraph (LangChain).
**Shape**: The workflow is an explicit graph -- nodes are agent calls or tool calls, edges are transitions, shared state is a typed object passed through. The graph is the program.
**Coordination**: Pure orchestration. The graph is the conductor.
**Strengths**: Complex multi-step decision flows where the structure is known. Strong observability (every node is a checkpoint). Production-friendly.
**Weaknesses**: Rigid. Hard to express "the agent figures out what to do next."

#### 2. Conversation / group chat

**Examples**: Microsoft AutoGen's `GroupChat`; similar patterns in OpenAI Agents SDK.
**Shape**: Multiple agents in a chat room. A chat manager (or a turn-taking algorithm) decides who speaks next. Agents read the conversation, contribute, pass or hand off.
**Coordination**: Either -- chat manager orchestrates (rule-based routing) or agents self-route (choreography).
**Strengths**: Creative tasks, debates, research where the path is not predetermined. Easy to add a new agent.
**Weaknesses**: Chat history bloats fast. Hard to debug when many agents have been talking for many turns. Output quality drifts if the manager is weak.

#### 3. Role-based crew

**Examples**: CrewAI.
**Shape**: Define roles (researcher, writer, editor), define tasks, assign tasks to roles. The crew works through tasks in sequence or hierarchy. Often a manager role coordinates.
**Coordination**: Mostly orchestration, often hierarchical (manager -> workers).
**Strengths**: Structured division of labor. Maps well to "I want a small team to do X."
**Weaknesses**: Rigid task assignment. Roles tend to be lightweight (a one-line description) rather than deep personas.

#### 4. Handoff-based

**Examples**: OpenAI Agents SDK (the successor to Swarm); various customer-support-style frameworks.
**Shape**: Agent A is doing the work; when something requires a different specialty, A calls a `handoff` tool to transfer control to Agent B. B sees the conversation context and continues.
**Coordination**: Choreography -- each agent decides when to hand off based on its own logic.
**Strengths**: Specialist routing (intake -> diagnosis -> escalation). Lightweight, easy to reason about.
**Weaknesses**: No global view. If two specialists need to collaborate, handoff does not cover it cleanly.

#### 5. Tool-use loop with sub-agent spawn

**Examples**: Claude Agent SDK (what Lifspel uses); various Anthropic-built systems.
**Shape**: A primary agent has a tool kit, *including* the ability to spawn a specialized sub-agent for a task. The sub-agent runs to completion, returns a result, terminates. Parent agent decides what to do next.
**Coordination**: Orchestration from the parent's perspective; the parent decides when and what to spawn.
**Strengths**: Maximum flexibility -- the parent decides the orchestration shape on the fly. Good for developer-driven systems where the workflow is not predetermined.
**Weaknesses**: All the orchestration intelligence lives in the parent's prompt and reasoning, harder to inspect than a graph.

#### 6. Pipeline / DAG

**Examples**: Airflow + agent steps; custom batch-processing systems.
**Shape**: Linear or DAG-shaped sequence of agent calls. Each step is fixed. Output of one feeds the next.
**Coordination**: Orchestration by the DAG runner.
**Strengths**: Batch work, well-defined workflows, predictable cost.
**Weaknesses**: Inflexible. Does not handle "the agent decides what's next."

#### 7. Event-driven / pub-sub

**Examples**: Custom systems built on message queues (Kafka, SQS, RabbitMQ) or webhooks.
**Shape**: Agents subscribe to events; they fire when triggered; their outputs become events for other agents.
**Coordination**: Choreography.
**Strengths**: Reactive systems, async work, easy to scale horizontally.
**Weaknesses**: Hard to reason about overall flow. Hard to test. Hard to debug failure cascades.

#### 8. Blackboard / shared workspace

**Examples**: Academic MAS literature; some custom systems.
**Shape**: Agents read/write to a shared workspace (database, document, threaded board). Coordination emerges from shared visibility -- Agent A writes something, Agent B notices and acts.
**Coordination**: Choreography.
**Strengths**: Long-running collaborative work, mixed sync/async, partner-in-the-loop visibility.
**Weaknesses**: Coordination is implicit and can drift. Hard to enforce ordering or invariants.

### Where Lifspel sits in the landscape

Lifspel is a **deliberate hybrid** combining several of these patterns. Most frameworks pick one and stick with it; Lifspel picks per surface:

| Lifspel surface | Pattern | Notes |
|---|---|---|
| Foundation (Claude Agent SDK) | **Tool-use loop + sub-agent spawn** (#5) | Each agent is a tool-using loop; sub-agents spawn via Agent tool |
| Round Table | **Blackboard / shared workspace** (#8) | Threads + posts + reads + checkoffs; humans and agents share the same workspace |
| Auto-react chain | **Event-driven / pub-sub** (#7) | `<!-- marcus-commit -->` markers trigger reactions; chain-depth limits prevent runaway |
| Task Runner cron | **Pipeline / scheduled fleet** (#6) | `storyforge-runner.php` dispatches scheduled jobs |
| `/plan-review` | **Role-based crew + orchestration** (#3 + #1) | Agents reviewing in parallel through their lenses; Diane consolidates |
| Idris-Lena auto-spar | **Small-orchestration + handoff** (#4 + #5) | After Idris produces, Lena auto-spawned as Challenger |

Most products lean hard on one pattern (LangGraph shops use graphs everywhere; CrewAI shops define crews for everything). Lifspel picks per use case. That is a real architectural choice and worth naming.

### What Lifspel does that is distinctive (positive)

Properties most off-the-shelf frameworks do not give you out of the box:

- **Persona richness.** Persona files (`docs/agents/<slug>.md`) are deep -- a substantive **Role + Domain Knowledge** layer (expertise, evidence-base scope, conventions, standards-compliance rules, handoff authority and override rules) plus a calibrated **Persona** layer (voice, tonal anchors, surface-form conventions, optional Character anchor). Most frameworks treat agents as one-line role descriptions ("a senior code reviewer"). Lifspel treats them as documented people whose Role+Domain layer carries the structural commitments and whose Persona layer is partner-editable for cognitive availability. Closer to how a small company maintains role descriptions than how most agent frameworks work. (See `docs/bfn/sparring-reference-deployment.md` "Role + Domain Knowledge is mandatory; Persona is a lightweight optional layer" for the depth-with-function specification.)
- **Partner-in-the-loop as a first-class design.** The Round Table sidebar, reads, checkoffs, and the sidebar-override math at `storyforge/tests/api/round-table-tree.php` exist specifically to make agent work visible and actionable for human partners. Most frameworks treat humans as either out-of-loop or as interruptive. Lifspel's design starts from "agents and partners share the same workspace."
- **Verification discipline.** The Verification Rule (no agent claims work exists or doesn't without reading the file), the Promise Verifier, the persona-integrity rules (no breaking the fiction by referencing automation), the chain-depth + agent-reaction marker for reaction-loop prevention -- project-grown reliability patterns. Most frameworks ship without them.
- **Mixed coordination patterns by design.** Choreography for reactions (low overhead, partner-visible), orchestration for plan-review (structured, auditable), fleet for cron tasks (predictable, billable). Most frameworks force you into one. Lifspel picks per use case.
- **PHP + Node hybrid.** The board layer is PHP (mature, partner-facing); the agents are Node.js (Claude Agent SDK). Most frameworks are TS-only or Python-only. Lifspel architecture explicitly bridges so each layer uses the best tool. Rare and works.

### What Lifspel lacks vs. mature industry practice

Honest gaps. Not all are worth filling at the current scale -- naming them is the point.

- **No systematic eval harness.** Cost, tokens, turn count are tracked per run (`sf_task_runs`). Output *quality* is not systematically evaluated. Tools in this space include Braintrust, LangFuse, custom harnesses. They allow A/B prompt testing, rubric-scored output evaluation, regression tests on agent behavior. The Promise Verifier is a step in this direction (verifying claimed work) but narrow -- it does not tell you whether Marcus's review was *good*, just whether his FIXED claims were truthful.
- **No distributed tracing.** When reaction chains get deep (Marcus -> Dani -> Marcus), debugging requires reading multiple board posts and correlating timestamps. OpenTelemetry-style tracing across agent calls would make this much easier. Not urgent at current scale; would matter at 20+ agents.
- **No vector-store-based long-term memory.** Memory is file-based (chatlogs, persona files, memory dir). For an agent ecosystem doing long-running creative work, a vector store can let agents pull relevant prior context automatically. Trade-off: vector stores have their own failure modes (false-positive retrieval, embedding drift). The file-based approach is more inspectable.
- **No replay debugging.** When Marcus produces a wrong output, you can read his post and his commit, but you cannot replay his full reasoning trace step-by-step. Frameworks like LangSmith and AutoGen Studio offer this.
- **No A/B prompt testing infrastructure.** When tuning a system prompt (e.g. the recent PNP rule on Marcus), there is no systematic way to compare "before" vs "after" on a held-out test set. Reliance on interactive partner judgment. Fine at current scale, not at scale.
- **No formal eval / quality CI.** Most frameworks let you run a battery of evals on every prompt change. Lifspel is "ship it and see," with the auto-deploy hook providing cheap rollback as partial compensation.

### Honest read and priority ranking

For a system at Lifspel's scale and stage, the architecture is strong. The hybrid pattern choice is mature. Verification and persona discipline are above-average. Most missing pieces (eval harness, tracing, vector memory) are *scaling infrastructure* -- useful at 20 agents or 1000 tasks/day, not urgent at current scale.

Cost-vs-leverage priority for what to add first:

1. **A lightweight eval harness for agent outputs.** Even a manual "score these 10 Marcus posts on a 1-5 rubric weekly" gives you a quality signal you do not currently have. Adds the quality dimension to the existing cost / turn tracking.
2. **Distributed tracing.** Useful when reaction chains get longer or harder to debug. Tractable now; worth adding before growth.
3. **Vector store.** Lowest priority. The file-based memory works and is more inspectable. Add when retrieval volume justifies the complexity.

The orchestration / choreography / fleet mix in the current architecture is correct for the workload. Collapsing onto one pattern would be a regression, not a simplification.

---

## Appendix D — Mitigations for theatrical adversariality

### There is no settled industry-standard *method*

No agent framework ships an out-of-box defense for theatrical adversariality. LangGraph, AutoGen, CrewAI, OpenAI Agents -- none ship one. The closest thing to a "standard" is recognition that sycophancy / pleasing bias is a known LLM failure mode (Sharma et al. 2023 *Towards Understanding Sycophancy in Language Models*; Perez et al. 2022 on model-written evaluations; the broader alignment-faking literature). There is a body of research; production-grade defenses exist; but they are a tool kit, not a single method, and most production systems combine several.

### The tool kit, in roughly increasing rigor and cost

#### 1. Anti-pattern prompting (cheap, weak)

Explicit "do not manufacture concerns to look rigorous" instructions in the Challenger's system prompt. Sometimes phrased as "honest no-finding is a valid outcome" or "avoid false novelty."

This is what `/pnp`, `/plan-review --pnp` Challenger prompts, and the Idris-Lena auto-spar prompt already do. Cheapest mitigation, weakest -- relies on the Challenger to follow instructions about not being theatrical, which has a recursive problem (the same agent that might be theatrical is being asked to police itself). Useful as part of a stack, not sufficient alone.

#### 2. Asymmetric prompting (cheap, slightly stronger)

The Challenger's success criterion is "find flaws," not "produce a balanced review." Anthropic and OpenAI both publish on red-team prompting patterns. Stronger than #1 because it shifts the optimization target. Still cheap, still vulnerable -- the agent can produce fake disagreements to satisfy the success criterion.

#### 3. Tool-grounded verification (medium cost, real strength)

Require every concern the Challenger raises to be backed by a verifiable artifact: a specific file path and line number; a specific source citation; a specific test result or build output; a specific counterexample or edge case. The Challenger cannot just say "this seems risky" -- it must produce something checkable. If the artifact does not exist or does not say what is claimed, the concern is dismissed.

Marcus's Verification Rule (no claiming work exists or doesn't without reading the file) is exactly this pattern, applied to claim-of-work rather than claim-of-concern. Extending it to Challenger output is a small skill-prompt edit, not infrastructure work.

Works well for tasks where ground truth is mechanizable (code, citations, tests). Fails for purely judgment-shaped questions where there is no checkable artifact.

#### 4. Domain-grounded specialization with disjoint evidence

The Challenger draws on an evidence base the Generator does not have. This is the structural defense already named in Part 3 conditionality #2 -- the Idris-Lena pairing.

A Challenger pressure-testing from a position of genuine expertise the Generator lacks has substance to draw on, not just adversarial posture. "Theatrical" requires manufacturing objections; specialized domain experts have *real* objections grounded in evidence the other party has not seen.

The strongest structural defense for production agent systems. Does not require eval infrastructure or training-layer changes -- it is a design choice about who gets paired.

#### 5. Multiple Challengers (medium-high cost, robust)

Three (or more) Challengers with different specializations. If 3 of 5 raise the same concern, that is a stronger signal than 1 of 1. If they disagree, the disagreement *itself* is information.

Harder than it sounds because Challengers built on the same model can have correlated biases -- multiple sycophants do not add up to honest pushback. The defense only works if the Challengers have *different specializations* (back to #4) or *different evidence bases*.

Some production systems use this for high-stakes decisions. Most do not, because of cost.

#### 6. Eval harness with rubric scoring (high cost, strong)

The closest pattern to "what mature shops actually do":

- Hold out a set of test cases where you know what good challenge looks like (manually scored, or with a ground-truth answer).
- Run the ceremony on those cases periodically.
- Score the Challenger's output against a rubric: did it catch the issue, raise spurious concerns, was its evidence specific?
- Track the score over time. Regressions trigger investigation.

Production tools: **Braintrust**, **LangFuse**, **Humanloop**, custom in-house harnesses. The evaluator is sometimes a separate LLM (LLM-as-judge), sometimes human raters, sometimes both. LLM-as-judge has its own problems (judges can have similar biases to the agents being judged), so the gold standard is human raters on a sample with LLM-as-judge for volume.

This is the missing piece named in Appendix C as priority #1 for Lifspel.

#### 7. Architectural mitigations (varied)

Several patterns from research and production:

- **Time-separated review.** Run the Challenger pass in a different session/context from the Generator's work, so the Challenger does not anchor on what was just produced.
- **Hold out Generator's identity / framing from the Challenger.** The Challenger sees the artifact, not the discussion that produced it. Reduces "agreeing with the user" pleasing-bias direction.
- **Asymmetric reward.** Reward the Challenger for finding real flaws, penalize for missing them. Mostly model-training-layer territory.
- **Adversarial generation paired with the Challenger.** A separate process tries to fool the Challenger with manufactured-rigor outputs; if it succeeds, the system is worse than naive. Used in eval pipelines at major labs.

#### 8. Training-layer mitigations (out of application's hands)

RLHF and Constitutional AI approaches that train models to push back rather than agree. Anthropic's published work on Constitutional AI and on sycophancy-reduction is at this layer. From an application's perspective, the choice is which model you use -- Anthropic models tend to be less sycophantic than the average; recent Claude versions specifically target this. Opus 4.7 is broadly a better behavioral target than older models on this dimension.

### What is well-solved vs. not

**Well-solved (production-grade defenses exist):**

- Theatrical adversariality on tasks with mechanizable ground truth -- tool-grounded verification (#3) handles it.
- Theatrical adversariality where you can build an eval harness -- measurement (#6) handles it.
- Theatrical adversariality where you have domain experts with disjoint evidence -- specialization (#4) handles it.

**Not well-solved:**

- Purely judgment-shaped questions where ground truth is not available and you cannot pair domain experts. There is no clean defense beyond the weak ones (#1, #2).
- Sycophancy at the model layer. Despite training-time work, it persists, especially under user pressure.

### How this maps to Lifspel

| Defense | Lifspel state |
|---|---|
| #1 Anti-pattern prompting | Present (`/pnp`, `/plan-review` Challenger prompt, Idris-Lena prompt) |
| #2 Asymmetric prompting | Partial (Challenger prompts say "pressure-test"; could be sharper) |
| #3 Tool-grounded verification | **Strong**, both directions. Claim-of-work via Verification Rule and Promise Verifier. Claim-of-concern via the verifiable-artifact requirement (rev. 8) extended across all five Challenger surfaces: `/pnp`, `/act-as-agent` Idris-Lena Challenger prompt, `/plan-review --pnp` Challenger prompt, `/thread-assessment` close-decision PNP, and Marcus's self-PNP. Concerns without a citable artifact are dismissed as theatrical adversariality. |
| #4 Domain-grounded specialization | Strong (Idris-Lena evidence-disjoint) |
| #5 Multiple Challengers | Absent (one Challenger per ceremony) |
| #6 Eval harness | Absent (named as priority #1 in Appendix C) |
| #7 Architectural mitigations | Mostly absent |
| #8 Training-layer | Using Opus 4.7 (best in class on this dimension) |

Lifspel is strong on #1, #3 (both directions, as of rev. 8), #4, #8. Weak on #2, #5, #6, #7.

The biggest remaining leverage move is **#6 (eval harness)** -- it is what actually *measures* whether the other defenses are working. Without it, the architecture is flying blind on whether Challenger ceremonies are producing real challenge or theater.

### Implementation roadmap

The eval harness expands in three phases. Each phase is independently shippable and produces signal that justifies (or doesn't justify) investing in the next.

#### Phase 1: lightweight rubric + partner sampling -- shipped 2026-04-28 (rev. 9)

The minimum viable measurement layer. A six-criterion rubric scored on a 5-point scale with per-criterion anchored point descriptions, applied by partners to a sample of 3-5 Challenger outputs across the five surfaces on a regular cadence (weekly to start). Eval reviews are saved as structured markdown files in `docs/chatlogs/` so they sit alongside other chatlogs and are browsable in The Stacks.

Lives at `docs/standards/challenger-output-rubric.md`.

Phase 1 produces ordinal quality signal: are the recently-shipped defenses (verifiable-artifact requirement, Idris-Lena auto-spar, Marcus's self-PNP, etc.) actually delivering better outputs than the pre-adoption baseline? The answer is currently unknown; Phase 1 starts collecting the data.

Phase 1 also produces an organic test corpus: every Challenger output reviewed by a partner is a candidate test case. After several months of weekly sampling, the accumulated corpus is the natural input to Phase 2.

#### Phase 2: smoke-test corpus + before/after testing -- deferred

The next step up. A curated test corpus of representative Challenger inputs (Generator outputs needing challenge), with reference outputs scored on the rubric. Run the corpus before a change (the baseline), make the change (e.g. add on-the-fly specialists), run the corpus again (the post-change), compare scores. Quantitative pre/post measurement.

Real costs to be honest about:

- **Building the test corpus is substantial work.** Curating 30+ representative cases per surface, hand-rating reference outputs or hand-validating challenges, periodic refresh as the system evolves. Hours of partner time, not minutes.
- **Static corpora drift.** Cases representative today may not represent cases six months from now. Refresh cadence is its own decision.
- **The judge is still LLM-based or partner-based.** Whether scoring is automated or manual, the same correlated-bias caveats apply. Ground truth (when available) helps; when unavailable, rubric-based judgment is the ceiling.
- **Smoke tests reveal deltas, not absolute quality.** A higher post-change score tells you "X is better than Y," not "X is good." Absolute quality requires the rubric anchored to what "good" actually means.
- **Confounding.** If you run Phase 2, change multiple things between baseline and post-change measurements, you cannot cleanly attribute deltas to any single change. Discipline: change one thing at a time when measurement matters.

Phase 1 is the prerequisite for Phase 2 because the organic corpus from Phase 1's sampling is the cheapest source of representative cases. Building a corpus from scratch without Phase 1 data is guessing at what cases matter.

The discipline at the rubric and log layer in Phase 1 (consistent format, appendable structure, every review producing a candidate test case) is a small design choice that makes Phase 2 substantially cheaper later.

#### Phase 3: full eval harness + automation -- further deferred

Production-grade infrastructure: automated rubric scoring (LLM-as-judge), regression tests on every prompt change, A/B framework for comparing prompt variants, divergent-domain dual judges where evaluations are multidimensional, on-the-fly specialized agents with detection-orchestrator routing for topics outside the standing pairs.

This is what shops like Braintrust, LangFuse, and Humanloop sell as products. At Lifspel scale, it is overkill until Phase 2 has produced enough signal to justify the investment.

The two named refinement candidates from rev. 8 sit here:

- **Divergent-domain dual judges.** Multi-Challenger pattern (Appendix D #5) applied to the eval layer. Useful for genuinely multidimensional evaluations (e.g. code-review-eval + security-eval on the same change). Less useful for unidimensional evaluations (e.g. "did the Challenger cite artifacts?" -- one judge is fine). Real cost increase; honest fit-vs-cost question per evaluation type.
- **On-the-fly specialized agents with genuinely-different-evidence injection.** The Idris-Lena fallback when their domain pairing does not fit a topic. Practical shape: a detection orchestrator routes the topic to a specialist whose spawn config includes at least one of {RAG corpus, tool access, evidence the Generator does not have}. The discipline that prevents collapse to specialization theater is enforcing the genuinely-different-evidence injection at spawn time -- not just a different system prompt label. Validation that the on-the-fly specialist is producing real specialization vs. theater requires the eval harness to compare on-the-fly outputs against generic-Claude-with-same-prompt outputs.

### Shape analysis: which agents benefit from in-loop self-sparring

When extending self-spar capability beyond Marcus (rev. 11), the gating analysis is shape-fit per agent, not "all agents should have it." The honest mapping at the time of the rev. 11 ship:

| Agent | In-loop self-spar fit | Reason |
|---|---|---|
| **Marcus Kowalski** | **Yes -- shipped rev. 11** | Substantive code/architecture decisions in his auto-fix workflow; clear Generator-shape; existing Verification Rule and POLITE-NOT-PLEASING discipline already in his system prompt provided the foundation to build on |
| Diane Pemberton | No | Consolidates and coordinates rather than generates. `/plan-review --pnp` already provides her a Challenger pass on the consolidation surface that matters; in-loop self-spar would be redundant |
| Zoe Kimura | No | UIX audits are evaluative-shaped, not Generator-shaped. Adding self-spar to evaluation work would compound her existing Challenger function rather than provide a divergent specialist perspective |
| Dani Fenn | No | Already adversarial by design. Her Generator-shape is "find security/quality issues"; sparring her output is closer to dual-judge eval (Phase 3 territory), useful but heavier and outside the current shape-fit |
| Dr. Lena Vasik | No | Already paired with Idris via `/act-as-agent` Idris-Lena auto-spar. The standing pair IS the SPARRING pattern for her work |
| Idris Harmon | No | Same as Lena -- already paired |
| Promise Verifier | No | Verification-shaped, not decision-shaped. The agent verifies claims; sparring does not apply |

**The gating heuristic**: an agent benefits from in-loop self-spar when (a) it has a Generator-shape (proposes evaluations, fixes, or recommendations), (b) the topics it produces are substantive enough that two divergent specialists would surface concerns the agent alone wouldn't, and (c) it does not already have a Challenger pass via existing infrastructure.

If an agent fails any of (a), (b), or (c), in-loop self-spar adds cost and safety surface without quality gain. Adding the Task tool to such an agent's kit reflexively is a category error of "more is better" -- the discipline is matching the change to the agents that actually need it.

**Two paths to expand if the analysis above turns out to be wrong** for another agent in the future:

- **Per-agent extension**: add Task tool + sub-agent templates + spar discipline to that specific agent's `*-agent.js` runner. Same shape as Marcus's rev. 11 change. Cost: ~30-45 minutes per agent.
- **Library function**: refactor `sparAgents` definition and the SELF-SPAR DISCIPLINE rule into a shared helper that any agent's runner can import. Larger refactor; the right move if 3+ agents end up needing it.

### Implementation caveats (current code)

The Marcus self-spar implementation (rev. 11) assumes the Claude Agent SDK's spawning tool name is `Task`, based on standard SDK convention. If Marcus hits an unknown-tool error on his first invocation with the spar trigger active, the fix is renaming `Task` to whatever the SDK exposes in this version (likely `Agent` if the SDK aligns with Claude Code's tool naming). The structural design -- sub-agent templates, system prompt rule, persona docs -- is unaffected by that name. Worth running Marcus on a real audit with a spar-triggering condition before relying on this in production.

A related uncertainty: when sub-agents are spawned via Task, they have their own tool kits (defined in the `agents` parameter) and their own hook posture. The parent's safety hooks do NOT automatically apply to sub-agents' tool calls. The current sub-agent definitions restrict tools to `Read`, `Grep`, `Glob` (read-only) so the safety surface is bounded -- sub-agents cannot edit files or run bash commands. If sub-agents are later given write capability, hooks should be revisited.

### The discipline that prevents premature investment

The temptation, at every phase, is to build the more exciting next phase before the current phase has produced enough signal to justify the expense. The discipline:

- Phase 1 must produce signal -- consistent rubric application, accumulated reviews, observable trends -- before Phase 2 is worth building.
- Phase 2 must reveal a clear gap -- baseline scores show the current architecture isn't producing the quality we want, AND a specific candidate change is hypothesized to close the gap -- before Phase 3 components are worth building.
- The detection orchestrator only gets built when there is observable routing pain (topics consistently falling outside standing pairs).
- On-the-fly specialists only get built when standing pairs are demonstrably failing on some topic AND the eval harness can validate the on-the-fly specialist is real specialization vs. theater.
- Divergent-domain dual judges only get built when single-judge measurement reveals genuinely multidimensional cases that warrant the cost.

This is conservative. The risk it manages: building exciting infrastructure (orchestrator, specialists, dual judges) without validated foundations produces a system that *looks* sophisticated and produces convincing-looking outputs without anyone being able to tell whether it's actually doing better than a single Claude. That is a structural-level instance of the same compounding-failure pattern the framework's individual-decision discipline targets (pleasing-shaped self-validation at the architecture level), applied to architecture rather than to individual outputs.
