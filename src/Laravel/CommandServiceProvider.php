<?php

namespace Mirutights\Laravel;

use Illuminate\Support\ServiceProvider;
use Mirutights\Handlers\DealCommandHandler;
use Psr\Log\LoggerInterface;

class CommandServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(DealCommandHandler::class, function ($app) {
            $logger = $app->make(LoggerInterface::class);
            return new DealCommandHandler($logger);
        });
    }
}