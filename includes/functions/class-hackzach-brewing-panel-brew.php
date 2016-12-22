<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class Hackzach_Brewing_Panel_Brew {
	/**
	 * The single instance of Hackzach_Brewing_Panel_Brew.
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
	 * Insert/Update/Delete Stage Notes in Database
	 * @return void
	 */
	public function update_stage_notes($serial, $notes) {
		if( !empty( $notes ) ) {
			global $wpdb;
			$table_brewing_notes = $wpdb->prefix.'brewing_notes';
			foreach( $notes as $item ) {
				$result = $wpdb->get_row($wpdb->prepare(" SELECT user_id,notes FROM $table_brewing_notes WHERE id = '%d'", $item['id']), ARRAY_A );
				if( !empty($result) ) { 
					if( ( get_current_user_id() == $result['user_id'] ) || current_user_can('edit_brews') || $this->parent->privileges->is_owner($serial) ) {
						if( $item['delete'] === "true" ) {
							// Delete 
							$wpdb->delete($table_brewing_notes, array('id' => $item['id'] ) );
						} else {
							if( strcasecmp($item['notes'], $result['notes'] ) !== 0 ) {
								$wpdb->update($table_brewing_notes, array(
									'modify_id' 	=> get_current_user_id(),
									'modify_date' 	=> current_time( 'mysql' ),
									'notes' 	=> $item['notes'] ),
								array( 'id' => $item['id'] ) );
							}
						}
					}
				}
				else if( current_user_can('edit_brews') || $this->parent->privileges->is_collaborator($serial) ) {
					// Insert
					$wpdb->insert($table_brewing_notes, array( 
									'user_id' 	=> get_current_user_id(),
									'serial' 	=> $serial,
									'stage' 	=> $item['stage'],
									'date' 		=> current_time( 'mysql' ),
									'notes' 	=> $item['notes']
	 							) );
				}

			}
		}
	}

	/**
	 * Add new brew to Database
	 * @return brew_serial or false
	 */
	public function add_data(
			$bottle,
			$type,
			$name,
			$stage,
			$ferment_date,
			$distill_date,
			$condition_date,
			$aging_date,
			$bottle_date,
			$ferment_notes,
			$distill_notes,
			$condition_notes,
			$aging_notes,
			$bottle_notes,
			$og,
			$yeast,
			$fermentables,
			$expected_fg,
			$actual_fg,
			$other) {
		if( current_user_can('brew') ) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'brewing_panel';
			if( $wpdb->insert( 
				$table_name, 
				array(   
					'bottle' => $bottle,
					'date' => current_time( 'mysql' ), 
					'type' => $type,
					'name' => $name,
					'modify_id' => get_current_user_id(),
					'stage' => $stage,
						'ferment_date' => $ferment_date,
						'distill_date' => $distill_date,
						'condition_date' => $condition_date,
						'aging_date' => $aging_date,
						'bottle_date' => $bottle_date,
					'og' => $og,
					'yeast' => $yeast,
					'fermentables' => $fermentables,
						'expect_fg' => $expected_fg,
						'actual_fg' => $actual_fg,
					'other' => $other
				) 
			) !== false) {
				$this->update_stage_notes($serial, $ferment_notes);
				$this->update_stage_notes($serial, $distill_notes);
				$this->update_stage_notes($serial, $condition_notes);
				$this->update_stage_notes($serial, $aging_notes);
				$this->update_stage_notes($serial, $bottle_notes);
				return $wpdb->insert_id;
			} else return false;
		} else return false;
	}

	/**
	 * Edit brew already in Database
	 * @return success code
	 */
	public function update_data(
			$serial,
			$bottle,
			$type,
			$name,
			$stage,
			$ferment_date,
			$distill_date,
			$condition_date,
			$aging_date,
			$bottle_date,
			$ferment_notes,
			$distill_notes,
			$condition_notes,
			$aging_notes,
			$bottle_notes,
			$og,
			$yeast,
			$fermentables,
			$expected_fg,
			$actual_fg,
			$other) {
		if( $this->parent->privileges->is_collaborator($serial) || current_user_can('edit_brews') ) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'brewing_panel';

			if($wpdb->update( 
				$table_name, 
				array(   
					'bottle' => $bottle,
					'date' => current_time( 'mysql' ), 
					'modify_id' => get_current_user_id(),
					'type' => $type,
					'name' => $name,
					'stage' => $stage,
						'ferment_date' => $ferment_date,
						'distill_date' => $distill_date,
						'condition_date' => $condition_date,
						'aging_date' => $aging_date,
						'bottle_date' => $bottle_date,
				'og' => $og,
				'yeast' => $yeast,
				'fermentables' => $fermentables,
				'expect_fg' => $expected_fg,
				'actual_fg' => $actual_fg,
				'other' => $other
				),
                		array( 'serial' => $serial )
			) !== false) {
				$this->update_stage_notes($serial, $ferment_notes);
				$this->update_stage_notes($serial, $distill_notes);
				$this->update_stage_notes($serial, $condition_notes);
				$this->update_stage_notes($serial, $aging_notes);
				$this->update_stage_notes($serial, $bottle_notes);
 				return true;
			} else return false;
		} else return false;
	}

	/**
	 * Deletebrew from Database
	 * @return boolean
	 */
	public function delete_data($serial) {
		if( $this->parent->privileges->is_owner($serial) || current_user_can('delete_brews') ) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'brewing_panel';
				if( $wpdb->delete(
					$table_name,
					array( 'serial' => $serial )
				) !== false) {
						return $this->parent->privileges->cleanup_permissions($serial);
				}
				else return false;
		} else return false;
	}

	/**
	 * Is Brew Free for Editing
	 * @return int(-1=this user in session,0=unlocked,1=locked by another user)
	 */
	public function is_locked($serial) {
		global $wpdb;
		$table_brew_lock = $wpdb->prefix.'brew_lock';
		$result = $wpdb->get_row($wpdb->prepare(" SELECT brew_locked,user_id FROM $table_brew_lock WHERE serial = '%d'", $serial), ARRAY_A );
		if( !empty($result) && $result['brew_locked'] ) {
			if( get_current_user_id() == $result['user_id'] ) {
				return -1; // This user locked the brew
			} else return 1; // User cannot access because the brew is locked by another user
		} else return 0; // Not locked
	}

	/**
	 * Date Brew was locked
	 * @return Mysql Datetime
	 */
	public function lock_time($serial) {
		global $wpdb;
		$table_brew_lock = $wpdb->prefix.'brew_lock';
		$result = $wpdb->get_var($wpdb->prepare(" SELECT date FROM $table_brew_lock WHERE serial = '%d'", $serial) );
		if( !empty($result) ) {
			return $result;
		} else return '0000-00-00 00:00:00';
	}

	/**
	 * Which User Locked Brew
	 * @return user_id || false
	 */
	public function lock_id($serial) {
		global $wpdb;
		$table_brew_lock = $wpdb->prefix.'brew_lock';
		$result = $wpdb->get_var($wpdb->prepare(" SELECT user_id FROM $table_brew_lock WHERE serial = '%d'", $serial) );
		if( !empty($result) ) {
			return $result;
		} else return false;
	}

	/**
	 * Lock Brew for Editing
	 * @return success code
	 */
	public function lock($serial) {
		global $wpdb;
		$table_brew_lock = $wpdb->prefix.'brew_lock';
		$result = $wpdb->get_row($wpdb->prepare(" SELECT brew_locked,user_id FROM $table_brew_lock WHERE serial = '%d'", $serial), ARRAY_A );
		if( !empty($result) ) { // Lock exists for this brew
			if( $result['brew_locked'] ) { // Brew is already locked by a user
				if( get_current_user_id() == $result['user_id'] ) return true; // Locked by this user
				else return false; // Another user is accessing the brew
			} else { // Brew is unlocked
				if( $wpdb->update( $table_brew_lock, array( 	'brew_locked' 	=> true,
										'user_id' 	=> get_current_user_id(),
										'date' 		=> current_time( 'mysql' )
									), 
									array( 	'serial' => $serial ) 
							) !== false ) return true;
						else return false;
			}
		} else { // No locking exists, create one for this user
			if( $wpdb->insert($table_brew_lock, array( 	'serial' 	=> $serial,
									'brew_locked' 	=> true,
									'user_id' 	=> get_current_user_id(),
									'date' 		=> current_time( 'mysql' )
								) ) !== false ) return true;
						else return false;
		}
	}

	/**
	 * Unlock Brew for Editing
	 * @return success code
	 */
	public function unlock($serial) {
		global $wpdb;
		$table_brew_lock = $wpdb->prefix.'brew_lock';
		$result = $wpdb->get_var($wpdb->prepare(" SELECT brew_locked FROM $table_brew_lock WHERE serial = '%d' AND user_id = '%d'", $serial, get_current_user_id()) );
		if( !empty($result) ) { // Lock exists for this brew under current user
			if( $wpdb->update( $table_brew_lock, array( 	'brew_locked' 	=> false,
									'user_id' 	=> NULL,
									'date' 		=> '0000-00-00 00:00:00'
									), 
									array( 	'serial' 	=> $serial,
										'user_id' 	=> get_current_user_id() ) 
							) !== false ) return true;
						else return false;
		} else return true;
	}

	/**
	 * Main Hackzach_Brewing_Panel_Brew Instance
	 *
	 * Ensures only one instance of Hackzach_Brewing_Panel_Brew is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Hackzach_Brewing_Panel()
	 * @return Main Hackzach_Brewing_Panel_Brew instance
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