import pandas as pd
import sqlite3

conn = sqlite3.connect("courses.db")
cursor = conn.cursor()

# Load both CSV files
fall = pd.read_csv("uic_fall2026_full_capacity.csv")
spring = pd.read_csv("uic_spring2026_full_capacity.csv")

# Add term column
fall["term"] = "fall"
spring["term"] = "spring"

# Combine datasets
df = pd.concat([fall, spring])

for _, row in df.iterrows():

    crn = int(row["crn"])
    term = row["term"]

    # Handle NaN values safely
    capacity = int(row["capacity"]) if pd.notna(row["capacity"]) else 0
    remaining = int(row["remaining_seats"]) if pd.notna(row["remaining_seats"]) else 0
    enrolled = int(row["enrolled"]) if pd.notna(row["enrolled"]) else 0

    # Find offering_id from offerings table
    cursor.execute(
        "SELECT offering_id FROM offerings WHERE crn=? AND term=?",
        (crn, term)
    )

    result = cursor.fetchone()

    # Skip if offering not found
    if result is None:
        continue

    offering_id = result[0]

    # Insert capacity data
    cursor.execute("""
        INSERT INTO section_capacity
        (offering_id, crn, term, capacity, remaining_seats, enrolled)
        VALUES (?, ?, ?, ?, ?, ?)
    """, (
        offering_id,
        crn,
        term,
        capacity,
        remaining,
        enrolled
    ))

conn.commit()
conn.close()

print("Capacity data inserted successfully.")