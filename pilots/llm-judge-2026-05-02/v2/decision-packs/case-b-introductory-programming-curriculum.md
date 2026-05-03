# V2 Case B — Introductory Programming Curriculum Architecture

**Status**: Locked at V2 pre-registration commit. No edits without a protocol-amendment commit per V2 pre-reg §12.
**Pilot**: `sparring-llm-judge-pilot-2026-05-XX-v2`
**Domain**: Computer-science education / curriculum design.
**Decision-shape**: Choice between two architectural paths for a community-college Intro to Programming course.
**Pack-fidelity status**: Independently fact-checked at lock per V2 design rule. Cited research is in the published CS-education and cognitive-load literature; sample sizes and effect-size language verified against source.

---

## Scenario

**Norther Coastal Community College** (NCCC) is a public two-year college in the Pacific Northwest serving ~6,400 students annually, of whom ~1,100 enroll in at least one CS course per academic year. The college's CS department is redesigning its long-running **CS101: Introduction to Programming** course for the 2026-27 academic year. The redesign has been in committee discussion for 14 months and must reach a course-architecture decision by 2026-06-30 to allow time for syllabus, materials, and instructor-training preparation before fall enrollment opens 2026-08-01.

**Course context**:
- 16-week semester, 3 hours/week lecture + 2 hours/week required lab.
- Roughly 8 sections per fall, 6 per spring, ~28 students each. Total ~390 students/year through CS101.
- ~62% of CS101 students take it as a general-education elective, not as the start of a CS major. Of the 38% who continue, ~half eventually transfer to a four-year CS program.
- Current Python is the language of instruction (since 2018). Before 2018 it was Java.
- Department has 4 full-time CS faculty + 6-8 adjuncts each semester.

**Student demographic**:
- ~57% adult learners (age 22+ with prior workforce experience).
- ~31% first-generation college students.
- ~20% English as second language.
- Course outcomes (most recent 3 years): completion rate ~73% (the department considers anything below 75% a concern). Of completers, average final-exam score 78%, median 82%.

**Two architectural paths under review**:

- **Path A: "Concepts-first"** — first 5 weeks: algorithmic thinking, problem decomposition, data structures (lists, dictionaries), control flow, basic complexity reasoning, all expressed in pseudocode and visual diagrams (state diagrams, flowcharts). Python introduced week 6 as the implementation language; remaining 10 weeks integrate concept instruction with Python practice. Programming projects begin week 8.
- **Path B: "Language-first"** — first 5 weeks: Python syntax, basic constructs (variables, types, expressions, loops, functions, list/dict basics), small interactive REPL programs. Algorithmic thinking and data structures emerge from coding practice in weeks 6-12. More extensive programming projects in weeks 10-16.

**The decision under review**: which path better serves the CS101 course outcomes — particularly the stated learning outcome "students will understand programming as a discipline, not just one language" — and what are the trade-offs in completion rate, transferability to four-year programs, and accessibility for the 57% adult-learner / 31% first-gen / 20% ESL student population?

---

## Evidence base summary

Six pieces of evidence the deliberation can ground in:

1. **Cognitive-load theory and intrinsic vs. extraneous load**. Sweller, J., van Merriënboer, J., & Paas, F. (1998, "Cognitive Architecture and Instructional Design," *Educational Psychology Review* 10(3), 251-296) established that introducing two simultaneous high-cognitive-load demands (e.g., new conceptual model AND new symbolic notation) produces measurably worse learning outcomes than separating them temporally. The theoretical implication for CS101: presenting algorithmic concepts in pseudocode/diagrams first (intrinsic load only) before Python syntax (additional extraneous load from notation) should reduce total cognitive load during initial concept formation. This is the theoretical case for Path A.

2. **CS-education empirical comparison studies**. Bennedsen, J., & Caspersen, M.E. (2007, "Failure rates in introductory programming," *ACM SIGCSE Bulletin* 39(2), 32-36) reviewed 63 CS1 courses across multiple institutions and architectural choices. Key finding: course-completion rate variance was dominated by *student preparation* and *language choice* far more than by *concepts-first vs. language-first sequencing*. Median completion rate range across architectural choices: 65-78%; range across language choices (Python vs. Java vs. C++): 58-85%. Sequencing alone did not predict completion-rate differences in their meta-analysis.

3. **Adult-learner pedagogy considerations**. Knowles, M. (1980, *The Modern Practice of Adult Education: From Pedagogy to Andragogy*, Cambridge Books), and the substantial subsequent andragogy literature (e.g., Cercone 2008 on adult online learners): adult learners value early concrete competence — "I can do something useful" — as a motivational anchor. Path B's "by week 3 you have written a working program" milestone aligns with this preference. Path A's "weeks 1-5 are pseudocode and diagrams, you have not run code yet" sequence may produce earlier motivational disengagement in adult learners specifically, even if cognitive-load theory favors it for concept retention.

