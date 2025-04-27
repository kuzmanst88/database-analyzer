<?php

if (!defined('ABSPATH')) {
    exit; 
}


class WPDBA_Database_Optimization {
    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function count_post_revisions() {
        global $wpdb;
    
        $table_name = esc_sql($wpdb->posts);
    
        $result = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM `$table_name` WHERE post_type = %s",
                'revision'
            )
        );
    
        return (int) $result;
    }

    public function delete_post_revisions() {
        global $wpdb;
    
        // Sanitize the table name
        $table_name = esc_sql($wpdb->posts);
    
        // Prepare and execute the query inline
        $result = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM $table_name WHERE post_type = %s",
                'revision'
            )
        );
    
        return (int) $result;
    }

    /* Orphaned Post Records in _postmeta */
    public function count_orphaned_post() {
        global $wpdb;

        $table_name = esc_sql($wpdb->posts);

        
        $query = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$this->wpdb->postmeta} pm
                  LEFT JOIN {$this->wpdb->posts} p ON pm.post_id = p.ID
                  WHERE p.ID IS NULL;"));
        return (int) $query;
    }


   /* Delete Orphaned Post Records in _postmeta */
    public function delete_orphaned_post() {
        global $wpdb;

        $postmeta_table = esc_sql($wpdb->postmeta);
        $posts_table = esc_sql($wpdb->posts);

        $result = $wpdb->query(
            "DELETE pm.* FROM $postmeta_table pm
            LEFT JOIN $posts_table p ON pm.post_id = p.ID
            WHERE p.ID IS NULL"
        );

        return (int) $result;
    }

    /* Count and Delete Auto-drafts */
    public function count_auto_drafts() {
        global $wpdb;

        $posts_table = esc_sql($wpdb->posts);

        $result = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $posts_table WHERE post_status = %s",
                'auto-draft'
            )
        );

        return (int) $result;
    }

    public function delete_auto_drafts() {
        global $wpdb;

        $posts_table = esc_sql($wpdb->posts);

        $result = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM $posts_table WHERE post_status = %s",
                'auto-draft'
            )
        );

        return (int) $result;
    }

    /* Count and Delete Orphaned Relationships */
    public function count_relationships() {
        global $wpdb;

        $term_relationships_table = esc_sql($wpdb->term_relationships);
        $posts_table = esc_sql($wpdb->posts);

        $result = $wpdb->get_var(
            "SELECT COUNT(*) FROM $term_relationships_table 
            WHERE term_taxonomy_id = 1 
            AND object_id NOT IN (SELECT ID FROM $posts_table)"
        );

        return (int) $result;
    }

    public function delete_relationships() {
        global $wpdb;

        $term_relationships_table = esc_sql($wpdb->term_relationships);
        $posts_table = esc_sql($wpdb->posts);

        $result = $wpdb->query(
            "DELETE FROM $term_relationships_table 
            WHERE term_taxonomy_id = 1 
            AND object_id NOT IN (SELECT ID FROM $posts_table)"
        );

        return (int) $result;
    }

    /* Count and Delete Pending Comments */
    public function count_pending_comments() {
        global $wpdb;

        $comments_table = esc_sql($wpdb->comments);

        $result = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $comments_table WHERE comment_approved = %s",
                '0'
            )
        );

        return (int) $result;
    }

    public function delete_pending_comments() {
        global $wpdb;

        $comments_table = esc_sql($wpdb->comments);

        $result = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM $comments_table WHERE comment_approved = %s",
                '0'
            )
        );

        return (int) $result;
    }

    /* Count and Delete Spam Comments */
    public function count_spam_comments() {
        global $wpdb;

        $comments_table = esc_sql($wpdb->comments);

        $result = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $comments_table WHERE comment_approved = %s",
                'spam'
            )
        );

        return (int) $result;
    }

    public function delete_spam_comments() {
        global $wpdb;

        $comments_table = esc_sql($wpdb->comments);

        $result = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM $comments_table WHERE comment_approved = %s",
                'spam'
            )
        );

        return (int) $result;
    }

    /* Count and Delete Trashed Comments */
    public function count_trashed_comments() {
        global $wpdb;

        $comments_table = esc_sql($wpdb->comments);

        $result = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $comments_table WHERE comment_approved = %s",
                'trash'
            )
        );

        return (int) $result;
    }

    public function delete_trashed_comments() {
        global $wpdb;

        $comments_table = esc_sql($wpdb->comments);

        $result = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM $comments_table WHERE comment_approved = %s",
                'trash'
            )
        );

        return (int) $result;
    }

    /* Count and Delete Orphaned Comment Meta Data */
    public function count_comment_meta() {
        global $wpdb;

        $commentmeta_table = esc_sql($wpdb->commentmeta);
        $comments_table = esc_sql($wpdb->comments);

        $result = $wpdb->get_var(
            "SELECT COUNT(*) FROM $commentmeta_table 
            WHERE comment_id NOT IN (SELECT comment_id FROM $comments_table)"
        );

        return (int) $result;
    }

    public function delete_comment_meta() {
        global $wpdb;

        $commentmeta_table = esc_sql($wpdb->commentmeta);
        $comments_table = esc_sql($wpdb->comments);

        $result = $wpdb->query(
            "DELETE FROM $commentmeta_table 
            WHERE comment_id NOT IN (SELECT comment_id FROM $comments_table)"
        );

        return (int) $result;
    }

    /* Count and Delete Expired Transients */
    public function count_expired_transients() {
        global $wpdb;

        $options_table = esc_sql($wpdb->options);

        $result = $wpdb->get_var(
            "SELECT COUNT(*) AS expired_transients FROM $options_table 
            WHERE option_name LIKE '%_transient_timeout_%' 
            AND option_value < UNIX_TIMESTAMP()"
        );

        return (int) $result;
    }

    public function delete_expired_transients() {
        global $wpdb;

        $options_table = esc_sql($wpdb->options);

        $result = $wpdb->query(
            "DELETE FROM $options_table 
            WHERE option_name LIKE '%_transient_timeout_%' 
            AND option_value < UNIX_TIMESTAMP()"
        );

        return (int) $result;
    }
}

