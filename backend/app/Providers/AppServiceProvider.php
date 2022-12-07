<?php

declare(strict_types=1);

namespace App\Providers;

use App\Console\Scheduling\VersionedCacheEventMutex;
use App\Console\Scheduling\VersionedCacheSchedulingMutex;
use GuzzleHttp\Client;
use Http\Factory\Guzzle\RequestFactory;
use Http\Factory\Guzzle\StreamFactory;
use Illuminate\Console\Scheduling\EventMutex;
use Illuminate\Console\Scheduling\SchedulingMutex;
use Illuminate\Support\ServiceProvider;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(RequestFactoryInterface::class, RequestFactory::class);
        $this->app->bind(StreamFactoryInterface::class, StreamFactory::class);
        $this->app->bind(ClientInterface::class, Client::class);
        $this->app->bind(EventMutex::class, VersionedCacheEventMutex::class);
        $this->app->bind(SchedulingMutex::class, VersionedCacheSchedulingMutex::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
