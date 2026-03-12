<?php

$db = new SQLite3("courses.db");

$subject = $_GET["subject"];
$course = $_GET["course"];

$query = $db->prepare("
SELECT crn
FROM section_capacity
WHERE subject = :subject
AND course_number = :course
ORDER BY capacity DESC
LIMIT 1
");

$query->bindValue(":subject",$subject);
$query->bindValue(":course",$course);

$result = $query->execute();

$row = $result->fetchArray(SQLITE3_ASSOC);

echo json_encode($row);

?>