<?php


use Illuminate\Support\Carbon;

if (!function_exists('nowFromApp')) {

    function nowFromApp(): Carbon
    {
        return now(config('app.timezone'));
    }

}

if (!function_exists('todayFromApp')) {

    function todayFromApp(): Carbon
    {
        return today(config('app.timezone'));
    }

}
