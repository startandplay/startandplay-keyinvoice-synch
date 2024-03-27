<?php
/*
Plugin Name: Keyinvoice Synchronization
Plugin URI:startandplay.com
Description: Keyinvoice products data synchronization
Author: Nuno Alves
Version: 1.0.1
Licence: GPL3
*/


defined('ABSPATH') or die('You Shall Not Pass!');

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once  dirname(__FILE__) . '/vendor/autoload.php';
}

if (!defined('PLUGIN_PATH'))
    define('PLUGIN_PATH', plugin_dir_path(__FILE__));

if (!defined('PLUGIN_URL'))
    define('PLUGIN_URL', plugin_dir_url(__FILE__));

if (!defined('PLUGIN_NAME'))
    define('PLUGIN_NAME', plugin_basename(__FILE__));

function activate()
{
    keyinvoicesync\Utils\Activate::activate();
}
register_activation_hook(dirname(__FILE__), 'activate');

function dectivate()
{
    keyinvoicesync\Utils\Deactivate::deactivate();
}
register_deactivation_hook(dirname(__FILE__), 'dectivate');


if (class_exists('keyinvoicesync\\Init')) {
    keyinvoicesync\Init::boot();
}
