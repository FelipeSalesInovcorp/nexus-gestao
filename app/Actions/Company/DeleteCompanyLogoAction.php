<?php

namespace App\Actions\Company;

use App\Models\CompanySetting;
use Illuminate\Support\Facades\Storage;

class DeleteCompanyLogoAction
{
    public function execute(CompanySetting $company): void
    {
        if (!$company->logo_path) {
            return;
        }

        Storage::disk('private')->delete($company->logo_path);
        $company->logo_path = null;
    }
}
