import requests
import pandas as pd
import time

BASE_URL = "https://banner.apps.uillinois.edu/StudentRegistrationSSB/ssb/searchResults/searchResults"

TERM = "220261"   # Spring 2026
PAGE_SIZE = 50

headers = {
    "Accept": "application/json, text/javascript, */*; q=0.01",
    "Accept-Language": "en-US,en;q=0.9",
    "Connection": "keep-alive",
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36",
    "X-Requested-With": "XMLHttpRequest",
    "X-Synchronizer-Token": "d167ec1f-8d57-4e1f-a92b-12be7e2c8f48",
    "ADRUM": "isAjax:true",
    "Referer": "https://banner.apps.uillinois.edu/StudentRegistrationSSB/ssb/classSearch/classSearch"
}

cookies = {
    "JSESSIONID": "ED817FF09F5CD666E8E5CE8DD19FB6CC.server9",
    "ADRUM": "s~1773252054336&r~aHR0cHMlM0ElMkYlMkZiYW5uZXIuYXBwcy51aWxsaW5vaXMuZWR1JTJGU3R1ZGVudFJlZ2lzdHJhdGlvblNTQiUyRnNzYiUyRnRlcm0lMkZ0ZXJtU2VsZWN0aW9uJTNGaGFzaCUzRC0yMTI0OTEwNzE3"
}

rows = []
offset = 0

print("Starting Spring 2026 scrape...")

while True:

    params = {
        "txt_term": TERM,
        "startDatepicker": "",
        "endDatepicker": "",
        "uniqueSessionId": "g0mu81773252047940",
        "pageOffset": offset,
        "pageMaxSize": PAGE_SIZE,
        "sortColumn": "subjectDescription",
        "sortDirection": "asc",
        "_": int(time.time()*1000)
    }

    r = requests.get(
        BASE_URL,
        headers=headers,
        cookies=cookies,
        params=params,
        timeout=20
    )

    if r.status_code != 200:
        print("HTTP error:", r.status_code)
        break

    data = r.json()["data"]

    print(f"offset {offset} -> {len(data)} rows")

    if len(data) == 0:
        break

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

    offset += PAGE_SIZE
    time.sleep(0.2)

df = pd.DataFrame(rows)
df = df.drop_duplicates(subset=["crn"])

print("\nTotal classes scraped:", len(df))

df.to_csv("uic_spring2026_full_capacity.csv", index=False)

print("Saved to uic_spring2026_full_capacity.csv")