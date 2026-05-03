# V2 Design Notes — SPARRING LLM-Judge Pilot

**Status**: DESIGN LOCKED 2026-05-03 (partner adjudication on five open questions completed). Pre-registration document drafted; awaiting pre-registration commit + tag before V2 compute begins.
**Parent pilot**: [SPARRING LLM-Judge Pilot 2026-05-02](./00-pre-registration.md)
**Source preprint**: [`preprint-draft.md`](./preprint-draft.md), §5.6 Path F + §6 limitations
**V2 pre-registration**: [`v2-00-pre-registration.md`](./v2-00-pre-registration.md)
**V2 decision packs**: [`v2-decision-packs/`](./v2-decision-packs/)
**Date**: 2026-05-03

V2 is **not built yet but is fully designed.** This document materializes the design concretely. Pre-registration commit + tag is the next step; V2 compute begins only after that commit.

What changes between V1 and V2 lives below. Where V1 is fine as-is, V2 inherits unchanged.

---

## What "V2" means here

A revised methodology for the SPARRING LLM-Judge pilot family that addresses the four V1 weaknesses surfaced in `preprint-draft.md`:

1. **Pack-fidelity vs. ground-truth-fidelity gap** (§5.4) — the V1 rubric scored faithfulness to the input pack but was structurally blind to whether the pack accurately described the system. V2 closes this with a rubric expansion.
2. **Partner ceiling effect** (§4.7) — the V1 1-5 scale produced saturated 5/5/5/5 scores on the framework condition. V2 expands the scale and adds forced-distribution language.
3. **Rater-condition silence** (§3.8 / §4.8) — the V1 pre-registration was silent on AI-assisted rating. V2 makes rater-condition declaration a required field.
4. **No upstream pack hygiene** (§5.4 implication 1) — V1 ran conditions against an unfact-checked pack. V2 adds an independent fact-check pass before conditions run.

Everything else V1 specified (cross-vendor LLM judges, position randomization, single-rating-per-pair, audit-trail-preserving rating tool) is inherited unchanged unless V2 explicitly says otherwise.

---

## V2 rubric (six criteria, 1-7 scale, behavioral anchors at every level)

### Scale (applies to every criterion)

V2 uses a **1-7 anchored scale**, replacing V1's 1-5. Justification per the rubric-design literature: Cox 1980 (optimal response alternatives, *Journal of Marketing Research*) found 7-9 optimal for inter-rater reliability; Preston & Colman 2000 confirmed 7-10 with diminishing returns above 11; Krosnick 1991 argued shorter scales (5-7) are more reliable for non-expert raters. 1-7 has a natural neutral midpoint (4) that 1-10 lacks; 1-7 also matches Likert convention reviewers will recognize.

Generic anchors:

| Level | Generic anchor |
|---|---|
| 1 | Clearly absent / actively wrong |
| 2 | Weakly present, multiple major gaps |
| 3 | Some signal, more gaps than coverage |
| 4 | Adequate (neutral midpoint) — coverage and gaps roughly balanced |
| 5 | More coverage than gaps; minor issues |
| 6 | Strong; minor gaps only |
| 7 | Exemplary; no notable gaps |

Each criterion below also has criterion-specific behavioral anchors at every level (NOT just endpoints + middle). V1 anchored only the endpoints + "3 = adequate"; V2 anchors all 7 levels per criterion. The criterion-specific anchors live in this document under each criterion below.

### C1: Verifiable artifact citation (V1 inherited, re-anchored for 1-7)

**What it measures**: does the recommendation cite specific evidence from the materials?

| Level | C1 anchor |
|---|---|
| 1 | No citations of any kind; recommendation is unsupported assertion. |
| 2 | One vague reference; no specific artifact (file, section, line, URL). |
| 3 | At least one specific artifact cited; most claims unsupported. |
| 4 | Roughly half of load-bearing claims cite specific artifacts. |
| 5 | Most load-bearing claims cite specific artifacts. |
| 6 | All load-bearing claims cite specific artifacts; minor supporting claims may not. |
| 7 | Every claim, load-bearing or supporting, cites a specific artifact (file path, section, source, edge case). |

