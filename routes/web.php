<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Students\StudentManager;
use Illuminate\Support\Facades\Route;

use App\Modules\UserManagement\Livewire\UserManagement;
use App\Modules\StudentManagement\Livewire\StudentManagement;
use App\Modules\StudentManagement\Livewire\StudentDashboard;
use App\Modules\FeesManagement\Livewire\FeesManagement;
use App\Modules\AcademicManagement\Livewire\ProgrammeManagement;
use App\Modules\AcademicManagement\Livewire\BatchManagement;
use App\Modules\AcademicManagement\Livewire\SectionManagement;
use App\Modules\FinancialManagement\Livewire\TransactionManagement;
use App\Modules\Reporting\Livewire\Reports;
use App\Modules\Dashboard\Livewire\AdminDashboard;
use App\Modules\DocumentManagement\Livewire\DocumentManagement;
use App\Modules\Audit\Livewire\AuditLogManagement;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    // Route::get('students', StudentManager::class)->name('students.index');
    // Route::get('students', StudentManager::class)->name('students');

    // 

    // Dashboard routes
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    
    // User Management routes
    Route::prefix('users')->group(function () {
        Route::get('/', UserManagement::class)->name('users.index');
        Route::get('/trashed', UserManagement::class)->name('users.index.trashed');
    });
    
    // Student Management routes
    Route::prefix('students')->group(function () {
        Route::get('/', StudentManagement::class)->name('students.index');
        Route::get('/trashed', StudentManagement::class)->name('students.index.trashed');
    });
    
    // Fee Management routes
    Route::prefix('fees')->middleware(['permission:fees.view'])->group(function () {
        Route::get('/', FeesManagement::class)->name('fees.index');
        Route::get('/trashed', FeesManagement::class)->name('fees.index.trashed');
    });
    
    // Academic Management routes
    Route::prefix('academic')->middleware(['permission:academic.view'])->group(function () {
        Route::get('/programmes', ProgrammeManagement::class)->name('academic.programmes.index');
        Route::get('/programmes/trashed', ProgrammeManagement::class)->name('academic.programmes.index.trashed');
        Route::get('/batches', BatchManagement::class)->name('academic.batches.index');
        Route::get('/batches/trashed', BatchManagement::class)->name('academic.batches.index.trashed');
        Route::get('/sections', SectionManagement::class)->name('academic.sections.index');
        Route::get('/sections/trashed', SectionManagement::class)->name('academic.sections.index.trashed');
    });
    
    // Transaction Management routes
    Route::prefix('transactions')->middleware(['permission:transactions.view'])->group(function () {
        Route::get('/', TransactionManagement::class)->name('transactions.index');
    });
    
    // Reports routes
    Route::prefix('reports')->middleware(['permission:reports.view'])->group(function () {
        Route::get('/', Reports::class)->name('reports.index');
    });
    
    // Document Management routes
    Route::prefix('documents')->middleware(['permission:documents.view'])->group(function () {
        Route::get('/', DocumentManagement::class)->name('documents.index');
    });
    
    // Audit Log routes
    Route::prefix('audit')->middleware(['permission:audit.view'])->group(function () {
        Route::get('/', AuditLogManagement::class)->name('audit.index');
    });
    // 
});

require __DIR__.'/auth.php';
