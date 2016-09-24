<?php
/**
 * @link              https://github.com/adrian-ortega
 * @since             1.0.0
 * @package           AODWpPluginBoilerPlate
 *
 * @wordpress-plugin
 * Plugin Name:       AOD WP Plugin Boilerplate
 * Plugin URI:        https://github.com/adrian-ortega
 * Description:       A Wordpress Plugin boilerplate
 * Version:           2016.2
 * Author:            Adrian Ortega
 * Author URI:        https://github.com/adrian-ortega
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       AODWpPluginBoilerplate
 * Domain Path:       /languages
 */

// Don't allow this file to be accessed directly.
if( !defined( 'WPINC' ) ) {
    die();
}

require_once __DIR__ . '/vendor/autoload.php';

// Instantiate it first, to start the container and all
// other dependencies. Methods from here on out can be called
// statically
$AODPlugin = AOD\Plugin::getInstance(__FILE__);

// Set the plugin name and version
$AODPlugin->init('AOD WP Plugin Boilerplate', '2016.2');

$AODPlugin->with('admin_scripts', function(\AOD\Container $c) {
    $assets = new \AOD\Assets($c);
    $assets->isAdmin();
    $assets->addScript('main', 'js/main.js', ['jquery']);
    $assets->addStyle('styles', 'css/styles.css');

    return $assets;
});

// Start the plugin
$AODPlugin->run();