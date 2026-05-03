# /spar report: NCCC CS101 introductory programming curriculum architecture -- Path A (concepts-first) / Path B (language-first) / hybrid

**Iterations**: 2 / 2
**Outcome**: **Converged.** Both agents signaled agree:true after Round 2. Convergence is on a *Modified Path B* with construct-warmup micro-passes embedded inside generative project work, contextualized per Guzdial, with the transfer cohort served by a second-course bridge and the 73%/75% framing explicitly named as a pipeline problem the curriculum cannot solve.

## Personas

- **Generator**: CS-education researcher and curriculum theorist.
  - Evidence base: SIGCSE proceedings; *ACM ToCE*; *Computer Science Education* journal; Sweller, J., van Merriënboer, J., & Paas, F. (1998), "Cognitive Architecture and Instructional Design," *Educational Psychology Review* 10(3), 251-296; van Merriënboer & Kirschner (2018), *Ten Steps to Complex Learning* (4C/ID model); Bennedsen, J. & Caspersen, M.E. (2007, 2019), "Failure rates in introductory programming," *ACM SIGCSE Bulletin* 39(2), 32-36 and successor work; Soloway, E. & Spohrer, J.C. (1989), *Studying the Novice Programmer*; Pea, R.D. (1986), "Language-independent conceptual 'bugs' in novice programming," *Journal of Educational Computing Research* 2(1), 25-36; Lopez et al. (2008) *ICER*; Venables et al. (2009) BRACElet hierarchy; Lister et al. (2004) ITiCSE working group; Felleisen et al., *How to Design Programs* (HtDP) and design-recipe pedagogy; Guzdial, M. (2003 onward) Media Computation; Guzdial & Tew (2006), *ACM ICER*; Renkl, A. (2014) on worked examples; Smith & Garcia (2021), *Journal of College Student Transfer*; Park et al. (2023), *Computer Science Education*.

- **Challenger**: Andragogy / community-college-pedagogy specialist.
  - Evidence base: Knowles, M.S., Holton, E.F., & Swanson, R.A. (2020), *The Adult Learner* (9th ed.); Wlodkowski, R.J. & Ginsberg, M.B. (2017), *Enhancing Adult Motivation to Learn* (4th ed.) and the Motivational Framework for Culturally Responsive Teaching; Tinto, V. (2012), *Completing College: Rethinking Institutional Action*; Rendón, L.I. (1994), "Validating Culturally Diverse Students," *Innovative Higher Education* 19(1), 33-51, and Rendón (2002), Validation Theory; Bailey, T., Jaggars, S.S., & Jenkins, D. (2015), *Redesigning America's Community Colleges* (Harvard University Press; the guided-pathways framework); Achieving the Dream (ATD) seven-state intervention data; Center for Community College Student Engagement (CCCSE) *SENSE* / *CCSSE* benchmark reports; Calcagno, J.C., Crosta, P., Bailey, T., & Jenkins, D. (2008), *Educational Evaluation and Policy Analysis*; Crisp, G. & Nora, A. (2010), *Community College Review*; Eagan, M.K. & Jaeger, A.J. (2009), *Research in Higher Education*; Jaeger, A.J. & Eagan, M.K. (2011); Margolis, J. & Fisher, A. (2002), *Unlocking the Clubhouse*; Margolis, Estrella, Goode et al. (2008), *Stuck in the Shallow End*; Cheryan, S., Plaut, V.C., Davies, P.G., & Steele, C.M. (2009), *JPSP* on ambient classroom cues and CS belonging; Steele, C.M. & Aronson, J. (1995) stereotype threat; Cercone, K. (2008), *AACE Journal*; Mezirow, J. (1991), *Transformative Dimensions of Adult Learning*.

Evidence-base disjointness: high. CS-education-internal empirical and theoretical work (cognitive load, novice misconceptions, sequencing meta-analyses) and adult-learner / community-college / sense-of-belonging literature are genuinely separate corpora. Cross-citation occurred only at the synthesis layer (Guzdial Media Computation appears in both as the bridging body of work).

## Iteration log

### Round 1

