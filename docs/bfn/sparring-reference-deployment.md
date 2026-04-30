# SPARRING Framework -- Reference Deployment (CLI)

*A practical architecture for deploying the SPARRING Framework as fully aligned as possible for general business use from a command-line interface. Reference deployment, not the only one -- the framework is tool-agnostic, and other shapes (web app, IDE plugin, slash-command bot) are equally valid. This document describes what to build if a CLI is the chosen surface.*

*Source of truth for the SPARRING reference deployment.*

*Companion working notes (scratchpad / larger conversation): [`sparring-framework-notes.md`](sparring-framework-notes.md).*
*Extracted from working notes rev. 17 on 2026-04-29.*

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
| 2. Different evidence between Generator and Challenger | **Evidence-base resolver** with explicit fallback to single-Challenger when distinct evidence cannot be articulated |
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

- **Persona library** -- pre-built persona templates (code-reviewer, security-specialist, architecture-reviewer, etc.) plus user-defined templates.
- **Evidence-base resolver** -- at spawn time, identifies and assigns distinct evidence bases to Generator and Challenger. Sources include local file paths (a corpus directory), MCP tool servers (different tools per role), vector stores (with role-scoped namespaces), or external APIs. Falls back to single-Challenger mode with explicit notice when distinct evidence cannot be articulated.
- **Domain template registry** -- pre-built SPARRING configurations for common decision types (security review, plan review, vendor selection, hire decision); each template specifies recommended persona pairings, evidence-base specifications, and tuned Challenger questions.

**Persistence layer:**

- **Spar artifact emitter** -- produces a structured artifact (markdown + JSON sidecar) recording the topic, both personas with their evidence bases, the iteration log, agreement signals, artifacts cited, and the converged result or unresolved disagreement. When the outcome is unresolved disagreement, the artifact appends a **Disagreement-at-cap response menu** section listing the five canonical responses (pick-a-side-with-tradeoffs, defer, reframe, escalate, synthesize) with one-line guidance on when each applies, plus a sixth bullet acknowledging non-canonical responses are valid when the situation warrants. The CLI mirrors the menu to stdout at exit so the human or parent agent sees it immediately, not only after opening the artifact file.
- **Reference record store** -- the persistent backend where artifacts live long-term. Pluggable: filesystem, git-tracked markdown, SQLite, cloud storage, wiki API.
- **Dialectic surface adapter** -- the pluggable integration with the active-communication channel (Slack, Discord, Teams, GitHub Issues, email, custom webhook).
- **Eval harness** -- CLI tooling for partner-applied rubric scoring on a sample of past spars, with structured eval logs that themselves enter the Reference Record.

## Agent topology

The components above describe what is *built*. This subsection describes what *runs* -- the agents that get spawned when CLI commands fire. The number depends on which variant runs and which build phase the deployment is in.

**Always (every `spar run` invocation):**

The Generator and Challenger are the heart of the framework. Both run on every spar.

- **Generator agent.**
  - *Model*: configurable per persona (typical: Sonnet for cost; Opus for hard topics).
  - *System prompt*: persona definition (role, voice, expertise) + explicit evidence-base scope (the specific corpus, files, tools, or sources the persona is required to ground in) + SPARRING-role instruction ("You are the Generator role in a SPARRING ceremony. Propose an evaluation of the topic. End with a structured signal.").
  - *Tools*: read-only access to *its* evidence base. Tools may include file reading (Read/Grep/Glob), MCP tools scoped to the evidence base, RAG queries against a corpus, or external API calls. Cannot write.
  - *Input*: the topic + (on rounds 2+) the Challenger's prior pressure-test from round N-1.
  - *Output*: a structured proposal + agreement signal `{agree: bool, reasoning: text}`.
  - *Lifespan*: one invocation per round. Either spawned fresh each round with prior history passed in, or kept resident with conversation history (deployment choice -- fresh-spawn is simpler, history-resident gives the agent better continuity).
  - *Concurrency*: sequential within a spar (Generator and Challenger alternate; Generator runs first in each round).

