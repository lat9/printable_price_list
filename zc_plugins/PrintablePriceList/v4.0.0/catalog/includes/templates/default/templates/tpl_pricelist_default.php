<?php
/** 
 *
 * @copyright Copyright 2003-2007 Paul Mathot Haarlem, The Netherlands
 * @copyright parts Copyright 2003-2005 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version v1.5.7 (or newer) 
 */
?>
    <div class="noPrintPL">
<?php
if ($messageStack->size('header') > 0) {
    echo $messageStack->output('header');
}
?>
        <div id="screenIntroPL">
<?php
if ($price_list->config['show_logo']) {
    echo '<a href="' . zen_href_link(FILENAME_DEFAULT) . '">' . zen_image($template->get_template_dir(HEADER_LOGO_IMAGE, DIR_WS_TEMPLATE, $current_page_base, 'images') . '/' . HEADER_LOGO_IMAGE, HEADER_ALT_TEXT) . '</a>';
}
?>
            <h3><?php echo sprintf(TEXT_PL_HEADER_TITLE, '<a href="' . zen_href_link(FILENAME_DEFAULT) . '">' . TITLE . '</a>'); ?></h3>
            <p><?php echo sprintf(TEXT_PL_SCREEN_INTRO, $price_list->product_count); ?></p>
        </div>
<?php

