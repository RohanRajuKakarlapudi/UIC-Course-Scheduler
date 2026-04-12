"""
reset_test_data.py

Clears all test rows from seat_tracking and student_registrations.
Run this after testing to bring both tables back to a clean state.

Usage:
    python reset_test_data.py
"""

import sqlite3

DB_FILE = "courses.db"

conn = sqlite3.connect(DB_FILE)
cursor = conn.cursor()

cursor.execute("DELETE FROM seat_tracking")
seat_rows = cursor.rowcount

cursor.execute("DELETE FROM student_registrations")
reg_rows = cursor.rowcount

conn.commit()
conn.close()

print(f"Cleared {seat_rows} rows from seat_tracking")
print(f"Cleared {reg_rows} rows from student_registrations")
print("Both tables reset to 0. Ready for testing.")
