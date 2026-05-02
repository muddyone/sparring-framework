# /spar report: Lifspel motion-accuracy coupling — design shape

**Iterations**: 2 / 2
**Outcome**: **Converged** at Round 2 (both agents signal `agree: true`).

## Personas

- **Generator**: Combat-engine architect with deep knowledge of the lifspel CRv2 engine.
  - Evidence base: `storyforge/engine/CRv2/` (`BeatResolver.php`, `NvMOrchestrator.php`, `DecisionModel.php`, `FighterState.php`), `data/cycle1.php`, P14 architecture, CRv2 Standards (esp. 8 and 9), 76a241c rollback lesson, `trained_archer_mount` flag, `gait` enum.
- **Challenger**: Historical military biomechanics and mounted-archery practice researcher.
  - Evidence base: Hyland 1994, Conlan 2003, May 2007, Selby 2000, Smail 1995, Hardy 2010, Karasulas 2004 (Osprey Elite 120), Drews 2004, modern equestrian sport biomechanics on intra-gait phase, FITA Mounted Archery rules.

Evidence-base disjointness: very high. Code architecture and historical biomechanics are completely separate corpora.

## Iteration log

### Round 1

- **Generator proposal**: Position on each of Vasik's 3 sub-questions plus identification of a 4th axis.
  - Sub-q 1 (curve shape): **STEPPED**. Architectural reasoning: gait enum `{walk, trot, canter, gallop}` already discrete; preserves enum semantics; doesn't reintroduce continuous parameter shared across decision and physics layers.
  - Sub-q 2 (flag interaction): **THRESHOLD-SHIFTER**. Architectural reasoning: keeps `trained_archer_mount` binary and decision-gating in spirit.
  - Sub-q 3 (scope boundary): **TARGETING LAYER (P14-successor)**. Standard 9 prevents amending closed P14; routing through action-resolution would re-create the 76a241c conflation.
  - Fourth axis identified: **TEMPORAL GRANULARITY / FIRING-WINDOW RESOLUTION**, leaning toward abstracting intra-gait phase into the trained-mount flag.
- **Challenger PNP**: Substantive engagement, concur on Sub-q 1 with stronger historical warrant; substantive pushback on Sub-q 2 and the fourth axis; surfaced a fifth axis Vasik missed.
  - Concur on STEPPED but with stronger historical warrant: Hyland 1994 ch. 5 pp. 99-117 (gaits as biomechanically discrete stability regimes) and Selby 2000 pp. 165-180 (Wu Bei Yao Lue commentary describes shooting drills in qualitative gait categories, never as a continuum) make linear-in-fraction a modeling artifact with no source corpus behind it.
  - **Push back on THRESHOLD-SHIFTER**: May 2007 ch. 3 pp. 41-60 on Mongol training and Karasulas 2004 pp. 28-34 document that even *trained* mounted archers suffer accuracy degradation at gallop — what training buys is "the penalty curve at every gait is shallower, AND the firing window within each gait is exploited," not "gallop becomes stable." Conlan 2003 yabusame chapter: samurai practice canter timing for years precisely because canter is not stable; they fire in the suspension phase. Historical reading is closer to **slope-reducer with a phase-exploitation modifier**.
  - **Push back on fourth-axis abstraction**: Folding intra-gait phase into the binary flag erases the mechanism Conlan and Karasulas document — the trained-archer skill IS the phase exploitation.
  - **FIFTH AXIS** (neither Generator nor Vasik named): **BOW-DRAW-CYCLE vs. GAIT-CYCLE COUPLING**. Hardy 2010 pp. 41-58 on draw-and-loose timing for heavy bows (~1.5-2s for a war bow); the relevant question is "how does the rider's draw cycle phase against the gait cycle (~0.5s gallop stride)." Selby 2000 and yabusame protocols implicitly address this. If unmodeled, the engine systematically over-credits shooting cadence at gallop.
