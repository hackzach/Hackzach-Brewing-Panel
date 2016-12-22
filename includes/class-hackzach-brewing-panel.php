<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Hackzach_Brewing_Panel {

	/**
	 * The single instance of Hackzach_Brewing_Panel.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Settings class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = null;

	/**
	 * Privileges class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $privileges = null;

	/**
	 * Functions class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $functions = null;

	/**
	 * Rendering class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $rendering = null;

	/**
	 * Brew class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $brew = null;

	/**
	 * SEO class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $seo = null;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct ( $file = '', $version = '1.0.1' ) {
		$this->_version = $version;
		$this->_token = 'hackzach_brewing_panel';

		// Load plugin environment variables
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		register_activation_hook( $this->file, array( $this, 'hackzach_install' ) );

		// Load JS & CSS on Admin Dashboard
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10 );

		// Load JS & CSS on WP frontend
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue_styles' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue_scripts' ), 10 );

		// Add Main menu items
		add_action( 'admin_menu' , array( $this, 'add_menu_items' ) );

		// Add Adminbar menu
		add_action( 'wp_before_admin_bar_render', array( $this , 'add_admin_menu_items' ), -1000 );
		add_action('wp_before_admin_bar_render', array( $this, 'add_admin_bar_view_brew' ), 100);

		// Register shortcodes for Batch Lookup Page
		add_shortcode( 'batch_lookup', array( $this, 'batch_lookup_page' ) );

		// hook Query Vars for page
		add_filter('query_vars', array( $this , 'add_query_vars' ) );

		// hook Rewrite Rules
		add_filter('rewrite_rules_array', array( $this , 'add_rewrite_rules' ) );

		/* AJAX Functons */
		// Include the Ajax on the front and back end
		add_action( 'wp_head', array( $this, 'add_ajax_library' ) );
		add_action( 'admin_head', array( $this, 'add_ajax_library' ) );

		// Add  AJAX Scripts to the Footer(Only for that page - see functions)
		add_action('admin_footer', array( $this, 'admin_ajax_scripts' ) );
		add_action('wp_footer', array( $this, 'frontend_ajax_scripts' ) );

		// Setup the event handler for search function(Both Users & Guests)
		add_action( 'wp_ajax_batch_lookup_ajax_response', array( $this, 'batch_lookup_ajax_response' ) );
		add_action( 'wp_ajax_nopriv_batch_lookup_ajax_response', array( $this, 'batch_lookup_ajax_response' ) );

		// Brew Form types AJAX handler
		add_action( 'wp_ajax_brew_forms_ajax_response', array( $this, 'brew_forms_ajax_response' ) );

		// Brew Locking AJAX handler
		add_action( 'wp_ajax_brew_lock_ajax_response', array( $this, 'brew_lock_ajax_response' ) );

		// WP_List_Table Brews AJAX handler
		add_action( 'wp_ajax_brew_list_ajax_response', array( $this, 'brew_list_ajax_response' ) );

		// WP_List_Table Ingredients AJAX handler
		add_action( 'wp_ajax_ingredient_list_ajax_response', array( $this, 'ingredient_list_ajax_response' ) );

		// Modify Nonce Failure string
		add_filter('gettext', array( $this, 'hbp_nonce_message' ) );

		// Handle localisation
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );

		// Determine domain address used for hidden service
		add_action( 'init', array( $this, 'get_domain_addr' ), 0);

	} // End __construct ()

	/**
	 * Add Top-level menu with subenu items
	 * @return void
	 */
	public function add_menu_items () {
		// Top-level Brews
		$main = add_menu_page( __( 'Hackzach Brewing Panel', 'hackzach-brewing-panel' ) , __( 'Brews', 'hackzach-brewing-panel' ) , 'brew' , $this->_token , array( $this->rendering, 'hbp_brew_list_tables_render' ) , plugins_url( 'hackzach-brewing-panel/assets/main_icon.png' ), 8 );
			add_submenu_page( $this->_token , __( 'Batch List', 'hackzach-brewing-panel' ) , __( 'All', 'hackzach-brewing-panel' ) , 'brew' , $this->_token , array( $this->rendering, 'hbp_brew_list_tables_render' ) );
			add_submenu_page( $this->_token , __( 'Add Batch', 'hackzach-brewing-panel' ) , __( 'Add', 'hackzach-brewing-panel' ) , 'brew' , $this->_token.'_add' , array( $this, 'add_brews_page' ) );
			add_submenu_page( $this->_token , __( 'Edit Batch', 'hackzach-brewing-panel' ) , __( 'Recent', 'hackzach-brewing-panel' ) , 'brew' , $this->_token . '_edit' , array( $this, 'edit_brews_page' ) );
			add_submenu_page( $this->_token , __( 'Ingredient Database', 'hackzach-brewing-panel' ) , __( 'Ingredients', 'hackzach-brewing-panel' ) , 'brew' , $this->_token . '_database' , array( $this->rendering, 'hbp_ingredients_list_tables_render' ) );	
	}

	/**
	 * Add to Admin Bar 'View Brew' Link furthest right.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function add_admin_bar_view_brew() {
		global $wp_admin_bar;
		if( ( $_GET['page'] == "hackzach_brewing_panel_edit" && !empty($_GET['brew']) ) || ( $_GET['page'] == "hackzach_brewing_panel_add" ) ) {
				$args = array(
					'id'     => 'brew_menu',
					'title'  => __( 'Brew Actions', 'hackzach-brewing-panel' ),
					'href'   => '#',
					'group'  => false,
				);
				$wp_admin_bar->add_node( $args );

			if( ( $_GET['page'] == "hackzach_brewing_panel_edit" && !empty($_GET['brew']) ) ) {
				$args = array(
					'id'     => 'view_brew',
					'parent' => 'brew_menu',
					'title'  => __( 'View Brew', 'hackzach-brewing-panel' ),
					'href'   => get_permalink().'/'.$this->hbp_get_page_name().'/'.$_GET['brew'].'/',
					'meta'  => array( 'target' => '_blank' ),
					'group'  => false,
				);
				$wp_admin_bar->add_node( $args );
			}

				$args = array(
					'id'     => 'save_brew',
					'parent' => 'brew_menu',
					'title'  => __( 'Save Brew (Ctrl+S)', 'hackzach-brewing-panel' ),
					'href'   => '#',
					'meta'  => array( 
							'class'   => 'save_brew',
							'onclick' => 'window.forms.saveBrew();'
							 ),
					'group'  => false,
				);
				$wp_admin_bar->add_node( $args );

				$args = array(
					'id'     => 'calculators',
					'parent' => 'brew_menu',
					'title'  => __( 'Calculators (Ctrl+X)', 'hackzach-brewing-panel' ),
					'href'   => '#TB_inline?width=600&height=550&inlineId=calculators',
					'meta'  => array( 
							'class'   => 'calculators',
							'onclick' => 'jQuery(this).addClass("thickbox");window.calculators.init();'
							 ),
					'group'  => false,
				);
				$wp_admin_bar->add_node( $args );
		}
	}

	/**
	 * Add Admin Menu Toolbar Links.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function add_admin_menu_items() {
		if( current_user_can( 'brew' ) ) {
			global $wp_admin_bar, $wpdb;

			$table_brewing_panel		= $wpdb->prefix . 'brewing_panel';
			$table_brew_owners	 	= $wpdb->prefix . 'brew_owners';
			$table_brew_collaborators	= $wpdb->prefix . 'brew_collaborators';

			$nodes = array();

			$main_menu = array(
				'id'     => 'main_page',
				'title'  => __( '<span class="ab-icon"><img src="'.plugins_url( 'hackzach-brewing-panel/assets/adminbar_icon.png' ).'" /></span>Brews', 'hackzach-brewing-panel' ),
				'href'   => admin_url( 'admin.php?page=hackzach_brewing_panel' ),
				'group'  => false,
			);
			$wp_admin_bar->add_node($main_menu);

			array_push($nodes, array(
				'id'     => 'brew_page',
				'parent' => 'main_page',
				'title'  => __( 'All', 'hackzach-brewing-panel' ),
				'href'   => admin_url( 'admin.php?page=hackzach_brewing_panel' ),
				'group'  => false,
			) );

			array_push($nodes, array(
				'id'     => 'add_page',
				'parent' => 'main_page',
				'title'  => __( 'Add', 'hackzach-brewing-panel' ),
				'href'   => admin_url( 'admin.php?page=hackzach_brewing_panel_add' ),
				'group'  => false,
			) );

			array_push($nodes, array(
				'id'     => 'bedit_page',
				'parent' => 'main_page',
				'title'  => __( 'Recent', 'hackzach-brewing-panel' ),
				'href'   => admin_url( 'admin.php?page=hackzach_brewing_panel_edit' ),
				'group'  => false,
			) );

			$result = $wpdb->get_results(" SELECT $table_brewing_panel.serial,name,type FROM $table_brewing_panel".
							" LEFT OUTER JOIN $table_brew_owners".
							" ON( $table_brew_owners.serial = $table_brewing_panel.serial )".
							" LEFT OUTER JOIN $table_brew_collaborators".
							" ON( $table_brew_collaborators.serial = $table_brewing_panel.serial )".
							" WHERE ( $table_brew_collaborators.collaborator = '".get_current_user_id()."'".
							" OR $table_brew_owners.owner = '".get_current_user_id()."')".
							" AND ( $table_brewing_panel.stage IS NULL".
							" OR $table_brewing_panel.stage != 'bottle' )".
							" ORDER BY $table_brewing_panel.date DESC LIMIT 0,5",ARRAY_A);
			foreach( $result as $brew ) {
				array_push($nodes, array(
					'id'     => $brew['serial'],
					'parent' => 'bedit_page',
					'title'  => __( $brew['serial'] . '-' . $brew['name'] . ' ' . $brew['type'], 'hackzach-brewing-panel' ),
					'href'   => admin_url( 'admin.php?page=hackzach_brewing_panel_edit&brew='.$brew['serial'] ),
					'group'  => false,
				) );
			}

			array_push($nodes, array(
				'id'     => 'database_page',
				'parent' => 'main_page',
				'title'  => __( 'Ingredients', 'hackzach-brewing-panel' ),
				'href'   => admin_url( 'admin.php?page=hackzach_brewing_panel_database' ),
				'meta'  => array( 'target' => '_blank' ),
				'group'  => false,
			) );
		
			for($i=0;$i<sizeof($nodes);$i++) {
				$wp_admin_bar->add_node($nodes[$i]);
			}
		}
	} // End hackzach_brewing_panel_admin_menu ()

	public function get_domain_addr () {
		$options = get_option('hbp_settings_general');
		
		if( $options['hbp_hidden_service'] ) { // Hidden Service(Tor) enabled

			$hostnames = explode(";", $options['hbp_hidden_service_hostnames'] );
			
			foreach($hostnames as $hostname) {
					/* 
						Not quite done here. Intent is to iterate over saved hostnames on onion relay to
						determine if the client is pulling data from one of them, and if so,
						show(or hide) certain pages or features. Still need to design a method to actually manage
						feature and page visibility based on plugins say-so. Then I can finish this part.
					*/
				// $domain_addr[] = get_site_url(); // Obviously not the most efficient solution...
			}
		}
	}
	/**
	 * Load admin CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function admin_enqueue_styles () {
		global $wp_scripts;

		wp_register_style( $this->_token . '-jquery-datetimepicker', esc_url( $this->assets_url ) . 'css/jquery.datetimepicker.css', array(), null );
		wp_enqueue_style( $this->_token . '-jquery-datetimepicker' );

		wp_register_style( $this->_token . '-jquery-tag-it', esc_url( $this->assets_url ) . 'css/jquery.tagit.css', array(), null );
		wp_enqueue_style( $this->_token . '-jquery-tag-it' );

		wp_register_style( $this->_token . '-tagit-ui-zendesk', esc_url( $this->assets_url ) . 'css/tagit.ui-zendesk.css', array(), null );
		wp_enqueue_style( $this->_token . '-tagit-ui-zendesk' );

 		wp_register_style( $this->_token . '-jquery-ui', esc_url('//ajax.googleapis.com/ajax/libs/jqueryui/'.$wp_scripts->registered['jquery-ui-core']->ver.'/themes/smoothness/jquery-ui.min.css'), array(), null);
		wp_enqueue_style( $this->_token . '-jquery-ui' );

		wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/hackzach-brewing-panel.admin.css', array( ), null );
		wp_enqueue_style( $this->_token . '-admin' );
	} // End enqueue_styles ()

	/**
	 * Load frontend CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function frontend_enqueue_styles () {
		wp_register_style( $this->_token . '-jquery-datetimepicker', esc_url( $this->assets_url ) . 'css/jquery.datetimepicker.css', array(), null );
		wp_enqueue_style( $this->_token . '-jquery-datetimepicker' );

		wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/hackzach-brewing-panel.frontend.css', array(), null );
		wp_enqueue_style( $this->_token . '-frontend' );
	} // End enqueue_styles ()

	/**
	 * Load admin Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_scripts () {
		wp_register_script( $this->_token . '-jquery-tag-it', esc_url( $this->assets_url ) . 'js/tag-it.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-ui-widget', 'jquery-ui-position', 'jquery-ui-autocomplete', 'jquery-effects-core', 'jquery-effects-blind', 'jquery-effects-highlight' ), null );
		wp_enqueue_script( $this->_token . '-jquery-tag-it' );

		wp_register_script( $this->_token . '-jquery-datetimepicker', esc_url( $this->assets_url ) . 'js/jquery.datetimepicker.js', array( 'jquery' ), null );
		wp_enqueue_script( $this->_token . '-jquery-datetimepicker' );
	} // End enqueue_scripts ()

	/**
	 * Load frontend Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function frontend_enqueue_scripts () {
		wp_register_script( $this->_token . '-jquery-datetimepicker', esc_url( $this->assets_url ) . 'js/jquery.datetimepicker.js', array( 'jquery' ), null );
		wp_enqueue_script( $this->_token . '-jquery-datetimepicker' );
	} // End enqueue_scripts ()

	/**
	 * Load plugin localisation
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'hackzach-brewing-panel', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation ()

	/**
	 * Load plugin textdomain
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = 'hackzach-brewing-panel';

	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain ()

	/**
	 * Add Query Variables
	 * @return query variables
	 */
	public function add_query_vars($vars) {
		$vars[] = "brew";
		$vars[] = "sort"; 
		$vars[] = "ord";
		$vars[] = "pagenum";

		return $vars;
	} // End add_query_vars ($vars)

	/**
	 * Add Rewrite Rules
	 * @return rewrite rules
	 */
	public function add_rewrite_rules($rules) {
		$addRules = array($this->hbp_get_page_name().'/([^/]*)/?([^/]*)/?([^/]*)/?$' => 'index.php?pagename='.$this->hbp_get_page_name().'&brew=$matches[1]&sort=$matches[2]&ord=$matches[3]');
		$rules = $addRules + $rules;
		return $rules;
	} // End add_rewrite_rules ($rules)

	/**
	 * Get Page Name for Batch Lookup page
	 * @return page_name
	 */
	public function hbp_get_page_name() {
		$pageID = get_option( 'hackzach_brewing_panel_page_id');
		$page = get_post($pageID);
		return $page->post_name;
	} // End hbp_get_page_name ()

	/**
	 * Modify WP_Nonce message for form authentication
	 * @return nonce failure message
	 */
	function hbp_nonce_message ($translation) {
  		if ($translation == 'Are you sure you want to do this?')
    			return 'Form session has expired, or nonce is mismatched. Refresh the form to continue.';
  		else
    			return $translation;
  	}

	/**
	 * Adds the WordPress Ajax Library to the frontend.
	 * @return void
	 */
	public function add_ajax_library() {
 
    		$html = '<script type="text/javascript">';
    		    $html .= 'var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '"';
    		$html .= '</script>';
 
    		echo $html;
 
	} // end add_ajax_library ()

	/**
	 * Brew Forms AJAX Response
	 * @return content
	 */
	public function brew_forms_ajax_response() {

		// This is AJAX response
 		include plugin_dir_path( __FILE__ )."/ajax/forms.php";

 		die(); // this is required to terminate immediately and return a proper response
	} // end brew_forms_ajax_response ()

	/**
	 * Batch Lookup Search Results
	 * @return content
	 */
	public function batch_lookup_ajax_response() {

		// This is AJAX response
 		include plugin_dir_path( __FILE__ )."/ajax/batch-lookup.php";

 		die(); // this is required to terminate immediately and return a proper response
	} // end batch_lookup_ajax_response ()

	/**
	 * Brew Locking AJAX Response
	 * @return content
	 */
	public function brew_lock_ajax_response() {

		// This is AJAX response
 		include plugin_dir_path( __FILE__ )."/ajax/brew_lock.php";

 		die(); // this is required to terminate immediately and return a proper response
	} // end brew_lock_ajax_response ()

	/**
	 * WP_List_Table Brews AJAX Response
	 * @return content
	 */
	public function brew_list_ajax_response() {
		$wp_list_table = new Hackzach_Brewing_Panel_List_Table($this,'brew');
		$wp_list_table->ajax_response();
	}

	/**
	 * WP_List_Table Ingredients AJAX Response
	 * @return content
	 */
	public function ingredient_list_ajax_response() {
		$wp_list_table = new Hackzach_Brewing_Panel_List_Table($this,'ingredient');
		$wp_list_table->ajax_response();
	}

	/**
	 * Load Batch Lookup page content
	 * @return page content
	 */
	public function batch_lookup_page ( ) {
		ob_start(); 

		include plugin_dir_path( __FILE__ )."/pages/batch-lookup-page.php";

		return ob_get_clean();

	} // End batch_lookup_page ( )
				
	/**
	 * Load New Brew page content
	 * @return void
	 */
	public function add_brews_page ( $attributes ) {

		include plugin_dir_path( __FILE__ )."/pages/add-brew-page.php";

	}

	/**
	 * Load Edit Brew page content
	 * @return void
	 */
	public function edit_brews_page ( $attributes ) {

		include plugin_dir_path( __FILE__ )."/pages/edit-brews-page.php";

	}

	/**
	 * Add AJAX Scripts to footer of page
	 * @return content|false
	 */
	public function admin_ajax_scripts() {
		$pagenow = $_REQUEST['page'];
		if('hackzach_brewing_panel' === $pagenow ||
			'hackzach_brewing_panel_database' === $pagenow ) {
				if('hackzach_brewing_panel' === $pagenow) {
					$list_type = 'brew';
				} else {
					$list_type = 'ingredient';
				}
				include plugin_dir_path( __FILE__ )."ajax/list-tables.js";
		}
		else if( 'hackzach_brewing_panel_edit' === $pagenow ||
				'hackzach_brewing_panel_add' === $pagenow) {

			/** 	Needed for forms.js loadBrewDetailForm( form_type ) to set current ABV
					See if additions are available and if so, get latest new gravity
					and use it as starting gravity in formula. It is expected to use theoretical
					starting gravity in additions, may later have it sum this gravity, but would need
					all gravities to do this.
			**/
			global $wpdb;
			$table_brewing_panel 		= $wpdb->prefix . 'brewing_panel';
			$table_brew_owners 		= $wpdb->prefix . 'brew_owners';
			$table_brew_collaborators 	= $wpdb->prefix . 'brew_collaborators';

			$table_fermentable 		= $wpdb->prefix . 'brewing_fermentables';
			$table_measures 		= $wpdb->prefix . 'brewing_measures';
			$table_hops 			= $wpdb->prefix . 'brewing_hops';

			$fermentable_list = $wpdb->get_results(" SELECT id,name,category FROM $table_fermentable ORDER BY name ASC",ARRAY_A);
			$measures_list 	 = $wpdb->get_results(" SELECT id,name,type,unit FROM $table_measures",ARRAY_A);
			$hops_list 	 = $wpdb->get_results(" SELECT id,name FROM $table_hops ORDER BY name ASC",ARRAY_A);

			$brew = $_GET['brew'];
			$tab = (!empty($_REQUEST['tab']) ? urlencode($_REQUEST['tab']) : 'brew_info' );

			$options = get_option('hbp_settings_general');

			$result = $wpdb->get_row($wpdb->prepare(" SELECT other,og FROM $table_brewing_panel WHERE serial = %d",$brew),ARRAY_A);
			$result['other'] = stripslashes_deep(unserialize( $result['other'] ) ); 

			include plugin_dir_path( __FILE__ )."ajax/forms.js";
		} else return false;
	}

	public function frontend_ajax_scripts() {
		global $post,$wp_query;
		if( get_option( 'hackzach_brewing_panel_page_id') == $post->ID ) { // Batch Lookup Page
				$sort = 'serial'; 
				$order = 'ASC'; // Default
				$current_page = isset( $wp_query->query_vars['pagenum'] ) ? absint( $wp_query->query_vars['pagenum'] ) : 1;
				$sortallowed = array(		  		//Sort name => Table_name,
							'serial' => 'serial',
							'bottle' => 'bottle',
							'date' => 'date',
							'type' => 'type',
							'name' => 'name',
							'stage' => 'stage',
							'og' => 'og',
							'fg' => 'actual_fg',
						);
				$orderallowed = array(
							'asc' => 'ASC',
							'desc' => 'DESC',
						);

				if ( isset( $wp_query->query_vars['sort'] ) && isset( $sortallowed[$wp_query->query_vars['sort']] ) ) {
					$sort = $sortallowed[$wp_query->query_vars['sort']];
				}

				if ( isset( $wp_query->query_vars['ord'] ) && isset( $orderallowed[$wp_query->query_vars['ord']] ) ) {
					$order = $orderallowed[$wp_query->query_vars['ord']];
				} 
			include plugin_dir_path( __FILE__ )."ajax/batch-lookup.js";
		} else return false;
	}

	/**
	 * Main Hackzach_Brewing_Panel Instance
	 *
	 * Ensures only one instance of Hackzach_Brewing_Panel is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Hackzach_Brewing_Panel()
	 * @return Main Hackzach_Brewing_Panel instance
	 */
	public static function instance ( $file = '', $version = '1.0.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}
		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'No Clones!' ), $this->_version );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'No Unserializing!' ), $this->_version );
	} // End __wakeup ()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function hackzach_install () {
		global $wpdb,$hackzach_db_version;

		$this->_log_version_number();
      		$this->_name      			= 'batch-lookup';
     		$this->page_title 			= 'Batch List & Serial Search';
      		$this->page_name  			= $this->_name;
      		$this->page_id    			= '138';

		$this->table_name_brewing_panel 	= $wpdb->prefix . 'brewing_panel';
		$this->table_name_brewing_fermentables  = $wpdb->prefix . 'brewing_fermentables';
		$this->table_name_brewing_hops  	= $wpdb->prefix . 'brewing_hops';
		$this->table_name_brewing_measures	= $wpdb->prefix . 'brewing_measures';
		$this->table_name_brewing_types 	= $wpdb->prefix . 'brewing_types';
		/*
		 * We'll set the default character set and collation for this table.
		 * If we don't do this, some characters could end up being converted 
		 * to just ?'s when saved in our table.
		 */
		$charset_collate = '';

		if ( ! empty( $wpdb->charset ) ) {
		  $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
		}

		if ( ! empty( $wpdb->collate ) ) {
		  $charset_collate .= " COLLATE {$wpdb->collate}";
		}

		$sql = file_get_contents( plugin_dir_path( __FILE__ )."/functions/tables/brews.sql").
			file_get_contents( plugin_dir_path( __FILE__ )."/functions/tables/collaborators.sql").
			file_get_contents( plugin_dir_path( __FILE__ )."/functions/tables/fermentables.sql").
			file_get_contents( plugin_dir_path( __FILE__ )."/functions/tables/hops.sql").
			file_get_contents( plugin_dir_path( __FILE__ )."/functions/tables/lock.sql").
			file_get_contents( plugin_dir_path( __FILE__ )."/functions/tables/measures.sql").
			file_get_contents( plugin_dir_path( __FILE__ )."/functions/tables/notes.sql").
			file_get_contents( plugin_dir_path( __FILE__ )."/functions/tables/owners.sql").
			file_get_contents( plugin_dir_path( __FILE__ )."/functions/tables/types.sql");

		$sql = str_replace('$this->table_name_brewing_panel', $this->table_name_brewing_panel, $sql);
		$sql = str_replace('$this->table_name_brewing_hops', $this->table_name_brewing_hops, $sql);
		$sql = str_replace('$this->table_name_brewing_fermentables', $this->table_name_brewing_fermentables, $sql);
		$sql = str_replace('$this->table_name_brew_lock', $this->table_name_brew_lock, $sql);

		$sql = str_replace('$this->table_name_brewing_measures', $this->table_name_brewing_measures, $sql);
		$sql = str_replace('$this->table_name_brew_notes', $this->table_name_brew_notes, $sql);
		$sql = str_replace('$this->table_name_brew_owners', $this->table_name_brew_owners, $sql);
		$sql = str_replace('$this->table_name_brew_collaborators', $this->table_name_brew_collaborators, $sql);
		$sql = str_replace('$this->table_name_brewing_types', $this->table_name_brewing_types, $sql);

		$sql = str_replace('$charset_collate', $charset_collate, $sql);

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( $sql );

		/*
    		// the menu entry...
    		delete_option("hackzach_brewing_panel_page_title");
    		add_option("hackzach_brewing_panel_page_title", $this->page_title, '', 'yes');
    		// the slug...
    		delete_option("hackzach_brewing_panel_page_name");
    		add_option("hackzach_brewing_panel_page_name", $this->page_name, '', 'yes');
    		// the id...
    		delete_option("hackzach_brewing_panel_page_id");
    		add_option("hackzach_brewing_panel_page_id", $this->page_id, '', 'yes');
		*/

    		$the_page = get_page_by_title( $this->page_title );

    		if ( ! $the_page ) {

        		// Create post object
        		$_p = array();
        		$_p['post_title'] = $this->page_title;
        		$_p['post_content'] = "[batch_lookup]";
        		$_p['post_status'] = 'publish';
        		$_p['post_type'] = 'page';
        		$_p['comment_status'] = 'closed';
        		$_p['ping_status'] = 'closed';
        		$_p['post_category'] = array(1); // the default 'Uncategorized'

        		// Insert the post into the database
        		$this->page_id = wp_insert_post( $_p );

    		}
    		else {
        		// the plugin may have been previously active and the page may just be trashed...

        		$this->page_id = $the_page->ID;

        		//make sure the page is not trashed...
       		 	$the_page->post_status = 'publish';
        		$this->page_id = wp_update_post($the_page);

   		 }
			$this->privileges->add_brew_roles();
			$this->privileges->grant_administrator_capabilities();

    		delete_option( 'hackzach_brewing_panel_page_id' );
    		add_option( 'hackzach_brewing_panel_page_id', $this->page_id ); 

		delete_option( 'hackzach_db_version' );
		add_option( 'hackzach_db_version', $hackzach_db_version );

		// Register default settings
		if ( get_option( 'hbp_serial_prefix' ) === false ) update_option( 'hbp_serial_prefix', 'HZ' );
		if ( get_option( 'hbp_date_format' ) === false ) update_option( 'hbp_date_format', 'g:ia \o\n l\, F jS Y' );
		if ( get_option( 'hbp_short_date' ) === false ) update_option( 'hbp_short_date', 'D. M. jS Y g:ia' );
	} // End install ()

	/**
	 * Log the plugin version number.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number () {
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number ()

}