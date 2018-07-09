<?php

namespace NotificationChannels\AsanakSms;

use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\ServiceProvider;

class AsanakSmsServiceProvider extends ServiceProvider
{
    public function boot()
    {
    	$this->app->make(AsanakSmsApi::class);

    	$cm = $this->app->make(ChannelManager::class);

	    $cm->extend('Asanaksms', function ($app) {
	        return new AsanakSmsChannel(
	          new AsanakSmsApi($this->app['config']['services.asanaksms'])
	        );
	    });

    }

    public function register()
    {
        $this->app->singleton(AsanakSmsApi::class, function ($app) {
            return new AsanakSmsApi($app['config']['services.asanaksms']);
        });
    }
}
