<?php

namespace Eugenefvdm\Api;

class ApiManager
{
    protected $app;

    protected $services = [];

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function bulksms()
    {
        return $this->app->make('bulksms');
    }

    public function discord()
    {
        return $this->app->make('discord');
    }

    public function dns()
    {
        return $this->app->make('dns');
    }

    public function hellopeter()
    {
        return $this->app->make('hellopeter');
    }

    public function slack()
    {
        return $this->app->make('slack');
    }

    public function telegram()
    {
        return $this->app->make('telegram');
    }

    public function zadomains()
    {
        return $this->app->make('zadomains');
    }

    public function whm()
    {
        return $this->app->make('whm');
    }

    public function x()
    {
        return $this->app->make('x');
    }

    /**
     * Magic method to dynamically access services
     */
    public function __call($name, $arguments)
    {
        if ($this->app->bound($name)) {
            return $this->app->make($name);
        }

        throw new \InvalidArgumentException("API service [$name] not found.");
    }
}
