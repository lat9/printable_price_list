<?php
// -----
// Part of the "Printable Price List" plugin by lat9
// Copyright (C) 2024, https://vinosdefrutastropicales.com
//
// Last updated: v4.0.0
//
use Zencart\PluginSupport\ScriptedInstaller as ScriptedInstallBase;

class ScriptedInstaller extends ScriptedInstallBase
{
    protected function executeInstall()
    {
        // -----
        // First, determine the main options' configuration-group-id and
        // install the main options.
        //
        $cgi = $this->installConfigGroup(
            'Printable Price-list',
            'The main options for the printable price-list module are stored here.'
        );

        $sql =
        "INSERT IGNORE INTO " . TABLE_CONFIGURATION . "
            (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added)
         VALUES
            ('Default Profile', 'PL_DEFAULT_PROFILE', '1', 'Choose the default profile to use.', $cgi, 10, NULL , 'zen_cfg_select_option([\'1\', \'2\', \'3\'],', now()),

            ('Show Profile Links?', 'PL_SHOW_PROFILES', 'true', 'Choose <em>true</em> to display links to the currently-enabled profiles on the <em>pricelist</em> page.', $cgi, 20, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now()),

            ('Show Information Sidebox Link?', 'PL_SHOW_INFO_LINK', 'true', 'Choose whether (<em>true</em>) or not (<em>false</em>) a &quot;Price List&quot; link should be shown in the Information sidebox.', $cgi, 30, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now()),

            ('Information Sidebox Link Position', 'PL_INFO_LINK_POSITION', '100', 'If the &quot;Price List&quot; link is to be shown in the Information sidebox, identify the <em>numeric</em> position at which that link is inserted. Enter <code>1</code> for the first position in the list, <code>2</code> for the second and so on. To position as the last entry, enter a number larger than the number of items currently in the sidebox\'s list.<br>', $cgi, 32, NULL, NULL, now()),

            ('Information Sidebox Link New Page?', 'PL_INFO_LINK_NEW_PAGE', 'true', 'If the &quot;Price List&quot; link is to be shown in the Information sidebox, indicate whether (<code>true</code>, the default) or not a click of the link should open in a new page.', $cgi, 34, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now()),

            ('Include currency symbol in pricelist header?', 'PL_INCLUDE_CURRENCY_SYMBOL', 'true', 'Choose whether (<em>true</em>) or not (<em>false</em>) the currently-selected currencies\' symbol should be included in the pricelist print-out.', $cgi, 40, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now())";
        $this->executeInstallerSql($sql);

        zen_deregister_admin_pages(['configPrintablePricelist']);
        zen_register_admin_page(
            'configPrintablePricelist', 'BOX_CONFIGURATION_PL', 'FILENAME_CONFIGURATION', "gID=$cgi", 'configuration', 'Y'
        );

        // -----
        // Next, install the three (3) profiles' configurations.
        //
        for ($profile = 1; $profile <= 3; $profile++) {
            // -----
            // Rename existing configuration key for consistent naming strategy.
            //
            if (defined("TEXT_PL_HEADER_LOGO_$profile")) {
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET configuration_key = 'PL_HEADER_LOGO_$profile' WHERE configuration_key = 'TEXT_PL_HEADER_LOGO_$profile' LIMIT 1");
            }
            $cgi = $this->installConfigGroup(
                "Price-list Profile-$profile",
                "Settings for printable price-list profile-$profile."
            );

            $sql =
                "INSERT IGNORE INTO " . TABLE_CONFIGURATION . "
                    (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added)
                 VALUES
                    ('Enable Profile?', 'PL_ENABLE_$profile', 'true', 'Choose <em>true</em> to enable this price-list profile to be used on the <em>pricelist</em> page.', $cgi, 10, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now()),

                    ('Group Name', 'PL_GROUP_NAME_$profile', '', 'Set this field to a <b>Group Name</b> (see <em>Customers->Group Pricing</em>) to enable this profile <em>only</em> for customers in that group. Leave the field empty for the profile to apply to all customers.', $cgi, 15, NULL, NULL, now()),

                    ('Profile Name', 'PL_PROFILE_NAME_$profile', 'Product Profile $profile', 'Give this profile a name.', $cgi, 20, NULL, NULL, now()),

                    ('Products to include?', 'PL_INCLUDED_PRODUCTS_$profile', 'all', 'Choose the products to be included in this price-list:<ul><li><b>all</b>: Displays all products</li><li><b>featured</b>: Displays all currently-featured products <em>only</em>.</li><li><b>specials</b>: Displays all products on special.</li><li><b>category</b>: Displays products associated with the category identified in the <em>Starting Category</em> setting, below.</li></ul>', $cgi, 25, NULL, 'zen_cfg_select_option([\'all\', \'featured\', \'specials\', \'category\'],', now()),

                    ('Starting Category', 'PL_START_CATEGORY_$profile', '0', 'If including only products from a specific category, identify that <code>categories_id</code> here.', $cgi, 26, NULL, NULL, now()),

                    ('Display Linked Products?', 'PL_USE_MASTER_CATS_ONLY_$profile', 'false', 'Should products be listed under all linked categories (<em>false</em>) or only under their master-category (<em>true</em>)?', $cgi, 32, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now()),

                    ('Show Selections?', 'PL_SHOW_BOXES_$profile', 'true', 'Set this value to <em>true</em> to display language and currency selections as well as a categories dropdown menu.', $cgi, 35, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now()),

                    ('Categories Dropdown: Main Only?', 'PL_CATEGORY_TREE_MAIN_CATS_ONLY_$profile', 'true', 'Should the categories dropdown menu contain <em>only</em> the main categories?  If set to <em>false</em>, then <b>all</b> categories are displayed.  <b>Note:</b> This setting is ignored if <em>Show Selections</em> is set to <em>false</em>', $cgi, 37, NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),', now()),

                    ('Main Categories: New Page', 'PL_MAINCATS_NEW_PAGE_$profile', 'false', 'If true, main categories on the printed price-list will start on a new page.', $cgi, 40, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now()),

                    ('Include attributes pricing?', 'PL_SHOW_ATTRIBUTES_$profile', 'false', 'Should any attribute-related pricing be listed for the products?', $cgi, 45, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now()),

                    ('No Wrap', 'PL_NOWRAP_$profile', 'false', 'To enable or disable wrapping on screen (nowrap is easier for debugging)', $cgi, 60, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now()),

                    ('Columns: Model', 'PL_SHOW_MODEL_$profile', 'true', 'Display each product\'s model number in a separate column?', $cgi, 100, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now()),

                    ('Columns: Manufacturer', 'PL_SHOW_MANUFACTURER_$profile', 'true', 'Display each product\'s manufacturer in a separate column?', $cgi, 105, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now()),

                    ('Columns: Weight', 'PL_SHOW_WEIGHT_$profile', 'false', 'Display each product\'s weight in a separate column?', $cgi, 110, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now()),

                    ('Columns: Stock-on-Hand', 'PL_SHOW_SOH_$profile', 'false', 'Display each product\'s stock-on-hand in a separate column?', $cgi, 115, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now()),

                    ('Columns: Notes (A)', 'PL_SHOW_NOTES_A_$profile', 'false', 'Display an empty column for each product, allowing the customer to make notes?', $cgi, 120, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now()),

                    ('Columns: Notes (B)', 'PL_SHOW_NOTES_B_$profile', 'false', 'Display another empty column for each product, allowing the customer to make notes?', $cgi, 125, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now()),

                    ('Columns:  Price', 'PL_SHOW_PRICE_$profile', 'true', 'Display each product\'s price, including or excluding tax based on your shop\'s tax-configuration settings)?', $cgi, 130, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now()),

                    ('Columns: Price (ex)', 'PL_SHOW_TAX_FREE_$profile', 'false', 'Display each product\'s tax-free price in a separate column?', $cgi, 135, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now()),

                    ('Show Specials Prices?', 'PL_SHOW_SPECIAL_PRICE_$profile', 'true', 'Display each product\'s &quot;special&quot; price?  If <em>true</em>, the script will execute 4 extra queries per product!', $cgi, 140, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now()),

                    ('Show Specials Expiry?', 'PL_SHOW_SPECIAL_DATE_$profile', 'false', 'Show special price expiry date?  This works <em>only</em> for specials (not for pricing by attributes and sales). Executes one extra query per special if enabled.', $cgi, 145, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now()),

                    ('Columns: Add-to-Cart', 'PL_SHOW_ADDTOCART_BUTTON_$profile', 'false', 'Display an add-to-cart button for each product? If the product has attributes, a &quot;More info&quot; link displays instead.', $cgi, 150, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now()),

                    ('Add-to-Cart Button Target', 'PL_ADDTOCART_TARGET_$profile', 'Cartpage', 'How to react to an Add-to-Cart button click: <em>Cartpage</em> sends all results to the same web page, <em>_self</em> sends result to the current page and <em>_blank</em> sends each result to a new page.', $cgi, 155, NULL, 'zen_cfg_select_option([\'Cartpage\', \'_self\', \'_blank\'],', now()),

                    ('Show Product Images?', 'PL_SHOW_IMAGE_$profile', 'false', 'Display each product\'s image?', $cgi, 160, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now()),

                    ('Image Height', 'PL_IMAGE_PRODUCT_HEIGHT_$profile', '80', 'If the product images are to be displayed, what is the height of each image?', $cgi, 165, NULL, NULL, now()),

                    ('Image Width', 'PL_IMAGE_PRODUCT_WIDTH_$profile', '100', 'If the product images are to be displayed, what is the width of each image?', $cgi, 170, NULL, NULL, now()),

                    ('Show Descriptions?', 'PL_SHOW_DESCRIPTION_$profile', 'false', 'Display each product\'s description?', $cgi, 175, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now()),

                    ('Truncate Descriptions?', 'PL_TRUNCATE_DESCRIPTION_$profile', '300', 'If <em>Show Descriptions?</em> is set to <b>true</b> and this field is a value other than 0 or blank, product descriptions will be truncated to this length &mdash; HTML will be stripped.', $cgi, 180, NULL, NULL, now()),

                    ('Show Inactive Products and Categories?', 'PL_SHOW_INACTIVE_$profile', 'false', 'Set this value to <em>true</em> to include disabled products and categories in the list.', $cgi, 200, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now()),

                    ('Sort By: Field', 'PL_SORT_PRODUCTS_BY_$profile', 'products_price', 'How products are sorted within a category', $cgi, 210, NULL, 'zen_cfg_select_option([\'products_name\', \'products_price\', \'products_model\'],', now()),

                    ('Sort By: Asc/Desc', 'PL_SORT_ASC_DESC_$profile', 'asc', 'Sort ascending or descending', $cgi, 215, NULL, 'zen_cfg_select_option([\'asc\', \'desc\'],', now()),

                    ('Enable Debug?', 'PL_DEBUG_$profile', 'false', 'If true debug info is shown', $cgi, 200, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now()),

                    ('Show Store Logo On-Screen?', 'PL_HEADER_LOGO_$profile', 'true', 'Display the store\'s logo at the top of the screen?', $cgi, 260, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now()),

                    ('Show Page Headers?', 'PL_SHOW_PRICELIST_PAGE_HEADERS_$profile', 'false', 'If true the page headers on each page are shown (screen and print).', $cgi, 270, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now()),

                    ('Show Page Footers?', 'PL_SHOW_PRICELIST_PAGE_FOOTERS_$profile', 'true', 'If true the page footers on each page are shown (screen and print).', $cgi, 280, NULL, 'zen_cfg_select_option([\'true\', \'false\'],', now())";
            $this->executeInstallerSql($sql);

            zen_deregister_admin_pages(["configPricelistProfile$profile"]);
            zen_register_admin_page(
                "configPricelistProfile$profile", "BOX_CONFIGURATION_PL_$profile", 'FILENAME_CONFIGURATION', "gID=$cgi", 'configuration', 'Y'
            );
        }
    }

