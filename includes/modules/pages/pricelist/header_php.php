<?php
/** 
 * @copyright Copyright 2003-2007 Paul Mathot Haarlem, The Netherlands & Carine Bruyndoncx, Belgium
 * @copyright parts Copyright 2003-2005 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version v1.5.7 (or newer)
 */
// -----
// Part of the Printable Price List plugin for Zen Cart v1.5.7 and later.
// Copyright (C) 2014-2021, Vinos de Frutas Tropicales (lat9)
//
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}
require DIR_WS_MODULES . 'require_languages.php';

// -----
// Define the class that provides the price-list support functions.
//
class price_list extends base
{
    public
        $current_profile,
        $config,
        $manufacturers_names,
        $header_columns,
        $products_sort_by;

    public function __construct()
    {
        global $db, $currencies;

        $this->product_count = 0;

        // -----
        // If a category has been selected from the template page's dropdown, remember it!
        //
        $this->current_category = (isset($_GET['plCat'])) ? ((int)$_GET['plCat']) : 0;

        $this->current_profile = (isset($_GET['profile'])) ? ((int)$_GET['profile']) : PL_DEFAULT_PROFILE;
        if (!defined('PL_ENABLE_' . $this->current_profile)) {
            $this->current_profile = PL_DEFAULT_PROFILE;
        }
        $this->enabled = (constant('PL_ENABLE_' . $this->current_profile) === 'true');

        // -----
        // This array, one element per profile-specific configuration setting, contains three required and one optional element:
        //
        // [0] ... The configuration setting "key" name (suffixed with _x where x is the profile number)
        // [1] ... The name of the class-based config array into which the setting value is stored
        // [2] ... The "type" (bool, int, or char), to which the value is converted
        // [3] ... (optional) If not 'empty', contains the database element that should be retrieved for the display.
        //
        $profile_settings = [
            ['PL_GROUP_NAME', 'group_name', 'char', ''],
            ['PL_PROFILE_NAME', 'profile_name', 'char', ''],
            ['PL_USE_MASTER_CATS_ONLY', 'master_cats_only', 'bool', ''],
            ['PL_SHOW_BOXES', 'show_boxes', 'bool', ''],
            ['PL_CATEGORY_TREE_MAIN_CATS_ONLY', 'main_cats_only', 'bool', ''],
            ['PL_MAINCATS_NEW_PAGE', 'maincats_new_page', 'bool', ''],
            ['PL_NOWRAP', 'nowrap', 'bool', ''],
            ['PL_SHOW_MODEL', 'show_model', 'bool-col', 'p.products_model'],
            ['PL_SHOW_MANUFACTURER', 'show_manufacturer', 'bool-col', 'p.manufacturers_id'],
            ['PL_SHOW_WEIGHT', 'show_weight', 'bool-col', 'p.products_weight'],
            ['PL_SHOW_SOH', 'show_stock', 'bool-col', 'p.products_quantity'],
            ['PL_SHOW_NOTES_A', 'show_notes_a', 'bool-col', ''],
            ['PL_SHOW_NOTES_B', 'show_notes_b', 'bool-col', ''],
            ['PL_SHOW_PRICE', 'show_price', 'bool-col', 'p.products_price'],
            ['PL_SHOW_TAX_FREE', 'show_taxfree', 'bool-col', 'p.products_price'],
            ['PL_SHOW_SPECIAL_PRICE', 'show_special_price', 'bool', ''],
            ['PL_SHOW_SPECIAL_DATE', 'show_special_date', 'bool', ''],
            ['PL_SHOW_ADDTOCART_BUTTON', 'show_cart_button', 'bool-col', ''],
            ['PL_ADDTOCART_TARGET', 'add_cart_target', 'char', ''],
            ['PL_SHOW_IMAGE', 'show_image', 'bool', 'p.products_image'],
            ['PL_IMAGE_PRODUCT_HEIGHT', 'image_height', 'int', ''],
            ['PL_IMAGE_PRODUCT_WIDTH', 'image_width', 'int', ''],
            ['PL_SHOW_DESCRIPTION', 'show_description', 'bool', ''],
            ['PL_TRUNCATE_DESCRIPTION', 'truncate_desc', 'int', ''],
            ['PL_SHOW_INACTIVE', 'show_inactive', 'bool', ''],
            ['PL_SORT_PRODUCTS_BY', 'sort_by', 'char', ''],
            ['PL_SORT_ASC_DESC', 'sort_dir', 'char', ''],
            ['PL_DEBUG', 'debug', 'bool', ''],
            ['PL_HEADER_LOGO', 'show_logo', 'bool', ''],
            ['PL_SHOW_PRICELIST_PAGE_HEADERS', 'show_headers', 'bool', ''],
            ['PL_SHOW_PRICELIST_PAGE_FOOTERS', 'show_footers', 'bool', ''],
        ];

        $this->header_columns = 1;
        $this->product_database_fields = '';
        foreach ($profile_settings as $current_setting) {
            list($key, $config_name, $type, $db_field) = $current_setting;
            $this->config[$config_name] = constant($key . '_' . $this->current_profile);
            if ($type === 'bool' || $type === 'bool-col') {
                $this->config[$config_name] = ($this->config[$config_name] === 'true');
                if ($type === 'bool-col' && $this->config[$config_name]) {
                    $this->header_columns++;
                }
            } elseif ($type === 'int') {
                $this->config[$config_name] = (int)$this->config[$config_name];
            }
            if ($db_field !== '' && $this->config[$config_name]) {
                $this->product_database_fields .= $db_field . ',';
            }
        }
        if ($this->config['show_description']) {
            $this->product_database_fields .= ($this->config['truncate_desc'] === '0') ? 'pd.products_description' : ('SUBSTR(pd.products_description, 1, ' . (int)$this->config['truncate_desc'] . ') AS products_description');
        }
        $this->product_database_fields = rtrim($this->product_database_fields, ',');  //-String trailing ','

        $this->products_sort_by = (($this->config['sort_by'] == 'products_name') ? 'pd.' : 'p.') . $this->config['sort_by'];

        // -----
        // Initialize categories and products to be displayed (updates $this->rows).
        //
        $this->initialize_pricelist_rows();

        // -----
        // If manufacturers' names are to be included, build up the array of id/value pairs.
        //
        $this->manufacturers_names = ['0' => '&nbsp;'];
        if ($this->config['show_manufacturer']) {
            $result = $db->Execute("SELECT manufacturers_id, manufacturers_name FROM " . TABLE_MANUFACTURERS . " ORDER BY manufacturers_name ASC");
            foreach ($result as $manufacturer) {
                $this->manufacturers_names[$manufacturer['manufacturers_id']] = $manufacturer['manufacturers_name'];
            }
            unset($result);
        }
        $this->currency_symbol = $currencies->currencies[$_SESSION['currency']]['symbol_left'] . $currencies->currencies[$_SESSION['currency']]['symbol_right'];
    }

