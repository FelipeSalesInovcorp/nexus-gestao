<?php

namespace App\Http\Controllers;

use App\Services\OnboardingChecklistService;
use Inertia\Inertia;

class OnboardingChecklistController extends Controller
{
    public function index(OnboardingChecklistService $service)
    {
        return Inertia::render('Onboarding/Checklist', $service->build());
    }
}