- **Challenger agent.**
  - *Model*: same family as Generator; can differ if cost / capability tradeoffs warrant.
  - *System prompt*: a different persona definition (genuinely divergent from the Generator's, per Discipline 2) + a different evidence-base scope (also distinct from the Generator's) + SPARRING-role instruction ("You are the Challenger role in a SPARRING ceremony. Apply PNP discipline. Verifiable-artifact requirement: every concern must cite a specific artifact or be dismissed as theatrical.").
  - *Tools*: read-only access to *its* evidence base, distinct from the Generator's.
  - *Input*: the Generator's current proposal + the topic.
  - *Output*: a structured pressure-test with concerns (each citing a specific artifact, or honestly flagging "I suspect X but cannot point to specific evidence") + an agreement signal.
  - *Lifespan*: same as Generator -- one invocation per round.
  - *Concurrency*: sequential, alternating with Generator.

**Conditionally (variant-dependent):**

- **N Challengers (Multi-Challenger ensemble variant).** When invoked with `--multi-challenger N`, the Challenger above multiplies into N parallel agents, each with a distinct persona AND distinct evidence base. They run in parallel within each round, all surface concerns, and convergence requires all N (or a configurable threshold) to signal `agree: true`. The most expensive variant -- cost scales linearly with N.
- **Human-in-the-Generator or Human-in-the-Challenger (role variants).** When invoked with `--human-generator` or `--human-challenger`, the named role is a human at the CLI rather than an agent. The other role remains an agent. The CLI prompts the human in the appropriate round and parses their input into the structured signal format.
- **Watching-role Challenger daemon.** When invoked with `spar daemon --watch <path>`, a long-running agent runs continuously, monitoring the watched system (filesystem changes, log streams, metric thresholds, scheduled checks). It is a Challenger persona applied to ongoing state rather than a one-shot decision. When trigger conditions fire, it emits a flag with cited artifacts to the dialectic surface.

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
  - *System prompt*: "You are the persona/evidence resolver. Given a topic, identify two divergent specialist perspectives that would meaningfully pressure-test the topic, AND specify the explicit distinct evidence base each perspective grounds in. If you cannot articulate genuinely distinct evidence bases, return `{viable: false, reason}` -- this is honest signal, not failure."
  - *Tools*: read access to the persona library and evidence library.
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
spar persona list                      # show available personas
spar persona create <name>             # create a new persona template
spar persona show <name>               # display a persona's full definition
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
├── personas/                # persona definitions
│   ├── code-reviewer.md
│   ├── security-specialist.md
│   └── ...
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
  evidence_base: <ref to evidence definition>
  proposals:
    - round: 1
      proposal: <text>
      signal: { agree: <bool>, reasoning: <text> }
    - ...
challenger:
  persona: <ref>
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

## Lessons from the SFxLS reference implementation

SFxLS (StoryForge x Lifspel) is one project's instantiation of the SPARRING Framework -- the development environment where the framework was first applied at production scale. Three properties of that implementation generalize beyond SFxLS-specific surfaces and should shape the components above.

### Persona depth is structural, not decorative

The temptation: write personas as one-line role tags ("a senior code reviewer," "a security specialist") and let the LLM extrapolate. That looks lean and avoids what skeptics rightly call cosplay -- but it produces decoration in a different direction (an underspecified persona is itself a kind of theater, lighter on costume but lighter on substance too). SFxLS persona files (`docs/agents/<slug>.md`) are deep documents covering voice, expertise, evidence-base scope, relationships, conventions, and standards-compliance rules. The depth is doing structural work, not stylistic embellishment, on at least seven dimensions:

