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
