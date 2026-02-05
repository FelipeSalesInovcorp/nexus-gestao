<?php

namespace App\Actions\Contacts;

use App\Models\Contact;

class DeleteContactAction
{
    public function execute(Contact $contact): void
    {
        $contact->delete();
    }
}
