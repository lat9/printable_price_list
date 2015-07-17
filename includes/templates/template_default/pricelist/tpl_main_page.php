<?php
/** 
 *
 * @copyright Copyright 2003-2007 Paul Mathot Haarlem, The Netherlands
 * @copyright parts Copyright 2003-2005 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version v1.5.0 (or newer) 
 */
?>
<body id="pricelist">
<!-- bof noPrintPL -->
  <div class="noPrintPL">
<!-- Current profile: <?php echo $price_list->config['profile_name'] . ' (' . $price_list->current_profile . ')'; ?> -->
<?php
if ($messageStack->size('header') > 0) {
  echo $messageStack->output('header');
  
}
?>
<!-- bof screenIntroPL -->
    <div id="screenIntroPL">
<?php
if ($price_list->config['show_logo']) {
  echo '<a href="' . zen_href_link (FILENAME_DEFAULT) . '">' . zen_image($template->get_template_dir (HEADER_LOGO_IMAGE, DIR_WS_TEMPLATE, $current_page_base,'images'). '/' . HEADER_LOGO_IMAGE, HEADER_ALT_TEXT) . '</a>';
  
}
?>
      <h3><?php echo sprintf (TEXT_PL_HEADER_TITLE, '<a href="' . zen_href_link (FILENAME_DEFAULT) . '">' . TITLE . '</a>'); ?></h3>
      <p><?php echo sprintf (TEXT_PL_SCREEN_INTRO, $price_list->product_count); ?></p>
    </div>
<!-- eof screenIntroPL -->
<?php

if (PL_SHOW_PROFILES == 'true') {
  $profiles_list = $price_list->get_profiles ();
  if ($profiles_list != '') {
    echo '<div id="profilesListPL">' . "\n" . $profiles_list . '</div>' . "\n";
    
  }
} 
if ($price_list->config['show_boxes']) {
  $column_box_default = 'tpl_box_default.php';
?>
    <table id="boxesPL">
      <tr>
        <td><?php $box_id = 'languagesPL'; require(DIR_WS_MODULES . 'sideboxes/' . 'languages.php') ?></td>
        <td><?php $box_id = 'currenciesPL'; require(DIR_WS_MODULES . 'sideboxes/' . 'currencies.php') ?></td>  
        <td>
<?php
  $content  = '<div id="categoriesPLContent" class="sideBoxContent centeredContent">';
  $content .= zen_draw_form ('categories', zen_href_link (FILENAME_DEFAULT), 'get') . "\n";
  $cat_tree = ($price_list->config['main_cats_only']) ? $price_list->get_category_list (0, '', '', '', false, true) : $price_list->get_category_list ();
  $content .= zen_draw_pull_down_menu ('plCat', $cat_tree, $price_list->current_category, 'onchange="this.form.submit();"') . "\n";
  $content .= zen_draw_hidden_field ('main_page', FILENAME_PRICELIST);
  $content .= zen_draw_hidden_field ('profile', $price_list->current_profile);
  $content .= '</form>';
  $content .= '</div>';
  echo $content;
?>
        </td>   
      </tr>
    </table>
<?php
} 
?>          
  </div>
<!-- eof noPrintPL -->

