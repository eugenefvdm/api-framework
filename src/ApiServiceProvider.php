<?php

namespace Eugenefvdm\Api;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        // Register the API manager
        $this->app->singleton('api', function ($app) {
            return new ApiManager($app);
        });

        // Register BulkSMS
        $this->app->singleton(BulkSMS::class, function ($app) {
            return new BulkSMS(
                Config::get('api.bulk_sms.username'),
                Config::get('api.bulk_sms.password')
            );
        });
        $this->app->alias(BulkSMS::class, 'bulk_sms');

        // Register Discord
        $this->app->singleton(Discord::class, function ($app) {
            return new Discord(
                Config::get('api.discord.bot_token'),
            );
        });
        $this->app->alias(Discord::class, 'discord');

        // Register HelloPeter
        $this->app->singleton(HelloPeter::class, function ($app) {
            return new HelloPeter(
                Config::get('api.hello_peter.api_key')
            );
        });
        $this->app->alias(HelloPeter::class, 'hello_peter');

        // Register Slack
        $this->app->singleton(Slack::class, function ($app) {
            return new Slack(
                Config::get('api.slack.webhook_url')
            );
        });
        $this->app->alias(Slack::class, 'slack');

        // Register Telegram
        $this->app->singleton(Telegram::class, function ($app) {
            return new Telegram(
                Config::get('api.telegram.bot_token'),
                Config::get('api.telegram.chat_id')
            );
        });
        $this->app->alias(Telegram::class, 'telegram');

        // Register ZADomains
        $this->app->singleton(ZADomains::class, function ($app) {
            return new ZADomains(
                Config::get('api.za_domains.username'),
                Config::get('api.za_domains.password')
            );
        });
        $this->app->alias(ZADomains::class, 'zadomains');
    }

    public function boot()
    {
        // Bootstrap your package
        // Load routes, views, migrations, etc.

        $this->publishes([
            __DIR__.'/../config/api.php' => \config_path('api.php'),
        ], 'config');
    }
}
