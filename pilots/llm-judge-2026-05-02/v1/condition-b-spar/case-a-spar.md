# /spar report: Lifspel racial taxonomy — Family-naming convention

**Iterations**: 2 / 2
**Outcome**: **Unresolved at cap** — Substantive convergence on Option 3; Challenger flags two operational-rule gaps (Spiritualia within-tier variance; sci-fi entity handling).

## Personas

- **Generator**: Biological systematics expert in the Mayr / de Queiroz / Gauthier tradition.
  - Evidence base: ICZN Code, Mayr 1969, de Queiroz & Gauthier (1990, 1992), NCBI Taxonomy working examples, great-ape reclassification literature.
- **Challenger**: Comparative mythology and worldbuilding lore curator in the Frye / Campbell / Shippey tradition.
  - Evidence base: Poetic & Prose Eddas, Mabinogion, Tolkien *Letters* and *History of Middle-earth*, Frye/Campbell/Shippey, D&D source canons (Monster Manual lineage 1977-present), Star Trek species canon, brand-voice / register theory, D&D 5e Volo's/Multiverse practitioner observations.

Evidence-base disjointness: high. Working systematics and mythopoeic register are genuinely separate corpora.

## Iteration log

### Round 1

- **Generator proposal**: Argued against uniform Linnaean naming on category-error grounds (ICZN Article 11.7 family-rank morphology presupposes monophyletic descent; engineered/mythopoeic groupings cannot supply it; cited Mayr 1969 Ch. 2, de Queiroz & Gauthier 1990/1992). **Critical methodology error: invented option labels ("Hold / Soft retire / Drop") rather than using the pack's actual Options 1, 2, 3.**
- **Challenger PNP**: Five concerns. (1) Tolkien's explicit practice (Letters 144 to Mitchison, 165 to Houghton Mifflin) defends linguistically-coherent / taxonomically-incoherent naming; Shippey 2000 Ch. 1 names this as the philological-authority claim. (2) D&D 5e Multiverse precedent (May 2022, Polygon and Gizmodo coverage) documents reader perception of pseudo-systematic naming as world-depth signaling. (3) de Queiroz & Gauthier polemic targets working systematics, not mythopoeic ornament — citation misframed. (4) ICZN Article 11.7 claim flagged as not verifiable from Challenger evidence base. **(5) Critical methodological flag**: neither agent had read the actual decision pack; both reasoning from the Generator's summary.
- **Generator signal**: `{agree: false, reasoning: "...mythopoeic dimension deferred to Challenger, recommendation cannot be finalized in Round 1"}`
- **Challenger signal**: `{agree: false, reasoning: "Generator applies a working-systematics evidence base to what is fundamentally a mythopoeic register question, and neither of us has read the decision pack we're both reasoning about."}`

### Round 2

- **Generator proposal (revised)**: Read the pack with the Read tool. Recommended **Option 3 (Mixed register), governed by the `grounding_tier` field from OQ #5 as the mechanical selector**: `NS-Tier-1` / `NS-Tier-4` → Latin-style (`Canidae` for SFxLS canines, where ICZN convention is honest and the LLM-grounding adjunct can point at real `Canidae` literature, NCBI TaxID 9608); `SC-grounded` / `engineering-construct` → English-engineered (`Sapient-Bipedal-Family` for the Human/Elf/Dwarf/Orc cluster, where the grouping is polyphyletic-by-construction). Conceded Round 1's option-invention failure; conceded Tolkien/Shippey lands for narrative copy; held that the Family-tier name does descent work in the schema (Body Plan inheritance, LLM grounding) regardless of partner-facing surface.
- **Challenger PNP**: Three concerns. (1) **Idris's Spiritualia worry is not addressed by `grounding_tier`** — pack lines 56-57 surface that Spiritualia houses Norse draugar, Greek shades, Haitian lwa, Japanese yurei, Chinese jiangshi; all `SC-grounded`, so the rule routes the entire Kingdom uniformly to English Family names, reproducing the flattening Idris flagged at the Primate level. Frye 1957 Second Essay (taxonomy of apocalyptic/demonic imagery) and Campbell 1949 (threshold-guardian/shadow typologies) treat these as non-substitutable. The rule does not see within-tier register variance. (2) **LLM-grounding asymmetry** — pack lines 73-74: `Canidae` lets an LLM ground in real Canidae literature; `Quadruped-Predator-Family` does not. The Latin-side win is *retrieval-substantive*; the English-side "win" is merely register-honest. The Generator's "mechanical, not per-Family judgment" framing understates this asymmetry. (3) **Sci-fi entities** (pack line 78) — Replicants, Borg, Culture Minds, Terminators have no Linnaean grouping in canon; default `engineering-construct` → English handles them but should be stated, not deferred.
- **Generator signal**: `{agree: true, reasoning: "Option 3 (Mixed register) governed by grounding_tier honors the working-systematics constraint where it operates and yields the floor to mythopoeic register where that operates; Round 1's posture of forcing a single-axis answer was the mistake."}`
- **Challenger signal**: `{agree: false, reasoning: "Option 3 is correct, but the grounding_tier operational rule does not address Idris's Spiritualia within-tier variance concern (all SC-grounded uniformly routed to English flattens draugar/lwa/yurei distinctions) and does not specify resolution for sci-fi entities the pack explicitly requires; recommend converging at cap with these two amendments noted."}`

## Final evaluation

**Substantive recommendation (both agents converge)**: **Option 3 (Mixed register)**. Latin-style for Families with monophyletic real-biology grounding; English-engineered for Families that are polyphyletic-by-construction or SFxLS-invented.

