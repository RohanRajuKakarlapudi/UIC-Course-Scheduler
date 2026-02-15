# import sqlite3
# from pathlib import Path

# db_path = Path(__file__).parent / "courses.db"

# conn = sqlite3.connect(db_path)
# cur = conn.cursor()

# cur.execute("DROP VIEW IF EXISTS class_listing_view")

# cur.execute("""
# CREATE VIEW class_listing_view AS

# -- =========================
# -- MAJORS (special bucket)
# -- parent_id = '-999' to match your frontend logic
# -- =========================
# SELECT
#     'MAJOR_' || m.major_id           AS id,
#     '-999'                           AS parent_id,
#     TRIM(m.name)                     AS name,
#     NULL                             AS credit_hours
# FROM majors m

# UNION ALL

# -- =========================
# -- SUBJECTS (top level)
# -- parent_id = NULL (your JS already handles this)
# -- =========================
# SELECT
#     TRIM(s.subject_code)             AS id,
#     NULL                             AS parent_id,
#     TRIM(s.subject_name)             AS name,
#     NULL                             AS credit_hours
# FROM subjects s

# UNION ALL

# -- =========================
# -- COURSES (second level)
# -- =========================
# SELECT
#     TRIM(c.subject_code) || ' ' || TRIM(c.course_number) AS id,
#     TRIM(c.subject_code)                                  AS parent_id,
#     TRIM(c.subject_code) || ' ' || TRIM(c.course_number) AS name,
#     GROUP_CONCAT(cc.credit_value)                         AS credit_hours
# FROM courses c
# LEFT JOIN course_credits cc
#     ON cc.course_id = c.course_id
# GROUP BY
#     c.course_id,
#     c.subject_code,
#     c.course_number;
# """)

# conn.commit()
# print("✅ VIEW CREATED SUCCESSFULLY")
# conn.close()


import sqlite3
from pathlib import Path

db_path = Path(__file__).parent / "courses.db"

conn = sqlite3.connect(db_path)
cur = conn.cursor()

cur.execute("DROP VIEW IF EXISTS class_listing_view")

cur.execute("""
CREATE VIEW class_listing_view AS

-- =========================
-- MAJORS (special bucket)
-- parent_id = '-999'
-- =========================
SELECT
    'MAJOR_' || m.major_id           AS id,
    '-999'                           AS parent_id,
    TRIM(m.name)                     AS name,
    NULL                             AS credit_hours
FROM majors m

UNION ALL

-- =========================
-- SUBJECTS (top level)
-- parent_id = NULL
-- =========================
SELECT
    TRIM(s.subject_code)             AS id,
    NULL                             AS parent_id,
    TRIM(s.subject_name)             AS name,
    NULL                             AS credit_hours
FROM subjects s

UNION ALL

-- =========================
-- COURSES (second level)
-- CLEANED VERSION
-- =========================
SELECT
    TRIM(c.subject_code) || ' ' ||
    REPLACE(TRIM(c.course_number), '_', '')       AS id,

    TRIM(c.subject_code)                          AS parent_id,

    TRIM(c.subject_code) || ' ' ||
    REPLACE(TRIM(c.course_number), '_', '')       AS name,

    GROUP_CONCAT(DISTINCT cc.credit_value)        AS credit_hours

FROM courses c
LEFT JOIN course_credits cc
    ON cc.course_id = c.course_id

GROUP BY
    c.course_id,
    c.subject_code,
    c.course_number;

""")

conn.commit()
print("✅ VIEW CREATED SUCCESSFULLY")
conn.close()
