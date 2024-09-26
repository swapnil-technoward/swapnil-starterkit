<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Registration Mode Configuration
    |--------------------------------------------------------------------------
    | you can configure registration mode by setting the following
    | configuration in your environment file
    | by default its true you can change the default registration mode in env
    */

    'register' => env('REGISTER_MODE', true),

    /*
    |--------------------------------------------------------------------------
    | Remember Me Configuration
    |--------------------------------------------------------------------------
    | you can configure remember me by setting the following it will make user to stay logged in until he manually logout
    | configuration in your environment file
    | by default its true you can change the default registration mode in env
    */

    'remember_me' => env('REMEMBER_ME', true),

];
