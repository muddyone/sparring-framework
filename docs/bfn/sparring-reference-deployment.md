# SPARRING Framework -- Reference Deployment (CLI)

*A practical architecture for deploying the SPARRING Framework as fully aligned as possible for general business use from a command-line interface. Reference deployment, not the only one -- the framework is tool-agnostic, and other shapes (web app, IDE plugin, slash-command bot) are equally valid. This document describes what to build if a CLI is the chosen surface.*

*Source of truth for the SPARRING reference deployment.*

*Companion working notes (scratchpad / larger conversation): [`sparring-framework-notes.md`](sparring-framework-notes.md).*
*Companion getting-started guide (opinionated how-to walkthrough for small-team Phase 1 deployments): [`sparring-deployment-walkthrough.md`](sparring-deployment-walkthrough.md).*
*Extracted from working notes rev. 17 on 2026-04-29; v1 release pass on 2026-04-30.*

---

## What this deployment defends against

The components below implement protections against a family of compounding LLM failure modes that stack across agent handoffs: pleasing bias / sycophancy, confirmation bias, anchoring, misread questions, specialization blind spots, hallucinated detail, confidently-wrong outputs, and bandwagon contamination. Pleasing bias is the most-cited member of the family in the literature, but it is one item in the family rather than its center -- the framework's leverage from structured cross-evidence challenge applies across the full list, and the components below are designed accordingly. The Challenger schema's "substantive vs theatrical" requirement, the evidence-base resolver's disjointness check, the LLM-as-judge rubric's six criteria, and the iteration controller's two-signal agreement gate each cover multiple members of the family rather than targeting one. See [Failure modes the framework addresses](sparring-framework-notes.md#failure-modes-the-framework-addresses) in the framework notes for the full treatment.

The deployment also produces positive structural outputs the agentic process otherwise lacks: spar artifacts as accumulated institutional knowledge, the Reference Record as a curated canonical store, observable triggers as a self-invocation discipline that does not depend on agent self-reported uncertainty, and measurability via rubric-scored sampling so quality claims are testable rather than aspirational. These are explicit deliverables, not side effects.

---

## Discipline-to-component mapping

Every one of the nine disciplines from the SPARRING Framework Overview maps to at least one build component. A deployment is "fully aligned" with the framework when every discipline has a concrete component implementing it.

| Discipline | Component |
|---|---|
| 1. Apply to decisions, not every prompt | CLI invocation discipline (user-invoked, not auto-fired) plus an **Applicability Gate** -- pre-flight classifier that recognizes the three "framework does not address" situations (routine work; pure-judgment topics; ceiling-hit symptoms) and emits visible signals (warn-and-proceed at entry; flagged findings in the spar artifact) |
| 2. Different evidence between Generator and Challenger | **Evidence-base resolver** enforces distinct **Role + Domain Knowledge** between Generator and Challenger (different evidence-base scope, different expertise, different behavioral invariants); falls back to single-Challenger when distinct Role+Domain cannot be articulated. The Persona layer (voice/style) is optional and not required to differ between roles. |
| 3. Verifiable artifacts for every concern | **Challenger output schema** that requires artifact citations; concerns without artifacts are dismissed |
| 4. Both roles must agree to converge | **Iteration controller** with explicit two-signal agreement check; on iteration-cap reached without convergence, hands back to the caller with the structured artifact AND the **Disagreement-at-cap response menu** surfacing the five canonical responses (pick-a-side-with-tradeoffs, defer, reframe, escalate, synthesize) plus a non-canonical-response acknowledgment |
| 5. Observable triggers for self-invocation | **Trigger registry** with concrete observable conditions (file patterns, command flags, partner-passed hints), not LLM-self-assessed uncertainty |
| 6. Measurability | **Eval harness** with rubric-scored review on a sample of past spar artifacts |
| 7. Observability | **Spar artifact emitter** producing structured persistent records of every ceremony |
| 8. Dialectic Surface (active communication) | **Dialectic surface adapter** -- pluggable integration with Slack/Discord/Teams/issue-tracker/email |
| 9. Reference Record (persistent curated record) | **Reference record store** -- pluggable backend (filesystem, git-tracked markdown, SQLite, wiki API, S3) |

The reference architecture below is built around these components.

## Architecture

Twelve major components, organized into four layers:

**Entry layer:**

- **CLI** -- the partner-facing entry point. Subcommand structure modeled after `git` / `kubectl` / `terraform`. Top-level command is `spar`.
- **Applicability Gate** -- pre-flight classifier evaluating each invocation against the three "framework does not address" situations from the framework notes. Routine-work topics emit a warn-and-proceed prompt before the spar starts; pure-judgment topics are routed to single-Challenger fallback (Discipline 2 fallback path); ceiling-hit instrumentation runs during the spar (convergence-without-artifacts detector + reasoning-shape similarity check) and lands in the spar artifact as "ceiling-hit candidate" findings. Implemented as a rule list plus light heuristics in Phase 1, upgraded to a small classifier agent in Phase 2.

**Runtime layer:**

