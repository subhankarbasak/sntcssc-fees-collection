<?php

return [
    App\Providers\AppServiceProvider::class,
    Maatwebsite\Excel\ExcelServiceProvider::class,
    Spatie\Permission\PermissionServiceProvider::class,
    App\Modules\UserManagement\UserManagementServiceProvider::class,
    App\Modules\StudentManagement\StudentManagementServiceProvider::class,
];
