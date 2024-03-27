<?php

/**
 * @package Keyinvoice
 */

namespace keyinvoicesync;

final class Init
{

    public static function get_services()
    {
        return [
            Admin\Pages::class,
            Admin\Enqueue::class,
            API\Ajax::class,
        ];
    }

    public static function boot()
    {
        foreach (self::get_services() as $class) {
            $service = self::instantiate($class);
            if (method_exists($service, 'register')) {
                $service->register();
            }
        }
    }

    private static function instantiate($class)
    {
        return new $class();
    }
}
