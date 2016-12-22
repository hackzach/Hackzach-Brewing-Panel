<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
			<div id="gravity_yeast" style="width:95%">
			<hr>
				Original Sp. Gravity: <?php print $result['og'] ?><br>
				Current FG&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php print $result['actual_fg'] ?><br>
				Expected FG&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php print $result['expect_fg'] ?><br><br>
				Volume&nbsp;&nbsp;&nbsp;: <?php print $result['other']['volume']; 
					if( $this->functions->recursive_in_array( $result['other']['volume_units'] , $measures_list , true ) ) {
			 			$imatch = $this->functions->recursive_array_search( $result['other']['volume_units'] , $measures_list );
						print " ".$measures_list[$imatch]['name'].($result['other']['volume'] > 1 ? "s " : " ");
						} ?><br>
				Yeast&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php print $result['yeast'] ?><hr>
			</div>