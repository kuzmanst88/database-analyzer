<?php

if (!defined('ABSPATH')) {
    exit;
}

class DB_Tables {

    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    /**
     * Get a list of all tables in the WordPress database.
     *
     * @param string $order_by Column to sort by (size or rows).
     * @param string $order Direction to sort ('ASC' or 'DESC').
     * @return array List of table names.
     */
    public function get_all_tables($order_by = 'size', $order = 'DESC') {
        // Default sorting is by size (in MB)
        $tables = $this->wpdb->get_results("SHOW TABLES", ARRAY_N);
        $tables_info = [];

        // Fetch size and row count for each table
        foreach ($tables as $table) {
            $table_name = $table[0];
            $table_size = $this->get_table_size($table_name);
            $row_count = $this->get_row_count($table_name);
            $tables_info[] = [
                'name' => $table_name,
                'size' => $table_size,
                'rows' => $row_count,
            ];
        }

        // Sort the tables by the specified column (size or rows)
        if ($order_by === 'size') {
            usort($tables_info, function ($a, $b) use ($order) {
                return ($order === 'ASC') ? $a['size'] - $b['size'] : $b['size'] - $a['size'];
            });
        } elseif ($order_by === 'rows') {
            usort($tables_info, function ($a, $b) use ($order) {
                return ($order === 'ASC') ? $a['rows'] - $b['rows'] : $b['rows'] - $a['rows'];
            });
        }

        return $tables_info;
    }

    /**
     * Get the number of rows in a given table.
     *
     * @param string $table Table name.
     * @return int Number of rows.
     */
    // public function get_row_count($table) {
    //     $query = "SELECT COUNT(*) FROM $table";
    //     return (int) $this->wpdb->get_var($query);
    // }

    public function get_row_count($table) {
        // Sanitize the table name manually
        $sanitized_table = esc_sql($table);
    
        // Dynamically inject the sanitized table name (not using `%s` here)
        $query = $this->wpdb->get_var("SELECT COUNT(*) FROM `$sanitized_table`");
    
        // Execute the query
        return (int) $query;
    }

    /**
     * Get the size of a given table in MB.
     *
     * @param string $table Table name.
     * @return float Size of the table in MB.
     */
    // public function get_table_size($table) {
    //     $query = "SHOW TABLE STATUS LIKE '$table'";
    //     $result = $this->wpdb->get_row($query);
    //     return isset($result->Data_length) ? round($result->Data_length / 1024 / 1024, 2) : 0; // in MB
    // }
    public function get_table_size($table) {
        global $wpdb;
        // Use $wpdb->prepare to safely pass the table name
        $query = $wpdb->get_row($wpdb->prepare("SHOW TABLE STATUS LIKE %s", $table));
        return isset($query->Data_length) ? round($query->Data_length / 1024 / 1024, 2) : 0; // in MB
    }
}