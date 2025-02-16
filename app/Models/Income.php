<?php

namespace App\Models;

class Income extends BaseModel
{
    protected string $table = 'incomes';

    /**
     * Traer ingresos por categoría
     */
    public function getByCategory(int $categoryId): array
    {
        return $this->where(['category_id' => $categoryId]) ?? [];
    }

    /**
     * Traer ingresos dentro de un rango de fechas
     */
    public function getByDateRange(string $startDate, string $endDate): array
    {
        return $this->where([
            'transaction_date[BETWEEN]' => [$startDate, $endDate]
        ]) ?? [];
    }

    /**
     * Traer ingresos con su categoría asociada
     */
    public function getWithCategory(): array
    {
        return $this->db->select(
            $this->table,
            [
                '[>]categories' => ['category_id' => 'id'] // LEFT JOIN para incluir ingresos sin categoría
            ],
            [
                "{$this->table}.*",
                'category_name' => 'categories.name'
            ]
        ) ?? [];
    }
}
