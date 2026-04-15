# GBU Classroom Availability Finder

> Find a free classroom at Gautam Buddha University — instantly.

A real-time web app that scrapes live timetable data from [mygbu.in](https://mygbu.in) and shows which of the 85 classrooms across all schools are free or occupied for any given day and period.

![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?logo=php&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?logo=bootstrap&logoColor=white)
![PWA](https://img.shields.io/badge/PWA-Ready-5A0FC8?logo=pwa&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green)

---

## Features

- **Live data** — scrapes all 85 rooms in parallel using PHP cURL multi
- **Session cache** — repeat queries are served instantly without re-scraping
- **School filters** — SOICT · SOE · SOBT · SOVS/AS · Common
- **Sort & search** — A→Z / Z→A sort and live room name search
- **Period countdown timer** — shows time remaining in the current period
- **Find me a room** — one click to find a free room right now
- **All free slots** — modal showing every free period for any room today
- **Dark mode** — persisted via localStorage
- **Shareable URLs** — `?day=Monday&period=III` auto-triggers a search
- **Copy room name** — clipboard button on every card
- **Recent searches** — last 3 searches saved locally
- **Occupancy bar** — visual campus occupancy percentage
- **Print support** — clean print stylesheet
- **PWA** — installable on mobile and desktop

---

## Screenshots

<p align="center">
  <img src="https://github.com/user-attachments/assets/9edc7bfa-3fb0-422a-bf83-034610dc8c29" width="45%" />
  <img src="https://github.com/user-attachments/assets/b4f31939-26da-4e54-975d-91f0163d18e0" width="45%" />
</p>

<p align="center">
    <img src="https://github.com/user-attachments/assets/03786719-16b7-4813-8e6b-7e4a22df8a29" width="45%" />
    <img src="https://github.com/user-attachments/assets/5f47d4ad-c91b-4bcf-a19a-06a6f07c171a" width="45%" /> 
</p>

---

## Project Structure

```
├── index.php              # Frontend — HTML, CSS, JavaScript
├── check.php              # Backend — validation, scraping, parsing, caching
├── rooms.php              # Room registry and school classifier (source of truth)
├── rooms.json             # Static JSON copy of room registry
├── next_free.php          # Returns all free periods for a specific room
├── clear_cache.php        # Destroys the PHP session cache
├── manifest.json          # PWA manifest
├── sw.js                  # Service worker (self-destructing cache cleaner)
├── logo.png               # GBU logo
├── scan_rooms.py          # Utility: discover room IDs from mygbu.in
├── check_depts.py         # Utility: verify school prefix mappings
├── tests/
│   ├── test_check_validation.php   # Unit tests for validateInput()
│   ├── test_parse_room_html.php    # Unit tests for parseRoomHtml()
│   └── test_rooms.php              # Unit tests for getSchoolForRoom()
└── SRS_GBU_Classroom_Availability_Finder.md
```

---

## Requirements

- PHP 7.4+ with the following extensions enabled:
  - `curl`
  - `session`
- Outbound HTTPS access to `mygbu.in`
- Any web server (Apache, Nginx, or PHP's built-in server for local dev)

---

## Setup

### 1. Clone the repository

```bash
git clone https://github.com/<your-username>/gbu-classroom-finder.git
cd gbu-classroom-finder
```

### 2. Serve with PHP

```bash
php -S localhost:8000
```

Then open [http://localhost:8000](http://localhost:8000) in your browser.

### 3. Deploy to a server

Upload all files to your PHP-capable web host. No database or build step required.

---

## How It Works

1. User selects a **day** and **period** (or clicks "Use Current Time").
2. The frontend POSTs to `check.php`.
3. `check.php` checks the PHP session cache. On a miss, it fires 85 parallel cURL requests to `mygbu.in/schd/rindex.php?id=<room_id>`.
4. Each HTML response is parsed with regex to find the target day row and period cell.
5. Rooms are classified as **free** or **occupied** (with subject, section, and faculty).
6. Results are cached in the session and returned as JSON.
7. The frontend renders room cards, filter counts, and the occupancy bar.

---

## Room Coverage

| School | Prefixes | Rooms |
|--------|----------|-------|
| SOICT — School of ICT | IL, IP, IT | 20 |
| SOE — School of Engineering | EL, EP, ET | 18 |
| SOBT — School of Biotechnology | BTLab, BL, BT | 12 |
| SOVS/AS — Vocational & Applied Sciences | VL, VT, VP | 11 |
| Common — Law, Management, Humanities, etc. | LL, LH, BP, V | 24 |

**Total: 85 rooms**

---

## Period Schedule

| Period | Time |
|--------|------|
| I | 8:30 – 9:30 |
| II | 9:30 – 10:30 |
| III | 10:30 – 11:30 |
| IV | 11:30 – 12:30 |
| V | 12:30 – 1:30 |
| VI | 1:30 – 2:30 |
| VII | 2:30 – 3:30 |
| VIII | 3:30 – 4:30 |
| IX | 4:30 – 5:30 |
| X | 5:30 – 6:30 |

---

## Running Tests

```bash
php tests/test_check_validation.php
php tests/test_parse_room_html.php
php tests/test_rooms.php
```

All three suites exit with code `0` on success and `1` on failure.

| Suite | What it tests | Cases |
|-------|--------------|-------|
| `test_check_validation.php` | `validateInput()` — all valid combos + injection/edge cases | 70 |
| `test_parse_room_html.php` | `parseRoomHtml()` — free, occupied, malformed, edge cases | 8 |
| `test_rooms.php` | `getSchoolForRoom()` + all 85 rooms return valid schools | 175 |

---

## Utility Scripts

### Rediscover room IDs

If GBU adds new rooms, re-run the scanner:

```bash
python scan_rooms.py
```

This scans IDs 1–399 on `mygbu.in` concurrently and regenerates `rooms.json`.

### Verify school prefixes

```bash
python check_depts.py
```

Fetches a sample of rooms and prints the subject codes found, useful for validating prefix-to-school mappings.

---

## Clearing the Cache

Visit `/clear_cache.php` in your browser, or just open a new browser session. The cache is per-session and expires automatically when the session ends.

---

## Security Notes

- All user input is validated against strict whitelists before use.
- All output is escaped with `htmlspecialchars()`.
- Session cookies are set with `HttpOnly` and `SameSite=Strict`.
- Room URLs are built from hardcoded integer IDs only — no user input is interpolated into external requests.

---

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you'd like to change. Make sure all three test suites pass before submitting.

---

## Author

**Vaibhav Porwal**
Roll No. 235UCS122</br>
School of Information and Communication Technology</br>
Gautam Buddha University, Greater Noida</br>

---

## License

[MIT](https://choosealicense.com/licenses/mit/)
