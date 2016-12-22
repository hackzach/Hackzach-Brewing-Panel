<?php
if ( ! defined( 'ABSPATH' ) ) exit;
	if(!empty($result['other']['additions'] ) ) {
?>

		<div id="additions_holder">
				<h4>Additions</h4>
			<div id="additions" style="width:95%">
				<ul>
<?php 			
		for($i = 0;$i < sizeof($result['other']['additions']); $i++) {
?>					<li id="addition_<?php print $i; ?>">
<?php			print $result['other']['additions'][$i]['date']." +";
	   		print $result['other']['additions'][$i]['amount']." ";
			if( $this->functions->recursive_in_array( $result['other']['additions'][$i]['measure'] , $measures_list , true ) ) {
			 	$imatch = $this->functions->recursive_array_search( $result['other']['additions'][$i]['measure'] , $measures_list );
				print $measures_list[$imatch]['name'].($result['other']['additions'][$i]['amount'] > 1 ? "s " : " ");
			} 
			if( $this->functions->recursive_in_array( $result['other']['additions'][$i]['id'] , $fermentable_list , true ) ) {
			 	$imatch = $this->functions->recursive_array_search( $result['other']['additions'][$i]['id'] , $fermentable_list );
				print "<i>".$fermentable_list[$imatch]['name']."</i>";
			} 

			if( $result['other']['additions'][$i]['gravity'] >= $result['og'] ) { //The addition is theoretical

				if( !empty( $result['other']['additions'][$i-1]['gravity'] ) ) { // More than one addition?

						// Subtract last addition from current one
					$ternary_additions = ( $result['other']['additions'][$i]['gravity'] - $result['other']['additions'][$i-1]['gravity'] ) * 1000; 

				} else {

						// Only one addition, subtract from original gravity
					$ternary_additions = ( $result['other']['additions'][$i]['gravity'] - $result['og']  ) * 1000;

				}

			} else {

						//Addition is subjective, no subtraction
					$ternary_additions = ( $result['other']['additions'][$i]['gravity']-1 ) * 1000; 

			}
		
			print ( $ternary_additions>0 ? " raised the gravity ".ceil($ternary_additions)." points" : " did not change wort's gravity." );
?>
					</li>
<?php
		}
 ?>				</ul>
			</div>
		</div>
<?php } ?>