    public function initialize_pricelist_rows()
    {
        global $db;

        $this->categories_status_clause = $this->products_status_clause = '';
        if (!$this->config['show_inactive']) {
            $this->categories_status_clause = ' AND c.categories_status = 1 ';
            $this->products_status_clause = ' AND p.products_status = 1';
        }
        $this->rows = [];
        if ($this->enabled) {
            $this->build_rows($this->current_category);
        }
    }

    public function build_rows($parent_category = 0, $level = 1)
    {
        global $db;
        $result = $db->Execute(
            "SELECT cd.categories_id, cd.categories_name, c.categories_status 
               FROM " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd
              WHERE c.parent_id = $parent_category
                AND c.categories_id = cd.categories_id 
                AND cd.language_id = " . $_SESSION['languages_id'] . $this->categories_status_clause . " 
              ORDER BY c.parent_id, c.sort_order, cd.categories_name"
        );   
        if ($result->EOF) {
            $this->get_products_in_category($parent_category);
        } else {
            foreach ($result as $fields) {
                $fields['level'] = $level;
                $fields['is_product'] = false;
                $this->rows[] = $fields;
                $this->build_rows($fields['categories_id'], $level+1);
            }
            unset($result, $fields);
        }
    }

    public function get_products_in_category($categories_id)
    {
        global $db;

        $categories_clause = ($this->config['master_cats_only']) ? " AND p.master_categories_id=$categories_id " : " AND c.categories_id=$categories_id ";
        $query = 
            'SELECT c.categories_id, c.categories_status, p.products_id, p.products_tax_class_id, p.products_status, pd.products_name, ' . $this->product_database_fields;
        $query .= 
            " FROM " . TABLE_PRODUCTS . " p
                LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd USING(products_id)
                LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " pc USING(products_id)
                LEFT JOIN " . TABLE_CATEGORIES . " c USING(categories_id)
             WHERE pd.language_id = " . $_SESSION['languages_id'] . $categories_clause . $this->products_status_clause . $this->categories_status_clause;
        $query .= ' ORDER BY ' . $this->products_sort_by  . ' ' . $this->config['sort_dir'];
        $result = $db->Execute($query);
        foreach ($result as $fields) {
            $fields['is_product'] = true;
            $this->rows[] = $fields;
            $this->product_count++;
        }
    }
  
