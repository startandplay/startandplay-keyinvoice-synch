<?php

/**
 * @package Keyinvoice
 */

namespace keyinvoicesync\Admin;

class Enqueue
{

    function register()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
    }

    function enqueue()
    {
        wp_enqueue_style('ki-style', PLUGIN_URL . 'assets/style.css');
        wp_enqueue_script('ki-script', PLUGIN_URL . 'assets/script.js');
    }
}
