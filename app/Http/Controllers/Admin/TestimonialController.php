<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTestimonialRequest;
use App\Http\Requests\Admin\UpdateTestimonialRequest;
use App\Models\Testimonial;
use App\Support\MediaUploader;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Testimonial::class, 'testimonial');
    }

    public function index(Request $request)
    {
        $query = Testimonial::query();

        if ($request->input('q')) {
            $query->where('author_name', 'like', '%'.$request->input('q').'%');
        }

        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        $testimonials = $query->orderBy('display_order')->paginate(15);

        return view('admin.testimonials.index', [
            'testimonials' => $testimonials,
        ]);
    }

    public function create()
    {
        return view('admin.testimonials.create', ['statuses' => AccountStatus::cases()]);
    }

    public function store(StoreTestimonialRequest $request)
    {
        $data = $request->validated();
        $data['photo_path'] = MediaUploader::store($request->file('photo'), 'testimonials');
        $data['display_order'] = $data['display_order'] ?? 0;
        unset($data['photo']);

        Testimonial::query()->create($data);

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial created.');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.edit', [
            'testimonial' => $testimonial,
            'statuses' => AccountStatus::cases(),
        ]);
    }

    public function update(UpdateTestimonialRequest $request, Testimonial $testimonial)
    {
        $data = $request->validated();
        $data['photo_path'] = MediaUploader::store($request->file('photo'), 'testimonials', $testimonial->photo_path);
        unset($data['photo']);
        $testimonial->update($data);

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial updated.');
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial deleted.');
    }
}
