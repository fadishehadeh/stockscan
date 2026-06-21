<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(private readonly ActivityLogService $activityLogService)
    {
    }

    public function index(): View
    {
        return view('users.index', [
            'users' => User::query()->orderBy('role')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = User::create($this->validatedData($request));

        $this->activityLogService->record('user.created', 'Created user ' . $user->name . '.', $request->user(), $user);

        return back()->with('success', 'User created.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validatedData($request, $user->id, false);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        $this->activityLogService->record('user.updated', 'Updated user ' . $user->name . '.', $request->user(), $user);

        return back()->with('success', 'User updated.');
    }

    private function validatedData(Request $request, ?int $userId = null, bool $passwordRequired = true): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($userId)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'role' => ['required', 'in:owner,staff'],
            'is_active' => ['nullable', 'boolean'],
            'password' => [
                $passwordRequired ? 'required' : 'nullable',
                'string',
                Password::min(10)->letters()->mixedCase()->numbers()->symbols(),
            ],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
