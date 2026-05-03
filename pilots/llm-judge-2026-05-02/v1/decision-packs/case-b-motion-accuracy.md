# Case B — Decision Pack: Motion-accuracy coupling design shape

**Source**: `docs/chatlogs/202604230218.inflight.thread-200-vasik-followups.md` SCOPE-CANDIDATE block; Round Table thread #200 post #1644 (Dr. Lena Vasik, 2026-04-23)
**Status**: Currently DEFERRED forward-architecture per Vasik Standard 8 gate (sim-driven failure required for promotion). Decision question for the spar is the design-shape *if/when* promoted, not the prior promotion question.

---

## The decision

**Question.** If/when motion-accuracy coupling is promoted from DEFERRED to a scoped milestone, what is the right design shape across Vasik's three open sub-questions? The recommendation should commit to a position on each sub-question with reasoning, not merely list the options.

**The three sub-questions** (Vasik #1644, 2026-04-23):

1. **Coupling curve shape** -- linear in gait fraction (smooth penalty curve from 0 to max as gait increases) vs. stepped (discrete penalty levels at walk / trot / canter / gallop with sharp transitions between). The source base supports stepped but does not quantify penalty magnitudes.

2. **Interaction with `trained_archer_mount` flag** -- the existing flag (currently used for Mongol-style horse archers and yabusame samurai) reduces the velocity-accuracy penalty. Two ways to model that reduction:
   - Slope-reducer: the flag scales the penalty curve down by a constant factor (e.g., 0.5x penalty at every gait level)
   - Threshold-shifter: the flag shifts the gait at which penalty starts to apply (e.g., penalties don't start until canter for trained mounts vs. trot for untrained)

3. **Scope boundary** -- where does the implementation live?
   - Targeting layer (P14-successor work): the rider's accuracy roll is modified by mount velocity at the targeting/aim resolution step, before any attack-mode decision
   - Action-resolution layer (encounter mechanics): mount velocity feeds into decision-model scoring, affecting which actions the AI selects, with downstream effect on hit probability

---

## Evidence base

### What's currently in the engine

P14 (Targeting and Collateral Resolution) closed 2026-04-20 with a three-layer architecture:
- Layer 1: target selection (mounts targetable, de-prioritized; rider/mount disambiguation)
- Layer 2: attack-geometry obstruction profiles (arc=0.41 melee, line=0.05 ranged for below-relationship)
- Layer 3: universal collateral miss-spill

The `trained_archer_mount` boolean flag exists on archetype data and is currently used by the AI to gate "fire while the mount is moving" decisions -- without it, the AI strongly prefers stable-platform shots. The flag has no current effect on hit-probability calculation; it only affects decision-model action selection.

P14 closed two open hit-probability bugs as resolved-by-construction. The motion-accuracy coupling concern is a *new* concern raised post-P14 by Vasik observing that the engine currently has no mechanism by which a galloping horse archer's shot is harder than a stationary horse archer's shot -- the current implementation treats them identically once the AI decides to fire.

The 2026-04-22 commit `76a241c` rolled back an earlier "gait-fraction hack" attempt that approximated motion-accuracy coupling by using gait fraction directly in the strike calculation. That approach was rejected because it conflated the AI's *decision to fire while moving* with the *physical accuracy penalty for firing while moving* -- two separable concerns that should not share a parameter. The rollback is what surfaced the motion-accuracy coupling as a clean separable concern worth architecting properly.

### Vasik's #1644 filing recommendation (verbatim, 2026-04-23 07:16:31)

> The concept is defensible: a mount's velocity should degrade the rider's ranged accuracy, and its absence is why the Parthian-shot question surfaced as a gait-fraction hack in the first place. But the shape is not yet defensible as a milestone.
>
> Open questions before scoping:
> - Coupling curve: linear in gait fraction? Stepped (walk/trot/canter/gallop)? Source base (Hyland 1994, Conlan 2003 on yabusame timing) supports a stepped model but does not quantify penalty magnitudes.
> - Interaction with trained_archer_mount: does the flag reduce the penalty slope, or shift the stable-platform threshold?
> - Scope boundary: is this a targeting-layer concern (routes through P14-successor work) or an action-resolution concern (routes through encounter mechanics)?
>
> Recommendation: file as DEFERRED forward-architecture note per §9, not as a scoped milestone. The concern is real and worth preserving, but the three open questions above need discovery -- probably a short research spike, not a scoping pass -- before a milestone shape can be justified. Premature scoping here would violate Standard 8 (narrative refinement masquerading as mechanical necessity) because nothing currently in the sim is failing from its absence. **Confidence: High** on "not ready to scope"; **Moderate** on the three open questions being the right ones (there may be a fourth I have not surfaced).

### Cited historical sources (Vasik thread #200 corpus)

- **Hyland, *The Medieval Warhorse* (1994)**, chapters 5-6 — warhorse training biomechanics; gives mounted-combatant silhouette analysis (~55-65% horse below shoulder line for destriers); careful about weapon-presentation-angle dependence. Tier 1 (specialist monograph). Discusses gait stability and rider stability in detail; does not quantify accuracy penalties at specific gaits.
- **Conlan, *State of War: The Violent Order of Fourteenth-Century Japan* (2003)**, esp. chapters on yabusame — samurai mounted-archery training and timing protocols. Tier 2 (general military history with specialist sections). Documents the yabusame practice of *firing during the brief stable moment of canter*, suggesting the trained samurai archers worked *with* gait phase rather than against it. Does not quantify penalty magnitudes for non-trained or untimed shots.
- **May, *The Mongol Art of War* (2007)** — Tier 1 specialist monograph on Mongol operational doctrine. Documents that Mongol horse archery doctrine was specifically a *stable-platform* doctrine: the firing beat is a stable platform, not a sustained acceleration. The "Parthian shot" is a specific turning-and-shooting technique during feigned retreat, not a generic mobile-firing posture.
- **Selby, *Chinese Archery* (2000)** — Tier 1 translation and commentary on Chinese archery manuals; corroborates the stable-platform reading from independent textual tradition.
- **Hardy, *Longbow* (2010)** — Tier 1; documents 10-12 aimed shots/min sustained rate of fire for trained English longbowmen; relevant for foot-archer-vs-horse-archer engagement modeling.
- **Smail, *Crusading Warfare 1097-1193* (1995)** — Tier 1; Arsuf dynamics and the Crusader/Saracen ranged-cavalry interplay; relevant by analogy for unarmored-horse-archer-vs-armored-foot scenarios.

The convergent reading across sources: trained mounted archery is a *stable-moment* technique, not a sustained-motion technique. Untrained mounted shooting (a regular cavalryman's bow shot) is qualitatively poor at any non-stationary gait. The trained/untrained distinction is real and substantial in the historical record; the source base supports a stepped model with a meaningful trained-mount qualifier; it does NOT supply specific accuracy penalty numbers.

### Constraints from CRv2 standards

- **Standard 8** (mechanical-necessity gate): a milestone may not be scoped from narrative refinement alone. There must be an observable simulation failure or an authoritative validation gap that the milestone resolves. Vasik flags that nothing currently in the sim is failing from motion-accuracy coupling's absence — so the scoping bar is HIGH.
- **Standard 9** (no amending closed phase ACs): P14 is CLOSED; the motion-accuracy coupling cannot be retrofitted as a P14 amendment. It would need its own milestone in an OPEN phase.
- **§13.4** (BUGS containers per phase): if scoped, motion-accuracy coupling could live as a P14-successor entry (if a P14-successor phase opens) or as a new milestone in a Phase 7-related slot.

### What's adjacent to the decision

- Vasik herself surfaces the possibility of a "fourth open question" she hasn't named -- her confidence on the three sub-questions being complete is only Moderate. The recommendation should consider whether a fourth axis is missing from her three.
- The `trained_archer_mount` flag's current use is limited to AI decision-gating. Promoting it to also affect hit-probability changes its semantic role, which is a small but real refactor.
- The `gait` enum is currently {walk, trot, canter, gallop} in cycle data; if the answer is "linear in gait fraction" the enum needs a numerical fraction parallel; if "stepped" the enum is already aligned.
- Cross-engine considerations: if the implementation lives in the targeting layer, it interacts with Layer 2 obstruction profiles (mount-side firing arcs may shift). If in the action-resolution layer, it interacts with the decision-model's action-scoring and may affect AI behavior in ways orthogonal to physics.

---

## What you are being asked to recommend

A position on each of Vasik's three sub-questions (curve shape; flag interaction; scope boundary), with reasoning grounded in the evidence base above. Address the implicit fourth axis if you identify one. Surface the key concerns a careful reader should be aware of, and a calibrated confidence in your recommendation.
