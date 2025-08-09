#!/usr/bin/env python3
import sys, json, re, pdfplumber, traceback

def extract(path):
    qualitative = []
    neg         = []
    pos         = []
    samp        = []

    try:
        with pdfplumber.open(path) as pdf:
            qual_page = None
            cr_page   = None

            # 1) Find pages containing our headings
            for idx, page in enumerate(pdf.pages):
                txt = page.extract_text() or ""
                if qual_page is None and "Qualitative Results" in txt:
                    qual_page = idx
                if cr_page   is None and "Combined Report" in txt:
                    cr_page = idx
                if qual_page is not None and cr_page is not None:
                    break

            # 2) Extract Qualitative block
            if qual_page is not None:
                saw = False
                for ln in (pdf.pages[qual_page].extract_text() or "").splitlines():
                    if "Qualitative Results" in ln:
                        saw = True
                        continue
                    if saw:
                        if not ln.strip() or "Combined Report" in ln:
                            break
                        qualitative.append(ln.strip())

            # 3) Extract *all* Combined Report rows from cr_page → end
            if cr_page is None:
                raise RuntimeError("Combined Report not found")

            sawCR = False
            for page in pdf.pages[cr_page:]:
                for ln in (page.extract_text() or "").splitlines():
                    line = ln.strip()
                    # start when Combined Report first seen
                    if not sawCR:
                        if "Combined Report" in line:
                            sawCR = True
                        continue

                    # skip noise
                    if (
                        not line
                        or line.startswith("O.D.")
                        or line.startswith("Patient ID")
                        or line.startswith("Value")
                        or line.startswith("Ratio")
                        or line.startswith("Result")
                    ):
                        continue

                    # split on any whitespace
                    toks = re.split(r"\s+", line)
                    if len(toks) < 6:
                        continue

                    # locate the two numeric fields
                    num_idxs = [i for i,t in enumerate(toks) if re.match(r"^\d+\.\d+$", t)]
                    if len(num_idxs) != 2:
                        continue
                    a,b = num_idxs  # a = ODValue index, b = Ratio index

                    pid   = toks[0]
                    num   = toks[1] if len(toks) > 1 else ""
                    well  = toks[2] if len(toks) > 2 else ""
                    flag  = " ".join(toks[3:a]) if a > 3 else ""
                    val   = toks[a]
                    ratio = toks[b]
                    res   = " ".join(toks[b+1:])

                    row = {
                        "Patient ID": pid,
                        "#":          num,
                        "Well":       well,
                        "Flag":       flag,
                        "ODValue":    val,
                        "Ratio":      ratio,
                        "Result":     res
                    }

                    if re.match(r"NC\d+", pid, re.I):
                        neg.append(row)
                    elif re.match(r"PC", pid, re.I):
                        pos.append(row)
                    elif re.match(r"\d", pid):
                        samp.append(row)

        return {
            "qualitative":       qualitative,
            "negative_controls": neg,
            "positive_controls": pos,
            "sample_results":    samp
        }

    except Exception:
        traceback.print_exc()
        sys.exit(1)


if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Usage: parse_res.py path/to/file.pdf", file=sys.stderr)
        sys.exit(1)
    out = extract(sys.argv[1])
    print(json.dumps(out, indent=2))
