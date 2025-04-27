<?php 
if(!defined('ABSPATH')){
    exit;
}

function wpdba_get_total_database_size() {
    global $wpdb;
    
    // Query to get the total size of the database
    // $query = "SELECT SUM(data_length + index_length) AS total_size
    //           FROM information_schema.TABLES
    //           WHERE table_schema = %s";
    
    // Prepare the query and pass the database name
    $total_size = $wpdb->get_var($wpdb->prepare("SELECT SUM(data_length + index_length) AS total_size
    FROM information_schema.TABLES
    WHERE table_schema = %s", DB_NAME));
    
    // Convert bytes to megabytes for readability
    $total_size_mb = $total_size / 1024 / 1024;
    
    // Format it to two decimal places
    return number_format($total_size_mb, 2) . ' MB';

    if ($total_size === null) {
        return 'Unable to retrieve data size';
    }
}



// Get the total count of the database tables
function wpdba_get_total_table_count() {
    global $wpdb;
    
    $table_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) AS table_count
    FROM information_schema.TABLES
    WHERE table_schema = %s", DB_NAME));
    
    // Return the result
    return intval($table_count);
}

// 3 largest tables
function wpdba_get_largest_tables($limit = 5) {
    global $wpdb;
    
    // Prepare and execute query
    $results = $wpdb->get_results($wpdb->prepare("SELECT TABLE_NAME AS table_name, 
    ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) AS table_size_mb
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = %s
ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC
LIMIT %d", DB_NAME, $limit));

    // Ensure we return results, even if no tables are found (return an empty array)
    return is_array($results) ? $results : [];
}

function wpdba_get_autoloaded_data_size() {
    global $wpdb;
    
    // Prepare the query and pass the database name
    $total_size = $wpdb->get_var($wpdb->prepare("SELECT SUM(LENGTH(option_value)) AS autoload_size
    FROM {$wpdb->options}
    WHERE autoload = 'yes'", DB_NAME));
    
    // Convert bytes to megabytes for readability
    $total_size_mb = $total_size / 1024 / 1024;
    
    // Format it to two decimal places
    return number_format($total_size_mb, 2) . ' MB';

    if ($total_size === null) {
        return 'Unable to retrieve autoloaded data size';
    }
}

// 3 largest options
function wpdba_get_largest_autoloaded_options($limit = 5) {
    global $wpdb;

    // Execute the prepared query to protect against SQL injection
    $results = $wpdb->get_results($wpdb->prepare("SELECT option_name, 
    ROUND(LENGTH(option_value) / (1024 * 1024), 3) AS option_size_mb
    FROM {$wpdb->options}
    WHERE autoload = 'yes'
    ORDER BY option_size_mb DESC
    LIMIT %d", $limit));

    // Ensure results are returned, even if no options are found
    return is_array($results) ? $results : [];
}
