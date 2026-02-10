<?php

namespace App\Actions\Company;

use App\Models\CompanySetting;

class GetCompanySettingAction
{
    public function execute(): ?CompanySetting
    {
        return CompanySetting::query()->first();
    }
}
