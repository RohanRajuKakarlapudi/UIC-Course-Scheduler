import sqlite3
import pandas as pd

DB = "courses.db"

FALL_FILE = "uic_fall2026_full_capacity.csv"
SPRING_FILE = "uic_spring2026_full_capacity.csv"

FALL_TERM = "Fall 2026"
SPRING_TERM = "Spring 2026"

conn = sqlite3.connect(DB)
cursor = conn.cursor()

# -------------------------
# Create table
# -------------------------

cursor.execute("""
CREATE TABLE IF NOT EXISTS section_capacity (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    term TEXT,
    crn INTEGER,
    subject TEXT,
    course_number TEXT,
    title TEXT,
    capacity INTEGER,
    remaining_seats INTEGER,
    enrolled INTEGER
)
""")

conn.commit()

print("Table ready")

# -------------------------
# Load Fall
# -------------------------

fall_df = pd.read_csv(FALL_FILE)
fall_df["term"] = FALL_TERM

# -------------------------
# Load Spring
# -------------------------

spring_df = pd.read_csv(SPRING_FILE)
spring_df["term"] = SPRING_TERM

# -------------------------
# Combine datasets
# -------------------------

df = pd.concat([fall_df, spring_df], ignore_index=True)

df = df[[
    "term",
    "crn",
    "subject",
    "course_number",
    "title",
    "capacity",
    "remaining_seats",
    "enrolled"
]]

# -------------------------
# Insert into DB
# -------------------------

df.to_sql(
    "section_capacity",
    conn,
    if_exists="append",
    index=False
)

conn.commit()
conn.close()

print("Inserted rows:", len(df))
print("Done.")