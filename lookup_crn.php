<?php

$db = new SQLite3("courses.db");

$subject = trim($_GET["subject"]);
$course = trim($_GET["course"]);
$term = "Fall 2026";

// normalize course number
$course = preg_replace('/[^0-9]/', '', $course);

$query = $db->prepare("
SELECT crn
FROM section_capacity
WHERE subject = :subject
AND CAST(course_number AS INTEGER) = CAST(:course AS INTEGER)
AND term = :term
ORDER BY capacity DESC
LIMIT 1
");

$query->bindValue(":subject", $subject);
$query->bindValue(":course", $course);
$query->bindValue(":term", $term);

$result = $query->execute();

$row = $result->fetchArray(SQLITE3_ASSOC);

echo json_encode($row);

?>