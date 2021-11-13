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
// $Id: pricelist.php, v1.02 2004/11/19 paulm
//
define('TABLE_HEADING_PRODUCTS', 'Product');
define('TABLE_HEADING_MODEL', 'Model');
define('TABLE_HEADING_MANUFACTURER', 'Fabrikant');
define('TABLE_HEADING_WEIGHT', 'Gewicht');
define('TABLE_HEADING_PRICE_INC', 'inc. ');
define('TABLE_HEADING_PRICE_EX', 'ex. ');
define('TABLE_HEADING_NOTES_A', 'Opmerking (A)');
define('TABLE_HEADING_NOTES_B', 'Opmerking (B)');

define('TEXT_PL_PAGE', 'Pagina: ');
define('TEXT_PL_HEADER_TITLE',  '%s Catalogus');
define('TEXT_PL_HEADER_TITLE_PRINT', 'Catalogus %s');
define('TEXT_PL_SCREEN_INTRO','Klik op de links voor gedetailleerde productinfomatie (%s producten).');
define('TEXT_PL_NOTHING_FOUND', 'Geen producten gevonden, maak een andere selectie alstublieft.');

define('STORE_NAME_ADDRESS_PL', str_replace("\n", " - ", STORE_NAME_ADDRESS));
define('TEXT_PL_AVAIL_TILL', 'Speciale aanbieding geldig tot: ');
define('TEXT_PL_SPECIAL', 'Speciale aanbieding ');
define('TEXT_PL_PRODUCT_HAS_NO_PRICE', '--.--');
define('TEXT_PL_CATEGORIES', 'Selecteer categorie');
define('NAVBAR_TITLE', 'Catalogus');
define('TABLE_HEADING_SOH', 'Voorraad'); // bmoroney
define('TABLE_HEADING_ADDTOCART', 'Add to cart');//Added by Vartan Kat for Add to cart button
define('PL_TEXT_GROUP_NOT_ALLOWED', 'Sorry, you\'re not allowed to view this list.');
