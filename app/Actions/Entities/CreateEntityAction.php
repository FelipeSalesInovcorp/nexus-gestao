<?php

namespace App\Actions\Entities;

use App\Models\Entity;
use Illuminate\Support\Facades\DB;

class CreateEntityAction
{
    public function execute(array $data): Entity
    {
        return DB::transaction(function () use ($data) {
            // número incremental (simples e funcional para já)
            $data['number'] = (Entity::max('number') ?? 0) + 1;

            return Entity::create($data);
        });
    }
}