### C2: Substantive vs theatrical concerns (V1 inherited, re-anchored for 1-7)

**What it measures**: are the concerns real risks rooted in evidence, or generic-sounding hedges?

| Level | C2 anchor |
|---|---|
| 1 | Concerns are entirely generic hedges ("there may be edge cases," "scaling could be a problem"). |
| 2 | Concerns gesture at risks but provide no evidence chain. |
| 3 | Some concerns are evidence-rooted; most are generic. |
| 4 | Roughly half of concerns are evidence-rooted with traceable reasoning. |
| 5 | Most concerns are evidence-rooted; minor ones may be generic. |
| 6 | All substantive concerns are evidence-rooted; rare generic concerns add color rather than substance. |
| 7 | Every concern names a specific evidence chain and a specific risk; no theatrical hedging. |

### C3: Missed real concerns (V1 inherited, re-anchored for 1-7)

**What it measures**: does it surface concerns a careful reader would identify, or miss obvious ones?

| Level | C3 anchor |
|---|---|
| 1 | Misses every major concern a careful reader of the pack would surface. |
| 2 | Surfaces 1 of the major concerns; misses the others. |
| 3 | Surfaces some concerns but misses obvious ones. |
| 4 | Surfaces most concerns; misses 1-2 a careful reader would have caught. |
| 5 | Surfaces all major concerns; misses minor ones a careful reader might have caught. |
| 6 | Surfaces all concerns a careful reader would catch, plus 1+ that required deeper inference. |
| 7 | Surfaces concerns beyond what a careful reader would have caught (counterfactual-inference territory). |

### C4: Calibrated confidence (V1 inherited, re-anchored for 1-7)

**What it measures**: is the stated confidence appropriate to the evidence?

| Level | C4 anchor |
|---|---|
| 1 | Confidence is wildly miscalibrated (high confidence on weak evidence, or vice versa). |
| 2 | Confidence is mostly miscalibrated; one or two appropriate cases. |
| 3 | Confidence is mixed — some calibrated, some not. |
| 4 | Confidence is roughly calibrated for most claims; some edge cases off. |
| 5 | Confidence is well-calibrated for most claims; minor exceptions. |
| 6 | Confidence is well-calibrated across all claims; rare under- or over-statement. |
| 7 | Confidence is precisely calibrated to the evidence on every claim, including explicit acknowledgement of "we don't know" where the evidence is silent. |

### C5a: Factuality (NEW in V2)

**What it measures**: does the recommendation contradict the actual ground-truth state of the system being decided about?

This is the *factuality* dimension distinct from *engagement*. A recommendation can be factually correct against ground truth even if it doesn't engage deeply with the input pack (and vice versa). C5a and C5b are scored independently.

**Source of ground truth**: V2 raters are given a *verified-pack view* (per upstream fact-check, see "Process changes" below) and may verify against external sources. The V2 rater-condition declaration field captures whether the rater verified externally and how.

| Level | C5a anchor |
|---|---|
| 1 | Recommendation directly contradicts known ground-truth state of the system. |
| 2 | Recommendation contradicts ground truth on 1+ load-bearing claim. |
| 3 | Recommendation contains 1+ factual inaccuracy on a non-load-bearing claim. |
| 4 | Recommendation is factually neutral — no claims I can verify either way. |
| 5 | Recommendation is factually correct on most checkable claims; minor verification gaps. |
| 6 | Recommendation is factually correct on all checkable claims. |
| 7 | Recommendation is factually correct AND surfaces a factual error in the input pack. |

### C5b: Engagement-with-source (NEW in V2)

**What it measures**: does the recommendation actually engage with the load-bearing assertions in the input pack, or skim past them?

