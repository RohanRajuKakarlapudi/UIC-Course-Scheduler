<?php
header("Content-Type: application/json; charset=utf-8");

try {
    $dbPath = realpath(__DIR__ . "/courses.db");
    if ($dbPath === false) {
        throw new Exception("courses.db not found");
    }

    $pdo = new PDO(
        "sqlite:$dbPath",
        null,
        null,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::SQLITE_ATTR_OPEN_FLAGS => PDO::SQLITE_OPEN_READONLY
        ]
    );

    $sql = "SELECT id, parent_id, name, credit_hours FROM class_listing_view";
    $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($rows, JSON_PRETTY_PRINT);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
