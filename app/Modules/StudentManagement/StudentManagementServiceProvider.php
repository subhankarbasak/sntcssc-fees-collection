<?php

// app/Modules/StudentManagement/StudentManagementServiceProvider.php

namespace App\Modules\StudentManagement;

use Illuminate\Support\ServiceProvider;

class StudentManagementServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('StudentService', function ($app) {
            return new \App\Services\StudentService();
        });
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '../../../../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/Resources/views', 'studentmanagement');
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
    }
}