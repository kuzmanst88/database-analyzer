<?php
if (!defined("ABSPATH")) {
    exit();
}

add_action("admin_menu", "wpdba_add_admin_menu");

function wpdba_add_admin_menu()
{
    add_menu_page(
        "WP DB Analyzer",
        "DB Analyzer",
        "manage_options",
        "wpdbanalyze",
        "wpdbanalyze_admin_page",
        "dashicons-database",
        999
    );
}

function wpdbanalyze_admin_page()
{
    if (!current_user_can("manage_options")) {
        return;
    }

    echo '<div class="wrap">';
    echo '<div class="wpdba-tab-content wpdba-main">';
    echo '<div class="wpdba-main-box-image"><img src="' .
        esc_url(WPDBA_PLUGIN_URL .
        "assets/img/wpdba-logo.png") .
        '" alt="Logo" width="100" height="100"></div>';
    echo '<div class="wpdba-main-box-title"><h1>' .
        esc_html__("Database Analyzer", "wpdbanalyze") .
        "</div></h1>";
    echo "</div>";
    // Set current tab based on URL query parameter, default to 'general'
    $current_tab = isset($_GET["tab"])
        ? sanitize_text_field($_GET["tab"])
        : "general";

    // Tab Navigation
    echo '<div class="wpdba-menu">';
    echo '<h2 class="wpdba-nav-tab-wrapper">';
    echo '<a  href="' .
        esc_url(admin_url("admin.php?page=wpdbanalyze&tab=general")) .
        '" class="wpdba-nav-tab ' .
        ($current_tab === "general" ? "wpdba-nav-tab-active" : "") .
        '">' .
        esc_html__("General Information", "wpdbanalyze") .
        "</a>";
    echo '<a href="' .
        esc_url(admin_url("admin.php?page=wpdbanalyze&tab=optimization")) .
        '" class="wpdba-nav-tab ' .
        ($current_tab === "optimization" ? "wpdba-nav-tab-active" : "") .
        '">' .
        esc_html__("Optimization", "wpdbanalyze") .
        "</a>";
    echo '<a href="' .
        esc_url(admin_url("admin.php?page=wpdbanalyze&tab=tables")) .
        '" class="wpdba-nav-tab ' .
        ($current_tab === "tables" ? "wpdba-nav-tab-active" : "") .
        '">' .
        esc_html__("Tables", "wpdbanalyze") .
        "</a>";
    echo '<a href="' .
        esc_url(admin_url("admin.php?page=wpdbanalyze&tab=options")) .
        '" class="wpdba-nav-tab ' .
        ($current_tab === "options" ? "wpdba-nav-tab-active" : "") .
        '">' .
        esc_html__("Options", "wpdbanalyze") .
        "</a>";
    echo '<a href="' .
        esc_url(admin_url("admin.php?page=wpdbanalyze&tab=woocommerce")) .
        '" class="wpdba-nav-tab ' .
        ($current_tab === "woocommerce" ? "wpdba-nav-tab-active" : "") .
        '">' .
        esc_html__("WooCommerce", "wpdbanalyze") .
        "</a>";
    echo "</h2>";
    echo "</div>";

    // Conditional Content Rendering Based on Selected Tab
    if ($current_tab === "general") {
        // General Tab Content
        echo '<div id="general" class="wpdba-tab-content">';
        echo '<div id="general" class="wpdba-title">';
        echo "<h2>Overview</h2>";
        echo "</div>";
        // total DB size
        echo '<div class="wpdba-admin-boxes">';
        echo '<div class="wpdba-box-three"><h2>' .
            esc_html__("Database Size", "wpdbanalyze") .
            "</h2>";
        echo '<span class="wpdba-result">' .
            esc_html(wpdba_get_total_database_size()) .
            "</span>";
        echo '<div class="wpdba-see-more"> <a href="' .
            esc_url(admin_url("admin.php?page=wpdbanalyze&tab=optimization")) .
            '" class="button wpdba-button">' .
            esc_html__("See more", "wpdbanalyze") .
            "</a></div></div>";
        // Tables Count
        echo '<div class="wpdba-box-three"><h2>' .
            esc_html__("Number of Tables", "wpdbanalyze") .
            "</h2>";
        echo '<span class="wpdba-result">' .
            esc_html(wpdba_get_total_table_count()) .
            "</span>";
        echo '<div class="wpdba-see-more"> <a href="' .
            esc_url(admin_url("admin.php?page=wpdbanalyze&tab=tables")) .
            '" class="button wpdba-button">' .
            esc_html__("See more", "wpdbanalyze") .
            "</a></div></div>";
        // Total Autoloaded
        echo '<div class="wpdba-box-three"><h2>' .
            esc_html__("Total Autoloaded Options", "wpdbanalyze") .
            "</h2>";
        echo '<span class="wpdba-result">' .
            esc_html(wpdba_get_autoloaded_data_size()) .
            "</span>";
        echo '<div class="wpdba-see-more"><a href="' .
            esc_url(admin_url("admin.php?page=wpdbanalyze&tab=options")) .
            '" class="button wpdba-button">' .
            esc_html__("See more", "wpdbanalyze") .
            "</a></div></div>";
        echo "</div>";

        echo "<div class=wpdba-subtitle>";
        echo "<div><h3>" .
            esc_html__("Largest Tables", "wpdbanalyze") .
            "</h3></div>";
        echo "<div><h3>" .
            esc_html__("Largest Autoloaded Options", "wpdbanalyze") .
            "</h3></div>";
        echo "</div>";
        echo '<div class="wpdba-admin-boxes">';
        echo '<div class="wpdba-box-two">';
        // 3 largest tables
        echo '<div class="wpdba-title-mobile"><h3>' .
        esc_html__("Largest Tables", "wpdbanalyze") .
        "</h3></div>";
        $largest_tables = wpdba_get_largest_tables();

        if (!empty($largest_tables)) {
            echo '<table class="striped wpdba-large-tables">';
            echo "<thead><tr><th>" .
                esc_html__("Table Name", "wpdbanalyze") .
                "</th><th>" .
                esc_html__("Size (MB)", "wpdbanalyze") .
                "</th></tr></thead>";
            echo "<tbody>";
            foreach ($largest_tables as $table) {
                // DBTable names and sizes sorted by size in a table
                echo "<tr>";
                echo "<td>" . esc_html($table->table_name) . "</td>";
                echo "<td>" . esc_html($table->table_size_mb) . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>" . esc_html__("No tables found.", "wpdbanalyze") . "</p>";
        }
        echo "</div>";
        //3 largest options

        echo '<div class="wpdba-box-two">';
        echo '<div class="wpdba-title-mobile"><h3>' .
        esc_html__("Largest Autoloaded Options", "wpdbanalyze") .
        "</h3></div>";

        $largest_autoloaded_options = wpdba_get_largest_autoloaded_options();

        if (!empty($largest_autoloaded_options)) {
            echo '<table class="striped">';
            echo "<thead><tr><th>" .
                esc_html__("Option Name", "wpdbanalyze") .
                "</th><th>" .
                esc_html__("Size (MB)", "wpdbanalyze") .
                "</th></tr></thead>";
            echo "<tbody>";
            foreach ($largest_autoloaded_options as $option) {
                echo "<tr>";
                echo "<td>" . esc_html($option->option_name) . "</td>";
                echo "<td>" . esc_html($option->option_size_mb) . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>" .
                esc_html__("No autoloaded options found.", "wpdbanalyze") .
                "</p>";
        }
        echo "</div>";
        echo "</div>";


        /* General Information tab ENDS  HERE*/
    } elseif ($current_tab === "optimization") {
        echo '<div id="wpdba_optimization" class="wpdba-tab-content">';

        // Optimization tab STARTS HERE
        //REVISIONS
        $db_optimize = new WPDBA_Database_Optimization();

        // Check for optimization action request
        if (
            isset($_POST["optimize_action"]) &&
            $_POST["optimize_action"] === "delete_revisions"
        ) {
            check_admin_referer("optimize_nonce"); // Security check
            $deleted_revisions = $db_optimize->delete_post_revisions();
            echo "<div class='updated'><p>" . esc_html($deleted_revisions) . " post revisions have been deleted.</p></div>";
        } elseif (
            isset($_POST["optimize_action"]) &&
            $_POST["optimize_action"] === "delete_orphaned_post"
        ) {
            check_admin_referer("optimize_nonce"); // Security check
            $deleted_orphaned_post = $db_optimize->delete_orphaned_post();
            echo "<div class='updated'><p>" . esc_html($deleted_orphaned_post) .  " records have been deleted.</p></div>";
        } elseif (
            isset($_POST["optimize_action"]) &&
            $_POST["optimize_action"] === "delete_auto_drafts"
        ) {
            check_admin_referer("optimize_nonce"); // Security check
            $deleted_auto_drafts = $db_optimize->delete_auto_drafts();
            echo "<div class='updated'><p>" . esc_html($deleted_auto_drafts) . " records have been deleted.</p></div>";
        } elseif (
            isset($_POST["optimize_action"]) &&
            $_POST["optimize_action"] === "delete_relationships"
        ) {
            check_admin_referer("optimize_nonce"); // Security check
            $deleted_relationships = $db_optimize->delete_relationships();
            echo "<div class='updated'><p>" . esc_html($deleted_relationships) . " records have been deleted.</p></div>";
        } elseif (
            isset($_POST["optimize_action"]) &&
            $_POST["optimize_action"] === "delete_pending_comments"
        ) {
            check_admin_referer("optimize_nonce"); // Security check
            $deleted_pending_comments = $db_optimize->delete_pending_comments();
            echo "<div class='updated'><p>" . esc_html($deleted_pending_comments) . " records have been deleted.</p></div>";
        } elseif (
            isset($_POST["optimize_action"]) &&
            $_POST["optimize_action"] === "delete_spam_comments"
        ) {
            check_admin_referer("optimize_nonce"); // Security check
            $deleted_spam_comments = $db_optimize->delete_spam_comments();
            echo "<div class='updated'><p>" . esc_html($deleted_spam_comments) . " records have been deleted.</p></div>";
        } elseif (
            isset($_POST["optimize_action"]) &&
            $_POST["optimize_action"] === "delete_trashed_comments"
        ) {
            check_admin_referer("optimize_nonce"); // Security check
            $deleted_trashed_comments = $db_optimize->delete_trashed_comments();
            echo "<div class='updated'><p>" . esc_html($deleted_trashed_comments) . " records have been deleted.</p></div>";
        } elseif (
            isset($_POST["optimize_action"]) &&
            $_POST["optimize_action"] === "delete_comment_meta"
        ) {
            check_admin_referer("optimize_nonce"); // Security check
            $deleted_orphaned_comment = $db_optimize->delete_comment_meta();
            echo "<div class='updated'><p>" . esc_html($deleted_orphaned_comment) . " records have been deleted.</p></div>";
        } elseif (
            isset($_POST["optimize_action"]) &&
            $_POST["optimize_action"] === "delete_transients"
        ) {
            check_admin_referer("optimize_nonce"); // Security check
            $deleted_transients = $db_optimize->delete_expired_transients();
            echo "<div class='updated'><p>" . esc_html($deleted_transients) . " records have been deleted.</p></div>";
        }

        // Display the optimization features
        $optimization_actions = [
            [
                "feature" => "Revisions",
                "count" => $db_optimize->count_post_revisions(),
                "action" => "delete_revisions",
            ],
            [
                "feature" => "Orphaned Post Meta",
                "count" => $db_optimize->count_orphaned_post(),
                "action" => "delete_orphaned_post",
            ],
            [
                "feature" => "Auto-drafts",
                "count" => $db_optimize->count_auto_drafts(),
                "action" => "delete_auto_drafts",
            ],
            [
                "feature" => "Orphaned Relationships",
                "count" => $db_optimize->count_relationships(),
                "action" => "delete_relationships",
            ],
            [
                "feature" => "Pending Comments",
                "count" => $db_optimize->count_pending_comments(),
                "action" => "delete_pending_comments",
            ],
            [
                "feature" => "Spam Comments",
                "count" => $db_optimize->count_spam_comments(),
                "action" => "delete_spam_comments",
            ],
            [
                "feature" => "Trashed Comments",
                "count" => $db_optimize->count_trashed_comments(),
                "action" => "delete_trashed_comments",
            ],
            [
                "feature" => "Orphaned comment meta",
                "count" => $db_optimize->count_comment_meta(),
                "action" => "delete_comment_meta",
            ],
            [
                "feature" => "Expired Transients",
                "count" => $db_optimize->count_expired_transients(),
                "action" => "delete_transients",
            ],
        ];

        // Display table

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Check if the nonce is set and valid
            if ( isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'optimize_nonce') ) {
                // The nonce is valid, now handle the action
                if ( isset($_POST['optimize_action']) ) {
                    $action = sanitize_text_field($_POST['optimize_action']);
                    
                    // Perform the optimization action (e.g., delete or optimize)
                    if ($action == 'delete') {
                        // Example logic for deletion or any other action
                        // Your code to handle the delete action goes here.
                        echo 'Optimization action performed: Delete';
                    }
                }
            } else {
                // Nonce verification failed
                wp_die('Nonce verification failed. The link you followed has expired.');
            }
        }

        echo '<div class="wpdba-optimization-container">

        <table class="striped">
            <thead>
                <tr>
                    <th>' .
            esc_html__("Elements", "wpdbanalyze") .
            '</th>
                    <th>' .
            esc_html__("Current Count", "wpdbanalyze") .
            '</th>
                    <th>' .
            esc_html__("Action", "wpdbanalyze") .
            '</th>
                </tr>
            </thead>
            <tbody>';

            foreach ($optimization_actions as $optimization) {
                echo '<tr>
                        <td>' .
                        sprintf(
                            /* translators: %s refers to the name of the database optimization feature */
                            // esc_html__('%s', 'wpdbanalyze'),
                            esc_html($optimization['feature'])
                        ) .
                        '</td>
                        <td>' .
                        esc_html($optimization['count']) .
                        '</td>
                        <td>
                            <form method="post">
                                ' .
                                wp_nonce_field('optimize_nonce', '_wpnonce', true, false) .
                        '
                                <input type="hidden" name="optimize_action" value="' .
                        esc_attr($optimization['action']) .
                        '">
                                <input type="submit" class="button button-primary" value="' .
                        esc_attr__('Delete', 'wpdbanalyze') .
                        '">
                            </form>
                        </td>
                    </tr>';
            }
            

        echo '      </tbody>
        </table>';


        echo '<div class="wpdba-optimization-text">';
        echo '<div><img src="' .
            esc_url(WPDBA_PLUGIN_URL .
            "assets/img/warning.png") .
            '" alt="Warning" width="100" height="100"></div>';
        echo '<div>IMPORTANT: The delete action is irreversible. Please create a database backup before running the optimization! </div>
     </div>';

        echo "</div>";
        // Optimization tab ENDS HERE
    } elseif ($current_tab === "tables") {
        
        echo '<div id="wpdba_tables" class="wpdba-tab-content">';

// Check if the DB_Tables class file has been included and instantiate it
if (file_exists(WPDBA_PLUGIN_PATH . "includes/db-tables.php")) {
    require_once WPDBA_PLUGIN_PATH . "includes/db-tables.php";
    $db_tables = new DB_Tables();
}

// Get sort parameters from the URL or default to sorting by size (DESC)
$order_by = isset($_GET["order_by"]) && in_array($_GET["order_by"], ["size", "rows"]) ? $_GET["order_by"] : "size";
$order = isset($_GET["order"]) && in_array($_GET["order"], ["ASC", "DESC"]) ? $_GET["order"] : "DESC";

// Get all tables sorted by the selected criteria
$tables = $db_tables->get_all_tables($order_by, $order);

// Display the tables in a responsive format
echo "<h1>" . esc_html__("WordPress Database Tables", "wpdbanalyze") . "</h1>";
echo '<table class="responsive-table">
    <thead>
        <tr>
            <th>' . esc_html__("Table Name", "wpdbanalyze") . '</th>
        </tr>
    </thead>
    <tbody>';

// Loop through each table and display its info
foreach ($tables as $table) {
    echo '<tr class="main-row">';
    echo '<td class="wpdba-table-expand">';
    echo '<div class="table-name">' . esc_html($table["name"]) . '</div>';
    echo '<button class="toggle-details">▼</button>';
    echo '<div class="details">';
    echo '<p><strong>' . esc_html__("Size (MB):", "wpdbanalyze") . '</strong> ' . esc_html($table["size"]) . '</p>';
    echo '<p><strong>' . esc_html__("Number of Rows:", "wpdbanalyze") . '</strong> ' . esc_html($table["rows"]) . '</p>';
    echo '</div>';
    echo '</td>';
    echo '</tr>';
}

echo '</tbody>
</table>';
echo "</div>";
    } elseif ($current_tab === "options") {
        echo '<div id="wpdba_options" class="wpdba-tab-content">';
        // Check if the DB_Tables class file has been included and instantiate it
        if (file_exists(WPDBA_PLUGIN_PATH . "includes/db-options.php")) {
            require_once WPDBA_PLUGIN_PATH . "includes/db-options.php";
            $test = new All_Options();
        }

        $current_page = isset($_GET["paged"]) ? absint($_GET["paged"]) : 1;
        $items_per_page = 20;

        // Get total options count
        $total_count = $test->get_total_options_count();
        $total_pages = ceil($total_count / $items_per_page);

        $opts = $test->get_all_opts($current_page, $items_per_page);

        echo '<div class="wpdba-bulk-actions">
    <button type="button" id="bulk-delete" class="button button-primary">Delete</button>
    <button type="button" id="bulk-enable-autoload" class="button">Enable Autoload</button>
    <button type="button" id="bulk-disable-autoload" class="button">Disable Autoload</button>
</div>';

        echo '<table class="striped  wpdba-options-table">
            <thead>
                <tr>
                    <th> <input type="checkbox" id="select-all" onclick="toggleSelectAll(this)"></th> 
                    <th> <a>' .
            esc_html__("Option Name", "wpdbanalyze") .
            '</a></th>
                    <th><a>' .
            esc_html__("Value", "wpdbanalyze") .
            '</a></th>
                    <th><a>' .
            esc_html__("Size (MB)", "wpdbanalyze") .
            '</a></th>
                    <th><a>' .
            esc_html__("Creator", "wpdbanalyze") .
            '</a></th>
                    <th><a>' .
            esc_html__("Autoload", "wpdbanalyze") .
            '</a></th>
                </tr>
            </thead>
            <tbody>';

        // Loop through each table and display its info
        foreach ($opts as $opt) {
            $creator = esc_html($test->get_option_creator($opt->option_name));

            echo "<tr>";
            // echo '<td> <input type="checkbox" class="wpdba-bulk-checkbox"> </td>';
            echo '<td> <input type="checkbox" class="wpdba-bulk-checkbox" value="' .
                esc_attr($opt->option_id) .
                '"> </td>';
            echo "<td>" . esc_html($opt->option_name) . "</td>";
            // echo '<td>' . esc_html($opt->option_value) . '</td>';
            echo "<td>" . esc_html("VALUE") . "</td>";
            echo "<td>" . esc_html($opt->option_size_mb) . "</td>";
            echo "<td>" . esc_html($creator) . "</td>";
            echo "<td>" . esc_html($opt->autoload) . "</td>";

            echo "</tr>";
        }

        echo '</tbody>
        </table>';
        echo '<div class="pagination">';

        // Previous button
        if ($current_page > 1) {
            echo '<a class="prev-page button" href="' .
                esc_url(add_query_arg("paged", $current_page - 1)) .
                '">' .
                esc_html__("Previous", "wpdbanalyze") .
                "</a>";
        }

        // Determine the range of pages to display
        $range = 1; // Number of pages to show on either side of the current page
        $start_page = max(1, $current_page - $range);
        $end_page = min($total_pages, $current_page + $range);

        // First page link
        if ($start_page > 1) {
            echo '<a class="button" href="' .
                esc_url(add_query_arg("paged", 1)) .
                '">1</a>';
            if ($start_page >= 2) {
                echo '<span class="dots">...</span>';
            }
        }

        // Pages in range
        for ($i = $start_page; $i <= $end_page; $i++) {
            $class =
                $i === $current_page ? "current-page button-primary" : "button";
            echo '<a class="' .
                esc_html($class) .
                '" href="' .
                esc_url(add_query_arg("paged", $i)) .
                '">' .
                esc_html($i) .
                "</a>";
        }

        // Last page link
        if ($end_page < $total_pages) {
            if ($end_page < $total_pages - 1) {
                echo '<span class="dots">...</span>';
            }
            echo '<a class="button" href="' .
                esc_url(add_query_arg("paged", $total_pages)) .
                '">' .
                esc_html($total_pages) .
                "</a>";
        }

        // Next button
        if ($current_page < $total_pages) {
            echo '<a class="next-page button" href="' .
                esc_url(add_query_arg("paged", $current_page + 1)) .
                '">' .
                esc_html__("Next", "wpdbanalyze") .
                "</a>";
        }

        echo "</div>";

        echo "</div>";
    } elseif ($current_tab === "woocommerce") {
        echo '<div id="wpdba_woocommerce" class="wpdba-tab-content">';
        echo esc_html__("Coming soon", "wpdbanalyze");
        echo "</div>";
    }

    echo "</div>";
    echo "</div>";

    echo '<div id="optimization" class="wpdba-tab-content" style="display:none;">' .
        esc_html__("Optimization", "wpdbanalyze") .
        "</div>";
    echo '<div id="tables" class="wpdba-tab-content" style="display:none;">' .
        esc_html__("Tables Content", "wpdbanalyze") .
        "</div>";
    echo '<div id="options" class="wpdba-tab-content" style="display:none;">' .
        esc_html__("Options Content", "wpdbanalyze") .
        "</div>";
    echo '<div id="woocommerce" class="wpdba-tab-content" style="display:none;">' .
        esc_html__("WooCommerce Content", "wpdbanalyze") .
        "</div>";
}

