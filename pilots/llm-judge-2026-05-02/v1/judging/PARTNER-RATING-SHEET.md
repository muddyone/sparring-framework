# Partner Rating Sheet — SPARRING LLM-judge Phase 1 calibration

You are rating two pairs of decision recommendations. You don't know which approach produced which answer.

**Pre-registration discipline**: I have run all 3 cross-vendor LLM judges already. I am NOT showing you their scores until you finish your ratings. You score blind.

## Rubric (same as the LLM judges saw)

Anchored 1-5 scale, applied to every criterion:
- **1** = clearly absent or actively wrong
- **2** = weakly present, multiple gaps
- **3** = adequate, some gaps
- **4** = strong, minor gaps
- **5** = exemplary, no notable gaps

Four criteria:
1. **Verifiable artifact citation** — does the recommendation cite specific evidence from the materials?
2. **Substantive vs theatrical concerns** — are concerns real risks rooted in evidence, or generic-sounding hedges?
3. **Missed real concerns** — does it surface concerns a careful reader would identify, or miss obvious ones?
4. **Calibrated confidence** — is the stated confidence appropriate to the evidence?

Score per criterion only. Do NOT decide overall preference. The X/Y labels were assigned by lock per the pre-registration; they do not encode any condition information you should try to infer.

---

## CASE A

**Question**: What naming convention should the racial taxonomy use for Family-tier nodes?
- (1) Scientific-Latin-style (e.g., `Primatidae-SFxLS`)
- (2) Explicitly-engineered names (e.g., `Sapient-Bipedal-Family`)
- (3) Mixed register (Latin when real-biology-grounded, English when SFxLS-invented)

