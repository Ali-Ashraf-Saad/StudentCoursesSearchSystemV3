<?php
date_default_timezone_set('Africa/Cairo');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Wed, 11 Jan 1984 05:00:00 GMT");
?>
<!doctype html>
<html lang="ar">
  <head>
    <link rel="icon" href="/favicon.ico?v=2">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>الاستعلام عن المقررات الدراسية</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
      @import url("https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap");

      * { box-sizing: border-box; }

      body {
        font-family: "Cairo", sans-serif;
        background: linear-gradient(135deg, #0f172a, #020617);
        color: #fff;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
      }

      .container { max-width: 700px; margin: auto; padding: 40px 20px; flex: 1; }

      h1 { text-align: center; margin-bottom: 30px; font-weight: 600; }

      .counter { text-align: center; margin-bottom: 20px; color: #94a3b8; font-size: 14px; }

      /* ── حقل البحث ── */
      .search-box { position: relative; }

      input {
        width: 100%;
        padding: 15px 20px 15px 45px;
        border-radius: 15px;
        border: none;
        outline: none;
        font-size: 16px;
        font-family: "Cairo", sans-serif;
        background: #1e293b;
        color: white;
        transition: box-shadow 0.3s;
      }
      input:focus { box-shadow: 0 0 15px #3b82f6; }

      .clear-search-btn {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #94a3b8;
        font-size: 22px;
        cursor: pointer;
        padding: 0 4px;
        line-height: 1;
        transition: color 0.2s;
        display: none;
      }
      .clear-search-btn:hover { color: #f87171; }

      /* ── سجل البحث ── */
      .history {
        margin-top: 12px;
        background: rgba(30, 41, 59, 0.6);
        border-radius: 12px;
        padding: 10px;
        display: none;
      }
      .history-label { font-size: 13px; color: #94a3b8; margin-bottom: 8px; padding: 0 8px; }
      .history-list { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 8px; }
      .history-item {
        background: #0f172a;
        border-radius: 8px;
        padding: 4px 10px;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: background 0.2s;
      }
      .history-item:hover { background: #1e3a5f; }
      .history-text {
        color: #e2e8f0;
        font-size: 14px;
        max-width: 120px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
      }
      .history-delete {
        background: none;
        border: none;
        color: #94a3b8;
        font-size: 16px;
        cursor: pointer;
        padding: 0;
        line-height: 1;
        transition: color 0.2s;
      }
      .history-delete:hover { color: #f87171; }
      .history-clear {
        text-align: left;
        color: #f59e0b;
        font-size: 13px;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 6px;
        transition: background 0.2s;
      }
      .history-clear:hover { background: rgba(245, 158, 11, 0.1); }

      /* ── النتائج ── */
      .results { margin-top: 30px; }

      .card {
        background: rgba(30, 41, 59, 0.8);
        padding: 20px;
        border-radius: 20px;
        margin-bottom: 20px;
        animation: fadeIn 0.4s ease;
        transition: transform 0.3s, box-shadow 0.3s;
      }
      .card:hover {
        transform: translateY(-5px) scale(1.01);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
      }

      .name { font-size: 18px; font-weight: 600; }
      .number { color: #94a3b8; margin: 5px 0; }
      .search-count { color: #fbbf24; font-size: 14px; margin: 4px 0; }

      .course-item {
        background: #0f172a;
        border-radius: 12px;
        padding: 12px;
        margin: 10px 0;
        border-right: 4px solid #3b82f6;
      }
      .course-name { font-weight: 600; margin-bottom: 6px; }
      .exam-details {
        font-size: 13px;
        color: #cbd5e1;
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
      }
      .exam-details span { white-space: nowrap; }
      .no-exam { color: #f59e0b; font-style: italic; }
      .no-result { text-align: center; color: #94a3b8; margin-top: 20px; }

      /* ── زر التصدير ── */
      .export-btn-container { display: none; text-align: center; margin: 20px 0; }
      .export-btn {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 50px;
        font-size: 16px;
        font-weight: 600;
        font-family: "Cairo", sans-serif;
        cursor: pointer;
        transition: transform 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
        display: inline-flex;
        align-items: center;
        gap: 8px;
      }
      .export-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.6);
      }

      /* ── بطاقة التصدير (مخفية خارج الشاشة) ── */
      #export-card {
        position: absolute;
        left: -9999px;
        top: -9999px;
        width: 600px;
        padding: 30px;
        background: linear-gradient(145deg, #0b1120 0%, #1a1f2f 100%);
        border-radius: 24px;
        color: #e2e8f0;
        font-family: "Cairo", sans-serif;
        direction: rtl;
        border: 2px solid rgba(59, 130, 246, 0.4);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
      }
      #export-card .export-header {
        text-align: center;
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 2px solid rgba(59, 130, 246, 0.3);
      }
      #export-card .export-name {
        font-size: 26px;
        font-weight: 700;
        background: linear-gradient(to left, #60a5fa, #a78bfa);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 5px;
      }
      #export-card .export-number { font-size: 16px; color: #94a3b8; }
      #export-card .export-course {
        background: rgba(15, 23, 42, 0.8);
        border-radius: 14px;
        padding: 15px;
        margin: 12px 0;
        border-right: 5px solid #3b82f6;
      }
      #export-card .export-course-name {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 8px;
        color: #f1f5f9;
      }
      #export-card .export-exam-row {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        font-size: 14px;
        color: #cbd5e1;
      }
      #export-card .export-exam-row span {
        background: rgba(59, 130, 246, 0.15);
        padding: 4px 12px;
        border-radius: 20px;
        white-space: nowrap;
      }
      #export-card .no-exam-export { color: #f59e0b; font-style: italic; font-size: 13px; margin-top: 5px; }
      #export-card .watermark { text-align: center; margin-top: 20px; font-size: 12px; color: #475569; }

      /* ── Footer ── */
      footer {
        text-align: center;
        padding: 15px;
        background: rgba(15, 23, 42, 0.9);
        color: #94a3b8;
        font-size: 14px;
        border-top: 1px solid rgba(148, 163, 184, 0.2);
        backdrop-filter: blur(10px);
      }

      /* ── زر Dashboard ── */
      .dashboard-btn {
        position: fixed;
        top: 20px;
        left: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #16537e, #cfe2f3);
        color: #000;
        border: none;
        padding: 10px 14px;
        border-radius: 12px;
        font-size: 14px;
        cursor: pointer;
        box-shadow: 0 0 15px rgba(0, 200, 255, 0.4);
        transition: all 0.3s ease;
        overflow: hidden;
        z-index: 999;
      }
      .dashboard-btn .text { opacity: 0; max-width: 0; transition: all 0.3s ease; white-space: nowrap; }
      .dashboard-btn:hover { padding: 10px 18px; transform: translateY(-2px) scale(1.05); box-shadow: 0 0 25px rgba(0, 200, 255, 0.7); }
      .dashboard-btn:hover .text { opacity: 1; max-width: 120px; }
      .dashboard-btn .icon { width: 25px; height: 25px; margin-right: 8px; }

      /* ── لوحة الإحصائيات ── */
      .stats-panel {
        position: relative;
        display: none;
        background: rgba(15, 23, 42, 0.95);
        border: 1px solid #3b82f6;
        border-radius: 20px;
        padding: 25px;
        margin: 20px auto;
        max-width: 1000px;
        backdrop-filter: blur(15px);
        animation: fadeIn 0.5s ease;
      }
      .stats-panel h2 { text-align: center; color: #60a5fa; margin-bottom: 25px; }
      .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
      }
      .stat-box {
        background: #1e293b;
        border-radius: 12px;
        padding: 15px;
        text-align: center;
        border: 1px solid #334155;
      }
      .stat-box .stat-value { font-size: 24px; font-weight: 700; color: #fbbf24; }
      .stat-box .stat-label { font-size: 12px; color: #94a3b8; margin-top: 5px; }

      .table-container { overflow-x: auto; margin-bottom: 20px; }
      table { width: 100%; border-collapse: collapse; background: #1e293b; border-radius: 12px; overflow: hidden; }
      th { background: #0f172a; color: #60a5fa; padding: 10px; font-size: 14px; }
      td { padding: 8px 10px; font-size: 13px; border-bottom: 1px solid #334155; text-align: center; }

      .toggle-buttons { text-align: center; margin: 10px 0; }
      .toggle-buttons button {
        background: #334155;
        color: white;
        border: none;
        padding: 6px 16px;
        border-radius: 8px;
        margin: 0 5px;
        cursor: pointer;
        font-size: 14px;
        font-family: "Cairo", sans-serif;
      }
      .toggle-buttons button:hover { background: #475569; }

      .close-stats-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #dc2626;
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        font-size: 18px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .panel-buttons { text-align: center; margin: 15px 0 5px; }

      #dev-name { cursor: default; }
      .hidden { display: none; }

      @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
      }
    </style>
  </head>
  <body>

    <button class="dashboard-btn" id="dashboard-page-btn">
      <img src="dashboard-icon.png" class="icon" alt="dashboard">
      <span class="text">Dashboard</span>
    </button>

    <div class="container">
      <h1>الاستعلام عن المقررات الدراسية</h1>
      <div class="counter">عدد الزوار: <span id="visitCount">...</span></div>

      <div class="search-box">
        <input type="text" id="search" placeholder="اكتب الاسم أو الرقم الأكاديمي..." autocomplete="on" />
        <button type="button" id="clear-search-btn" class="clear-search-btn" title="مسح النص">×</button>
      </div>

      <div class="history" id="history">
        <div class="history-label">سجل البحث:</div>
        <div class="history-list" id="history-list"></div>
        <div class="history-clear" id="clear-history">مسح السجل</div>
      </div>

      <div class="export-btn-container" id="export-container">
        <button class="export-btn" onclick="exportAsImage()">📸 تحميل صورة المواد</button>
      </div>

      <div class="results" id="results"></div>

      <div class="stats-panel" id="stats-panel">
        <button class="close-stats-btn" id="close-stats-btn" title="إخفاء اللوحة">×</button>
        <h2>📊 لوحة الإحصائيات</h2>
        <div class="stats-grid" id="stats-grid"></div>

        <h3>🔝 أكثر الطلاب بحثاً (أعلى 5)</h3>
        <div class="table-container">
          <table>
            <thead><tr><th>الرقم الأكاديمي</th><th>الاسم</th><th>عدد مرات البحث</th></tr></thead>
            <tbody id="top-tbody"></tbody>
          </table>
        </div>

        <h3>📋 آخر عمليات البحث</h3>
        <div class="table-container">
          <table>
            <thead><tr><th>الاستعلام</th><th>التوقيت</th><th>IP</th></tr></thead>
            <tbody id="log-tbody"></tbody>
          </table>
        </div>

        <h3>📈 جميع الطلاب وعدد مرات البحث</h3>
        <div id="all-toggle-buttons" class="toggle-buttons hidden">
          <button id="show-all-btn">إظهار الكل</button>
          <button id="hide-all-btn" class="hidden">إخفاء (أول 5)</button>
        </div>
        <div class="table-container">
          <table>
            <thead><tr><th>الرقم الأكاديمي</th><th>الاسم</th><th>عدد مرات البحث</th></tr></thead>
            <tbody id="all-tbody"></tbody>
          </table>
        </div>

        <div class="panel-buttons">
          <button class="export-btn" id="reset-stats-btn" style="background:#dc2626;">🗑️ تصفير جميع الإحصائيات</button>
        </div>
      </div>
    </div>

    <div id="export-card"></div>

    <footer>© 2026 StudentsCoursesFinals V3 · Developed by <span id="dev-name">Ali Ashraf</span></footer>

    <script>
      // ═══════════════════════════════════════════
      //  المتغيرات العامة
      // ═══════════════════════════════════════════
      const HISTORY_KEY   = 'search_history';
      const MAX_HISTORY   = 10;

      let debounceTimer        = null;
      let currentStudentData   = null;
      let statsModeActive      = false;
      let statsRefreshInterval = null;
      let statsHeartbeatInterval = null;
      let commitBlocked        = false;
      let committedQuery       = '';
      let autoCommitTimer      = null;
      let showAllStudents      = false;
      let prevStatsData        = null;
      let liveActiveUsers      = null;
      let liveStatsActiveUsers = null;

      // متغير الـ polling لكشف تغيير قيمة الحقل (بما فيه الإكمال التلقائي)
      let lastPolledValue = '';

      // ── العناصر ──
      const searchInput     = document.getElementById('search');
      const clearBtn        = document.getElementById('clear-search-btn');
      const resultsDiv      = document.getElementById('results');
      const exportContainer = document.getElementById('export-container');

      // ═══════════════════════════════════════════
      //  Client ID
      // ═══════════════════════════════════════════
      function getClientId() {
        let id = localStorage.getItem('client_id');
        if (!id) {
          id = 'client_' + Math.random().toString(36).substr(2, 9) + Date.now();
          localStorage.setItem('client_id', id);
        }
        return id;
      }
      const CLIENT_ID = getClientId();

      // ═══════════════════════════════════════════
      //  النبضة الحية (Heartbeat)
      // ═══════════════════════════════════════════
      function applyLiveCounters(data) {
        if (data && Object.prototype.hasOwnProperty.call(data, 'active_count')) {
          liveActiveUsers = data.active_count;
          const el = document.getElementById('stat-active_users');
          if (el) el.textContent = data.active_count;
        }
        if (data && Object.prototype.hasOwnProperty.call(data, 'stats_active_count')) {
          liveStatsActiveUsers = data.stats_active_count;
          const el = document.getElementById('stat-stats_active_users');
          if (el) el.textContent = data.stats_active_count;
        }
      }

      function sendHeartbeat() {
        return fetch(`active.php?client_id=${encodeURIComponent(CLIENT_ID)}`)
          .then(r => r.json())
          .then(applyLiveCounters)
          .catch(() => {});
      }
      sendHeartbeat();
      setInterval(sendHeartbeat, 3000);

      // ═══════════════════════════════════════════
      //  سجل البحث
      // ═══════════════════════════════════════════
      function loadHistory()     { return JSON.parse(localStorage.getItem(HISTORY_KEY) || '[]'); }
      function saveHistory(h)    { localStorage.setItem(HISTORY_KEY, JSON.stringify(h)); }

      function addToHistory(query) {
        if (!query) return;
        let h = loadHistory().filter(i => i !== query);
        h.unshift(query);
        if (h.length > MAX_HISTORY) h.pop();
        saveHistory(h);
        renderHistory();
      }

      function deleteHistoryItem(query) {
        saveHistory(loadHistory().filter(i => i !== query));
        renderHistory();
      }

      function clearHistory() {
        localStorage.removeItem(HISTORY_KEY);
        renderHistory();
      }

      function renderHistory() {
        const container = document.getElementById('history-list');
        const wrapper   = document.getElementById('history');
        const history   = loadHistory();
        if (!history.length) { wrapper.style.display = 'none'; return; }
        wrapper.style.display = 'block';
        container.innerHTML = history.map(item =>
          `<div class="history-item">
            <span class="history-text" data-query="${item}">${item}</span>
            <button class="history-delete" data-query="${item}">×</button>
          </div>`
        ).join('');
        container.querySelectorAll('.history-text').forEach(el => {
          el.onclick = () => {
            const q = el.dataset.query;
            searchInput.value = q;
            lastPolledValue   = q;
            clearBtn.style.display = 'block';
            doCommitSearch(q);
          };
        });
        container.querySelectorAll('.history-delete').forEach(btn => {
          btn.onclick = e => { e.stopPropagation(); deleteHistoryItem(btn.dataset.query); };
        });
      }

      document.addEventListener('click', e => { if (e.target?.id === 'clear-history') clearHistory(); });

      // ═══════════════════════════════════════════
      //  عداد الزوار
      // ═══════════════════════════════════════════
      function loadCounter() {
        fetch('counter.php?action=get')
          .then(r => r.json())
          .then(d => { document.getElementById('visitCount').innerText = d.count ?? 0; })
          .catch(() => { document.getElementById('visitCount').innerText = '—'; });
      }
      loadCounter();
      setInterval(loadCounter, 5000);

      // ═══════════════════════════════════════════
      //  منطق البحث
      // ═══════════════════════════════════════════
      function doLiveSearch(query) {
        if (!query) {
          resultsDiv.innerHTML = '';
          exportContainer.style.display = 'none';
          currentStudentData = null;
          return;
        }
        fetch(`search.php?q=${encodeURIComponent(query)}`)
          .then(r => r.json())
          .then(data => {
            renderResults(data);
            clearAutoCommit();
            if (data.results.length >= 1 && data.results.length <= 4) {
              autoCommitTimer = setTimeout(() => {
                const cur = searchInput.value.trim();
                if (cur && cur === query && cur !== committedQuery) doCommitSearch(cur);
              }, 4000);
            }
          });
      }

      function doCommitSearch(query) {
        if (!query || commitBlocked || query === committedQuery) return;
        commitBlocked = true;
        fetch(`search.php?q=${encodeURIComponent(query)}&commit=1&client_id=${encodeURIComponent(CLIENT_ID)}`)
          .then(r => r.json())
          .then(data => {
            renderResults(data);
            addToHistory(query);
            committedQuery = query;
            loadCounter();
            clearAutoCommit();
            setTimeout(() => { commitBlocked = false; }, 500);
          })
          .catch(() => { commitBlocked = false; clearAutoCommit(); });
      }

      function clearAutoCommit() {
        if (autoCommitTimer) { clearTimeout(autoCommitTimer); autoCommitTimer = null; }
      }

      // ═══════════════════════════════════════════
      //  أحداث حقل البحث
      // ═══════════════════════════════════════════

      // Polling كل 200ms — يكتشف الإكمال التلقائي وأي تغيير آخر
      setInterval(() => {
        const val = searchInput.value.trim();
        if (val === lastPolledValue) return;
        lastPolledValue = val;
        clearBtn.style.display = val ? 'block' : 'none';
        clearTimeout(debounceTimer);
        clearAutoCommit();
        if (val === '') committedQuery = '';
        debounceTimer = setTimeout(() => doLiveSearch(val), 300);
      }, 200);

      // زر مسح النص
      clearBtn.addEventListener('click', () => {
        searchInput.value = '';
        lastPolledValue   = '';
        clearBtn.style.display = 'none';
        clearTimeout(debounceTimer);
        clearAutoCommit();
        committedQuery = '';
        resultsDiv.innerHTML = '';
        exportContainer.style.display = 'none';
        currentStudentData = null;
        searchInput.focus();
      });

      // Enter
      searchInput.addEventListener('keydown', e => {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        clearAutoCommit();
        const val = searchInput.value.trim();
        if (val && val !== committedQuery) { doCommitSearch(val); searchInput.blur(); }
      });

      // Blur
      searchInput.addEventListener('blur', () => {
        clearAutoCommit();
        const val = searchInput.value.trim();
        if (!val || commitBlocked || val === committedQuery) return;
        doCommitSearch(val);
      });

      // ═══════════════════════════════════════════
      //  عرض النتائج
      // ═══════════════════════════════════════════
      function renderResults(data) {
        resultsDiv.innerHTML = '';
        if (!data.results.length) {
          resultsDiv.innerHTML = `<div class="no-result">لا يوجد نتائج</div>`;
          exportContainer.style.display = 'none';
          currentStudentData = null;
          return;
        }
        currentStudentData = data.results[0];
        const fragment = document.createDocumentFragment();
        data.results.forEach(item => {
          const card = document.createElement('div');
          card.className = 'card';
          const coursesHtml = item.courses.map(course => {
            const examHtml = course.exam
              ? `<div class="exam-details">
                   <span>🔢 لجنة ${course.exam.committee}</span>
                   <span>📍 ${course.exam.room}</span>
                   <span>📅 ${course.exam.day} ${course.exam.date}</span>
                   <span>🕒 ${course.exam.period} (${course.exam.time})</span>
                 </div>`
              : `<div class="no-exam">لم تحدد اللجنة بعد</div>`;
            const titleHtml = course.driveLink
              ? `<a href="${course.driveLink}" target="_blank" style="color:#60a5fa;text-decoration:none;">📘 ${course.name} (${course.code})</a>`
              : `📘 ${course.name} (${course.code})`;
            return `<div class="course-item"><div class="course-name">${titleHtml}</div>${examHtml}</div>`;
          }).join('') || `<div>لا توجد مواد مسجلة</div>`;

          const searchCountHtml = `<div class="search-count ${statsModeActive ? '' : 'hidden'}">🔍 تم البحث عنه ${item.search_count || 0} مرة</div>`;

          card.innerHTML = `
            <div class="name">${item.name}</div>
            <div class="number">الرقم: ${item.number}</div>
            ${searchCountHtml}
            <div>عدد المواد: ${item.courses.length}</div>
            ${coursesHtml}`;
          fragment.appendChild(card);
        });
        resultsDiv.appendChild(fragment);
        exportContainer.style.display = 'block';
      }

      // ═══════════════════════════════════════════
      //  تصدير صورة
      // ═══════════════════════════════════════════
      function exportAsImage() {
        if (!currentStudentData) return;
        fetch('export_counter.php');
        const card = document.getElementById('export-card');
        const s    = currentStudentData;
        const coursesHtml = s.courses.map(c => {
          const examHtml = c.exam
            ? `<div class="export-exam-row">
                 <span>🔢 لجنة ${c.exam.committee}</span>
                 <span>📍 ${c.exam.room}</span>
                 <span>📅 ${c.exam.day} ${c.exam.date}</span>
                 <span>🕒 ${c.exam.period} (${c.exam.time})</span>
               </div>`
            : `<div class="no-exam-export">لم تحدد اللجنة بعد</div>`;
          return `<div class="export-course"><div class="export-course-name">📘 ${c.name} (${c.code})</div>${examHtml}</div>`;
        }).join('');
        card.innerHTML = `
          <div class="export-header">
            <div class="export-name">${s.name}</div>
            <div class="export-number">الرقم الأكاديمي: ${s.number}</div>
            <div style="color:#94a3b8;margin-top:5px;">عدد المواد: ${s.courses.length}</div>
          </div>
          ${coursesHtml || '<div style="text-align:center;">لا توجد مواد</div>'}
          <div class="watermark">تم إنشاؤه بواسطة نظام الاستعلام عن المقررات</div>`;
        html2canvas(card, { backgroundColor: null, scale: 2, useCORS: true, allowTaint: true })
          .then(canvas => {
            const a = document.createElement('a');
            a.download = `student_${s.number}_courses.png`;
            a.href = canvas.toDataURL('image/png');
            a.click();
          })
          .catch(() => alert('فشل التصدير'));
      }

      // ═══════════════════════════════════════════
      //  زر Dashboard
      // ═══════════════════════════════════════════
      document.getElementById('dashboard-page-btn').addEventListener('click', e => {
        e.preventDefault();
        fetch('dashboard_page_open.php')
          .finally(() => { window.location.href = 'dashboard.html'; });
      });

      // ═══════════════════════════════════════════
      //  لوحة الإحصائيات (10 ضغطات على الاسم)
      // ═══════════════════════════════════════════
      let clickCount = 0;
      document.getElementById('dev-name').addEventListener('click', () => {
        if (++clickCount >= 10) {
          clickCount = 0;
          statsModeActive = true;
          document.querySelectorAll('.search-count').forEach(el => el.classList.remove('hidden'));
          fetch('stats_open.php');
          showStatsPanel();
        }
      });

      function showStatsPanel() {
        document.getElementById('stats-panel').style.display = 'block';
        sendStatsHeartbeat().then(() => fetchStatsData(true));

        if (statsRefreshInterval)   clearInterval(statsRefreshInterval);
        if (statsHeartbeatInterval) clearInterval(statsHeartbeatInterval);
        statsRefreshInterval   = setInterval(() => fetchStatsData(false), 2000);
        statsHeartbeatInterval = setInterval(sendStatsHeartbeat, 5000);

        document.getElementById('reset-stats-btn').onclick = resetAllStats;
        document.getElementById('show-all-btn').onclick    = () => { showAllStudents = true;  updateAllStudentsTable(window.lastStatsData); };
        document.getElementById('hide-all-btn').onclick    = () => { showAllStudents = false; updateAllStudentsTable(window.lastStatsData); };
        document.getElementById('close-stats-btn').onclick = hideStatsPanel;
      }

      function hideStatsPanel() {
        document.getElementById('stats-panel').style.display = 'none';
        if (statsRefreshInterval)   { clearInterval(statsRefreshInterval);   statsRefreshInterval   = null; }
        if (statsHeartbeatInterval) { clearInterval(statsHeartbeatInterval); statsHeartbeatInterval = null; }
        fetch(`active.php?client_id=${encodeURIComponent(CLIENT_ID)}&stats=0`)
          .then(r => r.json()).then(applyLiveCounters).catch(() => {});
      }

      function sendStatsHeartbeat() {
        return fetch(`active.php?client_id=${encodeURIComponent(CLIENT_ID)}&stats=1`)
          .then(r => r.json()).then(applyLiveCounters).catch(() => {});
      }

      function fetchStatsData(build = false) {
        fetch('dashboard_data.php')
          .then(r => r.json())
          .then(data => {
            window.lastStatsData = data;
            if (build || !prevStatsData) {
              buildStatsGrid(data);
              buildTopStudents(data);
              buildSearchLog(data);
              buildAllStudentsTable(data);
            } else {
              updateStatsGrid(data);
              updateTopStudents(data);
              updateSearchLog(data);
              updateAllStudentsTable(data);
            }
            prevStatsData = JSON.parse(JSON.stringify(data));
          });
      }

      // ── بناء شبكة الإحصائيات ──
      function buildStatsGrid(data) {
        const grid = document.getElementById('stats-grid');
        grid.innerHTML = '';
        const stats = [
          { id: 'active_users',       label: '👥 متصلون الآن',               value: liveActiveUsers      ?? data.active_users },
          { id: 'stats_active_users', label: '📊 فاتحو الإحصائيات الآن',     value: liveStatsActiveUsers ?? data.stats_active_users },
          { id: 'page_opens',         label: '📄 فتح صفحة Dashboard',         value: data.page_opens },
          { id: 'stats_opens',        label: '🔓 فتح اللوحة المخفية',         value: data.stats_opens },
          { id: 'total_searches',     label: '🔍 إجمالي البحوث',              value: data.total_searches },
          { id: 'today_searches',     label: '📅 بحوث اليوم',                 value: data.today_searches },
          { id: 'avg_per_hour',       label: '⏱ متوسط/ساعة (24س)',           value: data.avg_per_hour },
          { id: 'unique_students',    label: '👨‍🎓 طلاب فريدين',              value: data.unique_students },
          { id: 'peak_hour',          label: '⏰ ساعة الذروة',                value: data.peak_hour },
          { id: 'avg_per_user',       label: '👤 متوسط البحوث/مستخدم',       value: data.avg_per_user },
          { id: 'export_count',       label: '📸 تحميل الصور',               value: data.export_count },
          { id: 'last_search',        label: '🕒 آخر بحث',                   value: data.last_search },
        ];
        stats.forEach(s => {
          const box = document.createElement('div');
          box.className = 'stat-box';
          box.innerHTML = `<div class="stat-value" id="stat-${s.id}">${s.value}</div><div class="stat-label">${s.label}</div>`;
          grid.appendChild(box);
        });
      }

      function updateStatsGrid(data) {
        ['page_opens','stats_opens','total_searches','today_searches','avg_per_hour',
         'unique_students','peak_hour','avg_per_user','export_count','last_search'].forEach(f => {
          const el = document.getElementById(`stat-${f}`);
          if (el && el.textContent != data[f]) el.textContent = data[f];
        });
        // العدادات الحية تُحدَّث عبر applyLiveCounters مباشرة
      }

      // ── جدول أكثر الطلاب بحثاً ──
      function buildTopStudents(data) {
        document.getElementById('top-tbody').innerHTML =
          data.top_students.map(s => `<tr><td>${s.id}</td><td>${s.name}</td><td>${s.count}</td></tr>`).join('');
      }
      function updateTopStudents(data) {
        const o = prevStatsData.top_students, n = data.top_students;
        if (o.length !== n.length || !o.every((s, i) => s.id === n[i]?.id && s.count === n[i]?.count))
          buildTopStudents(data);
      }

      // ── سجل البحث ──
      function buildSearchLog(data) {
        document.getElementById('log-tbody').innerHTML =
          data.search_log.map(e => `<tr><td>${e.query}</td><td>${e.time}</td><td>${e.ip}</td></tr>`).join('');
      }
      function updateSearchLog(data) {
        const o = prevStatsData.search_log, n = data.search_log;
        if (o.length !== n.length || !o.every((e, i) => e.time === n[i]?.time))
          buildSearchLog(data);
      }

      // ── جدول جميع الطلاب ──
      function buildAllStudentsTable(data) {
        showAllStudents = false;
        updateAllStudentsTable(data);
      }
      function updateAllStudentsTable(data) {
        const all      = data.all_students;
        const showAll  = all.length > 5 && showAllStudents;
        const displayed = showAll ? all : all.slice(0, 5);
        document.getElementById('all-tbody').innerHTML =
          displayed.map(s => `<tr><td>${s.id}</td><td>${s.name}</td><td>${s.count}</td></tr>`).join('');
        const toggleDiv = document.getElementById('all-toggle-buttons');
        const showBtn   = document.getElementById('show-all-btn');
        const hideBtn   = document.getElementById('hide-all-btn');
        if (all.length > 5) {
          toggleDiv.classList.remove('hidden');
          showBtn.classList.toggle('hidden', showAll);
          hideBtn.classList.toggle('hidden', !showAll);
        } else {
          toggleDiv.classList.add('hidden');
        }
      }

      // ── تصفير الإحصائيات ──
      function resetAllStats() {
        if (!confirm('هل أنت متأكد من تصفير جميع الإحصائيات؟ لا يمكن التراجع.')) return;
        ['top-tbody','log-tbody','all-tbody'].forEach(id => { document.getElementById(id).innerHTML = ''; });
        ['active_users','stats_active_users','page_opens','stats_opens','total_searches',
         'today_searches','avg_per_hour','unique_students','peak_hour','avg_per_user','export_count'].forEach(f => {
          const el = document.getElementById(`stat-${f}`);
          if (el) el.textContent = '0';
        });
        const lastEl = document.getElementById('stat-last_search');
        if (lastEl) lastEl.textContent = '—';
        prevStatsData = null;

        fetch('reset_stats.php', { method: 'POST' })
          .then(r => r.json())
          .then(d => { alert(d.message); })
          .catch(() => { alert('فشل التصفير'); })
          .finally(() => { setTimeout(() => fetchStatsData(true), 300); });
      }

      // ── تهيئة ──
      renderHistory();
    </script>
  </body>
</html>