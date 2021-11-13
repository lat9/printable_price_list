<?php 
// -----
// Part of the "Printable Price List" plugin for Zen Cart.
//
require DIR_WS_MODULES . 'meta_tags.php';
?>
<!DOCTYPE html>
<html <?php echo HTML_PARAMS; ?>>
<title><?php echo META_TAG_TITLE; ?></title>
<meta name="keywords" content="<?php echo META_TAG_KEYWORDS; ?>" >
<meta name="description" content="<?php echo META_TAG_DESCRIPTION; ?>" >
<?php
$directory_array = $template->get_template_part($template->get_template_dir('.css', DIR_WS_TEMPLATE, $current_page_base, 'css'), '/^style/', '.css');
foreach ($directory_array as $key => $value) {
    echo '<link rel="stylesheet" href="' . $template->get_template_dir('.css', DIR_WS_TEMPLATE, $current_page_base, 'css') . '/' . $value . '" >' . "\n";
} 
?>
<link rel="stylesheet" href="<?php echo $template->get_template_dir('.css', DIR_WS_TEMPLATE, $current_page_base, 'css') . '/profile-' . $price_list->current_profile . '.css'; ?>">
<?php
$directory_array = $template->get_template_part($template->get_template_dir('.js', DIR_WS_TEMPLATE, $current_page_base, 'jscript'), '/^jscript_/', '.js');
foreach ($directory_array as $key => $value) {
    echo '<script src="' . $template->get_template_dir('.js', DIR_WS_TEMPLATE, $current_page_base, 'jscript') . '/' . $value . '"></script>';
} 

$directory_array = $template->get_template_part($page_directory, '/^jscript_/');
foreach ($directory_array as $key => $value) {
    require($page_directory . '/' . $value);
} 

if ($price_list->config['nowrap']) {
?>
<style>
<!--
td.prdPL div, td.manPL div, td.modPL div, td.wgtPL div, td.ntsPL div { display: block; white-space: nowrap; overflow: hidden; }
-->
</style>
<?php
} 
?>
</head>