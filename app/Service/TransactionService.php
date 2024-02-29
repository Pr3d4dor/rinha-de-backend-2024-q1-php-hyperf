<?php

declare(strict_types=1);

namespace App\Service;

use Carbon\Carbon;
use Hyperf\DbConnection\Db;

class TransactionService
{
    public function getCustomer(int $customerId): ?object
    {
        return Db::table('clientes')
            ->where('id', $customerId)
            ->first();
    }

    public function getLastTransactions(int $customerId, int $limit = 10): array
    {
        return Db::table('transacoes')
            ->select(['valor', 'tipo', 'descricao', 'realizada_em'])
            ->where('cliente_id', $customerId)
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                return [
                    'valor' => intval($row->valor),
                    'tipo' => $row->tipo,
                    'descricao' => $row->descricao,
                    'realizada_em' => Carbon::parse($row->realizada_em)->format('Y-m-d\TH:i:s\Z'),
                ];
            })
            ->toArray();
    }

    public function create(
        int $customerId,
        int $amount,
        string $type,
        string $description
    ): bool {
        return Db::table('transacoes')
            ->insert([
                'cliente_id' => $customerId,
                'valor' => $amount,
                'tipo' => $type,
                'descricao' => $description,
            ]);
    }
}
