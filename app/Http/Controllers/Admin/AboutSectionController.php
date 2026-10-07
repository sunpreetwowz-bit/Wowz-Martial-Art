<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAboutSectionRequest;
use App\Models\AboutSection;
use App\Support\MediaUploader;

class AboutSectionController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', AboutSection::class);

        $sections = AboutSection::query()->orderBy('display_order')->get();

        return view('admin.about.index', compact('sections'));
    }

    public function edit(AboutSection $about)
    {
        $this->authorize('update', $about);

        return view('admin.about.edit', [
            'section' => $about,
            'statuses' => AccountStatus::cases(),
        ]);
    }

    public function update(UpdateAboutSectionRequest $request, AboutSection $about)
    {
        $this->authorize('update', $about);

        $data = $request->validated();
        $data['image_path'] = MediaUploader::store($request->file('image'), 'about', $about->image_path);
        unset($data['image']);
        $about->update($data);

        return redirect()->route('admin.about.index')->with('success', 'About section updated.');
    }
}
