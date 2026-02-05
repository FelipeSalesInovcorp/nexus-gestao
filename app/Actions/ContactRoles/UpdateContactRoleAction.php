<?php

namespace App\Actions\ContactRoles;

use App\Models\ContactRole;

class UpdateContactRoleAction
{
    public function execute(ContactRole $role, array $data): ContactRole
    {
        $role->update([
            'name' => $data['name'],
            'active' => (bool) ($data['active'] ?? true),
        ]);

        return $role;
    }
}