    // -----
    // If a GROUP_NAME is defined for the profile, make sure that the customer is authorized to view the price-list profile.
    //
    public function group_is_valid($profile)
    {
        global $db;

        $group_name = (defined('PL_GROUP_NAME_' . $profile)) ? constant('PL_GROUP_NAME_' . $profile) : '';
        $group_is_valid = true;
        if ($group_name !== '') {
          $group_is_valid = false;
          if (zen_is_logged_in() && !zen_in_guest_checkout()) {
                $customer_group = $db->Execute(
                    "SELECT gp.group_name FROM " . TABLE_GROUP_PRICING . " gp, " . TABLE_CUSTOMERS . " c
                      WHERE c.customers_id = " . $_SESSION['customer_id'] . "
                        AND gp.group_id = c.customers_group_pricing
                      LIMIT 1"
                );
                $group_is_valid = (!$customer_group->EOF && stripos($customer_group->fields['group_name'], $group_name) === 0);
            }
        }
        return $group_is_valid;
    }

    // -----
    // Returns an ordered list containing links to the profiles that are valid for the current customer.
    //
    public function get_profiles()
    {
        for ($profile = 1, $profile_count = 0, $profiles_list = "<ul>\n"; $profile <= 10; $profile++) {
            $profile_enabled = (defined('PL_ENABLE_' . $profile)) ? (constant('PL_ENABLE_' . $profile) == 'true') : false;
            if (!$this->group_is_valid($profile)) {
                $profile_enabled = false;
            }
            if ($profile_enabled) {
                $profile_count++;
                $selected = ($profile == $this->current_profile) ? ' class="selectedPL"' : '';
                $name = (defined('PL_PROFILE_NAME_' . $profile)) ? constant('PL_PROFILE_NAME_' . $profile) : '--unknown--';
                $profiles_list .= '<li' . $selected . '><a href="' . zen_href_link(FILENAME_PRICELIST, 'profile=' . $profile) . '">' . $name . "</a></li>\n";
            }
        }
        return ($profile_count > 1) ? ($profiles_list . "</ul>\n") : '';
    }

    // -----
    // Adapted version of zen_get_category_tree() function (from zen admin)
    //
    public function get_category_list($parent_id = 0, $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false, $main_cats_only = false)
    {
        global $db;
        if (!is_array($category_tree_array)) {
            $category_tree_array = [['id' => '0', 'text' => TEXT_PL_CATEGORIES]];
        }

        if ($include_itself) {
            $category = $db->Execute(
                "SELECT cd.categories_name
                   FROM " . TABLE_CATEGORIES_DESCRIPTION . " cd
                  WHERE cd.language_id = " . (int)$_SESSION['languages_id'] . "
                    AND cd.categories_id = " . (int)$parent_id . "
                  LIMIT 1"
            );
            $category_tree_array[] = ['id' => $parent_id, 'text' => $category->fields['categories_name']];
        }

        $categories = $db->Execute(
            "SELECT c.categories_id, cd.categories_name, c.parent_id
               FROM " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd
              WHERE c.categories_id = cd.categories_id
                AND cd.language_id = " . (int)$_SESSION['languages_id'] . "
                AND c.parent_id = " . (int)$parent_id . "
                AND c.categories_status = 1
           ORDER BY c.sort_order, cd.categories_name"
        );

        foreach ($categories as $category) {
            if ($exclude != $category['categories_id']) {
                $category_tree_array[] = ['id' => $categories->fields['categories_id'], 'text' => $spacing . $categories->fields['categories_name']];
            }
            if (!$main_cats_only) {
                $category_tree_array = $this->get_category_list($categories->fields['categories_id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $category_tree_array, $include_itself, $main_cats_only);
            }
        }
        return $category_tree_array;
    }

    // -----
    // Return the price, without either the left- or right-currency symbol.
    //
    public function display_price($price_raw, $tax_percentage = 0)
    {
        global $currencies;

        $price = $currencies->format($price_raw * (1 + $tax_percentage / 100));
        $price = str_replace([$currencies->currencies[$_SESSION['currency']]['symbol_left'], $currencies->currencies[$_SESSION['currency']]['symbol_right']], '', $price);

        return $price;
    }

    // -----
    // Return a product's special price expiration date (returns nothing if there is no offer)
    //
    function get_products_special_date($product_id)
    {
        //PL_SHOW_SPECIAL_DATE
        // note that zen_get_products_special_price() by default also looks pricing by attributes and other discounts
        // for those features the date returned by this function probably is invalid
        global $db;
        $specials = $db->Execute("SELECT expires_date FROM " . TABLE_SPECIALS . " WHERE products_id = " . $product_id . " LIMIT 1");
        return (!$specials->EOF && $specials->fields['expires_date'] != '0001-01-01') ? zen_date_short($specials->fields['expires_date']) : false;
    }
}

// -----
// Instantiate the price list for use by the template.
// -----
$price_list = new price_list;
