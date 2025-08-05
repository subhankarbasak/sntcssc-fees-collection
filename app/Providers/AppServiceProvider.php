<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\Student;
use App\Models\Document;
use App\Models\Transaction;
use App\Models\Enrollment;
use App\Models\Programme;
use App\Models\Batch;
use App\Models\FeeStructure;
use App\Models\StudentFee;
use App\Observers\AuditLogObserver;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    // Reset cached roles and permissions
    app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

    // Create permissions
    // Permission::create(['name' => 'users.view']);
    // Permission::create(['name' => 'users.create']);
    // Permission::create(['name' => 'users.edit']);
    // Permission::create(['name' => 'users.delete']);
    
    // Add more permissions as needed...
    
    // Create roles and assign permissions
    // $adminRole = Role::create(['name' => 'admin']);
    // $adminRole->givePermissionTo(Permission::all());
    
    // $financeRole = Role::create(['name' => 'finance']);
    // $financeRole->givePermissionTo(['students.view', 'fees.view', 'payments.view', 'payments.create', 'transactions.view']);
    
    // $teacherRole = Role::create(['name' => 'teacher']);
    // $teacherRole->givePermissionTo(['students.view', 'batches.view']);

    // Register observers for models you want to audit
    User::observe(AuditLogObserver::class);
    Student::observe(AuditLogObserver::class);
    Document::observe(AuditLogObserver::class);
    Transaction::observe(AuditLogObserver::class);
    Enrollment::observe(AuditLogObserver::class);
    Programme::observe(AuditLogObserver::class);
    Batch::observe(AuditLogObserver::class);
    FeeStructure::observe(AuditLogObserver::class);
    StudentFee::observe(AuditLogObserver::class);
    }
}
