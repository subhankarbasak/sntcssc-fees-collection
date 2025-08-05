<?php

// app/Services/UserService.php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Events\UserCreated;
use App\Events\UserUpdated;
use App\Events\UserDeleted;

class UserService
{
    public function getAllUsers($filters = [])
    {
        $query = User::query();
        
        if (!empty($filters['role'])) {
            $query->role($filters['role']);
        }
        
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%');
        }
        
        return $query->with('roles')->paginate(10);
    }
    
    public function createUser(array $data)
    {
        try {
            DB::beginTransaction();
            
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
            
            if (isset($data['roles'])) {
                $user->assignRole($data['roles']);
            }
            
            if (isset($data['photo'])) {
                $user->addMedia($data['photo'])->toMediaCollection('photos');
            }
            
            DB::commit();
            
            UserCreated::dispatch($user);
            
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function updateUser(User $user, array $data)
    {
        try {
            DB::beginTransaction();
            
            $user->update([
                'name' => $data['name'] ?? $user->name,
                'email' => $data['email'] ?? $user->email,
            ]);
            
            if (isset($data['password'])) {
                $user->update([
                    'password' => Hash::make($data['password']),
                ]);
            }
            
            if (isset($data['roles'])) {
                $user->syncRoles($data['roles']);
            }
            
            if (isset($data['photo'])) {
                $user->clearMediaCollection('photos');
                $user->addMedia($data['photo'])->toMediaCollection('photos');
            }
            
            DB::commit();
            
            UserUpdated::dispatch($user);
            
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function deleteUser(User $user)
    {
        try {
            DB::beginTransaction();
            
            $user->delete();
            
            DB::commit();
            
            UserDeleted::dispatch($user);
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function restoreUser($id)
    {
        try {
            DB::beginTransaction();
            
            $user = User::withTrashed()->findOrFail($id);
            $user->restore();
            
            DB::commit();
            
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}