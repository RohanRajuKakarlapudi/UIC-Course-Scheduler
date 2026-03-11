import sqlite3

DB_FILE = "courses.db"

tables_to_drop = [
    "capacity_history",
    "section_capacity"
]

conn = sqlite3.connect(DB_FILE)
cursor = conn.cursor()

for table in tables_to_drop:
    try:
        cursor.execute(f"DROP TABLE IF EXISTS {table}")
        print(f"Dropped table: {table}")
    except Exception as e:
        print(f"Error dropping {table}: {e}")

conn.commit()
conn.close()

print("Done.")