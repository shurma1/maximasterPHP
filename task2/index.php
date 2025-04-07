<?php
require_once 'database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $message = isset($_POST['message']) ? $_POST['message'] : '';

    if (!empty(trim($message))) {
        try {
            saveMessage($name, $message);
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } catch(PDOException $e) {
            $error = 'Ошибка при сохранении сообщения';
        }
    } else {
        $error = 'Сообщение не может быть пустым';
    }
}

$messages = getMessages();
?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Гостевая книга</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="chat">
    <h1>Гостевая книга</h1>

    <div class="messages_wrapper">
        <?php if (empty($messages)): ?>
            <p>Пока нет сообщений. Будьте первым!</p>
        <?php else: ?>
            <?php foreach ($messages as $msg): ?>
                <div class="message">
                    <div class="message_header">
                        <span><?= htmlspecialchars($msg['date']) ?></span>
                        <span><?= htmlspecialchars($msg['name']) ?></span>
                    </div>
                    <p class="message_text">
                        <?= nl2br(htmlspecialchars($msg['message'])) ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <hr>

    <div class="input_message_container">
        <?php if ($error): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="name" placeholder="Имя">
            <textarea name="message" placeholder="Ваше сообщение" required></textarea>
            <button type="submit">Отправить</button>
        </form>
    </div>
</div>
</body>
</html>