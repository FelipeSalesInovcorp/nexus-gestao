<?php

namespace App\Http\Controllers;

use App\Actions\Contacts\CreateContactAction;
use App\Actions\Contacts\DeleteContactAction;
use App\Models\Contact;
use App\Models\Entity;
use Illuminate\Http\Request;

class EntityContactController extends Controller
{
    public function store(Request $request, Entity $entity, CreateContactAction $action)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => ['nullable', 'string', 'max:100'],
            'is_primary' => ['sometimes','boolean'],
        ]);

        $action->execute($entity, $data);

        return redirect()->back()->with('success', 'Contacto criado com sucesso.');
    }

    public function destroy(Entity $entity, Contact $contact, DeleteContactAction $action)
    {
        // Segurança: garantir que o contacto pertence à entidade
        abort_unless($contact->entity_id === $entity->id, 404);

        $action->execute($contact);

        return redirect()->back()->with('success', 'Contacto removido com sucesso.');
    }
}
