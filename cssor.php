<?php
/**
 * Plugin Name: Cssor
 * Plugin URI: https://wordpress.org/plugins/cssor/
 * Description: Simple and light wordpress custom css editor with minify option. 
 * Version: 1.1
 * Author: Antonio Novak
 * Author URI: http://www.webpuls.eu
 * License: GPL2
 *
 * Cssor is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * Cssor is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Cssor. If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Define plugin version
if ( ! defined( 'CSSOR_PLUGIN' ) ) {
	define( 'CSSOR_PLUGIN', '1.1' );
}

/**
* Core plugin classs
*/
class Cssor {
	
	protected static $plugin_name = 'Cssor';
	protected static $version = '1.1';
	protected static $textdomain = 'cssor';

	protected $cssor_style;
	protected $cssor_minify;
	protected $cssor_file;
	protected $cssor_dependency;

	static function run() {

		register_activation_hook( __FILE__, 'Cssor::activate' );
		register_deactivation_hook( __FILE__, 'Cssor::deactivate' );

		Cssor::actions();

	}

	static function generate_css() {

		$cssor_style = esc_attr( get_option('cssor_style') );
		$cssor_minify = esc_attr( get_option('cssor_minify') );
		$cssor_method = esc_attr( get_option('cssor_method') );
		$cssor_dependency = esc_attr( get_option('cssor_dependency') );

		// Check if css output is minify
		$cssor_style = $cssor_minify == 'on' ? Cssor::minify_css( $cssor_style ) : $cssor_style;

		if ( $cssor_method == 'inline' ) {
			wp_add_inline_style( 'cssor_style', $cssor_style );
		} else {
			$upload_dir = wp_upload_dir();
			$cssor_basedir = $upload_dir['basedir'] . '/cssor/';
			$cssor_file = fopen($cssor_basedir . "cssor.css", "w");
			fwrite( $cssor_file, $cssor_style);
			fclose( $cssor_file );
			add_action( 'wp_enqueue_scripts', 'Cssor::public_enqueue', 15 );
		}
	}

	static function load_admin_page() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'cssor/admin-page.php';

	}

	static function actions() {
		add_action( 'admin_init', 'Cssor::register_settings' );
		add_action( 'admin_menu', 'Cssor::menu' );
		add_action( 'admin_enqueue_scripts', 'Cssor::admin_enqueue'	);
		add_action( 'plugins_loaded', 'Cssor::generate_css' );
	}

	static function menu() {
		$hook = add_menu_page(
		    'Cssor',
		    'Cssor',
		    'manage_options',
		    'cssor',
			'Cssor::load_admin_page',
		    plugin_dir_url( __FILE__ ).'icons/admin-nav.png'
		);
	}

	static function minify_css( $cssor_style ) {
		// Remove comments
		$cssor_minify = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $cssor_style);
		// Remove comments
		$cssor_minify = str_replace(': ', ':', $cssor_minify);				
		// Remove whitespace
		$cssor_minify = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $cssor_minify);

		return $cssor_minify;
	}

	static function register_settings() {

		register_setting( 'cssor', 'cssor_style' ); // Css
		register_setting( 'cssor', 'cssor_minify' );	// If is minify
		//register_setting( 'cssor', 'cssor_file' );	// Destination of file
		register_setting( 'cssor', 'cssor_method' );	// if is inline or file
		register_setting( 'cssor', 'cssor_dependency');	// Dependency

	}

	static function admin_enqueue() {

		wp_enqueue_script( 'ace', plugin_dir_url( __FILE__ ) . 'assets/ace-builds-master/src-noconflict/ace.js' );
		wp_enqueue_script( 'ace-ext-language', plugin_dir_url( __FILE__ ) . 'assets/ace-builds-master/src-noconflict/ext-language_tools.js' );
	    wp_enqueue_style( 'cssor_style', plugin_dir_url( __FILE__ ) . 'css/style.css' );
	
	}

	static function public_enqueue() {

		/*$cssor_dependency = preg_replace('/\s+/', '', $cssor_dependency);
		$cssor_dependency = explode( ',', $cssor_dependency );*/
		$cssor_dependency = array();
		//$cssor_dependency = get_option('cssor_dependency') ? array( get_option('cssor_dependency') ) : array();
		$upload_dir = wp_upload_dir();
		$cssor_baseurl = $upload_dir['baseurl'] . '/cssor/';
		wp_enqueue_style( 'cssor', $cssor_baseurl . 'cssor.css', $cssor_dependency, self::$version );
	}

	static function activate() {

		$upload_dir = wp_upload_dir();
		$cssor_basedir = $upload_dir['basedir'] . '/cssor/';
		
		// Create 'cssor' folder in uploads
		wp_mkdir_p( $cssor_basedir );

		if ( !file_exists( $cssor_basedir . 'cssor.css' ) ) {

			// Try to create css file
			$cssor_file = fopen($cssor_basedir . "cssor.css", "w");
			if ( $cssor_file ) {
				update_option( 'cssor_method', 'file' );
			} else {
				update_option( 'cssor_method', 'inline' );
			}

		} else {
			update_option( 'cssor_method', 'file' );
		}

	}

	static function deactivate() {
		delete_option( 'cssor_method' );
	}

}

Cssor::run();