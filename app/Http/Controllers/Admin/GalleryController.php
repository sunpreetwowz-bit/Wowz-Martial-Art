<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountStatus;
use App\Http\Controllers\Controller;
use App\Models\GalleryCategory;
use App\Models\GalleryImage;
use App\Support\MediaUploader;
use App\Support\Slug;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GalleryController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', GalleryCategory::class);

        return view('admin.gallery.index', [
            'categories' => GalleryCategory::query()->withCount('images')->orderBy('display_order')->get(),
            'images' => GalleryImage::query()->with('category')->orderBy('display_order')->paginate(20),
            'statuses' => AccountStatus::cases(),
        ]);
    }

    public function storeCategory(Request $request)
    {
        $this->authorize('create', GalleryCategory::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', Rule::enum(AccountStatus::class)],
        ]);

        $data['slug'] = Slug::unique($data['name'], 'gallery_categories');
        $data['display_order'] = $data['display_order'] ?? 0;

        GalleryCategory::query()->create($data);

        return back()->with('success', 'Gallery category created.');
    }

    public function storeImage(Request $request)
    {
        $this->authorize('create', GalleryImage::class);

        $data = $request->validate([
            'gallery_category_id' => ['nullable', 'exists:gallery_categories,id'],
            'title' => ['nullable', 'string', 'max:150'],
            'caption' => ['nullable', 'string', 'max:255'],
            'image' => ['required', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', Rule::enum(AccountStatus::class)],
        ]);

        $data['image_path'] = MediaUploader::store($request->file('image'), 'gallery');
        $data['display_order'] = $data['display_order'] ?? 0;
        unset($data['image']);

        GalleryImage::query()->create($data);

        return back()->with('success', 'Gallery image uploaded.');
    }

    public function destroyImage(GalleryImage $image)
    {
        $this->authorize('delete', $image);
        $image->delete();

        return back()->with('success', 'Image removed.');
    }

    public function destroyCategory(GalleryCategory $category)
    {
        $this->authorize('delete', $category);
        $category->delete();

        return back()->with('success', 'Category removed.');
    }
}
