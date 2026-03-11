import requests
import pandas as pd
import sqlite3
import time

BASE_URL = "https://banner.apps.uillinois.edu/StudentRegistrationSSB/ssb/searchResults/searchResults"

TERM = "220261"

UNIQUE_SESSION = "0cuvg1773206331164"

headers = {
    "Accept": "application/json, text/javascript, */*; q=0.01",
    "User-Agent": "Mozilla/5.0",
    "X-Requested-With": "XMLHttpRequest",
    "X-Synchronizer-Token": "becbec3f-91b4-455e-b273-8eac3fd1368e"
}

cookies = {
    "JSESSIONID": "DC8DB6CAF9D2ED99416021842B21F739.server5"
}

# ---------------------------------
# Load subject codes from your DB
# ---------------------------------

conn = sqlite3.connect("courses.db")

subjects = pd.read_sql_query(
    "SELECT subject_code FROM subjects",
    conn
)["subject_code"].tolist()

print("Subjects loaded:", len(subjects))


rows = []


# ---------------------------------
# Scrape each subject
# ---------------------------------

for subject in subjects:

    print("Scraping:", subject)

    params = {
        "txt_subject": subject,
        "txt_term": TERM,
        "startDatepicker": "",
        "endDatepicker": "",
        "uniqueSessionId": UNIQUE_SESSION,
        "pageOffset": 0,
        "pageMaxSize": 500,
        "sortColumn": "subjectDescription",
        "sortDirection": "asc"
    }

    try:

        r = requests.get(
            BASE_URL,
            headers=headers,
            cookies=cookies,
            params=params
        )

        data = r.json()["data"]

        for c in data:

            capacity = c.get("maximumEnrollment")
            remaining = c.get("seatsAvailable")

            rows.append({
                "subject": c.get("subject"),
                "course_number": c.get("courseNumber"),
                "title": c.get("courseTitle"),
                "crn": c.get("courseReferenceNumber"),
                "capacity": capacity,
                "remaining_seats": remaining,
                "enrolled": capacity - remaining if capacity and remaining else None
            })

    except Exception as e:
        print("Failed:", subject)

    time.sleep(0.15)


df = pd.DataFrame(rows)

print("Total classes scraped:", len(df))

df.to_csv("uic_spring2026_full_capacity.csv", index=False)

print("Saved to uic_spring2026_full_capacity.csv")