- **Generator proposal**: Recommended a "Pseudocode-Anchored Spiral" (PAS) hybrid: weeks 1-2 algorithmic thinking in pseudocode + state diagrams paired with trace-and-predict against pre-written Python; weeks 3-16 a 7-10 day spiral of "concept-then-syntax-then-project" mini-cycles; programming projects begin in week 4 (vs. Path A's week 8 or Path B's week 10). Closer to Path A in concept primacy but rejecting Path A's 5-week defer-coding cliff. Grounded in Sweller cognitive-load theory bounded by van Merriënboer's 4C/ID against five-week separation; Soloway/Spohrer/Pea on language-independent decomposition gaps; Bennedsen & Caspersen 2007 meta-analysis showing sequencing is not load-bearing for completion (raised the question whether architecture is the right lever for the 73%/75% threshold); Lopez/Venables BRACElet trace-and-predict for first-gen/ESL gap mechanism. Explicitly deferred to Challenger on adult-learner motivational profiles, instructor-training cost across 6-8 adjuncts, general-education priorities for the 62% non-continuing cohort, and whether the completion-rate framing was the right metric.

- **Challenger PNP**: Five concrete concerns, each with specific external citations. (1) The week-4 generative milestone falls past Tinto's integration-critical window (first 4-6 weeks attrition risk), Wlodkowski & Ginsberg's experienced-competence requirement (inclusion+attitude window in first 2-3 weeks), Bailey et al.'s "first three weeks" persistence predictor, and Rendón's validation-must-be-generative requirement; trace-and-predict is receptive, not generative. (2) PAS's 12-cycle weekly handoff between three modes is exactly the redesign profile that degrades across adjunct pools per Eagan & Jaeger (2009, 2011) on contingent-faculty consistency, Bailey et al. (2015) Ch. 5 on hybrid-pedagogy degradation, and CCCSE benchmark data on cross-section variance. (3) For NCCC's 62%/38% terminal-cohort majority, Bailey et al.'s majority-population principle argues for terminal-design dominance: earlier building, less abstract pre-programming, more reading and modifying existing code, real tooling exposure. (4) Validation/belonging dimension is at least as load-bearing as cognitive load for first-gen / ESL: Margolis & Fisher (2002) on legitimate-generative-participant signaling; Cheryan et al. (2009) on ambient cues in first weeks; Steele & Aronson (1995) on trace-and-predict as high-threat assessment surface; Rendón's validation requires generative artifact. (5) The 73%/75% framing is a pipeline problem (advising, financial aid, work-schedule, prerequisite math), not a curriculum problem; Bailey et al. and ATD data show course-level redesign reliably fails to move aggregate completion at open-admission institutions.

- **Generator signal**: `{agree: ASSESS-AT-NEXT-ROUND, reasoning: "Defer to Challenger on andragogy / adjunct-training-realism / general-ed-design / completion-metric-framing"}`

- **Challenger signal**: `{agree: false, reasoning: "PAS is cognitive-load-defensible but andragogy-suboptimal. Five concrete gaps require Round 2 revision: week-2/3 generative milestone, simpler architecture for adjunct pool, design for the 62% terminal cohort as primary, validation/belonging beyond cognitive-load, and pipeline framing followed all the way."}`

### Round 2

- **Generator revision**: Substantive integration of all five Challenger concerns. Replaced PAS with **Modified Path B**: (1) Weeks 1-3 generative from day one with Guzdial-style contextualized tasks; every student ships a working artifact they wrote themselves by end of week 2 -- the Rendón-validation event on time per Wlodkowski/Ginsberg and Tinto. (2) Weeks 4-12 continued generative project work as the spine, with **construct-specific 20-30 min trace-and-predict micro-passes** layered *inside* project work (not as separate mode) when a project introduces a new construct; preserves Lopez/BRACElet construct-specific transfer effect without making tracing the framing mode. (3) Single-mode architecture (project-based, generative) with shared bank of construct-warmups; no weekly handoff between modes; flattest architecture compatible with adjunct pool per Eagan & Jaeger and Bailey Ch. 5. (4) Designed for the 62% terminal cohort as primary per Guzdial Media Computation (computing for everyone with CS-major preparation as a track inside, not a competing frame); transfer cohort served by a second-course bridge (CS1.5 or honors track inside CS2). (5) 73%/75% explicitly named as pipeline-and-structural problem, not curriculum; architecture choice does not claim to move aggregate completion. Generator conceded each concern and articulated where CS-ed evidence supported the integration (Guzdial replications + Lopez/BRACElet construct-specificity + worked-example evidence per Renkl/Sweller).