**Operational rule (Generator proposal, Challenger flags as incomplete)**: Use `grounding_tier` (OQ #5) as the mechanical selector — `NS-Tier-1` / `NS-Tier-4` → Latin; `SC-grounded` / `engineering-construct` → English.

**Unresolved at cap (Challenger's standing concerns)**:

1. **Spiritualia within-tier variance.** The grounding_tier rule routes all `SC-grounded` Kingdoms to English uniformly. For Spiritualia specifically — which houses cross-tradition entities (draugr, shades, lwa, yurei, jiangshi) that mythopoeic literature treats as non-substitutable — this reproduces the very flattening the Mixed register option was supposed to prevent. Recommend partner consultation with Idris on whether Spiritualia warrants either: (a) per-Family judgment override of the mechanical rule, (b) tradition-specific Family sub-naming convention, or (c) a separate within-Spiritualia tier discipline.
2. **LLM-grounding asymmetry framing.** The grounding_tier rule produces names whose retrievability differs substantially: Latin-named Families anchor real corpus retrieval; English-engineered names do not. This is a real asymmetry, not a symmetric stylistic choice. The Generator's framing understates it. Recommend explicit framing in the rule's documentation: Latin-styled names commit the schema to corpus-grounded behavior; English-styled names commit to corpus-free behavior. Both are honest; they are not equivalent.
3. **Sci-fi entity handling.** The pack constrains the engine to handle sci-fi entities (Replicants, Borg, Culture Minds, Terminators). The grounding_tier rule defaults them to `engineering-construct` → English-engineered, which works, but should be stated explicitly rather than deferred.

## Artifacts surfaced

External (cross-condition verifiable):

- ICZN Code 4th ed. (1999), Article 11.7 — family-group name availability requirements (Generator load-bearing; Challenger flagged as not verifiable from its own evidence base; **partner verification recommended**)
- Mayr, E. (1969). *Principles of Systematic Zoology*. McGraw-Hill, Ch. 2 and Ch. 10
- de Queiroz, K., & Gauthier, J. (1990). *Systematic Zoology* 39(4):307-322
- de Queiroz, K., & Gauthier, J. (1992). *Annual Review of Ecology and Systematics* 23:449-480
- Wozencraft, W.C. (2005). *Mammal Species of the World* 3rd ed., "Order Carnivora" — `Canidae` worked example
- NCBI Taxonomy: `Canidae` TaxID 9608; `Primates` TaxID 9443
- Tolkien, J.R.R. (1981, Carpenter ed.). *The Letters of J.R.R. Tolkien*, Letters 144 (Mitchison, 25 April 1954) and 165 (Houghton Mifflin, 30 June 1955)
- Shippey, T. (2000). *J.R.R. Tolkien: Author of the Century*, Ch. 1 "Lit. and Lang."
- Frye, N. (1957). *Anatomy of Criticism*, Second Essay "Theory of Symbols"
- Campbell, J. (1949). *The Hero with a Thousand Faces* — threshold-guardian/shadow typologies
- *Mordenkainen Presents: Monsters of the Multiverse* (Wizards of the Coast, May 2022) + Polygon and Gizmodo design-press reception coverage May-June 2022 — published artifact on the soft-retire failure mode

Internal (Lifspel decision pack):

- Decision pack: `docs/bfn/llm-judge-pilot-2026-05-02/decision-packs/case-a-family-naming.md` (read by both agents in Round 2; not read in Round 1)
- Vasik commentary §6 (cited as supporting the engineering-construct framing)
- Idris commentary §7 (cited as the Spiritualia variance source; Challenger Concern 1)
- Pack constraint paragraph re: ant-to-mile-long-space-creature universality (cited for the sci-fi handling concern)
- OQ #5: `grounding_tier` field proposal (load-bearing for the Generator's operational rule)

## Self-citation circularity check

- **Round 1**: load-bearing circularity risk (neither agent read the pack; reasoning grounded in Generator's summary). Mitigated in Round 2 by both agents using the Read tool.
- **Round 2**: Challenger Concern 1 grounds in Idris's commentary as quoted in the pack (mild self-citation; pack curated which quotes appear). Idris is the named domain reviewer for Natural-Mythic per pack line 71 — citation is role-appropriate rather than circular boosting. Net assessment: **clean** at Round 2.

## Recommendation for parent

**Accept Option 3 (Mixed register) as the family-naming convention, governed by the `grounding_tier` field from OQ #5 as the mechanical selector** — but acknowledge the recommendation is contingent on three unresolved-at-cap items:

1. **Idris consultation on Spiritualia**: the mechanical rule needs either a per-Family override path or tradition-specific sub-naming for Spiritualia, before Spiritualia Families ship at scale.
2. **Document the LLM-grounding asymmetry explicitly**: Latin-styled names commit to corpus-grounded behavior; English-styled to corpus-free. Both honest, not equivalent.
3. **Specify the sci-fi entity default explicitly** (probably `engineering-construct` → English, but state it rather than defer).

The Generator-Challenger disagreement at cap is on operational completeness, not on the chosen option. This is honest /spar output: substantive convergence on the recommendation, with named amendments the partner needs to resolve before the rule can ship as final policy.

A self-citation circularity flag attaches to the ICZN Article 11.7 reference, which carries some weight in the Generator's category-error argument; the Challenger could not ground-truth it from its own evidence base. **Partner verification of that specific code citation is recommended** before treating it as load-bearing in any downstream documentation.
