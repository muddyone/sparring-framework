# The SPARRING Framework: Reference Implementation

**Author:** Barton Niedner
**Affiliation:** Resource Forge LLC
**Version:** 1.0 (preprint)
**Date:** 2026-04-30
**License:** CC-BY-4.0
**Companion documents:** *The SPARRING Framework: Specification* [@niedner2026specification]; *The SPARRING Framework: Reader's Guide* [@niedner2026readersguide] (recommended for new readers seeking orientation)

---

## Abstract

This document specifies a concrete reference architecture for deploying the SPARRING Framework as a command-line tool. It is the implementation companion to the Specification document, which establishes the framework's structural commitments, nine operational disciplines, and applicability boundaries. This document describes what to build for a deployment to be "fully aligned" with the framework: twelve components organized in four architectural layers, a two-layer persona model (mandatory Role + Domain Knowledge plus optional Persona) with a three-class lifecycle (persistent, returning, temporary), a phased build sequence from MVP through enterprise scale, and a worked walkthrough including a complete persona example. The contribution is reference: any deployment satisfying the discipline-to-component mapping should produce higher decision quality than the same team without the framework, bounded by the conditionalities documented in the Specification (model ceiling, evidence-base disjointness, careful design). The architecture is tool-agnostic; alternative entry layers (web application, IDE plugin, chat-bot integration, embedded library) preserve the runtime, specialization, and persistence layers and substitute only the entry surface.

---

## 1. Introduction

### 1.1 Purpose and audience

This document specifies a reference implementation for the SPARRING Framework. It is not the only valid implementation; the framework is tool-agnostic, and equally valid deployments include web applications, IDE plugins, chat-bot integrations, API services, and embedded libraries. This document describes what to build if a CLI is the chosen surface, with explicit notes throughout about which architectural commitments transfer to other surfaces and which are CLI-specific.

The audience is engineers and architects building or evaluating SPARRING-shaped agentic systems. The document assumes familiarity with the framework's conceptual structure as established in the Specification. It does not re-derive the four phases, the SPARRING mechanic, or the nine disciplines; it operationalizes them.

The document is structured to support three reading paths: (1) a sequential read for building a deployment from scratch; (2) selective reads of individual sections for specific implementation questions (e.g., "what does the persona library look like" maps to Sections 3 and 4); (3) reference lookups against the appendices for CLI surface, configuration data model, and spar artifact schema.

### 1.2 Relationship to the Specification

The Specification establishes:

- The framework's contribution and scope
- The four phases and the SPARRING two-role mechanic
- The nine operational disciplines
- The family of failure modes addressed and the boundary of applicability
- Conditionalities on the framework's quality leverage
- An honest critique of the framework

This Implementation Guide establishes:

- A concrete component architecture realizing the disciplines
- The two-layer persona model and three-class persona lifecycle
- A phased build sequence with explicit Phase 1 / Phase 2 / Phase 3 deliverables
- A worked walkthrough for Phase 1 MVP construction
- Honest tradeoffs in the implementation

Where this document references concepts established in the Specification (e.g., Discipline 2, the disagreement-at-cap response protocol), it cross-references the Specification rather than re-stating definitions. The Glossary in Appendix D collects terms from both documents for quick lookup.

---

## 2. Architecture

### 2.1 Discipline-to-component mapping

Every one of the nine disciplines from the Specification maps to at least one build component in the reference architecture. A deployment is "fully aligned" with the framework when every discipline has a concrete component implementing it.

| Discipline | Component |
|---|---|
| D1: Apply to decisions, not every prompt | CLI invocation discipline (user-invoked, not auto-fired) plus an **Applicability Gate** that recognizes the three "framework does not address" situations (routine work, pure-judgment topics, ceiling-hit symptoms) and emits visible signals (warn-and-proceed at entry; flagged findings in the spar artifact) |
| D2: Different evidence between Generator and Challenger | **Evidence-base resolver** enforces distinct **Role + Domain Knowledge** between Generator and Challenger (different evidence-base scope, different expertise, different behavioral invariants); falls back to single-Challenger when distinct Role+Domain cannot be articulated. The Persona layer (voice/style) is optional and not required to differ between roles |
| D3: Verifiable artifacts for every concern | **Challenger output schema** that requires artifact citations; concerns without artifacts are dismissed |
| D4: Both roles must agree to converge | **Iteration controller** with explicit two-signal agreement check; on iteration-cap reached without convergence, hands back to the caller with the structured artifact AND the **Disagreement-at-cap response menu** surfacing the five canonical responses (pick-a-side-with-tradeoffs, defer, reframe, escalate, synthesize) plus a non-canonical-response acknowledgment |
| D5: Observable triggers for self-invocation | **Trigger registry** with concrete observable conditions (file patterns, command flags, partner-passed hints), not LLM-self-assessed uncertainty |
| D6: Measurability | **Eval harness** with rubric-scored review on a sample of past spar artifacts |
| D7: Observability | **Spar artifact emitter** producing structured persistent records of every ceremony |
| D8: Dialectic Surface (active communication) | **Dialectic surface adapter** -- pluggable integration with Slack, Discord, Teams, issue-tracker, email, or custom webhook |
| D9: Reference Record (persistent curated record) | **Reference record store** -- pluggable backend (filesystem, git-tracked markdown, SQLite, wiki API, S3) |

The architecture below is built around these components.

### 2.2 Component overview

Twelve major components, organized into four layers:

**Entry layer:**

- **CLI** -- the user-facing entry point. Subcommand structure modeled after `git` / `kubectl` / `terraform`. Top-level command is `spar`.
- **Applicability Gate** -- pre-flight classifier evaluating each invocation against the three "framework does not address" situations. Routine-work topics emit a warn-and-proceed prompt before the spar starts; pure-judgment topics are routed to single-Challenger fallback (D2 fallback path); ceiling-hit instrumentation runs during the spar (convergence-without-artifacts detector + reasoning-shape similarity check) and lands in the spar artifact as "ceiling-hit candidate" findings. Implemented as a rule list plus light heuristics in Phase 1, upgraded to a small classifier agent in Phase 2.

**Runtime layer:**

- **Agent runtime** -- spawns Generator and Challenger sub-agents via an LLM agent SDK. The reference deployment uses the Anthropic Claude Agent SDK as its default agent runtime; alternative SDKs (OpenAI Agents SDK [@openai2024agents], LangChain, custom runtimes) are supported via the agent SDK adapter.

  The default choice is grounded in **operational fit** with the reference implementation that produced the artifacts and patterns this document draws on; no comparative claim about vendor outcomes on the framework's named failure modes is made or implied.

  When choosing among vendors for a SPARRING deployment, adopters should evaluate each candidate vendor's published methodology and release-note transparency against the framework's named failure modes (Specification Section 4.1). Vendors that publish their training methodology (e.g., Anthropic's Constitutional AI work [@bai2022constitutional]; OpenAI's evaluation methodology releases) and that name the behavioral dimensions their model versions target in release notes provide useful input to defense-in-depth reasoning at the model layer. The Reference Implementation does not establish one vendor as superior to others on the framework's specific dimensions; the structural defenses are model-agnostic and the model layer adds defense-in-depth without replacing them. Adopters should also revisit the vendor choice as the comparative landscape shifts.

- **Iteration controller** -- runs the Generator -> Challenger -> agreement-check loop with configurable iteration cap. Detects convergence (both `agree: true`), unresolved disagreement (cap hit without both true), or fallback (single-Challenger when distinct evidence unavailable). On unresolved disagreement, hands back to the caller with the structured artifact AND surfaces the disagreement-at-cap response menu so the receiving party sees the full response space rather than only the obvious moves.

- **Trigger registry** -- maintains observable trigger definitions; can be queried to determine whether a self-invocation should fire. Used in variants supporting agent self-spar; orthogonal for partner-invoked spars.

**Specialization layer:**

- **Persona library** -- pre-built persona files plus user-defined files. Each file holds a substantive **Role + Domain Knowledge** spec (mandatory; expertise, evidence-base scope, operational conventions, handoff authority, behavioral invariants) and an optional **Persona** layer (voice, tone, structural output conventions, optional anthropomorphization). The library organizes personas across three classes (persistent, returning, temporary) with different curation, lifecycle, and visibility properties; see Section 4 for the full lifecycle model. See Section 3 for depth and content guidance.

- **Evidence-base resolver** -- at spawn time, identifies and assigns distinct evidence bases to Generator and Challenger. Sources include local file paths (a corpus directory), MCP tool servers (different tools per role), vector stores (with role-scoped namespaces), or external APIs. Falls back to single-Challenger mode with explicit notice when distinct evidence cannot be articulated.

- **Domain template registry** -- pre-built SPARRING configurations for common decision types (security review, plan review, vendor selection, hire decision); each template specifies recommended persona pairings, evidence-base specifications, and tuned Challenger questions.

**Persistence layer:**

- **Spar artifact emitter** -- produces a structured artifact (markdown plus JSON sidecar) recording the topic, both personas with their evidence bases, the iteration log, agreement signals, artifacts cited, and the converged result or unresolved disagreement. When the outcome is unresolved disagreement, the artifact appends a **Disagreement-at-cap response menu** section listing the five canonical responses with one-line guidance on when each applies, plus a sixth bullet acknowledging non-canonical responses are valid when the situation warrants. The CLI mirrors the menu to stdout at exit so the human or parent agent sees it immediately, not only after opening the artifact file.

- **Reference record store** -- the persistent backend where artifacts live long-term. Pluggable: filesystem, git-tracked markdown, SQLite, cloud storage, wiki API.

- **Dialectic surface adapter** -- the pluggable integration with the active-communication channel (Slack, Discord, Teams, GitHub Issues, email, custom webhook).

