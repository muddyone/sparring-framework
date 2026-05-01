# SPARRING and existing decision frameworks -- working notes

*Internal working draft. Lives in `docs/bfn/`, not in the professional-distribution document set. Sibling to [`sparring-framework-notes.md`](sparring-framework-notes.md).*

*Created: 2026-05-01.*
*Status: **WORKING DRAFT**. First framework examined (Torres / Continuous Discovery Habits / OST) sketched but not spar-validated. Further frameworks (Lean Startup, premortems, ADRs, design thinking, RAT) to be added below as time permits, on the assumption that the same argument shape recurs.*
*Origin: an external suggestion (ChatGPT-mediated) that the SPARRING Framework rhymes structurally with Torres's product-discovery work. This doc tests that suggestion against the canonical SPARRING sources before any public claim is made.*

---

## 1. What this document is

The staging ground for a larger argument: **SPARRING is the structural layer that lets human-engineered decision frameworks survive when LLM agents are added to the loop.** The first framework examined is Teresa Torres's Continuous Discovery Habits, because the resemblance was suggested directly. Subsequent comparisons go below as they're run.

This document is **not**:

- A LinkedIn post. Compressed positioning copy is downstream of this draft (see §7 Extraction targets).
- An addition to the v1.0 professional-distribution preprint. The Reader's Guide deliberately excludes PM/discovery audiences; expanding that is a v1.1 scope question.
- A finished or spar-validated comparison. Open questions are tagged inline as **TBD**.

## 2. Why this comparison is worth running

Torres's *Continuous Discovery Habits* (2021) ships an Opportunity Solution Tree -- Outcome -> Opportunities -> Solutions -> Experiments -- and a set of habits (continuous customer interviews, assumption testing, multiple-solution discipline) for high-trust product trios. The framework is human-optimized: it relies on the trio doing the right things, not on structural enforcement.

Two reasons the comparison earns its space:

1. **Both are decision-quality frameworks.** Torres defends decision quality through habits; SPARRING defends decision quality through structural disciplines. They sit in the same neighborhood.
2. **The agentic transition is happening on Torres's surface right now.** Discovery teams are already piping LLM agents into interview synthesis, opportunity clustering, and experiment design. Whether OST holds under agent mediation is an open question Torres herself has begun to engage publicly.

The actual lane is the second reason: not "SPARRING is like OST," which is shallow, but "OST has a known-failure surface when agents enter the loop, and SPARRING names what would have to be true for the failures not to compound."

## 3. The relationship, stated correctly

**Common-but-wrong framing:** SPARRING phases (SPARK, Pattern Lock, the Cut, PNP) map column-by-column onto OST nodes (Outcome, Opportunities, Solutions, Experiments) -- "PNP at the Outcome, SPARK at Opportunities, the Cut between Opportunities and Solutions, PNP at Solutions, PNP at Experiments." This is the framing the source ChatGPT report and the accompanying infographic use. It is wrong in three ways:

- It treats single SPARRING phases as standalone validation tools, severed from the four-phase loop. SPARRING's quality leverage comes from running the loop with two roles on one decision -- not from labeling a checkpoint with a phase name. Discipline 4 (mutual-agreement convergence) requires the loop, not the labels.
- It reduces SPARRING to vocabulary attached to "places to think harder," which is exactly the theatrical-adversariality risk Appendix D of the framework notes warns about.
- It implies SPARRING is *layered on top of* OST when in fact the two address different layers entirely -- OST organizes the discovery space; SPARRING governs how individual decisions inside that space are made.

**Correct framing -- decision-boundary wrap:** OST is a map of decision boundaries. A discovery cycle has at least four:

- *Outcome decision.* Is "onboarding completion" the right outcome, or is "first-value moment" closer to what the business needs?
- *Opportunity selection.* Of the surfaced opportunities, which is the priority?
- *Solution selection.* Of the candidate solutions, which one ships first?
- *Experiment design.* What is being tested, and what would falsify it?

A SPARRING ceremony at any of these boundaries runs the full SPARK -> Pattern Lock -> Cut -> PNP loop with two roles, distinct evidence bases, and mutual-agreement convergence -- not a single phase masquerading as the boundary's validation step.

**Compressed framing -- discipline injection:** OST already pairs with assumption testing (Torres explicitly cites RAT-style methodology, inherited from Lean Startup). The actual gap is that human-discipline assumption testing assumes the assumption-tester is a person doing the work in good faith. When the assumption-tester is an LLM agent, three of SPARRING's nine disciplines become *structurally* required, not merely good practice:

