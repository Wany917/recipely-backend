<?php
spl_autoload_register(function ($class_name) {
    $base_dir = __DIR__;
    $directories = [

        'Recipely\\Lib' => 'lib',
        'Recipely\\Auth' => 'auth',
        'Recipely\\Utils' => 'utils',
        'Recipely\\Traits' => 'traits',
        'Recipely\\Routes' => 'routes',
        'Recipely\\Models' => 'models',
        'Recipely\\Database' => 'database',
        'Recipely\\Functions' => 'functions',
        'Recipely\\Config' => 'database/config',
        'Recipely\\Controllers' => 'controllers',
        // COMPOSER ENV VARS
        'dotenv' => 'vendor/vlucas/phpdotenv/src',

        'Recipely\\Models\\user' => 'models/user',
        'Recipely\\Traits\\user' => 'traits/user',

        
        'Recipely\\Traits\\room' => 'traits/room',
        'Recipely\\Models\\room' => 'models/room',
        
        
        'Recipely\\Traits\\chat' => 'traits/chat',
        'Recipely\\Models\\chat' => 'models/chats',
        
        'Recipely\\Models\\order' => 'models/order',
        'Recipely\\Traits\\order' => 'traits/order',
        
        'Recipely\\Models\\event' => 'models/event',
        'Recipely\\Traits\\event' => 'traits/event',
        
        'Recipely\\Traits\\recipe' => 'traits/recipe',
        'Recipely\\Models\\recipe' => 'models/recipe',

        'Recipely\\Models\\service' => 'models/service',
        'Recipely\\Traits\\service' => 'traits/service',

        'Recipely\\Models\\provider' => 'models/provider',
        'Recipely\\Traits\\provider' => 'traits/provider',
        
        'Recipely\\Models\\workshop' => 'models/workshop',
        'Recipely\\Traits\\workshop' => 'traits/workshop',

        'Recipely\\Models\\formation' => 'models/formation',
        'Recipely\\Traits\\formation' => 'traits/formation',
        
        'Recipely\Models\reservation' => 'models/reservation',
        'Recipely\\Traits\\reservation' => 'traits/reservation',
        
        'Recipely\\Contollers\\room' => 'controllers/room',
        'Recipely\\Controllers\\user' => 'controllers/user',
        'Recipely\\Controllers\\chat' => 'controllers/chats',
        'Recipely\\Controllers\\event' => 'controllers/event',
        'Recipely\\Controllers\\order' => 'controllers/order',
        'Recipely\\Controllers\\recipe' => 'controllers/recipe',
        'Recipely\\Controllers\\service' => 'controllers/service',
        'Recipely\\Controllers\\provider' => 'controllers/provider',
        'Recipely\\Controllers\\workshop' => 'controllers/workshop',
        'Recipely\\Controllers\\formation' => 'controllers/formation',
        'Recipely\Controllers\reservation' => 'controllers/reservation'
    ];


    $lastAttemptedFile = "";

    foreach ($directories as $namespace => $dir) {
        if (strpos($class_name, $namespace) === 0) {
            // convert namespace to directory structure
            $directory = '/' . $dir;

            $relative_class = substr($class_name, strlen($namespace));
            $lastAttemptedFile = $base_dir . $directory . str_replace('\\', '/', $relative_class) . '.php';

            // echo "Looking for file: " . $lastAttemptedFile . "\n";

            if (is_file($lastAttemptedFile)) {
                require_once $lastAttemptedFile;
                // echo "Loaded class: " . $class_name . "\n";
                // echo "File path: " . realpath($lastAttemptedFile) . "\n\n";
                return;
            }
        }
    }
    echo "Class not found: " . $class_name . "\n";
    echo "Last attempted file: " . $lastAttemptedFile . "\n\n";
});