if (PL_SHOW_PROFILES === 'true') {
    $profiles_list = $price_list->get_profiles();
    if ($profiles_list != '') {
        echo '<div id="profilesListPL">' . $profiles_list . '</div>' . "\n";
    }
} 
if ($price_list->config['show_boxes']) {
    $column_box_default = 'tpl_box_default.php';
?>
        <table id="boxesPL">
            <tr>
                <td><?php $box_id = 'languagesPL'; require DIR_WS_MODULES . 'sideboxes/' . 'languages.php'; ?></td>
                <td><?php $box_id = 'currenciesPL'; require DIR_WS_MODULES . 'sideboxes/' . 'currencies.php'; ?></td>
<?php
    if ($price_list->config['included_products'] === 'all') {
        $cat_tree = ($price_list->config['main_cats_only']) ? $price_list->get_category_list(0, '', '', '', false, true) : $price_list->get_category_list();
?>
                <td>
                    <div id="categoriesPLContent" class="sideBoxContent centeredContent">
                        <?php echo 
                            zen_draw_form('categories', zen_href_link(FILENAME_DEFAULT), 'get') . "\n" .
                            zen_draw_pull_down_menu('plCat', $cat_tree, $price_list->current_category, 'onchange="this.form.submit();"') .
                            zen_draw_hidden_field('main_page', FILENAME_PRICELIST) .
                            zen_draw_hidden_field('profile', $price_list->current_profile) .
                            '</form>'; ?>
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
if (!$price_list->group_is_valid($price_list->current_profile)) {
    // customer is not allowed to view price_list list
    echo PL_TEXT_GROUP_NOT_ALLOWED;
    if (zen_is_logged_in() && !zen_in_guest_checkout()) {
        echo '<a href="'. zen_href_link(FILENAME_LOGOFF, '', 'SSL') . '">' . HEADER_TITLE_LOGOFF . '</a>';  
    } elseif (STORE_STATUS == '0'){
        echo '&nbsp;(<a href="'. zen_href_link(FILENAME_LOGIN, '', 'SSL') . '">' . HEADER_TITLE_LOGIN . '</a>)';
    }
} else {
    if (count($price_list->rows) === 0) {
        echo '<h3 id="noMatchPL">' . TEXT_PL_NOTHING_FOUND . '</h3>';
    } else {
?>
    <table class="colPL">
        <thead>
            <tr>
                <td colspan="<?php echo $price_list->header_columns; ?>">
<?php
        if ($price_list->config['show_headers']) {
?>
                    <div class="headPL">
                        <a href="<?php echo zen_href_link(FILENAME_DEFAULT); ?>"><?php echo zen_image($template->get_template_dir(HEADER_LOGO_IMAGE, DIR_WS_TEMPLATE, $current_page_base, 'images'). '/' . HEADER_LOGO_IMAGE, HEADER_ALT_TEXT); ?></a>
                        <h4 class="headerTitlePrintPL"><?php echo sprintf(TEXT_PL_HEADER_TITLE_PRINT , '<a href="' . zen_href_link(FILENAME_DEFAULT) . '">' . TITLE . '</a>'); ?></h4>
                    </div>
<?php
        }
?>
                    <div class="datePL"><?php echo strftime(DATE_FORMAT_LONG); ?></div>
                    <div id="print-me"><a href="javascript:window.print();"><?php echo PL_PRINT_ME; ?></a></div>
                    <div class="clearBoth"></div>
                </td>
            </tr>

            <tr class="colhPL">
                <td class="prdPL"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
<?php
        if ($price_list->config['show_model']) {
?>
                <td class="modPL"><div><?php echo TABLE_HEADING_MODEL; ?></div></td>
<?php
        }
        if ($price_list->config['show_manufacturer']) {
?>
                <td class="manPL"><div><?php echo TABLE_HEADING_MANUFACTURER; ?></div></td>
<?php
        }
        if ($price_list->config['show_weight']) {
?>
                <td class="wgtPL"><div><?php echo TABLE_HEADING_WEIGHT . ' (' . TEXT_SHIPPING_WEIGHT . ')'; ?></div></td>
<?php
        }
// stock by bmoroney
        if ($price_list->config['show_stock']) {
?>
                <td class="sohPL"><div><?php echo TABLE_HEADING_SOH; ?></div></td>
<?php
        }
        if ($price_list->config['show_notes_a']) {
?>
                <td class="ntsPL"><div><?php echo TABLE_HEADING_NOTES_A; ?></div></td>
<?php
        }
        if ($price_list->config['show_notes_b']) {
?>
                <td class="ntsPL"><div><?php echo TABLE_HEADING_NOTES_B; ?></div></td>
<?php
        }
        $pl_currency_symbol = (defined('PL_INCLUDE_CURRENCY_SYMBOL') && PL_INCLUDE_CURRENCY_SYMBOL === 'false') ? '' : $price_list->currency_symbol;
        if ($price_list->config['show_price']) {
?>
                <td class="prcPL"><?php echo TABLE_HEADING_PRICE_INC . $pl_currency_symbol; ?></td>
<?php
        }
        if ($price_list->config['show_taxfree']) {
?>
                <td class="prcPL"><?php echo TABLE_HEADING_PRICE_EX . $pl_currency_symbol; ?></td>
<?php
        }
//Added by Vartan Kat on july 2007 for Add to cart button
        if ($price_list->config['show_cart_button']) {
?>
                <td><?php echo TABLE_HEADING_ADDTOCART; ?></td>
<?php
        }
//End of Added by Vartan Kat on july 2007 for Add to cart button
?>
            </tr>
        </thead>
<?php
        if ($price_list->config['show_footers']) {
?>
        <tfoot>
            <tr>
                <td colspan="<?php echo $price_list->header_columns; ?>">
                    <div class="footPL"><?php echo STORE_NAME_ADDRESS_PL; ?>&nbsp;&nbsp;<a href="<?php echo zen_href_link (FILENAME_DEFAULT); ?>"><?php echo TITLE; ?></a></div>
                </td>
            </tr>
        </tfoot>
<?php
        }
?>
        <tbody>
<?php
        $found_main_cat = false;
        foreach ($price_list->rows as $current_row) {
            if (!$current_row['is_product']) {
                if ($current_row['product_count'] !== 0) {
?>
            <tr class="scPL-<?php echo $current_row['level'] . (($price_list->config['maincats_new_page'] && $current_row['level'] == 1 && $found_main_cat) ? ' new-page' : ''); ?>">
                <th colspan="<?php echo $price_list->header_columns; ?>"><?php echo $current_row['categories_name']; ?></th>
            </tr>
<?php
                }
                if ($current_row['level'] == 1) {
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
                    $products_price_inc = $price_list->display_price($products_base_price, zen_get_tax_rate($current_row['products_tax_class_id']));
                    $products_price_ex = $price_list->display_price($products_base_price);
                }

                $special_price_ex = ($price_list->config['show_special_price']) ? zen_get_products_special_price($products_id, true) : '';
                if (!empty($special_price_ex)) {
                    $special_price_inc = $price_list->display_price($special_price_ex, zen_get_tax_rate($current_row['products_tax_class_id']));
                    $special_price_ex = $price_list->display_price($special_price_ex);
                    $special_date = ($price_list->config['show_special_date']) ? $price_list->get_products_special_date($products_id) : '';
                }

                if (($price_list->config['show_inactive'] && $current_row['products_status'] === '0') || $current_row['categories_status'] === '0') {
?>
            <tr class="inactivePL">
                <td class="prdPL">
                    <div>
<?php
                    if ($price_list->config['show_image']){
                        echo zen_image(DIR_WS_IMAGES . $current_row['products_image'], $products_name, $price_list->config['image_width'], $price_list->config['image_height'], 'class="imgPL"');
                    }
                    echo $products_name;
?>
                    </div>
                </td>
<?php
                } else {
                    $products_info_page = zen_get_info_page($products_id);
?>
            <tr>
                <td class="prdPL">
                    <div>
<?php
                    if ($price_list->config['show_image']){
                        echo zen_image(DIR_WS_IMAGES . $current_row['products_image'], $products_name, $price_list->config['image_width'], $price_list->config['image_height'], 'class="imgPL"');
                    }
?>
                        <a href="<?php echo zen_href_link($products_info_page, 'products_id=' . $products_id); ?>" target="_blank"><?php echo $products_name; ?></a>
                    </div>
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
                                <td><?php echo zen_output_string_protected($option_values['name']); ?></td>
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
                                    $option_value_price = ': ' . $next_value['price_prefix'] . $price_list->display_price($next_value['price'], zen_get_tax_rate($current_row['products_tax_class_id']));
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
                <td class="modPL"><div><?php echo $current_row['products_model']; ?></div></td>
<?php
                }
                if ($price_list->config['show_manufacturer']) {
?>
                <td class="manPL"><div><?php echo $price_list->manufacturers_names[(int)$current_row['manufacturers_id']]; ?></div></td>
<?php
                }
                if ($price_list->config['show_weight']) {
?>
                <td class="wgtPL"><div><?php echo $current_row['products_weight']; ?></div></td>
<?php
                }
                // stock by bmoroney
                if ($price_list->config['show_stock']) {
?>
                    <td class="sohPL"><div><?php echo ($current_row['products_quantity'] > 0) ? $current_row['products_quantity'] : 0; ?></div></td>
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
                    <td class="<?php echo $price_class; ?>"><?php echo $products_price_inc; ?></td>
<?php
                }
                if ($price_list->config['show_taxfree']) {
?>
                    <td class="<?php echo $price_class; ?>"><?php echo $products_price_ex; ?></td>
<?php
                }

                //Added by Vartan Kat on july 2007 for Add to cart button
                if ($price_list->config['show_cart_button']) {
                    if (zen_has_product_attributes ($products_id) ) {
?>
                    <td>
                        <a href="<?php echo zen_href_link($products_info_page, 'products_id=' . $products_id); ?>" target="<?php echo $price_list->config['add_cart_target']; ?>">
                            <?php echo MORE_INFO_TEXT; ?>
                        </a>
                    </td>
<?php
                    } else {
?>
                    <td>
<?php
                        echo
                            zen_draw_form('cart_quantity', zen_href_link($products_info_page, zen_get_all_get_params(['action']) . 'action=add_product'), 'post', 'enctype="multipart/form-data" target="' . $price_list->config['add_cart_target'] . '" class="AddButtonBox"') . PHP_EOL .
                            PRODUCTS_ORDER_QTY_TEXT . '<input type="text" name="cart_quantity" value="' . (zen_get_buy_now_qty($products_id)) . '" maxlength="6" size="4" /><br>' .
                            zen_get_products_quantity_min_units_display($products_id) . '<br>' .
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
                    $colspan = $price_list->header_columns;
                    if ($price_list->config['show_price']) {
                        $colspan--;
                    } 
                    if ($price_list->config['show_taxfree']) {
                        $colspan--;
                    }
?>
            <tr>
                <td class="splDatePL" colspan="<?php echo $colspan; ?>"><?php echo (!empty($special_date)) ? (TEXT_PL_AVAIL_TILL . $special_date) : TEXT_PL_SPECIAL; ?></td>
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
                <td class="imgDescrPL" colspan="<?php echo $price_list->header_columns; ?>">
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
    </table>
<?php
    }
}

if ($price_list->config['debug']) {
?>
    <div class="noPrintPL">
<?php
    // BEGIN Superglobals
    if (defined ('SHOW_SUPERGLOBALS') && SHOW_SUPERGLOBALS == 'true') {
        echo superglobals_echo();
    }
    // END Superglobals
?>
<!--eof- superglobals display -->
        <p>
<?php
    echo 'memory_get_usage:' . memory_get_usage();
    if (function_exists ('memory_get_peak_usage')) {
        echo ',&nbsp;memory_get_peak_usage: ' . memory_get_peak_usage();
    }
    echo ',&nbsp;queries: ' . $db->count_queries;
    echo ',&nbsp;query time: ' . $db->total_query_time;
?>
        </p>
    </div>
<?php
}