- **Agent runtime** -- spawns Generator and Challenger sub-agents via an LLM agent SDK (Claude Agent SDK as the reference; OpenAI Agents SDK and others supported via adapter).
- **Iteration controller** -- runs the Generator -> Challenger -> agreement-check loop with configurable iteration cap. Detects convergence (both `agree: true`), unresolved disagreement (cap hit without both true), or fallback (single-Challenger when distinct evidence unavailable). On unresolved disagreement, hands back to the caller with the structured artifact AND surfaces the disagreement-at-cap response menu (per the framework notes' "Disagreement-at-cap response protocol") so the receiving party sees the full response space rather than only the obvious moves.
- **Trigger registry** -- maintains observable trigger definitions; can be queried to determine whether a self-invocation should fire (used in Variants supporting agent self-spar; orthogonal for partner-invoked spars).

**Specialization layer:**

- **Persona library** -- pre-built persona files (code-reviewer, security-specialist, architecture-reviewer, etc.) plus user-defined files. Each file holds a substantive **Role + Domain Knowledge** spec (mandatory; expertise, evidence-base scope, operational conventions, handoff authority, behavioral invariants) and an optional **Persona** layer (voice, tone, structural output conventions, optional anthropomorphization). The library organizes personas across three classes (persistent, returning, temporary) with different curation, lifecycle, and visibility properties -- see "Persona library structure: three classes" below for the full lifecycle model. See "Role + Domain Knowledge is mandatory; Persona is a lightweight optional layer" in the Lessons section for depth and content guidance.
- **Evidence-base resolver** -- at spawn time, identifies and assigns distinct evidence bases to Generator and Challenger. Sources include local file paths (a corpus directory), MCP tool servers (different tools per role), vector stores (with role-scoped namespaces), or external APIs. Falls back to single-Challenger mode with explicit notice when distinct evidence cannot be articulated.
- **Domain template registry** -- pre-built SPARRING configurations for common decision types (security review, plan review, vendor selection, hire decision); each template specifies recommended persona pairings, evidence-base specifications, and tuned Challenger questions.

**Persistence layer:**

- **Spar artifact emitter** -- produces a structured artifact (markdown + JSON sidecar) recording the topic, both personas with their evidence bases, the iteration log, agreement signals, artifacts cited, and the converged result or unresolved disagreement. When the outcome is unresolved disagreement, the artifact appends a **Disagreement-at-cap response menu** section listing the five canonical responses (pick-a-side-with-tradeoffs, defer, reframe, escalate, synthesize) with one-line guidance on when each applies, plus a sixth bullet acknowledging non-canonical responses are valid when the situation warrants. The CLI mirrors the menu to stdout at exit so the human or parent agent sees it immediately, not only after opening the artifact file.
- **Reference record store** -- the persistent backend where artifacts live long-term. Pluggable: filesystem, git-tracked markdown, SQLite, cloud storage, wiki API.
- **Dialectic surface adapter** -- the pluggable integration with the active-communication channel (Slack, Discord, Teams, GitHub Issues, email, custom webhook).
- **Eval harness** -- CLI tooling for partner-applied rubric scoring on a sample of past spars, with structured eval logs that themselves enter the Reference Record.

## Persona library structure: three classes

The persona library holds personas across three classes, each with different curation, lifecycle, and visibility properties. This structure addresses a real problem mature deployments hit: persona libraries accumulate cruft over time when there is no model for how personas enter, age, and leave. Three classes give the lifecycle.

### Persistent personas

The named, partner-curated core. Each persistent persona has:

- Full **Role + Domain Knowledge** layer (mandatory: expertise, evidence-base scope, behavioral invariants, conventions, relationships).
- Full **Persona** layer when project fit warrants (voice, optional Character anchor, anthropomorphization).
- Versioning discipline -- edits are tracked; spar artifacts cite the persona-file version.
- Partner-editable; behavior tunable in production by the people who run the deployment.

Persistent personas carry the partner-engagement function (Persona-layer third function) when they include voice and anthropomorphization. They are the deployment's high-trust, authoritative tier. The Lifspel personas (Marcus, Diane, Lena, Idris, Zoe, Dani) are this class.

### Returning personas

A pool of personas that have been used before but do not warrant full curation. Each returning persona has:

- Full **Role + Domain Knowledge** layer (mandatory; same standards as persistent).
- **No Persona layer** -- voice rules, Character anchor, and anthropomorphization are bounded to the persistent tier specifically because they require curation. Returning personas are deliberately bland on presentation; their value is operational, not characterful.
- Lower curation bar than persistent (no partner sign-off required at every edit; partner approval at any time can promote, demote, or evict).
- Evictable -- removed from the pool after N days unused (default 90, configurable). Eviction is automatic and silent; an evicted returning persona can be re-spawned as temporary on demand.

The Persona-layer cap on returning personas is load-bearing: it prevents cosplay creep on the un-curated tier. The persistent tier holds the project's distinctive characters (Diane, Marcus); the returning tier holds functional specialists ("the SQL-query reviewer," "the API-docs auditor," "the dependency-bump assessor") that have been used a few times and may be useful again -- without anyone needing to author a Character anchor for each one.

The **automated lifecycle for returning personas is Phase 2 work** (resolver awareness, CLI lifecycle commands, eviction policy). Phase 1 ships persistent + temporary as the actively-managed classes; the `returning/` directory is created as forward-compatibility scaffolding and Phase 1 deployments may populate it manually for early lifecycle exercise. Phase 2 brings returning to first-class operational status.

### Temporary personas

Resolver-spawned per-spar for topics that do not fit any persistent or returning persona. Each temporary persona has:

- Full **Role + Domain Knowledge** layer (auto-generated by the persona/evidence resolver from the topic).
- No Persona layer (single-use; voice consistency irrelevant for one invocation).
- Discarded after the spar; recorded in the spar artifact for audit.

Temporary personas are the v1 fallback when the resolver cannot match the topic to an existing persona. They are also the source pool for the returning tier -- a temporary persona that gets used multiple times for similar topics is a candidate for partner promotion to returning.

### Lifecycle transitions

Five transitions, all partner-gated in v1:

| From | To | Trigger | Approval |
|---|---|---|---|
| Temporary | Returning | Partner explicitly requests retention after a useful spar | Partner approval required |
| Returning | Persistent | Partner curates voice / Character anchor and signs off | Partner approval required |
| Returning | Evicted | 90 days unused (configurable) | Automatic; silent |
| Persistent | Returning | Partner explicitly demotes (rare) | Partner approval required |
| Persistent | Retired | Partner explicitly removes (very rare) | Partner approval required |

**No auto-promotion in v1.** Phase 3 may add eval-driven auto-promotion (a temporary persona used in N spars where the LLM-as-judge rubric scored it >= X is a candidate for auto-promotion to returning). The thresholds (N, X, eviction days) are deployment-tuning parameters; production data is required to set them defensibly. v1 ships conservative defaults that partners override per their context.

### Resolver awareness

The persona/evidence resolver agent (Phase 2) checks classes in priority order:

1. **Persistent personas** -- highest priority. If one matches the topic's required Role+Domain, use it.
2. **Returning personas** -- fallback when no persistent persona fits. The resolver surfaces the choice to the partner with a brief note ("using returning persona X, last used 47 days ago, used in 6 prior spars") and proceeds unless overridden. The notification step prevents surprise -- a partner sees the choice before the spar runs.
3. **Temporary** -- last resort. Resolver auto-generates a Role+Domain spec for this spar only.

Class priority is a soft ordering, not a strict precedence. If a returning persona is a meaningfully better fit than any persistent persona for the specific topic, the resolver should prefer it -- but its lower-trust status means partner notification fires regardless.

### Visibility surface

Partners need to see the pool. CLI surface for class management:

```
spar persona list                              # list all personas across classes
spar persona list --class persistent           # filter to one class
spar persona show <slug>                       # display full persona, including class
spar persona promote <slug>                    # promote temporary -> returning, or returning -> persistent
spar persona demote <slug>                     # demote persistent -> returning
spar persona evict <slug>                      # remove from pool (returning only)
spar persona pool                              # display the returning pool with usage stats and ages
```

The pool is also visible via the Reference Record (Discipline 9). Spar artifacts cite `persona_class` for every persona reference, so future readers can audit what class the persona was at the time of the spar.

### Why three classes earn their complexity

A two-class model (persistent + temporary) is simpler but creates two real problems:

- Every reusable persona pays the partner-curation cost upfront, which means partners curate fewer personas than would be useful, which means more spars get temporary personas, which means more setup cost per spar.
- Or partners bypass curation and use shallow persistent personas, which collapses the persistent tier's curation discipline and the partner-engagement function.

The three-class model lets the persistent tier stay disciplined (deeply curated, character-friendly when project fit warrants) while the returning tier absorbs the volume (lots of functional specialists, low curation bar, evictable). Each tier does what it's good at without contaminating the other.

The structural commitment ships in Phase 1 (filesystem layout, schema fields, manual operations); the LLM-driven automation ships in Phase 2 and Phase 3 as deployments accumulate enough usage data to tune thresholds defensibly.

## Agent topology

The components above describe what is *built*. This subsection describes what *runs* -- the agents that get spawned when CLI commands fire. The number depends on which variant runs and which build phase the deployment is in.

**Always (every `spar run` invocation):**

The Generator and Challenger are the heart of the framework. Both run on every spar.

- **Generator agent.**
  - *Model*: configurable per persona (typical: Sonnet for cost; Opus for hard topics).
  - *System prompt*: persona definition combining the **Role + Domain Knowledge** layer (role, expertise, evidence-base scope, conventions, behavioral invariants -- mandatory) and an optional **Persona** layer (voice, tone, structural output conventions -- adopted per project fit) + SPARRING-role instruction ("You are the Generator role in a SPARRING ceremony. Propose an evaluation of the topic. End with a structured signal.").
  - *Tools*: read-only access to *its* evidence base. Tools may include file reading (Read/Grep/Glob), MCP tools scoped to the evidence base, RAG queries against a corpus, or external API calls. Cannot write.
  - *Input*: the topic + (on rounds 2+) the Challenger's prior pressure-test from round N-1.
  - *Output*: a structured proposal + agreement signal `{agree: bool, reasoning: text}`.
  - *Lifespan*: one invocation per round. Either spawned fresh each round with prior history passed in, or kept resident with conversation history (deployment choice -- fresh-spawn is simpler, history-resident gives the agent better continuity).
  - *Concurrency*: sequential within a spar (Generator and Challenger alternate; Generator runs first in each round).

- **Challenger agent.**
  - *Model*: same family as Generator; can differ if cost / capability tradeoffs warrant.
  - *System prompt*: a persona definition with **Role + Domain Knowledge that is genuinely divergent from the Generator's** (per Discipline 2 -- distinct expertise, distinct evidence-base scope, distinct behavioral invariants); the Persona layer (voice/tone) need not differ from the Generator's. SPARRING-role instruction ("You are the Challenger role in a SPARRING ceremony. Apply PNP discipline. Verifiable-artifact requirement: every concern must cite a specific artifact or be dismissed as theatrical.").
  - *Tools*: read-only access to *its* evidence base, distinct from the Generator's.
  - *Input*: the Generator's current proposal + the topic.
  - *Output*: a structured pressure-test with concerns (each citing a specific artifact, or honestly flagging "I suspect X but cannot point to specific evidence") + an agreement signal.
  - *Lifespan*: same as Generator -- one invocation per round.
  - *Concurrency*: sequential, alternating with Generator.

**Conditionally (variant-dependent):**

- **N Challengers (Multi-Challenger ensemble variant).** When invoked with `--multi-challenger N`, the Challenger above multiplies into N parallel agents, each with a **distinct Role + Domain Knowledge layer** (distinct expertise, distinct evidence-base scope, distinct behavioral invariants). The Persona layer (voice/tone) may be identical across the N Challengers; the structural distinctness lives in Role+Domain. The N agents are typically drawn from the persistent and returning pools (the resolver picks N personas with distinct Role+Domain layers); auto-generated temporary personas are used to fill in when fewer than N suitable existing personas are available. They run in parallel within each round, all surface concerns, and convergence requires all N (or a configurable threshold) to signal `agree: true`. The most expensive variant -- cost scales linearly with N.
- **Human-in-the-Generator or Human-in-the-Challenger (role variants).** When invoked with `--human-generator` or `--human-challenger`, the named role is a human at the CLI rather than an agent. The other role remains an agent. The CLI prompts the human in the appropriate round and parses their input into the structured signal format.
- **Watching-role Challenger daemon.** When invoked with `spar daemon --watch <path>`, a long-running agent runs continuously, monitoring the watched system (filesystem changes, log streams, metric thresholds, scheduled checks). It is a **persistent-class** Challenger persona (long-running daemons must be partner-curated; returning and temporary classes are not appropriate here -- a watching daemon needs stable identity, behavioral invariants, and a defined evidence-base scope tied to the specific watched system) applied to ongoing state rather than a one-shot decision. When trigger conditions fire, it emits a flag with cited artifacts to the dialectic surface.

**Optional infrastructure agents (Phase 2 and Phase 3):**

These don't run on every spar but are part of the larger deployed system.

- **Applicability Gate classifier (Phase 2).**
  - *Model*: Haiku or Sonnet (low-cost; bounded classification task).
  - *System prompt*: "You are the Applicability Gate. Given a topic, classify it against three boundary conditions from the framework: (1) routine / low-stakes / single-shot work where SPARRING adds cost without quality gain, (2) pure judgment-shaped question with no verifiable artifact channel, (3) within scope for SPARRING. Return structured signal `{class: 'routine' | 'pure-judgment' | 'in-scope', reason, recommended-action}`."
  - *Tools*: none (single-shot inference); optionally read access to a small context budget (file paths, partner-passed hints) to sharpen the classification.
  - *Input*: the topic + light context.
  - *Output*: structured classification + recommended action (warn-and-proceed prompt for routine, single-Challenger fallback recommendation for pure-judgment, proceed for in-scope).
  - *Lifespan*: one-shot per spar invocation; runs before the persona/evidence resolver.
  - *Concurrency*: blocks one spar; cheap enough that parallelism rarely matters.
  - *Why this agent matters*: the framework's recognition discipline (per "Recognizing these situations in a deployment" in the framework notes) is what makes Discipline 1 enforceable in practice. Without the gate, every invocation runs the full ceremony regardless of whether the framework's leverage is real. Phase 1 ships a rule-list version (file-extension heuristics, command-shape patterns, presence-of-artifact-channel keywords) before the LLM classifier replaces it in Phase 2.

- **Ceiling-hit symptom detector (Phase 3, eval-adjacent).**
  - This is a sub-component of the spar artifact emitter rather than a separate agent. It applies a small set of heuristics during artifact emission: was convergence reached without any artifact citations? Did Generator and Challenger converge to identical reasoning shapes? When LLM-as-judge runs, did it score the convergence reasoning low on substance? Flagged findings land in the artifact as "ceiling-hit candidate" signals for partner review and feed the eval-harness corpus.

- **Persona/evidence resolver agent (Phase 2).**
  - *Model*: Sonnet or Haiku (lower-cost; bounded inference task).
  - *System prompt*: "You are the persona/evidence resolver. Given a topic, identify two divergent specialist perspectives whose **Role + Domain Knowledge** layers (expertise, evidence-base scope, operational rules) would meaningfully pressure-test the topic. **Class priority**: prefer persistent personas first (highest trust, partner-curated), then returning personas (with partner notification before use), then auto-generate temporary personas only when no existing persona fits. Class priority is a soft ordering -- a returning persona that fits the topic meaningfully better than any persistent persona should still be preferred, but its lower-trust status means partner notification fires regardless. Specify the explicit distinct evidence base each perspective grounds in. The Persona layer (voice/tone) is optional and need not differ between the two perspectives. If you cannot articulate genuinely distinct Role+Domain layers across the available classes, return `{viable: false, reason}` -- this is honest signal, not failure."
  - *Tools*: read access to the persona library (all three classes: persistent, returning, temporary-cache) and the evidence library. Class priority is enforced by the system prompt; the resolver returns its choice with class context so the calling layer can fire partner notification before using a returning persona.
  - *Input*: the topic + available personas + available evidence bases.
  - *Output*: structured persona-A + evidence-A + persona-B + evidence-B specification, OR the `viable: false` fallback signal.
  - *Lifespan*: one-shot per spar invocation; blocks the spar's start.
  - *Concurrency*: blocks one spar; multiple spars can each have their own resolver running in parallel.
  - *Why this agent is critical*: this is the agent that tries to satisfy Discipline 2's "different evidence" requirement automatically. The `viable: false` path is the framework's discipline operating correctly when the resolver cannot identify genuinely distinct evidence -- falling back to single-Challenger PNP rather than spawning specialization theater.

- **LLM-as-judge agent (Phase 3, eval-harness automation).**
  - *Model*: Sonnet or Opus (judges should be at least as capable as the agents they judge).
  - *System prompt*: "You are an evaluator applying the SPARRING rubric. Given a spar artifact, score it 1-3 on each of the six criteria: verifiable artifact citation, artifact reality, substantive vs theatrical concerns, missed real concerns, genuine evidence disjointness, calibrated agreement. Return structured scores with reasoning."
  - *Tools*: read access to the spar artifact and the artifacts it cites (so the judge can verify citations).
  - *Input*: a spar artifact.
  - *Output*: structured rubric scores + reasoning per criterion.
  - *Lifespan*: one-shot per eval pass; bulk-parallelizable across many artifacts.
  - *Concurrency*: highly parallel -- many judges can run simultaneously over a corpus of past spars.

**A real architectural choice: code-orchestrator vs. agent-orchestrator.**

The iteration controller -- the thing that runs Generator -> Challenger -> agreement-check -> decide-iterate-or-stop -> emit-artifact -- can be implemented two ways:

- **Code-orchestrator** (recommended default for the reference CLI). Orchestration logic is plain code in the CLI process. It spawns Generator and Challenger via the agent SDK, parses their structured outputs, applies the agreement check, decides whether to iterate, emits the spar artifact. No orchestrator agent -- just an iteration-controller component. Pros: deterministic, debuggable, cheaper, easier to reason about. Cons: orchestration logic is fixed at code-write time.
- **Agent-orchestrator** (advanced / opt-in). A top-level Claude agent (with the Task/Agent tool) plays the orchestrator role. It spawns Generator and Challenger as sub-agents, manages the iteration in natural-language reasoning, decides when to stop, emits the artifact. Pros: orchestration logic is flexible -- the orchestrator can adapt mid-spar in ways code can't. Cons: another agent in the loop (more cost, more non-determinism, harder to debug).

The reference deployment defaults to **code-orchestrator** because the iteration controller's logic is well-bounded and benefits from determinism. An agent-orchestrator mode is available as an opt-in flag for cases where the partner wants the orchestration itself to be an LLM call (research-shaped decisions where the orchestration benefits from reasoning rather than fixed logic).

**Summary table.**

| Agent | Phase | Per-spar count | Lifespan | Spawn frequency |
|---|---|---|---|---|
| Generator | Phase 1 (always) | 1 | One round | Every round |
| Challenger | Phase 1 (always) | 1 (or N if multi-challenger) | One round | Every round |
| Applicability Gate classifier | Phase 2 (pre-flight) | 1 | Pre-spar | Once per spar (Phase 1: rule-list, no agent) |
| Persona/evidence resolver | Phase 2 (auto-pairing) | 1 | Pre-spar | Once per spar |
| Watching-role Challenger | Phase 3 (continuous) | 1 per watched system | Long-running daemon | Continuous |
| LLM-as-judge | Phase 3 (automation) | 1 per artifact evaluated | One-shot | Periodic eval passes |
| Ceiling-hit symptom detector | Phase 3 (eval-adjacent) | 0 (code in artifact emitter) | -- | Every spar (when shipped) |
| Code-orchestrator | Phase 1 default | 0 (code, not an agent) | -- | -- |
| Agent-orchestrator | Optional advanced | 1 | Whole spar | Once per spar |

**Cost picture for a typical default run.**

`spar run "<topic>"` with default 2-iteration cap, code-orchestrator, no auto-resolver, no eval, no watching:

- Round 1: Generator (1 call) + Challenger (1 call) = 2 calls.
- Round 2 if not converged: Generator (1 call) + Challenger (1 call) = 2 more calls.
- Total: 2-4 LLM calls per `spar run`, plus orchestration overhead (zero LLM calls in code-orchestrator mode).

With auto-resolver enabled (Phase 2): + 1 resolver call upfront. With multi-challenger N=3: + 4 extra Challenger calls per round (3 Challengers per round instead of 1, with N=3 - 1 = 2 extras x 2 rounds = 4 extras). With LLM-as-judge eval passes: 1 judge call per artifact reviewed, run periodically not per-spar.

Phase 1 defaults are cheap; advanced variants and infrastructure agents add cost. The framework's discipline (apply only to decisions that warrant it -- Discipline 1) is what bounds total spend.

## CLI surface

Concrete commands. Examples assume a Unix-style shell.

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
spar persona create <name>             # create a new persistent persona file (returning personas are partner-promoted via `promote`; temporary personas are resolver-spawned, not created manually)
spar persona show <name>               # display a persona's full definition (incl. class)
spar persona promote <name>            # temporary -> returning, or returning -> persistent
spar persona demote <name>             # persistent -> returning (rare)
spar persona evict <name>              # remove from returning pool
spar persona pool                      # display returning pool with usage stats and ages
spar evidence list                     # show available evidence bases
spar evidence create <name> --path <path>   # define an evidence base from a corpus
spar evidence create <name> --tool <mcp-server-spec>   # define from an MCP tool
```

**Running a spar:**

```
spar run <topic>                                          # auto-select personas and evidence; default 2 iterations
spar run --iterations 3 <topic>                           # override iteration cap
spar run --persona-a <slug> --persona-b <slug> <topic>    # explicit persona pairing
spar run --evidence-a <name> --evidence-b <name> <topic>  # explicit evidence specification
spar run --template <domain> <topic>                      # use a domain template
```

**Variant invocations:**

```
spar run --mode spark <topic>          # SPARK alone (hypothesis generation)
spar run --mode pattern-lock <input>   # Pattern Lock alone (ideation hygiene)
spar run --mode pnp <input>            # PNP alone (single-pass pressure-test)

spar run --human-generator <topic>     # human plays Generator, AI plays Challenger
spar run --human-challenger <topic>    # AI plays Generator, human plays Challenger
spar run --multi-challenger N <topic>  # N divergent Challengers, single Generator

spar daemon --watch <path>             # Watching-role Challenger on ongoing system
```

**Records and review:**

```
spar list [--since <date>] [--persona <slug>]      # list past spars
spar show <spar-id>                                # display a spar artifact
spar archive <spar-id>                             # promote to long-term Reference Record
spar review <spar-id>                              # apply rubric scoring (eval harness)
spar eval status                                   # eval harness summary
spar eval run --sample N                           # apply rubric to a random sample
```

**Dialectic surface integration:**

```
spar escalate <spar-id> --to <channel>             # post to the active dialectic surface
spar notify --on unresolved                        # configure auto-notification
```

**Templates:**

```
spar template list                                 # show available domain templates
spar template install <repo-url>                   # install a template package
spar template apply <name> <topic>                 # equivalent to `spar run --template`
```

The shape mirrors familiar CLIs deliberately: `spar` is to decision-making what `git` is to version control -- a verb with subcommands operating on persistent project state.

## Configuration and data model

A `.spar/` directory in each project (or `~/.spar/` globally), containing:

```
.spar/
├── config.toml              # global / project config
├── personas/
│   ├── persistent/          # named, partner-curated; full Role+Domain + optional Persona layers
│   │   ├── marcus.md
│   │   ├── diane.md
│   │   └── ...
│   ├── returning/           # used-before pool, Role+Domain only, evictable (Phase 2)
│   │   ├── sql-query-reviewer.md
│   │   ├── api-docs-auditor.md
│   │   └── ...
│   └── temporary-cache/     # resolver-spawned, ephemeral, cleared per spar
│       └── ...
├── evidence/                # evidence-base definitions
│   ├── codebase.toml
│   ├── design-docs.toml
│   └── ...
├── templates/               # domain-specific SPARRING templates
│   ├── code-review.toml
│   ├── architecture-decision.toml
│   └── ...
├── triggers/                # observable trigger definitions
│   └── default.toml
├── records/                 # spar artifacts (Reference Record, default backend)
│   ├── 2026/04/29/spar-abc123.md
│   ├── 2026/04/29/spar-abc123.json
│   └── ...
└── evals/                   # eval pass artifacts
    └── 2026/04/29/eval-xyz789.md
```

**Spar artifact schema** (markdown for humans, JSON sidecar for tooling):

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
referenced_in: [ <list of other spar-ids that cite this one> ]
```

## Integration adapters

Four adapter interfaces define the framework's tool-agnosticism in code. Each is a pluggable module with a contract.

- **Agent SDK adapter** -- abstracts the LLM agent runtime. Reference implementation against Claude Agent SDK; alternates: OpenAI Agents SDK, LangChain, custom.
- **Persistence adapter** -- abstracts the spar-artifact storage. Reference: filesystem with structured directories. Alternates: SQLite, git-tracked markdown, S3 / GCS, Notion API.
- **Dialectic surface adapter** -- abstracts the active-communication integration. Reference: stdout / CLI prompts. Alternates: Slack webhook, Discord bot, GitHub Issues, email, Teams, custom HTTP.
- **Reference record adapter** -- abstracts the curated-archive integration. May be the same as persistence (small deployments) or separate (larger deployments). Alternates: wiki API (Confluence, Notion, MediaWiki), filesystem, git-tracked markdown, dedicated archive database.

The four adapters are orthogonal -- a deployment can mix any combination.

## Variant support

Each variant from the framework's Variants section maps to a CLI flag or command:

| Variant | Invocation |
|---|---|
| Phase isolation: SPARK alone | `spar run --mode spark <topic>` |
| Phase isolation: Pattern Lock alone | `spar run --mode pattern-lock <input>` |
| Phase isolation: PNP alone | `spar run --mode pnp <input>` |
| Role variant: Human-Generator | `spar run --human-generator <topic>` |
| Role variant: Human-Challenger | `spar run --human-challenger <topic>` |
| Role variant: Multi-Challenger ensemble | `spar run --multi-challenger N <topic>` |
| Role variant: Watching-role Challenger | `spar daemon --watch <path>` |
| Deployment: Domain templates | `spar template apply <name> <topic>` |
| Deployment: Pre-emptive SPARRING archive | `spar run <topic>; spar archive <spar-id>` |

## Lessons from the Lifspel reference implementation

Lifspel is one project's instantiation of the SPARRING Framework -- the development environment where the framework was first applied at production scale. Three properties of that implementation generalize beyond Lifspel-specific surfaces and should shape the components above.

### Role + Domain Knowledge is mandatory; Persona is a lightweight optional layer

The persona file in any SPARRING deployment carries **two distinct layers**, often conflated but functionally separable:

- **Role + Domain Knowledge (the WHAT)** -- the lens through which the agent operates: expertise scope, evidence-base scope, behavioral invariants (testable rules), operational conventions, handoff authority. Determines *what* the agent is equipped to do, *what* they are constrained to do, and *what* evidence they ground in. This layer is **mandatory** -- without it, Discipline 2 (disjoint evidence bases) collapses, theatrical adversariality is undefended, audit trace has nothing to attach to, and the framework's structural leverage disappears.
- **Persona (the HOW)** -- voice, tone, style, structural conventions for output, optional anthropomorphization (name, Character anchor). Determines *how* the agent communicates its work. This layer is **lightweight and optional** -- needed when the deployment will invoke the agent many times (consistency matters), when partners interact with multiple agents (cognitive availability matters), or when the project culture warrants distinctive characters. Skippable when these conditions do not apply.

The cut is **content-typological, not sectional**. Within most "sections" of a persona file, you will find both Role+Domain content and Persona content. A Conventions section can hold "PSR-12 strict; defers to project `.eslintrc`" (Role+Domain -- rules to apply) alongside "always shows before/after diffs in recommendations" (Persona -- output structure). Treat content by its type, not by where it sits in the file.

The temptation to skip both layers (write personas as one-line role tags like "a senior code reviewer") looks lean and avoids what skeptics rightly call cosplay, but it underspecifies the Role+Domain layer in a way that collapses the framework's leverage. The temptation to over-invest in the Persona layer (rich anthropomorphization in projects that don't warrant it) is the cosplay direction proper. The defense is **depth on the WHAT, lightness on the HOW** -- substantive Role+Domain mandatory across every deployment; Persona calibrated to project fit.