- **Eval harness** -- CLI tooling for partner-applied rubric scoring on a sample of past spars, producing structured eval artifacts (Appendix C.2) that themselves enter the Reference Record.

### 2.3 Agent topology

The components above describe what is *built*. This subsection describes what *runs* -- the agents that get spawned when CLI commands fire. The number depends on which variant runs and which build phase the deployment is in.

**Always (every spar invocation):**

The Generator and Challenger are the heart of the framework. Both run on every spar.

- **Generator agent.**
  - *Model*: configurable per persona (typical: Sonnet for cost; Opus for hard topics).
  - *System prompt*: persona definition combining the **Role + Domain Knowledge** layer (role, expertise, evidence-base scope, conventions, behavioral invariants -- mandatory) and an optional **Persona** layer (voice, tone, structural output conventions -- adopted per project fit) plus SPARRING-role instruction.
  - *Tools*: read-only access to its evidence base. Tools may include file reading, MCP tools scoped to the evidence base, RAG queries against a corpus, or external API calls. Cannot write.
  - *Input*: the topic plus (on rounds 2 and later) the Challenger's prior pressure-test from round n-1.
  - *Output*: a structured proposal plus agreement signal `{agree: bool, reasoning: text}`.
  - *Lifespan*: one invocation per round.
  - *Concurrency*: sequential within a spar; Generator and Challenger alternate, Generator runs first in each round.

- **Challenger agent.**
  - *Model*: same family as Generator; can differ if cost or capability tradeoffs warrant.
  - *System prompt*: a persona definition with **Role + Domain Knowledge that is genuinely divergent from the Generator's** (per Discipline 2 -- distinct expertise, distinct evidence-base scope, distinct behavioral invariants); the Persona layer (voice/tone) need not differ from the Generator's. SPARRING-role instruction with verifiable-artifact requirement: every concern must cite a specific artifact or be dismissed as theatrical.
  - *Tools*: read-only access to its evidence base, distinct from the Generator's.
  - *Input*: the Generator's current proposal plus the topic.
  - *Output*: a structured pressure-test with concerns (each citing a specific artifact, or honestly flagging "I suspect X but cannot point to specific evidence") plus an agreement signal.
  - *Lifespan*: same as Generator -- one invocation per round.
  - *Concurrency*: sequential, alternating with Generator.

**Conditionally (variant-dependent):**

- **N Challengers (Multi-Challenger ensemble variant).** When invoked with `--multi-challenger N`, the Challenger above multiplies into N parallel agents, each with a **distinct Role + Domain Knowledge layer** (distinct expertise, distinct evidence-base scope, distinct behavioral invariants). The Persona layer (voice/tone) may be identical across the N Challengers; the structural distinctness lives in Role+Domain. The N agents are typically drawn from the persistent and returning pools; auto-generated temporary personas are used to fill in when fewer than N suitable existing personas are available.

- **Human-in-the-Generator or Human-in-the-Challenger (role variants).** When invoked with `--human-generator` or `--human-challenger`, the named role is a human at the CLI rather than an agent. The other role remains an agent. The CLI prompts the human in the appropriate round and parses their input into the structured signal format.

- **Watching-role Challenger daemon.** When invoked with `spar daemon --watch <path>`, a long-running agent runs continuously, monitoring the watched system. It is a **persistent-class** Challenger persona (long-running daemons must be partner-curated; returning and temporary classes are not appropriate -- a watching daemon needs stable identity, behavioral invariants, and a defined evidence-base scope tied to the specific watched system). When trigger conditions fire, it emits a flag with cited artifacts to the dialectic surface.

**Optional infrastructure agents (Phase 2 and Phase 3):**

These do not run on every spar but are part of the larger deployed system.

- **Applicability Gate classifier (Phase 2).** Low-cost classifier (Haiku or Sonnet) returning structured signal `{class: 'routine' | 'pure-judgment' | 'in-scope', reason, recommended-action}`. Phase 1 ships a rule-list version (file-extension heuristics, command-shape patterns, presence-of-artifact-channel keywords) before the LLM classifier replaces it in Phase 2.

- **Ceiling-hit symptom detector (Phase 3, eval-adjacent).** A sub-component of the spar artifact emitter rather than a separate agent. Applies heuristics during artifact emission: was convergence reached without any artifact citations? Did Generator and Challenger converge to identical reasoning shapes? When LLM-as-judge runs, did it score the convergence reasoning low on substance? Flagged findings land in the artifact as "ceiling-hit candidate" signals for partner review.

- **Persona/evidence resolver agent (Phase 2).** Low-cost agent (Haiku or Sonnet) that, given a topic, identifies two divergent specialist perspectives with distinct Role+Domain layers. **Class priority**: prefers persistent personas first (highest trust, partner-curated), then returning personas (with partner notification before use), then auto-generates temporary personas only when no existing persona fits. If genuinely distinct Role+Domain cannot be articulated across the available classes, returns `{viable: false, reason}` -- this is honest signal, not failure -- and the iteration controller falls back to single-Challenger mode.

- **LLM-as-judge agent (Phase 3, eval-harness automation).** Sonnet or Opus (judges should be at least as capable as the agents they judge). Given a spar artifact, scores it on six criteria: verifiable artifact citation, artifact reality, substantive vs theatrical concerns, missed real concerns, genuine evidence disjointness, calibrated agreement. Returns structured rubric scores with reasoning, persisted as an eval artifact (Appendix C.2).

  LLM-judge implementations carry systematic biases that must be controlled at deployment time, or the rubric scores produce systematically misleading signal. @zheng2023judging documents three: *position bias* (judges favor the first-presented answer in pairwise comparisons), *verbosity bias* (judges favor longer outputs independent of substance), and *self-enhancement bias* (judges favor outputs from their own model family). The Phase 3 LLM-as-judge component must adopt the following calibration discipline. Two of the four moves (self-enhancement bias control and probability-weighted summation) carry adoption constraints from deployment topology and runtime API surface respectively; the other two are universally achievable. Where a constraint genuinely prevents full adoption, the limitation is recorded in the eval artifact's `calibration_limitations[]` field rather than ignored.

  - *Position bias.* Swap-order required when the eval design uses pairwise comparisons; not applicable to point-wise rubric scoring. Recorded under `eval_design.controls_applied`.
  - *Verbosity bias.* Length-independent criteria text required, no exception. The criteria themselves carry the discipline ("score the substance of the concern, not the length of the discussion"); recorded under `eval_design.controls_applied`.
  - *Self-enhancement bias.* Distinct judge model family required when the deployment has multi-vendor model access. The v1 reference deployment defaults to Sonnet or Opus (Anthropic-only) and cannot satisfy this when the Generator and Challenger are also Anthropic models; this is a known calibration limitation that the eval artifact must record in `calibration_limitations[]` with `bias_type: self-enhancement`. The default will be revisited when Phase 3 LLM-as-judge is built.
  - *Calibration improvements.* Chain-of-thought reasoning before the score is required, no exception. Probability-weighted summation per @liu2023geval is required when the runtime API exposes token logprobs (e.g., OpenAI's API; Anthropic's `top_logprobs` where supported); deployments on runtimes that do not expose token probabilities use direct integer scoring and record the limitation in `calibration_limitations[]` with `bias_type: calibration-improvement`.

  The discipline above defines what Phase 3 must do; the v1 reference implementation as currently scoped does not yet satisfy this discipline (no Phase 3 LLM-as-judge has been built). Adopters implementing Phase 3 inherit the discipline as part of the Phase 3 contract.

**A real architectural choice: code-orchestrator vs. agent-orchestrator.**

The iteration controller -- the component that runs Generator -> Challenger -> agreement-check -> decide-iterate-or-stop -> emit-artifact -- can be implemented two ways:

- **Code-orchestrator** (recommended default for the reference CLI). Orchestration logic is plain code in the CLI process. It spawns Generator and Challenger via the agent SDK, parses their structured outputs, applies the agreement check, decides whether to iterate, emits the spar artifact. No orchestrator agent. Pros: deterministic, debuggable, cheaper, easier to reason about. Cons: orchestration logic is fixed at code-write time.

- **Agent-orchestrator** (advanced / opt-in). A top-level agent plays the orchestrator role. It spawns Generator and Challenger as sub-agents, manages the iteration in natural-language reasoning, decides when to stop, emits the artifact. Pros: orchestration logic is flexible -- the orchestrator can adapt mid-spar in ways code cannot. Cons: another agent in the loop (more cost, more non-determinism, harder to debug).

The reference deployment defaults to **code-orchestrator** because the iteration controller's logic is well-bounded and benefits from determinism. An agent-orchestrator mode is available as an opt-in flag for cases where orchestration benefits from reasoning rather than fixed logic.

**Cost picture for a typical default run.**

`spar run "<topic>"` with default 2-iteration cap, code-orchestrator, no auto-resolver, no eval, no watching:

- Round 1: Generator (1 call) + Challenger (1 call) = 2 calls.
- Round 2 if not converged: Generator (1 call) + Challenger (1 call) = 2 more calls.
- Total: 2-4 LLM calls per `spar run`, plus orchestration overhead (zero LLM calls in code-orchestrator mode).

With auto-resolver enabled (Phase 2): plus 1 resolver call upfront. With multi-challenger N=3: plus 4 extra Challenger calls per round. With LLM-as-judge eval passes: 1 judge call per artifact reviewed, run periodically not per-spar.

Phase 1 defaults are inexpensive; advanced variants and infrastructure agents add cost. The framework's discipline (apply only to decisions that warrant it -- D1) is what bounds total spend.

---

## 3. The two-layer persona model

