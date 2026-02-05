<?php

namespace App\Actions\ContactRoles;

use App\Models\ContactRole;

class DeleteContactRoleAction
{
    public function execute(ContactRole $role): void
    {
        $role->delete();
    }
}
