=====================================================
COURSES.DB — DATABASE SCHEMA & DATA MODEL OVERVIEW
=====================================================

View using https://www.chatdb.ai/tools/sqlite-viewer
or using DB Viewer extension form VS code

This database was created by normalizing the original
database_v7.json file into relational SQLite tables.

The schema separates concerns cleanly:
- course metadata
- credit variability
- offerings per term
- time slots
- prerequisites
- subjects and majors

-----------------------------------------------------
TABLE: subjects
-----------------------------------------------------
Purpose:
Stores top-level academic subject codes (e.g., CS, MATH).

Columns:
- subject_code (TEXT, PRIMARY KEY)
    Example: "CS", "MATH", "ECE"

- subject_name (TEXT)
    Example: "Computer Science", "Mathematics"

Primary Key:
- subject_code

Mapping from JSON:
database_v7.json → subjects[]
Each unique subject becomes one row.


-----------------------------------------------------
TABLE: courses
-----------------------------------------------------
Purpose:
Defines each course independent of credits or offerings.

Columns:
- course_id (TEXT, PRIMARY KEY)
    Example: "CS_141"

- subject_code (TEXT, FOREIGN KEY → subjects.subject_code)
    Example: "CS"

- course_number (TEXT)
    Example: "141"

- offered_fall (BOOLEAN)
- offered_spring (BOOLEAN)

Primary Key:
- course_id

Foreign Keys:
- subject_code → subjects(subject_code)

Mapping from JSON:
database_v7.json → courses[]
Each course object becomes one row.


-----------------------------------------------------
TABLE: course_credits
-----------------------------------------------------
Purpose:
Represents possible credit values for a course.
Some courses allow multiple credit options.

Columns:
- course_id (TEXT, FOREIGN KEY → courses.course_id)
    Example: "CS_396"

- credit_idx (INTEGER)
    Index position of the credit option
    (0 = first option, 1 = second option, etc.)

- credit_value (REAL)
    Example: 1.0, 2.0, 3.0

Primary Key:
- (course_id, credit_idx)

Foreign Keys:
- course_id → courses(course_id)

Mapping from JSON:
database_v7.json → courses[].credits[]

Example:
"credits": [1,2,3]
→
(CS_396, 0, 1)
(CS_396, 1, 2)
(CS_396, 2, 3)


-----------------------------------------------------
TABLE: offerings
-----------------------------------------------------
Purpose:
Represents a specific section of a course in a term.

Columns:
- offering_id (INTEGER, PRIMARY KEY AUTOINCREMENT)
    Unique identifier for this section

- course_id (TEXT, FOREIGN KEY → courses.course_id)
    Example: "ACTG_210"

- term (TEXT)
    Example: "fall", "spring"

- section_idx (INTEGER)
    Section number (0-based index)
    Section 0 = first section of that course in that term

- crn (INTEGER)
    Campus Registration Number

- days_count (INTEGER)
    Number of meeting days per week

Primary Key:
- offering_id

Foreign Keys:
- course_id → courses(course_id)

Mapping from JSON:
database_v7.json → offerings[]

Interpretation:
If days_count = 3
→ the class meets 3 times per week


-----------------------------------------------------
TABLE: offering_times
-----------------------------------------------------
Purpose:
Stores time blocks for each offering (class meeting).

Columns:
- offering_id (INTEGER, FOREIGN KEY → offerings.offering_id)
    Links to a specific section

- seq (INTEGER)
    Order of time blocks for that offering
    (0 = first meeting, 1 = second, etc.)

- start_min (INTEGER)
    Minutes since start of week

- end_min (INTEGER)
    Minutes since start of week

Primary Key:
- (offering_id, seq)

Foreign Keys:
- offering_id → offerings(offering_id)

Mapping from JSON:
database_v7.json → offerings[].times[]

Example:
If an offering meets M/W/F:
seq 0 = Monday time
seq 1 = Wednesday time
seq 2 = Friday time


-----------------------------------------------------
TABLE: prerequisites
-----------------------------------------------------
Purpose:
Represents prerequisite relationships between courses.

Columns:
- course_id (TEXT, FOREIGN KEY → courses.course_id)
    The course being taken

- prereq_course_id (TEXT, FOREIGN KEY → courses.course_id)
    Required course

Primary Key:
- (course_id, prereq_course_id)

Mapping from JSON:
database_v7.json → prerequisites[]


-----------------------------------------------------
TABLE: majors
-----------------------------------------------------
Purpose:
Stores degree programs (BS, BA, Minor, Certificates).
This is logically independent from courses.

Columns:
- major_id (INTEGER, PRIMARY KEY AUTOINCREMENT)

- major_name (TEXT)
    Example: "Computer Science - BS"

- major_type (TEXT)
    Example: "BS", "BA", "Minor", "Certificate"

Mapping:
Manually populated from UIC Academic Catalog
(Not derived from course data)


-----------------------------------------------------
VIEW: class_listing_view
-----------------------------------------------------
Purpose:
Unified view used by the frontend dropdowns.

Structure:
1) Subjects (top level)
2) Courses (child level with credit options)

Columns:
- id
- parent_id
- name
- credit_hours

Frontend Logic:
- parent_id IS NULL → Subject
- parent_id = subject_code → Course

This view is consumed by:
class_listing.php → index.html via AJAX


=====================================================
HOW TO RUN THE PHP SERVER (IMPORTANT)
=====================================================

You must run PHP from the folder that contains:
- index.html
- class_listing.php
- courses.db

Correct directory:
UI-W-Migrated/

Command (PowerShell or CMD):

    cd path\to\UI-W-Migrated
    php -S 127.0.0.1:8000

Then open browser:

    http://127.0.0.1:8000/index.html

Do NOT:
- Use VS Code Live Server
- Open index.html directly via file://
- Run PHP from another directory

=====================================================
END OF DOCUMENTATION
=====================================================
