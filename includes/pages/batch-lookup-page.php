<?php
if ( ! defined( 'ABSPATH' ) ) exit;
 ?>
	<div id="hackzach_container">
<?php 
global $wp_query,$wpdb, $post;
	$table_name = $wpdb->prefix . 'brewing_panel';

	$options = get_option('hbp_settings');

	$brew = urldecode($wp_query->query_vars['brew']);

	/*
		 If the brew is private and the requester is not a member of this brew's 
		permissions group, do not allow access to this brew
	*/
	$privacy = ( !$this->privileges->is_collaborator($brew) || !current_user_can('edit_brews') ? 'AND PRIVATE <> true' : '' );

	$total = $wpdb->get_var($wpdb->prepare( " SELECT COUNT(`serial`) FROM $table_name WHERE  serial = %d $privacy" , $brew ) );



	if( !empty( $wp_query->query_vars['brew'] ) && ( $wp_query->query_vars['brew'] != "all" ) && ($total == 1) ) {
			$fermentable_list 	= $wpdb->get_results(" SELECT id,name FROM ".$wpdb->prefix."brewing_fermentables",ARRAY_A);
			$measures_list 		= $wpdb->get_results(" SELECT id,name,unit FROM ".$wpdb->prefix."brewing_measures",ARRAY_A);
			$hops_list 		= $wpdb->get_results(" SELECT id,name FROM ".$wpdb->prefix."brewing_hops",ARRAY_A);

			$result = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE serial = %d" , $brew) , ARRAY_A);

			$result['fermentables'] = unserialize( $result['fermentables'] );  
 			$result['other'] = unserialize( $result['other'] ); 

			$result = stripslashes_deep( $result );

			$brew_path = "/single-brew-views/".$result['type']."-page.php";
			if( validate_file( $brew_path ) === 0) include plugin_dir_path( __FILE__ ).$brew_path;
			else print "Bad File Path.";
	} else {

	?>
		<div id="brew_search_container">
			<center>
				<input type="text" id="brew_search" onkeyup="batch.ajax()" placeholder="Type a serial, name etc." style="width:220px" <?php print (!empty($brew) && ($brew != "all") ? 'value="'.$brew.'"' : ''); ?>/><br />
			</center>
			<br />
		</div>
	</div>
	<div id="ajax_response" class="ajax-response"><center><img src="<?php print plugins_url( 'hackzach-brewing-panel/assets/ajax-loader.gif' ) ?>" title="Loading..." /></center></div>
<?php } ?>