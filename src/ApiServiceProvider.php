<?php

namespace Eugenefvdm\Api;

use Eugenefvdm\Api\Contracts\Fail2banInterface;
use Eugenefvdm\Api\Contracts\TailInterface;
use Eugenefvdm\Api\Contracts\WhmcsInterface;
use Eugenefvdm\Api\Contracts\WhmInterface;
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

        // Bulksms
        $this->app->singleton(Bulksms::class, function ($app) {
            return new Bulksms(
                config('api.bulksms.username'),
                config('api.bulksms.password')
            );
        });
        $this->app->alias(Bulksms::class, 'bulksms');

        // Discord
        $this->app->singleton(Discord::class, function ($app) {
            return new Discord(
                config('api.discord.bot_token'),
            );
        });
        $this->app->alias(Discord::class, 'discord');

        $this->app->singleton(Dns::class, function ($app) {
            return new Dns;
        });
        $this->app->alias(Dns::class, 'dns');

        // Fail2ban
        $this->app->singleton(Fail2banInterface::class, function ($app) {
            return new Fail2ban;
        });
        $this->app->alias(Fail2banInterface::class, 'fail2ban');

        // Hellopeter
        $this->app->singleton(Hellopeter::class, function ($app) {
            return new Hellopeter(
                config('api.hellopeter.api_key')
            );
        });
        $this->app->alias(Hellopeter::class, 'hellopeter');

        // Slack
        $this->app->singleton(Slack::class, function ($app) {
            return new Slack(
                config('api.slack.webhook_url')
            );
        });
        $this->app->alias(Slack::class, 'slack');

        // Tail
        // Single binding for Tail that handles both interface and concrete class
        $this->app->singleton(TailInterface::class, function ($app) {
            return new Tail;
        });
        $this->app->alias(TailInterface::class, 'tail');

        // Telegram
        $this->app->singleton(Telegram::class, function ($app) {
            return new Telegram(
                config('api.telegram.bot_token'),
                config('api.telegram.chat_id')
            );
        });
        $this->app->alias(Telegram::class, 'telegram');

        // WHM
        $this->app->singleton(WhmInterface::class, function ($app) {
            return new Whm(
                config('api.whm.username'),
                config('api.whm.password'),
                config('api.whm.server')
            );
        });
        $this->app->alias(WhmInterface::class, 'whm');

        // WHMCS
        $this->app->singleton(WhmcsInterface::class, function ($app) {
            return new Whmcs(
                config('api.whmcs.url'),
                config('api.whmcs.api_identifier'),
                config('api.whmcs.api_secret')
            );
        });
        $this->app->alias(WhmcsInterface::class, 'whmcs');

        // X        
        $this->app->singleton(X::class, function ($app) {
            return new X(
                config('api.x.bearer_token')
            );
        });
        $this->app->alias(X::class, 'x');

        // Zadomains
        $this->app->singleton(Zadomains::class, function ($app) {
            return new Zadomains(
                config('api.zadomains.username'),
                config('api.zadomains.password')
            );
        });
        $this->app->alias(Zadomains::class, 'zadomains');
    }

    public function boot(): void
    {
        // Bootstrap your package
        // Load routes, views, migrations, etc.

        $this->publishes([
            __DIR__.'/../config/api.php' => \config_path('api.php'),
        ], 'config');
    }
}
