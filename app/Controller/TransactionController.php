<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\TransactionService;
use Carbon\Carbon;
use Hyperf\Validation\Rule;

class TransactionController extends AbstractController
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(string $id)
    {
        $now = Carbon::now();

        $customer = $this->transactionService->getCustomer(intval($id));
        if (! $customer) {
            return $this->response->withStatus(404);
        }

        $lastTransactions = $this->transactionService->getLastTransactions($customer->id);

        return $this->response->json([
            'saldo' => [
                'total' => intval($customer->saldo),
                'data_extrato' => $now->toIso8601String(),
                'limite' => intval($customer->limite),
            ],
            'ultimas_transacoes' => $lastTransactions,
        ]);
    }

    public function store(string $id)
    {
        $validator = $this->validationFactory->make(
            $this->request->all(),
            [
                'valor' => ['required', 'integer', 'min:0'],
                'tipo' => ['required', Rule::in(['c', 'd'])],
                'descricao' => ['required', 'min:1', 'max:10'],
            ],
        );

        if ($validator->fails()) {
            return $this->response->withStatus(422);
        }

        $customer = $this->transactionService->getCustomer(intval($id));
        if (! $customer) {
            return $this->response->withStatus(404);
        }

        $amount = $this->request->post('valor');
        $type = $this->request->post('tipo');
        $description = $this->request->post('descricao');

        try {
            $this->transactionService->create(
                intval($customer->id),
                intval($amount),
                $type,
                $description,
            );

            $updatedCustomer = $this->transactionService->getCustomer(intval($id));

            return $this->response->json([
                'limite' => intval($customer->limite),
                'saldo' => intval($updatedCustomer->saldo),
            ]);
        } catch (\Exception $e) {
            return $this->response->withStatus(422);
        }
    }
}
