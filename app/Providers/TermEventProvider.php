<?php

namespace App\Providers;

use App\Events\Backend\Terms\LinkedBrandEvent;
use App\Models\MedicalStuff\MedicalArticle;
use App\Models\MedicalStuff\MedicalTerm;
use App\Models\MedicalStuff\MedicalTermType;
use App\Models\MedicalStuff\TermConnectionDescription;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class TermEventProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    // protected $listen = [
    //     LinkedBrandEvent::class => [
    //         \App\Listeners\Backend\Terms\LinkedBrandListener::class
    //     ]
    // ];



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
        // parent::boot();

        MedicalArticle::observe(\App\Observers\Terms\ArticleObserver::class);

        MedicalTerm::observe(\App\Observers\Terms\TermObserver::class);

        MedicalTermType::observe(\App\Observers\Terms\TypeObserver::class);

        TermConnectionDescription::observe(\App\Observers\Terms\TermConnectionDescriptionObserver::class);
    }
}
