<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\GalleryCategory;
use App\Models\GalleryImage;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $categories = GalleryCategory::query()
            ->active()
            ->withCount('images')
            ->get();

        $query = GalleryImage::query()
            ->active()
            ->with('category');

        // Optional filter: ?category=kids
        if ($request->input('category')) {
            $slug = $request->input('category');
            $query->whereHas('category', function ($q) use ($slug) {
                $q->where('slug', $slug);
            });
        }

        $images = $query->orderBy('display_order')->paginate(24);

        return view('web.gallery.index', [
            'categories' => $categories,
            'images' => $images,
        ]);
    }
}