function wpdbanalyze_enqueue_admin_styles()
{
    wp_enqueue_style(
        "wpdbanalyze-admin-styles",
        WPDBA_PLUGIN_URL . "assets/css/admin-styles.css",
        [],
        filemtime(plugin_dir_path(__FILE__) . "assets/css/admin-styles.css")
    );
}

add_action("admin_enqueue_scripts", "wpdbanalyze_enqueue_admin_styles");

add_action("admin_enqueue_scripts", function () {
    wp_enqueue_script(
        "wpdba-admin-scripts",
        WPDBA_PLUGIN_URL . "assets/js/admin-scripts.js",
        ["jquery"],
        "1.0",
        true
    );

    // Localize script to add `ajaxurl`
    wp_localize_script("wpdba-admin-scripts", "wpdba_ajax", [
        "ajaxurl" => admin_url("admin-ajax.php"),
        "nonce" => wp_create_nonce("wpdba_nonce"), // Add security nonce
    ]);
});

/* Handle the Delete, add/remove autoload functions  */

add_action("wp_ajax_wpdba_bulk_delete", function () {
    // Verify nonce
    if (
        !isset($_POST["nonce"]) ||
        !wp_verify_nonce($_POST["nonce"], "wpdba_nonce")
    ) {
        wp_send_json_error(["message" => "Invalid nonce."]);
    }

// Parse the IDs from the request

$ids = isset($_POST["ids"])
    ? json_decode(stripslashes($_POST["ids"]), true)
    : [];

if (empty($ids)) {
    wp_send_json_error(["message" => "No options selected."]);
}

global $wpdb;

// Sanitize the IDs to make sure they are integers
$ids = array_map('intval', $ids);

// Generate placeholders for the query (e.g. "%d, %d, %d" for each ID)
$ids_placeholder = implode(",", array_fill(0, count($ids), "%d"));

// Prepare and execute the query directly
$result = $wpdb->query(
    $wpdb->prepare(
        "DELETE FROM {$wpdb->options} WHERE option_id IN ($ids_placeholder)",
        ...$ids  // Spread the array of IDs as separate arguments
    )
);

if ($result !== false) {
    wp_send_json_success();
} else {
    wp_send_json_error(["message" => "Failed to delete options."]);
}

});

