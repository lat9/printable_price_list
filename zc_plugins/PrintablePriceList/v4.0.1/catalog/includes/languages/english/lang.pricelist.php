<?php
// -----
// Part of the "Printable Price List" plugin for Zen Cart.
// $Id: pricelist.php, 2006 paulm
//
$define = [
    'TABLE_HEADING_PRODUCTS' => 'Product',
    'TABLE_HEADING_MODEL' => 'Model',
    'TABLE_HEADING_MANUFACTURER' => 'Manufacturer',
    'TABLE_HEADING_WEIGHT' => 'Weight',
    'TABLE_HEADING_PRICE_INC' => 'inc. ',
    'TABLE_HEADING_PRICE_EX' => 'ex. ',
    'TABLE_HEADING_NOTES_A' => 'Notes (A)',
    'TABLE_HEADING_NOTES_B' => 'Notes (B)',

    'TEXT_PL_PAGE' => 'Page: ',
    'TEXT_PL_HEADER_TITLE' => '%s Printable Price List',
    'TEXT_PL_HEADER_TITLE_PRINT' => 'Printable Price List: %s',
    'TEXT_PL_SCREEN_INTRO' => 'Displaying %s products, click on the links for detailed product information.',
    'TEXT_PL_NOTHING_FOUND' => 'No products or categories match your query, please make another selection.',

    'STORE_NAME_ADDRESS_PL' => str_replace("\n", ' - ', STORE_NAME_ADDRESS),
    'TEXT_PL_AVAIL_TILL' => 'Special offer valid till: ',
    'TEXT_PL_SPECIAL' => 'Special offer ',
    'TEXT_PL_PRODUCT_HAS_NO_PRICE' => '--.--',
    'TEXT_PL_CATEGORIES' => 'All Categories',
    'NAVBAR_TITLE' => 'Printable Price List',
    'TABLE_HEADING_SOH' => 'Stock', // bmoroney
    'TABLE_HEADING_ADDTOCART' => 'Add to cart',//Added by Vartan Kat for Add to cart button
    'PL_TEXT_GROUP_NOT_ALLOWED' => 'Sorry, you are not allowed to view this list.',
    'PL_PRINT_ME' => 'Print this Page',

    'TEXT_OPTIONS_AVAILABLE' => 'Available Options:',
    'TEXT_INCL' => '-',
    'TEXT_OPTION_IS_FILE' => 'File upload',
    'TEXT_OPTION_IS_TEXT' => 'Text input',
    'TEXT_OPTION_IS_PER_WORD' => ', per word',
    'TEXT_OPTION_FREE_WORDS' => ', %u word(s) free.',   //-%u is filled in with the number of free words
    'TEXT_OPTION_IS_PER_LETTER' => ', per letter',
    'TEXT_OPTION_FREE_LETTERS' => ', %u letter(s) free.',   //-%u is filled in with the number of free letters
];
return $define;
