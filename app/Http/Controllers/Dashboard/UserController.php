<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\RoleUser;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view', User::class);
        $users = User::paginate(10);

        return view('dashboard.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', User::class);
        $user = new User;

        return view('dashboard.users.create', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $request->validate([
            'name' => 'required',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|same:confirm_password',
            'confirm_password' => 'required|same:password',
        ], [
            'password.same' => 'كلمة المرور غير متطابقة',
            'confirm_password.same' => 'كلمة المرور غير متطابقة',
        ]);

        DB::beginTransaction();
        try {
            if ($request->has('avatar')) {
                $avatar = $request->file('avatar');
                $path = $avatar->store('avatars', 'public');
                $request->merge(['avatar' => $path]);
            }
            $user = User::create($request->all());
            foreach ($request->input('abilities', []) as $role) {
                RoleUser::create([
                    'role_name' => $role,
                    'user_id' => $user->id,
                    'ability' => 'allow',
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('dashboard.users.index')->with('success', 'تم اضافة مستخدم جديد');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {

        if (Auth::user()->id != $user->id && ! Auth::user()->can('view', User::class)) {
            abort(403);
        }
        $profile = Auth::user()->id == $user->id && ! Auth::user()->can('view', User::class) ? true : false;
        $logs = ActivityLog::where('user_id', $user->id)->orderBy('created_at', 'DESC')->paginate(20);

        return view('dashboard.users.show', compact('user', 'logs', 'profile'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function settings(Request $request)
    {
        $user = Auth::user();
        if (Auth::user()->id != $user->id && ! Auth::user()->can('update', User::class)) {
            abort(403);
        }
        $btn_label = 'تعديل';
        $settings_profile = true;

        return view('dashboard.users.settings', compact('user', 'btn_label', 'settings_profile'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, User $user)
    {
        $this->authorize('update', User::class);
        $btn_label = 'تعديل';

        return view('dashboard.users.edit', compact('user', 'btn_label'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        if (Auth::user()->id != $user->id && ! Auth::user()->can('update', User::class)) {
            abort(403);
        }
        $request->validate([
            'name' => 'required',
            'username' => 'required|string|unique:users,username,'.$user->id,
        ]);
        DB::beginTransaction();
        try {
            $userOld = $user->toArray();
            $oldAvatar = $user->avatar;
            if ($request->has('avatarUpload')) {
                if ($oldAvatar != null) {
                    Storage::disk('public')->delete($oldAvatar);
                }
                $avatar = $request->file('avatarUpload');
                $path = $avatar->store('avatars', 'public');
                $request->merge(['avatar' => $path]);
            }
            $avatar = $request->avatar ?? $user->avatar;
            if (isset($request->password)) {
                $user->update($request->all());
            }

            $user->update([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'avatar' => $avatar ?? null,
                'is_active' => $request->is_active ?? $user->is_active,
            ]);
            $this->syncUserAbilities($user, $request->input('abilities', []));
            ActivityLogService::log(
                'Updated',
                'User',
                "تم تحديث المستخدم : {$user->name}.",
                $userOld,
                $user->getChanges()
            );
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        if (Auth::user()->id == $user->id) {
            return redirect()->route('dashboard.home')->with('success', 'تم تعديل المستخدم');
        }

        return redirect()->route('dashboard.users.index')->with('success', 'تم تعديل المستخدم');
    }

    private function syncUserAbilities(User $user, array $abilities): void
    {
        $roleOld = RoleUser::where('user_id', $user->id)->pluck('role_name')->toArray();

        foreach ($roleOld as $role) {
            if (! in_array($role, $abilities, true)) {
                RoleUser::where('user_id', $user->id)->where('role_name', $role)->delete();
            }
        }

        foreach ($abilities as $role) {
            $roleRecord = RoleUser::where('user_id', $user->id)->where('role_name', $role)->first();
            if ($roleRecord === null) {
                RoleUser::create([
                    'role_name' => $role,
                    'user_id' => $user->id,
                    'ability' => 'allow',
                ]);
            } else {
                $roleRecord->update(['ability' => 'allow']);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $user)
    {
        $this->authorize('delete', User::class);
        if ($user->avatar != null) {
            Storage::disk('public')->delete($user->avatar);
        }
        $user->delete();

        return redirect()->route('dashboard.users.index')->with('success', 'تم حذف المستخدم');
    }
}
