<?php

namespace App\Providers;

// use Illuminate\Support\ServiceProvider;

use App\Models\Questionnaires\Questionnaire;
use App\Observers\Questionnaires\QuestionnaireObserve;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class QuestionnaireProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Questionnaire::observe(QuestionnaireObserve::class);
    }
}
