<?php
require_once 'includes/config.php';
require_once 'includes/cities_service.php';

$cities = getCities();
$defaultCity = 'Москва';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Калькулятор доставки</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="delivery-calculator">
    <h1>Калькулятор доставки</h1>

    <form id="deliveryForm">
        <div class="form-group">
            <label for="city">Город доставки:</label>
            <select id="city" name="city" required>
                <?php foreach ($cities as $city): ?>
                    <option value="<?= htmlspecialchars($city) ?>"
                        <?= $city === $defaultCity ? 'selected' : '' ?>>
                        <?= htmlspecialchars($city) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="weight">Вес груза (кг):</label>
            <input type="number" id="weight" name="weight" min="1" required>
        </div>

        <button type="submit" id="calculateBtn">Рассчитать</button>
    </form>

    <div id="resultContainer"></div>
</div>

<script src="js/script.js"></script>
</body>
</html>