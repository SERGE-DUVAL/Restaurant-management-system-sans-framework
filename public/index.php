<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json');


// Inclure l'autoload de Composer

require_once __DIR__ . '/../vendor/autoload.php';
/*
var_dump($_SERVER['REQUEST_URI']);
var_dump(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
exit;
*/

use src\Controller\AuthController;
use src\Controller\UserController;
use src\Controller\CategorieController as CategoryController;
use src\Controller\PlatsController;
use src\Controller\CommandeController;
use src\Controller\PaiementController;
use src\Controller\StockController;
use src\Controller\NotificationController;
use src\Controller\OnlineOrderController;
use src\Controller\DashboardController;

// Charger les classes automatiquement (Composer autoload)
//require_once __DIR__ . '/vendor/autoload.php';

// Récupérer la méthode HTTP et l'URL
$method = $_SERVER['REQUEST_METHOD'];

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$basePath = '/backend/public';
$uri = str_replace($basePath, '', $uri);


// Parse query string
parse_str($_SERVER['QUERY_STRING'] ?? '', $queryParams);

// Lire le body JSON si POST/PUT/PATCH
$body = json_decode(file_get_contents('php://input'), true);

// Simuler un middleware simple pour authentification
function authMiddleware() {
    // Exemple : token JWT dans Authorization header
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['message' => 'Unauthenticated']);
        exit;
    }
    // Ici tu pourrais vérifier le JWT
    return true;
}


