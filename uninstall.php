<?php
/**
 * 
 * This file runs when the plugin in uninstalled (deleted).
 * This will not run when the plugin is deactivated.
 * Ideally you will add all your clean-up scripts here
 * that will clean-up unused meta, options, etc. in the database.
 *
 */
// If plugin is not being uninstalled, exit (do nothing)
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
/*
 * Deactivate the Batch Lookup page
 */
    		$the_page_id = get_option( 'hackzach_brewing_panel_page_id' );
    		if( $the_page_id ) {
    		    wp_delete_post( $the_page_id ); // this will trash, not delete
    		}
		delete_option( 'hackzach_db_version' );
    		delete_option("hackzach_brewing_panel_page_id");