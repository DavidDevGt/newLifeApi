<?php

namespace App\Models;

use App\Models\BaseModel;

class Expense extends BaseModel
{
    protected string $table = 'expenses';

    /**
     * Traer gastos por categoría
     */
    public function getByCategory(int $categoryId): array
    {
        return $this->where(['category_id' => $categoryId]) ?? [];
    }

    /**
     * Traer gastos dentro de un rango de fechas
     */
    public function getByDateRange(string $startDate, string $endDate): array
    {
        return $this->where([
            'transaction_date[BETWEEN]' => [$startDate, $endDate]
        ]) ?? [];
    }

    /**
     * Obtiene gastos con su categoría asociada
     */
    public function getWithCategory(): array
    {
        return $this->db->select(
            $this->table,
            [
                '[>]categories' => ['category_id' => 'id'] // LEFT JOIN para incluir gastos sin categoría
            ],
            [
                "{$this->table}.*",
                'category_name' => 'categories.name'
            ]
        ) ?? [];
    }
}