- **Challenger PNP on Round 2**: Substantive, not cosmetic, revision; architecture is internally coherent and externally defensible. Five residual implementation specifics surfaced: (a) week 1 should contain at least one student-authored running artifact even if smaller than the week-2 milestone; "by end of week 2" risks slipping if week 1 is dead time. (b) Warmup-bank needs named owner, misconception-tag metadata on each warmup, and a default selection rule -- otherwise "pull the loop warmup" degenerates to "pull any loop exercise" and the construct-specificity that delivers the Lopez/BRACElet transfer effect is lost. (c) Transfer-bridge depends on articulation agreements with receiving four-year institutions per Crisp & Nora (2010); should be flagged as a prerequisite, not assumed. (d) Pipeline naming should be paired with at minimum three concrete structural interventions per Bailey et al. Ch. 6-7 -- corequisite math support, embedded-tutor advising touchpoints in weeks 3 and 6, proactive intervention triggers tied to first-assignment non-submission. (e) Warmup-as-load-management framing only holds if adjuncts deliver it as load management, not as prep; instructor script / adjunct training needs to name this explicitly to prevent collapse back to PAS-flavored sequencing.

- **Generator signal**: `{agree: true, reasoning: "Accept Challenger pressure on all five concerns; revised architecture is generative-first single-mode with construct-warmup micro-passes; named pipeline framing; second-course bridge for transfer cohort; residuals: warmup-bank curation quality, validation-event contextualization quality, standing pipeline gap that no curriculum choice closes."}`

- **Challenger signal**: `{agree: true, reasoning: "Round 2 substantively addresses all five concerns with architectural changes (week-2 milestone, single-mode collapse, Guzdial terminal-cohort framing, validation as generative artifact, pipeline named as named limit) rather than cosmetic relabeling. Residual gaps are implementation specifics within a sound architecture, addressable in a recommendation document. Honest signal: agree."}`

## Final evaluation

**Converged recommendation, both agents agree**: Adopt **Modified Path B** for NCCC's CS101 redesign:

1. **Generative-first architecture**: project-based work as the spine throughout the 16-week semester. Weeks 1-3 are the inclusion+attitude+validation window; every student ships a working artifact they wrote themselves by end of week 2. Week 1 should contain a smaller student-authored running artifact ("hello, your name, three lines you wrote") to prevent the validation event from slipping. Tasks contextualized per Guzdial Media Computation -- image manipulation, CSV from a domain students care about, simple text processing -- to deliver Rendón validation and Margolis/Fisher belonging in the first weeks.

2. **Concept-reinforcement via construct-specific micro-passes, not as a separate mode**: when a project introduces a new construct (loops, nested data, functions-as-parameters, recursion), a 20-30 minute trace-and-predict warmup on *that specific construct* runs as load-management before the project work that uses it. Framed and delivered as load management within real work the student already owns -- NOT as "prep before the real thing." Adjunct training language explicitly names this framing to prevent collapse back toward PAS-flavored sequencing.

3. **Single-mode adjunct-coordination architecture**: one mode (project-based, generative); shared bank of construct-warmups adjuncts pull from when their section reaches that construct. Bank specifications (the Round 2 residual the spar should be implemented per): named owner, misconception-tag metadata on each warmup tied to the Soloway/Spohrer/Pea localized misconception literature, and a default selection rule when adjuncts have not diagnosed misconceptions in their section.

4. **Designed for the 62% terminal cohort as primary**: per Bailey et al.'s majority-population principle and Guzdial's "computing for everyone" framing. Computational literacy as the goal: read+modify existing code, real tooling (debuggers, version control basics), domain-relevant projects. Preserves higher pass rates and broader belonging effects per Margolis/Fisher and Cheryan et al.