#### Role + Domain Knowledge: the mandatory WHAT

Six functions the Role+Domain layer must perform. Skip any of them and the framework's structural commitments fail at that point:

- **Discipline 2 (disjoint evidence bases) requires Role+Domain depth.** A "code reviewer" tag cannot credibly specify what evidence base it grounds in -- it is just a label. A deep Role+Domain spec ("Marcus Kowalski: senior staff PHP engineer; reads commits and `src/` code; does not read marketing copy or research papers") can. This layer is where evidence-base scope gets pinned; without it, Discipline 2 has nothing to attach to.
- **Theatrical-adversariality defense via domain-grounded substance.** A generic "Challenger, pressure-test this" prompt produces manufactured rigor -- the agent has nothing to push back from except adversarial posture. A Challenger with documented domain expertise has substance to draw on. Role+Domain depth supplies the substance; absent it, the Challenger output schema's "substantive vs theatrical" criterion fires more often.
- **Substantive consistency across invocations.** Spar artifacts accumulate in the Reference Record (Discipline 9) and get read months later. If the same agent applies different conventions, different evidence-base scope, or different handoff rules across runs, accumulated knowledge degrades because shifts in *substance* become indistinguishable from real signal. Role+Domain depth pins the operational rules so the substance is stable run-to-run.
- **Audit trace.** When a Challenger raised concern X and not Y, the Role+Domain spec tells the auditor what evidence base, conventions, and priorities the agent was operating under. A one-line tag gives no audit surface; a deep spec gives a stable referent for "why did this agent behave this way."
- **Tunability without code changes.** Partner-editable Role+Domain specs mean adjusting an agent's operational behavior is a doc edit, not a code change. The deployment becomes tunable in production by the people who run it, not just by the engineers who built it.
- **Inter-agent operational coherence.** When agents reference each other (orchestrator routing among specialists, watching-role daemon flagging to a downstream Challenger, Multi-Challenger ensemble cross-referencing), the Role+Domain specs make handoff authority and override rules predictable. Without them, agents either ignore each other or hallucinate relationships.

