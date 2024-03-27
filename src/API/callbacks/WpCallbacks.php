<?php

/**
 * @package Keyinvoice
 */

namespace keyinvoicesync\API\callbacks;

class WpCallbacks
{
    public function configPage()
    {
        require_once PLUGIN_PATH . 'src/templates/config.php';
    }

    public function synchPage()
    {
        require_once PLUGIN_PATH . 'src/templates/synch.php';
    }


    public function KeyConfigOptionsGroup($input)
    {
        return $input;
    }

    public function KeyinvoiceConfigsSection($section)
    {
        echo '<h3>' . $section['title'] . '</h3>';
    }

    public function urlAPIConfig()
    {
        $value = esc_attr(get_option('url_api'));
        echo '<input type="text" class="regular-text" name="url_api" value="' . $value . '" placeholder="URL">';
    }

    public function keyAPIConfig($args)
    {
        $value = esc_attr(get_option('api_key'));
        echo '<input type="text" class="regular-text" name="api_key" value="' . $value . '" placeholder="Chave API"><p>' . $args['text'] . '</p>';
    }


    public function keyTextArea($args)
    {
        $value = esc_attr(get_option($args['label_for']));
        echo '<textarea type="text" class="regular-text" rows="3" name="' . $args['label_for'] . '" value="' . $value . '" placeholder="' . $args['placeholder'] . '"></textarea>
        <p>' . $args['text'] . '</p>';
    }

    public function keyBtn($args)
    {
        echo '<button class="button button-primary" id="test-api-ki">TESTAR</button><div id="result-testing"></div>';
    }
}
