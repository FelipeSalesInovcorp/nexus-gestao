<?php

namespace App\Actions\Entities;

use App\Models\Entity;
use Illuminate\Support\Facades\DB;

class UpdateEntityAction
{
    public function execute(Entity $entity, array $data): Entity
    {
        return DB::transaction(function () use ($entity, $data) {
            $entity->update($data);
            return $entity->fresh();
        });
    }
}