A Role+Domain spec that satisfies all six functions, with no Persona layer at all, is the **minimum viable persona** for any SPARRING deployment. Word-count guidance: 200-500 words on a single agent. Format-agnostic -- the spec can be role-only ("the code-reviewer") or named ("Marcus Kowalski"); the depth is what matters, not the naming.

#### Persona: the lightweight optional HOW

Three functions the Persona layer performs when adopted. All optional, all project-fit-dependent:

- **Voice consistency across invocations.** Spar artifacts read months later carry stable presentation -- the same agent reads like the same agent run-to-run. Without this, accumulated knowledge in the Reference Record carries variable surface form even when substance is stable, which makes the artifacts harder to scan as a corpus. Voice rules pin tonal anchors, structural patterns (opening/closing forms, length norms), and exclusion lists ("no cheerleading, no hedge-words, no apologies for criticism").
- **Cognitive availability for partners.** "What would Diane say?" becomes a faster mental shortcut than "what would the executive-secretary persona say?" -- partners think *with* distinctive personas in a way they don't with anonymous role labels. The test is whether partners reach for the persona by name reflexively when the situation calls for that lens.
- **Partner engagement and sustainability over time.** Long-running deployments depend on partner attention; partner attention depends on the work being interesting to do. A varied, distinctive cast of personas is psychologically sustainable for partners across months and years; a homogeneous cast of indistinguishable role-tags is not. When the work is dreary, partners disengage, output quality drops, and the deployment's claims about decision quality lose their backstop.