- **Generator signal**: `{agree: false, reasoning: "Round 1 opening — Challenger has not yet pressure-tested any of these positions, particularly the historical-evidence claims I deferred and the fourth-axis call."}`
- **Challenger signal**: `{agree: false, reasoning: "Concur on Sub-question 1 (with stronger historical warrant) and defer on Sub-question 3, but Sub-question 2 (THRESHOLD-SHIFTER) and the fourth-axis abstraction both contradict what May, Karasulas, and Conlan document about trained mounted archery — and a fifth axis (draw-cycle/gait-cycle coupling) was missed entirely."}`

### Round 2

- **Generator proposal (revised)**: Substantive revision incorporating all of Challenger's pressure.
  - Sub-q 1: STEPPED **holds**, now recorded as historically required (not architecturally convenient).
  - Sub-q 2: **REVISED to hybrid SLOPE-REDUCER + small THRESHOLD-SHIFT**. Conceded original threshold-shifter encoded a fiction the corpus rejects. Architecturally: flag becomes a two-field modifier struct `{slope_multiplier, threshold_offset}` on `BeatResolver`'s motion-penalty calculation. `DecisionModel.php` flag use unchanged (still binary gate for decision-to-fire); hit-probability layer reads same flag through new modifier struct. Respects 76a241c by sharing flag *identity* but not flag *parameter* across layers.
  - Sub-q 3: TARGETING LAYER **holds**.
  - Fourth axis: **WITHDREW** the "abstract intra-gait phase into the flag" framing. Honest commitment: engine does not represent intra-gait phase; STEPPED-with-hybrid-flag does not capture it; trained-archer flag's slope-reducer is a coarse proxy. Representing intra-gait phase honestly requires a `gait_phase` field on cycle state and phase-aware penalty interpolation (touches `NvMOrchestrator` cycle scheduling). Defer-with-honesty.
  - Fifth axis: **Accepted as real omission**. Architecturally downstream of the fourth-axis decision — without intra-gait phase, can't represent draw-cycle/gait-cycle phasing. **Defer as explicit forward note**, paired with fourth-axis limit. Eliding now acceptable *only* because flagging known systematic over-credit of shooting cadence at gallop.
- **Challenger PNP**: Substantive engagement; revisions hold up to the historical record with one narrow scoping caution.
  - Sub-q 2 hybrid is right calibration. Mongol corpus + Hyland biomechanics imply training does not move the gait at which firing becomes possible (Mongol/Mamluk doctrine still preferred rein-back / momentary check before release at canter and gallop, not "fire at trot like an untrained rider would at walk"). Conlan yabusame supports a *threshold around canter* for trained mounts and a *threshold around walk-trot boundary* for untrained — small shift, not entire-gait offset. Dominant effect is slope reduction.
  - **Narrow caution (scoping detail, not blocker)**: `threshold_offset` must not be permitted to *eliminate* a gait class for trained riders. FITA Mounted Archery scoring and Karasulas 2004 confirm even elite trained pairs incur measurable accuracy degradation at gallop versus canter — there is no historical regime in which gallop is penalty-free. Recommend a **clamp** ("offset cannot remove gallop tier from penalty domain") to close this.
  - Fourth-axis deferral framing is honest engineering, not deferral-theater, *because* the Generator named what is being elided (phase exploitation is the actual mechanism; slope-reducer is a coarse proxy) and named what would be required to represent it properly. Modern equestrian sport biomechanics literature on intra-gait phase stability supports that this is genuinely a separate representational concern.
  - Fifth-axis deferral meets same standard. Hardy's 10-12 aimed-shots/min figure is a foot-archer rate; mounted draw rates in Selby and Karasulas are gait-phase-locked, not free-running.
  - **Sixth-axis check**: tested whether an axis was missed. Candidate would be *target-relative motion* (closing/opening/crossing geometry against mount velocity vector — Smail's Arsuf and Karasulas's steppe-tactics). Concluded this is properly a *targeting-layer* concern that Sub-q 3's convergence on TARGETING LAYER already routes correctly. Subsumed, not separate.
