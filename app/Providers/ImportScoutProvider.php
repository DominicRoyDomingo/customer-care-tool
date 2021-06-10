<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Scout\Console\ImportCommand;
use Laravel\Scout\Console\FlushCommand;

class ImportScoutProvider extends ServiceProvider
{

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands([
            ImportCommand::class,
            FlushCommand::class,
        ]);
    }
}