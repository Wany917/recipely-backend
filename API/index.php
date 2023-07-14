<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}
require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/autoload.php";
require_once __DIR__ . "/lib/body.php";

use Recipely\Controllers\Router;
use dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$router = new Router();
$routes = [
    // Ajoute un paramètre pour le nom du modèle / contrôleur
    'GET' =>
    [
        'auth' => [
            '/auth/me' => 'me',
        ],
        
        'user' => [
            '/users' => 'getAllUsers',
            '/users/:id' => 'getUser'
        ],
        
        'room' => [
            '/rooms' => 'getAllRooms',
            '/rooms/:id' => 'getRoom'
        ],
        
        'reservation' => [
            '/reservations/user/:id' => 'getAllReservationsByUser',
            '/reservations/:id' => 'getReservation',
            '/reservations' => 'getAllReservations',
        ],
        
        'chat' => [
            '/chat/:id' => 'getMessage',
            '/chats/:user_id' => 'getMessages'
        ],
        
        'order' => [
            '/orders' => 'getAllOrders',
            '/orders/:id' => 'getOrder'
        ],
        
        'event' => [
            '/events' => 'getAllEvents',
            '/events/:id' => 'getEvent'
        ],
        
        'service' => [
            '/services' => 'getAllServices',
            '/services/:id' => 'getService'
        ],
        
        'provider' => [
            '/providers' => 'getAllProviders',
            // C'est l'ID  du provider
            '/providers/:id' => 'getProvider'
        ],
        
        'workshop' => [
            '/workshops' => 'getAllWorkshops',
            '/workshops/:id' => 'getWorkshop'
        ],
        
        'recipe' => [
            '/recipes/list' => 'getAllRecipes',
            '/recipes/details' => 'getRecipe',
            '/recipes/latest' => 'getLatestRecipes',
            '/recipes/search?query=:id' => 'getRecipe',
            '/recipes' => 'getAllRecipes',
            '/recipes/:id' => 'getRecipe',
            '/recipes/:id_provider' => 'getRecipesByCreator'
        ],

        'formation' => [
            '/formations' => 'getAllFormations',
            '/formations/:id' => 'getFormation',
            '/formations/formation/:id' => 'getFormationWithRecipes'
        ],
    ],
    'POST' =>
    [
        'auth' => [
            '/auth/login' => 'login',
            '/auth/logout' => 'logout',
            '/auth/register' => 'register',
        ],
        'user' => [
            '/users' => 'createUser',
        ],
        'chat' => ['/chat' => 'sendMessage'],
        'room' => ['/rooms' => 'createRoom'],
        'order' => ['/orders' => 'createOrder'],
        'service' => ['/services' => 'createService'],
        'recipe' => ['/recipes/:id_provider' => 'createRecipe'],
        
        'event' => [
            '/events/:id' => 'createEvent',
            '/events/:id/:id_location' => 'createEvent'
        ],
        
        // le provider est rattacher à la classe user il faut donc specifier son id pour l'update.
        'provider' => ['/providers/:id' => 'createProvider'],
        'formation' => ['/formations/:id_provider' => 'createFormation'],
        'workshop' => ['/workshops/:id_room/:id_provider' => 'createWorkshop'],
        'workshop' => ['/workshops/:id_room/:id_provider' => 'createWorkshop'],
        'reservation' => [
            '/reservations/:id_event/:id_client' => 'createReservation'
        ],


    ],
    'PATCH' =>
    [

        'user' => ['/users/:id' => 'update'],
        'room' => ['/rooms/:id' => 'updateRoom'],
        'chat' => ['/chat/:id' => 'updateMessage'],
        'event' => ['/events/:id' => 'updateEvent'],
        'order' => ['/orders/:id' => 'updateOrder'],
        'recipe' => ['/recipes/:id' => 'updateRecipe'],
        'service' => ['/services/:id' => 'updateService'],
        'provider' => ['/providers/:id' => 'updateProvider'],
        'workshop' => ['/workshops/:id' => 'updateWorkshop'],
        'formation' => ['/formations/:id' => 'updateFormation'],
        'reservation' => ['/reservations/:id' => 'updateReservation'],
    ],
    'DELETE' =>
    [
        'user' => ['/users/:id' => 'deleteUser'],
        'room' => ['/rooms/:id' => 'deleteRoom'],
        'chat' => ['/chat/:id' => 'deleteMessage'],
        'event' => ['/events/:id' => 'deleteEvent'],
        'order' => ['/orders/:id' => 'deleteOrder'],
        'recipe' => ['/recipes/:id' => 'deleteRecipe'],
        'service' => ['/services/:id' => 'deleteService'],
        'workshop' => ['/workshops/:id' => 'deleteWorkshop'],
        'provider' => ['/providers/:id' => 'deleteProvider'],
        'formation' => ['/formations/:id' => 'deleteFormation'],
        'reservation' => ['/reservations/:id' => 'deleteReservation'],
    ]
];

