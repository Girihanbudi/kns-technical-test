<?php

namespace App\Controllers;

use App\Enums\UserRole;
use Illuminate\Auth\Access\AuthorizationException;
use App\Mail\AdminNewUserNotification;
use App\Mail\NewUserWelcome;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class UserController
{
    /**
     * Create a new user.
     */
    public function store(array $data): array
    {
        $user = User::create([
            'email' => $data['email'],
            'password' => $data['password'],
            'name' => $data['name'],
            'role' => UserRole::User->value,
            'active' => true,
        ]);

        Mail::to($user->email)->send(new NewUserWelcome($user));

        $adminEmail = env('ADMIN_EMAIL_NOTIF', config('mail.from.address'));
        if ($adminEmail) {
            Mail::to($adminEmail)->send(new AdminNewUserNotification($user));
        }

        return [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'created_at' => $user->created_at,
        ];
    }

    /**
     * Get paginated users with filters and permissions.
     */
    public function index(array $filters, ?User $currentUser)
    {
        $sortBy = $filters['sortBy'] ?? 'created_at';
        $sortDirection = $filters['sortDirection'] ?? 'asc';
        $search = $filters['search'] ?? null;
        $active = $filters['active'] ?? null;
        $limit = (int) ($filters['limit'] ?? 10);
        $page = (int) ($filters['page'] ?? 1);

        $query = User::query()
            ->withCount('orders');

        if ($active !== null) {
            $query->where('active', (bool) $active);
        } else {
            $query->where('active', true);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $query->orderBy($sortBy, $sortDirection);

        $users = $query->paginate(perPage: $limit, page: $page);

        $users->getCollection()->transform(function ($user) use ($currentUser) {
            return [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'role' => $user->role?->value ?? $user->role,
                'created_at' => $user->created_at,
                'orders_count' => $user->orders_count,
                'can_edit' => $this->canEdit($currentUser, $user),
            ];
        });

        return [
            'page' => $users->currentPage(),
            'users' => $users->items(),
        ];
    }

    /**
     * Determine if the current user can edit the given user.
     */
    private function canEdit(?User $currentUser, User $targetUser): bool
    {
        if (!$currentUser) {
            return false;
        }

        $currentRole = $currentUser->role instanceof UserRole ? $currentUser->role : UserRole::tryFrom((string) $currentUser->role);
        $targetRole = $targetUser->role instanceof UserRole ? $targetUser->role : UserRole::tryFrom((string) $targetUser->role);

        return match ($currentRole) {
            UserRole::Administrator => true,
            UserRole::Manager => $targetRole === UserRole::User || $currentUser->id === $targetUser->id,
            default => $currentUser->id === $targetUser->id,
        };
    }

    /**
     * Update a user's editable fields.
     */
    public function update(User $currentUser, User $user, array $data): array
    {
        if (!$this->canEdit($currentUser, $user)) {
            throw new AuthorizationException('Forbidden.');
        }

        $payload = [];

        if (array_key_exists('name', $data)) {
            $payload['name'] = $data['name'];
        }

        if (array_key_exists('role', $data)) {
            $payload['role'] = $data['role'];
        }

        if (array_key_exists('emailVerifiedAt', $data)) {
            $payload['email_verified_at'] = $data['emailVerifiedAt'];
        }

        if (array_key_exists('active', $data)) {
            $payload['active'] = $data['active'];
        }

        if ($payload) {
            $user->update($payload);
        }

        return [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'role' => $user->role?->value ?? $user->role,
            'email_verified_at' => $user->email_verified_at,
            'active' => (bool) $user->active,
        ];
    }
}
