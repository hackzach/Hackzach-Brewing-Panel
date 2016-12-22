<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class Hackzach_Brewing_Panel_Rendering {
	/**
	 * The single instance of Hackzach_Brewing_Panel_Rendering.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;
	/**
	 * The main plugin object.
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */
	public $parent = null;
	/**
	 * Prefix for plugin settings.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $base = '';

	public function __construct ( $parent ) {
		$this->parent = $parent;
		$this->base = 'hbp_';
	}

	/**
	 * Brew Forms Tab Rendering
	 * @return void
	 */
	public function hbp_brew_forms_tabs( $current = 'brew_info', $serial = NULL) {
			if( $this->parent->privileges->is_owner($serial) ) {
				$tabs = array( 'brew_info' => 'Brew Info', 'permissions' => 'Permissions' );
			} else {
				$tabs = array( 'brew_info' => 'Brew Info');
			}
			echo '<div id="icon-themes" class="icon32"><br></div>';
			echo '<h2 class="nav-tab-wrapper">';
			foreach( $tabs as $tab => $name ){
				$class = ( $tab == $current ) ? ' nav-tab-active' : '';
				echo "<a id='$tab' class='nav-tab$class' href='?page=".$_GET['page']."&brew=".$_GET['brew']."&tab=$tab'>$name</a>";
			}
			echo '</h2>';
	}

	/**
	 * Brew List Tab Rendering
	 * @return void
	 */
	public function hbp_brews_list_tabs( $current = 'my_brews' ) {
			if( current_user_can('edit_brews') ) {
				$tabs = array( 'my_brews' => 'My Brews', 'my_collaborations' => 'Collaborations', 'all_brews' => 'All Brews' );
			} else {
				$tabs = array( 'my_brews' => 'My Brews', 'my_collaborations' => 'Collaborations' );
			}
			echo '<div id="icon-themes" class="icon32"><br></div>';
			echo '<h2 class="nav-tab-wrapper">';
			if('recent' === $_REQUEST['filter']) {
				echo "<a id='recent' class='nav-tab nav-tab-active' href='?page=".$_GET['page']."&tab=recent'>Recent</a>";
			}
			foreach( $tabs as $tab => $name ){
				$class = ( $tab == $current ) ? ' nav-tab-active' : '';
				echo "<a id='$tab' class='nav-tab$class' href='?page=".$_GET['page']."&tab=$tab'>$name</a>";
			}
			echo '</h2>';
	}

	/**
	 * Ingredient List Tab Rendering
	 * @return void
	 */
	public function hbp_ingredients_list_tabs( $current = 'fermentables' ) {
			$tabs = array( 'fermentables' => 'Fermentables', 'hops' => 'Hops' );

			echo '<div id="icon-themes" class="icon32"><br></div>';
			echo '<h2 class="nav-tab-wrapper">';
			foreach( $tabs as $tab => $name ){
				$class = ( $tab == $current ) ? ' nav-tab-active' : '';
					echo "<a id='$tab' class='nav-tab$class' href='?page=".$_GET['page']."&filter=$tab'>$name</a>";
			}
			echo '</h2>';
	}

	public function hbp_permissions_page_render( $serial = NULL ) {
		if($this->parent->privileges->is_owner($serial) ) {
			global $wpdb;
			$table_brew_collaborators 	= $wpdb->prefix.'brew_collaborators';
			$table_brewing_panel 		= $wpdb->prefix.'brewing_panel';

			$privacy = $wpdb->get_var($wpdb->prepare(" SELECT private FROM $table_brewing_panel WHERE serial = '%d'", $serial) );
	?>
			<div id="privacy_holder">
				<div id="private_brew">
					<h4>Privacy</h4>
					<p style="width:20%">
						<input id="privacy" type="checkbox" name="privacy" value="<?php print $privacy ?>" <?php print ($privacy ? "checked" : "" ) ?>>
						<label for="privacy">Hide Brew from Public?</label><br>
						<small>
							This website may or may not <a href="<?php print get_permalink().'/'.$this->parent->hbp_get_page_name().'/'; ?>">
							list the brews publicly</a> on a page. Check this if you do not want your brew to be listed there.
						</small>
					</p>
				</div>
			</div>
	<?php
			$brewers = array_merge( 
				get_users( array('role' => 'Administrator',
						 'fields' => 'ID') ), 
				get_users( array('role' => 'Brewmaster',
						 'fields' => 'ID') ),
				get_users( array('role' => 'Brewer',
						 'fields' => 'ID') )
			);
			$collaborators = $wpdb->get_results($wpdb->prepare( " SELECT * FROM $table_brew_collaborators WHERE serial = '%d'",$serial),ARRAY_A);
	?>
			<script>
			jQuery(document).ready(function() {
				var collaborators = [ <?php
				for($i = 0; $i<count($collaborators); $i++ ) {
					print "'".$collaborators[$i]['collaborator']."'";
					if( (count($collaborators)-1) > $i ) {
						print ", ";
					}
				}
						?> ];
				var brewers = [ <?php 
							if( !empty($brewers) ) {
								for($i = 0; $i<count($brewers); $i++ ) {
									print "{ label : '".$this->parent->functions->get_display_name($brewers[$i])."', value : '".$brewers[$i]."' }";
									if( (count($brewers)-1) > $i ) {
										print ", ";
									}
								}
							}
			 			?> ];
            			jQuery('#current_brewers').tagit({
                						autocomplete: { source : brewers },
								afterTagAdded: function(event, ui) {
									if( !ui.duringInitialization && collaborators.indexOf( jQuery('#current_brewers').tagit('tagLabel', ui.tag) ) == -1) { // Not in original Array
        									jQuery('#add_brewers').append('<input type="hidden" name="collaborators[add][]" value="'+ jQuery('#current_brewers').tagit('tagLabel', ui.tag) + '" id="add_'+ jQuery('#current_brewers').tagit('tagLabel', ui.tag) + '">\n');
									} else {
										jQuery('#add_'+ jQuery('#current_brewers').tagit('tagLabel', ui.tag) + '').remove();
										jQuery('#remove_'+ jQuery('#current_brewers').tagit('tagLabel', ui.tag) + '').remove();
									}
   								},
								afterTagRemoved: function(event, ui) {
									if( collaborators.indexOf( jQuery('#current_brewers').tagit('tagLabel', ui.tag) ) >= 0 ) { // In original array
        									jQuery('#remove_brewers').append('<input type="hidden" name="collaborators[remove][]" value="'+ jQuery('#current_brewers').tagit('tagLabel', ui.tag) + '" id="remove_'+ jQuery('#current_brewers').tagit('tagLabel', ui.tag) + '">\n');
									} else {
										jQuery('#add_'+ jQuery('#current_brewers').tagit('tagLabel', ui.tag) + '').remove();
										jQuery('#remove_'+ jQuery('#current_brewers').tagit('tagLabel', ui.tag) + '').remove();
									}
   								},
								placeholderText: 'Type a user name.',
								removeConfirmation: true		
            			});
			});
			</script>
			<div id="collaborators_holder">
				<div id="collaborators" class="collaborators">
					<h4>Collaborators</h4>
					<ul id="current_brewers" class="taggable">
	<?php
			if( !empty($collaborators) ) {
				foreach($collaborators as $collaborator) {
	?>
						<li id="<?php print $collaborator['collaborator'] ?>">
					<?php print $this->parent->functions->get_display_name($collaborator['collaborator']); ?>
						</li>
	<?php
				}
			}
	?>
					</ul>
					<div id="remove_brewers"></div>
					<div id="add_brewers"></div>
				</div>
			</div>
	<?php

		}
		else {
	?>
			<center><h4>You do not have permission to do this.</h4></center>
	<?php	}
	}

	public function batch_lookup_sorting_url($id, $title, $post_parent, $search, $sort, $order) {
		$search = (!empty($search) ? urlencode($search)."/" : "all/");
		print '<a class="sort" sort="'.$id.'" order="'.((strcasecmp($order,'ASC') === 0 && strcasecmp($sort,$id) === 0) ? 'desc' : 'asc').'" href="'.get_permalink( $post_parent ).$search.$id.'/'.((strcasecmp($order,'ASC') === 0 && strcasecmp($sort,$id) === 0) ? 'desc/' : '').'" style="color:#444">
			<b>'.$title.'</b></a>'.($sort == $id ? '<span id="sorting_icon">'.((strcasecmp($order,'ASC') === 0) ? '&uarr;' : '&darr;').'</span>' : '');
	}

	public function hbp_brew_list_tables_render(){
		$list_type = 'brew';
		$generateTable = new Hackzach_Brewing_Panel_List_Table($this->parent,$list_type);
    	?>
		<div id="hackzach_container" class="wrap">
			<div id="icon-users" class="icon32"><br/></div>
				<?php $this->hbp_brews_list_tabs( ( !empty($_REQUEST['filter']) ? urlencode($_REQUEST['filter']) : 'my_brews' ) ); ?>

        		<div id="ajax_response" class="ajax-response"></div>
				<?php $generateTable->prepare_items(); ?>
			<form id="brew_list" action="<?php echo $_SERVER['PHP_SELF']."?page=".$_GET['page'] ?>" method="POST">
				<?php $generateTable->search_box('Search '.ucfirst($list_type).'s',$list_type, 'Search'); ?>
				<?php $generateTable->display() ?>
			</form>
        		<div id="delete-confirm" class="modal-ui">
  				<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>The brew(s) will be permanently deleted and cannot be recovered.</p>
			</div>
		</div>
	<?php
	}

	public function hbp_ingredients_list_tables_render(){
		$list_type = 'ingredient';
		$generateTable = new Hackzach_Brewing_Panel_List_Table($this->parent,$list_type);
    	?>
		<div id="hackzach_container" class="wrap">
			<div id="icon-users" class="icon32"><br/></div>
				<?php $this->hbp_ingredients_list_tabs( ( !empty($_REQUEST['filter']) ? $_REQUEST['filter'] : 'fermentables' ) ); ?>

        		<div id="ajax_response" class="ajax-response"></div>
				<?php $generateTable->prepare_items(); ?>
			<form id="ingredient_list" action="<?php echo $_SERVER['PHP_SELF']."?page=".$_GET['page'] ?>" method="POST">
				<?php $generateTable->search_box('Search '.ucfirst($list_type).'s',$list_type, 'Search'); ?>
				<?php $generateTable->display() ?>
			</form>
        		<div id="delete-confirm" class="modal-ui">
  				<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>The ingredient(s) will be permanently deleted and cannot be recovered.</p>
			</div>
		</div>
	<?php
	}

	/**
	 * Main Hackzach_Brewing_Panel_Rendering Instance
	 *
	 * Ensures only one instance of Hackzach_Brewing_Panel_Rendering is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Hackzach_Brewing_Panel()
	 * @return Main Hackzach_Brewing_Panel_Rendering instance
	 */
	public static function instance ( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance()
	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'No Cloning!' ), $this->parent->_version );
	} // End __clone()
	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'No Unserializing!' ), $this->parent->_version );
	} // End __wakeup()
}