<?php

namespace App\Actions\Company;

use App\Models\CompanySetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadCompanyLogoAction
{
    public function execute(CompanySetting $company, UploadedFile $file): void
    {
        // Remove o anterior, caso exista
        if ($company->logo_path) {
            Storage::disk('private')->delete($company->logo_path);
        }

        $company->logo_path = $file->store('company', 'private');
    }
}