    protected function executeUninstall()
    {
        zen_deregister_admin_pages(['configPrintablePricelist', 'configPricelistProfile1', 'configPricelistProfile2', 'configPricelistProfile3']);

        $sql = "DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key LIKE 'PL\_%'";
        $this->executeInstallerSql($sql);

        $deleteMap = "'Printable Price-list', 'Price-list Profile-1', 'Price-list Profile-2', 'Price-list Profile-3'";
        $sql = "DELETE FROM " . TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_title IN (" . $deleteMap . ")";
        $this->executeInstallerSql($sql);
    }

    protected function executeInstallerSelectSql(string $sql)
    {
        $this->dbConn->dieOnErrors = false;
        $result = $this->dbConn->Execute($sql);
        if ($this->dbConn->error_number !== 0) {
            $this->errorContainer->addError(0, $this->dbConn->error_text, true, PLUGIN_INSTALL_SQL_FAILURE);
            return false;
        }
        $this->dbConn->dieOnErrors = true;
        return $result;
    }

    protected function installConfigGroup(string $config_group_title, string $config_group_description): int
    {
        $sql =
            "INSERT IGNORE INTO " . TABLE_CONFIGURATION_GROUP . "
                (configuration_group_title, configuration_group_description, sort_order, visible)
             VALUES
                ('$config_group_title', '$config_group_description', 1, 1)";
        $this->executeInstallerSql($sql);
        $sql = "SELECT configuration_group_id FROM " . TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_title = '$config_group_title' LIMIT 1";
        $config_group = $this->executeInstallerSelectSql($sql);
        $cgi = $config_group->fields['configuration_group_id']; 
        $sql = "UPDATE " . TABLE_CONFIGURATION_GROUP . " SET sort_order = $cgi WHERE configuration_group_id = $cgi LIMIT 1";
        $this->executeInstallerSql($sql);
        return (int)$cgi;
    }
}