5. **Transfer cohort (38%) served by second-course bridge**: CS1.5 or honors track inside CS2 carries the deeper conceptual primitives (recursion, AST-level reasoning, Felleisen-style design recipes). The bridge depends on articulation agreements with receiving four-year institutions per Crisp & Nora (2010); articulation work is a prerequisite for the architecture and must be explicitly worked rather than assumed.

6. **73%/75% framing named explicitly as pipeline, not curriculum**: per Bailey et al. (2015), Margolis et al. (2008) *Stuck in the Shallow End*, and ATD data. Course-level redesign reliably fails to move aggregate completion at open-admission community colleges; architecture choice does not claim to move it. The recommendation pairs the curriculum architecture with three concrete structural interventions per Bailey et al. Ch. 6-7: corequisite math support (replacing prerequisite developmental math), embedded-tutor advising touchpoints in weeks 3 and 6, and proactive intervention triggers tied to first-assignment non-submission. These are not curriculum scope but ARE recommendation scope.

**Residual concerns explicitly flagged** (all addressable in a recommendation document, not architectural defects):

- Warmup-bank curation quality (named owner, misconception-tag metadata, default selection rule).
- Validation-event contextualization quality (architecture cannot mandate that adjuncts pick domains students care about; instructor selection of context matters).
- Articulation friction for the transfer-cohort second-course bridge.
- Adjunct framing fidelity for load-management warmups (instructor script / training language).
- Standing pipeline gap that no curriculum choice closes.

## Artifacts surfaced

External (cross-condition verifiable):

**CS-education (Generator):**
- Sweller, J., van Merriënboer, J., & Paas, F. (1998). "Cognitive Architecture and Instructional Design." *Educational Psychology Review* 10(3), 251-296.
- van Merriënboer, J. & Kirschner, P. (2018). *Ten Steps to Complex Learning*. Routledge. (4C/ID model)
- Bennedsen, J. & Caspersen, M.E. (2007, 2019). "Failure rates in introductory programming." *ACM SIGCSE Bulletin* 39(2), 32-36; successor meta-analysis.
- Soloway, E. & Spohrer, J.C. (1989). *Studying the Novice Programmer*. Lawrence Erlbaum.
- Pea, R.D. (1986). "Language-independent conceptual 'bugs' in novice programming." *Journal of Educational Computing Research* 2(1), 25-36.
- Lopez, M., Whalley, J., Robbins, P., & Lister, R. (2008). "Relationships between reading, tracing, and writing skills in introductory programming." *ACM ICER 2008*.
- Lister, R., Adams, E.S., et al. (2004). "A multi-national study of reading and tracing skills in novice programmers." *ITiCSE Working Group Reports*.
- Venables, A., Tan, G., & Lister, R. (2009). "A closer look at tracing, explaining, and code writing skills in the novice programmer." *ICER 2009* (BRACElet hierarchy).
- Felleisen, M., Findler, R.B., Flatt, M., & Krishnamurthi, S. *How to Design Programs* (HtDP), 2nd ed. (design-recipe pedagogy)
- Guzdial, M. (2003 onward). Media Computation; Georgia Tech and community-college replication studies.
- Guzdial, M. & Tew, A.E. (2006). "Imagineering inauthentic legitimate peripheral participation." *ACM ICER 2006*.
- Renkl, A. (2014). "Toward an instructionally oriented theory of example-based learning." *Cognitive Science* 38(1), 1-37.
- Smith, J. & Garcia, M. (2021). *Journal of College Student Transfer*. (Path A transfer-data-structures advantage)
- Park, A. et al. (2023). *Computer Science Education*. (Path A transfer effect replication)

