# Case A — Decision Pack: P13M1 Family-naming convention

**Source**: `docs/plans/CRv2/crv2-p13m1-taxonomy-brainstorm.md` §9 Open Question 4
**Status**: Genuinely pending. Phase 13 is SCOPED; M1 formal scope to be produced by Chal+Icarus in response to partner discussion of the brainstorm.

---

## The decision

**Question.** What naming convention should the racial taxonomy use for **Family-tier nodes** -- the level above Genus / Species and below Kingdom?

The Family tier groups Genera by Body Plan and shared physiology within a Kingdom. Examples within Animalia would include the Family that contains *Homo* (Humans), *Elf*, *Dwarf*, *Orc*, *Halfling*, *Ogre*, *Tiefling-base* -- all Genera sharing a primate body plan.

**Three options on the table:**

1. **Scientific-Latin-style** -- e.g., `Primatidae-SFxLS`, `Canidae`, `Dracoidea`, `Arthropodidae`. Borrows scientific naming convention; carries the visual and grammatical legitimacy of real Linnaean taxonomy.

2. **Explicitly-engineered names** -- e.g., `Sapient-Bipedal-Family`, `Quadruped-Predator-Family`, `Winged-Drake-Family`. English-language, descriptive, signals engineering intent rather than borrowed scientific authority.

3. **Mixed register** -- Latin-style when the Family has a real-biology grounding (`Canidae` for SFxLS canines, which are calibrated against real wolves); English-style when the Family is SFxLS-invented with no real-world counterpart (`Sapient-Bipedal-Family` for the Human/Elf/Dwarf/Orc grouping, which has no biological precedent).

The decision applies uniformly to every Family-tier node in the schema and propagates to documentation, the LLM-grounding adjunct table, the in-tool authoring affordance, and partner-facing brand surfaces.

---

## Evidence base

### What the schema already does

The current `sf_races` table has two enum fields doing partial taxonomic work: `race_category` (humanoid / creature / undead / spirit / beast / fae / elemental / construct / plant / aberrant / insect) and `entity_archetype` (biological / mechanical / spiritual / elemental / hybrid / collective). It has no formal Kingdom or Family tier today; lineage relationships between Plains Elf and Wood Elf rely on author discipline rather than structural inheritance. The brainstorm proposes adding a Kingdom → Family → Genus → Species lineage axis.

### What Family-tier names will appear in

- The `lineage` field on `sf_races` rows
- A separate `sf_race_external_references` table mapping SFxLS Family entries to real-world Linnaean classification where applicable (LLM-grounding adjunct)
- The Race Builder authoring tool (P13M2; Chal-led) -- partners author races by selecting Family from a dropdown
- The Catalog Expansion deliverable (P13M3) -- Chal authors N in-genre + M out-of-genre races, with Lena and Idris reviewing per-race against the Family it claims membership in
- Engine inheritance: Family-tier Body Plan templates default hit-zone distributions for member races, which races override per-species
- LLM-grounding: when an LLM is asked to reason about a race, the Family-tier name is in the prompt context

### Lena Vasik's commentary (extracted from §6 of the brainstorm)

> The Linnaean mapping is sound for real-world cases. For Timber Wolf, Andalusian Horse, Giant Spider (if accepting analogical mapping to Scarabaeidae or another arthropod family), and Human, the Family-Genus-Species structure maps directly onto actual scientific taxonomy. The tiered trait-defaulting gives correct shared-physiology inference at Family (mammals share warm-bloodedness, endothermy, specific body plans) and Genus levels (Canis spp. share pack behavior, specific cranial morphology). Citation chains for shared-trait inference are available at every level — Tier 1 evidence.

On where the framing breaks:

> The LP-signature axis is a game abstraction, not empirically derived. This deserves honesty. Corpus/Mentus/Spiritus/Affectus is an excellent design structure for this engine, but the four domains do not map onto natural categories a biologist would recognize. Humans do not have a measurable "spiritus" organ. Framing the signature as "which domains this entity *has*" rather than as empirical claim is the honest approach. [...] when we ground the taxonomy in "real biology" we should not claim the LP signature is part of the biological grounding. It's part of the game grounding.

