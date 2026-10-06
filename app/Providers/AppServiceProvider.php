<?php

namespace App\Providers;

use App\Models\AcademicYear;
use App\Models\Conversation;
use App\Models\Payment;
use App\Models\Result;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use App\Policies\AcademicReportPolicy;
use App\Policies\ConversationPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\ResultPolicy;
use App\Policies\StudentPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected array $policies = [
        Student::class => StudentPolicy::class,
        Result::class => ResultPolicy::class,
        Payment::class => PaymentPolicy::class,
        Conversation::class => ConversationPolicy::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            if ($user->isAdmin()) {
                return true;
            }

            return null;
        });

        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

        Gate::define('manage-fees', fn ($user) => $user->hasRole('admin', 'accountant'));
        Gate::define('view-finances', fn ($user) => $user->hasRole('admin', 'accountant'));
        Gate::define('manage-academics', fn ($user) => $user->hasRole('admin', 'teacher'));
        Gate::define('manage-users', fn ($user) => $user->isAdmin());
        Gate::define('manage-admissions', fn ($user) => $user->isAdmin());

        Gate::define('generate-academic-reports', fn ($user) => $user->hasRole('admin', 'teacher'));
        Gate::policy(AcademicYear::class, AcademicReportPolicy::class);
        Gate::policy(Term::class, AcademicReportPolicy::class);
        Gate::policy(SchoolClass::class, AcademicReportPolicy::class);
        Gate::policy(Subject::class, AcademicReportPolicy::class);
        Gate::policy(Student::class, AcademicReportPolicy::class);
    }
}