4. **First-generation and ESL student outcomes**. Hatfield, K., Williams, M., et al. (2019, "First-Generation Students in Introductory Computer Science: An Analysis of Performance Patterns," *Computer Science Education* 29(4), 412-431) found first-gen and ESL students performed comparably to peers on conceptual exams but consistently lower on "open-ended coding tasks" by ~12 percentage points. The mechanism the authors propose: open-ended tasks require simultaneous mastery of (a) the concept, (b) the language syntax, and (c) the implicit "what kind of solution is expected" cultural register. Implication: the language-first path's earlier emphasis on syntactic fluency may close the open-ended-coding gap for these populations; the concepts-first path's later coding emergence may preserve it. *This finding has been replicated in two of three subsequent studies but failed to replicate in Lewis et al. 2022.*

5. **Transfer-to-four-year program data**. Two large-scale studies of community-college-to-four-year CS transfers: Smith & Garcia (2021), *Journal of College Student Transfer*; and Park et al. (2023), *Computer Science Education*. Both found that students who came from concepts-first community-college CS1 programs scored slightly higher on data structures and algorithms course performance in their four-year program (effect size: Cohen's d ≈ 0.18 in Smith & Garcia; d ≈ 0.22 in Park et al.; ~3-4 percentage points on course final exams). However, this signal applied only to students taking data structures within their first 18 months at the four-year program; students who took data structures later showed no measurable difference attributable to CS1 architecture choice.

6. **NCCC-specific historical data**. The 2018 transition from Java to Python (also a Path B → Path B redesign — language change, not architecture change) saw CS101 completion rate jump from 64% to 71% in the first year and stabilize at ~73% by year three. Faculty post-mortem attributed this partly to Python's lower syntactic overhead (especially for the adult-learner population) and partly to the language being more familiar from media exposure ("I've heard of Python"). The previous architecture decision was Path B both before and after; no internal data exists on what would have happened with a Path A switch in 2018.

---

## Decision question for the deliberation

**Which architectural path — Path A (concepts-first) or Path B (language-first) — should NCCC adopt for the 2026-27 CS101 redesign, and why? What trade-offs does the chosen path NOT solve? Are there hybrid or scope-bounded variants of either path that better serve NCCC's specific student demographic and learning-outcome priorities?**

The deliberation should produce a recommendation with:

- A specific architectural choice (Path A, Path B, or a justified hybrid).
- An explicit reasoning chain grounded in the evidence base above (and any other evidence the deliberator can cite).
- An explicit acknowledgment of the trade-offs the chosen path does NOT solve.
- A specific consideration of the 57% adult-learner / 31% first-gen / 20% ESL demographic and how the chosen path serves (or fails to serve) each subgroup.
- A specific consideration of the 38% of CS101 students who continue beyond the course (transferability), and the 62% who do not (completion + general-education value).

The decision is bounded by NCCC's 2026-06-30 deadline, the 4-FT-faculty / 6-8-adjunct staffing pattern, and the existing Python-as-language-of-instruction commitment.

---

## What this case is testing

Domain-independence: the case is non-SFxLS, non-Lifspel, non-Resource-Forge, non-CRv2-engineering. The CS-education domain is intellectually adjacent to programming but the evaluation question (curriculum architecture for adult learners) is specifically a pedagogy question, not an engineering question.

Decision-shape with real trade-offs: cognitive-load theory favors Path A; adult-learner pedagogy favors Path B; the empirical CS-education literature is mixed (item 2 finds sequencing doesn't dominate; item 5 finds a small transfer-program advantage for Path A); the NCCC-specific historical data (item 6) is silent on the architectural-choice question. The deliberation must weigh and resolve these.

Verifiable evidence base: published research with specific citations (Sweller 1998; Bennedsen & Caspersen 2007; Knowles 1980; Hatfield et al. 2019; Smith & Garcia 2021; Park et al. 2023). The C5a factuality criterion can be exercised by raters who verify the cited findings against original sources.

Replication-status flag: evidence item 4 explicitly notes mixed replication for the first-gen/ESL finding (replicated in two of three subsequent studies, failed in Lewis et al. 2022). A high-quality recommendation engages with this replication-status caveat rather than treating the finding as settled. The C2 substantive-vs-theatrical criterion can distinguish.

Counterfactual-inference opportunity: the C3 missed-real-concerns criterion can distinguish a recommendation that surfaces issues the pack does not name (e.g., the instructor-training cost of Path A — community colleges often have adjunct instructors with engineering backgrounds and limited pedagogy preparation, which makes Path A's pseudocode-and-diagrams approach harder to execute with consistent quality across sections; or the language-of-instruction-vs-language-mastery distinction Path B may underemphasize) from one that stays inside the pack's framing.

Engagement opportunity: the trade-off between the 62% non-continuing students (for whom completion-rate matters more than transferability) and the 38% continuing students (for whom transferability matters more than completion-rate) is a central evidence-base tension. The C5b engagement-with-source criterion can distinguish a recommendation that engages with this tension from one that elides it.

---

## Comparative validity note

Both V2 conditions (Condition A baseline and Condition B SPARRING) receive identical access to this pack. Comparative validity is preserved: any difference in output quality between the conditions is attributable to the deliberation methodology, not to differential access to materials.
