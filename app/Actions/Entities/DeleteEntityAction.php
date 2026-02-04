<?php

namespace App\Actions\Entities;

use App\Models\Entity;

class DeleteEntityAction
{
    public function execute(Entity $entity): void
    {
        $entity->delete(); // soft delete
    }
}
