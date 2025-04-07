<?php
require_once '../config.php';

$createTableSql = "
CREATE TABLE IF NOT EXISTS page_visits (
    id SERIAL PRIMARY KEY,
    visit_count INTEGER NOT NULL DEFAULT 0,
    last_updated TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);
";

$initialInsertSql = "
INSERT INTO page_visits (visit_count) VALUES (0)
ON CONFLICT (id) DO NOTHING;
";

try {
    $conn = new PDO(
        "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASSWORD
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $conn->exec($createTableSql);

    $conn->exec($initialInsertSql);

    echo "ok";

} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>