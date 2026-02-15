import sqlite3
from pathlib import Path

def title_case(s: str) -> str:
    """
    Converts 'computer science' -> 'Computer Science'
    Keeps spacing intact, title-cases every word.
    """
    return " ".join(word.capitalize() for word in s.split())

# Path to your database (same folder as this script)
db_path = Path(__file__).parent / "courses.db"

conn = sqlite3.connect(db_path)
cur = conn.cursor()

# Fetch existing subjects
cur.execute("SELECT subject_code, subject_name FROM subjects")
rows = cur.fetchall()

# Update each subject name
for subject_code, subject_name in rows:
    if subject_name is None:
        continue

    new_name = title_case(subject_name)

    cur.execute(
        """
        UPDATE subjects
        SET subject_name = ?
        WHERE subject_code = ?
        """,
        (new_name, subject_code)
    )

conn.commit()
conn.close()

print("✅ Subject names successfully title-cased.")
