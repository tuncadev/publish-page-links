<?php
/*
Plugin Name: Published Posts List
Plugin URI:  https://www.epicflow.com
Description: A plugin to list all published posts with outgoing links and their permalinks. With the functionality to delete <a> tag from the chosen content
Version:     1.2
Author:      Murat Tunca (HYS Enterprise)
Author URI:  https://www.epicflow.com
License:     GPL2
*/

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin path constants
define('PPL_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('PPL_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include necessary files
include_once PPL_PLUGIN_PATH . 'includes/PPL_Hooks.php';
include_once PPL_PLUGIN_PATH . 'includes/PPL_Functions.php';
include_once PPL_PLUGIN_PATH . 'classes/PPL_Admin_Page.php';

// Function to add custom role
function add_ppl_seo_user_role() {
  add_role(
      'ppl_seo_user',
      'PPL SEO User',
      array(
          'read' => true,
          'edit_posts' => false,
          'delete_posts' => false,
          'publish_posts' => false,
          'upload_files' => false,
          'manage_options' => false,
          'access_published_posts_list' => true, // Custom capability for accessing your plugin
      )
  );
}
add_action('init', 'add_ppl_seo_user_role');

// Function to remove admin menus for SEO users
function remove_menus_for_ppl_seo_user() {
  if (current_user_can('ppl_seo_user')) {
      remove_menu_page('index.php');                  // Dashboard
      remove_menu_page('edit.php');                   // Posts
      remove_menu_page('upload.php');                 // Media
      remove_menu_page('edit.php?post_type=page');    // Pages
      remove_menu_page('edit-comments.php');          // Comments
      remove_menu_page('themes.php');                 // Appearance
      remove_menu_page('profile.php');                // Profile
      remove_menu_page('plugins.php');                // Plugins
      remove_menu_page('users.php');                  // Users
      remove_menu_page('tools.php');                  // Tools
      remove_menu_page('options-general.php');        // Settings
  }
}
add_action('admin_menu', 'remove_menus_for_ppl_seo_user', 999);


// Hook to add admin menu and handle exports
add_action('admin_menu', ['PPL_Hooks', 'add_admin_menu']);
add_action('admin_init', ['PPL_Hooks', 'handle_txt_export']);
