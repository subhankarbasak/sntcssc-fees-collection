<?php

// app/Modules/UserManagement/UserManagementServiceProvider.php
namespace App\Modules\UserManagement;

use Illuminate\Support\ServiceProvider;

class UserManagementServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('UserService', function ($app) {
            return new \App\Services\UserService();
        });
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '../../../../routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'usermanagement');
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }
}