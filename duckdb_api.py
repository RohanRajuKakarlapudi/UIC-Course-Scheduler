import json
import sys

import duckdb

DB_PATH = "database_v4.duckdb"   # put this file in your project folder (same level)

def q(sql, params=None):
    con = duckdb.connect(DB_PATH, read_only=True)
    try:
        cur = con.execute(sql, params or [])
        cols = [d[0] for d in cur.description]
        rows = [dict(zip(cols, r)) for r in cur.fetchall()]
        return rows
    finally:
        con.close()

# ---- IMPORTANT ----
# You MUST adjust table/column names below to match YOUR DuckDB schema.
# Run: duckdb database_v4.duckdb
# Then: show tables; describe <table>;

def get_majors():
    return q("SELECT major_id AS id, major_name AS name FROM majors ORDER BY major_name")

def get_subjects():
    return q("SELECT subject_id AS id, subject_code AS name FROM subjects ORDER BY subject_code")

def get_courses(subject_id):
    return q("""
        SELECT course_id AS id,
               course_label AS name,      -- e.g., "CS 401"
               credit_hours               -- e.g., "3" or "1-3" or "2,3,4"
        FROM courses
        WHERE subject_id = ?
        ORDER BY course_label
    """, [subject_id])

if __name__ == "__main__":
    action = (sys.argv[1] if len(sys.argv) > 1 else "").lower()

    if action == "majors":
        print(json.dumps(get_majors()))
    elif action == "subjects":
        print(json.dumps(get_subjects()))
    elif action == "courses":
        subject_id = sys.argv[2] if len(sys.argv) > 2 else ""
        print(json.dumps(get_courses(subject_id)))
    else:
        print(json.dumps({"error": "unknown action"}))
