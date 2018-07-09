<?php
/**
 * Created by PhpStorm.
 * User: eche
 * Date: 6/29/18
 * Time: 12:36 PM
 */


require_once "vendor/autoload.php";

if (!function_exists("app_base_dir")) {
    function app_base_dir()
    {
        return __DIR__;
    }
}
