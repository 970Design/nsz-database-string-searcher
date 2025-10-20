# 970 Design Database String Searcher

Search WordPress postmeta for specific strings and export results as CSV files.

## Description

The Database String Searcher plugin provides a simple yet powerful tool for searching through your WordPress database's postmeta table and exporting the results.

### Features
- Search across all postmeta fields (meta_id, post_id, meta_key, meta_value)
- Export results to CSV format
- Excludes post revisions from search results
- Clean, user-friendly interface
- Detailed search statistics
- UTF-8 compatible CSV export

![Main search window](/assets/screenshot-1.png?raw=true "Main search window")

### Use Cases
- Debug database issues
- Find specific metadata values
- Export metadata for analysis
- Data migration preparation

## Requirements
- WordPress 6.0 or higher
- PHP 7.4 or higher

## Installation

1. Upload the plugin files to the `/wp-content/plugins/nsz-db-string-searcher` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Access the tool via Tools -> DB Search Export in the WordPress admin menu

## FAQ

**What database tables does this search?**  
The plugin searches the wp_postmeta table, specifically looking in meta_id, post_id, meta_key, and meta_value fields.

**Can I search for multiple terms at once?**  
Currently, the plugin supports searching for one term at a time.

**Are the exports safe for sensitive data?**  
The plugin includes basic security measures, but you should always review exported data for sensitive information before sharing.

## Changelog

### 1.1.0
- Added automatic update checker
- Improved CSV export formatting
- Enhanced error handling

### 1.0.0
- Initial release

## Privacy Policy

This plugin does not collect any personal data. It only searches through existing database content and exports it as requested by administrators.

## License

GPLv2 or later. See [http://www.gnu.org/licenses/gpl-2.0.html](http://www.gnu.org/licenses/gpl-2.0.html) for full license details.

## Credits

The development of this package is sponsored by [970 Design](https://970design.com), a creative agency based in Vail, Colorado.  If you need help with your headless WordPress project, please don't hesitate to [reach out](https://970design.com/reach-out/).