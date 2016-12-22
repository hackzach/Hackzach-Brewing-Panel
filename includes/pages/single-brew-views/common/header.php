<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="wrap" id="<?php print $this->_token; ?>_view">
		<div id="<?php print $this->_token; ?>_container">
			<div id="_<?php print $this->_token; ?>general_brew" class="brew-column">
				<?php print $options['hbp_serial_prefix']; ?>-<?php print $result['serial'] ?>-<?php print $result['bottle'] ?> <b><?php print $result['name'] ?> <?php print $result['type'] ?></b><br>
				<?php if(!empty( $result['other']['final_yield'] ) && !empty( $result['other']['final_abv'] ) ) { 
					print $result['other']['final_yield'];
					if( $this->functions->recursive_in_array( $result['other']['final_yield_units'] , $measures_list , true ) ) {
			 			$imatch = $this->functions->recursive_array_search( $result['other']['final_yield_units'] , $measures_list );
						print $measures_list[$imatch]['unit'];
					}
					print " at ".(2*$result['other']['final_abv'])." Proof (".$result['other']['final_abv']."% Alc. by Vol)<br>";
					} ?>
<br>