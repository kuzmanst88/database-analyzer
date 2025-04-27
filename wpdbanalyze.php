<?php 
/*
Plugin Name: Database Analyzer
Description: Analyze + Optimize WP database
Version: 1.0.0
Author URI:        https://kuzmans3.sg-host.com
Author: Kuzman
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if(!defined('ABSPATH')){
    exit;
}

define ('WPDBA_PLUGIN_PATH', plugin_dir_path(__FILE__));
define ('WPDBA_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once WPDBA_PLUGIN_PATH . 'includes/admin-menu.php';

$current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';

    if ($current_tab==='general'){
        require_once WPDBA_PLUGIN_PATH . 'includes/db-general-functions.php';
    }   elseif ($current_tab==='optimization'){
        require_once WPDBA_PLUGIN_PATH . 'includes/db-optimization-functions.php';
    }

