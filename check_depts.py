import urllib.request
import re

ids = {214: "EL201", 258: "BL201", 181: "VL201", 218: "LL101", 278: "LH101", 232: "EP101", 265: "VP102", 257: "IT201"}

for rid, rname in ids.items():
    req = urllib.request.Request(f"https://mygbu.in/schd/rindex.php?id={rid}", headers={"User-Agent": "Mozilla/5.0"})
    with urllib.request.urlopen(req, timeout=10) as resp:
        html = resp.read().decode("utf-8", errors="ignore")
    subjects = set(re.findall(r'([A-Z]{2,4}\d{3})\(', html))
    print(f"Room {rname} (ID={rid}): {sorted(subjects)}")
