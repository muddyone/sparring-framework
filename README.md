# SPARRING Framework

A framework for structured AI deliberation: SPARK / Pattern Lock / The Cut / PNP, with the SPARRING two-role mechanic. Originally developed by Bart Niedner under Resource Forge LLC.

## What this is

SPARRING is a methodology for getting better answers out of multi-agent AI systems. It addresses a class of compounding LLM failure modes -- sycophancy, confirmation bias, anchoring, blind spots, hallucinated detail, confidently-wrong outputs, bandwagon contamination -- by structuring agent collaboration into a four-phase ceremony (SPARK, Pattern Lock, The Cut, PNP) with two roles (Generator and Challenger) and nine disciplines (verifiable artifacts, role + domain knowledge, both-must-agree convergence, surfaced-work routing, dialectic surface, reference record, measurability, observability, self-citation circularity check).

## What's here

- **[`framework/`](framework/)** -- the core framework documents:
  - [`notes.md`](framework/notes.md) -- working-notes scratchpad. Canonical for in-progress thinking; mature content extracts into the standalone documents below.
  - [`reference-deployment.md`](framework/reference-deployment.md) -- production deployment patterns.
  - [`deployment-walkthrough.md`](framework/deployment-walkthrough.md) -- getting-started guide for small teams.
  - [`decision-frameworks.md`](framework/decision-frameworks.md) -- comparisons with adjacent decision frameworks (Torres / Continuous Discovery Habits / Opportunity Solution Tree).
  - [`monte-carlo-design.md`](framework/monte-carlo-design.md) -- eval-harness design notes (Monte Carlo for SPARRING).

- **[`pilots/`](pilots/)** -- empirical work testing the framework:
  - [`llm-judge-2026-05-02/`](pilots/llm-judge-2026-05-02/) -- LLM-as-judge calibration pilot.
    - V1 closed (FAIL @ ρ = 0.351 with surfaced methodology findings; preprint draft at [`v1/preprint-draft.md`](pilots/llm-judge-2026-05-02/v1/preprint-draft.md)).
    - V2 locked in pre-registration ([`v2/pre-registration.md`](pilots/llm-judge-2026-05-02/v2/pre-registration.md), tag `v2-prereg-2026-05-03`); partner-rating phase active.

## Implementing SPARRING in your environment

The framework is methodology, not code. The `/spar` ceremony spec lives in [`framework/notes.md`](framework/notes.md) (the SPARRING ceremony sections); the production deployment patterns live in [`framework/reference-deployment.md`](framework/reference-deployment.md). Anyone implementing `/spar` in their own environment reads the spec and writes their own integration.

The reference Lifspel implementation that produced the V1 and V2 pilot data lives in a private Resource Forge codebase and is not redistributed; the methodology and the data are.

## Pre-registration discipline

Each pilot version is locked at a specific commit + tag (e.g., `v2-prereg-2026-05-03`) before any compute is spent. Protocol amendments after compute starts must be reported as deviations per [Nosek et al. 2018](https://www.pnas.org/doi/10.1073/pnas.1708274114) preregistration discipline. The locked commit is the immutable methodological record; subsequent commits document the experimental run.

## License

- Documentation, pre-registration documents, decision packs, condition outputs, partner ratings, judge results, preprint draft -- everything textual: [Creative Commons Attribution 4.0 International (CC BY 4.0)](LICENSE).
- Analysis scripts (anything under `pilots/*/v*/scripts/`): [Apache License 2.0](LICENSE-CODE).

When citing this work, see [`CITATION.cff`](CITATION.cff) for machine-readable citation metadata, or attribute the framework as:

> Niedner, Bart. *SPARRING Framework*. Resource Forge LLC, 2026. https://github.com/muddyone/sparring-framework

## Working pattern

The `framework/notes.md` file is the working-notes scratchpad; mature content is extracted into standalone documents per the workflow described inside that file. Pre-registrations are immutable -- once a pilot version is locked, the corresponding commit and tag do not move; deviations after compute starts are reported as protocol amendments.

The repo's commit history is preserved from its origin in a private Resource Forge codebase via `git filter-repo`, so the pre-registration tags' SHA-as-immutable-reference promise survives the public extraction.
