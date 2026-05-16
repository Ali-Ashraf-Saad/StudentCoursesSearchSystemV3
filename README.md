# Student Course Inquiry System with Analytics Dashboard

> **🌐 Language:** [English](README.md) | [العربية](README_AR.md)

A complete web‑based system for students to look up their enrolled courses, exam schedules, and study materials, combined with a real‑time analytics dashboard for administrators. The system uses **PHP** with flat‑file JSON storage, live user tracking, and rich visualizations powered by **Plotly**.

---

## ✨ Features

### For Students (Main Interface – `index.php`)

- **Search** by student ID or name (Arabic name normalization & partial matching)
- Display enrolled courses with:
  - Course name and code
  - Exam committee, room, date, period, and time
  - Direct **Google Drive** links to course materials (where available)
- **Search history** (stored in browser’s localStorage) – click to re‑search
- **Export** student’s course list as a PNG image (using `html2canvas`)
- Real‑time visitor counter (based on `counter.php`)

### For Administrators / Staff

- **Hidden statistics panel** – click the developer name (“Ali Ashraf”) 10 times to unlock
- Real‑time metrics:
  - Currently active users (heartbeat every 3 seconds)
  - Number of users currently viewing the stats panel
  - Total page opens for the dashboard & stats panel
  - Total searches, searches today, average searches per hour / per user
  - Unique students searched, peak search hour, total image exports
- **Top 5 most searched students** table
- **Live search log** (last 50 queries with timestamp and IP)
- **All students search frequency** table with toggle to show more than 5 entries
- **Reset all statistics** button (clears counters, logs, and active users)

### Analytics Dashboard (`dashboard.html`)

- **Four key metrics** cards: Total Students, Unique Courses, Avg Courses/Student, Top Course
- **Interactive charts** (Plotly):
  - Top 15 enrolled courses (bar chart)
  - Course distribution (pie chart)
  - Courses per student histogram
  - Box plot of enrollment spread
- **Course popularity ranking** with animated horizontal bars
- Fully responsive, dark‑themed UI with modern design

### Real‑time User Tracking

- `active.php` – heartbeat endpoint (`client_id` stored in localStorage)
- Tracks two states:
  - Normal active users (timeout 20 seconds)
  - Users who have opened the statistics panel (`stats=1` flag)
- Used in both the main page and the dashboard to display live concurrent users

---

## 🛠️ Tech Stack

| Component       | Technology                                   |
|----------------|----------------------------------------------|
| Backend        | PHP (native, no frameworks)                  |
| Data storage   | JSON files (flat‑file database)              |
| Frontend       | HTML5, CSS3, Vanilla JavaScript              |
| Charts         | Plotly.js (v2.32.0)                          |
| Image export   | html2canvas                                  |
| Fonts          | Google Fonts (Cairo, Sora, DM Sans, DM Mono) |

---

## 📁 File Structure

```
.
├── index.php                   # Main search interface + hidden stats panel
├── dashboard.html              # Independent analytics dashboard
├── search.php                  # Search logic & statistics recording
├── active.php                  # Heartbeat & active users management
├── active_stats.php            # (Legacy) separate stats viewer tracking
├── counter.php                 # Simple visitor counter
├── dashboard_data.php          # Aggregates data for dashboard
├── dashboard_page_open.php     # Logs dashboard page opens
├── stats_open.php              # Logs hidden stats panel opens
├── export_counter.php          # Counts image export actions
├── reset_stats.php             # Resets all statistics (POST only)
├── favicon.ico / dashboard-icon.png
│
├── data/                       # All JSON data files (auto‑created)
│   ├── students.json           # Student records (see format below)
│   ├── courses.json            # Course catalogue
│   ├── exams.json              # Exam schedules per course/student
│   ├── rooms.json              # Room mappings (optional)
│   ├── active_users.json       # Live active users (auto‑managed)
│   ├── search_counts.json      # Per‑student search frequency
│   ├── search_log.json         # Detailed search log (max 1000 entries)
│   ├── dashboard_page_opens.txt
│   ├── dashboard_stats_opens.txt
│   └── export_count.txt
│
├── students.json               # (Deprecated?) root copy – not used by code
└── README.md                   # This file
```

> **Note:** The `data/` directory must be writable by the web server.

---

