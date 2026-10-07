<?php

namespace App\Http\Controllers\Web;

use App\Enums\AccountStatus;
use App\Http\Controllers\Controller;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::query()->active()->orderBy('display_order')->get();

        return view('web.services.index', [
            'services' => $services,
        ]);
    }

    public function show(Service $service)
    {
        if ($service->status !== AccountStatus::Active) {
            abort(404);
        }

        $related = Service::query()
            ->active()
            ->where('id', '!=', $service->id)
            ->orderBy('display_order')
            ->take(3)
            ->get();

        return view('web.services.show', [
            'service' => $service,
            'related' => $related,
        ]);
    }
}
