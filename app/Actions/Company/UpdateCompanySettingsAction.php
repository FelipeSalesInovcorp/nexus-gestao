<?php

namespace App\Actions\Company;

use App\Models\CompanySetting;
use Illuminate\Http\UploadedFile;

class UpdateCompanySettingsAction
{
    public function __construct(
        private readonly UploadCompanyLogoAction $uploadCompanyLogo,
        private readonly DeleteCompanyLogoAction $deleteCompanyLogo,
    ) {}

    /**
     * @param  array{ name:?string,address:?string,postal_code:?string,locality:?string,tax_number:?string,remove_logo:bool }  $payload
     */
    public function execute(array $payload, ?UploadedFile $logoFile): CompanySetting
    {
        $company = CompanySetting::query()->first() ?? new CompanySetting();

        // Logo removido
        if (($payload['remove_logo'] ?? false) && $company->logo_path) {
            $this->deleteCompanyLogo->execute($company);
        }

        // Upload novo logo
        if ($logoFile) {
            $this->uploadCompanyLogo->execute($company, $logoFile);
        }

        $company->fill([
            'name' => $payload['name'] ?? null,
            'address' => $payload['address'] ?? null,
            'postal_code' => $payload['postal_code'] ?? null,
            'locality' => $payload['locality'] ?? null,
            'tax_number' => $payload['tax_number'] ?? null,
        ]);

        $company->save();

        return $company;
    }
}