On per-species override density:

> Body Plan templates: strong at Family level, softer for per-species overrides. The Primate body-plan template will produce a hit-zone distribution that matches real primate anatomy closely. Well-evidenced. But the template will not capture what makes a Dwarf physically different from a Human without per-species overrides. [...] Per-species override density is likely high for fantasy races, and the "author only what differs" benefit of template inheritance is genuine but smaller than it first appears.

### Idris Harmon's commentary (extracted from §7 of the brainstorm)

> The Family-tier name "Primatidae-SFxLS" is doing a lot of cultural work. Grouping Human, Elf, Dwarf, Orc, Halfling, Ogre under a single Family called "Primates" is a choice. Defensible by body-plan-shared-physiology reasoning. But it flattens real traditions: Norse alfar are not a sub-species of Primate to any tradition that actually uses them. They are a separate order of being. Tolkien's Quendi are demigods who chose incarnation -- grouping them with Mortals is Tolkien-itself-controversial. The Primate grouping is an engineering convenience, not a mythological consensus. Fine, but call it what it is. Suggest naming it to signal engineering intent (`Sapient-Bipedal-Family` or similar) rather than borrowing the scientific term and pretending the alignment is neutral. Flagging as naming concern; partners decide -- vote is for clarity.

Idris also surfaces (separately) that **Spiritualia is the Kingdom with the richest internal variance across traditions** (Norse draugar, Greek shades, Haitian lwa, Japanese yurei, Chinese jiangshi all sit under Spiritualia at Kingdom but differ profoundly at every tier below) -- which has implications for how Spiritualia's Families are named.

### The grounding-tier discipline (a related open question, OQ #5)

A separate brainstorm proposal: every Kingdom / Family / Genus node carries a `grounding_tier` field per Lena's recommendation:
- `NS-Tier-1` (real biology, well-evidenced)
- `NS-Tier-4` (real biology, weak evidence)
- `SC-grounded` (Southern Cross, mythologically/narratively grounded but not empirically calibrated)
- `engineering-construct` (SFxLS-invented, no claim of real-world grounding)

If adopted, the grounding-tier field is metadata adjacent to the Family name and could potentially influence the naming choice (Latin-style names with `engineering-construct` tier might feel mismatched; English-style names with `NS-Tier-1` tier might lose the convention familiarity Vasik describes).

### What's downstream of this decision

- **P13M3 review ownership** -- Lena reviews Natural-Evolved races; Idris reviews Natural-Mythic. The naming convention affects how each reviewer reads the Family they're working on -- Lena's Tier-1 evidence chains attach naturally to Latin-style names; Idris's archetypal-coherence reviews attach naturally to descriptive English-style names.
- **Race Builder UX** -- partners selecting Family from a dropdown; long descriptive names are harder to scan, Latin names are unfamiliar to non-biologist authors.
- **Brand and partner-facing communication** -- Family names appear in agent-generated lore, scenario text, and any partner-facing tooling.
- **LLM-grounding strength** -- a Family named `Canidae` lets an LLM ground inferences in the actual `Canidae` literature; a Family named `Quadruped-Predator-Family` does not.

### Constraints

- Per `docs/plans/lifspel-product-objective.md`, the engine must handle "ant to mile-long space creature, dagger to laser cannon" -- the naming convention must work for sci-fi entities (Replicants, Borg, Culture Minds, Terminators) that have no Linnaean-equivalent grouping in their canon.
- The Substrate axis is orthogonal -- a Family in Animalia and a Family in Mechanica will sit side-by-side in the same field, and the convention has to handle both.
- The decision is hard to reverse: changing Family names later means updating every `sf_races` row, every documentation reference, every authoring-tool option, and every LLM-grounding context. Forward-compatibility matters.

---

## What you are being asked to recommend

A single naming convention -- Option 1, Option 2, or Option 3 -- for Family-tier nodes in the racial taxonomy, with rationale grounded in the evidence above. Surface the key concerns a careful reader should be aware of, and a calibrated confidence in your recommendation.
