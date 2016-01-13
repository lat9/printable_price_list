<?php
/** 
 * @copyright Copyright 2003-2007 Paul Mathot Haarlem, The Netherlands & Carine Bruyndoncx, Belgium
 * @copyright parts Copyright 2003-2005 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version v1.5.0 (or newer)
 */
// -----
// Part of the Printable Price List plugin for Zen Cart v1.5.1 and later.
// Copyright (C) 2014, Vinos de Frutas Tropicales (lat9)
//
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
require(DIR_WS_MODULES . 'require_languages.php');

// -----
// Define the class that provides the price-list support functions.
//
class price_list extends base {
  var $current_profile;
  var $config;
  var $manufacturers_names;
  var $header_columns;
  var $products_sort_by;

  function __construct() {
    global $db, $currencies;
    
    $this->product_count = 0;
     
    // -----
    // If a category has been selected from the template page's dropdown, remember it!
    //
    $this->current_category = (isset ($_GET['plCat'])) ? ((int)$_GET['plCat']) : 0;
       
    $this->current_profile = (isset ($_GET['profile'])) ? ((int)$_GET['profile']) : PL_DEFAULT_PROFILE;
    if (!defined ('PL_ENABLE_' . $this->current_profile)) {
      $this->current_profile = PL_DEFAULT_PROFILE;
      
    }
    $this->enabled = (constant ('PL_ENABLE_' . $this->current_profile) == 'true');
    
    // -----
    // This array, one element per profile-specific configuration setting, contains three required and one optional element:
    //
    // [0] ... The configuration setting "key" name (suffixed with _x where x is the profile number)
    // [1] ... The name of the class-based config array into which the setting value is stored
    // [2] ... The "type" (bool, int, or char), to which the value is converted
    // [3] ... (optional) If set, contains the database element that should be retrieved for the display.
    //
    $profile_settings = array (
      array ('PL_GROUP_NAME', 'group_name', 'char'),
      array ('PL_PROFILE_NAME', 'profile_name', 'char'),
      array ('PL_USE_MASTER_CATS_ONLY', 'master_cats_only', 'bool'),
      array ('PL_SHOW_BOXES', 'show_boxes', 'bool'),
      array ('PL_CATEGORY_TREE_MAIN_CATS_ONLY', 'main_cats_only', 'bool'),
      array ('PL_MAINCATS_NEW_PAGE', 'maincats_new_page', 'bool'),
      array ('PL_NOWRAP', 'nowrap', 'bool'),
      array ('PL_SHOW_MODEL', 'show_model', 'bool-col', 'p.products_model'),
      array ('PL_SHOW_MANUFACTURER', 'show_manufacturer', 'bool-col', 'p.manufacturers_id'),
      array ('PL_SHOW_WEIGHT', 'show_weight', 'bool-col', 'p.products_weight'),
      array ('PL_SHOW_SOH', 'show_stock', 'bool-col', 'p.products_quantity'),
      array ('PL_SHOW_NOTES_A', 'show_notes_a', 'bool-col'),
      array ('PL_SHOW_NOTES_B', 'show_notes_b', 'bool-col'),
      array ('PL_SHOW_PRICE', 'show_price', 'bool-col', 'p.products_price'),
      array ('PL_SHOW_TAX_FREE', 'show_taxfree', 'bool-col', 'p.products_price'),
      array ('PL_SHOW_SPECIAL_PRICE', 'show_special_price', 'bool'),
      array ('PL_SHOW_SPECIAL_DATE', 'show_special_date', 'bool'),
      array ('PL_SHOW_ADDTOCART_BUTTON', 'show_cart_button', 'bool-col'),
      array ('PL_ADDTOCART_TARGET', 'add_cart_target', 'char'),
      array ('PL_SHOW_IMAGE', 'show_image', 'bool', 'p.products_image'),
      array ('PL_IMAGE_PRODUCT_HEIGHT', 'image_height', 'int'),
      array ('PL_IMAGE_PRODUCT_WIDTH', 'image_width', 'int'),
      array ('PL_SHOW_DESCRIPTION', 'show_description', 'bool'),
      array ('PL_TRUNCATE_DESCRIPTION', 'truncate_desc', 'int'),
      array ('PL_SHOW_INACTIVE', 'show_inactive', 'bool'),
      array ('PL_SORT_PRODUCTS_BY', 'sort_by', 'char'),
      array ('PL_SORT_ASC_DESC', 'sort_dir', 'char'),
      array ('PL_DEBUG', 'debug', 'bool'),
      array ('PL_HEADER_LOGO', 'show_logo', 'bool'),
      array ('PL_SHOW_PRICELIST_PAGE_HEADERS', 'show_headers', 'bool'),
      array ('PL_SHOW_PRICELIST_PAGE_FOOTERS', 'show_footers', 'bool'),

    
    );
    
    $this->header_columns = 1;
    $this->product_database_fields = '';
    foreach ($profile_settings as $current_setting) {
      $this->config[$current_setting[1]] = constant($current_setting[0] . '_' . $this->current_profile);
      if ($current_setting[2] == 'bool' || $current_setting[2] == 'bool-col') {
        $this->config[$current_setting[1]] = ($this->config[$current_setting[1]] == 'true');
        if ($current_setting[2] == 'bool-col' && $this->config[$current_setting[1]]) {
          $this->header_columns++;
          
        }
      } elseif ($current_setting[2] == 'int') {
        $this->config[$current_setting[1]] = (int)$this->config[$current_setting[1]];
        
      }
      if (isset ($current_setting[3]) && $this->config[$current_setting[1]]) {
        $this->product_database_fields .= $current_setting[3] . ',';
        
      }
    }
    if ($this->config['show_description']) {
      $this->product_database_fields .= ($this->config['truncate_desc'] == 0) ? 'pd.products_description' : ('SUBSTR(pd.products_description,1,' . $this->config['truncate_desc'] . ') AS products_description');
      
    }
    $this->product_database_fields = rtrim ($this->product_database_fields, ',');  //-String trailing ','

    $this->products_sort_by = (($this->config['sort_by'] == 'products_name') ? 'pd.' : 'p.') . $this->config['sort_by'];
    
    // -----
    // Initialize categories and products to be displayed (updates $this->rows).
    //
    $this->initialize_pricelist_rows ();
    
    // -----
    // If manufacturers' names are to be included, build up the array of id/value pairs.
    //
    $this->manufacturers_names = array ( 0 => '&nbsp;' );
    if ($this->config['show_manufacturer']) {
      $result = $db->Execute ("SELECT manufacturers_id, manufacturers_name FROM " . TABLE_MANUFACTURERS);
      while (!$result->EOF) {
        $this->manufacturers_names[$result->fields['manufacturers_id']] = $result->fields['manufacturers_name'];
        $result->MoveNext ();
        
      }
      unset ($result);
      
    }
    
    $this->currency_symbol = $currencies->currencies[$_SESSION['currency']]['symbol_left'] . $currencies->currencies[$_SESSION['currency']]['symbol_right'];
    
  }  //-END __construct
  
