<?php
/**
 * register_courses.php
 *
 * NEW FILE — do not modify optimize-schedule.php or get_remaining_seats.php.
 *
 * Called from optimize-schedule.php via PHP include at the top of the file:
 *
 *     include("register_courses.php");
 *
 * WHAT IT DOES:
 *   1. Generates a session-based student_id from timestamp + hex
 *   2. For each chosen course:
 *      - Resolves CRN from section_capacity (read-only, never modified)
 *      - Inserts into student_registrations (existing table)
 *      - Inserts into seat_tracking with delta = -1 (seat taken)
 *
 * RESULT:
 *   - $student_id is available to the rest of optimize-schedule.php
 *   - seat_tracking reflects real-time seat usage
 */

$db   = new SQLite3("courses.db");
$term = "Fall 2026";

// Use timestamp + hex as student_id (temp until real auth exists)
$student_id = time() . "_" . bin2hex(random_bytes(8));

if (isset($_POST["chosenClasses"])) {

    foreach ($_POST["chosenClasses"] as $course) {

        $parts     = explode(" ", trim($course));
        $subject   = $parts[0];
        $courseNum = $parts[1];

        // Resolve CRN from section_capacity — read-only, never modified
        $q = $db->prepare("
            SELECT crn
            FROM section_capacity
            WHERE subject       = :subject
              AND course_number = :course
              AND term          = :term
            ORDER BY capacity DESC
            LIMIT 1
        ");
        $q->bindValue(":subject", $subject);
        $q->bindValue(":course",  $courseNum);
        $q->bindValue(":term",    $term);
        $r   = $q->execute();
        $row = $r->fetchArray(SQLITE3_ASSOC);
        $crn = $row["crn"] ?? null;

        // 1. Insert into student_registrations
        $ins1 = $db->prepare("
            INSERT INTO student_registrations
                (student_id, term, crn, subject, course_number)
            VALUES
                (:student, :term, :crn, :subject, :course)
        ");
        $ins1->bindValue(":student", $student_id);
        $ins1->bindValue(":term",    $term);
        $ins1->bindValue(":crn",     $crn);
        $ins1->bindValue(":subject", $subject);
        $ins1->bindValue(":course",  $courseNum);
        $ins1->execute();

        // 2. Insert into seat_tracking — delta = -1 means one seat taken
        if ($crn) {
            $ins2 = $db->prepare("
                INSERT INTO seat_tracking
                    (crn, term, subject, course_number, student_id, delta, action)
                VALUES
                    (:crn, :term, :subject, :course, :student, -1, 'add')
            ");
            $ins2->bindValue(":crn",     $crn);
            $ins2->bindValue(":term",    $term);
            $ins2->bindValue(":subject", $subject);
            $ins2->bindValue(":course",  $courseNum);
            $ins2->bindValue(":student", $student_id);
            $ins2->execute();
        }
    }
}
?>
