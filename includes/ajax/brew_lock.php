<?php
if ( ! defined( 'ABSPATH' ) ) exit;
	switch($_POST['request']) {
		case 'lock' :
			switch($this->brew->is_locked($_POST['serial'])) { 
				case -1 : // This user is in session already
					print "1";
				break;
				case 0:  // Not locked by any user
 					if( $this->brew->lock($_POST['serial']) ) {
						print "1"; // Successful Lock Request
					} else print "0"; // Could not lock brew
				break;

				case 1 : // Locked by another User
					print "-1";
				break;
			}
		break;
		case 'unlock' :
		if( $this->brew->is_locked($_POST['serial']) !== 1 ) {
			if( $this->brew->unlock($_POST['serial']) ) {
				print "1"; // Successful Unlock Request
			} else print "0"; // Unsuccessful Unlock
		} else print "-1"; // Not Locked
		break;
	}	
?>
