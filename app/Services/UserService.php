<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    public function listAll(array $filters = []): LengthAwarePaginator
    {
        return User::when(($filters['name'] ?? null), function ($q) use ($filters) {
            $q->where('name', 'like', '%' . $filters['name'] . '%');
        })->orderBy('name')->paginate();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $User, array $data): User
    {
        $User->update($data);

        return $User;
    }

    public function delete(User $User): bool
    {
        return $User->delete();
    }
}
