<?php

namespace Eugenefvdm\Api;

use Illuminate\Contracts\Foundation\Application;

class ApiManager
{
    public function __construct(private Application $app)
    {
        $this->app = $app;
    }

    public function bulksms(): Bulksms
    {
        return $this->app->make('bulksms');
    }

    public function discord(): Discord
    {
        return $this->app->make('discord');
    }

    public function dns(): Dns
    {
        return $this->app->make('dns');
    }

    public function fail2ban(): Fail2ban
    {
        return $this->app->make('fail2ban');
    }

    public function hellopeter(): Hellopeter
    {
        return $this->app->make('hellopeter');
    }

    public function slack(): Slack
    {
        return $this->app->make('slack');
    }

    public function tail(): Tail
    {
        return $this->app->make('tail');
    }

    public function telegram(): Telegram
    {
        return $this->app->make('telegram');
    }

    public function zadomains(): Zadomains
    {
        return $this->app->make('zadomains');
    }

    public function whm(): Whm
    {
        return $this->app->make('whm');
    }

    public function x(): X
    {
        return $this->app->make('x');
    }

    /**
     * Magic method to dynamically access services
     */
    public function __call(string $name, array $arguments): mixed
    {
        if ($this->app->bound($name)) {
            return $this->app->make($name, $arguments);
        }

        throw new \InvalidArgumentException("API service [$name] not found.");
    }
}
