<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PublishStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAchievementRequest;
use App\Http\Requests\Admin\UpdateAchievementRequest;
use App\Models\Achievement;
use App\Models\Student;
use App\Support\MediaUploader;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Achievement::class, 'achievement');
    }

    public function index(Request $request)
    {
        $query = Achievement::query()->with('student.user');

        if ($request->input('q')) {
            $query->where('title', 'like', '%'.$request->input('q').'%');
        }

        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        $achievements = $query->latest('achieved_on')->paginate(15);

        return view('admin.achievements.index', [
            'achievements' => $achievements,
            'statuses' => PublishStatus::cases(),
        ]);
    }

    public function create()
    {
        return view('admin.achievements.create', [
            'statuses' => PublishStatus::cases(),
            'students' => Student::query()->with('user')->orderBy('student_code')->get(),
        ]);
    }

    public function store(StoreAchievementRequest $request)
    {
        $data = $request->validated();
        $data['image_path'] = MediaUploader::store($request->file('image'), 'achievements');
        $data['document_path'] = MediaUploader::store($request->file('document'), 'achievements/documents');
        unset($data['image'], $data['document']);

        Achievement::query()->create($data);

        return redirect()->route('admin.achievements.index')->with('success', 'Achievement created.');
    }

    public function edit(Achievement $achievement)
    {
        return view('admin.achievements.edit', [
            'achievement' => $achievement,
            'statuses' => PublishStatus::cases(),
            'students' => Student::query()->with('user')->orderBy('student_code')->get(),
        ]);
    }

    public function update(UpdateAchievementRequest $request, Achievement $achievement)
    {
        $data = $request->validated();
        $data['image_path'] = MediaUploader::store($request->file('image'), 'achievements', $achievement->image_path);
        $data['document_path'] = MediaUploader::store($request->file('document'), 'achievements/documents', $achievement->document_path);
        unset($data['image'], $data['document']);
        $achievement->update($data);

        return redirect()->route('admin.achievements.index')->with('success', 'Achievement updated.');
    }

    public function destroy(Achievement $achievement)
    {
        $achievement->delete();

        return redirect()->route('admin.achievements.index')->with('success', 'Achievement archived.');
    }
}