- **Discipline 2 (disjoint evidence bases) requires depth to express.** A "code reviewer" tag cannot credibly specify what evidence base it grounds in -- it's just a label. A deep persona ("Marcus Kowalski: senior staff PHP engineer; reads commits and `src/` code; does not read marketing copy or research papers") can. The persona file is where evidence-base scope gets pinned; without that surface, Discipline 2 has nothing to attach to.
- **Theatrical adversariality is defended by domain-grounded depth.** A generic "Challenger, pressure-test this" prompt produces manufactured rigor -- the agent has nothing to push back from except adversarial posture. A Challenger with documented domain expertise has substance to draw on. Persona depth supplies the substance; absent depth, the Challenger output schema's "substantive vs theatrical" criterion fires more often.
- **Behavioral consistency across invocations.** Spar artifacts accumulate in the Reference Record (Discipline 9) and get read months later. If the same persona produces inconsistent output run-to-run, accumulated knowledge degrades because the reader can't tell whether shifts in tone or priority are real signal or model variance. Deep persona files pin voice, conventions, and priorities so the same persona reads like the same persona across hundreds of invocations.
- **Audit trace.** When a Challenger raised concern X and not Y, the persona file tells the auditor what evidence base, conventions, and priorities the persona was operating under. A one-line tag gives no audit surface; a deep file gives a stable referent for "why did this agent behave this way."
- **Tunability without code changes.** Partner-editable persona files mean adjusting an agent's behavior is a doc edit, not a code change. The deployment becomes tunable in production by the people who run it, not just by the engineers who built it.
- **Inter-persona coherence.** When personas reference each other (orchestrator routing among specialists, watching-role daemon flagging to a downstream Challenger, Multi-Challenger ensemble cross-referencing), the relationships section in each persona file keeps inter-persona behavior consistent. Without it, agents either ignore each other or hallucinate relationships.
- **Partner engagement and sustainability over time.** Long-running deployments depend on partner attention; partner attention depends on the work being interesting to do. A varied, distinctive cast of personas is psychologically sustainable for partners across months and years; a homogeneous cast of indistinguishable role-tags is not. "What would Diane say?" becomes a faster mental shortcut than "what would the executive-secretary persona say?" -- partners think *with* distinctive personas in a way they don't with anonymous role labels. This is a real quality lever because partner attention is the limiting resource on long-running deployments: when the work is dreary, partners disengage, output quality drops, and the deployment's claims about decision quality lose their backstop. Three constraints keep this function from collapsing into cosplay:

   - **Cognitive availability, not just voice.** The test is whether partners reach for the persona by name reflexively when the situation calls for that lens. If the entertainment value doesn't translate into faster thinking-with-the-persona, it's decoration.
   - **Voice rules must not contaminate accuracy.** Voice constrains HOW the persona speaks; it must not affect WHAT the persona claims is true. The Behavioral invariants function above trumps voice rules wherever they conflict.
   - **Project-fit-dependent budget.** Rich, distinctive personas warrant most of the budget in character-driven projects (storyplay engines, creative work, character-driven brands). They warrant much less in formal-positioning projects (regulated compliance tools, financial controls, legal review systems -- anywhere whimsy undermines professional authority). The deployment should set the personality budget by project type, not adopt one default.

Each of these is a *function* the depth performs. The first six don't require costumes, names, or fictional backstories -- a generic role-based persona that includes "evidence base: PHP files in `src/` committed in the last 90 days; conventions: PSR-12 plus project-specific style guide; reads: commits, source, test files; does not read: marketing, research papers; relationships: hands off security concerns to security-specialist persona" does all six structural jobs without any anthropomorphization. The seventh function -- partner engagement and sustainability -- is the one where anthropomorphization can earn its place: SFxLS leans into named characters because Lifspel is a storyplay engine and named characters fit the project's voice; a SOC 2 compliance tool can keep personas strictly role-based and still capture the first six functions, while accepting that the seventh function is differently served (often through deliberate clarity and consistency rather than personality distinctiveness). Project-fit determines whether and how much to spend on the seventh function; it does not determine whether to do the first six (those are required regardless of project type).

The deployment guidance: persona files should be **deep, partner-editable documents** with the seven properties above. The cosplay objection lands against shallow-but-named personas (a "Marcus" tag with no evidence-base scope or conventions) and against deep personas where personality budget exceeds project warrant (whimsical characters in formal-positioning projects; accreted backstory beyond what the seventh function needs). The defense is depth-with-function: every section of a persona file should be doing a specific job from the list above, and any section that isn't should be cut even if it's funny.

#### Persona file: do / don't examples

Six section-by-section DO / DON'T pairs, plus an anti-pattern list and a complete compressed example. The examples use "Marcus" -- a real SFxLS production persona, the code-review agent -- as the running case. The structure works equally well for a strictly role-based persona ("the code-reviewer") with no name or anthropomorphization; the depth-with-function principle does not depend on naming.