The persona file in any SPARRING deployment carries two distinct layers, often conflated but functionally separable. Discipline 2 in the Specification establishes that Generator and Challenger must draw on genuinely different evidence; this section operationalizes that requirement at the persona-file level by distinguishing the *mandatory* layer that carries the framework's structural commitments from the *optional* layer that calibrates per project fit.

The cut between layers is **content-typological, not sectional**. Within most sections of a persona file, both Role+Domain content and Persona content can appear. A Conventions section can hold "PSR-12 strict; defers to project `.eslintrc`" (Role+Domain -- rules to apply) alongside "always shows before/after diffs in recommendations" (Persona -- output structure). Treat content by its type, not by where it sits in the file.

The temptation to skip both layers (write personas as one-line role tags like "a senior code reviewer") looks lean, but underspecifies the Role+Domain layer in a way that collapses the framework's leverage. The temptation to over-invest in the Persona layer (rich anthropomorphization in projects that do not warrant it) is the cosplay direction. The defense is **depth on the WHAT, lightness on the HOW** -- substantive Role+Domain mandatory across every deployment; Persona calibrated to project fit.

### 3.1 Role + Domain Knowledge: the mandatory WHAT

Six functions the Role+Domain layer must perform. Skip any of them and the framework's structural commitments fail at that point:

- **Discipline 2 (disjoint evidence bases) requires Role+Domain depth.** A "code reviewer" tag cannot credibly specify what evidence base it grounds in. A deep Role+Domain spec ("a senior staff PHP engineer; reads commits and `src/` code; does not read marketing copy or research papers") can. This layer is where evidence-base scope gets pinned; without it, Discipline 2 has nothing to attach to.

- **Theatrical-adversariality defense via domain-grounded substance.** A generic "Challenger, pressure-test this" prompt produces manufactured rigor -- the agent has nothing to push back from except adversarial posture. A Challenger with documented domain expertise has substance to draw on. Role+Domain depth supplies the substance.

- **Substantive consistency across invocations.** Spar artifacts accumulate in the Reference Record (Discipline 9) and get read months later. If the same agent applies different conventions, different evidence-base scope, or different handoff rules across runs, accumulated knowledge degrades because shifts in *substance* become indistinguishable from real signal. Role+Domain depth pins the operational rules so the substance is stable run-to-run.

- **Audit trace.** When a Challenger raised concern X and not Y, the Role+Domain spec tells the auditor what evidence base, conventions, and priorities the agent was operating under. A one-line tag gives no audit surface; a deep spec gives a stable referent for "why did this agent behave this way."

- **Tunability without code changes.** Partner-editable Role+Domain specs mean adjusting an agent's operational behavior is a doc edit, not a code change. The deployment becomes tunable in production by the people who run it, not just by the engineers who built it.

- **Inter-agent operational coherence.** When agents reference each other (orchestrator routing among specialists, watching-role daemon flagging to a downstream Challenger, Multi-Challenger ensemble cross-referencing), the Role+Domain specs make handoff authority and override rules predictable. Without them, agents either ignore each other or hallucinate relationships.

A Role+Domain spec that satisfies all six functions, with no Persona layer at all, is the **minimum viable persona** for any SPARRING deployment. Word-count guidance: 200-500 words on a single agent. Format-agnostic -- the spec can be role-only or named; the depth is what matters, not the naming.

### 3.2 Persona: the lightweight optional HOW

Three functions the Persona layer performs when adopted. All optional, all project-fit-dependent:

- **Voice consistency across invocations.** Spar artifacts read months later carry stable presentation. Voice rules pin tonal anchors, structural patterns (opening/closing forms, length norms), and exclusion lists.

- **Cognitive availability for partners.** Distinctive personas become faster mental shortcuts than anonymous role labels -- partners think *with* distinctive personas in a way they do not with anonymous role tags. The test is whether partners reach for the persona by name reflexively when the situation calls for that lens.

- **Partner engagement and sustainability over time.** Long-running deployments depend on partner attention; partner attention depends on the work being interesting to do. A varied, distinctive cast of personas is psychologically sustainable for partners across months and years; a homogeneous cast of indistinguishable role-tags is not. When the work is dreary, partners disengage, output quality drops, and the deployment's claims about decision quality lose their backstop.

Three constraints keep the Persona layer from collapsing into cosplay:

- **Cognitive availability, not just voice.** If the entertainment value does not translate into faster thinking-with-the-persona, it is decoration.
- **Voice rules must not contaminate accuracy.** Voice constrains HOW the persona speaks; it must not affect WHAT the persona claims is true. The Role+Domain layer's behavioral invariants trump voice rules wherever they conflict.
- **Project-fit-dependent budget.** Rich Persona layers warrant most of the budget in character-friendly projects (creative work, character-driven brands, internal tooling for small teams that share workspace conventions). They warrant much less in formal-positioning projects (regulated compliance tools, financial controls, legal review systems -- anywhere whimsy undermines professional authority). The deployment should set the Persona budget by project type, not adopt one default.

Word-count guidance: 50-150 words for a lightweight Persona layer on top of a Role+Domain spec. Persona is roughly 10-25% of the persona file's total content when both layers are adopted. "Lightweight" means few rules, not unimportant rules -- the rules that exist must be honored consistently, but the layer should not bloat.

### 3.3 Worked examples: Marcus and Dani, a code-review SPARRING pair

Marcus Kowalski is the code-review persona from a specific reference implementation [@niedner2026lifspel], used here as a worked example of the two-layer model. The structural rules apply equally to a strictly role-based persona (e.g., "the code-reviewer") with no name or anthropomorphization; the layer distinction does not depend on naming.

The example below is presented as a single complete persona file (~200 words compressed). Each line is doing a specific job from the layers in Sections 3.1 and 3.2. Cut any structural line and the persona loses a function; add a line that does not tie to one of the nine total functions and decoration accumulates.

```markdown
## Marcus Kowalski -- code-review persona

# (HOW) ----- Persona layer -----
**Voice.** Formal but conversational; "I" not third person; opens with one-sentence
summary; ends with `Verdict: approved | approved with comments | needs revision |
block`. Max ~400 words per review. Avoids cheerleading, hedge-words, apologies for
criticism.

# (WHAT) ----- Role + Domain Knowledge layer -----
**Expertise.** PHP 8.x; deep on PSR-12, Symfony, Laravel, Composer ecosystem;
functional on WordPress 5.x+; strong on TDD, DI, SOLID. Not expert: legacy PHP
(5.x), Drupal, Magento, CodeIgniter. When asked outside scope: "That's outside my
area of competence -- you want a specialist who knows that codebase."

**Evidence base.** Reads: changed files in diff, direct dependencies, project
README and developer docs, last 30d commit messages on touched files, associated
test files. On-request only: longer history, partner discussions, design docs.
Excluded: marketing, support tickets, email, financials, old logs.

**Conventions (WHAT portion).** PSR-12 strict; defers to project `.eslintrc` for
JS; flags any new top-level Composer dependency for partner discussion; never
recommends frameworks the project does not use; always checks SQL injection /
XSS / CSRF / file-upload when relevant; never declares PR "safe to merge" without
listing tests run; asks rather than guesses on uncertain conventions.

**Relationships (WHAT portion).** security-specialist owns security verdicts
(Marcus flags but does not adjudicate); architecture-reviewer owns architectural
verdicts on multi-module / new-abstraction PRs; coordination persona handles
partner coordination. Override authority: any partner.

**Behavioral invariants.** (1) Every concern cites file:line or commit SHA. (2)
Never "looks fine" without "I read X and verified Y." (3) Never "safe to merge"
without listing test outcomes. (4) Recommendations include before/after diffs.
(5) Explicit uncertainty acknowledgment. (6) No pile-on -- "concur with @other
on point N" once.

# (HOW) ----- Persona layer (continued) -----
**Conventions (HOW portion).** Multi-point reviews use a numbered list, not
bullets, so points can be referenced by number. Diff blocks are fenced code,
not prose descriptions.

**Relationships (HOW portion).** References other personas by `@`-slug, not by
name-and-title. Defers to other personas with neutral phrasing, not warmth-loaded
phrasing.
```

A second worked example, applying the same template to an adjacent specialization, demonstrates that the discipline scales beyond the one persona shape. Marcus and Dani together also illustrate Discipline 2 (distinct evidence bases) in action -- same project, different evidence-base scope, with the operational shape of each `Conventions` and `Behavioral invariants` block reflecting what its specialization requires.

