<?php

namespace Eugenefvdm;

use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        'bulk_sms' => BulkSMS::class,
        'discord' => Discord::class,
        'hello_peter' => HelloPeter::class,
        'slack' => Slack::class,
        'telegram' => Telegram::class,
        'za_domains' => ZADomains::class,
    ];

    public function register()
    {
        // Register the API manager
        $this->app->singleton('api', function ($app) {
            return new ApiManager($app);
        });

        // Register individual API services with their configurations
        $this->app->singleton('bulk_sms', function ($app) {
            return new BulkSMS(
                config('services.bulk_sms.username'),
                config('services.bulk_sms.password')
            );
        });

        $this->app->singleton('discord', function ($app) {
            return new Discord(
                config('services.discord.webhook_url')
            );
        });

        $this->app->singleton('hello_peter', function ($app) {
            return new HelloPeter(
                config('services.hello_peter.api_key')
            );
        });

        $this->app->singleton('slack', function ($app) {
            return new Slack(
                config('services.slack.webhook_url')
            );
        });

        $this->app->singleton('telegram', function ($app) {
            return new Telegram(
                config('services.telegram.bot_token'),
                config('services.telegram.chat_id')
            );
        });

        $this->app->singleton('zadomains', function ($app) {
            return new ZADomains(
                config('services.zadomains.username'),
                config('services.zadomains.password')
            );
        });
    }

    public function boot()
    {
        // Bootstrap your package
        // Load routes, views, migrations, etc.
        
        $this->publishes([
            __DIR__.'/../config/api.php' => config_path('api.php'),
        ], 'config');               
    }
} 