<?php
if ( ! defined( 'ABSPATH' ) ) exit;
 if(!empty($result['fermentables'] ) ) { ?>
		<div id="fermentables_holder">
				<h4>Fermentables</h4>
			<div id="fermentables" style="width:95%">
				<ul>
<?php 			
		for($i = 0;$i < sizeof($result['fermentables']); $i++) {
?>					<li id="fermentable_<?php print $i; ?>">
<?php   		print $result['fermentables'][$i]['amount']." ";
			if( $this->functions->recursive_in_array( $result['fermentables'][$i]['measure'] , $measures_list , true ) ) {
			 	$imatch = $this->functions->recursive_array_search( $result['fermentables'][$i]['measure'] , $measures_list );
				print $measures_list[$imatch]['name'].($result['fermentables'][$i]['amount'] > 1 ? "s " : " ");
			} 
			if( $this->functions->recursive_in_array( $result['fermentables'][$i]['id'] , $fermentable_list , true ) ) {
			 	$imatch = $this->functions->recursive_array_search( $result['fermentables'][$i]['id'] , $fermentable_list );
				print "<i>".$fermentable_list[$imatch]['name']."</i>";
			} 
?>
					</li>
<?php
		}
 ?>				</ul>
			</div>
		</div>
<?php } ?>