- **D2 -- distinct evidence bases.** Two agents with the same training distribution and the same prompt produce more correlated outputs than they appear to. A human trio doesn't have this problem; a multi-agent assumption test does.
- **D3 -- verifiable-artifact requirement.** Humans cite real evidence in good faith; LLM agents can hallucinate citations that look authoritative. Assumption testing under agent mediation needs the artifact-or-it-doesn't-count rule.
- **D4 -- mutual-agreement convergence.** A trio can credibly say "we agree, we're done"; an LLM agent's "we're done" is structurally suspect under pleasing-bias drift.

Disciplines D6 (measurability) and D7 (observability) are equally load-bearing -- the only way to know whether an agent-mediated discovery cycle is producing real challenge or theater is to score Challenger outputs and persist spar artifacts.

The compressed claim:

> **OST organizes the discovery space. SPARRING enforces the structural disciplines that allow individual decisions inside that space to remain trustworthy when agents mediate any part of the work.**

## 4. The delta vs. what OST already pairs with

Torres does not leave OST without a validation layer. She pairs it with assumption testing -- most prominently the Riskiest Assumption Test (RAT), inherited from Lean Startup. A defensible comparison must say what SPARRING adds *beyond* what Torres already prescribes.

| Concern | OST + RAT | SPARRING |
|---|---|---|
| Distinct viewpoints in assumption testing | Implicit -- relies on trio diversity | Explicit -- D2 requires distinct evidence bases |
| Evidence requirement on challenges | Implicit -- discovery interviews provide grounding | Explicit -- D3 dismisses unverifiable concerns as theater |
| Convergence signal | Trio judgment | Mutual-agreement; cap returns disagreement to a human or parent agent |
| Failure mode addressed | Solution bias, premature commitment, weak experiments | All of the above plus pleasing-bias compounding under agent mediation |
| Boundary of effectiveness | Human discipline | Structural discipline that survives agent mediation |

The argument is not that Torres missed the gap. She doesn't have the gap on a strict-human team. The argument is that her gap-free framework develops a gap when agents are added, and SPARRING is engineered to stay closed under that condition.

## 5. Where the human / agent boundary actually sits

The source analysis frames Torres as human-only and SPARRING as agentic-only. That binary doesn't survive the spec.

- SPARRING explicitly supports human-in-the-Generator and human-in-the-Challenger variants. A product trio could run SPARRING on themselves with no agents in the loop, and the framework would work.
- Torres has publicly engaged AI-in-discovery work (interview synthesis, opportunity-cluster generation). OST is not strictly anti-agent.

The correct boundary is not "Torres = humans, SPARRING = agents." It is:

> **As LLM agent mediation increases in a discovery cycle, SPARRING's structural disciplines (D2/D3/D4/D6/D7) move from "good practice that mature trios already approximate" to "structurally required for the framework's quality leverage to survive."**

Continuum, not dichotomy. The agentic transition is where SPARRING becomes load-bearing -- not where it becomes relevant for the first time.

## 6. Worked example -- corrected

The source analysis uses an onboarding-completion example. The example survives; the application reshapes.

**Setting.** A product team running a CDH cycle with LLM agents in interview synthesis, opportunity clustering, and experiment design. Outcome candidate: "increase onboarding completion."

**Decision boundaries and ceremonies.**

- *Boundary 1 -- Outcome.* SPARRING asks: is completion the right outcome, or is "first-value moment" / "30-day retention" closer to what the business needs? Generator: the PM. Challenger: an agent grounded in the company's revenue and retention data, with an evidence base distinct from the synthesis agent's interview-derived signal.
- *Boundary 2 -- Opportunity selection.* SPARRING on which opportunity to prioritize. SPARK exercised seriously to avoid premature lock-in on the loudest interview-derived opportunity; Pattern Lock catches the case where the synthesis agent has rephrased one underlying need three different ways.
- *Boundary 3 -- Solution selection.* The known anti-pattern: the team converges on "add a checklist" because the synthesis agent over-weighted "users feel overwhelmed." Challenger asks whether the checklist optimizes the metric (completion) at the expense of the underlying first-value experience.
- *Boundary 4 -- Experiment design.* Challenger asks what assumption is being tested, what would falsify it, what secondary effects are unmeasured. D3 enforced: every challenge cites an evidence source the experiment design has not yet addressed.