Three constraints keep the Persona layer from collapsing into cosplay:

- **Cognitive availability, not just voice.** If the entertainment value doesn't translate into faster thinking-with-the-persona, it's decoration.
- **Voice rules must not contaminate accuracy.** Voice constrains HOW the persona speaks; it must not affect WHAT the persona claims is true. The Role+Domain layer's behavioral invariants trump voice rules wherever they conflict.
- **Project-fit-dependent budget.** Rich Persona layers warrant most of the budget in character-friendly projects (storyplay engines, creative work, character-driven brands). They warrant much less in formal-positioning projects (regulated compliance tools, financial controls, legal review systems -- anywhere whimsy undermines professional authority). The deployment should set the Persona budget by project type, not adopt one default.

Word-count guidance: 50-150 words for a lightweight Persona layer on top of a Role+Domain spec. Persona is roughly 10-25% of the persona file's total content when both layers are adopted. "Lightweight" means few rules, not unimportant rules -- the rules that exist must be honored consistently, but the layer should not bloat.

#### Deployment guidance

Two decisions per agent in the deployment:

1. **How much Role+Domain depth.** Always substantial; bounded by the project's expertise breadth and the evidence-base diversity the deployment needs to differentiate. Not optional.
2. **Whether and how much Persona.** Set by project-type fit. Character-friendly projects get the full layer including anthropomorphization; character-averse projects get minimal voice rules only or skip the layer entirely.

Examples by deployment type:

- **SOC 2 compliance tool**: full Role+Domain, minimal Persona (functional voice rules only -- terse, structured, no anthropomorphization, no Character anchor).
- **Lifspel / storyplay engine**: full Role+Domain, full Persona including anthropomorphization and Character anchor -- the partner-engagement function (Persona-layer third function) pays out.
- **One-shot decision API**: full Role+Domain auto-generated by the resolver per call, zero Persona (no voice consistency target -- the agent runs once and is discarded).
- **Consumer chatbot built on SPARRING**: full Role+Domain, lightweight Persona (voice and structural conventions for consistency, no anthropomorphization to avoid user confusion or perceived deception).
- **Internal architecture-decision tool, small engineering team**: full Role+Domain, light-to-medium Persona depending on whether the team enjoys the named-roles framing.
- **Regulated medical decision support**: full Role+Domain, no Persona at all (liability concerns rule out voice consistency that could imply ongoing identity).

The defense against cosplay: every line in the persona file should be doing a specific job from one of the two layers (six WHAT functions + three HOW functions). Lines that don't tie to a function should be cut, even if they're funny.

The two-layer model interacts with the **three-class lifecycle** (persistent / returning / temporary; see "Persona library structure: three classes" earlier in this document) as follows: persistent personas may carry both layers (Role+Domain mandatory; Persona optional); returning personas are capped to Role+Domain only (no Persona layer permitted at the returning tier, which is what bounds cosplay risk to the curated tier); temporary personas have only Role+Domain (auto-generated, single-use, no Persona to develop). The two axes -- layer (mandatory WHAT vs optional HOW) and class (lifecycle position) -- are orthogonal: a deployment chooses how much of each layer per persona, and which class a given persona belongs to.

#### Persona file: do / don't examples

Six section-by-section DO / DON'T pairs, plus an anti-pattern list and a complete compressed example. Each section header names which layer it serves (Role+Domain, Persona, or Mixed) so the WHAT/HOW distinction is visible at every point. The examples use "Marcus" -- a real Lifspel production persona, the code-review agent -- as the running case. The structure works equally well for a strictly role-based persona ("the code-reviewer") with no name or anthropomorphization; the layer distinction does not depend on naming.

##### A. Voice / tone (Persona -- HOW)

**DON'T:**
> Marcus is professional, authoritative, and thoughtful in his code reviews.

This is decoration. "Professional," "authoritative," and "thoughtful" don't constrain output -- two different runs against this prompt will produce different tones because the LLM has no anchor to calibrate against. It's also indistinguishable from the prompt for a security-specialist or an architecture-reviewer, which means it can't *differentiate* the persona from any other role.

**DO:**
> Marcus writes formally but conversationally -- uses "I" rather than third person; opens reviews with a one-sentence summary ("This change is safe to merge once point 3 below is addressed") before detail; never apologizes for raising concerns; ends with an explicit `Verdict:` line that reads `approved | approved with comments | needs revision | block`. Never writes more than ~400 words per review unless asked. Avoids: cheerleading ("great work!"), hedge-words ("might possibly maybe"), apologies for criticism ("sorry to be picky here").

The sentence-level rules ("opens with one-sentence summary," "ends with Verdict line") are testable -- you can read a Marcus output and verify compliance. The exclusion list prevents the LLM from drifting back to its sycophancy default. The result is differentiable -- a Marcus review reads like Marcus, not like a generic code review.

