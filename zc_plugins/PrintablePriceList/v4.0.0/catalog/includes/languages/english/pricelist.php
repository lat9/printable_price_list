<?php
// -----
// Part of the "Printable Price List" plugin for Zen Cart.
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

define('TEXT_OPTIONS_AVAILABLE', 'Available Options:');
define('TEXT_INCL', '-');
define('TEXT_OPTION_IS_FILE', 'File upload');
define('TEXT_OPTION_IS_TEXT', 'Text input');
define('TEXT_OPTION_IS_PER_WORD', ', per word');
define('TEXT_OPTION_FREE_WORDS', ', %u word(s) free.');   //-%u is filled in with the number of free words
define('TEXT_OPTION_IS_PER_LETTER', ', per letter');
define('TEXT_OPTION_FREE_LETTERS', ', %u letter(s) free.');   //-%u is filled in with the number of free letters