The point is *not* that SPARRING phases match OST nodes one-to-one. It is that a discovery cycle has multiple decision boundaries, and SPARRING ceremonies can be invoked selectively where agentic mediation creates compounding-failure exposure. A team can spar one boundary, two, or all four, depending on stakes.

## 7. Extraction targets

Downstream artifacts derived from this working draft. Updated as each moves through its lifecycle (PLANNED -> DRAFT -> SHIPPED with link). LinkedIn posts that ship from this draft become Series 2 of the LinkedIn post tree (`docs/bfn/linkedin/posts/02-decision-frameworks-*.md`); Series 2 follows the SPARRING framework series (Series 1) with a 1-2 week gap so each series lands cleanly.

| Artifact | Surface | Status | Notes |
|---|---|---|---|
| Agentic-compounding-in-discovery LinkedIn post | LinkedIn -- `posts/02-decision-frameworks-01-agentic-compounding.md` (planned path) | PLANNED | Lede: the problem (compounding LLM failure modes hit Torres-style discovery the moment agents enter the loop). Anchors on Torres respectfully -- book + OST attribution. Closes with link to the canonical professional-distribution preprint. |
| Discipline-injection LinkedIn post | LinkedIn -- `posts/02-decision-frameworks-02-discipline-injection.md` (planned path) | PLANNED | Lede: the framing (D2/D3/D4 move from good-practice to structurally-required under agent mediation). Names the three disciplines specifically. Same closing as above. |
| Worked-example LinkedIn post | LinkedIn -- `posts/02-decision-frameworks-03-worked-example.md` (planned path) | PLANNED | The four boundaries from §6, made concrete. May be a carousel rather than a single post. |
| Self-applied spar artifact (credibility move) | `.claude/spars/<date>/` + narrative companion at `docs/bfn/spars/` | PLANNED | Run a real `/spar` on one of the post drafts (Marcus on the SPARRING side; a PM-discovery-grounded Challenger on the Torres side); link to the artifact from one of the posts. Walking the talk on a series that names epistemic discipline. |
| v1.1 companion document: SPARRING in relation to human-discipline decision frameworks | `docs/bfn/public/sparring-framework/professional-distribution/` | DEFERRED | Not v1.0 scope. Awaits stabilization of multiple framework comparisons in this draft. The companion will need its own honest-self-critique section (see §9) modeled on Spec §5.3. |

**Extraction discipline:** anything that ships gets a link back from this register; any drift between the artifact and this draft gets resolved in favor of whichever has had real challenge run on it.

## 8. Open questions (TBD)

- **TBD-1.** Does Torres-style assumption testing satisfy a weak version of D2 in practice (interview evidence as distinct from the team's prior beliefs), and if so, where exactly does agent mediation break that satisfaction?
- **TBD-2.** Is "the agentic continuum" (§5) the right phrasing, or is there a sharper threshold condition (e.g., "the moment a single agent's output becomes input to a second agent without human inspection")?
- **TBD-3.** What's the right shape for a Lean Startup / Build-Measure-Learn comparison? Probably the same -- SPARRING ceremonies at hypothesis-formation and learning-interpretation boundaries -- but the comparison hasn't been run.
- **TBD-4.** Does the v1.1 companion-doc plan need to land before the LinkedIn post, or can the post stand alone with the spec link?
- **TBD-5.** Public-facing handling of the credibility-recursion problem (a SPARRING document about SPARRING needs to itself be SPARRING-validated, or honestly flag that it isn't).

## 9. Self-discipline note (seed for v1.1 honest-self-critique section)

This document has not yet been put through a `/spar`. By Discipline 2 and Discipline 3, the claims here are the working assertions of one author, not converged-via-mutual-agreement findings. The credibility-recursion problem is real: a document arguing for structural challenge that has not itself undergone structural challenge is in a credibility hole. Closing it requires running a real spar -- Generator and Challenger with distinct evidence bases (Marcus on the SPARRING side; a PM-discovery-grounded Challenger on the Torres side) -- on the central claim of §3 and §4 before any public version of this argument ships.

This section is the **seed** of an equivalent section in the eventual v1.1 professional-distribution companion document, modeled on the Specification's §5.3 ("Honest critique applied to the framework itself"). The formal version will be more developed than this seed because, by the time it ships, real spars will have run and produced concrete material to critique.
