import sqlite3

conn = sqlite3.connect("courses.db")
cursor = conn.cursor()

cursor.execute("""
CREATE TABLE IF NOT EXISTS capacity_history (
    history_id INTEGER PRIMARY KEY AUTOINCREMENT,
    offering_id INTEGER,
    crn INTEGER,
    capacity INTEGER,
    remaining_seats INTEGER,
    enrolled INTEGER,
    snapshot_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(offering_id) REFERENCES offerings(offering_id)
)
""")

conn.commit()
conn.close()

print("capacity_history table created successfully.")