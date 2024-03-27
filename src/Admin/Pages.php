<?php

/**
 * @package Keyinvoice
 */

namespace keyinvoicesync\Admin;

use keyinvoicesync\API\WPress;
use keyinvoicesync\API\callbacks\WpCallbacks;

class Pages
{
    public $wpress_api;
    public $callback;
    public $pages = array();
    public $subpages = array();

    public function register()
    {

        $this->wpress_api = new Wpress;
        $this->callback = new WpCallbacks();

        $this->setPages();
        $this->setSubPages();
        $this->setSettings();
        $this->setSections();
        $this->setFields();
        $this->wpress_api->addPages($this->pages)->withSubPage('Configurações')->addSubPages($this->subpages)->register();
    }


    public function setPages()
    {
        $this->pages = [
            [
                'page_title' => 'Keyinvoice Configurações',
                'menu_title' => 'Keyinvoice Sync',
                'capability' => 'manage_options',
                'menu_slug' => 'keyinvoice',
                'callback' => [$this->callback, 'configPage'],
                // 'icon_url' => PLUGIN_URL . 'assets/images/icon_keyinvoice.png',
                'position' => 110,
            ]
        ];
    }

    public function setSubPages()
    {
        $this->subpages = [
            [
                'parent_slug' => 'keyinvoice',
                'page_title' => 'Keyinvoice Sincronizar Stock',
                'menu_title' => 'Sincronizar Stock',
                'capability' => 'manage_options',
                'menu_slug' => 'keyinvoice_stock',
                'callback' => [$this->callback, 'synchPage']
            ],
        ];
    }

    public function setSettings()
    {
        $args = [
            [
                'option_group' => 'keyinvoice_configs_options_group',
                'option_name' => 'url_api',
                'callback' => [$this->callback, 'KeyConfigOptionsGroup']
            ],
            [
                'option_group' => 'keyinvoice_configs_options_group',
                'option_name' => 'api_key',
                'callback' => [$this->callback, 'KeyConfigOptionsGroup']
            ],
            [
                'option_group' => 'keyinvoice_configs_options_group',
                'option_name' => 'api_synch_products'
            ]
        ];

        $this->wpress_api->setSettings($args);
    }



    public function setSections()
    {
        $args = [
            [
                'id' => 'keyinvoice_configs',
                'title' => 'Settings',
                'callback' => [$this->callback, 'KeyinvoiceConfigsSection'],
                'page' => 'keyinvoice'
            ],
        ];

        $this->wpress_api->setSections($args);
    }

    public function setFields()
    {
        $args = [
            [
                'id' => 'url_api',
                'title' => 'URL Api',
                'callback' => [$this->callback, 'urlAPIConfig'],
                'page' => 'keyinvoice',
                'section' => 'keyinvoice_configs',
                'args' => [
                    'label_for' => 'url_api',
                    'class' => 'example-class'
                ]
            ],
            [
                'id' => 'api_key',
                'title' => 'Chave API',
                'callback' => [$this->callback, 'keyAPIConfig'],
                'page' => 'keyinvoice',
                'section' => 'keyinvoice_configs',
                'args' => [
                    'label_for' => 'api_key',
                    'class' => 'example-class',
                    'text' => 'Para obter estes dados entre na sua conta KEYINVOICE e clique em Configurações -» separador “API KeyInvoice'
                ]
            ],
            [
                'id' => 'test_api',
                'title' => 'Testar Configuração',
                'callback' => [$this->callback, 'keyBtn'],
                'page' => 'keyinvoice',
                'section' => 'keyinvoice_configs',
                'args' => [
                    'label_for' => 'api_key',
                    'class' => 'example-class'
                ]
            ],
        ];
        $this->wpress_api->setFields($args);
    }
}
