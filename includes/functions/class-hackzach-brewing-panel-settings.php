<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class Hackzach_Brewing_Panel_Settings {
	/**
	 * The single instance of Hackzach_Brewing_Panel_Settings.
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
	/**
	 * Available settings for plugin.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = array();

	public function __construct ( $parent ) {
		$this->parent = $parent;
		$this->base = 'hbp_';

		add_action( 'admin_menu', array( $this , 'hbp_add_admin_menu' ) );
		add_action( 'admin_init', array( $this , 'hbp_settings_init' ) );
	}

	public function hbp_add_admin_menu(  ) { 

		add_options_page( 'Hackzach Brewing Panel', 'Hackzach Brewing Panel', 'manage_options', 'hackzach_brewing_panel_settings', array( $this , 'hbp_options_page' ) );

	}
	public function hbp_admin_tabs( $current = 'general' ) {
			$tabs = array( 'general' => 'General', 'fermentables' => 'Fermentables', 'hops' => 'Hops', 'advanced' => 'Advanced' );
			echo '<div id="icon-themes" class="icon32"><br></div>';
			echo '<h2 class="nav-tab-wrapper">';
			foreach( $tabs as $tab => $name ){
				$class = ( $tab == $current ) ? ' nav-tab-active' : '';
				echo "<a class='nav-tab$class' href='?page=".$_GET['page']."&tab=$tab'>$name</a>";
			}
			echo '</h2>';
	}


	public function hbp_settings_init(  ) { 
		$tab = ( isset ( $_GET['tab'] ) ? $_GET['tab'] : 'general' );

		switch( $tab ) {
			case 'general' :
				register_setting( 'hackzach-brewing-panel-general', 'hbp_settings_general' );

				add_settings_section(
					'hbp_settings_general', 
					__( 'Global Settings', 'hackzach-brewing-panel' ), 
					array( $this , 'hbp_settings_section_callback' ), 
					'hackzach-brewing-panel-general'
				);

				add_settings_field( 
					'hbp_serial_prefix', 
					__( 'Serial Prefix', 'hackzach-brewing-panel' ), 
					array( $this , 'hbp_serial_prefix_render' ), 
					'hackzach-brewing-panel-general', 
					'hbp_settings_general'
				);

				add_settings_field( 
					'hbp_date_format', 
					__( 'Date Format String', 'hackzach-brewing-panel' ), 
					array( $this , 'hbp_date_format_render' ), 
					'hackzach-brewing-panel-general', 
					'hbp_settings_general'
				);

				add_settings_field( 
					'hbp_short_date', 
					__( 'Short Date Format', 'hackzach-brewing-panel' ), 
					array( $this , 'hbp_short_date_render' ), 
					'hackzach-brewing-panel-general', 
					'hbp_settings_general'
				);

				add_settings_field( 
					'hbp_hidden_service_enabled', 
					__( 'Hidden Service Enabled?', 'hackzach-brewing-panel' ), 
					array( $this , 'hbp_hidden_service_enabled_render' ), 
					'hackzach-brewing-panel-general',  
					'hbp_settings_general'
				);

				add_settings_field( 
					'hbp_hidden_service_hostnames', 
					__( 'Hidden Service addresses(Separated by semicolon)', 'hackzach-brewing-panel' ), 
					array( $this , 'hbp_hidden_service_hostnames_render' ), 
					'hackzach-brewing-panel-general', 
					'hbp_settings_general'
				);
			break;
			case 'advanced' :

			break;
		}
	}


	public function hbp_serial_prefix_render(  ) { 

		$options = get_option( 'hbp_settings_general' );
		?>
		<input type='text' name='hbp_settings_general[hbp_serial_prefix]' value='<?php echo $options['hbp_serial_prefix']; ?>'>
		<?php

	}


	public function hbp_date_format_render(  ) { 

		$options = get_option( 'hbp_settings_general' );
		?>
		<input type='text' name='hbp_settings_general[hbp_date_format]' value='<?php echo $options['hbp_date_format']; ?>'><?php print date_format(date_create(), $options['hbp_date_format']); ?>
		<?php

	}


	public function hbp_short_date_render(  ) { 

		$options = get_option( 'hbp_settings_general' );
		?>
		<input type='text' name='hbp_settings_general[hbp_short_date]' value='<?php echo $options['hbp_short_date']; ?>'><?php print date_format(date_create(), $options['hbp_short_date']); ?>
		<?php

	}


	public function hbp_hidden_service_enabled_render(  ) { 

		$options = get_option( 'hbp_settings_general' );
		?>
		<input type='checkbox' name='hbp_settings_general[hbp_hidden_service]' <?php checked( $options['hbp_hidden_service'], 1 ); ?> value='1'>
		<?php

	}


	public function hbp_hidden_service_hostnames_render(  ) { 

		$options = get_option( 'hbp_settings_general' );
		?>
		<textarea cols='40' rows='5' name='hbp_settings_general[hbp_hidden_service_hostnames]'><?php echo $options['hbp_hidden_service_hostnames']; ?></textarea>
		<?php

	}


	public function hbp_settings_section_callback(  ) { 

		echo __( 'Choose the options pertaining to how the brewing software functions.', 'hackzach-brewing-panel' );

	}


	public function hbp_options_page(  ) { 
		if ( isset ( $_GET['tab'] ) ) {
			$this->hbp_admin_tabs($_GET['tab']); 
			$tab = $_GET['tab'];
		}
		else {
			$this->hbp_admin_tabs('general');
			$tab = 'general';
		}

		switch ( $tab ){ 
			case 'general' :
	?>
				<form action='options.php?tab=general' method='post'>
   					<table class='form-table'>
	<?php
				settings_fields( 'hackzach-brewing-panel-general' );
				do_settings_sections( 'hackzach-brewing-panel-general' );
				submit_button();
	?>
					</table>
				</form>
	<?php
			break;
			case 'fermentables' :
				if( ( $_SERVER['REQUEST_METHOD'] == 'POST' ) ) {

				}
	?>
				<form action='options-general.php?tab=fermentables' method='post'>
   					<table class='form-table'>
	<?php
	?>
					</table>
				</form>
	<?php
			break;
			case 'hops' :
				if( ( $_SERVER['REQUEST_METHOD'] == 'POST' ) ) {

				}
	?>
				<form action='options-general.php?tab=hops' method='post'>
   					<table class='form-table'>
	<?php
	?>
					</table>
				</form>
	<?php
			break;
			case 'advanced' :
	?>
				<form action='options.php?tab=advanced' method='post'>
   					<table class='form-table'>
	<?php
	?>
					</table>
				</form>
	<?php
			break;
		}
	}

	/**
	 * Main Hackzach_Brewing_Panel_Settings Instance
	 *
	 * Ensures only one instance of Hackzach_Brewing_Panel_Settings is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Hackzach_Brewing_Panel()
	 * @return Main Hackzach_Brewing_Panel_Settings instance
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