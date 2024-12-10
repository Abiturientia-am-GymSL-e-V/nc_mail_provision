<?php
return [
    'routes' => [
        ['name' => 'provision#index', 'url' => '/provision', 'verb' => 'GET'],
        ['name' => 'provision#create', 'url' => '/provision', 'verb' => 'POST'],
        ['name' => 'provision#update', 'url' => '/provision/{id}', 'verb' => 'PUT'],
        ['name' => 'provision#destroy', 'url' => '/provision/{id}', 'verb' => 'DELETE'],
        ['name' => 'provision#getSettings', 'url' => '/settings', 'verb' => 'GET'],
        ['name' => 'provision#updateSettings', 'url' => '/settings', 'verb' => 'POST'],
        ['name' => 'provision#testConnection', 'url' => '/test-connection', 'verb' => 'POST'],
        ['name' => 'provision#show', 'url' => '/provision/{id}', 'verb' => 'GET'],
    ]
];