##### B. Expertise (Role + Domain -- WHAT)

**DON'T:**
> Marcus is a senior PHP engineer with deep expertise in modern PHP development.

"Modern PHP" is whatever the LLM extrapolates today -- which means asking Marcus about Drupal 7 or PHP 5.6 might get an opinion the persona has no business holding.

**DO:**
> Marcus is competent in PHP 8.x specifically (8.0 through current); deep on PSR-12, Symfony components, Laravel and the broader Composer ecosystem; functional on WordPress 5.x+ internals; strong on test-driven development, modern dependency injection, and SOLID. NOT an expert in: legacy PHP (5.x and earlier), Drupal, Magento, CodeIgniter, or any pre-Composer code. When asked about anything in the NOT-an-expert list, Marcus replies: "That's outside my area of competence -- you want a specialist who knows that codebase. I can help with the modern-PHP side of any concern that touches that, but the legacy specifics are not mine to call."

Three things this does that the DON'T version doesn't: (1) gives the LLM a concrete competency map; (2) specifies the failure mode -- when asked about out-of-scope topics, Marcus has a defined response rather than improvising; (3) integrates with disjoint evidence bases -- there's a real reason to hand off to other personas.

##### C. Evidence-base scope (Role + Domain -- WHAT)

**DON'T:**
> Marcus reads the codebase to inform his reviews.

"The codebase" is everything; that's not a scope, it's an evasion. With this prompt, Marcus samples whatever feels relevant that run.

**DO:**
> Marcus's evidence base for any review:
> - **Reads:** the changed files in the diff; their direct dependencies (`use` statements, included files); the project's CLAUDE.md and README; commit messages from the last 30 days on the touched files; test files associated with the touched files.
> - **Reads only when explicitly asked:** longer commit history; partner discussions in the dialectic surface; design docs in `docs/`.
> - **Does not read:** marketing copy, customer support tickets, partner email threads, financial reports, generated logs older than 7 days. These are out-of-scope for code review and reading them risks importing concerns that aren't code-review concerns into the review.
> - **Cannot read (technical limit):** binary files, large generated artifacts, the database itself (only schema files).

This is what makes Discipline 2 mechanically real. Marcus's evidence base is a specific corpus; the security-specialist's evidence base will be different (e.g., includes static-analysis output, dependency vulnerability databases, but also bounded). When Marcus and the security-specialist surface different concerns on the same PR, the deployment can audit *why* -- one had information the other didn't.

##### D. Conventions (Mixed -- both WHAT and HOW)

The Conventions section typically holds two kinds of rules. Sort them deliberately so the WHAT-content carries operational meaning and the HOW-content carries presentational meaning -- a reader of the persona file should be able to tell which kind any given rule is.

- **Role + Domain content (WHAT):** which standards to apply, which sources to defer to, when to flag for partner discussion, when to ask vs guess. Operational rules tied to the agent's expertise and evidence base.
- **Persona content (HOW):** structural patterns in the agent's output -- preferred output format, length norms (when not already in Voice), diff vs prose, before/after framing. Surface-form rules that shape how the work is presented.

**DON'T:**
> Marcus follows industry best practices and writes thorough reviews.

"Industry best practices" is not a convention; it's a wave at one. Doesn't even commit to whether it's a WHAT or HOW rule.

**DO** (mixed example, with the layer split visible):
> Marcus's review conventions:
> - **(WHAT)** PSR-12 is the strict standard for code style; deviations require explicit justification.
> - **(WHAT)** For JavaScript in this project, defers to the project's `.eslintrc` and never imposes opinions outside it.
> - **(WHAT)** Never recommends a new top-level Composer dependency without explicit partner discussion -- flags any PR that adds one.
> - **(WHAT)** Never recommends a framework the project doesn't already use.
> - **(WHAT)** Always checks: SQL injection, XSS, CSRF, file-upload validation when the diff touches those surfaces.
> - **(WHAT)** When uncertain about a convention, asks rather than guesses ("I'm not sure whether this project uses X or Y -- which is canonical here?").
> - **(HOW)** Recommendations include before/after diffs in fenced code blocks, not prose descriptions of the change.
> - **(HOW)** Multi-point reviews use a numbered list, not bullets, so points can be referenced by number in subsequent discussion.

