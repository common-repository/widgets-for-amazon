<?php

/**
 * Plugin Name: Widgets for Amazon
 * Plugin URI: https://blog.eggnstone.com/blog/widgets-for-amazon-for-wordpress
 * Description: Widgets for Amazon by eggnstone
 * Version: 1.0.27
 * Author: eggnstone
 * Author URI: https://eggnstone.com
 */

namespace WidgetsForAmazon;

/** @noinspection PhpIncludeInspection */
include plugin_dir_path(__FILE__) . 'includes/Admin.php';
/** @noinspection PhpIncludeInspection */
include plugin_dir_path(__FILE__) . 'includes/Constants.php';
/** @noinspection PhpIncludeInspection */
include plugin_dir_path(__FILE__) . 'includes/Plugin.php';
/** @noinspection PhpIncludeInspection */
include plugin_dir_path(__FILE__) . 'includes/Tools.php';

//register_activation_hook(__FILE__, __NAMESPACE__ . '\Admin::admin_activate_plugin');
//register_deactivation_hook(__FILE__, __NAMESPACE__ . '\Admin::admin_deactivate_plugin');
//register_uninstall_hook(__FILE__, __NAMESPACE__ . '\Admin::admin_uninstall_plugin');

add_action('admin_init', __NAMESPACE__ . '\Admin::admin_init');
add_action('admin_menu', __NAMESPACE__ . '\Admin::admin_menu');

add_filter('get_product_search_form', __NAMESPACE__ . '\Plugin::filter_get_product_search_form');
add_filter('get_search_form', __NAMESPACE__ . '\Plugin::filter_get_search_form');
add_filter('plugin_action_links_' . plugin_basename(__FILE__), __NAMESPACE__ . '\Plugin::filter_plugin_action_links', 10, 2);
add_filter('the_content', __NAMESPACE__ . '\Plugin::filter_the_content', 20); // 20 to wait for expansion of e.g. UX blocks.