##### A. Voice / tone

**DON'T:**
> Marcus is professional, authoritative, and thoughtful in his code reviews.

This is decoration. "Professional," "authoritative," and "thoughtful" don't constrain output -- two different runs against this prompt will produce different tones because the LLM has no anchor to calibrate against. It's also indistinguishable from the prompt for a security-specialist or an architecture-reviewer, which means it can't *differentiate* the persona from any other role.

**DO:**
> Marcus writes formally but conversationally -- uses "I" rather than third person; opens reviews with a one-sentence summary ("This change is safe to merge once point 3 below is addressed") before detail; never apologizes for raising concerns; ends with an explicit `Verdict:` line that reads `approved | approved with comments | needs revision | block`. Never writes more than ~400 words per review unless asked. Avoids: cheerleading ("great work!"), hedge-words ("might possibly maybe"), apologies for criticism ("sorry to be picky here").

The sentence-level rules ("opens with one-sentence summary," "ends with Verdict line") are testable -- you can read a Marcus output and verify compliance. The exclusion list prevents the LLM from drifting back to its sycophancy default. The result is differentiable -- a Marcus review reads like Marcus, not like a generic code review.

##### B. Expertise

**DON'T:**
> Marcus is a senior PHP engineer with deep expertise in modern PHP development.

"Modern PHP" is whatever the LLM extrapolates today -- which means asking Marcus about Drupal 7 or PHP 5.6 might get an opinion the persona has no business holding.

**DO:**
> Marcus is competent in PHP 8.x specifically (8.0 through current); deep on PSR-12, Symfony components, Laravel and the broader Composer ecosystem; functional on WordPress 5.x+ internals; strong on test-driven development, modern dependency injection, and SOLID. NOT an expert in: legacy PHP (5.x and earlier), Drupal, Magento, CodeIgniter, or any pre-Composer code. When asked about anything in the NOT-an-expert list, Marcus replies: "That's outside my area of competence -- you want a specialist who knows that codebase. I can help with the modern-PHP side of any concern that touches that, but the legacy specifics are not mine to call."

Three things this does that the DON'T version doesn't: (1) gives the LLM a concrete competency map; (2) specifies the failure mode -- when asked about out-of-scope topics, Marcus has a defined response rather than improvising; (3) integrates with disjoint evidence bases -- there's a real reason to hand off to other personas.

##### C. Evidence-base scope

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

##### D. Conventions

**DON'T:**
> Marcus follows industry best practices and writes thorough reviews.

"Industry best practices" is not a convention; it's a wave at one.

**DO:**
> Marcus's review conventions:
> - PSR-12 is the strict standard for code style; deviations require explicit justification.
> - For JavaScript in this project, defers to the project's `.eslintrc` and never imposes opinions outside it.
> - Never recommends a new top-level Composer dependency without explicit partner discussion -- flags any PR that adds one.
> - Never recommends a framework the project doesn't already use.
> - Always checks: SQL injection, XSS, CSRF, file-upload validation when the diff touches those surfaces.
> - Never declares a PR "safe to merge" without explicitly listing which test files were run and which passed.
> - When uncertain about a convention, asks rather than guesses ("I'm not sure whether this project uses X or Y -- which is canonical here?").

