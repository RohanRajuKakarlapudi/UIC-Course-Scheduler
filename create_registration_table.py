import sqlite3

DB_FILE = "courses.db"

conn = sqlite3.connect(DB_FILE)
cursor = conn.cursor()

cursor.execute("""
CREATE TABLE IF NOT EXISTS student_registrations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id TEXT,
    term TEXT,
    crn INTEGER,
    subject TEXT,
    course_number TEXT,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
""")

conn.commit()
conn.close()

print("student_registrations table created.")