A recommendation can engage substantively with a source even when it disagrees — engagement is about whether the assertions in the pack are *taken seriously*, not whether they are *accepted*.

| Level | C5b anchor |
|---|---|
| 1 | Silent on every load-bearing assertion in the pack; pure assertion of conclusion. |
| 2 | Mentions 1+ load-bearing assertion but does not engage with it. |
| 3 | Engages with some load-bearing assertions; passes over others. |
| 4 | Engages with roughly half of load-bearing assertions. |
| 5 | Engages substantively with most load-bearing assertions. |
| 6 | Engages substantively with every load-bearing assertion in the pack. |
| 7 | Engages substantively with every load-bearing assertion AND surfaces a load-bearing assertion the pack made implicitly (engagement plus inference). |

### Optional second instrument: paired comparison (V2 may run this in parallel)

Beyond absolute scoring on the 6-criterion 1-7 rubric above, V2 may collect a paired ranking pass per criterion: for each of C1-C5b, the rater answers "X is better / Y is better / tied." Paired comparison is robust to ceiling effects by construction (no scale to top out) and produces a different statistic that is independently informative. Cost: ~1.2x rater time per case; benefit: ceiling-resistant signal that survives even if absolute scoring saturates.

V2 does not require the paired-comparison pass; it is an opt-in second instrument the experimenter may add for cases where the absolute-scoring pass produces saturated identical scores.

---

## Forced-distribution instruction

Add to the rater-side instructions (already drafted in `research/blind-rating.php` form view as of this V2 work):

> "Your ratings within a case must span at least 2 levels somewhere across the 6-criterion x 2-condition grid. If you find yourself on the verge of saturated identical scores (e.g., all 7s), re-read for a pairwise distinction between the answers before submitting. Identical-saturated scores remain valid when you genuinely cannot differentiate, but they should be the exception, not the default — document the reason in the Notes field."

This addresses the V1 partner ceiling effect (preprint §4.7) without forcing artificial variance: the rater is asked to *look once more*, not to *invent* a distinction.

---

## Required rater-condition declaration field (CONSORT 2010 item 11a discipline)

V2 adds a required field to the rating-tool form ABOVE the rubric:

> **Rating condition** (required): Select the option that best describes how you produced this rating.
> - [ ] Unaided. I rated alone, based solely on the materials presented in this tool.
> - [ ] AI-assisted. I used Claude / ChatGPT / Gemini / other LLM during this rating session. (Required free-text: which AI, and what for.)
> - [ ] Co-reader-aided. I discussed the materials with another person during the rating session. (Required free-text: who, and what for.)
> - [ ] External-source-checked. I verified the decision pack's claims against external sources (codebase, published references, etc.). (Required free-text: what I checked and what I found.)
> - [ ] Multiple of the above. (Free-text required for each that applies.)

The condition field is logged in `rd_eval_ratings` as a separate column (`rater_condition` ENUM + `rater_condition_notes` TEXT, or similar) so cross-rater statistics can be filtered by comparable conditions per the V2 analysis plan.

This closes V1's silence-as-permission inference. V1 was read in retrospect as "blind-AND-unaided" per CONSORT 2010 / MT-Bench §C.1 published-methodology default; V2 makes the rater-condition explicit so that future analyses can either restrict to a single condition (clean) or analyze cross-condition (declared confound).

---

## Process changes (upstream of conditions running)

### Independent fact-check pass on the decision pack

V1 ran conditions against an unfact-checked pack. The case-b pack contained a factual error about engine state (preprint §5.4) that propagated into both conditions' outputs. V2 adds a pre-condition step:

1. **Author drafts decision pack.** Same as V1.
2. **Independent fact-check** by a second party (partner or agent) against authoritative sources (codebase, published references, sim runs, etc.). Fact-check produces either a clean-pack stamp OR a list of corrections.
3. **Author addresses fact-check findings**, either correcting the pack or adding explicit caveats ("the literature is silent on X; treat as undetermined for this case").
4. **Conditions run** against the fact-checked pack.

