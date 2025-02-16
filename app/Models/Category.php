<?php

namespace App\Models;

use App\Models\BaseModel;

class Category extends BaseModel
{
    protected string $table = 'categories';

    /**
     * Traer categorías por tipo (expense/income)
     */
    public function getByType(string $type): array
    {
        $validTypes = ['expense', 'income'];

        if (!in_array($type, $validTypes, true)) {
            return [];
        }

        return $this->where(['type' => $type]) ?? [];
    }
}
