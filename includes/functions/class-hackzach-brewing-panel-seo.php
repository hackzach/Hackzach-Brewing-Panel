<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class Hackzach_Brewing_Panel_SEO {
	/**
	 * The single instance of Hackzach_Brewing_Panel_SEO.
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

		// Hook Yoast SEO functions on Batch view if enabled
		if( function_exists( 'is_plugin_active' ) && is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) {
			add_filter('wpseo_metadesc' , array( $this , 'hbp_rewrite_metadesc' ) , 100 );
			add_filter('wpseo_title' , array( $this , 'hbp_rewrite_title' ) , 100 );
			add_filter('wpseo_canonical' , array( $this , 'hbp_rewrite_canonical' ) , 100 );
		}
	}

	/**
	 * Rewrite meta description for batch lookup
	 * @return meta description
	 */
	public function hbp_rewrite_metadesc( $description ) {
		global $pagename, $wp_query,$wpdb;
		if($pagename == $this->parent->hbp_get_page_name() && ( !empty( $wp_query->query_vars['brew'] ) && is_numeric( $wp_query->query_vars['brew'] ) ) ) {
			$result = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}brewing_panel WHERE serial = %d" , $wp_query->query_vars['brew']) , ARRAY_A);
			$result['other'] = unserialize( $result['other'] );
			if( !empty( $result['other']['meta_desc'] ) ) {
				return htmlentities( stripslashes( $result['other']['meta_desc'] ) );
			}
		} 
			return $description;
	}

	/**
	 * Rewrite meta title for batch lookup
	 * @return meta title
	 */
	public function hbp_rewrite_title( $title ) {
		global $pagename, $wp_query,$wpdb;
		if($pagename == $this->parent->hbp_get_page_name() && ( !empty( $wp_query->query_vars['brew'] ) && is_numeric( $wp_query->query_vars['brew'] ) ) ) {
			$result = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}brewing_panel WHERE serial = %d" , $wp_query->query_vars['brew']) , ARRAY_A);
			return $result['name']." ".$result['type']." - ".get_bloginfo('name');
		} else {
			return $title;
		}
	}

	/**
	 * Rewrite canonical url for batch lookup
	 * @return canonical url
	 */
	public function hbp_rewrite_canonical( $url ) {
		global $pagename, $wp_query;

		if($pagename == $this->parent->hbp_get_page_name() && ( !empty( $wp_query->query_vars['brew'] ) && is_numeric( $wp_query->query_vars['brew'] ) ) ) {
			return $url.$wp_query->query_vars['brew']."/";
		} else {
			return $url;
		}
	}

	/**
	 * Main Hackzach_Brewing_Panel_SEO Instance
	 *
	 * Ensures only one instance of Hackzach_Brewing_Panel_SEO is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Hackzach_Brewing_Panel()
	 * @return Main Hackzach_Brewing_Panel_SEO instance
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