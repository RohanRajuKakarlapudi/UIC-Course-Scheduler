<?php
/**
 * get_seats_live.php
 *
 * NEW FILE — drop this alongside get_remaining_seats.php.
 * Do NOT modify the existing get_remaining_seats.php.
 *
 * In index.html, change the two fetch calls from:
 *     "get_remaining_seats.php?crn=..."
 * to:
 *     "get_seats_live.php?crn=..."
 *
 * DIFFERENCE FROM OLD FILE:
 *   Old file: registered = COUNT(*) from student_registrations
 *   New file: registered = ABS(SUM(delta)) from seat_tracking
 *             remaining  = section_capacity.remaining_seats + SUM(delta)
 *   section_capacity is NEVER modified.
 *
 * GET params:
 *   crn  - Course Reference Number
 *   term - e.g. "Fall 2026"
 */

$db   = new SQLite3("courses.db");
$crn  = (int) $_GET["crn"];
$term = trim($_GET["term"]);

// Pull base data from section_capacity — read-only
$q1 = $db->prepare("
    SELECT subject, course_number, capacity, remaining_seats
    FROM section_capacity
    WHERE crn  = :crn
      AND term = :term
    LIMIT 1
");
$q1->bindValue(":crn",  $crn);
$q1->bindValue(":term", $term);
$r1  = $q1->execute();
$row = $r1->fetchArray(SQLITE3_ASSOC);

if (!$row) {
    echo json_encode(["error" => "CRN not found"]);
    exit;
}

$subject       = $row["subject"];
$course        = $row["course_number"];
$capacity      = (int) $row["capacity"];
$scraped_seats = (int) $row["remaining_seats"];

// Pull live delta from seat_tracking
$q2 = $db->prepare("
    SELECT COALESCE(SUM(delta), 0) AS net_delta
    FROM seat_tracking
    WHERE crn  = :crn
      AND term = :term
");
$q2->bindValue(":crn",  $crn);
$q2->bindValue(":term", $term);
$r2        = $q2->execute();
$delta_row = $r2->fetchArray(SQLITE3_ASSOC);
$net_delta = (int) $delta_row["net_delta"];

// remaining = scraped baseline + all deltas (never go below 0)
$remaining  = max(0, $scraped_seats + $net_delta);
$registered = abs($net_delta);   // how many seats taken via UI

echo json_encode([
    "subject"    => $subject,
    "course"     => $course,
    "capacity"   => $capacity,
    "registered" => $registered,
    "remaining"  => $remaining
]);
?>