foreach ($routes as $method => $controllers) {
    foreach ($controllers as $controllerName => $endpoints) {
        foreach ($endpoints as $endpoint => $action) {
            $router->addRoute($method, $endpoint, function ($params) use ($controllerName, $action) {
                $body = getBody();
                switch ($action){
                    case 'me':
                    case 'login':
                    case 'logout':
                    case 'register':
                        $controller = "Recipely\\Auth\\". ucfirst($controllerName);
                        $object = new $controller();
                        $object->$action($body);
                        break;
                    case 'createEvent':
                        $controller = "Recipely\\Controllers\\" . $controllerName . "\\" . ucfirst($controllerName) . "Controllers";
                        $object = new $controller();
                        $id_location = $params['id_location'] ?? null;
                        $object->$action($params['id'],$id_location, $body);
                        break;
                    case 'createProvider':
                        $controller = "Recipely\\Controllers\\" . $controllerName . "\\" . ucfirst($controllerName) . "Controllers";
                        $object = new $controller();
                        // Si le body est vide ça crée une erreur
                        $object->$action($params['id'], $body);
                        break;
                    case 'createRecipe':
                    case 'createFormation':
                        $controller = "Recipely\\Controllers\\" . $controllerName . "\\" . ucfirst($controllerName) . "Controllers";
                        $object = new $controller();
                        $object->$action($params['id_provider'], $body);
                        break;
                    case 'createWorkshop':
                        $controller = "Recipely\\Controllers\\" . $controllerName . "\\" . ucfirst($controllerName) . "Controllers";
                        $object = new $controller();
                        $object->$action($params['id_room'], $params['id_provider'], $body);
                        break;
                    case 'createRoom':
                    case 'createUser':
                    case 'createService':
                        $controller = "Recipely\\Controllers\\" . $controllerName . "\\" . ucfirst($controllerName) . "Controllers";
                        $object = new $controller();
                        $object->$action($body);
                        break;
                    case 'createReservation':
                        $controller = "Recipely\\Controllers\\" . $controllerName . "\\" . ucfirst($controllerName) . "Controllers";
                        $object = new $controller();
                        $object->$action($params['id_event'], $params['id_client'], $body);
                        break;
                    case 'createOrder':
                        $controller = "Recipely\\Controllers\\" . $controllerName . "\\" . ucfirst($controllerName) . "Controllers";
                        $object = new $controller();
                        $object->$action($params['id_client'], $body);
                        break;
                    default:
                        $controller = "Recipely\\Controllers\\" . $controllerName . "\\" . ucfirst($controllerName) . "Controllers";
                        if (class_exists($controller) && method_exists($controller, $action)) {
                            $object = new $controller();
                            $id = $params['id'] ?? null;
                            $object->$action($id, json_encode($body));
                        } else{
                            throw new Exception("Controller or action not found");
                        }
                        break;
                }
            });
        }
    }
}



$request = $_SERVER['REQUEST_URI'];
if (substr($request, -1) === '/') {
    $request = rtrim($request, '/');
}
$method = $_SERVER['REQUEST_METHOD'];

$router->handleRequest($method, $request);