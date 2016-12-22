<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class Hackzach_Brewing_Panel_Privileges {
	/**
	 * The single instance of Hackzach_Brewing_Panel_Privileges.
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
	 * Check if a user is the owner of a batch
	 * @return boolean
	 */
	public function is_owner($serial, $user_id = NULL) {
		if( !empty($serial) ) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'brew_owners';

			$result = $wpdb->get_var($wpdb->prepare(" SELECT owner FROM $table_name WHERE serial = %d ", $serial) );
			if( !empty($result) ) {
				$user_id = ( !empty($user_id) ? $user_id : get_current_user_id() );
				return ( strcmp($result,$user_id ) === 0 ? true : false );
			} else return false;
		} else return false;
	}

	/**
	 * Make user the owner of an existing batch
	 * @return boolean
	 */
	public function make_owner_existing_brew($serial, $user_id = NULL) {
		if( !empty($serial) ) {
			global $wpdb,$current_user;
			get_currentuserinfo();

			$user_id = ( !empty($user_id) ? $user_id : $current_user->ID );
			if( $this->is_owner($serial, $user_id) ) return true; // Already Owner

			$table_name = $wpdb->prefix . 'brew_owners';
			$result = $wpdb->get_var($wpdb->prepare(" SELECT owner FROM $table_name WHERE serial = '%d' ", $serial) );
			if( empty($result) || current_user_can('edit_brews') ) {
				//Insert row if not exists or update row query (See ON DUPLICATE KEY)
				$result = $wpdb->query($wpdb->prepare(" INSERT INTO $table_name(serial,owner) VALUES('%d','%d') ON DUPLICATE KEY UPDATE owner = '%d'", $serial, $owner, $owner) );
				return  ( $result !== false ? true : false );
			} else return false;
		} else return false;
	}
	/**
	 * Make user the owner of a new batch
	 * @return boolean
	 */
	public function make_owner_new_brew($serial, $user_id = NULL) {
		if( !empty($serial) ) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'brew_owners';
			$result = $wpdb->get_var($wpdb->prepare(" SELECT owner FROM $table_name WHERE serial = '%d' ", $serial) );
			if( empty($result) ) {
				global $current_user;
				get_currentuserinfo();

				$user_id = ( !empty($user_id) ? $user_id : $current_user->ID );
				$result = $wpdb->insert($table_name, array(	'serial'	=> 	$serial,
										'owner'		=>	$user_id
										),	
							   			array(
											'%d',
										  	'%d'
										)
								);
				return  ( $result !== false ? true : false );
			} else return false;
		} else return false;
	}

	/**
	 * Check if a user is a collaborator for a batch
	 * @return boolean
	 */
	public function is_collaborator($serial, $user_id = NULL) {
		if( !empty($serial) ) {
			$user_id = ( !empty($user_id) ? $user_id : get_current_user_id() );
			if( $this->is_owner($serial, $user_id) ) return true;

			global $wpdb;
			$table_name = $wpdb->prefix . 'brew_collaborators';

			$result = $wpdb->get_var($wpdb->prepare(" SELECT COUNT(`collaborator`) FROM $table_name WHERE serial = %d AND collaborator = '%d' ", $serial, $user_id) );

			return ( $result > 0 ? true : false );
		} else return false;
	}

	/**
	 * Add a collaborator for a batch
	 * @return boolean(Permission)
	 */
	public function add_collaborator($serial, $user_id = NULL) {
		if( !empty($serial) ) {
			$user_id = ( !empty($user_id) ? $user_id : get_current_user_id() );
			if( $this->is_owner($serial, $user_id) || current_user_can('edit_brews') ){
				global $wpdb;
				$table_name = $wpdb->prefix . 'brew_collaborators';

				$result = $wpdb->get_var($wpdb->prepare(" SELECT COUNT(`collaborator`) FROM $table_name WHERE serial = '%d' AND collaborator = '%d' ", $serial, $user_id) );
				if( $result < 1 ) {
					// Add collaborator. One to Many relationship (EAV Model)
					$wpdb->insert($table_name, array( 'serial' 	=> $serial,
									'collaborator' 	=> $user_id),
								   array( '%d',
									  '%d' ) );
				} else return true; // Already collaborator
			} else return false;
		} else return false;
	}
	/**
	 * Remove a collaborator for a batch
	 * @return boolean(Permission)
	 */
	public function remove_collaborator($serial, $user_id = NULL) {
		if( !empty($serial) ) {
			$user_id = ( !empty($user_id) && ($this->is_owner($serial) || current_user_can('edit_brews') ) ? $user_id : get_current_user_id() );
			if( $this->is_owner($serial) || current_user_can('edit_brews') ){
				global $wpdb;
				$table_name = $wpdb->prefix . 'brew_collaborators';

				$result = $wpdb->get_var($wpdb->prepare(" SELECT COUNT(`collaborator`) FROM $table_name WHERE serial = '%d' AND collaborator = '%d' ", $serial, $user_id) );
				if( $result > 0 ) {
					// Remove collaborator. One to Many relationship (EAV Model)
					$wpdb->delete($table_name, array( 'serial' 	=> $serial,
									'collaborator' 	=> $user_id),
								   array( '%d',
									  '%d' ) );
				} else return true; // Not a collaborator
			} else return false;
		} else return false;
	}

	/**
	 * Remove all permissions for a batch(Used for delete brew)
	 * @return boolean(Permission)
	 */
	public function cleanup_permissions($serial) {
		if( !empty($serial) ) {
			if( $this->is_owner($serial) || current_user_can('delete_brews') ){
				global $wpdb;
				$table_collaborators 	= $wpdb->prefix . 'brew_collaborators';
				$table_owners		= $wpdb->prefix . 'brew_owners';

				$results = $wpdb->get_results($wpdb->prepare(" SELECT collaborator FROM $table_collaborators WHERE serial = '%d' ", $serial), ARRAY_A );
				if( $results > 0 ) {
					// Remove collaborators. One to Many relationship (EAV Model)
					$wpdb->delete($table_collaborators, array( 'serial' 	=> $serial ),
								   array( '%d') );
				} 

				$result = $wpdb->get_var($wpdb->prepare(" SELECT owner FROM $table_owners WHERE serial = '%d' ", $serial) );
				if( $result > 0 ) {
					// Remove owner.
					$wpdb->delete($table_owners, array( 'serial' 	=> $serial ),
								   array( '%d') );
				} 
			} else return false;
		} else return false;
	}
	/**
	 * Add Brewer roles
	 * @return boolean (permission)
	 */
	public function add_brew_roles() {
		if( current_user_can( 'promote_users' ) ) {
				remove_role('brewer');
			$result = add_role( 'brewer', __( 'Brewer', 'hackzach-brewing-panel' ), array(
								'read' => true,
								'brew' => true,
				));
				remove_role('brewmaster');
			$result = add_role( 'brewmaster', __( 'Brewmaster', 'hackzach-brewing-panel' ), array(
								'read' => true,
								'brew' => true,
								'edit_brews' => true,
								'delete_brews' => true,
				));
			return true;
		} else return false;
	}

	/**
	 * Remove Brewer roles
	 * @return boolean (permission)
	 */
	public function remove_brew_roles() {
		if( current_user_can( 'promote_users' ) ) {
			remove_role('brewer');
			remove_role('brewmaster');
			return true;
		} else return false;
	}
	/**
	 * Grant Administrator Brewer Capabilities
	 * return boolean (permission)
	 */
	public function grant_administrator_capabilities() {
		if( current_user_can( 'promote_users' ) ) {
			$administrator 	= array('brew', 'edit_brews', 'delete_brews');

			$role = get_role('administrator');

			foreach( $administrator as $cap ) {
				$role->remove_cap( $cap );
        			$role->add_cap( $cap );
			}
			return true;
		} else return false;
	}

	/**
	 * Revoke Administrator Brewer Capabilities
	 * return boolean (permission)
	 */
	public function revoke_administrator_capabilities() {
		if( current_user_can( 'promote_users' ) ) {
			$administrator 	= array('brew', 'edit_brews', 'delete_brews');

			$role = get_role('administrator');

			foreach( $administrator as $cap ) {
				$role->remove_cap( $cap );
			}
			return true;
		} else return false;
	}
	/**
	 * Grant User Brewing Capabilities
	 * return boolean (no user specified, or no permission)
	 */
	public function grant_user_capabilities( $user_id = null, $user_name = null, $role = 'brewer') {
		if( ( !empty($user_id) || !empty($user_name) ) && current_user_can( 'promote_users' ) ) {
			$brewmaster 	= array('brew', 'edit_brews', 'delete_brews');
			$brewer 	= array('brew');
			$capabilities 	= array( 'brewer' => $brewer, 'brewmaster' => $brewmaster );

			$user = new WP_User( $user_id, $user_name );
			foreach( $brewmaster as $cap ) {
				$user->remove_cap( $cap ); // Clear all the privileges we have for highest rank
			}
			foreach( $capabilities[$role] as $cap ) {
        			$user->add_cap( $cap ); // Add our new privileges now
			} return true;
		} else return false;
	}

	/**
	 * Revoke User Brewing Capabilities
	 * return boolean (no user specified, or no permission)
	 */
	public function revoke_user_capabilities( $user_id = null, $user_name = null, $role = 'brewer') {
		if( ( !empty($user_id) || !empty($user_name) ) && current_user_can( 'promote_users' ) ) {
			$brewmaster 	= array('brew', 'edit_brews', 'delete_brews');
			$brewer 	= array('brew');
			$capabilities 	= array( 'brewer' => $brewer, 'brewmaster' => $brewmaster );

			$user = new WP_User( $user_id, $user_name );

			foreach( $capabilities[$role] as $cap ) {
				$user->remove_cap( $cap );
			} return true;
		} else return false;
	}
	/**
	 * Grant User Brewing Roles
	 * return boolean (no user specified, or no permission)
	 */
	public function grant_user_role( $user_id = null, $user_name = null, $role = 'brewer') {
		if( ( !empty($user_id) || !empty($user_name) ) && current_user_can( 'promote_users' ) ) {
			$user = new WP_User( $user_id, $user_name );
        		$user->set_role( $role );
			return true;
		} else return false;
	}

	/**
	 * Revoke User brewing roles
	 * return boolean(no user specified, or no permission)
	 */
	public function revoke_user_role( $user_id = null, $user_name = null, $role = 'brewer') {
		if( ( !empty($user_id) || !empty($user_name) ) && current_user_can( 'promote_users' ) ) {
			$user = new WP_User( $user_id, $user_name );
        		$user->remove_role( $role );
			return true;
		} else return false;
	}

	/**
	 * Main Hackzach_Brewing_Panel_Privileges Instance
	 *
	 * Ensures only one instance of Hackzach_Brewing_Panel_Privileges is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Hackzach_Brewing_Panel()
	 * @return Main Hackzach_Brewing_Panel_Privileges instance
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