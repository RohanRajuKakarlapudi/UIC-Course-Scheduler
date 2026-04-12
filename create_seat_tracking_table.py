"""
create_seat_tracking_table.py

Creates the `seat_tracking` table in courses.db.

PURPOSE:
    Tracks real-time seat changes driven by student selections in the UI.
    This table is the source of truth for "current seats available" shown
    in the frontend. It NEVER touches section_capacity (which is read-only
    scraped data).

HOW IT WORKS:
    - When a student adds a course  -> insert a row with delta = -1
    - When a student removes course -> insert a row with delta = +1
    - Available seats for a CRN = section_capacity.remaining_seats + SUM(seat_tracking.delta)
"""

import sqlite3

DB_FILE = "courses.db"

conn = sqlite3.connect(DB_FILE)
cursor = conn.cursor()

cursor.execute("""
CREATE TABLE IF NOT EXISTS seat_tracking (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    crn             INTEGER NOT NULL,
    term            TEXT NOT NULL,
    subject         TEXT NOT NULL,
    course_number   TEXT NOT NULL,
    student_id      TEXT,
    delta           INTEGER NOT NULL DEFAULT -1,
    action          TEXT CHECK(action IN ('add', 'remove')) NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
""")

cursor.execute("""
CREATE INDEX IF NOT EXISTS idx_seat_tracking_crn_term
ON seat_tracking (crn, term)
""")

conn.commit()
conn.close()

print("seat_tracking table created successfully.")