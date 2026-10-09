<?php

namespace App\Http\Controllers;

use App\Models\ContactSettings;
use App\Support\Schema\OrganizationSchema;
use Illuminate\Contracts\View\View;

class ContactController extends Controller
{
    public function index(OrganizationSchema $organizationSchema): View
    {
        $settings = ContactSettings::query()->find(1);

        return view('pages.contacts', [
            'settings' => $settings,
            'organizationSchema' => $organizationSchema->make($settings),
            'mapboxPublicToken' => config('services.mapbox.public_token'),
        ]);
    }
}
