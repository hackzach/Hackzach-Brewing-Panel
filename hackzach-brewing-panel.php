<?php
/*
 * Plugin Name: Hackzach Brewing Panel
 * Version: 1.0.0
 * Plugin URI: http://www.hackzach.com/
 * Description: Brewing panel for batch tracking, stage notation and publishing.
 * Author: Zach Padove
 * Author URI: http://www.zacharyp.org/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: Hackzach_Brewing_Panel
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Zach Padove
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Load plugin class files
require_once( 'includes/class-hackzach-brewing-panel.php' );
require_once( 'includes/functions/class-hackzach-brewing-panel-brew.php' );
require_once( 'includes/functions/class-hackzach-brewing-panel-settings.php' );
require_once( 'includes/functions/class-hackzach-brewing-panel-privileges.php' );
require_once( 'includes/functions/class-hackzach-brewing-panel-functions.php' );
require_once( 'includes/functions/class-hackzach-brewing-panel-rendering.php' );
require_once( 'includes/functions/class-hackzach-brewing-panel-seo.php' );

require_once( 'includes/functions/class-hackzach-brewing-panel-list-table.php' );

// For is_plugin_active() to check for WP-SEO
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

global $hackzach_db_version;

$hackzach_db_version = '1.0.0';

/**
 * Returns the main instance of Hackzach_Brewing_Panel to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Hackzach_Brewing_Panel
 */
function Hackzach_Brewing_Panel () {

	$instance = Hackzach_Brewing_Panel::instance( __FILE__, '1.0.0' );
	if ( is_null( $instance->brew ) ) {
		$instance->brew = Hackzach_Brewing_Panel_Brew::instance( $instance );
	}
	if ( is_null( $instance->settings ) ) {
		$instance->settings = Hackzach_Brewing_Panel_Settings::instance( $instance );
	}
	if ( is_null( $instance->privileges ) ) {
		$instance->privileges = Hackzach_Brewing_Panel_Privileges::instance( $instance );
	}
	if ( is_null( $instance->functions ) ) {
		$instance->functions = Hackzach_Brewing_Panel_Functions::instance( $instance );
	}
	if ( is_null( $instance->rendering ) ) {
		$instance->rendering = Hackzach_Brewing_Panel_Rendering::instance( $instance );
	}
	if ( is_null( $instance->seo ) ) {
		if( function_exists( 'is_plugin_active' ) && is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) {
			$instance->seo = Hackzach_Brewing_Panel_SEO::instance( $instance );
		}
	}
	return $instance;
}

$hackzach = Hackzach_Brewing_Panel();