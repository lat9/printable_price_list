<?php 

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
// $Id: html_header.php, v1.0 2007/12/17 paulm
// 
require(DIR_WS_MODULES . 'meta_tags.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">  
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>" > 
<title><?php echo META_TAG_TITLE; ?></title>
<meta name="keywords" content="<?php echo META_TAG_KEYWORDS; ?>" >
<meta name="description" content="<?php echo META_TAG_DESCRIPTION; ?>" >
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER . DIR_WS_HTTPS_CATALOG : HTTP_SERVER . DIR_WS_CATALOG); ?>" >
<?php
$directory_array = $template->get_template_part($template->get_template_dir('.css', DIR_WS_TEMPLATE, $current_page_base, 'css'), '/^style/', '.css');
while (list ($key, $value) = each($directory_array)) {
  echo '<link rel="stylesheet" type="text/css" href="' . $template->get_template_dir('.css', DIR_WS_TEMPLATE, $current_page_base, 'css') . '/' . $value . '" >' . "\n";
} 
?>
<link rel="stylesheet" type="text/css" href="<?php echo $template->get_template_dir('.css', DIR_WS_TEMPLATE, $current_page_base, 'css') . '/profile-' . $price_list->current_profile . '.css'; ?>">
<?php
$directory_array = $template->get_template_part($template->get_template_dir('.js', DIR_WS_TEMPLATE, $current_page_base, 'jscript'), '/^jscript_/', '.js');
while (list ($key, $value) = each($directory_array)) {
  echo '<script language="javascript" src="' . $template->get_template_dir('.js', DIR_WS_TEMPLATE, $current_page_base, 'jscript') . '/' . $value . '"></script>';
} 

$directory_array = $template->get_template_part($page_directory, '/^jscript_/');

while (list ($key, $value) = each($directory_array)) {
  require($page_directory . '/' . $value);
} 

if ($price_list->config['nowrap']) {
?>
<style type="text/css">
<!--
td.prdPL div, td.manPL div, td.modPL div, td.wgtPL div, td.ntsPL div { display: block; white-space: nowrap; overflow: hidden; }
-->
</style>
<?php
} 
?>
</head>