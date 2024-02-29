<?php

declare(strict_types=1);

use App\Controller\TransactionController;
use Hyperf\HttpServer\Router\Router;

Router::post('/clientes/{id}/transacoes', [TransactionController::class, 'store']);
Router::get('/clientes/{id}/extrato', [TransactionController::class, 'index']);
