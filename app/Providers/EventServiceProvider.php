<?php

namespace App\Providers;

use App\Events\DocumentUploaded;
use App\Events\DocumentVerified;
use App\Events\DocumentRejected;
use App\Listeners\SendDocumentUploadedNotification;
use App\Listeners\SendDocumentVerifiedNotification;
use App\Listeners\SendDocumentRejectedNotification;
use App\Events\FeeCreated;
use App\Events\FeeUpdated;
use App\Events\FeeDeleted;
use App\Listeners\LogFeeActivity;
use App\Events\ProgrammeCreated;
use App\Events\BatchCreated;
use App\Events\SectionCreated;
use App\Listeners\LogAcademicActivity;
use App\Events\PaymentProcessed;
use App\Events\RefundProcessed;
use App\Listeners\SendUserCreatedNotification;
use App\Events\UserCreated;
use App\Events\UserUpdated;
use App\Events\UserDeleted;
use App\Listeners\LogPaymentActivity;
use App\Events\StudentCreated;
use App\Events\StudentUpdated;
use App\Events\StudentDeleted;
use App\Listeners\LogStudentActivity;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // Add your event-listener mappings here
        DocumentUploaded::class => [
            SendDocumentUploadedNotification::class,
        ],

        DocumentVerified::class => [
            SendDocumentVerifiedNotification::class,
        ],

        DocumentRejected::class => [
            SendDocumentRejectedNotification::class,
        ],

        // Fee events
        FeeCreated::class => [
            LogFeeActivity::class,
        ],
        FeeUpdated::class => [
            LogFeeActivity::class,
        ],
        FeeDeleted::class => [
            LogFeeActivity::class,
        ],

        // Academic events
        ProgrammeCreated::class => [
            LogAcademicActivity::class,
        ],
        BatchCreated::class => [
            LogAcademicActivity::class,
        ],
        SectionCreated::class => [
            LogAcademicActivity::class,
        ],

        // Payment events
        PaymentProcessed::class => [
            LogPaymentActivity::class,
        ],
        RefundProcessed::class => [
            LogPaymentActivity::class,
        ],
        // User
        UserCreated::class => [
            SendUserCreatedNotification::class,
        ],
        UserUpdated::class => [
            // listeners
        ],
        UserDeleted::class => [
            // listeners
        ],
        // Student events
        // Student events
        StudentCreated::class => [
            LogStudentActivity::class,
        ],
        StudentUpdated::class => [
            LogStudentActivity::class,
        ],
        StudentDeleted::class => [
            LogStudentActivity::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
