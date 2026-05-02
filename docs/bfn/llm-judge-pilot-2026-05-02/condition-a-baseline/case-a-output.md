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
