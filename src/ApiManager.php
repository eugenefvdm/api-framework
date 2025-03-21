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

    /**
     * Get BulkSMS service
     *
     * @return BulkSMS
     */
    public function bulksms()
    {
        return $this->app->make('bulksms');
    }

    /**
     * Get Discord service
     *
     * @return Discord
     */
    public function discord()
    {
        return $this->app->make('discord');
    }

    // Add similar methods for other services...
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