  function initialize_pricelist_rows () {
    global $db;
    
    $this->categories_status_clause = $this->products_status_clause = '';
    if (!$this->config['show_inactive']) {
      $this->categories_status_clause = ' AND c.categories_status = 1 ';
      $this->products_status_clause = ' AND p.products_status = 1';
      
    }
    $this->rows = array ();
    if ($this->enabled) {
      $this->build_rows ($this->current_category);
      
    }     
  }  //-END function initialize_categories
  
  function build_rows ($parent_category = 0, $level = 1) {
    global $db;
    $result = $db->Execute ("SELECT cd.categories_id, cd.categories_name, c.categories_status 
                               FROM " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd
                             WHERE  c.parent_id = $parent_category
                                AND c.categories_id = cd.categories_id 
                                AND cd.language_id = " . $_SESSION['languages_id'] . $this->categories_status_clause . " 
                           ORDER BY c.parent_id, c.sort_order, cd.categories_name");   
    
    if ($result->EOF) {
      $this->get_products_in_category ($parent_category);
      
    } else {
      while (!$result->EOF) {
        $result->fields['level'] = $level;
        $result->fields['is_product'] = false;
        $this->rows[] = $result->fields;
        $this->build_rows ($result->fields['categories_id'], $level+1);
        $result->MoveNext ();
        
      }
      unset ($result);
      
    }
  }

  /* get products from db per category */
  function get_products_in_category ($categories_id) {
    global $db;

    $categories_clause = ($this->config['master_cats_only']) ? " AND p.master_categories_id=$categories_id " : " AND c.categories_id=$categories_id ";
    $query = 'SELECT c.categories_id, c.categories_status, p.products_id, p.products_tax_class_id, p.products_status, pd.products_name, ' . $this->product_database_fields;
    $query .= " FROM " . TABLE_PRODUCTS . " p LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd USING(products_id) LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " pc USING(products_id) LEFT JOIN " . TABLE_CATEGORIES . " c USING(categories_id) WHERE pd.language_id=" . $_SESSION['languages_id'] . $categories_clause . $this->products_status_clause . $this->categories_status_clause;
    $query .= ' ORDER BY ' . $this->products_sort_by  . ' ' . $this->config['sort_dir'];
    $result = $db->Execute ($query);
    while (!$result->EOF) {
      $result->fields['is_product'] = true;
      $this->rows[] = $result->fields;
      $this->product_count++;
      $result->MoveNext ();

    }
  }  //-END function get_products_per_cat
  
  // -----
  // If a GROUP_NAME is defined for the profile, make sure that the customer is authorized to view the price-list profile.
  //
  function group_is_valid ($profile) {
    global $db;
    
    $group_name = (defined ('PL_GROUP_NAME_' . $profile)) ? constant ('PL_GROUP_NAME_' . $profile) : '';
    $group_is_valid = true;
    if ($group_name != '') {
      $group_is_valid = false;
      if (isset ($_SESSION['customer_id'])) {
        $customer_group = $db->Execute ("SELECT gp.group_name FROM " . TABLE_GROUP_PRICING . " gp, " . TABLE_CUSTOMERS . " c
                                          WHERE c.customers_id = " . $_SESSION['customer_id'] . "
                                            AND gp.group_id = c.customers_group_pricing LIMIT 1");
        $group_is_valid = (!$customer_group->EOF && stripos ($customer_group->fields['group_name'], $group_name) === 0);
        
      }
    }
    return $group_is_valid;
     
  }  //-END function group_is_valid
  
  // -----
  // Returns an ordered list containing links to the profiles that are valud for the current customer.
  //
  function get_profiles ()  {
    for ($profile = 1, $profile_count = 0, $profiles_list = "<ul>\n"; $profile <= 10; $profile++) {
      $profile_enabled = (defined ('PL_ENABLE_' . $profile)) ? (constant ('PL_ENABLE_' . $profile) == 'true') : false;
      if (!$this->group_is_valid ($profile)) {
        $profile_enabled = false;
        
      }
      if ($profile_enabled) {
        $profile_count++;
        $selected = ($profile == $this->current_profile) ? ' class="selectedPL"' : '';
        $name = (defined ('PL_PROFILE_NAME_' . $profile)) ? constant ('PL_PROFILE_NAME_' . $profile) : '--unknown--';
        $profiles_list .= '<li' . $selected . '><a href="' . zen_href_link (FILENAME_PRICELIST, 'profile=' . $profile) . '">' . $name . "</a></li>\n";
        
      }
    }
    return ($profile_count > 1) ? ($profiles_list . "</ul>\n") : '';

  }  //-END function get_profiles

  // -----
  // Adapted version of zen_get_category_tree() function (from zen admin)
  //
  function get_category_list ($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false, $main_cats_only = false) {
    global $db;
    if (!is_array ($category_tree_array)) {
      $category_tree_array = array ( array ('id' => '0', 'text' => TEXT_PL_CATEGORIES) );
      
    }

    if ($include_itself) {
      $category = $db->Execute ("SELECT cd.categories_name
                                   FROM " . TABLE_CATEGORIES_DESCRIPTION . " cd
                                  WHERE cd.language_id = '" . (int)$_SESSION['languages_id'] . "'
                                    AND cd.categories_id = '" . (int)$parent_id . "' LIMIT 1");

      $category_tree_array[] = array('id' => $parent_id, 'text' => $category->fields['categories_name']);
      
    }

    $categories = $db->Execute("SELECT c.categories_id, cd.categories_name, c.parent_id
                                  FROM " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd
                                 WHERE c.categories_id = cd.categories_id
                                   AND cd.language_id = '" . (int)$_SESSION['languages_id'] . "'
                                   AND c.parent_id = '" . (int)$parent_id . "'
                                   AND c.categories_status= '1'
                              ORDER BY c.sort_order, cd.categories_name");

    while (!$categories->EOF) {
      if ($exclude != $categories->fields['categories_id']) {
        $category_tree_array[] = array('id' => $categories->fields['categories_id'], 'text' => $spacing . $categories->fields['categories_name']);
        
      }
      if (!$main_cats_only) {
        $category_tree_array = $this->get_category_list ($categories->fields['categories_id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $category_tree_array, $include_itself, $main_cats_only);
        
      }
      $categories->MoveNext();
      
    }
    return $category_tree_array;
    
  }  //-END function get_category_tree

  // -----
  // Return the price, without either the left- or right-currency symbol.
  //
  function display_price ($price_raw, $tax = 0){
    global $currencies;
    $price = $currencies->display_price ($price_raw, $tax);
    $price = str_replace($currencies->currencies[$_SESSION['currency']]['symbol_left'], '', $price);
    $price = str_replace($currencies->currencies[$_SESSION['currency']]['symbol_right'], '', $price);          
    
    return $price;
    
  }  //-END function display_price

  // -----
  // Return a product's special price expiration date (returns nothing if there is no offer)
  //
  function get_products_special_date ($product_id){
    //PL_SHOW_SPECIAL_DATE
    // note that zen_get_products_special_price() by default also looks pricing by attributes and other discounts
    // for those features the date returned by this function probably is invalid
    global $db;
    $specials = $db->Execute ("SELECT expires_date FROM " . TABLE_SPECIALS . " WHERE products_id = '" . $product_id . "' LIMIT 1");
    return (!$specials->EOF && $specials->fields['expires_date'] != '0001-01-01') ? zen_date_short ($specials->fields['expires_date']) : false;

  }  //-END function get_products_special_date
  
}  //-END price-list class definition

// -----
// Instantiate the price list for use by the template.
// -----
$price_list = new price_list;