## 📦 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/student-course-inquiry.git
   cd student-course-inquiry
   ```

2. **Set up a web server** (Apache / Nginx / XAMPP / WAMP) with PHP 7.4+.

3. **Configure permissions** – ensure the web server can write to the `data/` directory:
   ```bash
   chmod 755 data
   chmod 666 data/*.json data/*.txt
   ```

4. **Prepare the JSON data files** inside the `data/` folder.  
   See the **Data Format** section below for examples.

5. **Access the application**:
   - Main page: `http://your-server/index.php`
   - Dashboard: `http://your-server/dashboard.html`

---

## 📄 Data Format

All files are stored in `data/` as UTF‑8 JSON.

### `students.json`
```json
[
  {
    "id": "20210001",
    "name": "أحمد محمد",
    "department": "CS",
    "courses": ["CS438", "IS381", "CS424"]
  }
]
```

### `courses.json`
```json
{
  "CS438": { "name": "شبكات الحاسوب المتقدمة" },
  "IS381": { "name": "أمن المعلومات" }
}
```

### `exams.json`
```json
[
  {
    "course": "CS438",
    "committee": "A",
    "room": "B102",
    "day": "الاثنين",
    "date": "15/05/2026",
    "period": "الفترة الأولى",
    "time": "09:00 – 12:00",
    "students": ["20210001", "20210002"]
  }
]
```

### `rooms.json` (optional, not actively used)
```json
{
  "B102": "مبنى B - قاعة 102"
}
```

The system will create the other files (`active_users.json`, `search_log.json`, etc.) automatically when first needed.

---

## 🔧 Configuration

You can adjust timeouts, file paths, and Google Drive links directly inside the PHP files.

| File           | Setting                     | Default |
|----------------|-----------------------------|---------|
| `active.php`   | `$timeout` (seconds)        | 20      |
| `search.php`   | `$MAX_RESULTS`              | 20      |
| `search.php`   | Google Drive `$driveLinks`  | Array of course codes → URLs |
| `dashboard_data.php` | `$timeout` for active users | 60      |

---

## 🚀 Usage Guide

### Normal User (Student / Visitor)

1. Open `index.php`.
2. Type a student **ID** or **name** (Arabic) in the search box.
3. Results appear automatically with courses, exam details, and drive links.
4. Click the camera button to download an image of the student’s courses.
5. Recent searches are saved in the history panel – click any to repeat.

### Access the Hidden Statistics Panel

1. On `index.php`, scroll to the footer.
2. **Click on “Ali Ashraf” (developer name) 10 times**.
3. The stats panel will slide down with live data.
4. All users who open this panel are counted separately (`stats_active_users`).
5. Use the **“🗑️ تصفير جميع الإحصائيات”** button to reset all counters and logs.

### Analytics Dashboard

- Open `dashboard.html` (or click the floating dashboard button on `index.php`).
- The dashboard shows high‑level course enrollment analytics based on `students.json` and `courses.json`.
- Charts are interactive – hover, zoom, pan.
- Data refreshes on page reload (no auto‑refresh in this version).

---

## 🔄 API Endpoints (for developers)

| Endpoint                     | Method | Description                                                                 |
|------------------------------|--------|-----------------------------------------------------------------------------|
| `search.php?q=<query>&commit=1&client_id=<id>` | GET | Returns matching students. `commit=1` logs the search (increment counter, log, search_counts). |
| `active.php?client_id=<id>[&stats=1/0]`       | GET | Registers heartbeat. `stats=1` marks user as viewing stats panel.          |
| `counter.php?action=increment`               | GET   | Increments and returns total visitor count.                                |
| `dashboard_data.php`                         | GET   | Returns aggregated statistics for the dashboard.                           |
| `reset_stats.php`                            | POST  | Resets all counters and logs (requires POST).                              |
| `export_counter.php`                         | GET   | Increments export image counter.                                           |
| `dashboard_page_open.php` / `stats_open.php` | GET   | Increments respective open counters.                                       |

All responses are `application/json`.

---

## 🌟 Known Limitations & Future Improvements

- **JSON file locking** – concurrent writes may cause race conditions (mitigated with `flock` in `active.php`, but not everywhere).
- **Search performance** – linear scan over `students.json`. For >10k students, consider a database.
- **Real‑time updates** – Dashboard uses polling (`setInterval` 2 seconds). Could be replaced with WebSockets.
- **Dashboard charts** – do not auto‑refresh; requires manual page reload.

Possible improvements:
- Add database support (MySQL/PostgreSQL)
- User authentication for admin panel
- Export dashboard charts as PDF/PNG
- Multi‑language support (English/Arabic toggle)

---

## 👤 Author

Developed by **Ali Ashraf**  
📧 [Your email] – 🌐 [Your website / GitHub]

---

## 📄 License

This project is open‑source and available under the **MIT License**.  
Feel free to use, modify, and distribute with appropriate credit.

---

## 🙏 Acknowledgements

- [Plotly.js](https://plotly.com/javascript/) for beautiful charts
- [html2canvas](https://html2canvas.hertzen.com/) for image exports
- Google Fonts (Cairo, Sora, DM Sans, DM Mono)

---
