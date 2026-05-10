<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use App\Models\User;
use App\Filament\Pages\Hostel\Users\AllUsers;
use App\Filament\Pages\Hostel\Users\EditUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\Unique;
use Illuminate\View\View;

class UserController extends Controller
{
    public function create(): View
    {
        return view('users.create', [
            'designations' => $this->activeDesignations(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::in(['Super admin', 'admin', 'staff', 'guest'])],
            'designation_id' => ['required', $this->activeDesignationRule()],
            'cadre_number' => ['required', 'string', 'max:255'],
        ]);

        User::create([
            'name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
            'designation_id' => $validated['designation_id'],
            'cadre_number' => $validated['cadre_number'],
            'password' => Hash::make(Str::random(32)),
        ]);

        return redirect()
            ->to(AllUsers::getUrl(panel: 'admin'))
            ->with('success', __('User created successfully.'));
    }

    public function edit(User $user): RedirectResponse
    {
        return redirect()->to(EditUser::getUrl(['id' => $user->id], panel: 'admin'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', $this->uniqueUserEmailRule($user)],
            'phone' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::in(['Super admin', 'admin', 'staff', 'guest'])],
            'designation_id' => ['required', $this->activeDesignationRule()],
            'cadre_number' => ['required', 'string', 'max:255'],
        ]);

        $user->update([
            'name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
            'designation_id' => $validated['designation_id'],
            'cadre_number' => $validated['cadre_number'],
        ]);

        return redirect()
            ->to(AllUsers::getUrl(panel: 'admin'))
            ->with('success', __('User updated successfully.'));
    }

    private function activeDesignations()
    {
        return Designation::query()
            ->orderBy('name')
            ->get();
    }

    private function activeDesignationRule(): Exists
    {
        return Rule::exists('designations', 'id');
    }

    private function uniqueUserEmailRule(User $user): Unique
    {
        return Rule::unique('users', 'email')->ignore($user->id);
    }
}
