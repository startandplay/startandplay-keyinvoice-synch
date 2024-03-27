<?php

/**
 * @package Keyinvoice
 */

namespace keyinvoicesync\Utils;

class Activate
{
    public static function activate()
    {
        flush_rewrite_rules();
    }
}
