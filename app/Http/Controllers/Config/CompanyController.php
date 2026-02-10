<?php

namespace App\Http\Controllers\Config;

use App\Actions\Company\GetCompanySettingAction;
use App\Actions\Company\UpdateCompanySettingsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Company\UpdateCompanyRequest;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function edit(GetCompanySettingAction $getCompanySetting)
    {
        $company = $getCompanySetting->execute();

        return Inertia::render('Config/Company/Edit', [
            'company' => $company,
            'logoUrl' => $company?->logo_path ? route('company.logo') : null,
        ]);
    }

    public function update(UpdateCompanyRequest $request, UpdateCompanySettingsAction $updateCompanySettings)
    {
        $payload = $request->validatedPayload();
        $updateCompanySettings->execute(
            $payload,
            $request->file('logo')
        );

        return redirect()
            ->route('config.company.edit')
            ->with('success', 'Dados da empresa atualizados.');
    }

    /**
     * Serve o logotipo diretamente do storage privado.
     * (Não fica dentro de public_html.)
     */
    public function logo(GetCompanySettingAction $getCompanySetting)
    {
        $company = $getCompanySetting->execute();

        if (!$company || !$company->logo_path || !Storage::disk('private')->exists($company->logo_path)) {
            abort(404);
        }

        return Storage::disk('private')->response($company->logo_path);
    }
}
