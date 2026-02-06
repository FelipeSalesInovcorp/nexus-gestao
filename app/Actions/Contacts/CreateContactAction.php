<?php

namespace App\Actions\Contacts;

use App\Models\Contact;
use App\Models\Entity;

class CreateContactAction
{
    public function execute(Entity $entity, array $data): Contact
    {
        // (Opcional) se marcar is_primary, desmarca os outros
        if (!empty($data['is_primary'])) {
            $entity->contacts()->update(['is_primary' => false]);
        }

        return $entity->contacts()->create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'contact_role_id' => $data['contact_role_id'] ?? null,
            'role' => $data['role'] ?? null,
            'is_primary' => (bool)($data['is_primary'] ?? false),
        ]);
    }
}
