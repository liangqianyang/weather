<?php
/**
 * Created by PhpStorm.
 * User: bb
 * Date: 2019/8/13
 * Time: 9:26
 */

namespace Amap\Weather;


class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

    protected $defer = true;

    public function register()
    {
        $this->app->singleton(Weather::class, function(){
            return new Weather(config('services.weather.key'));
        });

        $this->app->alias(Weather::class, 'weather');
    }

    public function provides()
    {
        return [Weather::class, 'weather'];
    }
}