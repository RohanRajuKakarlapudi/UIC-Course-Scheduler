import sqlite3

conn = sqlite3.connect("courses.db")
cursor = conn.cursor()

cursor.execute("""
INSERT INTO student_registrations
(student_id, term, crn, subject, course_number)
VALUES (?, ?, ?, ?, ?)
""", ("student_1", "Fall 2026", 12345, "ACTG", "594"))

conn.commit()
conn.close()

print("Test registration inserted")