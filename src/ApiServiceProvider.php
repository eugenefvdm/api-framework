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
        $this->app->singleton(Bulksms::class, function ($app) {
            return new Bulksms(
                Config::get('api.bulksms.username'),
                Config::get('api.bulksms.password')
            );
        });
        $this->app->alias(Bulksms::class, 'bulksms');

        // Register Discord
        $this->app->singleton(Discord::class, function ($app) {
            return new Discord(
                Config::get('api.discord.bot_token'),
            );
        });
        $this->app->alias(Discord::class, 'discord');

        // Register HelloPeter
        $this->app->singleton(Hellopeter::class, function ($app) {
            return new Hellopeter(
                Config::get('api.hello_peter.api_key')
            );
        });
        $this->app->alias(Hellopeter::class, 'hellopeter');

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
        $this->app->singleton(Zadomains::class, function ($app) {
            return new Zadomains(
                Config::get('api.zadomains.username'),
                Config::get('api.zadomains.password')
            );
        });
        $this->app->alias(Zadomains::class, 'zadomains');

        // Register WHM
        $this->app->singleton(Whm::class, function ($app) {
            return new Whm(
                Config::get('api.whm.username'),
                Config::get('api.whm.password'),
                Config::get('api.whm.server')
            );
        });
        $this->app->alias(Whm::class, 'whm');
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