<!-- bof echo Price List -->
<?php
if (!$price_list->group_is_valid ($price_list->current_profile)) {
  // customer is not allowed to view price_list list
  echo PL_TEXT_GROUP_NOT_ALLOWED;
  if ($_SESSION['customer_id']) {
    echo '<a href="'. zen_href_link (FILENAME_LOGOFF, '', 'SSL') . '">' . HEADER_TITLE_LOGOFF . '</a>';  
    
  } else {
    if (STORE_STATUS == '0'){
      echo '&nbsp;(<a href="'. zen_href_link(FILENAME_LOGIN, '', 'SSL') . '">' . HEADER_TITLE_LOGIN . '</a>)';
      
    }
  }
} else {
  if (count ($price_list->rows) == 0) {
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
            <a href="<?php echo zen_href_link (FILENAME_DEFAULT); ?>"><?php echo zen_image ($template->get_template_dir (HEADER_LOGO_IMAGE, DIR_WS_TEMPLATE, $current_page_base, 'images'). '/' . HEADER_LOGO_IMAGE, HEADER_ALT_TEXT); ?></a>
            <h4 class="headerTitlePrintPL"><?php echo sprintf (TEXT_PL_HEADER_TITLE_PRINT , '<a href="' . zen_href_link (FILENAME_DEFAULT) . '">' . TITLE . '</a>'); ?></h4>
          </div>
<?php
    }
?>
          <div class="datePL"><?php echo strftime (DATE_FORMAT_LONG); ?></div>
          <div id="print-me"><a href="javascript:window.print();"><?php echo PL_PRINT_ME; ?></a></div>
          <div class="clearBoth"></div>
        </td>
      </tr>
      
<!-- BOF price-list column header -->
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
    $pl_currency_symbol = (defined ('PL_INCLUDE_CURRENCY_SYMBOL') && PL_INCLUDE_CURRENCY_SYMBOL == 'false') ? '' : $price_list->currency_symbol;
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
<!-- EOF price-list header -->
    </thead>
<?php
    if ($price_list->config['show_footers']) {
?>
    <tfoot>
      <tr>
        <td colspan="<?php echo $price_list->header_columns; ?>"><div class="footPL"><?php echo STORE_NAME_ADDRESS_PL; ?>&nbsp;&nbsp;<a href="<?php echo zen_href_link (FILENAME_DEFAULT); ?>"><?php echo TITLE; ?></a></div></td>
      </tr>
    </tfoot>
<?php
    }
?>
    <tbody>
<!-- BOF price-list main -->
<?php
    $found_main_cat = false;
    foreach ($price_list->rows as $current_row) {
      if (!$current_row['is_product']) {
?>
      <tr class="scPL-<?php echo $current_row['level'] . (($price_list->config['maincats_new_page'] && $current_row['level'] == 1 && $found_main_cat) ? ' new-page' : ''); ?>"><th colspan="<?php echo $price_list->header_columns; ?>"><?php echo $current_row['categories_name']; ?></th></tr>
<?php
        if ($current_row['level'] == 1) {
          $found_main_cat = true;
          
        }
      } else {
        $products_id = $current_row['products_id'];
        $products_price_inc = $price_list->display_price ($current_row['products_price'], zen_get_tax_rate ($current_row['products_tax_class_id']));
        $products_price_ex = $price_list->display_price ($current_row['products_price']);
        // $specials_price_only=false multiplies the number of queries per product!!
        //function zen_get_products_special_price($product_id, $specials_price_only=false)
        $special_price_ex = ($price_list->config['show_special_price']) ? zen_get_products_special_price ($products_id, true) : '';
        if (zen_not_null($special_price_ex)) {
          $special_price_inc = $price_list->display_price($special_price_ex, zen_get_tax_rate ($current_row['products_tax_class_id']));
          $special_price_ex = $price_list->display_price ($special_price_ex);
          $special_date = ($price_list->config['show_special_date']) ? $price_list->get_products_special_date ($products_id) : '';

        }
        
        if (($price_list->config['show_inactive'] && $current_row['products_status'] == 0) || $current_row['categories_status'] == 0) {
?>
      <tr class="inactivePL">
        <td class="prdPL"><div><?php echo $current_row['products_name']; ?></div></td>
<?php          
        } else {
?>
      <tr>
        <td class="prdPL"><div><a href="<?php echo zen_href_link (zen_get_info_page ($products_id), 'products_id=' . $products_id); ?>" target="_blank"><?php echo $current_row['products_name']; ?></a></div></td>
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
        <td class="sohPL"><div><?php echo $current_row['products_quantity']; ?></div></td>
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
            echo '<td><a href="' . zen_href_link (zen_get_info_page($products_id), 'products_id=' . $products_id) . '" target="' . $price_list->config['add_cart_target'] . '" >' . MORE_INFO_TEXT . '</a></td>';
            
          } else {
            // show the quantity box
            echo '<td>' . zen_draw_form ('cart_quantity', zen_href_link (zen_get_info_page($products_id), zen_get_all_get_params (array('action')) . 'action=add_product'), 'post', 'enctype="multipart/form-data" target="' . $price_list->config['add_cart_target'] . '" class="AddButtonBox"') . "\n" . PRODUCTS_ORDER_QTY_TEXT . '<input type="text" name="cart_quantity" value="' . (zen_get_buy_now_qty($products_id)) . '" maxlength="6" size="4" /><br />' . zen_get_products_quantity_min_units_display ((int)$products_id) . '<br />' . zen_draw_hidden_field ('products_id', (int)$products_id) . zen_image_submit(BUTTON_IMAGE_IN_CART, BUTTON_IN_CART_ALT). '</form></td>';
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
        <td class="splDatePL" colspan="<?php echo $colspan; ?>"><?php echo (zen_not_null ($special_date)) ? (TEXT_PL_AVAIL_TILL . $special_date) : TEXT_PL_SPECIAL; ?></td>
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
        
        if ($price_list->config['show_image'] || $price_list->config['show_description']) {
?>
      <tr>
        <td class="imgDescrPL" colspan = "<?php echo $price_list->header_columns; ?>">
<?php
          // add random class for nicer catalog images display
          $pl_random = rand(1, 4);
          // adding div wrapper for easier overflow etc
?>
          <div class="imgDescrRndPL_<?php echo $pl_random; ?>">
<?php
          if ($price_list->config['show_image']){
            echo zen_image (DIR_WS_IMAGES . $current_row['products_image'], '', $price_list->config['image_width'], $price_list->config['image_height'], 'class="imgPL"');
            
          }
          if ($price_list->config['show_description']) {        
            if ($price_list->config['truncate_desc'] > 0 && strlen ($current_row['products_description']) == $price_list->config['truncate_desc']) {
              echo zen_clean_html ($current_row['products_description']) . '<a href="' . zen_href_link (zen_get_info_page ($current_row['products_id']), 'products_id=' . $current_row['products_id']) . '"> ' . MORE_INFO_TEXT . '</a>';
              
            } else {
              echo $current_row['products_description'];
              
            }
          }
?>
          </div>
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
 ?>
<!-- eof echo Price List  -->
<?php
if ($price_list->config['debug']) {
?>
<!-- bof noPrintPL -->
  <div class="noPrintPL">
<!--bof- superglobals display -->
<?php
// BEGIN Superglobals
  if (defined ('SHOW_SUPERGLOBALS') && SHOW_SUPERGLOBALS == 'true') echo superglobals_echo();
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
<!-- eof noPrintPL -->
<?php
}
?>
</body>