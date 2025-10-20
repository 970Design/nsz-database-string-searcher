=== 970 Design Database String Searcher ===
Contributors: 970design
Tags: database, search, export, csv, tools, postmeta
Requires at least: 5.0
Tested up to: 6.8.4
Stable tag: 1.1.0
Requires PHP: 7.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Search WordPress postmeta for specific strings and export results as CSV files.

== Description ==

The Database String Searcher plugin provides a simple yet powerful tool for searching through your WordPress database's postmeta table and exporting the results.

= Features =
* Search across all postmeta fields (meta_id, post_id, meta_key, meta_value)
* Export results to CSV format
* Excludes post revisions from search results
* Clean, user-friendly interface
* Detailed search statistics
* UTF-8 compatible CSV export

= Use Cases =
* Debug database issues
* Find specific metadata values
* Export metadata for analysis
* Data migration preparation

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/nsz-db-string-searcher` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Access the tool via Tools -> DB Search Export in the WordPress admin menu

== Frequently Asked Questions ==

= What database tables does this search? =

The plugin searches the wp_postmeta table, specifically looking in meta_id, post_id, meta_key, and meta_value fields.

= Can I search for multiple terms at once? =

Currently, the plugin supports searching for one term at a time.

= Are the exports safe for sensitive data? =

The plugin includes basic security measures, but you should always review exported data for sensitive information before sharing.

== Screenshots ==

1. Main search interface

== Changelog ==

= 1.1.0 =
* Added automatic update checker
* Improved CSV export formatting
* Enhanced error handling

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.1.0 =
This version adds automatic updates and improves CSV export functionality.

== Privacy Policy ==

This plugin does not collect any personal data. It only searches through existing database content and exports it as requested by administrators.