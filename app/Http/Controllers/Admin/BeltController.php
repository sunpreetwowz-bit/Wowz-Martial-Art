<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBeltRequest;
use App\Http\Requests\Admin\UpdateBeltRequest;
use App\Models\Belt;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class BeltController extends Controller
{
    protected $auditLogger;

    public function __construct(AuditLogger $auditLogger)
    {
        $this->authorizeResource(Belt::class, 'belt');
        $this->auditLogger = $auditLogger;
    }

    public function index(Request $request)
    {
        $query = Belt::query()->withCount('students');

        if ($request->input('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('color', 'like', '%'.$search.'%');
            });
        }

        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        $belts = $query->orderBy('rank_order')->paginate(15);

        return view('admin.belts.index', [
            'belts' => $belts,
        ]);
    }

    public function create()
    {
        $nextRank = (int) Belt::query()->max('rank_order') + 1;

        return view('admin.belts.create', [
            'nextRank' => $nextRank,
            'statuses' => AccountStatus::cases(),
        ]);
    }

    public function store(StoreBeltRequest $request)
    {
        $belt = Belt::query()->create($request->validated());

        $this->auditLogger->log('belt.created', $belt, null, $belt->toArray());

        return redirect()
            ->route('admin.belts.index')
            ->with('success', "Belt \"{$belt->name}\" created successfully.");
    }

    public function edit(Belt $belt)
    {
        return view('admin.belts.edit', [
            'belt' => $belt,
            'statuses' => AccountStatus::cases(),
        ]);
    }

    public function update(UpdateBeltRequest $request, Belt $belt)
    {
        $old = $belt->toArray();
        $belt->update($request->validated());

        $this->auditLogger->log('belt.updated', $belt, $old, $belt->fresh()->toArray());

        return redirect()
            ->route('admin.belts.index')
            ->with('success', "Belt \"{$belt->name}\" updated successfully.");
    }

    public function destroy(Belt $belt)
    {
        if ($belt->students()->exists()) {
            return back()->with('error', 'Cannot delete a belt that is assigned to students. Deactivate it instead.');
        }

        $name = $belt->name;
        $belt->delete();

        $this->auditLogger->log('belt.deleted', null, null, ['name' => $name]);

        return redirect()
            ->route('admin.belts.index')
            ->with('success', "Belt \"{$name}\" deleted successfully.");
    }
}
