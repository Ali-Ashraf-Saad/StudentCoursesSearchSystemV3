# Student Course Inquiry System with Analytics Dashboard

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
