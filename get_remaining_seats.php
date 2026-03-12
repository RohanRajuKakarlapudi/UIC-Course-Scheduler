<?php

$db = new SQLite3("courses.db");

$crn = $_GET["crn"];
$term = $_GET["term"];

$query1 = $db->prepare("
SELECT subject, course_number, capacity
FROM section_capacity
WHERE crn = :crn
AND term = :term
");

$query1->bindValue(":crn",$crn);
$query1->bindValue(":term",$term);

$result1 = $query1->execute();
$row = $result1->fetchArray(SQLITE3_ASSOC);

$capacity = $row["capacity"];
$subject = $row["subject"];
$course = $row["course_number"];

$query2 = $db->prepare("
SELECT COUNT(*) as registered
FROM student_registrations
WHERE crn = :crn
AND term = :term
");

$query2->bindValue(":crn",$crn);
$query2->bindValue(":term",$term);

$result2 = $query2->execute();
$registered = $result2->fetchArray(SQLITE3_ASSOC)["registered"];

$remaining = $capacity - $registered;

echo json_encode([
"subject"=>$subject,
"course"=>$course,
"capacity"=>$capacity,
"registered"=>$registered,
"remaining"=>$remaining
]);

?>