<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb;
$brew = $_POST['brew'];
$tab = (!empty($_POST['tab']) ? urlencode($_POST['tab']) : 'brew_info' );
switch($tab) {
	case 'brew_info' :
		if( !empty( $_POST['edit'] ) ) {
			if( $this->brew->is_locked($brew) !== 1 ) { // Not locked by another user
				if( ( current_user_can( 'brew') && $this->privileges->is_collaborator($brew) ) || current_user_can('edit_brews') ) {
					$table_name = $wpdb->prefix . 'brewing_panel';

					$result = $wpdb->get_row($wpdb->prepare(" SELECT * FROM $table_name WHERE serial = %d",$brew),ARRAY_A);

					$result['fermentables'] = unserialize( $result['fermentables'] );  
					$result['other'] 	= unserialize( $result['other'] ); 
					$result 		= stripslashes_deep( $result );
					$form = 'existing';
				} else break;
			} else break;
		}
		else {
			$form = 'new';
		}
				$fermentable_list = $wpdb->get_results(" SELECT id,name,category FROM ".$wpdb->prefix."brewing_fermentables ORDER BY name ASC",ARRAY_A);
				$hops_list 	  = $wpdb->get_results(" SELECT id,name FROM ".$wpdb->prefix."brewing_hops ORDER BY name ASC",ARRAY_A);
				$measures_list    = $wpdb->get_results(" SELECT id,name,type,unit FROM ".$wpdb->prefix."brewing_measures",ARRAY_A);
				$type_list 	  = $wpdb->get_results(" SELECT type FROM ".$wpdb->prefix."brewing_types",ARRAY_A);

				$options 	  = get_option('hbp_settings_general');

				$form_path = "forms/".$form."/".htmlentities( ( !empty( $_POST['form_name'] ) ? $_POST['form_name'] : ( !empty( $result['type'] ) ? $result['type'] : "Beer" ) ) )."-form.php";
				if( validate_file( $form_path ) === 0) include plugin_dir_path( __FILE__ ).$form_path;
				else print "Bad file path.";
	break;
	case 'permissions' :
		if( !empty( $_POST['edit'] ) && ( current_user_can( 'brew') && $this->privileges->is_owner($brew) ) || current_user_can('edit_brews') ) {
			$this->rendering->hbp_permissions_page_render($brew);
		}
	break;
}
?>
