<?php
require_once '../config.php';

try {
    $conn = new PDO(
        "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASSWORD
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $conn->exec("UPDATE page_visits SET visit_count = visit_count + 1, last_updated = CURRENT_TIMESTAMP WHERE id = 1");

    $stmt = $conn->query("SELECT visit_count FROM page_visits WHERE id = 1");
    $visitCount = $stmt->fetchColumn();

    $currentTime = date('H:i');

} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Счетчик посещений</title>
</head>
<body>
<h1>Страница была загружена <?php echo $visitCount; ?> раз. Текущее время <?php echo $currentTime; ?>.</h1>
</body>
</html>