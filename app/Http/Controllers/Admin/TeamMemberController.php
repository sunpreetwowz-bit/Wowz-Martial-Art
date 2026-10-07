<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTeamMemberRequest;
use App\Http\Requests\Admin\UpdateTeamMemberRequest;
use App\Models\TeamMember;
use App\Support\MediaUploader;
use Illuminate\Http\Request;

class TeamMemberController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(TeamMember::class, 'team_member');
    }

    public function index(Request $request)
    {
        $query = TeamMember::query();

        if ($request->input('q')) {
            $query->where('name', 'like', '%'.$request->input('q').'%');
        }

        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        $members = $query->orderBy('display_order')->paginate(15);

        return view('admin.team-members.index', [
            'members' => $members,
        ]);
    }

    public function create()
    {
        return view('admin.team-members.create', ['statuses' => AccountStatus::cases()]);
    }

    public function store(StoreTeamMemberRequest $request)
    {
        $data = $request->validated();
        $data['photo_path'] = MediaUploader::store($request->file('photo'), 'team');
        $data['display_order'] = $data['display_order'] ?? 0;
        unset($data['photo']);

        TeamMember::query()->create($data);

        return redirect()->route('admin.team-members.index')->with('success', 'Team member created.');
    }

    public function edit(TeamMember $team_member)
    {
        return view('admin.team-members.edit', [
            'member' => $team_member,
            'statuses' => AccountStatus::cases(),
        ]);
    }

    public function update(UpdateTeamMemberRequest $request, TeamMember $team_member)
    {
        $data = $request->validated();
        $data['photo_path'] = MediaUploader::store($request->file('photo'), 'team', $team_member->photo_path);
        unset($data['photo']);
        $team_member->update($data);

        return redirect()->route('admin.team-members.index')->with('success', 'Team member updated.');
    }

    public function destroy(TeamMember $team_member)
    {
        $team_member->update(['status' => AccountStatus::Inactive]);
        $team_member->delete();

        return redirect()->route('admin.team-members.index')->with('success', 'Team member archived.');
    }
}
