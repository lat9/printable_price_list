<?php
/** 
 * @copyright Copyright 2003-2007 Paul Mathot Haarlem, The Netherlands & Carine Bruyndoncx, Belgium
 * @copyright parts Copyright 2003-2005 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version v1.5.7 (or newer)
 */
// -----
// Part of the Printable Price List plugin for Zen Cart v1.5.7 and later.
// Copyright (C) 2014-2021, Vinos de Frutas Tropicales (lat9)
//
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}
require DIR_WS_MODULES . 'require_languages.php';

// -----
// Instantiate the price list for use by the template.
//
$price_list = new PrintablePriceList();

$flag_disable_header = true;
$flag_disable_footer = true;
$flag_disable_right = true;
$flag_disable_left = true;