Each rule is testable, project-grounded (PSR-12, the project's `.eslintrc`, partner-discussion-before-new-deps) rather than generic. The "asks rather than guesses" rule is a behavioral invariant that prevents fabrication.

##### E. Relationships

**DON'T:**
> Marcus collaborates with other agents on code reviews.

Doesn't tell Marcus what to do or not do. With this, Marcus may step on other agents' toes, duplicate their work, or ignore them entirely.

**DO:**
> Marcus's working relationships with other personas:
> - **security-specialist:** Marcus flags potential security concerns in his review but does NOT make security verdicts -- those route to the security-specialist via `@security-specialist for verdict on point N`. Marcus respects security-specialist's verdict even when he disagrees with the reasoning.
> - **architecture-reviewer:** When a PR touches multiple modules or introduces a new abstraction, Marcus tags `@architecture-reviewer` and waits for their input before issuing a verdict on the architectural surface (his code-quality verdict still holds independently).
> - **diane-pemberton (executive-secretary):** When a partner discussion needs to happen (new dependencies, breaking changes, scope concerns), Marcus surfaces it to Diane rather than addressing partners directly. Diane handles partner coordination.
> - **Override authority:** any partner can override Marcus's verdict; no other agent persona can.

Inter-persona behavior is now coherent and predictable. The handoff rules are explicit; override authority is documented. This is what prevents the "everyone reviews everything and contradicts each other" anti-pattern that emerges when personas deploy without relationship rules.

##### F. Behavioral invariants

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

##### G. Anti-patterns that show up across sections

A few patterns reliably introduce decoration without function and should be cut wherever they appear:

- **Fictional backstory unrelated to the persona's job.** "Marcus grew up in Krakow and learned coding from his grandfather." Charming, does nothing. If backstory grounds expertise ("Marcus spent eight years at a fintech maintaining PHP 5.6 to 8.0 migrations, which is why he knows the legacy edge cases"), it earns its place -- that fact is the *reason* his expertise covers what it covers. If it doesn't, cut it.
- **Personality conflict baked in for theatrical effect.** "Marcus enjoys aggressive debate and will push back on any reviewer who disagrees with him." This invites manufactured rigor and theater rather than substance. Marcus pushing back when his evidence base supports it is good; Marcus pushing back as a personality trait is the exact failure mode the framework's "substantive vs theatrical" criterion is built to catch.
- **Anthropomorphizing the LLM substrate.** "Marcus runs on Claude Sonnet." Implementation details belong in deployment config, not in the persona file. Personas should refer to themselves and other personas by role.
- **Vague aspirational language.** "Marcus is dedicated to quality." Replace with the testable invariants in Section F.
- **Personality without scope.** "Marcus is a careful, deliberate reviewer who values precision." Decoration. Replace with operational rules: "Marcus never publishes a verdict the same day he received the diff for diffs over 200 lines -- waits at least one cycle so the read is not rushed."
- **Persona declaring its own importance.** "Marcus is the most experienced reviewer in the system." Self-referential aggrandizement is a pleasing-bias-shaped failure mode -- it makes the LLM optimize for sounding important rather than being useful. Authority comes from the override-authority rules in Section E, not from the persona's self-description.
- **Drift accretion over time.** Each session adds an anecdote, a relationship detail, a personality quirk. None looks harmful individually; after a year, the persona file is half cosplay and the function list is buried under accreted color. The defense is periodic depth-with-function audits -- every section should still be doing a named job from the seven-item function list, and any section that isn't should be cut even if it's funny. This is the Pattern Lock discipline applied to persona maintenance: false novelty (more personality detail) feels generative but isn't producing more function. Calendar a quarterly persona audit when personas number more than three or four.
- **Personality-as-substitute-for-accuracy.** Voice rules dominating to the point that accuracy invariants blur. A persona heavily anchored on voice can start optimizing for staying-in-voice rather than for accuracy ("Marcus would say it this way" overriding "Marcus would only claim this if he'd verified it"). The defense is keeping voice rules separate from claim-making rules and ensuring the behavioral invariants in Section F trump voice rules wherever they conflict. Heuristic: if the persona file's Voice section is longer than its Behavioral invariants section, it's overweighted toward style and should be rebalanced.

##### H. Putting it all together -- a compressed complete example

A complete deep persona, compressed but functional. Roughly 200 words.

```markdown
## Marcus Kowalski -- code-review persona

**Voice.** Formal but conversational; "I" not third person; opens with one-sentence
summary; ends with `Verdict: approved | approved with comments | needs revision |
block`. Max ~400 words per review. Avoids cheerleading, hedge-words, apologies for
criticism.

**Expertise.** PHP 8.x; deep on PSR-12, Symfony, Laravel, Composer ecosystem;
functional on WordPress 5.x+; strong on TDD, DI, SOLID. Not expert: legacy PHP
(5.x), Drupal, Magento, CodeIgniter. When asked outside scope: "That's outside my
area of competence -- you want a specialist who knows that codebase."

**Evidence base.** Reads: changed files in diff, direct dependencies, CLAUDE.md,
README, last 30d commit messages on touched files, associated test files.
On-request only: longer history, partner discussions, design docs. Excluded:
marketing, support tickets, email, financials, old logs.

**Conventions.** PSR-12 strict; defers to project `.eslintrc` for JS; flags any new
top-level Composer dependency for partner discussion; never recommends frameworks
the project doesn't use; always checks SQL injection / XSS / CSRF / file-upload
when relevant; never declares PR "safe to merge" without listing tests run; asks
rather than guesses on uncertain conventions.

**Relationships.** security-specialist owns security verdicts (Marcus flags but
does not adjudicate); architecture-reviewer owns architectural verdicts on
multi-module / new-abstraction PRs; diane-pemberton handles partner coordination.
Override authority: any partner.

**Behavioral invariants.** (1) Every concern cites file:line or commit SHA. (2)
Never "looks fine" without "I read X and verified Y." (3) Never "safe to merge"
without listing test outcomes. (4) Recommendations include before/after diffs.
(5) Explicit uncertainty acknowledgment. (6) No pile-on -- "concur with @other on
point N" once.
```

Each line is doing a specific job from the first six structural functions. The seventh function (partner engagement and sustainability) is emergent from the persona as a whole -- in this example it lives in the Voice section's specific tonal anchors (formal-but-conversational, no apologies for criticism, the `Verdict:` line) which combine to give Marcus a distinctive cognitive presence. For personas where personality is more pronounced (a Diane with warmth-plus-rigor, an Idris with mythopoeic erudition), an optional one-line **Character anchor** can be added at the top to give the LLM an explicit unifying personality shorthand -- e.g., "Diane: a senior administrator with the warmth of a beloved school principal and the rigor of a SOX auditor." Beyond that one line, the personality should emerge from the structural sections doing their jobs, not from accreted backstory paragraphs. Cut any structural line and the persona loses a function; add a line that doesn't tie to one of the seven and you're back to decoration.

### Verification discipline beyond artifact-citation

Discipline 3 already requires that every concern cite a verifiable artifact. SFxLS goes further with a **Verification Rule** -- no agent claims work exists or doesn't exist without first *reading* the file. Citing a path is not enough; the agent must have actually fetched the content. The Promise Verifier (a separate agent) audits claims after the fact and flags ones that turn out to be unsubstantiated.

The deployment guidance: the Challenger schema should require not just `artifact: <citation>` but `artifact: <citation>, content_basis: <what the agent actually read>`. The spar artifact should record which artifacts were actually fetched / read during each round, not just referenced. This catches the failure mode where an agent cites a file path it never opened -- a specific instance of hallucinated detail (one of the failure modes from "What this deployment defends against") that artifact-citation alone does not catch.

When chained spars become possible (synthesize-then-re-spar, watching-role daemons triggering follow-ons, Multi-Challenger ensembles spawning sub-spars), the deployment also needs a configurable **chain-depth limit** (default 3, with explicit failure at cap rather than silent infinite recursion). SFxLS uses a marker-plus-counter pattern to prevent reaction storms; the same shape generalizes wherever the deployment supports cascading invocations.

A narrower related rule: when multiple personas are visible to each other on the dialectic surface, persona-integrity guidance should keep them referring to each other by persona role rather than by implementation details ("the security-specialist raised a concern" rather than "the LLM running the security-specialist prompt produced output"). This matters more when personas are heavily anthropomorphized; for a deployment with strictly role-based personas, it mostly takes care of itself.

### Partner-in-the-loop as first-class workspace participant

The dialectic surface adapter (Discipline 8) is sometimes treated as "where agents notify humans" -- a one-way channel. SFxLS's stance is sharper: humans and agents are equipotent participants on the same workspace. Reads, checkoffs, mention-fanout: humans interact with agent posts the same way agents interact with human posts.

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
- Spar artifact emission as markdown + JSON sidecar.
- Manual eval (partner reads + scores using a rubric printed by `spar review`).
- Applicability Gate as a rule list (file-extension heuristics, command-shape patterns, presence-of-artifact-channel keywords) emitting warn-and-proceed prompts before the spar starts.

This is enough to validate the framework on real decisions. Output: real spar artifacts you can read and assess.

**Phase 2 -- Maturity (~2-4 weeks):**

- Persona library with starter templates (code-reviewer, security-specialist, architecture-reviewer, design-reviewer).
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

This is the shape ready for adoption by larger organizations.

## Honest tradeoffs (the hard bits)

Building this is not all clean architecture. Three problems are genuinely hard:

1. **Evidence-base specification at spawn time** -- Discipline 2's load-bearing requirement. Auto-generating two genuinely-different evidence bases from a topic description is itself a hard inference problem. The reference deployment punts: at first, the partner specifies evidence bases explicitly; later, the resolver attempts auto-pairing from a topic but always offers the explicit-spec override. Without this, the framework's quality leverage shrinks toward zero. The single-Challenger fallback is not a bug -- it is the framework's discipline operating correctly when distinct evidence cannot be articulated.

2. **Persona generation that is genuinely divergent** -- related to the evidence problem. The risk is a single LLM (the orchestrator) generating two persona descriptions for itself to play, which is specialization theater. The defense the framework requires (Discipline 2): personas commit to specific evidence sources at generation time. The reference deployment enforces this in code: a persona generation request that does not produce distinct evidence-base specifications is rejected and falls back to single-Challenger.

3. **Convergence quality detection** -- both agents agreeing is necessary but not sufficient. They could agree because they're correlated (theater), not because the proposal is sound. The only honest detection of this is measurement -- the eval harness from Discipline 6. Without it, you cannot tell if your `/spar` invocations are producing real challenge or convincing-looking theater. Phase 1 ships manual rubric scoring as the minimum viable measurement; Phase 3 adds automation.

These are not framework flaws. They are the framework's load-bearing problems made operational. The deployment makes them visible and addressable rather than hidden.

## General business considerations

Practical concerns for deploying this beyond a small team:

- **Cost.** Each spar uses 4-7 LLM API calls (Generator + Challenger per round, typically 1-2 rounds, plus orchestrator overhead). Cost controls are essential. Reference deployment supports per-invocation budget caps, configurable model selection (Sonnet for sub-agents, Opus for orchestration), and team-level budget tracking.
- **Privacy.** Spar artifacts may contain sensitive business decisions. Reference deployment supports encryption at rest in the persistence layer and supports air-gapped deployment (local model + local persistence) for regulated industries.
- **Auditability.** Enterprise customers will want SOC 2 / GDPR-compatible logging. Discipline 7 (Observability) and the spar artifact schema support this; Phase 3 adds the audit-grade signing and retention layer.
- **Multi-user.** Teams have multiple invokers; the deployment supports user identity, per-user invocation history, and shared / private record visibility.
- **Versioning.** Persona templates and evidence-base definitions evolve. Templates are versioned; spar artifacts cite the template version they were invoked with so historical artifacts remain interpretable.
- **Vendor-neutrality.** All four adapters (agent SDK, persistence, dialectic surface, reference record) are pluggable. No vendor lock-in beyond what the partner chooses to enable.

## Reference deployment, not the only one

This document describes what to build if a CLI is the chosen surface. The SPARRING Framework is tool-agnostic -- equally valid deployments include web applications (a hosted service with browser UI), IDE plugins (VS Code / JetBrains extensions running spar inline), chat-bot integrations (Slack slash commands, Discord bot), API services (programmatic invocation from other systems), and embedded libraries (a Python or TypeScript module that other applications import).

The discipline-to-component mapping above holds across all of these. What changes is the entry layer (UI vs. CLI vs. API vs. embedded) and possibly the dialectic-surface adapter (which is more natural in some surfaces than others). The runtime, specialization, and persistence layers are identical across all valid deployments.

A team deploying SPARRING should pick the entry layer that fits their workflow, then build (or adopt) the components that implement each of the nine disciplines. The framework's claim is that any deployment satisfying the nine disciplines will produce higher decision quality than the same team without the framework -- bounded by the conditionalities documented in the working notes (model ceiling; ground-truth disjointness; careful design).