- **Generator signal**: `{agree: true, reasoning: "Revised proposal accepts Challenger's historical pushback on Sub-q 2 (hybrid slope-reducer + threshold-shift), withdraws the dishonest fourth-axis abstraction, and folds the fifth axis as an explicit deferred forward note paired with intra-gait phase — all within architectural reach without re-introducing the 76a241c conflation."}`
- **Challenger signal**: `{agree: true, reasoning: "Revised hybrid slope-reducer + small threshold-shift correctly matches the historical record per the Mongol/yabusame convergence, the fourth and fifth axis deferrals are honest engineering with named representational gaps rather than deferral-theater, and the only remaining concern (clamp on threshold_offset to prevent removing gallop from the penalty domain) is a scoping detail not a design-shape disagreement."}`

## Final evaluation

**Converged design shape:**

1. **Curve shape: STEPPED** — penalty constants for each gait `{walk, trot, canter, gallop}` rather than linear-in-gait-fraction. Historically required (gaits are discrete biomechanical regimes per Hyland/Selby), not just architecturally convenient.

2. **Flag interaction: hybrid SLOPE-REDUCER + small THRESHOLD-SHIFT.** `trained_archer_mount` becomes a two-field modifier struct `{slope_multiplier, threshold_offset}` on `BeatResolver`'s motion-penalty calculation. Slope-reduction is the dominant effect; threshold-offset is small (canter-level for trained, walk-trot boundary for untrained). **MUST clamp** `threshold_offset` to prevent removing the gallop tier from the penalty domain — no historical regime makes gallop penalty-free even for elite pairs (FITA scoring, Karasulas confirm).

3. **Scope boundary: TARGETING LAYER (P14-successor).** Physics must remain separable from AI decision-to-fire (76a241c lesson). Standard 9 forecloses retrofitting closed-P14.

4. **Fourth axis (intra-gait phase): explicit deferred limit.** Engine does not represent intra-gait phase; the slope-reducer component is a coarse proxy for what is actually phase exploitation. Representing it honestly requires a `gait_phase` field on cycle state and phase-aware penalty interpolation — defer to a later milestone.

5. **Fifth axis (draw-cycle / gait-cycle coupling): explicit deferred forward note.** Architecturally downstream of intra-gait phase. Pair with fourth-axis limit. Eliding now acceptable *only* because flagging known systematic over-credit of shooting cadence at gallop.

**The flag's semantic role expands** from "binary AI decision-gate" to "binary AI decision-gate + parametric hit-probability modifier (read through separate modifier struct)." Surface-area cost: ~one struct, one new consumer site in `BeatResolver`. Existing `DecisionModel.php` use unchanged.

**Standard 8 unchanged**: this design shape is what to scope *if/when* a sim-driven failure surfaces. Vasik's High-confidence "not ready to scope" still holds; nothing in the sim is currently failing from motion-accuracy coupling's absence. This /spar produces an architecture sketch ready to deploy, not a promotion recommendation.

## Artifacts surfaced

External (cross-condition verifiable):

- Hyland, A. (1994). *The Medieval Warhorse: From Byzantium to the Crusades*. Sutton. Chs. 5-6, esp. pp. 99-117 — gait biomechanics as discrete stability regimes; rider stability mechanics
- Conlan, T. (2003). *State of War: The Violent Order of Fourteenth-Century Japan*. U. Michigan CJS — yabusame protocol sections; canter phase-exploitation as multi-year trained skill
- May, T. (2007). *The Mongol Art of War*. Pen & Sword. Ch. 3, pp. 41-60 — Mongol training; trained-archer accuracy still degraded at gallop
- Karasulas, A. (2004). *Mounted Archers of the Steppe 600 BC-AD 1300*. Osprey Elite 120, pp. 28-34 — phase exploitation as the trained skill; gallop never penalty-free even for elite pairs; closing-geometry treatment
- Selby, S. (2000). *Chinese Archery*. HKU Press, pp. 165-180 — *Wu Bei Yao Lue* commentary on qualitative gait categories
- Hardy, R. (2010). *Longbow*, pp. 41-58 — draw-and-loose timing for war bows (~1.5-2s); 10-12 aimed shots/min foot-archer rate
- Smail, R.C. (1995, 2nd ed.). *Crusading Warfare 1097-1193*. Cambridge UP — Arsuf target-relative motion geometry
- FITA Mounted Archery rules (modern) — confirming gallop accuracy degradation persists for trained riders
- Modern equestrian sport literature on intra-gait phase stability — supports fourth-axis deferral as honest representational gap

