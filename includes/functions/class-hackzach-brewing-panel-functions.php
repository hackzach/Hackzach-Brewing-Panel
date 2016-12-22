<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class Hackzach_Brewing_Panel_Functions {
	/**
	 * The single instance of Hackzach_Brewing_Panel_Functions.
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
	 * Time Elapsed function
	 * @return Elapsed string
	 */
	public function time_elapsed_string($datetime, $full = false) {
		// http://stackoverflow.com/a/18602474
		// 

		$now = new DateTime;
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);

		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;

		$string = array(
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			} else {
				unset($string[$k]);
			}
		}

		if (!$full) $string = array_slice($string, 0, 1);
		return $string ? implode(', ', $string) . ' ago' : 'just now';
	}

	/**
	 * Get Display Name By ID
	 * @return Display Name or False
	 */
	public function get_display_name($user_id) {
		if (!$user = get_userdata($user_id))
			return false;
		return $user->data->display_name;
	}

	/**
	 * Recursive in_array() function
	 * @return boolean
	 */
	public function recursive_in_array($needle, $haystack, $alsokeys=false, $alsostrict=false) {
		//  (Adrian Foeder) http://php.net/manual/en/function.in-array.php#58560
        	if(!is_array($haystack)) return false;
        	if(in_array($needle, $haystack, $alsostrict) || ($alsokeys && in_array($needle, array_keys($haystack), $alsostrict) )) return true;
        	else {
        	    foreach($haystack as $element) {
        	        $ret = $this->recursive_in_array($needle, $element, $alsokeys, $alsostrict);
			if($ret) break;
           	    }
        	}
       
       	 	return $ret;
    	}

	/**
	 * Recursive array_search() function
	 * @return Key or false
	 */
	public function recursive_array_search($needle,$haystack) {
		// (Buddel) http://php.net/manual/en/function.array-search.php#91365
    		foreach($haystack as $key=>$value) {
       		 	$current_key=$key;
        		if($needle===$value || (is_array($value) && $this->recursive_array_search($needle,$value) !== false)) {
            			return $current_key;
       			 }
    		}
    		return false;
	}

	/**
	 * Multi-Dimensional array_key_exists() function
	 * @return boolean
	 */
	public function multi_array_key_exists( $key , $array , $strict=false ) {
		// (Tim) http://stackoverflow.com/a/19420866
    	// is in base array?
    	if ( array_key_exists($key, $array , $strict ) ) {
        	return true;
    	}

    		// check arrays contained in this array
    		foreach ( $array as $element ) {
        		if ( is_array( $element ) ) {
            			if ( $this->multi_array_key_exists( $key , $element , $strict ) ) {
                			return true;
           	 		}
        		}

    		}

    		return false;
	}

	/**
	 * Main Hackzach_Brewing_Panel_Functions Instance
	 *
	 * Ensures only one instance of Hackzach_Brewing_Panel_Functions is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Hackzach_Brewing_Panel()
	 * @return Main Hackzach_Brewing_Panel_Functions instance
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