**Andragogy / community-college (Challenger):**
- Knowles, M.S., Holton, E.F. III, & Swanson, R.A. (2020). *The Adult Learner: The Definitive Classic in Adult Education and Human Resource Development*, 9th ed. Routledge.
- Wlodkowski, R.J. & Ginsberg, M.B. (2017). *Enhancing Adult Motivation to Learn: A Comprehensive Guide for Teaching All Adults*, 4th ed. Jossey-Bass. (Motivational Framework for Culturally Responsive Teaching)
- Tinto, V. (2012). *Completing College: Rethinking Institutional Action*. University of Chicago Press.
- Rendón, L.I. (1994). "Validating Culturally Diverse Students." *Innovative Higher Education* 19(1), 33-51.
- Rendón, L.I. (2002). "Community College Puente: A Validating Model of Education." *Educational Policy* 16(4), 642-667.
- Bailey, T., Jaggars, S.S., & Jenkins, D. (2015). *Redesigning America's Community Colleges: A Clearer Path to Student Success*. Harvard University Press.
- Achieving the Dream seven-state intervention data; CCCSE *SENSE* / *CCSSE* benchmark reports.
- Calcagno, J.C., Crosta, P., Bailey, T., & Jenkins, D. (2008). "Stepping Stones to a Degree." *Educational Evaluation and Policy Analysis* 30(2), 155-170.
- Crisp, G. & Nora, A. (2010). "Hispanic Student Success: Factors Influencing the Persistence and Transfer Decisions of Latino Community College Students." *Community College Review* 37(4), 291-322.
- Eagan, M.K. & Jaeger, A.J. (2009). "Effects of Exposure to Part-Time Faculty on Community College Transfer." *Research in Higher Education* 50, 168-188.
- Jaeger, A.J. & Eagan, M.K. (2011). "Examining Retention and Contingent Faculty Use in a State System." *Research in Higher Education* 52, 511-538.
- Margolis, J. & Fisher, A. (2002). *Unlocking the Clubhouse: Women in Computing*. MIT Press.
- Margolis, J., Estrella, R., Goode, J., et al. (2008). *Stuck in the Shallow End: Education, Race, and Computing*. MIT Press.
- Cheryan, S., Plaut, V.C., Davies, P.G., & Steele, C.M. (2009). "Ambient belonging: How stereotypical cues impact gender participation in computer science." *Journal of Personality and Social Psychology* 97(6), 1045-1060.
- Steele, C.M. & Aronson, J. (1995). "Stereotype threat and the intellectual test performance of African Americans." *Journal of Personality and Social Psychology* 69(5), 797-811.
- Cercone, K. (2008). "Characteristics of adult learners with implications for online learning design." *AACE Journal* 16(2), 137-159.
- Mezirow, J. (1991). *Transformative Dimensions of Adult Learning*. Jossey-Bass.

## Recommendation for parent

NCCC should adopt the converged Modified Path B architecture above for the 2026-27 CS101 redesign. The convergence is robust: both agents agreed at Round 2 after substantive Round 1 disagreement; the evidence chain is externally corroborated across two genuinely-distinct corpora (CS-education research and andragogy/community-college pedagogy); and the residuals are implementation specifics rather than architectural defects.

Three implementation specifications NCCC should treat as load-bearing alongside the architecture:

1. **Warmup-bank with named owner, misconception-tag metadata, default selection rule.** Without these, "construct-specific" warmups degrade to "any-construct" warmups and the Lopez/BRACElet transfer effect is lost. Department investment in bank curation and a single coordinator is non-negotiable.

2. **Articulation work as prerequisite to the second-course bridge.** The transfer-cohort serving depends on CS1.5 or honors-track-CS2 articulating cleanly to receiving four-year institutions. Articulation agreements should be initiated alongside the curriculum redesign, not after it.

3. **Pipeline interventions paired with curriculum**: corequisite math support replacing prerequisite developmental math; embedded-tutor advising touchpoints in weeks 3 and 6; proactive intervention triggers tied to first-assignment non-submission. These are not curriculum scope, but they are scope of the recommendation to NCCC, and the curriculum choice's success in moving completion is partly hostage to them.

Both agents agree that the recommendation does NOT include adopting pure Path A (rejected for its 5-week defer-coding cliff that violates the Wlodkowski/Ginsberg inclusion-attitude window, the Tinto integration-critical-window, and Rendón validation-as-generative requirement) or pure Path B (rejected because it leaves the Soloway/Spohrer/Pea language-independent-decomposition gap unaddressed, surrenderring the construct-specific transfer effect that the Lopez/BRACElet line genuinely supports). Both agree that the 73%/75% framing is a pipeline problem the architecture cannot solve and should be named as such.
