<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request): Response
    {
        $query = User::query();

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by email verification status
        if ($request->filled('status')) {
            if ($request->status === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->status === 'unverified') {
                $query->whereNull('email_verified_at');
            }
        }

        // Order by latest
        $query->orderBy('created_at', 'desc');

        // Paginate with 10 items per page
        $users = $query->paginate(10)->withQueryString();

        return Inertia::render('users/index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): Response
    {
        return Inertia::render('users/create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi data dengan aturan ketat
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Role selalu 'academy' — jangan percaya input dari client
        $validated['role'] = 'academy';

        // Hash password sebelum disimpan
        $validated['password'] = Hash::make($validated['password']);

        // Buat user baru
        User::create($validated);

        return redirect()
            ->route('users.index')
            ->with('flash', [
                'toast' => [
                    'type' => 'success',
                    'message' => 'Pengguna berhasil ditambahkan.',
                ],
            ]);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): Response
    {
        return Inertia::render('users/view', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): Response
    {
        return Inertia::render('users/edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        // Validasi data
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        // Role selalu 'academy' — jangan izinkan diubah dari client
        $validated['role'] = 'academy';

        // Jika password diisi, validasi dan hash
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Password::defaults()],
            ]);

            $validated['password'] = Hash::make($request->password);
        }

        // Update user
        $user->update($validated);

        return redirect()
            ->route('users.index')
            ->with('flash', [
                'toast' => [
                    'type' => 'success',
                    'message' => 'Pengguna berhasil diperbarui.',
                ],
            ]);
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Cegah penghapusan user sendiri
        if (Auth::id() === $user->id) {
            return redirect()
                ->route('users.index')
                ->with('flash', [
                    'toast' => [
                        'type' => 'error',
                        'message' => 'Anda tidak dapat menghapus akun sendiri.',
                    ],
                ]);
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('flash', [
                'toast' => [
                    'type' => 'success',
                    'message' => 'Pengguna berhasil dihapus.',
                ],
            ]);
    }

    /**
     * Update user password only.
     */
    public function updatePassword(Request $request, User $user): RedirectResponse
    {
        // Validasi password
        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()
            ->route('users.show', $user)
            ->with('flash', [
                'toast' => [
                    'type' => 'success',
                    'message' => 'Password berhasil diperbarui.',
                ],
            ]);
    }

    /**
     * Resend email verification notification.
     */
    public function resendVerification(User $user): RedirectResponse
    {
        if ($user->hasVerifiedEmail()) {
            return back()
                ->with('flash', [
                    'toast' => [
                        'type' => 'info',
                        'message' => 'Email sudah terverifikasi.',
                    ],
                ]);
        }

        $user->sendEmailVerificationNotification();

        return back()
            ->with('flash', [
                'toast' => [
                    'type' => 'success',
                    'message' => 'Email verifikasi telah dikirim ulang.',
                ],
            ]);
    }
}
