<?php

declare(strict_types=1);

return [
    'handler' => [
        'http' => [
            Hyperf\Validation\ValidationExceptionHandler::class,
            Hyperf\HttpServer\Exception\Handler\HttpExceptionHandler::class,
            App\Exception\Handler\AppExceptionHandler::class,
        ],
    ],
];
