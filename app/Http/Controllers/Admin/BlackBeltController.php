<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBlackBeltRequest;
use App\Http\Requests\Admin\UpdateBlackBeltRequest;
use App\Models\BlackBelt;
use App\Support\MediaUploader;
use Illuminate\Http\Request;

class BlackBeltController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(BlackBelt::class, 'black_belt');
    }

    public function index(Request $request)
    {
        $query = BlackBelt::query();

        if ($request->input('q')) {
            $query->where('name', 'like', '%'.$request->input('q').'%');
        }

        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        $blackBelts = $query->orderBy('display_order')->orderByDesc('promoted_on')->paginate(15);

        return view('admin.black-belts.index', [
            'blackBelts' => $blackBelts,
        ]);
    }

    public function create()
    {
        return view('admin.black-belts.create', [
            'statuses' => AccountStatus::cases(),
            'branches' => config('academy.branches', []),
        ]);
    }

    public function store(StoreBlackBeltRequest $request)
    {
        $data = $request->validated();
        $data['photo_path'] = MediaUploader::store($request->file('photo'), 'black-belts');
        $data['display_order'] = $data['display_order'] ?? 0;
        unset($data['photo']);

        BlackBelt::query()->create($data);

        return redirect()->route('admin.black-belts.index')->with('success', 'Black belt profile created.');
    }

    public function edit(BlackBelt $black_belt)
    {
        return view('admin.black-belts.edit', [
            'blackBelt' => $black_belt,
            'statuses' => AccountStatus::cases(),
            'branches' => config('academy.branches', []),
        ]);
    }

    public function update(UpdateBlackBeltRequest $request, BlackBelt $black_belt)
    {
        $data = $request->validated();
        $data['photo_path'] = MediaUploader::store($request->file('photo'), 'black-belts', $black_belt->photo_path);
        unset($data['photo']);
        $black_belt->update($data);

        return redirect()->route('admin.black-belts.index')->with('success', 'Black belt profile updated.');
    }

    public function destroy(BlackBelt $black_belt)
    {
        $black_belt->update(['status' => AccountStatus::Inactive]);
        $black_belt->delete();

        return redirect()->route('admin.black-belts.index')->with('success', 'Black belt profile archived.');
    }
}
