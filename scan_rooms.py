import urllib.request
import re
import json
from concurrent.futures import ThreadPoolExecutor, as_completed

BASE_URL = "https://mygbu.in/schd/rindex.php?id={}"
PATTERN = re.compile(r"<h3><a[^>]+>([^<]+)</a></h3>")

def check_room(i):
    try:
        req = urllib.request.Request(BASE_URL.format(i), headers={"User-Agent": "Mozilla/5.0"})
        with urllib.request.urlopen(req, timeout=10) as resp:
            html = resp.read().decode("utf-8", errors="ignore")
        m = PATTERN.search(html)
        if m:
            name = m.group(1).strip()
            if "University Coordinator" not in name and name:
                return (i, name)
    except:
        pass
    return None

rooms = {}
with ThreadPoolExecutor(max_workers=20) as ex:
    futures = {ex.submit(check_room, i): i for i in range(1, 400)}
    done = 0
    for f in as_completed(futures):
        done += 1
        result = f.result()
        if result:
            rooms[result[0]] = result[1]
            print(f"ID={result[0]} : {result[1]}")
        if done % 50 == 0:
            print(f"Progress: {done}/399")

# Sort and save
sorted_rooms = dict(sorted(rooms.items()))
with open("rooms.json", "w") as f:
    json.dump(sorted_rooms, f, indent=2)

print(f"\nTotal rooms found: {len(sorted_rooms)}")
for k, v in sorted_rooms.items():
    print(f"  ID={k} : {v}")
