<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Check for 'lang' cookie
        $cookie = \Crypt::decrypt(\Cookie::get('lang'), false);

        //Get visitors IP
        $userIp = \Request::ip();

        //Get visitors Geo info based on his IP
        $geo = \GeoIP::getLocation($userIp);


        if($geo == null) {
            //Probably a localhost server, set language to english

            //set locale from cookie if exists
            if (!isset($cookie) && !empty($cookie)) {
                \App::setLocale($cookie);
                return;
            }

            \App::setLocale('es');
        }

        // //Get visitors country name
        $userCountry = $geo['country'];

  
        //Set language based on country name
        // You can add as many as you want
        $supportedLanguages = [
            'United States' => 'en',
            'Canada' => 'en',
            'India' => 'en',
            'Argentina' => 'es',
            'Spain' => 'es',
            'Chile' => 'es',
            'Austria' => 'de',
            'Luxembourg' => 'de',
            'Belgium' => 'de',
            'Germany' => 'de',
        ];

        if (!empty($cookie)) {
            //User has manually chosen a lang. We set it
            \App::setLocale($cookie);
        } else {

            //Check country name in supportedLanguages array
            if (array_key_exists($userCountry, $supportedLanguages)) {
                //Get userCountry value(language) from array
                $preferredLang = $supportedLanguages[$userCountry];
                //Set language based on value
                \App::setLocale($preferredLang);
            } else {
                //If user is visiting from an unsupported country, default to English
                \App::setLocale('en');
            }
        }
    }
}
