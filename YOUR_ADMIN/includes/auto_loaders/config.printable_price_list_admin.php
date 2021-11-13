<?php
// -----
// Admin-level auto-loader for the "Printable Price List" plugin for Zen Cart, provided by lat9 and others.
//
if (!defined ('IS_ADMIN_FLAG')) { 
    die ('Illegal Access'); 
}

$autoLoadConfig[200][] = [
    'autoType' => 'init_script',
    'loadFile' => 'init_price_list_admin.php'
];