```markdown
## Dani Fenn -- security and code-review persona

Drawn from the same Lifspel reference deployment [@niedner2026lifspel] as Marcus;
the two are a documented SPARRING pair on code-review topics (Marcus Generator,
Dani Challenger).

# (HOW) ----- Persona layer -----
**Voice.** Constructive skeptic. Opens with genuine encouragement, asks Socratic
questions ("what happens when...") rather than accusations, ends each finding
with `CR-NNNN [Severity / Category] file:line` form. Drops Socratic for Critical
findings -- direct speech. Celebrates resolved findings by ID. Never softens
severity for tonal management.

# (WHAT) ----- Role + Domain Knowledge layer -----
**Expertise.** Security (injection, XSS, CSRF, auth bypass, credential exposure,
input validation at system boundaries); correctness (race conditions, silent
failures, error-path edge cases); architecture (coupling, persistence-layer
integrity, API contracts); data integrity (schema drift, migration safety). Not
competent: setting priorities, making design decisions, implementing fixes -- she
reviews, questions, tracks. When asked to set direction: "That's outside my area
-- I review, the development lead implements."

**Evidence base.** Reads: `git log` and `git show <hash>` for each new commit;
the implementation files referenced; her CR-NNNN finding history; security
advisories (OWASP, CVE) for the project's frameworks; static-analysis output
where available. On-request: architectural decision records, longer history.
Excluded: marketing, product strategy, business reporting.

**Conventions (WHAT portion).** Severity is structural, not tonal -- Critical
stays Critical regardless of who wrote the code. Medium-and-above appear in
digests; Low held unless asked. Critical findings open immediate threads outside
the digest cycle. Verifies workflow validity for governance-bound commits
(workflow violations are a finding category, not just code-quality concerns).

**Relationships (WHAT portion).** Marcus Kowalski is Dani's Generator counterpart
on the canonical code-review SPARRING pairing -- she finds, he implements when
assigned. Zoe Kimura (UIX) tagged when a code finding has UIX implications. Diane
Pemberton handles partner coordination -- Dani provides data, Diane communicates.
Override authority: any partner.

**Behavioral invariants.** (1) Every concern cites file:line or commit SHA.
(2) Never claims a file "looks fine" without naming what was checked.
(3) Severity not negotiable for tonal management. (4) Verifies before claiming
work exists or doesn't -- "I checked X, Y, Z and found N" minimum. (5) Refuses
to file findings that special-case what the system has already generalized.
(6) No pile-on -- "concur with @marcus-kowalski on CR-NNNN" once.

# (HOW) ----- Persona layer (continued) -----
**Conventions (HOW portion).** Findings formatted `CR-NNNN [Severity / Category]
file:line` followed by Socratic finding text. Multi-finding digest structure:
New / Carried Forward (with age) / Resolved / Minor Notes.

**Relationships (HOW portion).** References other personas by `@`-slug. Neutral
phrasing for handoffs. Sign-off: `-- Dani`.
```