Internal (Lifspel decision pack):

- Decision pack: `docs/bfn/llm-judge-pilot-2026-05-02/decision-packs/case-b-motion-accuracy.md` (read by both agents in both rounds)
- `storyforge/engine/CRv2/BeatResolver.php` — site of new motion-penalty calculation and modifier-struct consumer
- `storyforge/engine/CRv2/DecisionModel.php` — unchanged binary flag gating for decision-to-fire
- `storyforge/engine/CRv2/NvMOrchestrator.php` — would be touched if intra-gait phase promoted (deferred)
- `data/cycle1.php` — `gait` enum `{walk, trot, canter, gallop}`; aligned with STEPPED, no parallel fraction needed
- CRv2 Standards 8 (mechanical-necessity gate) and 9 (no amending closed P14)
- Commit `76a241c` — gait-fraction rollback; lesson preserved by keeping flag-parameter separation between layers
- `trained_archer_mount` flag — current binary decision-gating role preserved; new hit-probability role added via separate modifier struct read
- Round Table thread #200 post #1644 (Vasik, 2026-04-23 07:16:31)

## Self-citation circularity check

Round 2 (final): clean. Challenger's threshold-offset clamp note draws on Karasulas and FITA — independent of the Hyland/Conlan/Selby/May convergence the Generator now uses as primary support; no circularity. Fourth-axis check uses modern equestrian literature outside Vasik's #200 corpus, which is appropriate (the corpus is the engine's evidence base; Challenger's role is to test from outside). Generator's architectural claims ground in the Lifspel codebase, which IS the Generator's appropriate evidence base — not improper self-citation. Both agents grounding load-bearing claims in genuinely external corpora.

## Recommendation for parent

**Adopt the converged design shape** as the architecture commitment for any future motion-accuracy coupling milestone. **Do not promote the milestone today** — Vasik's High-confidence "not ready to scope" (Standard 8: nothing currently failing in the sim) still applies.

When promoted, the milestone should:

1. Add the modifier struct `{slope_multiplier, threshold_offset}` to the `trained_archer_mount` flag's storage shape.
2. Add the new consumer site in `BeatResolver` for motion-penalty calculation reading the modifier struct.
3. **Implement the threshold_offset clamp** preventing the offset from removing the gallop tier from the penalty domain (Challenger's narrow caution).
4. Document explicitly in the milestone scoping doc that intra-gait phase exploitation (fourth axis) and draw-cycle / gait-cycle coupling (fifth axis) are NOT modeled by this milestone, paired with the architectural cost (a `gait_phase` field on cycle state and phase-aware penalty interpolation; touches `NvMOrchestrator`) and the systematic over-credit of shooting cadence at gallop that follows from the elision.
5. Pair the eventual intra-gait-phase milestone (if it ever lands) with the draw-cycle/gait-cycle coupling milestone — they are the same architectural extension.

The convergence rests on cross-corpus evidence (architecture from the codebase; biomechanics from Hyland/Conlan/May/Selby/Karasulas/Hardy/Smail/FITA). No load-bearing claim depends solely on internal Lifspel materials. The /spar's verifiable-artifact discipline is satisfied throughout.
