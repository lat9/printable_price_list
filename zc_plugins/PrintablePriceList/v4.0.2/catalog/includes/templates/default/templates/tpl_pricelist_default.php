<?php
/**
 *
 * @copyright Copyright 2003-2007 Paul Mathot Haarlem, The Netherlands
 * @copyright parts Copyright 2003-2005 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version v1.5.8a (or newer)
 *
 * Last updated: v4.0.2
 */
?>
    <div class="noPrintPL">
        <div id="screenIntroPL">
<?php
if ($price_list->config['show_logo']) {
    echo '<a href="' . zen_href_link(FILENAME_DEFAULT) . '">' . zen_image($template->get_template_dir(HEADER_LOGO_IMAGE, DIR_WS_TEMPLATE, $current_page_base, 'images') . '/' . HEADER_LOGO_IMAGE, HEADER_ALT_TEXT) . '</a>';
}
?>
            <h3><?= sprintf(TEXT_PL_HEADER_TITLE, '<a href="' . zen_href_link(FILENAME_DEFAULT) . '">' . TITLE . '</a>') ?></h3>
            <p><?= sprintf(TEXT_PL_SCREEN_INTRO, $price_list->productCount) ?></p>
        </div>
<?php

if (PL_SHOW_PROFILES === 'true') {
    $profiles_list = $price_list->getProfiles();
    if ($profiles_list !== '') {
        echo '<div id="profilesListPL">' . $profiles_list . '</div>' . "\n";
    }
}

if ($price_list->config['show_boxes']) {
    $column_box_default = 'tpl_box_default.php';
?>
        <table id="boxesPL">
            <tr>
                <td>
<?php
    $box_id = 'languagesPL';
    require DIR_WS_MODULES . 'sideboxes/' . 'languages.php';
?>
                </td>
                <td>
<?php
    $box_id = 'currenciesPL';
    require DIR_WS_MODULES . 'sideboxes/' . 'currencies.php';
?>
                </td>
<?php
    if ($price_list->config['included_products'] === 'all') {
        $cat_tree = ($price_list->config['main_cats_only']) ? $price_list->getCategoryList(0, '', '', '', false, true) : $price_list->getCategoryList();
?>
                <td>
                    <div id="categoriesPLContent" class="sideBoxContent centeredContent">
                        <?= 
                            zen_draw_form('categories', zen_href_link(FILENAME_DEFAULT), 'get') . "\n" .
                                zen_draw_pull_down_menu('plCat', $cat_tree, $price_list->currentCategory, 'onchange="this.form.submit();"') .
                                zen_draw_hidden_field('main_page', FILENAME_PRICELIST) .
                                zen_draw_hidden_field('profile', $price_list->currentProfile) .
                            '</form>' ?>
                    </div>
                </td>
<?php
    }
?>
            </tr>
        </table>
<?php
}
?>
    </div>
