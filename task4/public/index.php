<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../../config.php';

$app = AppFactory::create();

$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

function getDbConnection() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Ошибка подключения к базе данных: " . $e->getMessage());
        }
    }
    return $pdo;
}

$app->get('/api/products', function (Request $request, Response $response) {
    try {
        $db = getDbConnection();
        $stmt = $db->query('SELECT * FROM products');
        $products = $stmt->fetchAll();

        $response->getBody()->write(json_encode($products));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

$app->get('/api/products/{id}', function (Request $request, Response $response, $args) {
    try {
        $id = $args['id'];
        $db = getDbConnection();
        $stmt = $db->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        if (!$product) {
            $response->getBody()->write(json_encode(['error' => 'Товар не найден']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode($product));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

$app->post('/api/products', function (Request $request, Response $response) {
    try {
        $data = $request->getParsedBody();

        if (!isset($data['name']) || !isset($data['price'])) {
            $response->getBody()->write(json_encode(['error' => 'Требуются поля name и price']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $db = getDbConnection();
        $stmt = $db->prepare('INSERT INTO products (name, description, price, stock) VALUES (?, ?, ?, ?) RETURNING *'); // Изменили на RETURNING *
        $stmt->execute([
            $data['name'],
            $data['description'] ?? '',
            $data['price'],
            $data['stock'] ?? 0
        ]);

        $product = $stmt->fetch();

        $response->getBody()->write(json_encode([
            'product' => $product,
            'message' => 'Товар успешно создан'
        ]));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

$app->put('/api/products/{id}', function (Request $request, Response $response, $args) {
    try {
        $id = $args['id'];
        $data = $request->getParsedBody();
        $db = getDbConnection();

        $check = $db->prepare('SELECT * FROM products WHERE id = ?');
        $check->execute([$id]);
        $existingProduct = $check->fetch();

        if (!$existingProduct) {
            $response->getBody()->write(json_encode(['error' => 'Товар не найден']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $fields = [];
        $values = [];

        if (isset($data['name'])) {
            $fields[] = 'name = ?';
            $values[] = $data['name'];
        } else {
            $values[] = $existingProduct['name'];
        }

        if (isset($data['description'])) {
            $fields[] = 'description = ?';
            $values[] = $data['description'];
        } else {
            $values[] = $existingProduct['description'];
        }

        if (isset($data['price'])) {
            $fields[] = 'price = ?';
            $values[] = $data['price'];
        } else {
            $values[] = $existingProduct['price'];
        }

        if (isset($data['stock'])) {
            $fields[] = 'stock = ?';
            $values[] = $data['stock'];
        } else {
            $values[] = $existingProduct['stock'];
        }

        $values[] = $id;
        $sql = 'UPDATE products SET ' . implode(', ', $fields) . ' WHERE id = ? RETURNING *'; // Добавили RETURNING *

        $stmt = $db->prepare($sql);
        $stmt->execute($values);
        $updatedProduct = $stmt->fetch();

        $response->getBody()->write(json_encode([
            'product' => $updatedProduct,
            'message' => 'Товар успешно обновлен'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

$app->delete('/api/products/{id}', function (Request $request, Response $response, $args) {
    try {
        $id = $args['id'];
        $db = getDbConnection();

        $check = $db->prepare('SELECT id FROM products WHERE id = ?');
        $check->execute([$id]);
        if (!$check->fetch()) {
            $response->getBody()->write(json_encode(['error' => 'Товар не найден']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $stmt = $db->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);

        $response->getBody()->write(json_encode(['message' => 'Товар успешно удален']));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

$app->run();