CREATE TABLE IF NOT EXISTS section_capacity (
    capacity_id INTEGER PRIMARY KEY AUTOINCREMENT,
    offering_id INTEGER,
    crn INTEGER,
    term TEXT,
    capacity INTEGER,
    remaining_seats INTEGER,
    enrolled INTEGER,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(offering_id) REFERENCES offerings(offering_id)
);

CREATE TABLE IF NOT EXISTS capacity_history (
    history_id INTEGER PRIMARY KEY AUTOINCREMENT,
    offering_id INTEGER,
    crn INTEGER,
    capacity INTEGER,
    remaining_seats INTEGER,
    enrolled INTEGER,
    snapshot_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(offering_id) REFERENCES offerings(offering_id)
);