<?php
if ($price_list->groupIsValid($price_list->currentProfile) === false) {
    // customer is not allowed to view price_list list
    echo PL_TEXT_GROUP_NOT_ALLOWED;
    if (zen_is_logged_in() && !zen_in_guest_checkout()) {
        echo '<a href="'. zen_href_link(FILENAME_LOGOFF, '', 'SSL') . '">' . HEADER_TITLE_LOGOFF . '</a>';  
    } elseif (STORE_STATUS === '0'){
        echo '&nbsp;(<a href="'. zen_href_link(FILENAME_LOGIN, '', 'SSL') . '">' . HEADER_TITLE_LOGIN . '</a>)';
    }
} else {
    if (count($price_list->rows) === 0) {
        echo '<h3 id="noMatchPL">' . TEXT_PL_NOTHING_FOUND . '</h3>';
    } else {
?>
    <table class="tablePL">
        <thead>
            <tr>
                <th colspan="<?= $price_list->headerColumns ?>">
<?php
        if ($price_list->config['show_headers']) {
?>
                    <div class="headPL">
                        <a href="<?= zen_href_link(FILENAME_DEFAULT) ?>"><?= zen_image($template->get_template_dir(HEADER_LOGO_IMAGE, DIR_WS_TEMPLATE, $current_page_base, 'images'). '/' . HEADER_LOGO_IMAGE, HEADER_ALT_TEXT) ?></a>
                        <h4 class="headerTitlePrintPL"><?= sprintf(TEXT_PL_HEADER_TITLE_PRINT , '<a href="' . zen_href_link(FILENAME_DEFAULT) . '">' . TITLE . '</a>') ?></h4>
                    </div>
<?php
        }

        global $zcDate;
?>
                    <div class="datePL"><?= $zcDate->output(DATE_FORMAT_LONG) ?></div>
                    <div id="print-me"><a href="javascript:window.print();"><?= PL_PRINT_ME ?></a></div>
                    <div class="clearBoth"></div>
                </th>
            </tr>

            <tr class="theadPL">
                <th class="prdPL"><?= TABLE_HEADING_PRODUCTS ?></th>
<?php
        if ($price_list->config['show_model']) {
?>
                <th class="modPL"><?= TABLE_HEADING_MODEL ?></th>
<?php
        }
        if ($price_list->config['show_manufacturer']) {
?>
                <th class="manPL"><?= TABLE_HEADING_MANUFACTURER ?></th>
<?php
        }
        if ($price_list->config['show_weight']) {
?>
                <th class="wgtPL"><?= TABLE_HEADING_WEIGHT . ' (' . TEXT_SHIPPING_WEIGHT . ')' ?></th>
<?php
        }
// stock by bmoroney
        if ($price_list->config['show_stock']) {
?>
                <th class="sohPL"><?= TABLE_HEADING_SOH ?></th>
<?php
        }
        if ($price_list->config['show_notes_a']) {
?>
                <th class="ntsPL"><div><?= TABLE_HEADING_NOTES_A ?></div></th>
<?php
        }
        if ($price_list->config['show_notes_b']) {
?>
                <th class="ntsPL"><?= TABLE_HEADING_NOTES_B ?></th>
<?php
        }
        $pl_currency_symbol = (PL_INCLUDE_CURRENCY_SYMBOL === 'false') ? '' : $price_list->currencySymbol;
        if ($price_list->config['show_price']) {
?>
                <th class="prcPL"><?= TABLE_HEADING_PRICE_INC . $pl_currency_symbol ?></th>
<?php
        }
        if ($price_list->config['show_taxfree']) {
?>
                <th class="prcPL"><?= TABLE_HEADING_PRICE_EX . $pl_currency_symbol ?></th>
<?php
        }
//Added by Vartan Kat on july 2007 for Add to cart button
        if ($price_list->config['show_cart_button']) {
?>
                <th><?= TABLE_HEADING_ADDTOCART ?></th>
<?php
        }
//End of Added by Vartan Kat on july 2007 for Add to cart button
?>
            </tr>
        </thead>

        <tbody>
<?php
        $found_main_cat = false;
        foreach ($price_list->rows as $current_row) {
            if (($current_row['is_product'] ?? false) === false) {
                if ($current_row['product_count'] !== 0 && isset($current_row['level'])) {
?>
            <tr class="scPL-<?= $current_row['level'] . (($price_list->config['maincats_new_page'] && $current_row['level'] === 1 && $found_main_cat) ? ' new-page' : '') ?>">
                <th colspan="<?= $price_list->headerColumns ?>">
                    <?= $current_row['categories_name'] ?>
                </th>
            </tr>
<?php
                }
                if (($current_row['level'] ?? 0) === 1) {
                    $found_main_cat = true;
                }
            } else {
                $products_id = $current_row['products_id'];
                $products_name = zen_output_string_protected($current_row['products_name']);

                // -----
                // If the price-list is to display products' pricing (either inc or ex), get the product's 'base' price
                // for the display.  That'll include any attribute-based pricing, too.
                //
                if ($price_list->config['show_price'] || $price_list->config['show_taxfree']) {
                    $products_base_price = zen_get_products_base_price($products_id);
                    $products_price_inc = $price_list->displayPrice($products_base_price, zen_get_tax_rate($current_row['products_tax_class_id']));
                    $products_price_ex = $price_list->displayPrice($products_base_price);
                }

                $special_price_ex = ($price_list->config['show_special_price']) ? zen_get_products_special_price($products_id, true) : '';
                if (!empty($special_price_ex)) {
                    $special_price_inc = $price_list->displayPrice($special_price_ex, zen_get_tax_rate($current_row['products_tax_class_id']));
                    $special_price_ex = $price_list->displayPrice($special_price_ex);
                    $special_date = ($price_list->config['show_special_date']) ? $price_list->getProductsSpecialDate($products_id) : '';
                }

                if (($price_list->config['show_inactive'] && $current_row['products_status'] === '0') || $current_row['categories_status'] === '0') {
?>
            <tr class="inactivePL">
                <td class="prdPL">
<?php
                    if ($price_list->config['show_image']){
                        echo zen_image(DIR_WS_IMAGES . $current_row['products_image'], $products_name, $price_list->config['image_width'], $price_list->config['image_height'], 'class="imgPL"');
                    }
                    echo $products_name;
?>
                </td>
<?php
                } else {
                    $products_info_page = zen_get_info_page($products_id);
?>
            <tr>
                <td class="prdPL">
<?php
                    if ($price_list->config['show_image']){
                        echo zen_image(DIR_WS_IMAGES . $current_row['products_image'], $products_name, $price_list->config['image_width'], $price_list->config['image_height'], 'class="imgPL"');
                    }
?>
                    <a href="<?= zen_href_link($products_info_page, 'products_id=' . $products_id) ?>" target="_blank"><?= $products_name ?></a>
<?php
                    // -----
                    // If the current product has attributes, build up a table (one option/row) that lists the available
                    // option-values and their associated pricing.
                    //
                    if (!empty($current_row['attributes'])) {
?>
                    <div class="pl-attr">
                        <table class="pl-attr-table">
<?php
                        $is_priced_by_attributes = $current_row['products_priced_by_attribute'];
                        foreach ($current_row['attributes'] as $option_id => $option_values) {
?>
                            <tr>
                                <td><?= zen_output_string_protected($option_values['name']) ?></td>
                                <td>
<?php
                            $separator = '';
                            foreach ($option_values['values'] as $next_value) {
                                // -----
                                // Special 'name' handling for TEXT and FILE attributes ...
                                //
                                $price_suffix = '';
                                if ($option_values['option_type'] === '1') {
                                    $option_value_name = TEXT_OPTION_IS_TEXT;
                                    if (!empty($next_value['price_per_word'])) {
                                        $option_value_name .= TEXT_OPTION_IS_PER_WORD;
                                        $next_value['price_prefix'] = '';
                                        $next_value['price'] = $next_value['price_per_word'];
                                        if ($next_value['free_words'] !== '0') {
                                            $price_suffix = sprintf(TEXT_OPTION_FREE_WORDS, $next_value['free_words']);
                                        }
                                    } elseif (!empty($next_value['price_per_letter'])) {
                                       $option_value_name .= TEXT_OPTION_IS_PER_LETTER;
                                        $next_value['price_prefix'] = '';
                                        $next_value['price'] = $next_value['price_per_letter'];
                                        if ($next_value['free_letters'] !== '0') {
                                            $price_suffix = sprintf(TEXT_OPTION_FREE_LETTERS, $next_value['free_letters']);
                                        }
                                    }
                                } elseif ($option_values['option_type'] === '4') {
                                    $option_value_name = TEXT_OPTION_IS_FILE;
                                } else {
                                    $option_value_name = zen_output_string_protected($next_value['name']);
                                }

                                // -----
                                // No pricing for read-only attributes.
                                //
                                $option_value_price = ': ' . TEXT_INCL;
                                if ($option_values['option_type'] === '5') {
                                    $option_value_price = '';
                                } elseif ($next_value['price'] != 0) {
                                    $option_value_price = ': ' . $next_value['price_prefix'] . $price_list->displayPrice($next_value['price'], zen_get_tax_rate($current_row['products_tax_class_id']));
                                }
                                echo $separator . $option_value_name . $option_value_price . $price_suffix;
                                $separator = ', ';
                            }
?>
                                </td>
                            </tr>
<?php
                        }
?>
                        </table>
                    </div>
<?php
                    }
?>
                </td>
<?php
                }

                if ($price_list->config['show_model']) {
?>
                <td class="modPL"><?= $current_row['products_model'] ?></td>
<?php
                }
                if ($price_list->config['show_manufacturer']) {
?>
                <td class="manPL"><?= $price_list->manufacturersNames[(int)$current_row['manufacturers_id']] ?? '' ?></td>
<?php
                }
                if ($price_list->config['show_weight']) {
?>
                <td class="wgtPL"><?= $current_row['products_weight'] ?? '' ?></td>
<?php
                }

                // stock by bmoroney
                if ($price_list->config['show_stock']) {
?>
                    <td class="sohPL"><?= ($current_row['products_quantity'] > 0) ? $current_row['products_quantity'] : 0 ?></td>
<?php
                }

                if ($price_list->config['show_notes_a']) {
?>
                    <td class="ntsaPL">&nbsp;</td>
<?php
                }

                if ($price_list->config['show_notes_b']) {
?>
                    <td class="ntsbPL">&nbsp;</td>
<?php
                }

                $price_class = ($special_price_ex > 0) ? 'prcPL notSplPL' : 'prcPL';
                if ($price_list->config['show_price']) {
?>        
                    <td class="<?= $price_class ?>"><?= $products_price_inc ?></td>
<?php
                }

                if ($price_list->config['show_taxfree']) {
?>
                    <td class="<?= $price_class ?>"><?= $products_price_ex ?></td>
<?php
                }

                //Added by Vartan Kat on july 2007 for Add to cart button
                if ($price_list->config['show_cart_button']) {
                    if (zen_has_product_attributes ($products_id) ) {
?>
                    <td>
                        <a href="<?= zen_href_link($products_info_page, 'products_id=' . $products_id) ?>" target="<?= $price_list->config['add_cart_target'] ?>">
                            <?= MORE_INFO_TEXT ?>
                        </a>
                    </td>
<?php
                    } else {
?>
                    <td>
<?php
                        echo
                            zen_draw_form('cart_quantity', zen_href_link($products_info_page, zen_get_all_get_params(['action']) . 'action=add_product'), 'post', 'enctype="multipart/form-data" target="' . $price_list->config['add_cart_target'] . '" class="AddButtonBox"') . PHP_EOL .
                                PRODUCTS_ORDER_QTY_TEXT .
                                '<input type="text" name="cart_quantity" value="' . (zen_get_buy_now_qty($products_id)) . '" maxlength="6" size="4"><br>' .
                                zen_get_products_quantity_min_units_display($products_id) .
                                '<br>' .
                                zen_draw_hidden_field('products_id', $products_id) .
                                zen_image_submit(BUTTON_IMAGE_IN_CART, BUTTON_IN_CART_ALT) .
                            '</form>';
?>
                    </td>
<?php
                    }
                }
                //End of Added by Vartan Kat on july 2007 for Add to cart button
?>
            </tr>
<?php
                if ($special_price_ex > 0) {
                    $colspan = $price_list->headerColumns;
                    if ($price_list->config['show_price']) {
                        $colspan--;
                    } 
                    if ($price_list->config['show_taxfree']) {
                        $colspan--;
                    }
?>
            <tr>
                <td class="splDatePL" colspan="<?= $colspan ?>"><?= (!empty($special_date)) ? (TEXT_PL_AVAIL_TILL . $special_date) : TEXT_PL_SPECIAL ?></td>
<?php
                    if ($price_list->config['show_price']) {
                        echo '<td class="splPL">' . $special_price_inc . '</td>' . "\n";
                    }
                    if ($price_list->config['show_taxfree']) {
                        echo '<td class="splPL">' . $special_price_ex . '</td>' . "\n";
                    }
?>
            </tr>
<?php
                }

                if ($price_list->config['show_description']) {
?>
            <tr>
                <td class="imgDescrPL" colspan="<?= $price_list->headerColumns ?>">
<?php
                    if ($price_list->config['truncate_desc'] > 0 && strlen($current_row['products_description']) >= $price_list->config['truncate_desc']) {
                        echo zen_clean_html($current_row['products_description']) . '<a href="' . zen_href_link($products_info_page, 'products_id=' . $products_id) . '">' . MORE_INFO_TEXT . '</a>';
                    } else {
                        echo $current_row['products_description'];
                    }
?>
                </td>
            </tr>
<?php     
                }
            }
        }
?>
<!-- EOF price-list main -->
        </tbody>
<?php
        if ($price_list->config['show_footers']) {
?>
        <tfoot>
            <tr>
                <td colspan="<?= $price_list->headerColumns ?>">
                    <div class="footPL">
                        <?= STORE_NAME_ADDRESS_PL ?>&nbsp;&nbsp;
                        <a href="<?= zen_href_link(FILENAME_DEFAULT) ?>">
                            <?= TITLE ?>
                        </a>
                    </div>
                </td>
            </tr>
        </tfoot>
<?php
        }
?>
    </table>
<?php
    }
}

if ($price_list->config['debug']) {
?>
    <div class="noPrintPL">
        <p>
<?php
    echo 'memory_get_usage:' . memory_get_usage();
    if (function_exists ('memory_get_peak_usage')) {
        echo ',&nbsp;memory_get_peak_usage: ' . memory_get_peak_usage();
    }
    echo ',&nbsp;queries: ' . $db->queryCount();
    echo ',&nbsp;query time: ' . $db->queryTime();
?>
        </p>
    </div>
<?php
}
