<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'organization_name' => 'required|string|max:255',
        ]);

        $org = Organization::create([
            'name' => $validated['organization_name'],
            'slug' => Str::slug($validated['organization_name']) . '-' . substr(uniqid(), -6),
            'plan' => 'starter',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'company_admin',
            'organization_id' => $org->id,
        ]);

        $user->organizations()->attach($org->id, ['role' => 'company_admin']);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data' => [
                'user' => $this->formatUser($user->load('organization')),
                'token' => $token,
            ],
            'message' => 'Registration successful',
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($validated)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials. Please check your email and password.'],
            ]);
        }

        /** @var User $user */
        $user = Auth::user();
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data' => [
                'user' => $this->formatUser($user->load('organization')),
                'token' => $token,
            ],
            'message' => 'Login successful',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->formatUser($request->user()->load('organization')),
        ]);
    }

    private function formatUser(User $user): array
    {
        return [
            'id' => (string) $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'organizationId' => $user->organization_id ? (string) $user->organization_id : null,
            'avatarUrl' => $user->avatar_url,
            'createdAt' => $user->created_at?->toISOString(),
            'organization' => $user->relationLoaded('organization') && $user->organization ? [
                'id' => (string) $user->organization->id,
                'name' => $user->organization->name,
                'slug' => $user->organization->slug,
                'plan' => $user->organization->plan,
            ] : null,
        ];
    }
}
