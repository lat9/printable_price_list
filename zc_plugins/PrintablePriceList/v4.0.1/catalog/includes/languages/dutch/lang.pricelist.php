<?php
// -----
// Part of the "Printable Price List" plugin for Zen Cart.
// $Id: pricelist.php, 2006 paulm
//
// $Id: pricelist.php, v1.02 2004/11/19 paulm
//
$define = [
    'TABLE_HEADING_PRODUCTS' => 'Product',
    'TABLE_HEADING_MODEL' => 'Model',
    'TABLE_HEADING_MANUFACTURER' => 'Fabrikant',
    'TABLE_HEADING_WEIGHT' => 'Gewicht',
    'TABLE_HEADING_PRICE_INC' => 'inc. ',
    'TABLE_HEADING_PRICE_EX' => 'ex. ',
    'TABLE_HEADING_NOTES_A' => 'Opmerking (A)',
    'TABLE_HEADING_NOTES_B' => 'Opmerking (B)',

    'TEXT_PL_PAGE' => 'Pagina: ',
    'TEXT_PL_HEADER_TITLE' => '%s Catalogus',
    'TEXT_PL_HEADER_TITLE_PRINT' => 'Catalogus %s',
    'TEXT_PL_SCREEN_INTRO' => 'Klik op de links voor gedetailleerde productinfomatie (%s producten).',
    'TEXT_PL_NOTHING_FOUND' => 'Geen producten gevonden, maak een andere selectie alstublieft.',

    'STORE_NAME_ADDRESS_PL' => str_replace("\n", " - ", STORE_NAME_ADDRESS),
    'TEXT_PL_AVAIL_TILL' => 'Speciale aanbieding geldig tot: ',
    'TEXT_PL_SPECIAL' => 'Speciale aanbieding ',
    'TEXT_PL_PRODUCT_HAS_NO_PRICE' => '--.--',
    'TEXT_PL_CATEGORIES' => 'Selecteer categorie',
    'NAVBAR_TITLE' => 'Catalogus',
    'TABLE_HEADING_SOH' => 'Voorraad', // bmoroney
    'TABLE_HEADING_ADDTOCART' => 'Add to cart',//Added by Vartan Kat for Add to cart button
    'PL_TEXT_GROUP_NOT_ALLOWED' => 'Sorry, you\'re not allowed to view this list.',
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