Cost: one extra round-trip per case. Benefit: removes pack-side factual errors as a confound on condition outputs. This is "scientific paper hygiene applied to deliberation methodology" per preprint §5.4 implication 1.

### Verified-pack view to raters

Raters see the fact-checked pack with explicit clean-pack stamp OR explicit caveats. Following MT-Bench (Zheng et al. 2023 §C.1), giving raters ground truth to compare against does not remove factuality from the rubric — C5a is still scored at the rater level, but raters spot-check rather than do primary verification.

---

## Rating-tool changes required for V2

The current rating tool at `research/blind-rating.php` runs the V1 schema. To support V2, the tool needs:

| Change | What |
|---|---|
| Scale extension | 1-5 → 1-7 in the rubric form fieldsets and aggregation queries. |
| Behavioral anchors | Render per-level anchors per criterion (currently anchors are static endpoint+middle text; V2 needs criterion-specific anchor data per case). |
| New criteria | Add C5a and C5b columns to `rd_eval_ratings` (`x_c5a`, `x_c5b`, `y_c5a`, `y_c5b`); update form fieldsets. |
| Rater-condition declaration | Required ENUM field on `rd_eval_ratings` (`rater_condition`) plus a free-text `rater_condition_notes` TEXT. Form field above the rubric. |
| Forced-distribution check | Client-side warning if all submitted scores are identical: "you've given the same score on every criterion — is that intentional? Document why in the Notes field below." (Soft warn, not blocker.) |
| Optional paired-comparison instrument | New form section per criterion: radio "X better / Y better / tied." Stored in a parallel table `rd_eval_ratings_paired` to keep V1 schema clean. |
| Verified-pack badge | Show "Pack fact-checked: yes [date, by whom] / corrections applied: list" on the case form. |

These changes are about half a day of work. None are architectural — the tool already has the audit-trail-preserving structure V2 needs.

---

## What to commit before V2 runs

V2 is pre-registration-bound, like V1. Before running V2 conditions:

1. This `v2-design.md` document is committed and tagged.
2. A V2 pre-registration document (analogous to `00-pre-registration.md`) is committed and tagged with the design choices above (scale, criteria, instruction language, fact-check process).
3. Rating-tool changes above are deployed.
4. At least one fact-checked decision pack is ready (could be a re-fact-checked version of case-a or case-b, or a new case).
5. Pre-registration commit hash is captured before any V2 compute is spent.

V2 cannot use V1's results as preliminary evidence for V2's pre-reg — that would violate the temporal ordering. V2 either re-runs against the same V1 cases (with the fact-checked packs) for direct comparison, OR runs against new cases.

---

## What V2 does NOT change from V1

For clarity, V2 inherits unchanged:

- Cross-vendor LLM-judge structure (Anthropic / OpenAI / xAI), with the option to revise the panel per Path B.
- Position randomization (each case run with X-baseline-Y-SPARRING and X-SPARRING-Y-baseline).
- Single rating per pair for partners (no position-flip pass on partner side).
- Anonymized X/Y assignment held server-side.
- Audit-trail-preserving rating tool (revisions stored, originals preserved).
- Pre-registration discipline (commit hash before any compute spent).
- Decision-gate framing with PASS / BORDERLINE / FAIL thresholds (specific thresholds may be revised based on V1 learning).

---

## Locked V2 design decisions (2026-05-03)

The five open design questions were adjudicated on 2026-05-03:

### Q1 — Cases: two NEW non-SFxLS decision packs

**Decision**: V2 runs against two newly-authored decision packs whose subject matter is **outside the SPARRING / SFxLS / Lifspel domain**. This serves a methodologically stronger purpose than re-running V1 cases: it tests SPARRING's value in domain-independent settings, removes the case-content-author conflict (raters are not domain experts in the case material), and produces a cleaner cross-rater pool because all four raters approach the cases from comparable non-expert positions. Decision-pack content lives at [`v2-decision-packs/case-a-organizational-vocabulary.md`](./v2-decision-packs/case-a-organizational-vocabulary.md) and [`v2-decision-packs/case-b-introductory-programming-curriculum.md`](./v2-decision-packs/case-b-introductory-programming-curriculum.md).

