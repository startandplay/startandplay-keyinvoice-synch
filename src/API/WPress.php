<?php

/**
 * @package Keyinvoice
 */

namespace keyinvoicesync\API;


class WPress
{
    public $admin_pages = array();
    public $admin_subpages = array();
    public $settings = array();
    public $sections = array();
    public $fields = array();


    public function register()
    {
        if (!empty($this->admin_pages)) {
            add_action('admin_menu', [$this, 'addAdminMenu']);
        }

        if (!empty($this->settings)) {
            add_action('admin_init', [$this, 'registerCustomFields']);
        }
    }

    public function addPages(array $pages)
    {
        $this->admin_pages = $pages;
        return $this;
    }

    public function withSubPage(string $title = null)
    {
        if (empty($this->admin_pages)) {
            return $this;
        }

        $admin_page = $this->admin_pages[0];

        $subpage = [
            [
                'parent_slug' => $admin_page['menu_slug'],
                'page_title' => $admin_page['page_title'],
                'menu_title' => ($title) ? $title : $admin_page['menu_title'],
                'capability' => $admin_page['capability'],
                'menu_slug' => $admin_page['menu_slug'],
                'callback' => $admin_page['callback']
            ]
        ];
        $this->admin_subpages = $subpage;
        return $this;
    }

    public function addSubPages(array $pages)
    {
        $this->admin_subpages = array_merge($this->admin_subpages, $pages);
        return $this;
    }

    public function addAdminMenu()
    {
        foreach ($this->admin_pages as $page) {
            // add_menu_page($page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'], $page['icon_url'], $page['position']);
            add_menu_page($page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'], $page['position']);
        }

        foreach ($this->admin_subpages as $page) {
            add_submenu_page($page['parent_slug'], $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback']);
        }
    }

    public function setSettings(array $settings)
    {
        $this->settings = $settings;
        return $this;
    }

    public function setSections(array $sections)
    {
        $this->sections = $sections;
        return $this;
    }

    public function setFields(array $fields)
    {
        $this->fields = $fields;
        return $this;
    }

    public function registerCustomFields()
    {
        foreach ($this->settings as $setting) {
            register_setting($setting["option_group"], $setting["option_name"], (isset($setting["callback"]) ? $setting["callback"] : ''));
        }

        foreach ($this->sections as $section) {
            add_settings_section($section["id"], $section["title"], (isset($section["callback"]) ? $section["callback"] : ''), $section["page"]);
        }

        foreach ($this->fields as $field) {
            add_settings_field($field["id"], $field["title"], (isset($field["callback"]) ? $field["callback"] : ''), $field["page"], $field["section"], (isset($field["args"]) ? $field["args"] : ''));
        }
    }


    public function updateProduct($kiProduct)
    {
        $sku = $kiProduct['sku'];

        if (isset($sku) && !empty($sku) && $sku) {
            $product_id = wc_get_product_id_by_sku($sku);
        }

        if ($product_id) {

            $wooProduct = wc_get_product($product_id);

            // ***** Create logic to calculate the final price value if TAX is not niclued in keyinvoice price 
            $finalPrice = $this->getFinalPrice($wooProduct, $kiProduct);
            $stockStatus = $this->getCurrentStockStatus($wooProduct, $kiProduct);
            $salePrice = $wooProduct->get_sale_price();
            $menuOrder = $this->getMenuOrder($stockStatus);

            $wooProduct->set_id($product_id);
            $wooProduct->set_regular_price($finalPrice);
            $wooProduct->set_manage_stock(isset($kiProduct['manage_stock']) ? $kiProduct['manage_stock'] : false);
            $wooProduct->set_stock_status($stockStatus);
            $wooProduct->set_stock_quantity($kiProduct['stock_qty']);
            $wooProduct->set_backorders(isset($kiProduct['backorders']) ? $kiProduct['backorders'] : 'no');
            $wooProduct->set_menu_order($menuOrder);
            $wooProduct->set_sale_price(isset($salePrice) ? $salePrice : '');

            $product_id = $wooProduct->save();
        }
        return $product_id;
    }

    private function getFinalPrice($wooProduct, $kiProduct)
    {
        $wooPrice = $wooProduct->get_regular_price();
        $wooSalePrice = $wooProduct->get_sale_price();
        $kiPrice = $kiProduct['regular_price'];
        $kiAsStock = intval($kiProduct['stock_qty']) > 0;

        // The price only change if no sale price and stock > 0
        $finalPrice = (empty($wooSalePrice) && $kiAsStock) ? $kiPrice : $wooPrice;

        // Add to logger products that have changed the price.
        // if ($finalPrice != $wooPrice) {
        //     $this->logger->setUpdatedPrices($kiProduct);
        // }

        return $finalPrice;
    }

    public function getMenuOrder($stockStatus)
    {
        switch ($stockStatus) {
            case 'pre_order':
                return -4;
            case 'instock':
                return -3;
            case 'availability_1_3_days':
                return -2;
            case 'availability_5_days':
                return -1;
            case 'onbackorder':
                return 0;
            case 'outofstock':
                return 1;
            default:
                throw new \ErrorException("Stock Status not supported yet " . $stockStatus);
                break;
        }
    }

    private function getCurrentStockStatus($wooProduct, $kiProduct)
    {
        // Only change thw stock status if has instock
        $keyInvoiceStock = $kiProduct['stock_qty'];

        if ($keyInvoiceStock >= 1) {
            return 'instock';
        }
        return $wooProduct->get_stock_status();
    }
}
