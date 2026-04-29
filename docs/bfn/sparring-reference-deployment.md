# SPARRING Framework -- Reference Deployment (CLI)

*A practical architecture for deploying the SPARRING Framework as fully aligned as possible for general business use from a command-line interface. Reference deployment, not the only one -- the framework is tool-agnostic, and other shapes (web app, IDE plugin, slash-command bot) are equally valid. This document describes what to build if a CLI is the chosen surface.*

*Source of truth for the SPARRING reference deployment.*

*Companion working notes (scratchpad / larger conversation): [`sparring-framework-notes.md`](sparring-framework-notes.md).*
*Extracted from working notes rev. 17 on 2026-04-29.*

---

## Discipline-to-component mapping

Every one of the nine disciplines from the SPARRING Framework Overview maps to at least one build component. A deployment is "fully aligned" with the framework when every discipline has a concrete component implementing it.

| Discipline | Component |
|---|---|
| 1. Apply to decisions, not every prompt | CLI invocation discipline -- user-invoked, not auto-fired |
| 2. Different evidence between Generator and Challenger | **Evidence-base resolver** with explicit fallback to single-Challenger when distinct evidence cannot be articulated |
| 3. Verifiable artifacts for every concern | **Challenger output schema** that requires artifact citations; concerns without artifacts are dismissed |
| 4. Both roles must agree to converge | **Iteration controller** with explicit two-signal agreement check, mechanical fallback at iteration cap |
| 5. Observable triggers for self-invocation | **Trigger registry** with concrete observable conditions (file patterns, command flags, partner-passed hints), not LLM-self-assessed uncertainty |
| 6. Measurability | **Eval harness** with rubric-scored review on a sample of past spar artifacts |
| 7. Observability | **Spar artifact emitter** producing structured persistent records of every ceremony |
| 8. Dialectic Surface (active communication) | **Dialectic surface adapter** -- pluggable integration with Slack/Discord/Teams/issue-tracker/email |
| 9. Reference Record (persistent curated record) | **Reference record store** -- pluggable backend (filesystem, git-tracked markdown, SQLite, wiki API, S3) |

The reference architecture below is built around these components.

## Architecture

Ten major components, organized into four layers:

**Entry layer:**

- **CLI** -- the partner-facing entry point. Subcommand structure modeled after `git` / `kubectl` / `terraform`. Top-level command is `spar`.

**Runtime layer:**

- **Agent runtime** -- spawns Generator and Challenger sub-agents via an LLM agent SDK (Claude Agent SDK as the reference; OpenAI Agents SDK and others supported via adapter).
- **Iteration controller** -- runs the Generator -> Challenger -> agreement-check loop with configurable iteration cap. Detects convergence (both `agree: true`), unresolved disagreement (cap hit without both true), or fallback (single-Challenger when distinct evidence unavailable).
- **Trigger registry** -- maintains observable trigger definitions; can be queried to determine whether a self-invocation should fire (used in Variants supporting agent self-spar; orthogonal for partner-invoked spars).

**Specialization layer:**

- **Persona library** -- pre-built persona templates (code-reviewer, security-specialist, architecture-reviewer, etc.) plus user-defined templates.
- **Evidence-base resolver** -- at spawn time, identifies and assigns distinct evidence bases to Generator and Challenger. Sources include local file paths (a corpus directory), MCP tool servers (different tools per role), vector stores (with role-scoped namespaces), or external APIs. Falls back to single-Challenger mode with explicit notice when distinct evidence cannot be articulated.
- **Domain template registry** -- pre-built SPARRING configurations for common decision types (security review, plan review, vendor selection, hire decision); each template specifies recommended persona pairings, evidence-base specifications, and tuned Challenger questions.

**Persistence layer:**

- **Spar artifact emitter** -- produces a structured artifact (markdown + JSON sidecar) recording the topic, both personas with their evidence bases, the iteration log, agreement signals, artifacts cited, and the converged result or unresolved disagreement.
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
| Persona/evidence resolver | Phase 2 (auto-pairing) | 1 | Pre-spar | Once per spar |
| Watching-role Challenger | Phase 3 (continuous) | 1 per watched system | Long-running daemon | Continuous |
| LLM-as-judge | Phase 3 (automation) | 1 per artifact evaluated | One-shot | Periodic eval passes |
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

This is enough to validate the framework on real decisions. Output: real spar artifacts you can read and assess.

**Phase 2 -- Maturity (~2-4 weeks):**

- Persona library with starter templates (code-reviewer, security-specialist, architecture-reviewer, design-reviewer).
- Evidence-base resolver with file-path and basic RAG support.
- Auto-pairing for personas based on topic analysis (with explicit fallback to single-Challenger).
- Domain templates (code-review, architecture-decision, vendor-selection, hire-decision, plan-review).
- Eval harness with structured rubric tooling.
- Slack and email adapters for dialectic surface integration.
- Variant support: phase-isolation modes and Multi-Challenger ensemble.

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