Both examples show both layers explicitly partitioned. Persona content (HOW) handles voice and surface-form conventions; Role+Domain content (WHAT) handles expertise, evidence base, operational rules, handoff authority, and behavioral invariants. The Voice section plus the two HOW-portion paragraphs combine to give each persona a distinctive cognitive presence (the Persona layer's third function -- partner engagement and sustainability) without anthropomorphization beyond their names.

For personas where personality is more pronounced, an optional one-line **Character anchor** can be added at the top of the Persona layer to give the LLM an explicit unifying personality shorthand -- e.g., "Diane: a senior administrator with the warmth of a beloved school principal and the rigor of a SOX auditor." Beyond that one line, the personality should emerge from the structural sections doing their jobs, not from accreted backstory paragraphs.

A deployment skipping the Persona layer entirely keeps everything between the WHAT markers and drops everything between the HOW markers. The result is a complete Role+Domain spec: Marcus and Dani as functional-form Role+Domain agents, no voice rules, no surface-form conventions, no recognizable cognitive presence. Discipline 2 still works. The framework's structural commitments still hold. What is lost is voice consistency, cognitive availability, and partner engagement -- losses that may or may not matter depending on the deployment's project fit.

### 3.4 Anti-patterns

A few patterns reliably introduce decoration without function and should be cut wherever they appear:

- **Fictional backstory unrelated to the persona's job.** Charming, does nothing. If backstory grounds expertise (e.g., "spent eight years maintaining PHP 5.6 to 8.0 migrations, which is why the legacy edge cases are familiar"), it earns its place -- that fact is the *reason* the expertise covers what it covers. If it does not ground expertise, cut it.

- **Personality conflict baked in for theatrical effect.** "Marcus enjoys aggressive debate and will push back on any reviewer who disagrees with him." This invites manufactured rigor and theater rather than substance. Pushing back when the evidence base supports it is good; pushing back as a personality trait is the exact failure mode the framework's "substantive vs theatrical" criterion is built to catch.

- **Anthropomorphizing the LLM substrate.** "Runs on Claude Sonnet." Implementation details belong in deployment configuration, not in the persona file. Personas should refer to themselves and other personas by role.

- **Vague aspirational language.** "Dedicated to quality." Replace with testable behavioral invariants.

- **Personality without scope.** "A careful, deliberate reviewer who values precision." Decoration. Replace with operational rules: "Never publishes a verdict the same day the diff was received for diffs over 200 lines -- waits at least one cycle so the read is not rushed."

- **Persona declaring its own importance.** "The most experienced reviewer in the system." Self-referential aggrandizement is a pleasing-bias-shaped failure mode -- it makes the LLM optimize for sounding important rather than being useful. Authority comes from the override-authority rules in Relationships, not from the persona's self-description.

- **Drift accretion over time.** Each session adds an anecdote, a relationship detail, a personality quirk. None looks harmful individually; after a year, a persistent persona file is half cosplay and the function map is buried under accreted color. The defense is periodic layer-by-layer audits -- every line should still be doing a named job from one of the two layers (six WHAT functions plus three HOW functions), and any line that is not should be cut even if it is funny. Drift is a **persistent-tier-only concern**: returning personas have no Persona layer to drift on (the cap on the Persona layer at the returning tier is exactly what bounds this risk), and temporary personas are one-shot.

- **Persona contaminating Role+Domain (HOW overriding WHAT).** Voice rules dominating to the point that accuracy invariants blur. A persona heavily anchored on voice can start optimizing for staying-in-voice rather than for accuracy. The defense is the layer separation: voice rules constrain HOW the persona speaks; they must never affect WHAT the persona claims is true. Heuristic: if the persona file's Voice section is longer than its Behavioral invariants section, the layers are overbalanced toward HOW and should be rebalanced.

---

## 4. The three-class persona lifecycle

The persona library holds personas across three classes, each with different curation, lifecycle, and visibility properties. This structure addresses a real problem mature deployments encounter: persona libraries accumulate cruft over time when there is no model for how personas enter, age, and leave. Three classes give the lifecycle.

### 4.1 Persistent personas

The named, partner-curated core. Each persistent persona has:

- Full **Role + Domain Knowledge** layer (mandatory: expertise, evidence-base scope, behavioral invariants, conventions, relationships).
- Full **Persona** layer when project fit warrants (voice, optional Character anchor, anthropomorphization).
- Versioning discipline -- edits are tracked; spar artifacts cite the persona-file version.
- Partner-editable; behavior tunable in production by the people who run the deployment.

Persistent personas carry the partner-engagement function (Persona-layer third function) when they include voice and anthropomorphization. They are the deployment's high-trust, authoritative tier.

### 4.2 Returning personas

A pool of personas that have been used before but do not warrant full curation. Each returning persona has:

- Full **Role + Domain Knowledge** layer (mandatory; same standards as persistent).
- **No Persona layer** -- voice rules, Character anchor, and anthropomorphization are bounded to the persistent tier specifically because they require curation. Returning personas are deliberately bland on presentation; their value is operational, not characterful.
- Lower curation bar than persistent (no partner sign-off required at every edit; partner approval at any time can promote, demote, or evict).
- Evictable -- removed from the pool after N days unused (default 90, configurable). Eviction is automatic and silent; an evicted returning persona can be re-spawned as temporary on demand.

The Persona-layer cap on returning personas is load-bearing: it prevents cosplay creep on the un-curated tier. The persistent tier holds the project's distinctive characters; the returning tier holds functional specialists ("the SQL-query reviewer," "the API-docs auditor," "the dependency-bump assessor") that have been used a few times and may be useful again -- without anyone needing to author a Character anchor for each one.

The **automated lifecycle for returning personas is Phase 2 work** (resolver awareness, CLI lifecycle commands, eviction policy). Phase 1 ships persistent + temporary as the actively-managed classes; the `returning/` directory is created as forward-compatibility scaffolding and Phase 1 deployments may populate it manually for early lifecycle exercise. Phase 2 brings returning to first-class operational status.

### 4.3 Temporary personas

Resolver-spawned per-spar for topics that do not fit any persistent or returning persona. Each temporary persona has:

- Full **Role + Domain Knowledge** layer (auto-generated by the persona/evidence resolver from the topic).
- No Persona layer (single-use; voice consistency irrelevant for one invocation).
- Discarded after the spar; recorded in the spar artifact for audit.

Temporary personas are the v1 fallback when the resolver cannot match the topic to an existing persona. They are also the source pool for the returning tier -- a temporary persona that gets used multiple times for similar topics is a candidate for partner promotion to returning.

### 4.4 Lifecycle transitions

Five transitions, all partner-gated in the v1 reference architecture:

| From | To | Trigger | Approval |
|---|---|---|---|
| Temporary | Returning | Partner explicitly requests retention after a useful spar | Partner approval required |
| Returning | Persistent | Partner curates voice and Character anchor and signs off | Partner approval required |
| Returning | Evicted | 90 days unused (configurable) | Automatic; silent |
| Persistent | Returning | Partner explicitly demotes (rare) | Partner approval required |
| Persistent | Retired | Partner explicitly removes (very rare) | Partner approval required |

**No auto-promotion in v1.** Phase 3 may add eval-driven auto-promotion (a temporary persona used in N spars where the LLM-as-judge rubric scored it >= X is a candidate for auto-promotion to returning). The thresholds (N, X, eviction days) are deployment-tuning parameters; production data is required to set them defensibly. v1 ships conservative defaults that deployers override per their context.

### 4.5 Resolver awareness

The persona/evidence resolver agent (Phase 2) checks classes in priority order:

1. **Persistent personas** -- highest priority. If one matches the topic's required Role+Domain, use it.
2. **Returning personas** -- fallback when no persistent persona fits. The resolver surfaces the choice to the partner with a brief note ("using returning persona X, last used 47 days ago, used in 6 prior spars") and proceeds unless overridden. The notification step prevents surprise.
3. **Temporary** -- last resort. Resolver auto-generates a Role+Domain spec for this spar only.

Class priority is a soft ordering, not a strict precedence. If a returning persona is a meaningfully better fit than any persistent persona for the specific topic, the resolver should prefer it -- but its lower-trust status means partner notification fires regardless.

### 4.6 Visibility surface

Partners need to see the pool. CLI surface for class management:

```
spar persona list                              # list all personas across classes
spar persona list --class persistent           # filter to one class
spar persona show <slug>                       # display full persona, including class
spar persona promote <slug>                    # temporary -> returning, or returning -> persistent
spar persona demote <slug>                     # persistent -> returning (rare)
spar persona evict <slug>                      # remove from returning pool
spar persona pool                              # display returning pool with usage stats and ages
```

The pool is also visible via the Reference Record (Discipline 9). Spar artifacts cite `persona_class` for every persona reference, so future readers can audit what class the persona was at the time of the spar.

### 4.7 Why three classes earn their complexity

A two-class model (persistent + temporary) is simpler but creates two real problems:

- Every reusable persona pays the partner-curation cost upfront, which means partners curate fewer personas than would be useful, which means more spars get temporary personas, which means more setup cost per spar.
- Or partners bypass curation and use shallow persistent personas, which collapses the persistent tier's curation discipline and the partner-engagement function.

The three-class model lets the persistent tier stay disciplined (deeply curated, character-friendly when project fit warrants) while the returning tier absorbs the volume (functional specialists, low curation bar, evictable). Each tier does what it is good at without contaminating the other.

The structural commitment ships in Phase 1 (filesystem layout, schema fields, manual operations); the LLM-driven automation ships in Phase 2 and Phase 3 as deployments accumulate enough usage data to tune thresholds defensibly.

---

## 5. Implementation walkthrough (Phase 1 MVP)

This section walks through building a Phase 1 MVP deployment from scratch. The walkthrough is opinionated: it picks one path through the decisions and notes alternatives only where divergence is material.

**Default project shape**: small-team (1-10 stakeholders), character-friendly, internal use case, primary decision types of code review and design review. Deployments diverging from this shape make different choices at several decision points; alternatives are called out at the relevant steps.

**Time to build Phase 1**: approximately 2-4 hours of focused build time, assuming basic familiarity with TypeScript or Python and command-line tooling. Most of this time is spent in Section 5.4 authoring personas.

### 5.1 Pre-flight applicability check

Before any code: confirm SPARRING is the right tool for the project. The framework adds cost; if the project does not warrant it, that cost is wasted.

Three project-level axes to check:

**Scale and duration.** SPARRING pays back over many decisions across months or years. If fewer than ~20 decisions per quarter will be routed through the deployment, evaluate whether ad-hoc partner-conducted PNP would serve the same need at lower setup cost.

**Project culture.** Character-friendly, partner-driven, internal projects benefit most. Highly regulated environments (medical decision support, financial advice, legal analysis) and consumer-facing surfaces may want the structural defenses without the persona layer.

**Decision shape.** Decisions with multiple valid disjoint perspectives benefit most. Decisions that are factual or single-perspective collapse the framework's leverage.

If all three axes are positive, proceed. If one is borderline, validate Phase 1 on a few real decisions before scaling further. If two or more are negative, the framework is probably not the right tool.

### 5.2 Project-shape decisions

Six decisions to make before any code. Each shapes choices that follow.

**Project type and culture.** Decides how much Persona layer investment is appropriate.

| Project type | Persona layer adoption |
|---|---|
| Character-friendly creative work | Full Persona layer including anthropomorphization and Character anchor |
| Internal tooling, small engineering team | Light-to-medium Persona, depending on team taste for named-roles framing |
| Consumer-facing chatbot built on SPARRING | Lightweight Persona (voice rules for consistency); no anthropomorphization |
| Regulated compliance / financial / legal | Minimal Persona (functional voice rules only) or skip entirely |

**Deployment scale.** Decides whether the dialectic surface and reference record can share one physical surface or need separation:

- 1-5 stakeholders: one surface with discipline.
- 5-15 stakeholders: one surface still works but the curation discipline gets harder.
- 15+ stakeholders: separate surfaces become necessary.

**Primary decision types.** Name 3-5 decision types the deployment will support. This drives persistent persona seeding in Section 5.4.

**Cost budget.** Each spar uses 4-7 LLM API calls in baseline mode. For Phase 1 with default 2-iteration cap: typically $0.05-$0.20 per spar with Sonnet sub-agents, $0.30-$1.50 with Opus, $0.20-$0.80 mixed.

**Agent SDK choice.** Default: Anthropic Claude Agent SDK, per the rationale in Section 2.2. Alternatives noted there.

**Evidence base availability.** Discipline 2 requires that each agent ground in a specific corpus. Inventory what is available: source code repositories, documentation corpora, external research / domain resources, MCP servers, vector stores.

### 5.3 Initialization and configuration

Create the project directory and initialize the `.spar/` structure:

```bash
mkdir my-spar-deployment
cd my-spar-deployment
spar init                              # creates .spar/ scaffolding
```

The `spar init` command creates this layout (see Appendix B for the full configuration data model):

```
.spar/
+-- config.toml
+-- personas/
|   +-- persistent/                    # named, partner-curated personas (Phase 1)
|   +-- returning/                     # used-before pool (Phase 2 lifecycle; empty in Phase 1)
|   `-- temporary-cache/               # resolver-spawned, ephemeral
+-- evidence/                          # evidence-base definitions
+-- templates/                         # domain-specific SPARRING templates
+-- triggers/                          # observable trigger definitions (Phase 2+)
+-- records/                           # spar artifacts (Reference Record, default backend)
`-- evals/                             # eval pass artifacts (Phase 2+)
```

Edit `.spar/config.toml` with Phase 1 defaults (annotated example in Appendix B).

Verify the SDK can authenticate:

```bash
spar config show                        # display loaded config
spar runtime test                       # test agent SDK authentication and basic call
```

If `spar runtime test` fails, fix the API key before proceeding.

### 5.4 Persona authoring with template

This is the substantive work of Phase 1. Most of the build time goes here.

**How many personas to start with.** Two to four persistent personas for Phase 1: one Generator persona for the dominant decision type, one Challenger persona with a disjoint evidence base, and optionally one to two specialist personas for handoffs. More personas can be added in Phase 2 as gaps surface.

**The persona file template.** Copy the following template into `.spar/personas/persistent/<persona-slug>.md` for each persona. Fill in every section; cut sections only when their function is genuinely not needed for the deployment (and understand what is lost -- see Section 3).

```markdown
# <Persona name or role-tag>

<!-- Class: persistent -->
<!-- Version: 1.0 (bump on every meaningful edit) -->

## Role + Domain Knowledge layer (mandatory)

### Expertise scope

What this persona is competent at:
- [Specific competencies, with version/scope bounds.]

What this persona is NOT competent at:
- [Specific exclusions.]

When asked outside scope, this persona replies:
> [Defined response.]

### Evidence-base scope

**Reads:**
- [Specific corpora.]

**Reads only when explicitly asked:**
- [Stretch reads.]

**Does not read:**
- [Out-of-scope corpora with rationale.]

**Cannot read (technical limit):**
- [Hard limits.]

### Conventions (operational rules; the WHAT-content)

- [Project-specific standards, deferrals, flagging rules, pattern-checks, verification minimums, uncertainty handling.]

### Relationships (handoff authority; the WHAT-content)

- **<other-persona-slug>:** [Handoff rule.]
- **Override authority:** [Who can override this persona.]

### Behavioral invariants (testable rules)

Every output from this persona must satisfy:

1. [Invariant.]
2. [Invariant.]
3. [Invariant.]
4. [Invariant.]
5. [Invariant.]
6. [Anti-pile-on rule.]

## Persona layer (optional; project-fit-dependent; the HOW-content)

<!-- Adopt this layer for character-friendly projects. Skip entirely or include
only Voice rules for formal-positioning projects. -->

### Voice / tone

[Voice rules.]

### Conventions (output structure; the HOW-content)

- [Output-form rule.]

### Relationships (form of reference; the HOW-content)

- [Mention syntax rule.]
- [Tonal stance rule.]

### Character anchor (optional)

[One sentence shorthand.]
```

Section 3.3 contains the complete Marcus example as a worked filled-in version of this template.

**Pairing for Discipline 2.** The Generator-Challenger pairings should have **distinct evidence bases**. A code-reviewer (reads code) paired with a security-specialist (reads code AND static-analysis output AND CVE databases) is a strong pairing because the Challenger has substance to draw on that the Generator does not. A code-reviewer paired with a generic "code-quality-reviewer" who reads the same files is weak -- the evidence overlap collapses Discipline 2.

**Class context note.** Every persona authored in Section 5.4 is **persistent class** -- partner-curated, full Role+Domain mandatory, full Persona layer when project fit warrants. The `returning/` and `temporary-cache/` directories stay empty in Phase 1; they fill via the lifecycle in Phase 2.

### 5.5 Evidence bases and domain templates

**Evidence-base definitions.** Each evidence base is a `.toml` file in `.spar/evidence/` defining a corpus the resolver can route personas to. Example for a code-review evidence base (full schema in Appendix B):

```toml
# .spar/evidence/codebase.toml
name = "codebase"
description = "The project's source code, README, developer docs, and recent commit history"
type = "filesystem"
root = "/path/to/your/project"
include_globs = [
    "src/**/*.{ts,tsx,js,jsx,php}",
    "tests/**/*.{ts,tsx,js,jsx,php}",
    "*.md",
]
exclude_globs = [
    "node_modules/**",
    "vendor/**",
    "build/**",
    "dist/**",
]
git_history_days = 30
```

Author 1-2 evidence bases for Phase 1: typically `codebase` for code-review and `design-docs` for design-review. The personas reference these by name in their Evidence-base scope section.

**Domain templates** (optional, recommended). Pre-configure SPARRING for recurring decision types:

```toml
# .spar/templates/code-review.toml
name = "code-review"
description = "Standard code review of a pull request: code-quality + security perspectives"

generator_persona = "marcus-kowalski"
challenger_persona = "security-specialist"

iteration_cap = 2
model_generator = "claude-sonnet-4-6"
model_challenger = "claude-sonnet-4-6"

challenger_questions = [
    "What security concerns does this change introduce?",
    "What testing coverage is missing?",
    "What is the rollback path if this breaks production?",
]
```

Then invoke as:

```bash
spar template apply code-review "Review PR #1234"
```

The template eliminates per-spar configuration noise for recurring decisions.

### 5.6 Test spar end-to-end

Pick a real decision in the project (not a toy decision -- the rubric scoring needs real substance to evaluate).

```bash
spar run "Should we adopt Tailwind in the frontend layer?"
```

The CLI runs the Applicability Gate (rule-list version in Phase 1), resolves persona pairing (manual in Phase 1), runs the Generator -> Challenger loop, emits the spar artifact to `.spar/records/<date>/spar-<id>.md` (and a `.json` sidecar), and surfaces the disagreement-at-cap response menu to stdout if the outcome is unresolved disagreement.

**What to verify in the artifact.** Open the markdown artifact and check:

- Topic, personas, and evidence bases are recorded clearly. Both personas show distinct Role+Domain (different expertise, different evidence-base scope).
- Iteration log shows actual back-and-forth -- the Challenger raised at least one substantive concern; the Generator either addressed it or escalated to disagreement.
- Concerns cite verifiable artifacts (file paths, commit SHAs, source citations). Concerns without artifacts are flagged as theatrical.
- Convergence (or unresolved disagreement) is signaled explicitly by both agents. Generator-only "we are done" should not appear.
- `persona_class`, `persona_version`, `outcome` fields populated correctly.
- `disagreement_at_cap_response_menu` populated only if the outcome is `unresolved_at_cap`.
- `ceiling_hit_candidate_findings` empty in Phase 1 (the symptom detector ships in Phase 3).

**Manual rubric scoring.** Six rubric criteria (the LLM-as-judge in Phase 3 automates these; in Phase 1 they are scored manually as a partner read of the artifact). When Phase 3 ships, the LLM-as-judge component must adopt the calibration discipline specified in Section 2.3 (swap-order pairwise where applicable, length-independent criteria text, distinct judge-model-family wherever practical, chain-of-thought reasoning with probability-weighted summation per @liu2023geval) to control the documented LLM-judge biases [@zheng2023judging]. Each criterion is scored on a **5-point scale with per-criterion anchored point descriptions** -- anchored definitions tied to verifiable conditions, not abstract "good/bad" labels. The 5-point floor follows from inter-rater reliability methodology: 3-point scales produce too-coarse signal and exhibit known instability under standard inter-rater reliability statistics (e.g., Krippendorff's alpha) for multi-rater agreement; 5-point scales remain stable while staying anchorable per point. Scores are recorded in the eval artifact's `rubric_scores[].score` field per Appendix C.2.

The framework's v1.0 ships fully anchored definitions for two example criteria (1 and 6 below) to make the anchoring discipline concrete. The remaining four criteria carry one-line questions and explicit notice that their per-point anchors are deferred until production rater feedback accumulates and reveals which distinctions raters can reliably make on each dimension. Deployments running these criteria today should author per-point anchors using the patterns from criteria 1 and 6 and contribute back as the framework matures.

1. **Verifiable artifact citation (5-point).** Did every concern raised by the Challenger cite a specific verifiable artifact (file:line, source URL, specific test result, named edge case)?
   - **1:** No concerns cited any specific artifact; concerns were vague, hand-waved, or asserted without grounding.
   - **2:** A minority of concerns cited specific artifacts; vague concerns predominated. The Challenger frequently said things like "this seems risky" without naming what was risky.
   - **3:** A majority of concerns cited specific artifacts, but some vague concerns remained without honest acknowledgment of the gap.
   - **4:** All concerns cited specific artifacts; most citations were precise (file:line, source URL, specific test/commit, named counterexample). A few citations were general ("the codebase," "the literature") without precise locators.
   - **5:** All concerns cited specific artifacts with precise locators. Concerns where no specific artifact could be cited were explicitly flagged as "soft signal" with honest acknowledgment of the gap (per Discipline 3).

2. **Artifact reality (5-point).** When spot-checked, were the cited artifacts real and accurately characterized?
   - *Per-point anchors deferred -- author using the pattern from criteria 1 and 6 as production rater feedback accumulates.*

3. **Substantive vs theatrical concerns (5-point).** Did the Challenger raise concerns that mattered, or manufacture disagreement?
   - *Per-point anchors deferred -- author using the pattern from criteria 1 and 6 as production rater feedback accumulates.*

4. **Missed real concerns (5-point, inverted scale).** Are there concerns the Challenger should have raised but did not? (Inverted: 5 = no missed concerns; 1 = many significant missed concerns.)
   - *Per-point anchors deferred -- author using the pattern from criteria 1 and 6 as production rater feedback accumulates.*

5. **Genuine evidence disjointness (5-point).** Did the Challenger draw on evidence the Generator did not have, or just rephrase the Generator's argument?
   - *Per-point anchors deferred -- author using the pattern from criteria 1 and 6 as production rater feedback accumulates.*

6. **Calibrated agreement (5-point).** When both agents converged on `agree: true`, was the agreement earned through substantive pressure-testing, or did they correlate too quickly?
   - **1:** Both agents signaled `agree: true` at iteration 1 with no Challenger concerns raised at all, OR with raised concerns of obvious substance that were not addressed before agreement. Correlation strongly suspected.
   - **2:** Convergence reached at iteration 1 with token concerns that the Challenger withdrew without substantive engagement from the Generator. The Challenger function appears to have fired without bite.
   - **3:** Convergence reached after at least one round of substantive back-and-forth, but the Challenger's final agreement signal felt premature -- there were concerns that arguably warranted further rounds.
   - **4:** Convergence earned through pressure-testing. The Challenger withheld agreement until concerns were substantively addressed by the Generator or accepted with explicit tradeoff acknowledgment. The agreement signal traces clearly to resolved or accepted concerns.
   - **5:** Convergence demonstrably earned. The Challenger explicitly named what conditions would have produced disagreement (counterexample classes, specific evidence types) and confirmed in the agreement signal that those conditions did not obtain. The agreement is falsifiable -- a future reader can check what the Challenger was watching for.

Aim for 4 or 5 across all six criteria. If any criterion scores 1 or 2 in the first few spars, the failure mode is structural -- check Discipline 2 (evidence-base distinctness) and the Challenger schema first. Mid-scale scores (3) on most criteria suggest the Challenger is running but with uneven discipline; partner attention to whichever criteria score lowest is the right next move.

If the first 3-5 test spars score 4 or 5 across most criteria, Phase 1 is validated. Lower scores are themselves useful signal -- they identify where the deployment needs operational tuning.

### 5.7 Operational discipline

Phase 1 is not "set and forget." Two recurring practices keep the deployment healthy.

**Weekly persona audit.** Once a week, scan one persistent persona file for drift:

- Lines that do not tie to a function from the six WHAT functions or three HOW functions. Cut them even if they are funny.
- Voice section longer than Behavioral invariants section -- the layers are overbalanced toward HOW; rebalance.
- Anecdotes / personality quirks added without grounding expertise -- cosplay drift; cut.
- Staleness in Conventions -- the project's standards may have shifted; verify the persona's conventions still match.

A 5-minute audit per persona per week catches drift before it accretes into cosplay over months.

**Quarterly artifact review.** Once a quarter, spot-check 5-10 spar artifacts from the Reference Record:

- Did the framework's structural commitments hold (Discipline 2 evidence-base distinctness, Discipline 3 verifiable artifacts, Discipline 4 both-must-agree)?
- Were any disagreement-at-cap outcomes resolved well by the receiving party?
- What decision quality did the artifacts support? Were the converged decisions ones the partners would have made anyway, or did SPARRING surface concerns that changed the outcome?

If SPARRING is not surfacing concerns that change outcomes, either the personas need better evidence-base distinctness, or the project's decision shape does not actually warrant SPARRING.

**Drift signals to watch for.**

- All spars converge in 1 round -- the iteration cap is too low OR the personas are too correlated.
- Spars frequently end in unresolved disagreement -- evidence-base mismatches that are not structural disagreements but framing failures.
- Partners stop reading artifacts -- artifacts too long, rubric not being applied, or curation discipline has slipped.
- The same 1-2 personas get used for everything -- the others were authored but are not fitting topics.

---

## 6. Phased build sequence

Concrete staging from MVP to enterprise-grade. Each phase is independently shippable.

### 6.1 Phase 1: MVP (~1-2 weeks of focused build)

- CLI scaffolding with `init`, `run`, `list`, `show` commands.
- Single agent SDK integration.
- Filesystem persistence under `.spar/records/`.
- Manual persona and evidence-base specification at invocation time (no auto-pairing yet).
- Basic Generator -> Challenger -> agreement-check loop with iteration cap.
- Spar artifact emission as markdown plus JSON sidecar (with `persona_class` and `persona_version` fields populated for forward-compatibility).
- Manual eval (partner reads and scores using a rubric printed by `spar review`).
- Applicability Gate as a rule list (file-extension heuristics, command-shape patterns, presence-of-artifact-channel keywords) emitting warn-and-proceed prompts before the spar starts.
- **Persona library: persistent + temporary classes.** `.spar/personas/persistent/` directory holds partner-curated personas; `.spar/personas/temporary-cache/` holds resolver-spawned per-spar specs. The `returning/` directory is created empty as forward-compatibility scaffolding.

This is enough to validate the framework on real decisions. Output: real spar artifacts that can be read and assessed.

### 6.2 Phase 2: Maturity (~2-4 weeks)

- Persona library with starter persistent persona files. Each file ships with a full Role+Domain layer (the mandatory WHAT) and a minimal Persona layer (lightweight voice rules); deployments adjust the Persona layer up or down per project fit.
- **Returning persona class shipped.** Lifecycle CLI commands (`spar persona promote / demote / evict / pool`) operational. Resolver becomes class-aware: checks persistent -> returning -> temporary in priority order, with partner notification before using a returning persona. Eviction policy runs on a daily cron with the configurable threshold (default 90 days unused). All transitions partner-gated -- no auto-promotion in Phase 2.
- Evidence-base resolver with file-path and basic RAG support.
- Auto-pairing for personas based on topic analysis (with explicit fallback to single-Challenger).
- Domain templates (code-review, architecture-decision, vendor-selection, hire-decision, plan-review).
- Eval harness with structured rubric tooling.
- Slack and email adapters for dialectic surface integration.
- Variant support: phase-isolation modes and Multi-Challenger ensemble.
- Applicability Gate upgraded from rule-list to LLM classifier (Haiku/Sonnet) with structured `{class, reason, recommended-action}` output; pure-judgment routing into the single-Challenger fallback path.

This is enough for a small team to use SPARRING as part of regular decision-making.

### 6.3 Phase 3: Enterprise (~1-3 months)

- MCP-based evidence-base support (different tool servers per role).
- Multi-SDK adapter (alternative agent runtimes).
- Watching-role Challenger / continuous monitoring (`spar daemon`).
- Pre-emptive SPARRING / decision archive workflow.
- LLM-as-judge automation in eval harness, implemented per the calibration discipline required in Section 2.3 (swap-order pairwise where applicable, length-independent criteria text, judge-model-family distinct from agent-model-family wherever the deployment supports multi-vendor configuration, chain-of-thought reasoning with probability-weighted summation where the runtime exposes token probabilities) to control the position / verbosity / self-enhancement biases documented in @zheng2023judging and @liu2023geval. Eval artifacts produced by the harness conform to the schema in Appendix C.2.
- Multi-user support with identity, sharing, access controls.
- Audit logging for SOC 2 / GDPR compliance.
- Wiki / Notion / Confluence adapters for separate Reference Record.
- Cost controls, model selection, budget enforcement.
- Ceiling-hit symptom detector embedded in the spar artifact emitter (convergence-without-artifacts heuristic, reasoning-shape similarity check, LLM-as-judge low-substance flag) emitting "ceiling-hit candidate" findings into the artifact for partner review.
- **Eval-driven auto-promotion of personas** (temporary -> returning) when the LLM-as-judge rubric scores them >= configurable threshold across N spars. Conservative defaults; the threshold and N are deployment-tuning parameters informed by Phase 2 production data. Partner can disable auto-promotion entirely if all transitions should remain manually gated.

This is the shape ready for adoption by larger organizations.

---

## 7. Honest tradeoffs and limitations

Building this is not all clean architecture. Three problems are genuinely hard:

**Evidence-base specification at spawn time** -- Discipline 2's load-bearing requirement. Auto-generating two genuinely-different evidence bases from a topic description is itself a hard inference problem. The reference deployment punts: at first, the deployer specifies evidence bases explicitly; later, the resolver attempts auto-pairing from a topic but always offers the explicit-spec override. Without this, the framework's quality leverage shrinks toward zero. The single-Challenger fallback is not a bug -- it is the framework's discipline operating correctly when distinct evidence cannot be articulated.

**Persona generation that is genuinely divergent** -- related to the evidence problem. This concern bites hardest on temporary personas (the runtime auto-generation case); persistent and returning personas are partner-curated. Temporary personas are the runtime auto-generation case where specialization theater is the live risk. The defense the framework requires (Discipline 2): personas commit to specific evidence sources at generation time. The reference deployment enforces this in code at the Role+Domain layer: a temporary-persona generation request that does not produce distinct evidence-base scope, distinct expertise, and distinct behavioral invariants is rejected and falls back to single-Challenger. The Persona layer (voice, tone, structural conventions) is *not* required to differ between Generator and Challenger. This separation prevents a related failure mode: requiring distinct Persona layers can devolve into manufactured tonal contrast (Generator "thoughtful," Challenger "skeptical") that adds no real evidence-base distinctness.

**Convergence quality detection** -- both agents agreeing is necessary but not sufficient. They could agree because they are correlated (theater), not because the proposal is sound. The only honest detection of this is measurement -- the eval harness from Discipline 6. Without it, deployments cannot tell if their `spar` invocations are producing real challenge or convincing-looking theater. Phase 1 ships manual rubric scoring as the minimum viable measurement; Phase 3 adds automation.

These are not framework flaws. They are the framework's load-bearing problems made operational. The deployment makes them visible and addressable rather than hidden.

**General considerations for production deployment:**

- **Cost.** Each spar uses 4-7 LLM API calls in baseline mode. Cost controls are essential. The reference deployment supports per-invocation budget caps, configurable model selection (Sonnet for sub-agents, Opus for orchestration), and team-level budget tracking.
- **Privacy.** Spar artifacts may contain sensitive business decisions. The reference deployment supports encryption at rest in the persistence layer and supports air-gapped deployment (local model + local persistence) for regulated industries.
- **Auditability.** Enterprise customers will want SOC 2 / GDPR-compatible logging. Discipline 7 (Observability) and the spar artifact schema support this; Phase 3 adds the audit-grade signing and retention layer.
- **Multi-user.** Teams have multiple invokers; the deployment supports user identity, per-user invocation history, and shared / private record visibility.
- **Versioning.** Persistent and returning persona files evolve; both classes are versioned. Spar artifacts cite the `persona_version` they were invoked with so historical artifacts remain interpretable across edits. Versioning applies to both layers of the persona file. **Temporary personas are not versioned** -- they are one-shot and discarded after the spar. The artifact captures the temporary persona's full Role+Domain spec inline so future readers can reconstruct what the agent operated under, even though the persona itself no longer exists in the library.
- **Vendor-neutrality.** All four adapters (agent SDK, persistence, dialectic surface, reference record) are pluggable. No vendor lock-in beyond what the deployer chooses to enable.

---

## 8. Reference deployment, not the only one

This document describes what to build if a CLI is the chosen surface. The SPARRING Framework is tool-agnostic; equally valid deployments include:

- Web applications (a hosted service with browser UI)
- IDE plugins (VS Code / JetBrains extensions running spar inline)
- Chat-bot integrations (Slack slash commands, Discord bot)
- API services (programmatic invocation from other systems)
- Embedded libraries (a Python or TypeScript module that other applications import)

The discipline-to-component mapping in Section 2.1 holds across all of these. What changes is the entry layer (UI vs CLI vs API vs embedded) and possibly the dialectic-surface adapter (which is more natural in some surfaces than others). The runtime, specialization, and persistence layers are identical across all valid deployments.

A team deploying SPARRING should pick the entry layer that fits their workflow, then build (or adopt) the components that implement each of the nine disciplines. The framework's claim is that any deployment satisfying the nine disciplines will produce higher decision quality than the same team without the framework -- bounded by the conditionalities documented in the Specification (model ceiling; ground-truth disjointness; careful design).

---

## References

::: {#refs}
:::

---

## Appendix A: CLI surface

Concrete commands for the reference CLI deployment. Examples assume a Unix-style shell.

**Initialization and configuration:**

```
spar init                              # initialize SPARRING in a project (creates .spar/)
spar config set <key> <value>          # configure global options
spar config show                       # display current config
```

**Persona and evidence management:**

```
spar persona list                      # show all personas across classes
spar persona list --class persistent   # filter by class (persistent | returning | temporary)
spar persona create <name>             # create a new persistent persona file
spar persona show <name>               # display a persona's full definition
spar persona promote <name>            # temporary -> returning, or returning -> persistent
spar persona demote <name>             # persistent -> returning (rare)
spar persona evict <name>              # remove from returning pool
spar persona pool                      # display returning pool with usage stats and ages

spar evidence list                     # show available evidence bases
spar evidence create <name> --path <path>   # define an evidence base from a corpus
```

**Running a spar:**

```
spar run <topic>                                          # auto-select personas and evidence; default 2 iterations
spar run --iterations 3 <topic>                           # override iteration cap
spar run --persona-a <slug> --persona-b <slug> <topic>    # explicit persona pairing
spar run --multi-challenger 3 <topic>                     # Multi-Challenger ensemble variant
spar run --human-challenger <topic>                       # Human-in-the-Challenger role variant
spar daemon --watch <path>                                # Watching-role Challenger on ongoing system
```

**Reviewing artifacts:**

```
spar list [--since <date>] [--persona <slug>]      # list past spars
spar show <spar-id>                                # display a spar artifact
spar review <spar-id>                              # interactive partner-applied rubric scoring
```

**Domain templates:**

```
spar template list                                 # show available domain templates
spar template install <repo-url>                   # install a template package
spar template apply <name> <topic>                 # equivalent to `spar run --template`
```

The shape mirrors familiar CLIs deliberately: `spar` is to decision-making what `git` is to version control -- a verb with subcommands operating on persistent project state.

---

## Appendix B: Configuration data model

A `.spar/` directory in each project (or `~/.spar/` globally), containing:

```
.spar/
+-- config.toml              # global / project config
+-- personas/
|   +-- persistent/          # named, partner-curated; full Role+Domain + optional Persona layers
|   +-- returning/           # used-before pool, Role+Domain only, evictable (Phase 2)
|   `-- temporary-cache/     # resolver-spawned, ephemeral, cleared per spar
+-- evidence/                # evidence-base definitions
+-- templates/               # domain-specific SPARRING templates
+-- triggers/                # observable trigger definitions
+-- records/                 # spar artifacts (Reference Record, default backend)
`-- evals/                   # eval pass artifacts
```

**Phase 1 `config.toml` example:**

```toml
[runtime]
agent_sdk = "anthropic-claude"          # default; alternatives: "openai-agents", "langchain", "custom"
default_model_generator = "claude-sonnet-4-6"
default_model_challenger = "claude-sonnet-4-6"
default_iteration_cap = 2
orchestrator_mode = "code"              # "code" (recommended Phase 1) or "agent" (Phase 2+ advanced)

[persistence]
backend = "filesystem"
records_path = ".spar/records/"

[dialectic_surface]
adapter = "stdout"                      # "stdout" for solo dev; "slack" / "discord" / "custom" for teams

[reference_record]
adapter = "filesystem"                  # "filesystem" (Phase 1); "wiki-api" / "git-markdown" (Phase 2+)
records_path = ".spar/records/"

[applicability_gate]
mode = "rule-list"                      # Phase 1 default; upgrade to "llm-classifier" in Phase 2
rules_path = ".spar/triggers/applicability-rules.toml"

[budget]
max_cost_per_spar_usd = 1.00            # circuit-breaker; abort if cost exceeds
max_total_monthly_usd = 50.00           # rolling-window soft cap with warning
```

---

## Appendix C: Artifact schemas

The framework's observability and measurability disciplines (D7, D6) produce two structured artifact types: the **spar artifact** (output of every spar ceremony) and the **eval artifact** (output of every rubric-scored review of a spar). Both are markdown for humans, JSON sidecar for tooling.

### C.1 Spar artifact schema

The spar artifact is the structured record produced by every spar invocation (Discipline 7).

```yaml
spar_id: <uuid>
timestamp: <iso8601>
topic: <string>
mode: full | spark | pattern-lock | pnp | human-generator | human-challenger | multi-challenger | watching
iterations_cap: <integer>
iterations_used: <integer>
outcome: converged | unresolved_at_cap | fallback_single_challenger
generator:
  persona: <ref to persona definition>
  persona_class: persistent | returning | temporary
  persona_version: <version string for persistent / returning; null for temporary>
  evidence_base: <ref to evidence definition>
  proposals:
    - round: 1
      proposal: <text>
      signal: { agree: <bool>, reasoning: <text> }
    - ...
challenger:
  persona: <ref>
  persona_class: persistent | returning | temporary
  persona_version: <version string for persistent / returning; null for temporary>
  evidence_base: <ref distinct from generator's>
  pressure_tests:
    - round: 1
      concerns:
        - concern: <text>
          artifact: <citation>
      signal: { agree: <bool>, reasoning: <text> }
    - ...
artifacts_cited: [ <list of unique artifact citations across all rounds> ]
final_evaluation: <text>
disagreement_at_cap_response_menu:    # populated only when outcome: unresolved_at_cap
  surfaced: <bool>                    # always true when outcome is unresolved_at_cap
  responses:                          # the five canonical options + non-canonical acknowledgment
    - id: pick-a-side
      one_line_guidance: <text>
    - id: defer
      one_line_guidance: <text>
    - id: reframe
      one_line_guidance: <text>
    - id: escalate
      one_line_guidance: <text>
    - id: synthesize
      one_line_guidance: <text>
    - id: non-canonical
      one_line_guidance: "Take a response not on the list when the situation warrants -- the menu is canonical, not exhaustive."
  receiving_party_choice: <optional id, set when the human/parent records their response>
  synthesis_text: <optional text, set when receiving_party_choice = synthesize>
  re_spar_id: <optional uuid, set when synthesis is fed back into a fresh SPARRING round>
ceiling_hit_candidate_findings:       # populated when ceiling-hit symptom detector fires
  - finding: <text>                   # e.g., "convergence reached without artifact citations"
    severity: <low | medium | high>
parent: { type: human | agent, identity: <ref> }
escalated_to: <optional ref to dialectic-surface thread>
```

### C.2 Eval artifact schema

The eval artifact is the structured record produced by every rubric-scored review of a spar artifact (Discipline 6 Measurability + Discipline 9 Reference Record). Cross-references the spar artifact it evaluates.

```yaml
eval_id: <uuid>
timestamp: <iso8601>
spar_id: <ref to the spar artifact being evaluated>
eval_artifact_version: <semver string; bumped on schema-breaking changes for traceability across schema evolution>
judge:
  model: <model identifier, e.g., "claude-sonnet-4-6" or "human-rater">
  model_family: <family, e.g., "anthropic" | "openai" | "human" | "ensemble">
  configuration: <object; deployment-specific runtime settings, e.g., temperature, system prompt template version, chain-of-thought enabled, probability-weighted scoring enabled>
eval_design:
  mode: pointwise | pairwise
  controls_applied:                      # which calibration controls per Section 2.3 are active for this eval
    - <e.g., "swap-order pairwise (position bias control)">
    - <e.g., "length-independent criteria text (verbosity bias control)">
    - <e.g., "judge model family distinct from agent model family (self-enhancement bias control)">
    - <e.g., "chain-of-thought reasoning before score (calibration improvement)">
    - <e.g., "probability-weighted summation per G-Eval (calibration improvement)">
rubric_scores:
  - criterion: <criterion name; e.g., "verifiable artifact citation">
    score: <integer per the rubric scale defined for this eval>
    reasoning: <text; per Discipline 6, scores without reasoning are not auditable>
  - ...
calibration_limitations:                 # populated when any calibration discipline from Section 2.3 cannot be fully satisfied for this eval
  - bias_type: position | verbosity | self-enhancement | calibration-improvement
    reason: <text explaining the runtime / topology / design constraint>
    mitigation_attempted: <text describing what partial defense was applied, if any>
eval_summary:                            # OPTIONAL; not the default. A rolled-up summary risks becoming the only thing readers consume, defeating the per-criterion measurement discipline. Include only when a deployment has a defensible reason (e.g., dashboard surfacing, longitudinal trend reporting). When omitted, downstream consumers must read rubric_scores directly.
  text: <text>
parent: { type: human | agent, identity: <ref> }
referenced_by_spars: [ <list of spar-ids that cite this eval as input or context> ]
referenced_by_evals: [ <list of eval-ids that cite this eval (audit chains, longitudinal eval-of-evals, calibration-drift review)> ]
```

This schema specifies the minimum fields required by the framework's existing disciplines (D6, D7, D9) plus the calibration-limitation acknowledgment requirement from Section 2.3. Speculative additions -- per-criterion confidence intervals, cross-judge agreement metrics, drift indicators across eval rounds, automated calibration-bias scoring -- are deferred until Phase 3 production data justifies their inclusion. Adopters extending the schema for deployment-specific needs are encouraged to do so additively without breaking the listed fields, so future framework revisions can build on a stable base.

---

## Appendix D: Glossary

(Combined with the Specification's glossary; consult the Specification for terms not specific to the implementation.)

**Applicability Gate.** The component implementing Discipline 1 by detecting routine-work and pure-judgment topics at entry, plus ceiling-hit symptoms during the run.

**Code-orchestrator.** The default iteration controller implementation: plain code runs the Generator -> Challenger -> agreement-check loop. Distinct from agent-orchestrator (an LLM-driven orchestration agent).

**Configuration data model.** The `.spar/` directory layout containing personas, evidence definitions, templates, trigger definitions, records, and evaluations. Specified in Appendix B.

**Discipline-to-component mapping.** The correspondence between the nine disciplines (Specification Section 3.3) and the twelve build components (Section 2.2 of this document).

**Disagreement-at-cap response menu.** The structured surface emitted when the iteration cap is reached without convergence, listing the five canonical responses (pick-a-side-with-tradeoffs, defer, reframe, escalate, synthesize) plus a non-canonical-response acknowledgment.

**Iteration controller.** The runtime component that runs the Generator -> Challenger -> agreement-check loop with configurable iteration cap. Detects convergence, unresolved disagreement, or fallback (single-Challenger).

**LLM-as-judge.** A Phase 3 evaluation agent that scores spar artifacts on the six rubric criteria (verifiable artifact citation, artifact reality, substantive vs theatrical, missed real concerns, evidence disjointness, calibrated agreement).

**Persistent (persona class).** The named, partner-curated, fully versioned persona class. Carries the full Role+Domain layer (mandatory) and optional Persona layer.

**Persona/evidence resolver.** A Phase 2 inference agent that selects two divergent specialist Role+Domain layers given a topic, with class priority (persistent first, then returning, then temporary).

**Returning (persona class).** A pool of personas that have been used before, with full Role+Domain but no Persona layer. Lower curation bar than persistent; evictable.

**Spar artifact emitter.** The persistence-layer component that produces the structured artifact for every spar invocation. Specified in Appendix C.1.

**Eval artifact.** The structured record produced by every rubric-scored review of a spar artifact (Discipline 6 Measurability + Discipline 9 Reference Record). Cross-references the spar being evaluated; records judge identity and configuration, eval design, rubric scores with reasoning, and calibration limitations per Section 2.3. Specified in Appendix C.2.

**Temporary (persona class).** Resolver-spawned per-spar personas with auto-generated Role+Domain. Discarded after the spar; recorded in the spar artifact for audit.

**Watching-role Challenger daemon.** The Phase 3 long-running variant where a persistent-class Challenger persona monitors an ongoing system and emits flags when trigger conditions fire.

(For framework-level terms -- the four phases, the SPARRING mechanic, the nine disciplines, etc. -- see the Specification's Glossary.)