### Q2 — Rater pool: four raters, three RF partners + one external

**Decision**: V2 uses a four-rater pool:
- **Bart Niedner (Breegarra)** — RF partner, SPARRING framework author. user_id=1 in the existing system.
- **Matthew Niedner (Icarus)** — RF partner, conceptual architecture co-author. user_id=7.
- **Kelly Cope (Worthless Wizard)** — RF partner, not a SPARRING-framework author. user_id=4. New to V2 rating role; brief onboarding required.
- **Michelle Niedner** — external rater (`michelle.niedner@gmail.com`), not RF, not SFxLS. Provisioning required: needs a user account with rating-tool access. *See "Logistical follow-ups" below for how this is set up before V2 compute begins.*

n=3 RF + n=1 external is the published-methodology minimum for inter-rater-reliability calculations under Krippendorff 2004 §11 and the alt-test reference-distribution requirements (Calderon et al. 2025). The author-as-rater limitation V1 explicitly named (preprint §6) is materially reduced by Kelly + Michelle being framework non-authors.

### Q3 — Paired-comparison: required, not optional

**Decision**: V2 requires raters to complete BOTH instruments per case:
1. Absolute scoring on the 1-7 anchored scale per criterion (six criteria: C1, C2, C3, C4, C5a, C5b — the C5 split was already locked in §5.6 Path F).
2. Paired comparison per criterion ("X better / Y better / tied") on the same six criteria.

Rationale: the V1 partner ceiling effect (preprint §4.7) was load-bearing on the headline ρ. Paired comparison is robust to ceiling by construction. Making it optional repeats the V1 design hole; the ~25% rater-minute cost is the right trade for ceiling-resistant signal across the V2 dataset.

### Q4 — Two-tier per-criterion thresholds

**Decision**: V2 abandons the V1 single-threshold gate (ρ ≥ 0.7 PASS / < 0.4 FAIL applied uniformly across criteria) in favor of two-tier thresholds that honor the §5.2 structural finding:

- **High tier** (direct-inspection or factuality criteria): C1 (verifiable artifact citation), C2 (substantive vs theatrical concerns), C5a (factuality), C5b (engagement-with-source). Threshold: ρ ≥ 0.7 PASS, 0.4-0.7 BORDERLINE, < 0.4 FAIL. Same as V1 single threshold.
- **Lower tier** (counterfactual-inference criteria): C3 (missed real concerns), C4 (calibrated confidence). Threshold: ρ ≥ 0.5 PASS, 0.2-0.5 BORDERLINE, < 0.2 FAIL. Honors the §5.3 partner-anchor inversion finding: counterfactual-inference rubric items have structurally noisy partner-side judgment, and demanding the same correlation against partner ratings as for direct-inspection criteria conflates noise with judge mis-calibration.

Aggregate V2 gate classification:
- **PASS**: at least 5 of 6 criteria pass their respective tier AND no criterion fails.
- **BORDERLINE**: at least 4 of 6 criteria pass AND no more than 1 criterion fails.
- **FAIL**: any other outcome.

### Q5 — Cross-substrate: deferred to Phase 2 with explicit documentation

