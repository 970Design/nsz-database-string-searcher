<?php
/**
 * Plugin Name: 970 Design Database String Searcher
 * Description: Search wp_postmeta for a string and export results as CSV
 * Version:     1.0.1
 * Author:      970Design
 * Author URI:  https://970design.com/
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: nsz-db-string-searcher
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation. You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class DB_Search_Export {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_post_db_search_export', array($this, 'handle_search'));
        add_action('admin_post_db_download_csv', array($this, 'handle_download'));

	    // Add settings link on plugins page
	    add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_plugin_action_links'));
    }

    public function add_admin_menu() {
        add_management_page(
            'Database Search & Export',
            'DB Search Export',
            'manage_options',
            'db-search-export',
            array($this, 'admin_page')
        );
    }

    public function admin_page() {
        // Check if we have search results to display
        $search_string = isset($_GET['search_string']) ? sanitize_text_field($_GET['search_string']) : '';
        $results_count = isset($_GET['results_count']) ? intval($_GET['results_count']) : 0;
        $show_results = isset($_GET['show_results']) && $_GET['show_results'] === '1';

        ?>
        <div class="wrap">
            <h1>Database Search & Export</h1>
            <p>Search for a string in post metadata and export results as CSV.</p>

            <?php if ($show_results): ?>
                <div class="notice notice-success is-dismissible">
                    <p><strong>Search completed!</strong></p>
                </div>

                <div class="card" style="max-width: 800px; margin-bottom: 20px;">
                    <h2>Search Results Statistics</h2>
                    <table class="widefat" style="margin-top: 10px;">
                        <tbody>
                            <tr>
                                <td style="width: 200px;"><strong>Search Term:</strong></td>
                                <td><code><?php echo esc_html($search_string); ?></code></td>
                            </tr>
                            <tr class="alternate">
                                <td><strong>Results Found:</strong></td>

                                <?php if ($results_count > 0) : ?>
                                <td style="color: #2271b1;"><strong style="font-size: 18px;"><?php echo number_format($results_count); ?></strong> records</td>
                                <?php else: ?>
                                    <td style="color: #d63638;"><span class="dashicons dashicons-warning"></span> No results found for this search term.</td>
                                <?php endif; ?>

                            </tr>
                            <tr>
                                <td><strong>Search Date:</strong></td>
                                <td><?php echo date('F j, Y g:i:s A'); ?></td>
                            </tr>
                        </tbody>
                    </table>

                    <?php if ($results_count > 0): ?>
                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-top: 20px;">
                            <input type="hidden" name="action" value="db_download_csv">
                            <input type="hidden" name="search_string" value="<?php echo esc_attr($search_string); ?>">
                            <?php wp_nonce_field('db_download_csv_action', 'db_download_csv_nonce'); ?>

                            <button type="submit" class="button button-primary button-hero" style="display: inline-flex; align-items: center; gap: 0.25rem;">
                                <span class="dashicons dashicons-download" style="margin-top: 3px;"></span>
                                Download CSV File (<?php echo number_format($results_count); ?> records)
                            </button>
                        </form>
                    <?php endif; ?>
                </div>

                <hr>
            <?php endif; ?>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="db_search_export">
                <?php wp_nonce_field('db_search_export_action', 'db_search_export_nonce'); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="search_string">Search String</label>
                        </th>
                        <td>
                            <input type="text"
                                   id="search_string"
                                   name="search_string"
                                   class="regular-text"
                                   placeholder="e.g., sweet basil"
                                   value="<?php echo esc_attr($search_string); ?>"
                                   required>
                            <p class="description">Enter the text you want to search for in the database.</p>
                        </td>
                    </tr>
                </table>

                <?php submit_button($show_results ? 'Search Again' : 'Search Database', 'primary', 'submit'); ?>
            </form>

            <hr>

            <h2>What This Does</h2>
            <p>This tool searches the <code>wp_postmeta</code> table for your specified string in:</p>
            <ul>
                <li>meta_id</li>
                <li>post_id</li>
                <li>meta_key</li>
                <li>meta_value</li>
            </ul>
            <p>It excludes post revisions and exports all matching rows as a CSV file.</p>
        </div>
        <?php
    }

    public function handle_search() {
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized access');
        }

        // Verify nonce
        if (!isset($_POST['db_search_export_nonce']) ||
            !wp_verify_nonce($_POST['db_search_export_nonce'], 'db_search_export_action')) {
            wp_die('Security check failed');
        }

        // Get search string
        $search_string = isset($_POST['search_string']) ? sanitize_text_field($_POST['search_string']) : '';

        if (empty($search_string)) {
            wp_die('Please provide a search string');
        }

        // Perform the search
        global $wpdb;

        $search_term = '%' . $wpdb->esc_like($search_string) . '%';

        $query = $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->postmeta} 
            INNER JOIN {$wpdb->posts} ON {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
            WHERE {$wpdb->posts}.post_type != 'revision'
            AND (
                CONVERT(`meta_id` USING utf8) LIKE %s 
                OR CONVERT(`post_id` USING utf8) LIKE %s 
                OR CONVERT(`meta_key` USING utf8) LIKE %s 
                OR CONVERT(`meta_value` USING utf8) LIKE %s
            )",
            $search_term,
            $search_term,
            $search_term,
            $search_term
        );

        $results_count = $wpdb->get_var($query);

        // Redirect back to the page with results
        $redirect_url = add_query_arg(
            array(
                'page' => 'db-search-export',
                'search_string' => urlencode($search_string),
                'results_count' => $results_count,
                'show_results' => '1'
            ),
            admin_url('tools.php')
        );

        wp_redirect($redirect_url);
        exit;
    }

    public function handle_download() {
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized access');
        }

        // Verify nonce
        if (!isset($_POST['db_download_csv_nonce']) ||
            !wp_verify_nonce($_POST['db_download_csv_nonce'], 'db_download_csv_action')) {
            wp_die('Security check failed');
        }

        // Get search string
        $search_string = isset($_POST['search_string']) ? sanitize_text_field($_POST['search_string']) : '';

        if (empty($search_string)) {
            wp_die('Please provide a search string');
        }

        // Clean output buffer and suppress all output before CSV
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Disable error display for clean CSV output
        @ini_set('display_errors', '0');

        // Perform the search
        global $wpdb;

        $search_term = '%' . $wpdb->esc_like($search_string) . '%';

        $query = $wpdb->prepare(
            "SELECT wp_postmeta.* FROM {$wpdb->postmeta} 
            INNER JOIN {$wpdb->posts} ON {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
            WHERE {$wpdb->posts}.post_type != 'revision'
            AND (
                CONVERT(`meta_id` USING utf8) LIKE %s 
                OR CONVERT(`post_id` USING utf8) LIKE %s 
                OR CONVERT(`meta_key` USING utf8) LIKE %s 
                OR CONVERT(`meta_value` USING utf8) LIKE %s
            )",
            $search_term,
            $search_term,
            $search_term,
            $search_term
        );

        $results = $wpdb->get_results($query, ARRAY_A);

        // Generate CSV
        $filename = 'db-search-' . sanitize_file_name($search_string) . '-' . date('Y-m-d-His') . '.csv';

        // Set headers before any output
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Pragma: no-cache');
        header('Expires: 0');

        // Disable time limit for large exports
        @set_time_limit(0);

        $output = fopen('php://output', 'w');

        // Add BOM for Excel UTF-8 support
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        if (!empty($results)) {
            // Write header row with exact column names
            fputcsv($output, array('meta_id', 'post_id', 'meta_key', 'meta_value'), ',', '"');

            // Write data rows with proper formatting
            foreach ($results as $row) {
                // Ensure column order matches header
                $csv_row = array(
                    $row['meta_id'],
                    $row['post_id'],
                    $row['meta_key'],
                    $row['meta_value']
                );

                // fputcsv automatically handles quote escaping (doubles quotes inside quoted fields)
                fputcsv($output, $csv_row, ',', '"');
            }
        } else {
            // No results found
            fputcsv($output, array('No results found for: ' . $search_string), ',', '"');
        }

        fclose($output);
        exit;
    }

	/**
	 * Add settings link on plugin page
	 *
	 * @param array $links Existing plugin action links
	 * @return array Modified plugin action links
	 */
	public function add_plugin_action_links($links) {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url(admin_url('tools.php?page=db-search-export')),
			esc_html__('Search', 'nsz-db-string-searcher')
		);

		array_unshift($links, $settings_link);

		return $links;
	}
}

// Initialize the plugin
new DB_Search_Export();
