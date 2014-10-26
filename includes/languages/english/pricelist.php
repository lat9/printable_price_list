<?php
//
// +----------------------------------------------------------------------+
// |zen-cart Open Source E-commerce                                       |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003 The zen-cart developers                           |
// |                                                                      |
// | http://www.zen-cart.com/index.php                                    |
// |                                                                      |
// | Portions Copyright (c) 2003 osCommerce & Others                      |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the GPL license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.zen-cart.com/license/2_0.txt.                             |
// | If you did not receive a copy of the zen-cart license and are unable |
// | to obtain it through the world-wide-web, please send a note to       |
// | license@zen-cart.com so we can mail you a copy immediately.          |
// +----------------------------------------------------------------------+
// $Id: pricelist.php, 2006 paulm
//
define('TABLE_HEADING_PRODUCTS', 'Product');
define('TABLE_HEADING_MODEL', 'Model');
define('TABLE_HEADING_MANUFACTURER', 'Manufacturer');
define('TABLE_HEADING_WEIGHT', 'Weight');
define('TABLE_HEADING_PRICE_INC', 'inc. ');
define('TABLE_HEADING_PRICE_EX', 'ex. ');
define('TABLE_HEADING_NOTES_A', 'Notes (A)');
define('TABLE_HEADING_NOTES_B', 'Notes (B)');

define('TEXT_PL_PAGE', 'Page: ');
define('TEXT_PL_HEADER_TITLE',  '%s Printable Price List');
define('TEXT_PL_HEADER_TITLE_PRINT', 'Printable Price List: %s');
define('TEXT_PL_SCREEN_INTRO','Displaying %s products, click on the links for detailed product information.');
define('TEXT_PL_NOTHING_FOUND', 'No products or categories match your query, please make another selection.');

define('STORE_NAME_ADDRESS_PL', str_replace("\n", " - ", STORE_NAME_ADDRESS));
define('TEXT_PL_AVAIL_TILL', 'Special offer valid till: ');
define('TEXT_PL_SPECIAL', 'Special offer ');
define('TEXT_PL_PRODUCT_HAS_NO_PRICE', '--.--');
define('TEXT_PL_CATEGORIES', 'All Categories');
define('NAVBAR_TITLE', 'Printable Price List');
define('TABLE_HEADING_SOH', 'Stock'); // bmoroney
define('TABLE_HEADING_ADDTOCART', 'Add to cart');//Added by Vartan Kat for Add to cart button
define('PL_TEXT_GROUP_NOT_ALLOWED', 'Sorry, you\'re not allowed to view this list.');
define('PL_PRINT_ME', 'Print this Page');