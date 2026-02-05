<?php

namespace App\Actions\ContactRoles;

use App\Models\ContactRole;

class CreateContactRoleAction
{
    public function execute(array $data): ContactRole
    {
        return ContactRole::create([
            'name' => $data['name'],
            'active' => (bool) ($data['active'] ?? true),
        ]);
    }
}
