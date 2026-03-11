import sqlite3

conn = sqlite3.connect("courses.db")
cursor = conn.cursor()

cursor.execute("""
INSERT INTO capacity_history (offering_id, crn, capacity, remaining_seats, enrolled)
SELECT offering_id, crn, capacity, remaining_seats, enrolled
FROM section_capacity
""")

conn.commit()
conn.close()

print("Snapshot saved to capacity_history.")