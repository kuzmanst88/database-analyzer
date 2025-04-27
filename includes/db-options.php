<?php
if(!defined('ABSPATH')){
    exit;
}

 class All_Options {
     private $wpdb;

     public function __construct(){
         global $wpdb; 
         $this->wpdb=$wpdb;
     }
     public function get_all_opts($page = 1, $items_per_page = 20) {
        global $wpdb;
    
        // Calculate offset for pagination
        $offset = ($page - 1) * $items_per_page;
    
        $options_table = esc_sql($wpdb->options);
    
        // Use $wpdb->prepare() to safely inject pagination values
        $query = $wpdb->get_results($wpdb->prepare(
            "SELECT 
                option_id,
                option_name,
                option_value, 
                autoload,
                ROUND(LENGTH(option_value) / (1024 * 1024), 2) AS option_size_mb
            FROM 
                $options_table
            ORDER BY 
                option_size_mb DESC
            LIMIT 
                %d OFFSET %d",
            $items_per_page,
            $offset
        ));
    
        return $query;
    }
    
    public function get_total_options_count() {
        global $wpdb;
    
        $options_table = esc_sql($wpdb->options);
    
        // No placeholders are needed because this query has no dynamic parameters
        $query = $wpdb->get_var("SELECT COUNT(*) FROM $options_table");
    
        // Execute the query and return the result
        return (int) ($query);
    }
    
    
    public function get_option_creator($option_name) {
        // List of common core WordPress options
        $core_options = array(
            // WP core options until version 6.7.1
            'siteurl',
            'home',
            'blogname',
            'admin_email',
            'date_format',
            'time_format',
            'rewrite_rules',
            'cron',
            'blogdescription',
            'users_can_register',
            'start_of_week',
            'use_balanceTags',
            'use_smilies',
            'require_name_email',
            'comments_notify',
            'posts_per_rss',
            'rss_use_excerpt',
            'mailserver_url',
            'mailserver_login',
            'mailserver_pass',
            'mailserver_port',
            'default_category',
            'default_comment_status',
            'default_ping_status',
            'default_pingback_flag',
            'posts_per_page',
            'links_updated_date_format',
            'comment_moderation',
            'moderation_notify',
            'permalink_structure',
            'hack_file',
            'blog_charset',
            'moderation_keys',
            'active_plugins',
            'category_base',
            'ping_sites',
            'comment_max_links',
            'gmt_offset',
            'default_email_category',
            'recently_edited',
            'template',
            'stylesheet',
            'comment_registration',
            'html_type',
            'use_trackback',
            'default_role',
            'db_version',
            'uploads_use_yearmonth_folders',
            'upload_path',
            'blog_public',
            'default_link_category',
            'show_on_front',
            'tag_base',
            'show_avatars',
            'avatar_rating',
            'upload_url_path',
            'thumbnail_size_w',
            'thumbnail_size_h',
            'thumbnail_crop',
            'medium_size_w',
            'medium_size_h',
            'avatar_default',
            'large_size_w',
            'large_size_h',
            'image_default_link_type',
            'image_default_size',
            'image_default_align',
            'close_comments_for_old_posts',
            'close_comments_days_old',
            'thread_comments',
            'thread_comments_depth',
            'page_comments',
            'comments_per_page',
            'default_comments_page',
            'comment_order',
            'sticky_posts',
            'widget_categories',
            'widget_text',
            'widget_rss',
            'uninstall_plugins',
            'timezone_string',
            'page_for_posts',
            'page_on_front',
            'default_post_format',
            'link_manager_enabled',
            'finished_splitting_shared_terms',
            'site_icon',
            'medium_large_size_w',
            'medium_large_size_h',
            'wp_page_for_privacy_policy',
            'show_comments_cookies_opt_in',
            'admin_email_lifespan',
            'disallowed_keys',
            'comment_previously_approved',
            'auto_plugin_theme_update_emails',
            'auto_update_core_dev',
            'auto_update_core_minor',
            'auto_update_core_major',
            'wp_force_deactivated_plugins',
            'wp_attachment_pages_enabled',
            'initial_db_version',
            'opq_user_roles',
            'fresh_site',
            'user_count',
            'widget_block',
            'sidebars_widgets',
            'widget_pages',
            'widget_calendar',
            'widget_archives',
            'widget_media_audio',
            'widget_media_image',
            'widget_media_gallery',
            'widget_media_video',
            'widget_meta',
            'widget_search',
            'widget_recent-posts',
            'widget_recent-comments',
            'widget_tag_cloud',
            'widget_nav_menu',
            'widget_custom_html',
            'recovery_keys',
            'WPLANG',
            'sco_id',
            'recently_activated'
        );
        

        // Check if the option is a core option
        if (in_array($option_name, $core_options)) {
            return 'WordPress Core';
        }

        // Else check your custom table for plugin-added options
    }

}
