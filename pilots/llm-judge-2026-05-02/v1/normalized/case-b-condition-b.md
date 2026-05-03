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
