<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BannerTypeEnum;
use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/User/UserIndex')->with([
            'users' => fn () => User::withCount('recipes')
                ->orderBy('id', 'desc')
                ->get(),
            'roles' => UserRoleEnum::toSelectOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'max:140'],
            'email' => ['required', 'email', Rule::unique('users')],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
            'role' => ['nullable', 'string', 'in:'.collect(UserRoleEnum::cases())->implode(fn ($r) => $r->value, ',')],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => UserRoleEnum::tryFrom($validated['role']),
        ]);

        return redirect()->route('admin.users.index')->withBanner("Successfully created {$user->name}");
    }

    public function edit(User $user)
    {
        return Inertia::render('Admin/User/UserEdit')->with([
            'user' => fn () => $user->makeVisible('id'),
            'roles' => fn () => UserRoleEnum::toSelectOptions(),
        ]);
    }

    public function update(User $user, Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'max:140'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => ['nullable', 'string', 'in:'.collect(UserRoleEnum::cases())->implode(fn ($r) => $r->value, ',')],
        ]);

        $user->update($validated);

        return redirect()->back()->withBanner("Successfully updated {$user->name}");
    }

    public function create()
    {
        return Inertia::render('Admin/User/UserEdit')->with([
            'user' => fn () => new User(),
            'roles' => fn () => UserRoleEnum::toSelectOptions(),
        ]);
    }

    public function destroy(User $user)
    {
        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->withBanner("Deleted {$name}", BannerTypeEnum::danger);
    }
}