add_action("wp_ajax_wpdba_bulk_autoload", function () {
    // Verify nonce
    if (
        !isset($_POST["nonce"]) ||
        !wp_verify_nonce($_POST["nonce"], "wpdba_nonce")
    ) {
        wp_send_json_error(["message" => "Invalid nonce."]);
    }

    // Parse IDs and autoload action
    $ids = isset($_POST["ids"])
        ? json_decode(stripslashes($_POST["ids"]), true)
        : [];
    $autoload = isset($_POST["autoload"])
        ? sanitize_text_field($_POST["autoload"])
        : "";

    if (empty($ids) || !in_array($autoload, ["enable", "disable"], true)) {
        wp_send_json_error(["message" => "Invalid input data."]);
    }

    global $wpdb;

    // Generate placeholders for the IN clause
    $ids_placeholder = implode(",", array_fill(0, count($ids), "%d"));
    $autoload_value = $autoload === "enable" ? "yes" : "no";

    // Prepare the SQL query directly using prepare() within the query itself
    $query = $wpdb->query($wpdb->prepare(
        "UPDATE {$wpdb->options} SET autoload = %s WHERE option_id IN ($ids_placeholder)",
        $autoload_value,
        ...$ids // Spread operator to pass each ID separately
    ));

    // Check the result and respond accordingly
    if ($query !== false) {
        wp_send_json_success();
    } else {
        wp_send_json_error(["message" => "Failed to update autoload values."]);
    }
});


