<?php
// -----
// Part of the "Printable Price List" plugin for Zen Cart.
// $Id: pricelist.php, 2006 paulm
//
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
define('PL_PRINT_ME', 'Print this Page');

define('TEXT_OPTIONS_AVAILABLE', 'Available Options:');
define('TEXT_INCL', '-');
define('TEXT_OPTION_IS_FILE', 'File upload');
define('TEXT_OPTION_IS_TEXT', 'Text input');
define('TEXT_OPTION_IS_PER_WORD', ', per word');
define('TEXT_OPTION_FREE_WORDS', ', %u word(s) free.');   //-%u is filled in with the number of free words
define('TEXT_OPTION_IS_PER_LETTER', ', per letter');
define('TEXT_OPTION_FREE_LETTERS', ', %u letter(s) free.');   //-%u is filled in with the number of free letters