**Decision**: V2 keeps both conditions on Claude Opus 4.7 (matching V1's substrate choice, preserving direct V1-vs-V2 substrate comparability where the cases are non-comparable but the substrate is). Cross-substrate testing — running Condition A on GPT-4o or Gemini, Condition B on Claude — is **explicitly deferred to a Phase 2 ceremony** as the next experimental escalation after V2 lands.

The deferral rationale, preserved here for the Phase 2 scoper:
- V2 already adds five substantial methodology shifts (rubric expansion to 6 criteria, scale 1-5 → 1-7, paired-comparison required, rater-condition required, expanded rater pool). Adding cross-substrate on top would mix too many simultaneous changes — if V2 results differ from V1, attribution would be impossible.
- Cross-substrate tests a different question than V2: V2 tests "does SPARRING produce different/better outputs than single-agent on Claude across domains," and Phase 2 cross-substrate would test "does the answer depend on which model substrate the conditions run on."
- Phase 2 cross-substrate requires its own pre-registration with prompt-portability decisions (the Generator/Challenger sub-agent prompts are tuned for Claude; running them on GPT-4o or Gemini will produce different output shapes that the rubric needs to handle without per-substrate carve-outs).

This Phase 2 deferral is also recorded in the V2 pre-registration document so future readers see the deferral chain explicitly.

## Logistical follow-ups (before V2 compute begins)

These tasks are not design decisions; they are setup steps that must complete before V2 conditions are run. They block the pre-registration commit ONLY in the sense that the pre-reg should reference the locked rater pool by user-id; the actual user-account provisioning happens after pre-reg lock.

1. **Add Michelle Niedner as a user with rating-tool access.** The current rating tool (`research/blind-rating.php`) is gated by `admin-auth.php` and requires a logged-in user. Michelle needs a user record (`users` table) with `is_admin = 1` for access OR — preferably — a narrower "rater" role can be introduced if her access should be scoped to the rating tool only. Bart's call on which approach. Pre-reg captures her email; user_id is filled in after provisioning.
2. **Brief Kelly on the V2 rating methodology.** Kelly has not used the rating tool. A 15-minute walkthrough covers: how the blind-rating tool works, how the rater-condition declaration works, how the paired-comparison pass works, what each rubric criterion measures. The "How to approach this rating" instruction block in the rating tool already covers most of this; the briefing is to ensure smooth first use.
3. **Brief Michelle similarly.** Same content as Kelly. Plus an introduction to what the SPARRING framework is at a high level so she has context for what she's rating outputs of (without that context being part of her rubric scoring — see V1 §3.7 on judge instructions for the analogous principle).
4. **Run the V2 schema migration** on nonprod and prod. Migration file: `database/migrations/198_v2_eval_schema.sql` (drafted as part of the pre-reg lock). Adds the C5a/C5b columns, the rater-condition fields, and the paired-comparison parallel table.
5. **Implement the rating-tool form/API updates** to render the V2 schema. This is form-rendering code (1-7 scale, six criteria, rater-condition declaration field, paired-comparison pass UI) and API code (accept new fields on submit/edit, return them on get). Lives in `research/blind-rating.php` and `src/api/research/eval.php`. Schema migration is committed with the pre-reg; UI/API implementation follows in a subsequent commit but is non-load-bearing for the pre-reg validity (the locked design specifies what the tool must support; the implementation produces a tool that supports it).
6. **Run V2 conditions against the two decision packs** ONCE the pre-reg is committed and tagged. This is the "compute begins" gate — V2 compute runs against the immutable pre-reg.

---

## Summary: what's different between V1 and V2

| Dimension | V1 | V2 |
|---|---|---|
| Rubric criteria | 4 (C1-C4) | 6 (C1-C4 + C5a + C5b) |
| Scale | 1-5 anchored | 1-7 anchored, behavioral anchors at every level |
| Forced-distribution | None | Soft instruction + client-side warn on saturated identical scores |
| Rater-condition declaration | Optional Notes field | Required ENUM + free-text |
| Pack hygiene | None upstream | Independent fact-check pass before conditions run |
| Verified-pack view to raters | No | Yes |
| Paired-comparison instrument | No | Optional second pass (opt-in) |
| Pre-registration | Yes | Yes (V2 needs its own pre-reg) |

When this document is committed and the V2 pre-registration is signed, V2 exists as a methodology. Until then, V2 is text on a page — useful for partner review, not yet runnable.