// Router simple
switch (true) {

    // Auth
    case $method === 'POST' && $uri === '/api/login':
        echo (new AuthController())->login($body);
        break;

    case $method === 'POST' && $uri === '/api/password/forgot':
        echo (new AuthController())->forgotPassword($body);
        break;

    case $method === 'POST' && $uri === '/api/password/reset':
        echo (new AuthController())->resetPassword($body);
        break;

    case $method === 'POST' && $uri === '/api/logout':
        authMiddleware();
        echo (new AuthController())->logout();
        break;

    // Users
    case preg_match('#^/api/users$#', $uri) && $method === 'GET':
        authMiddleware();
        echo (new UserController())->index();
        break;

    case preg_match('#^/api/users$#', $uri) && $method === 'POST':
        authMiddleware();
        echo (new UserController())->store($body);
        break;

    case preg_match('#^/api/users/(\d+)$#', $uri, $matches) && $method === 'PUT':
        authMiddleware();
        echo (new UserController())->update($matches[1], $body);
        break;

    case preg_match('#^/api/users/(\d+)$#', $uri, $matches) && $method === 'DELETE':
        authMiddleware();
        echo (new UserController())->destroy($matches[1]);
        break;

    case preg_match('#^/api/users/search$#', $uri) && $method === 'GET':
        authMiddleware();
        echo (new UserController())->search($queryParams);
        break;

    // Categories
    case preg_match('#^/api/categories$#', $uri) && $method === 'GET':
        authMiddleware();
        echo (new CategoryController())->index();
        break;

    case preg_match('#^/api/categories$#', $uri) && $method === 'POST':
        authMiddleware();
        echo (new CategoryController())->store($body);
        break;

    case preg_match('#^/api/categories/(\d+)$#', $uri, $matches) && $method === 'PUT':
        authMiddleware();
        echo (new CategoryController())->update($matches[1], $body);
        break;

    case preg_match('#^/api/categories/(\d+)$#', $uri, $matches) && $method === 'DELETE':
        authMiddleware();
        echo (new CategoryController())->destroy($matches[1]);
        break;

    // Dishes / Plats
    case preg_match('#^/api/dishes$#', $uri) && $method === 'GET':
        authMiddleware();
        echo (new PlatsController())->index();
        break;

    case preg_match('#^/api/dishes$#', $uri) && $method === 'POST':
        authMiddleware();
        echo (new PlatsController())->store($body);
        break;

    case preg_match('#^/api/dishes/(\d+)$#', $uri, $matches) && $method === 'PUT':
        authMiddleware();
        echo (new PlatsController())->update($matches[1], $body);
        break;

    case preg_match('#^/api/dishes/(\d+)$#', $uri, $matches) && $method === 'DELETE':
        authMiddleware();
        echo (new PlatsController())->destroy($matches[1]);
        break;

    case preg_match('#^/api/dishes/search$#', $uri) && $method === 'GET':
        authMiddleware();
        echo (new PlatsController())->search($queryParams);
        break;

    // Orders / Commandes
    case preg_match('#^/api/orders$#', $uri) && $method === 'GET':
        authMiddleware();
        echo (new CommandeController())->index();
        break;

    case preg_match('#^/api/orders$#', $uri) && $method === 'POST':
        authMiddleware();
        echo (new CommandeController())->store($body);
        break;

    case preg_match('#^/api/orders/(\d+)$#', $uri, $matches) && $method === 'PUT':
        authMiddleware();
        echo (new CommandeController())->update($matches[1], $body);
        break;

    case preg_match('#^/api/orders/(\d+)$#', $uri, $matches) && $method === 'DELETE':
        authMiddleware();
        echo (new CommandeController())->destroy($matches[1]);
        break;

    case preg_match('#^/api/orders/(\d+)/add-dish$#', $uri, $matches) && $method === 'POST':
        authMiddleware();
        echo (new CommandeController())->addDish($matches[1], $body);
        break;

    case preg_match('#^/api/orders/(\d+)/remove-dish$#', $uri, $matches) && $method === 'POST':
        authMiddleware();
        echo (new CommandeController())->removeDish($matches[1], $body);
        break;

    case preg_match('#^/api/orders/history$#', $uri) && $method === 'GET':
        authMiddleware();
        echo (new CommandeController())->history();
        break;

    // Paiements
    case preg_match('#^/api/paiements$#', $uri) && $method === 'POST':
        authMiddleware();
        echo (new PaiementController())->store($body);
        break;

    case preg_match('#^/api/paiements/(\d+)$#', $uri, $matches) && $method === 'GET':
        authMiddleware();
        echo (new PaiementController())->show($matches[1]);
        break;

    case preg_match('#^/api/paiements/(\d+)/facture$#', $uri, $matches) && $method === 'GET':
        authMiddleware();
        echo (new PaiementController())->generateInvoice($matches[1]);
        break;

    // Stocks
    case preg_match('#^/api/stocks$#', $uri) && $method === 'GET':
        authMiddleware();
        echo (new StockController())->index();
        break;

    case preg_match('#^/api/stocks$#', $uri) && $method === 'POST':
        authMiddleware();
        echo (new StockController())->store($body);
        break;

    case preg_match('#^/api/stocks/(\d+)$#', $uri, $matches) && $method === 'PUT':
        authMiddleware();
        echo (new StockController())->update($matches[1], $body);
        break;

    case preg_match('#^/api/stocks/(\d+)$#', $uri, $matches) && $method === 'DELETE':
        authMiddleware();
        echo (new StockController())->destroy($matches[1]);
        break;

    case preg_match('#^/api/stocks/movements$#', $uri) && $method === 'GET':
        authMiddleware();
        echo (new StockController())->movements();
        break;

    // Online orders / Menu
    case preg_match('#^/api/menu$#', $uri) && $method === 'GET':
        authMiddleware();
        echo (new OnlineOrderController())->menu();
        break;

    case preg_match('#^/api/cart/add$#', $uri) && $method === 'POST':
        authMiddleware();
        echo (new OnlineOrderController())->addToCart($body);
        break;

    case preg_match('#^/api/cart/checkout$#', $uri) && $method === 'POST':
        authMiddleware();
        echo (new OnlineOrderController())->checkout($body);
        break;

    case preg_match('#^/api/cart/status/(\d+)$#', $uri, $matches) && $method === 'GET':
        authMiddleware();
        echo (new OnlineOrderController())->status($matches[1]);
        break;

    // Notifications
    case preg_match('#^/api/notifications$#', $uri) && $method === 'GET':
        authMiddleware();
        echo (new NotificationController())->index();
        break;

    case preg_match('#^/api/notifications$#', $uri) && $method === 'POST':
        authMiddleware();
        echo (new NotificationController())->store($body);
        break;

    case preg_match('#^/api/notifications/(\d+)/read$#', $uri, $matches) && $method === 'PATCH':
        authMiddleware();
        echo (new NotificationController())->markAsRead($matches[1]);
        break;

    // Dashboard
    case preg_match('#^/api/dashboard$#', $uri) && $method === 'GET':
        authMiddleware();
        echo (new DashboardController())->index();
        break;

    case preg_match('#^/api/dashboard/sales-chart$#', $uri) && $method === 'GET':
        authMiddleware();
        echo (new DashboardController())->salesChart();
        break;

    default:
        http_response_code(404);
        echo json_encode(['message' => 'Route not found']);
}
