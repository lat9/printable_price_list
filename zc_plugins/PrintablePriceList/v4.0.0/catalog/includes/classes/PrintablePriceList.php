<?php
// -----
// Define the class that provides the price-list support functions.
//
class PrintablePriceList extends base
{
    public array $manufacturersNames;
    public int $productCount = 0;
    public int $currentProfile;
    protected bool $enabled;
    public int $headerColumns;
    protected string $productDatabaseFields;
    public array $config = [];
    protected string $productsSortBy;
    public int $currentCategory;
    public string $currencySymbol;

    protected string $productsStatusClause;
    protected string $categoriesStatusClause;
    protected string $additionalJoins;

    public array $rows;

    public function __construct()
    {
        global $db, $currencies;

        $this->currentProfile = (int)($_GET['profile'] ?? PL_DEFAULT_PROFILE);
        $this->enabled = (constant('PL_ENABLE_' . $this->currentProfile) === 'true');

        // -----
        // This array, one element per profile-specific configuration setting, contains three required and one optional element:
        //
        // [0] ... The configuration setting "key" name (suffixed with _x where x is the profile number)
        // [1] ... The name of the class-based config array element into which the setting's value is stored
        // [2] ... The "type" (bool, int, or char), to which the value is converted
        // [3] ... (optional) If not 'empty', contains the database element that should be retrieved for the display.
        //
        $profile_settings = [
            ['PL_GROUP_NAME', 'group_name', 'char', ''],
            ['PL_PROFILE_NAME', 'profile_name', 'char', ''],
            ['PL_INCLUDED_PRODUCTS', 'included_products', 'char', ''],
            ['PL_START_CATEGORY', 'start_category', 'char', ''],
            ['PL_USE_MASTER_CATS_ONLY', 'master_cats_only', 'bool', ''],
            ['PL_SHOW_BOXES', 'show_boxes', 'bool', ''],
            ['PL_CATEGORY_TREE_MAIN_CATS_ONLY', 'main_cats_only', 'bool', ''],
            ['PL_MAINCATS_NEW_PAGE', 'maincats_new_page', 'bool', ''],
            ['PL_SHOW_ATTRIBUTES', 'show_attributes', 'bool', ''],
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

        $this->headerColumns = 1;
        $this->productDatabaseFields = '';
        foreach ($profile_settings as $current_setting) {
            [$key, $config_name, $type, $db_field] = $current_setting;
            $this->config[$config_name] = constant($key . '_' . $this->currentProfile);
            if ($type === 'bool' || $type === 'bool-col') {
                $this->config[$config_name] = ($this->config[$config_name] === 'true');
                if ($type === 'bool-col' && $this->config[$config_name] === true) {
                    $this->headerColumns++;
                }
            } elseif ($type === 'int') {
                $this->config[$config_name] = (int)$this->config[$config_name];
            }
            if ($db_field !== '' && $this->config[$config_name]) {
                $this->productDatabaseFields .= $db_field . ',';
            }
        }
        if ($this->config['show_description']) {
            $this->productDatabaseFields .= ($this->config['truncate_desc'] === 0) ? 'pd.products_description' : ('SUBSTR(pd.products_description, 1, ' . (int)$this->config['truncate_desc'] . ') AS products_description');
        }
        $this->productDatabaseFields = rtrim($this->productDatabaseFields, ',');  //-Strip trailing ','

        $this->productsSortBy = (($this->config['sort_by'] === 'products_name') ? 'pd.' : 'p.') . $this->config['sort_by'];

        // -----
        // If *all* categories are to be displayed and a category has been selected from the template page's dropdown, remember it!
        //
        $this->currentCategory = 0;
        if ($this->config['included_products'] === 'all' && isset($_GET['plCat'])) {
            $this->currentCategory = (int)$_GET['plCat'];
        } elseif ($this->config['included_products'] === 'category') {
            $this->currentCategory = (int)constant('PL_START_CATEGORY_' . $this->currentProfile);
        }

        // -----
        // Initialize categories and products to be displayed (updates $this->rows).
        //
        $this->initializePricelistRows();

        // -----
        // If manufacturers' names are to be included, build up the array of id/value pairs.
        //
        $this->manufacturersNames = ['0' => '&nbsp;'];
        if ($this->config['show_manufacturer']) {
            $result = $db->Execute("SELECT manufacturers_id, manufacturers_name FROM " . TABLE_MANUFACTURERS . " ORDER BY manufacturers_name ASC");
            foreach ($result as $manufacturer) {
                $this->manufacturersNames[$manufacturer['manufacturers_id']] = $manufacturer['manufacturers_name'];
            }
            unset($result);
        }
        $this->currencySymbol = $currencies->currencies[$_SESSION['currency']]['symbol_left'] . $currencies->currencies[$_SESSION['currency']]['symbol_right'];
    }

    public function getCurrentProfile(): int
    {
        return $this->currentProfile;
    }

    protected function initializePricelistRows()
    {
        $this->categoriesStatusClause = '';
        $this->productsStatusClause = '';
        $this->additionalJoins = '';

        if (!$this->config['show_inactive']) {
            $this->categoriesStatusClause = ' AND c.categories_status = 1 ';
            $this->productsStatusClause = ' AND p.products_status = 1';
        }

        if ($this->config['included_products'] === 'featured') {
            $this->additionalJoins = ' LEFT JOIN ' . TABLE_FEATURED . ' AS f USING(products_id) ';
            $this->productsStatusClause .= ' AND f.status = 1';
        } elseif ($this->config['included_products'] === 'specials') {
            $this->additionalJoins = ' LEFT JOIN ' . TABLE_SPECIALS . ' AS s USING(products_id) ';
            $this->productsStatusClause .= ' AND s.status = 1';
        }

        $this->rows = [];
        if ($this->enabled === true) {
            $this->buildRows($this->currentCategory);
        }
    }

    protected function buildRows($parent_category = 0, $level = 1)
    {
        global $db;

        $result = $db->Execute(
            "SELECT cd.categories_id, cd.categories_name, c.categories_status 
               FROM " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd
              WHERE c.parent_id = $parent_category
                AND c.categories_id = cd.categories_id 
                AND cd.language_id = " . $_SESSION['languages_id'] .
                $this->categoriesStatusClause . " 
              ORDER BY c.parent_id, c.sort_order, cd.categories_name"
        );
        $parent_index = count($this->rows) - 1;
        $current_product_count = $this->productCount;
        if ($result->EOF) {
            $category_index = count($this->rows) - 1;
            $this->rows[$category_index]['product_count'] = $this->getProductsInCategory($parent_category);
        } else {
            foreach ($result as $fields) {
                $fields['level'] = $level;
                $fields['is_product'] = false;
                $this->rows[] = $fields;
                $this->buildRows($fields['categories_id'], $level+1);
            }
            unset($result, $fields);
        }
        if ($parent_index !== -1) {
            $this->rows[$parent_index]['product_count'] = $this->productCount - $current_product_count;
        }
    }

    protected function getProductsInCategory($categories_id): int
    {
        global $db;

        $categories_clause = ($this->config['master_cats_only']) ? " AND p.master_categories_id = $categories_id " : " AND c.categories_id = $categories_id ";
        $query =
            "SELECT c.categories_id, c.categories_status, 
                    p.products_id, p.products_tax_class_id, p.products_status, p.products_priced_by_attribute, p.product_is_free,
                    pd.products_name, " . $this->productDatabaseFields . "
               FROM " . TABLE_PRODUCTS . " p
                    LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd USING(products_id)
                    LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " pc USING(products_id)
                    LEFT JOIN " . TABLE_CATEGORIES . " c USING(categories_id) " .
                    $this->additionalJoins . "
             WHERE pd.language_id = " . $_SESSION['languages_id'] .
                $categories_clause .
                $this->productsStatusClause .
                $this->categoriesStatusClause . "
             ORDER BY " . $this->productsSortBy  . ' ' . $this->config['sort_dir'];
        $result = $db->Execute($query);
        $current_product_count = $this->productCount;
        foreach ($result as $fields) {
            $fields['is_product'] = true;
            if ($this->config['show_attributes']) {
                if (PRODUCTS_OPTIONS_SORT_ORDER == '0') {
                    $order_by = ' ORDER BY LPAD(po.products_options_sort_order,11,"0"), po.products_options_name';
                } else {
                    $order_by = ' ORDER BY po.products_options_name';
                }
                if (PRODUCTS_OPTIONS_SORT_BY_PRICE === '1') {
                    $order_by .= ', LPAD(pa.products_options_sort_order,11,"0"), pov.products_options_values_name';
                } else {
                    $order_by .= ',  LPAD(pa.products_options_sort_order,11,"0"), pa.options_values_price';
                }
                $attributes = $db->Execute(
                    "SELECT po.products_options_name, po.products_options_type, pov.products_options_values_name, pa.*
                       FROM  " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                            LEFT JOIN " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov
                                ON pa.options_values_id = pov.products_options_values_id
                               AND pov.language_id = " . $_SESSION['languages_id'] . "
                            LEFT JOIN " . TABLE_PRODUCTS_OPTIONS . " po
                                ON pa.options_id = po.products_options_id
                               AND po.language_id = " . $_SESSION['languages_id'] . "
                      WHERE pa.products_id = " . $fields['products_id'] . "
                        AND pa.attributes_display_only != 1" . $order_by
                );

                $fields['attributes'] = [];
                $options_id = false;
                $product_priced_by_attributes = ($fields['products_priced_by_attribute'] === '1');
                $product_is_free = ($fields['product_is_free'] === '1');
                foreach ($attributes as $next_variant) {
                    if ($options_id !== $next_variant['options_id']) {
                        $options_id = $next_variant['options_id'];
                        $fields['attributes'][$options_id] = [
                            'name' => $next_variant['products_options_name'],
                            'discounts_available' => false,
                            'option_type' => $next_variant['products_options_type'],
                            'values' => []
                        ];
                    }

                    if (!empty($next_variant['attributes_qty_prices']) || !empty($next_variant['attributes_qty_prices_onetime'])) {
                        $fields['attributes'][$options_id]['discounts_available'] = true;
                    }

                    $variant_values = [
                        'name' => $next_variant['products_options_values_name'],
                        'price_prefix' => $next_variant['price_prefix'],
                        'is_free' => ($next_variant['product_attribute_is_free'] === '1'),
                        'included_in_base' => ($next_variant['attributes_price_base_included'] === '1'),
                    ];

                    // -----
                    // TEXT-type variants might include per-word or per-letter pricing.
                    //
                    if ($next_variant['products_options_type'] === '1') {
                        $text_values = [
                            'price_per_word' => ($next_variant['attributes_price_words'] === '0.0000') ? 0 : $next_variant['attributes_price_words'],
                            'free_words' => $next_variant['attributes_price_words_free'],
                            'price_per_letter' => ($next_variant['attributes_price_letters'] === '0.0000') ? 0 : $next_variant['attributes_price_letters'],
                            'free_letters' => $next_variant['attributes_price_letters_free'],
                        ];
                        $variant_values = array_merge($variant_values, $text_values);
                    }

                    if ($next_variant['attributes_discounted'] === '1') {
                        $variant_values['price'] = zen_get_attributes_price_final($next_variant['products_attributes_id'], 1, '', 'false', $product_priced_by_attributes);
                    } else {
                        $variant_values['price'] = $next_variant['options_values_price'];

                        // -----
                        // If the attribute's price is 0, set it to an (int) 0 so that follow-on checks
                        // using empty() will find that value 'empty'.
                        //
                        if ($variant_values['price'] === '0.0000') {
                            $variant_values['price'] = 0;
                        }
                        if ($variant_values['price'] < 0) {
                            $variant_values['price'] = -$variant_values['price'];
                        }

                        if ($next_variant['attributes_price_onetime'] !== '0.0000' || $next_variant['attributes_price_factor_onetime'] !== '0.0000') {
                            $variant_values['onetime'] = zen_get_attributes_price_final_onetime($next_variant['products_attributes_id'], 1, '');
                        } else {
                            $variant_values['onetime'] = false;
                        }
                    }
                    $fields['attributes'][$options_id]['values'][] = $variant_values;
                }
            }
            $this->rows[] = $fields;
            $this->productCount++;
        }
        return $this->productCount - $current_product_count;
    }

    // -----
    // If a GROUP_NAME is defined for the profile, make sure that the customer is authorized to view the price-list profile.
    //
    public function groupIsValid($profile): bool
    {
        global $db;

        $group_name = constant('PL_GROUP_NAME_' . $profile);
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
    public function getProfiles(): string
    {
        for ($profile = 1, $profile_count = 0, $profiles_list = "<ul>\n"; $profile <= 3; $profile++) {
            $profile_enabled = (constant('PL_ENABLE_' . $profile) === 'true');
            if (!$this->groupIsValid($profile)) {
                $profile_enabled = false;
            }
            if ($profile_enabled === true) {
                $profile_count++;
                $selected = ($profile == $this->currentProfile) ? ' class="selectedPL"' : '';
                $name = constant('PL_PROFILE_NAME_' . $profile);
                $profiles_list .= '<li' . $selected . '><a href="' . zen_href_link(FILENAME_PRICELIST, 'profile=' . $profile) . '">' . $name . "</a></li>\n";
            }
        }
        return ($profile_count > 1) ? ($profiles_list . "</ul>\n") : '';
    }

    // -----
    // Adapted version of zen_get_category_tree() function (from zen admin)
    //
    public function getCategoryList($parent_id = 0, $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false, $main_cats_only = false): array
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
    // Return the price, without either the left- or right-currency symbol.  Prices are calculated with
    // tax **only if** the site's configured to display prices with tax.
    //
    public function displayPrice($price_raw, $tax_percentage = 0)
    {
        global $currencies;

        if (DISPLAY_PRICE_WITH_TAX !== 'true') {
            $tax_percentage = 0;
        }
        $price = $currencies->format($price_raw * (1 + $tax_percentage / 100));
        $price = str_replace([$currencies->currencies[$_SESSION['currency']]['symbol_left'], $currencies->currencies[$_SESSION['currency']]['symbol_right']], '', $price);

        return $price;
    }

    // -----
    // Return a product's special price expiration date (returns nothing if there is no offer)
    //
    public function getProductsSpecialDate($product_id)
    {
        //PL_SHOW_SPECIAL_DATE
        // note that zen_get_products_special_price() by default also looks pricing by attributes and other discounts
        // for those features the date returned by this function probably is invalid
        global $db;
        $specials = $db->Execute("SELECT expires_date FROM " . TABLE_SPECIALS . " WHERE products_id = " . $product_id . " LIMIT 1");
        return (!$specials->EOF && $specials->fields['expires_date'] != '0001-01-01') ? zen_date_short($specials->fields['expires_date']) : false;
    }
}