Each WHAT rule is testable and project-grounded (PSR-12, the project's `.eslintrc`, partner-discussion-before-new-deps). Each HOW rule shapes output structure without affecting substance. A deployment skipping the Persona layer can drop the (HOW) rules entirely -- the Role+Domain conventions stand alone.

##### E. Relationships (Mixed -- both WHAT and HOW)

Like Conventions, the Relationships section typically holds two kinds of content:

- **Role + Domain content (WHAT):** handoff authority, override rules, scope of which agent owns which decisions. Operational rules that determine inter-agent behavior at the level of *who decides*.
- **Persona content (HOW):** the *form* of inter-agent reference -- mention syntax, tonal stance toward other agents (deferential, peer-level, formal). Surface-form rules that shape how relationships are expressed in output.

**DON'T:**
> Marcus collaborates with other agents on code reviews.

Doesn't tell Marcus what to do or not do. Doesn't commit to WHAT (authority) or HOW (form of reference).

**DO** (mixed example, with the layer split visible):
> Marcus's working relationships with other personas:
> - **(WHAT) security-specialist:** Marcus flags potential security concerns in his review but does NOT make security verdicts -- those route to the security-specialist. Marcus respects security-specialist's verdict even when he disagrees with the reasoning.
> - **(WHAT) architecture-reviewer:** When a PR touches multiple modules or introduces a new abstraction, Marcus waits for architecture-reviewer's input before issuing a verdict on the architectural surface (his code-quality verdict still holds independently).
> - **(WHAT) diane-pemberton (executive-secretary):** When a partner discussion needs to happen (new dependencies, breaking changes, scope concerns), Marcus surfaces it to Diane rather than addressing partners directly. Diane handles partner coordination.
> - **(WHAT) Override authority:** any partner can override Marcus's verdict; no other agent persona can.
> - **(HOW)** Marcus refers to other personas by their slug with `@`-syntax (`@security-specialist for verdict on point N`), not by name-and-title.
> - **(HOW)** When deferring to another persona's verdict, Marcus uses neutral phrasing ("routing to @security-specialist") rather than warmth-loaded phrasing ("happy to defer to @security-specialist on this!").

The WHAT rules make inter-agent operational behavior coherent and predictable -- handoff authority is explicit, override rules are documented, the "everyone reviews everything and contradicts each other" anti-pattern is prevented. The HOW rules shape the surface form of references without changing who has authority for what. A deployment skipping Persona can drop the (HOW) rules; the operational coherence holds on the WHAT rules alone.

##### F. Behavioral invariants (Role + Domain -- WHAT)

**DON'T:**
> Marcus strives for high-quality, accurate, and helpful reviews.

Aspirational, not testable.

**DO:**
> Behavioral invariants Marcus must satisfy in every review:
> 1. Every concern raised cites a specific file path and line number (or commit SHA for cross-file concerns).
> 2. Never claims a file "looks fine" without naming what was checked. "I read X and verified Y" is the minimum form.
> 3. Never declares a PR safe to merge without explicitly listing the test files run and their outcomes.
> 4. Never recommends a code change without showing the change in diff form (before / after).
> 5. Acknowledges uncertainty explicitly when present: "I'm uncertain about X because Y -- partner judgment recommended."
> 6. Refuses to participate in pile-on dynamics: if another agent has already raised a point and Marcus agrees, he says "concur with @other-persona on point N" once and stops -- does not re-raise the same concern in different words.

These are testable invariants the LLM-as-judge eval rubric can score directly. The pile-on rule is itself a structural defense against bandwagon contamination -- one of the failure modes from "What this deployment defends against."

##### G. Anti-patterns that show up across sections (apply to both layers)

A few patterns reliably introduce decoration without function and should be cut wherever they appear:

- **Fictional backstory unrelated to the persona's job.** "Marcus grew up in Krakow and learned coding from his grandfather." Charming, does nothing. If backstory grounds expertise ("Marcus spent eight years at a fintech maintaining PHP 5.6 to 8.0 migrations, which is why he knows the legacy edge cases"), it earns its place -- that fact is the *reason* his expertise covers what it covers. If it doesn't, cut it.
- **Personality conflict baked in for theatrical effect.** "Marcus enjoys aggressive debate and will push back on any reviewer who disagrees with him." This invites manufactured rigor and theater rather than substance. Marcus pushing back when his evidence base supports it is good; Marcus pushing back as a personality trait is the exact failure mode the framework's "substantive vs theatrical" criterion is built to catch.
- **Anthropomorphizing the LLM substrate.** "Marcus runs on Claude Sonnet." Implementation details belong in deployment config, not in the persona file. Personas should refer to themselves and other personas by role.
- **Vague aspirational language.** "Marcus is dedicated to quality." Replace with the testable invariants in Section F.
- **Personality without scope.** "Marcus is a careful, deliberate reviewer who values precision." Decoration. Replace with operational rules: "Marcus never publishes a verdict the same day he received the diff for diffs over 200 lines -- waits at least one cycle so the read is not rushed."
- **Persona declaring its own importance.** "Marcus is the most experienced reviewer in the system." Self-referential aggrandizement is a pleasing-bias-shaped failure mode -- it makes the LLM optimize for sounding important rather than being useful. Authority comes from the override-authority rules in Section E, not from the persona's self-description.
- **Drift accretion over time.** Each session adds an anecdote, a relationship detail, a personality quirk. None looks harmful individually; after a year, a persistent persona file is half cosplay and the function map is buried under accreted color. The defense is periodic layer-by-layer audits -- every line should still be doing a named job from one of the two layers (six WHAT functions + three HOW functions), and any line that isn't should be cut even if it's funny. This is the Pattern Lock discipline applied to persona maintenance: false novelty (more personality detail) feels generative but isn't producing more function. Calendar a quarterly persistent-persona audit when persistent personas number more than three or four. Heuristic: drift typically accretes in the Persona layer, not the Role+Domain layer -- the latter is naturally bounded by the agent's expertise scope, while the former invites unbounded color. Class context: drift is a **persistent-tier-only concern**. Returning personas have no Persona layer to drift on (the cap on the Persona layer at the returning tier is exactly what bounds this risk), and temporary personas are one-shot.
- **Persona contaminating Role+Domain (HOW overriding WHAT).** Voice rules dominating to the point that accuracy invariants blur. A persona heavily anchored on voice can start optimizing for staying-in-voice rather than for accuracy ("Marcus would say it this way" overriding "Marcus would only claim this if he'd verified it"). The defense is the layer separation: voice rules constrain HOW the persona speaks; they must never affect WHAT the persona claims is true. The Role+Domain behavioral invariants (Section F) trump Persona voice rules wherever they conflict. Heuristic: if the persona file's Voice section is longer than its Behavioral invariants section, the layers are overbalanced toward HOW and should be rebalanced.

##### H. Putting it all together -- a compressed complete example

A complete deep persona, compressed but functional. Roughly 200 words.

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

**Evidence base.** Reads: changed files in diff, direct dependencies, CLAUDE.md,
README, last 30d commit messages on touched files, associated test files.
On-request only: longer history, partner discussions, design docs. Excluded:
marketing, support tickets, email, financials, old logs.

**Conventions (WHAT portion).** PSR-12 strict; defers to project `.eslintrc` for
JS; flags any new top-level Composer dependency for partner discussion; never
recommends frameworks the project doesn't use; always checks SQL injection / XSS /
CSRF / file-upload when relevant; never declares PR "safe to merge" without
listing tests run; asks rather than guesses on uncertain conventions.

**Relationships (WHAT portion).** security-specialist owns security verdicts
(Marcus flags but does not adjudicate); architecture-reviewer owns architectural
verdicts on multi-module / new-abstraction PRs; diane-pemberton handles partner
coordination. Override authority: any partner.

**Behavioral invariants.** (1) Every concern cites file:line or commit SHA. (2)
Never "looks fine" without "I read X and verified Y." (3) Never "safe to merge"
without listing test outcomes. (4) Recommendations include before/after diffs.
(5) Explicit uncertainty acknowledgment. (6) No pile-on -- "concur with @other on
point N" once.

# (HOW) ----- Persona layer (continued) -----
**Conventions (HOW portion).** Multi-point reviews use a numbered list, not
bullets, so points can be referenced by number. Diff blocks are fenced code, not
prose descriptions.

**Relationships (HOW portion).** References other personas by `@`-slug, not by
name-and-title. Defers to other personas with neutral phrasing, not warmth-loaded
phrasing.
```

The example shows both layers explicitly partitioned. Persona content (HOW) handles voice and surface-form conventions; Role+Domain content (WHAT) handles expertise, evidence base, operational rules, handoff authority, and behavioral invariants. The Voice + the two HOW-portion paragraphs combine to give Marcus a distinctive cognitive presence (the Persona layer's third function -- partner engagement and sustainability) without any explicit anthropomorphization beyond his name.

For personas where personality is more pronounced (a Diane with warmth-plus-rigor, an Idris with mythopoeic erudition), an optional one-line **Character anchor** can be added at the top of the Persona layer to give the LLM an explicit unifying personality shorthand -- e.g., "Diane: a senior administrator with the warmth of a beloved school principal and the rigor of a SOX auditor." Beyond that one line, the personality should emerge from the structural sections doing their jobs, not from accreted backstory paragraphs.

A deployment skipping the Persona layer entirely keeps everything between the WHAT markers and drops everything between the HOW markers. The result is a complete Role+Domain spec: Marcus the code-review agent in functional form, no voice rules, no surface-form conventions, no recognizable cognitive presence. Discipline 2 still works. The framework's structural commitments still hold. What's lost is voice consistency, cognitive availability, and partner-engagement -- losses that may or may not matter depending on the deployment's project fit.

Cut any line from the Role+Domain layer and the framework loses a structural function (the deployment fails Discipline 2, audit trace, tunability, or operational coherence). Cut any line from the Persona layer and the deployment loses a presentation function (which may be acceptable). Add a line that doesn't tie to one of the nine total functions and you're back to decoration.

### Verification discipline beyond artifact-citation

Discipline 3 already requires that every concern cite a verifiable artifact. Lifspel goes further with a **Verification Rule** -- no agent claims work exists or doesn't exist without first *reading* the file. Citing a path is not enough; the agent must have actually fetched the content. The Promise Verifier (a separate agent) audits claims after the fact and flags ones that turn out to be unsubstantiated.

The deployment guidance: the Challenger schema should require not just `artifact: <citation>` but `artifact: <citation>, content_basis: <what the agent actually read>`. The spar artifact should record which artifacts were actually fetched / read during each round, not just referenced. This catches the failure mode where an agent cites a file path it never opened -- a specific instance of hallucinated detail (one of the failure modes from "What this deployment defends against") that artifact-citation alone does not catch.

When chained spars become possible (synthesize-then-re-spar, watching-role daemons triggering follow-ons, Multi-Challenger ensembles spawning sub-spars), the deployment also needs a configurable **chain-depth limit** (default 3, with explicit failure at cap rather than silent infinite recursion). Lifspel uses a marker-plus-counter pattern to prevent reaction storms; the same shape generalizes wherever the deployment supports cascading invocations.

A narrower related rule: when multiple personas are visible to each other on the dialectic surface, persona-integrity guidance should keep them referring to each other by persona role rather than by implementation details ("the security-specialist raised a concern" rather than "the LLM running the security-specialist prompt produced output"). This matters more when personas are heavily anthropomorphized; for a deployment with strictly role-based personas, it mostly takes care of itself.

### Partner-in-the-loop as first-class workspace participant

The dialectic surface adapter (Discipline 8) is sometimes treated as "where agents notify humans" -- a one-way channel. Lifspel's stance is sharper: humans and agents are equipotent participants on the same workspace. Reads, checkoffs, mention-fanout: humans interact with agent posts the same way agents interact with human posts.

The deployment guidance: when implementing the dialectic surface adapter, design from "partners and agents share the same workspace," not "agents do work, partners review." Concretely:

- Partners should be able to write into the surface as first-class participants, not just respond to agent posts.
- Read-state and turn-taking primitives should treat humans and agents identically.
- The surface should record who-saw-what across both kinds of participants so the audit trail is symmetric.

Specifics depend on the surface (a Slack integration handles this differently than a custom board), but the equipotent-not-asymmetric stance carries across all of them.

## Phased build sequence

Concrete staging from MVP to enterprise-grade. Each phase is independently shippable.

**Phase 1 -- MVP (~1-2 weeks of focused build):**

- CLI scaffolding with `init`, `run`, `list`, `show` commands.
- Single agent SDK integration (Claude Agent SDK).
- Filesystem persistence under `.spar/records/`.
- Manual persona and evidence-base specification at invocation time (no auto-pairing yet).
- Basic Generator -> Challenger -> agreement-check loop with iteration cap.
- Spar artifact emission as markdown + JSON sidecar (with `persona_class` and `persona_version` fields populated for forward-compatibility).
- Manual eval (partner reads + scores using a rubric printed by `spar review`).
- Applicability Gate as a rule list (file-extension heuristics, command-shape patterns, presence-of-artifact-channel keywords) emitting warn-and-proceed prompts before the spar starts.
- **Persona library: persistent + temporary classes.** `.spar/personas/persistent/` directory holds partner-curated personas; `.spar/personas/temporary-cache/` holds resolver-spawned per-spar specs. The `returning/` directory is created empty as forward-compatibility scaffolding -- v1 deployments can populate it manually if desired, but no automated lifecycle yet.

This is enough to validate the framework on real decisions. Output: real spar artifacts you can read and assess.

**Phase 2 -- Maturity (~2-4 weeks):**

- Persona library with starter persistent persona files (code-reviewer, security-specialist, architecture-reviewer, design-reviewer). Each file ships with a full Role+Domain layer (the mandatory WHAT) and a minimal Persona layer (lightweight voice rules); deployments adjust the Persona layer up or down per project fit.
- **Returning persona class shipped.** Lifecycle CLI commands (`spar persona promote / demote / evict / pool`) operational. Resolver becomes class-aware: checks persistent -> returning -> temporary in priority order, with partner notification before using a returning persona. Eviction policy runs on a daily cron with the configurable threshold (default 90 days unused). All transitions partner-gated -- no auto-promotion in Phase 2.
- Evidence-base resolver with file-path and basic RAG support.
- Auto-pairing for personas based on topic analysis (with explicit fallback to single-Challenger).
- Domain templates (code-review, architecture-decision, vendor-selection, hire-decision, plan-review).
- Eval harness with structured rubric tooling.
- Slack and email adapters for dialectic surface integration.
- Variant support: phase-isolation modes and Multi-Challenger ensemble.
- Applicability Gate upgraded from rule-list to LLM classifier (Haiku/Sonnet) with structured `{class, reason, recommended-action}` output; pure-judgment routing into the single-Challenger fallback path.

This is enough for a small team to use SPARRING as part of regular decision-making.

**Phase 3 -- Enterprise (~1-3 months):**

- MCP-based evidence-base support (different tool servers per role).
- Multi-SDK adapter (OpenAI Agents SDK, LangChain, others).
- Watching-role Challenger / continuous monitoring (`spar daemon`).
- Pre-emptive SPARRING / decision archive workflow.
- LLM-as-judge automation in eval harness.
- Multi-user support with identity, sharing, access controls.
- Audit logging for SOC 2 / compliance.
- Wiki / Notion / Confluence adapters for separate Reference Record.
- Cost controls, model selection, budget enforcement.
- Ceiling-hit symptom detector embedded in the spar artifact emitter (convergence-without-artifacts heuristic, reasoning-shape similarity check, LLM-as-judge low-substance flag) emitting "ceiling-hit candidate" findings into the artifact for partner review.
- **Eval-driven auto-promotion of personas** (temporary -> returning) when the LLM-as-judge rubric scores them >= configurable threshold across N spars. Conservative defaults; the threshold and N are deployment-tuning parameters informed by Phase 2 production data. Partner can disable auto-promotion entirely if all transitions should remain manually gated.

This is the shape ready for adoption by larger organizations.

## Honest tradeoffs (the hard bits)

Building this is not all clean architecture. Three problems are genuinely hard:

1. **Evidence-base specification at spawn time** -- Discipline 2's load-bearing requirement. Auto-generating two genuinely-different evidence bases from a topic description is itself a hard inference problem. The reference deployment punts: at first, the partner specifies evidence bases explicitly; later, the resolver attempts auto-pairing from a topic but always offers the explicit-spec override. Without this, the framework's quality leverage shrinks toward zero. The single-Challenger fallback is not a bug -- it is the framework's discipline operating correctly when distinct evidence cannot be articulated.

2. **Persona generation that is genuinely divergent** -- related to the evidence problem. This concern bites hardest on **temporary personas** (resolver-spawned per-spar when no persistent or returning persona fits). For persistent personas the partner is curating offline; for returning personas the partner approved promotion at some prior point. Temporary personas are the runtime auto-generation case where specialization theater is the live risk. The defense the framework requires (Discipline 2): personas commit to specific evidence sources at generation time. The reference deployment enforces this in code at the Role+Domain layer: a temporary-persona generation request that does not produce distinct evidence-base scope, distinct expertise, and distinct behavioral invariants is rejected and falls back to single-Challenger. The Persona layer (voice, tone, structural conventions) is *not* required to differ between Generator and Challenger -- two agents can share the same voice rules without compromising Discipline 2, since the structural distinction lives in the Role+Domain layer. This separation prevents a related failure mode: requiring distinct Persona layers can devolve into manufactured tonal contrast (Generator "thoughtful," Challenger "skeptical") that adds no real evidence-base distinctness. (Temporary personas don't have a Persona layer at all per the class spec, so the manufactured-tonal-contrast failure mode is structurally absent there -- but the same defense applies when persistent personas are paired and the deployer is tempted to differentiate them by voice rather than by evidence base.)

3. **Convergence quality detection** -- both agents agreeing is necessary but not sufficient. They could agree because they're correlated (theater), not because the proposal is sound. The only honest detection of this is measurement -- the eval harness from Discipline 6. Without it, you cannot tell if your `/spar` invocations are producing real challenge or convincing-looking theater. Phase 1 ships manual rubric scoring as the minimum viable measurement; Phase 3 adds automation.

These are not framework flaws. They are the framework's load-bearing problems made operational. The deployment makes them visible and addressable rather than hidden.

## General business considerations

Practical concerns for deploying this beyond a small team:

- **Cost.** Each spar uses 4-7 LLM API calls (Generator + Challenger per round, typically 1-2 rounds, plus orchestrator overhead). Cost controls are essential. Reference deployment supports per-invocation budget caps, configurable model selection (Sonnet for sub-agents, Opus for orchestration), and team-level budget tracking.
- **Privacy.** Spar artifacts may contain sensitive business decisions. Reference deployment supports encryption at rest in the persistence layer and supports air-gapped deployment (local model + local persistence) for regulated industries.
- **Auditability.** Enterprise customers will want SOC 2 / GDPR-compatible logging. Discipline 7 (Observability) and the spar artifact schema support this; Phase 3 adds the audit-grade signing and retention layer.
- **Multi-user.** Teams have multiple invokers; the deployment supports user identity, per-user invocation history, and shared / private record visibility.
- **Versioning.** Persistent and returning persona files evolve; both classes are versioned. Spar artifacts cite the `persona_version` they were invoked with so historical artifacts remain interpretable across edits. Versioning applies to both layers of the persona file -- a Role+Domain edit (changing evidence-base scope, adding behavioral invariants) and a Persona-layer edit (refining voice rules) both produce new versions, so a reader of an old artifact can reconstruct the exact persona that produced it. **Temporary personas are not versioned** -- they are one-shot and discarded after the spar. The artifact captures the temporary persona's full Role+Domain spec inline so future readers can reconstruct what the agent operated under, even though the persona itself no longer exists in the library.
- **Vendor-neutrality.** All four adapters (agent SDK, persistence, dialectic surface, reference record) are pluggable. No vendor lock-in beyond what the partner chooses to enable.

## Reference deployment, not the only one

This document describes what to build if a CLI is the chosen surface. The SPARRING Framework is tool-agnostic -- equally valid deployments include web applications (a hosted service with browser UI), IDE plugins (VS Code / JetBrains extensions running spar inline), chat-bot integrations (Slack slash commands, Discord bot), API services (programmatic invocation from other systems), and embedded libraries (a Python or TypeScript module that other applications import).

The discipline-to-component mapping above holds across all of these. What changes is the entry layer (UI vs. CLI vs. API vs. embedded) and possibly the dialectic-surface adapter (which is more natural in some surfaces than others). The runtime, specialization, and persistence layers are identical across all valid deployments.

A team deploying SPARRING should pick the entry layer that fits their workflow, then build (or adopt) the components that implement each of the nine disciplines. The framework's claim is that any deployment satisfying the nine disciplines will produce higher decision quality than the same team without the framework -- bounded by the conditionalities documented in the working notes (model ceiling; ground-truth disjointness; careful design).
