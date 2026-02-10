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

        $nextNumber = Contact::max('number');
        $nextNumber = $nextNumber ? ($nextNumber + 1) : 1;

        return $entity->contacts()->create([
            'number' => $data['number'] ?? $nextNumber,
            'name' => $data['name'],
            'surname' => $data['surname'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'mobile' => $data['mobile'] ?? null,
            'contact_role_id' => $data['contact_role_id'] ?? null,
            'role' => $data['role'] ?? null,
            'is_primary' => (bool)($data['is_primary'] ?? false),
            'rgpd_consent' => (bool)($data['rgpd_consent'] ?? false),
            'notes' => $data['notes'] ?? null,
            'active' => (bool)($data['active'] ?? true),
        ]);
    }
}
