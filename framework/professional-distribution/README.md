# SPARRING Framework -- Professional Distribution

The polished, citation-ready document set for the SPARRING Framework. These are the artifacts to cite in papers, submit to academic venues, hand to peers, or ship as a release bundle PDF.

## Where this fits

The SPARRING Framework has two layers of documentation in this repository, and they serve different purposes:

| Layer | Location | Audience | Cadence |
|---|---|---|---|
| **Working notes** (continuously evolving) | [`../notes.md`](../notes.md), [`../reference-deployment.md`](../reference-deployment.md), [`../deployment-walkthrough.md`](../deployment-walkthrough.md), [`../decision-frameworks.md`](../decision-frameworks.md), [`../monte-carlo-design.md`](../monte-carlo-design.md) | Practitioners and contributors who want the live state of the framework, including in-flight refinements, failure-mode discoveries, and ongoing design decisions. | Updated as the framework evolves. |
| **Professional distribution** (versioned, citation-ready) | This directory. | Readers who want a frozen v1.0 reference -- academics evaluating the framework, peers reading it linearly, anyone citing it formally. | Updated only at versioned releases. |

Both layers are public; the difference is stability vs. currency. If you want to *cite* SPARRING in a paper, cite this directory at a tagged version. If you want to *use* SPARRING and stay current, read the working notes.

## Reading order

**New readers should start with the Reader's Guide.** It identifies the document set, the intended audiences, and recommends reading paths by reader role.

| Document | Purpose |
|---|---|
| [`sparring-readers-guide.md`](sparring-readers-guide.md) | Meta-navigation and reader's guide. Recommended entry point for any reader not yet certain which document fits their purpose. |
| [`sparring-specification.md`](sparring-specification.md) | Formal framework specification. Primary artifact for academic submission (arXiv preprint, workshop paper). |
| [`sparring-implementation-guide.md`](sparring-implementation-guide.md) | Reference architecture for deploying the framework as a CLI tool. Primary artifact for engineering practitioners. |
| [`refs.bib`](refs.bib) | BibTeX bibliography for citation processing during pandoc conversion. |

## Generating PDFs

All three documents convert cleanly to PDF via pandoc with `--citeproc` against `refs.bib`. Required apt packages on Debian/Ubuntu:

```
sudo apt-get install -y pandoc texlive-latex-base texlive-fonts-recommended texlive-latex-recommended texlive-latex-extra
```

Generation commands (run from the repo root):

```bash
DIR=framework/professional-distribution

pandoc "$DIR/sparring-readers-guide.md" \
    --citeproc \
    --bibliography="$DIR/refs.bib" \
    --metadata title="The SPARRING Framework: Reader's Guide" \
    --metadata author="Barton Niedner" \
    -o sparring-readers-guide.pdf

pandoc "$DIR/sparring-specification.md" \
    --citeproc \
    --bibliography="$DIR/refs.bib" \
    --metadata title="The SPARRING Framework: Specification" \
    --metadata author="Barton Niedner" \
    -o sparring-specification.pdf

pandoc "$DIR/sparring-implementation-guide.md" \
    --citeproc \
    --bibliography="$DIR/refs.bib" \
    --metadata title="The SPARRING Framework: Reference Implementation" \
    --metadata author="Barton Niedner" \
    -o sparring-implementation-guide.pdf
```

The same commands generate HTML or LaTeX by changing the output extension (`.html`, `.tex`).

## Versioning and provenance

Current version: 1.0 (preprint), 2026-04-30 / 2026-05-01.

These documents were derived from the working-notes layer cited above. Future versioned releases of this directory will carry their own version numbers and dates; the working notes will continue to evolve in between releases. Use a tagged version of this repo (e.g., a future `v1.0-pro-distribution-YYYY-MM-DD`) when citing the polished forms in any context where citation stability matters.

## License

All documents in this directory are licensed CC BY 4.0 (Creative Commons Attribution 4.0 International), matching the rest of the framework documentation in this repository. See the repository [`LICENSE`](../../LICENSE) for the full text. Free to share, adapt, and build upon, including for commercial purposes, with attribution to Barton Niedner / Resource Forge LLC.
