<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServiceRequest;
use App\Http\Requests\Admin\UpdateServiceRequest;
use App\Models\Service;
use App\Support\MediaUploader;
use App\Support\Slug;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Service::class, 'service');
    }

    public function index(Request $request)
    {
        $query = Service::query();

        // Search by name
        if ($request->input('q')) {
            $query->where('name', 'like', '%'.$request->input('q').'%');
        }

        // Filter by status
        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        $services = $query->orderBy('display_order')->paginate(15);

        return view('admin.services.index', [
            'services' => $services,
        ]);
    }

    public function create()
    {
        return view('admin.services.create', [
            'statuses' => AccountStatus::cases(),
        ]);
    }

    public function store(StoreServiceRequest $request)
    {
        $data = $request->validated();

        // Make a unique slug from the custom slug or the name
        if (! empty($data['slug'])) {
            $data['slug'] = Slug::unique($data['slug'], 'services');
        } else {
            $data['slug'] = Slug::unique($data['name'], 'services');
        }

        $data['image_path'] = MediaUploader::store($request->file('image'), 'services');
        $data['display_order'] = $data['display_order'] ?? 0;
        unset($data['image']);

        Service::query()->create($data);

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Service created successfully.');
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', [
            'service' => $service,
            'statuses' => AccountStatus::cases(),
        ]);
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $data = $request->validated();

        $slugSource = ! empty($data['slug']) ? $data['slug'] : $data['name'];
        $data['slug'] = Slug::unique($slugSource, 'services', 'slug', $service->id);
        $data['image_path'] = MediaUploader::store($request->file('image'), 'services', $service->image_path);
        unset($data['image']);

        $service->update($data);

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        $service->update(['status' => AccountStatus::Inactive]);
        $service->delete();

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Service archived successfully.');
    }
}
