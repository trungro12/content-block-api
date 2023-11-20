<?php

/**
 * @package ContentBlockAPI
 */
/*
Plugin Name: Content Block API
Description: API cho plugin Content Blocks (Custom Post Widget) - https://wordpress.org/plugins/custom-post-widget/
Version: 1.0
Requires at least: 5.0
Requires PHP: 5.2
Author: Trung Pham
License: GPLv2 or later
*/

if (!function_exists('add_action')) {
	echo 'Không thể chạy plugins trong website!';
	exit;
}

const CONTENTBLOCK_API_CLASS_NAME = 'ContentBlockAPI';
define('CONTENTBLOCK_API_URL', home_url('wp-json/content-block-api/get'));
define('CONTENTBLOCK_API__PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CONTENTBLOCK_API__PLUGIN_URL', plugin_dir_url(__FILE__));
require_once(CONTENTBLOCK_API__PLUGIN_DIR . '/lib/core.php');
add_action('init', array(CONTENTBLOCK_API_CLASS_NAME, 'init'));