============================================================
UIC COURSE DATA PIPELINE – SETUP & VIEWING GUIDE
============================================================

Download XAMPP from : https://sourceforge.net/projects/xampp/

PURPOSE
-------
This project migrates course data from a large JSON file into
a structured SQLite database and exposes it to the frontend
via PHP.

A unified SQL VIEW (class_listing_view) is used so the frontend
does NOT need to understand database joins or schema details.
It simply consumes clean JSON from PHP.

------------------------------------------------------------
WHY class_listing_view EXISTS
------------------------------------------------------------
We created `class_listing_view` to:

• Flatten multiple tables (subjects, courses, credits)
• Match the structure expected by the original frontend JSON
• Avoid rewriting frontend JavaScript logic
• Keep database logic centralized and maintainable

The frontend reads ONLY from:
    class_listing.php  →  class_listing_view

------------------------------------------------------------
DATABASE STRUCTURE (SUMMARY)
------------------------------------------------------------

Tables in courses.db:
• subjects
• courses
• course_credits
• offerings
• offering_times
• prerequisites
• majors   (added manually for frontend independence)

Primary relationships:
• subjects.subject_code → courses.subject_code
• courses.course_id → course_credits.course_id
• offerings.offering_id → offering_times.offering_id

The VIEW handles all joins.

------------------------------------------------------------
CREATING / RECREATING THE VIEW
------------------------------------------------------------
Run this from the project folder:

    C:\Users\sairo\desktop\SEM 7\RESEARCH\UI-W-Migrated

Command:
    python create_view.py

Expected output:
    ✅ VIEW CREATED SUCCESSFULLY

This safely drops and recreates `class_listing_view`.

------------------------------------------------------------
HOW TO VIEW class_listing_view (NO SQLITE CLI NEEDED)
------------------------------------------------------------
We DO NOT rely on the sqlite3 CLI on Windows.

Instead, use Python:

1. Open PowerShell
2. Go to project directory:

    cd "C:\Users\sairo\desktop\SEM 7\RESEARCH\UI-W-Migrated"

3. Enter Python:

    python

4. Run:

    import sqlite3
    conn = sqlite3.connect("courses.db")
    cur = conn.cursor()
    cur.execute("SELECT * FROM class_listing_view LIMIT 20;")
    rows = cur.fetchall()
    for r in rows:
        print(r)
    conn.close()

This prints subjects and courses exactly as sent to the frontend.

------------------------------------------------------------
WHY WE DO NOT USE `php -S 127.0.0.1:8000`
------------------------------------------------------------
On Windows + XAMPP:

• Apache already runs PHP
• `php -S` is unnecessary and often fails
• Running both causes path and permission confusion

We ONLY use Apache via XAMPP.

------------------------------------------------------------
USING XAMPP (RECOMMENDED SETUP)
------------------------------------------------------------

1. Create project folder under htdocs:

    mkdir C:\xampp\htdocs\UI-W-Migrated

2. Copy project files:

    xcopy "C:\Users\sairo\desktop\SEM 7\RESEARCH\UI-W-Migrated" ^
          "C:\xampp\htdocs\UI-W-Migrated" /E

3. Open XAMPP Control Panel
4. Start:
    • Apache

------------------------------------------------------------
VIEWING THE APP IN BROWSER
------------------------------------------------------------

Open browser and go to:

    http://localhost/UI-W-Migrated/index.html

OR directly inspect the JSON output:

    http://localhost/UI-W-Migrated/class_listing.php

If this URL returns JSON → everything is wired correctly.

------------------------------------------------------------
FRONTEND DATA FLOW
------------------------------------------------------------

Browser
  ↓
index.html (jQuery)
  ↓ AJAX
class_listing.php
  ↓ SQL
class_listing_view
  ↓ SQLite
courses.db

No hardcoded course or credit data remains.

------------------------------------------------------------
WHY MAJORS ARE STORED SEPARATELY
------------------------------------------------------------
Majors are independent of:
• courses
• credits
• scheduling constraints

Therefore:
• majors are stored in a dedicated table
• exposed through the same VIEW
• keeps frontend fully database-driven

------------------------------------------------------------
END OF GUIDE
------------------------------------------------------------
