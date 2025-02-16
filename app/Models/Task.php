<?php

namespace App\Models;

use App\Models\BaseModel;

class Task extends BaseModel
{
    protected string $table = 'tasks';

    /**
     * Traer tareas con estado "pending"
     */
    public function getPending(): array
    {
        return $this->where(['status' => 'pending']) ?? [];
    }

    /**
     * Traer tareas según la prioridad (low, medium, high)
     */
    public function getByPriority(string $priority): array
    {
        $validPriorities = ['low', 'medium', 'high'];

        if (!in_array($priority, $validPriorities, true)) {
            return [];
        }

        return $this->where(['priority' => $priority]) ?? [];
    }
}
