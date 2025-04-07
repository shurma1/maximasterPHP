<?php
require_once '../config.php';

function getConnection() {
    try {
        $conn = new PDO(
            "pgsql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME,
            DB_USER,
            DB_PASSWORD
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        die("Ошибка подключения: " . $e->getMessage());
    }
}

function saveMessage($name, $message) {
    $conn = getConnection();

    try {
        $conn->beginTransaction();

        $stmt = $conn->prepare(
            "INSERT INTO messages (name, message) 
            VALUES (:name, :message)"
        );
        $stmt->execute([
            ':name' => $name ?: 'Анонимно',
            ':message' => trim($message)
        ]);

        // Послледние 100, чтобы сайт не встал на колени
        $conn->exec(
            "DELETE FROM messages 
            WHERE id NOT IN (
                SELECT id FROM messages 
                ORDER BY created_at ASC 
                LIMIT 100
            )"
        );

        $conn->commit();
    } catch(PDOException $e) {
        $conn->rollBack();
        throw $e;
    }
}

function getMessages() {
    $conn = getConnection();

    $stmt = $conn->query(
        "SELECT id, name, message, 
        to_char(created_at, 'DD.MM.YYYY HH24:MI') as date 
        FROM messages 
        ORDER BY created_at ASC 
        LIMIT 100"
    );

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>