**Evidence base summary**: Lifspel is adding a Kingdom→Family→Genus→Species lineage axis to its racial taxonomy. Family-tier names will appear in the sf_races schema, in a separate sf_race_external_references LLM-grounding adjunct table, in the Race Builder authoring tool, in per-race lore reviews (Lena Vasik for Natural-Evolved races, Idris Harmon for Natural-Mythic), and in engine inheritance (Family-tier Body Plan templates default hit-zone distributions). Vasik (biological systematics commentary) emphasizes that Linnaean mapping is sound for real-world cases (Canidae, Equidae) but the LP-signature axis is a game abstraction not biological grounding; per-species override density is high for fantasy races. Idris (mythological commentary) argues `Primatidae-SFxLS` does cultural work it cannot honor — Norse alfar are not a sub-species of Primate to any tradition that uses them — and votes for clarity / engineering-intent naming. A separate open question (OQ #5) proposes a `grounding_tier` field per Family node (NS-Tier-1 / NS-Tier-4 / SC-grounded / engineering-construct). The decision is hard to reverse: changing Family names later means migrating every `sf_races` row, every doc reference, every authoring-tool option, and every LLM-grounding context.

### CASE A — ANSWER X

```
RECOMMENDATION: Adopt Option 3, the mixed register, with the `grounding_tier` field from OQ #5 as the deciding rule: Families with `NS-Tier-1` or `NS-Tier-4` grounding take Latin-style names (`Canidae`, `Equidae`); Families that are `SC-grounded` or `engineering-construct` take descriptive English names (`Sapient-Bipedal-Family`, `Winged-Drake-Family`, `Corporeal-Undead-Family`). This is the only option that simultaneously honors Lena's point that Latin names carry genuine Tier-1 citation chains where the biology is real, and Idris's point that borrowing Linnaean authority for engineering conveniences like the Human/Elf/Dwarf grouping is a category error that flattens mythological traditions. It also handles the sci-fi and Mechanica cases natively — Borg and Replicants get descriptive names without contortion — and pairs the surface name with the grounding-tier metadata an author or LLM needs to calibrate trust. The convention encodes the honesty Lena explicitly asked for and the clarity Idris voted for, at the cost of a single rule authors must learn.

KEY CONCERNS:

- The recommendation is contingent on OQ #5 being adopted. If `grounding_tier` is rejected or deferred, the mixed register loses its principled selector and degenerates into case-by-case judgment, which is exactly the inconsistency that makes Option 3 risky in isolation.

- Boundary cases will be contested. Spiritualia is the worst offender — Idris flags it as the Kingdom with the richest cross-tradition variance, and deciding whether a Family of draugar-like entities is `SC-grounded` (English name) or analogically `NS-Tier-4` (Latin name) is a judgment call that will recur for every mythic Family.

- Race Builder UX takes a hit either way: Latin names are unfamiliar to non-biologist partners, and hyphenated descriptive names are long and harder to scan in a dropdown. Plan for tooltips, search-by-substring, and possibly a short-code alias column.

- LLM-grounding asymmetry is real but acceptable: Latin-named Families ground into actual literature; English-named Families do not. The mitigation is the `sf_race_external_references` adjunct table, but its quality for `SC-grounded` Families will be uneven and should not be oversold.

- The convention is hard to reverse (per the constraints section). Once `Primatidae-SFxLS` ships in rows, docs, tooling, and prompt context, renaming it to `Sapient-Bipedal-Family` later is expensive — so the up-front classification of each Family's grounding tier needs more care than a brainstorm pass typically gets.

- Reviewer-attachment is a feature here (Lena's Tier-1 chains naturally attach to Latin Families she reviews; Idris's archetypal reviews attach to English Families he reviews), but it also means a misclassified Family routes to the wrong reviewer. Build a lightweight reclassification path into P13M3.

- The `-SFxLS` suffix on Latin names (as in `Primatidae-SFxLS`) is a small but visible tell that the taxonomy is engineered. Decide deliberately whether to keep it, drop it, or apply it only to invented Latin coinages — and document the rule, because partners will ask.

CONFIDENCE: Medium. The evidence strongly supports a mixed register over either pure option, but the recommendation's coherence depends on OQ #5 landing and on disciplined per-Family grounding-tier classification, neither of which is yet settled.
```

### CASE A — ANSWER Y

```
RECOMMENDATION: Adopt Option 3 (mixed register) governed by the `grounding_tier` field from OQ #5 as the mechanical selector — Families with `NS-Tier-1` or `NS-Tier-4` grounding take Latin-style names where the convention is honest and the LLM-grounding adjunct can point at real corpus (e.g., `Canidae` mapping to NCBI TaxID 9608 and `Mammal Species of the World` 3rd ed.); Families that are `SC-grounded` or `engineering-construct` take descriptive English-engineered names (e.g., `Sapient-Bipedal-Family` for the polyphyletic-by-construction Human/Elf/Dwarf/Orc/Halfling/Ogre cluster). The case for the mixed register is structural, not stylistic: the Family-tier name does descent work in the schema (Body Plan inheritance, LLM grounding context) regardless of what it does on partner-facing surfaces, and Option 3 separates the cases where descent work is honest from the cases where it would be a category error. ICZN Article 11.7 family-rank morphology presupposes monophyletic descent that engineered/mythopoeic groupings cannot supply, but mythopoeic register considerations (per Tolkien Letters 144 and 165 on linguistic-coherence-without-taxonomic-correspondence, and Shippey 2000 on philological authority) make the choice context-sensitive — which Option 3 honors and Options 1 and 2 do not.

KEY CONCERNS:

- The recommendation is contingent on OQ #5 (`grounding_tier`) being adopted. Without that field as the mechanical selector, Option 3 degenerates into per-Family judgment, which loses the principled discipline.

- Spiritualia's within-tier variance is not addressed by `grounding_tier`. Norse draugar, Greek shades, Haitian lwa, Japanese yurei, and Chinese jiangshi all sit in `SC-grounded` and route uniformly to English-engineered names, reproducing the very flattening the Mixed register option was supposed to prevent. Per Frye 1957 (Second Essay on apocalyptic/demonic imagery taxonomy) and Campbell 1949 (threshold-guardian/shadow typologies), mythopoeic literature treats these as non-substitutable — partner consultation with the mythological domain reviewer is needed before Spiritualia Families ship at scale.

- The LLM-grounding asymmetry is substantively larger than a symmetric stylistic choice suggests. Latin-styled names anchor real corpus retrieval (`Canidae` → NCBI TaxID 9608 → real wolf biology); English-engineered names do not. Document this honestly in the rule's specification: Latin-styled names commit the schema to corpus-grounded behavior; English-styled to corpus-free behavior.

- Sci-fi entity handling (Replicants, Borg, Culture Minds, Terminators) defaults to `engineering-construct` → English-engineered, which works, but this default should be stated explicitly in the rule rather than left implicit.

- The category-error argument against uniform Latin naming rests partly on the ICZN Article 11.7 reference. That specific code citation should be partner-verified before the rule's documentation treats it as load-bearing — the reference is plausible but was not independently corroborated within the deliberation.

- D&D 5e's published experience (Mordenkainen Presents: Monsters of the Multiverse, May 2022 release; design-press reception May-June 2022) shows that pseudo-systematic naming is read by genre audiences as world-depth signaling; Option 3's hybrid handling preserves depth where honest and avoids it where a category error.

- The convention is hard to reverse. Up-front per-Family classification of grounding tier needs more care than a brainstorm pass typically gets, because changing Family names later means migrating every `sf_races` row, every documentation reference, every authoring-tool option, and every LLM-grounding context.

CONFIDENCE: Medium. The recommendation's structural soundness on the option choice is high (cross-corpus convergence between systematics and mythopoeic register considerations); the operational rule's completeness is incomplete on Spiritualia within-tier variance and on explicit sci-fi handling, both of which need partner amendment before deployment.
```

### CASE A — Your scores

Fill in each cell with an integer 1-5:

```
Answer X:
  Criterion 1 (verifiable artifact citation):    [_]
  Criterion 2 (substantive vs theatrical):       [_]
  Criterion 3 (missed real concerns):            [_]
  Criterion 4 (calibrated confidence):           [_]

Answer Y:
  Criterion 1: [_]
  Criterion 2: [_]
  Criterion 3: [_]
  Criterion 4: [_]
```

---

## CASE B

**Question**: If/when motion-accuracy coupling is promoted from DEFERRED to a scoped milestone, what is the right design shape across three sub-questions:
- (1) Coupling curve shape — linear in gait fraction vs stepped (walk/trot/canter/gallop)
- (2) Interaction with the `trained_archer_mount` flag — slope-reducer vs threshold-shifter
- (3) Scope boundary — targeting-layer (P14-successor) vs action-resolution-layer (encounter mechanics)

The recommendation should commit to a position on each sub-question, and address Vasik's flag that a fourth axis may be missing.

**Evidence base summary**: P14 (Targeting and Collateral Resolution) closed 2026-04-20 with a three-layer architecture (target selection / attack-geometry obstruction / universal collateral miss-spill). The `trained_archer_mount` boolean flag exists on archetype data and currently affects only AI decision-gating, not hit-probability calculation. The 2026-04-22 commit `76a241c` rolled back an earlier "gait-fraction hack" that conflated the AI's decision-to-fire with the physical accuracy penalty for firing-while-moving. Vasik's 2026-04-23 thread #200 post #1644 filing recommends DEFERRED forward-architecture per Standard 8 (no scoping without sim-driven failure). Cited historical sources: Hyland 1994 (warhorse biomechanics, gait stability), Conlan 2003 (yabusame canter-phase exploitation as multi-year skill), May 2007 (Mongol stable-platform doctrine), Selby 2000 (Chinese archery manuals), Hardy 2010 (longbow draw timing), Smail 1995 (Arsuf dynamics). Source convergence: trained mounted archery is a stable-moment technique, not sustained-motion; trained/untrained distinction is real and substantial; source base supports a stepped model with trained-mount qualifier; supplies no specific accuracy penalty numbers. CRv2 Standard 9 prevents amending closed-P14; any milestone must land as P14-successor or in another OPEN phase. Vasik notes Moderate confidence on the three sub-questions being complete — possible fourth axis missing.

### CASE B — ANSWER X

```
RECOMMENDATION

If/when promoted, scope motion-accuracy coupling as a **stepped curve over the existing `gait` enum, with `trained_archer_mount` becoming a hybrid slope-reducer + small threshold-shift modifier** (a two-field struct `{slope_multiplier, threshold_offset}`), implemented in the **targeting layer as P14-successor work**. Stepped is historically required, not just architecturally convenient: Hyland 1994 (chs. 5-6, gait biomechanics as discrete stability regimes) and Selby 2000 (Wu Bei Yao Lue commentary, qualitative gait categories) make linear-in-fraction a modeling artifact with no source corpus. The hybrid flag interaction reflects what the historical record actually documents: May 2007 on Mongol training and Karasulas 2004 on steppe-archery practice show that training reduces the penalty slope at every gait *and* exploits firing-window phase, while never making gallop penalty-free. Threshold-shift is real but small (canter-level for trained, walk-trot boundary for untrained); slope-reduction is the dominant effect. The targeting-layer placement preserves the 76a241c lesson — keep physical accuracy penalty separable from AI decision-to-fire — by sharing only flag *identity* across layers, not flag *parameter*.

KEY CONCERNS

- **`threshold_offset` must be clamped** so it cannot remove the gallop tier from the penalty domain. FITA Mounted Archery scoring (modern competitive evidence) and Karasulas 2004 confirm even elite trained pairs incur measurable accuracy degradation at gallop. An unclamped offset would encode the historical fiction that elite trained pairs shoot at gallop with stationary-archer accuracy.

- **Intra-gait phase exploitation is the actual mechanism for trained mounted archery, and this design does not represent it.** Conlan 2003 documents yabusame as canter-phase exploitation that takes years to learn; the slope-reducer component is a coarse proxy for that mechanism, not a model of it. Representing it honestly would require a `gait_phase` field on cycle state and phase-aware penalty interpolation, with downstream impact on `NvMOrchestrator` cycle scheduling — defer to a later milestone, but document the abstraction limit explicitly in the scoping doc rather than letting the proxy stand unmarked.

- **Bow-draw-cycle / gait-cycle coupling is unmodeled.** Hardy 2010 documents draw-and-loose timing for war bows (~1.5-2s); mounted draw rates per Selby and yabusame protocols are gait-phase-locked, not free-running. The engine without intra-gait phase will systematically over-credit shooting cadence at gallop. This concern is architecturally downstream of intra-gait phase — the same milestone that promotes intra-gait phase should also scope draw-cycle/gait-cycle coupling.

- **Standard 8 promotion bar remains unmet.** Nothing in the sim is currently failing from motion-accuracy coupling's absence. This is an architecture sketch ready to deploy *if* a sim-driven failure surfaces; it is not itself a promotion recommendation. Resist scoping pressure from narrative refinement (the Parthian-shot question's evocative pull) until a concrete failure forces promotion.

- **No quantified penalties in the source corpus.** Historical record supports the *shape* but supplies zero magnitudes. Any committed penalty constants will be designer-chosen; flag in the scoping doc as tunable rather than historically grounded.

- **The flag's semantic role expands** from binary AI decision-gate to (binary AI decision-gate) + (parametric hit-probability modifier read through a separate struct). Existing archetype data for Mongol horse archers and yabusame samurai implicitly carries balance assumptions that should be re-audited.

- **Standard 9 forecloses retrofitting closed-P14.** Design must land as P14-successor or new milestone in an OPEN phase; interaction with P14's Layer 2 obstruction profiles (arc=0.41 melee, line=0.05 ranged) must be explicitly additive, not an amendment.

CONFIDENCE

**Medium-high.** The design shape rests on cross-corpus convergence (codebase architecture and historical biomechanics) with no load-bearing claim depending solely on either domain alone. The dominant residual uncertainty is the magnitude calibration (no numbers from sources) and the abstraction limits explicitly named (intra-gait phase, draw-cycle coupling). The `threshold_offset` clamp is a concrete scoping detail worth carrying into the eventual milestone doc.
```

### CASE B — ANSWER Y

```
RECOMMENDATION

If/when promoted, scope motion-accuracy coupling as **stepped by gait, threshold-shifting on `trained_archer_mount`, implemented in the targeting layer** as P14-successor work. Stepped is what the source base actually supports: May, Selby, and Conlan converge on a stable-platform/stable-moment reading where the firing beat is qualitatively distinct between gaits, not a smooth acceleration penalty — and the existing `gait` enum already aligns with this. Threshold-shifting matches Conlan's yabusame evidence directly: trained archers don't suffer a uniformly scaled penalty, they relocate the stable-platform regime upward into canter, which is a categorical change in *where* shooting is viable, not a proportional discount. Targeting-layer placement keeps the physical accuracy penalty cleanly separated from the AI's decision to fire — exactly the separation the `76a241c` rollback established as architecturally correct, and it composes naturally with P14's Layer 2 geometry. The implicit fourth axis worth surfacing: **rider skill / archetype**, distinct from mount training, which the source base treats as a separate variable.

KEY CONCERNS

- **No quantified penalties anywhere in the source base.** Hyland, Conlan, May, and Selby all support the *shape* of the model but supply zero numbers; any committed magnitudes will be designer-chosen and should be flagged as tunable, not historically grounded. This is the single largest risk to Standard 8 defensibility at scoping time.

- **Standard 8 promotion bar remains unmet.** Vasik is explicit that nothing in the sim is currently failing; this design shape is a contingency, and the team should resist scoping pressure until a sim-driven failure actually surfaces. A clean design does not justify promotion.

- **The fourth axis (rider archetype/skill) is probably real and unaddressed.** Conlan's yabusame evidence is about *trained riders on trained mounts*; May's Mongol doctrine is about trained-rider populations. `trained_archer_mount` alone may conflate two variables, and a future refactor splitting them will be cheaper if anticipated now.

- **Threshold-shifter has a discontinuity risk.** If trained mounts have *zero* penalty below canter and untrained have penalty starting at trot, edge cases at the threshold may produce counterintuitive AI behavior; a small slope-reduction component at the shifted threshold may be needed as a smoothing term.

- **Targeting-layer placement requires re-opening Layer 2 assumptions carefully.** P14 is CLOSED (Standard 9); mount-velocity effects on firing arcs must be additive in a P14-successor, not a retrofit, and the interaction with arc=0.41/line=0.05 obstruction profiles needs explicit design.

- **Semantic role of `trained_archer_mount` expands.** The flag currently only gates AI action selection; making it also modify hit probability is a small refactor but means existing archetype data (Mongol, yabusame samurai) implicitly carries a balance assumption that should be audited.

- **Stepped model presumes the `gait` enum is the right granularity.** If future work needs sub-gait phase modeling (the yabusame "brief stable moment of canter" is intra-gait, not inter-gait), a stepped-by-enum design will need extension; worth a forward note.

- **Risk of narrative refinement pressure.** The Parthian-shot question is evocative and will keep generating scoping pressure; the team should be disciplined about treating this as forward-architecture until a concrete sim failure forces it.

CONFIDENCE

**Medium.** The qualitative shape recommendations (stepped, threshold-shifter, targeting-layer) are well-supported by the convergent source reading and the `76a241c` precedent, but the absence of quantified penalties and Vasik's own moderate confidence on sub-question completeness mean the design will need empirical tuning and possibly a fourth-axis revision once promoted.
```

### CASE B — Your scores

```
Answer X:
  Criterion 1 (verifiable artifact citation):    [_]
  Criterion 2 (substantive vs theatrical):       [_]
  Criterion 3 (missed real concerns):            [_]
  Criterion 4 (calibrated confidence):           [_]

Answer Y:
  Criterion 1: [_]
  Criterion 2: [_]
  Criterion 3: [_]
  Criterion 4: [_]
```

---

## How to return scores

Reply in chat with the 16 numbers in this exact format:

```
Case A: X = a, b, c, d ; Y = a, b, c, d
Case B: X = a, b, c, d ; Y = a, b, c, d
```

(replace each lowercase letter with an integer 1-5)

Once you reply, I'll pull the LLM judge scores out of cold storage, compute Spearman ρ between your scores and the cross-vendor judge consensus, and run the per-criterion / position-bias / cross-judge-agreement / mode-collapse checks per the pre-registered analysis plan.
