<?php
if ( ! defined( 'ABSPATH' ) ) exit;
 if(!empty($result['other']['hops'] ) ) { ?>
		<!-- Hops list JS -->
		<div id="hops_holder">
				<h4>Hops</h4>
			<div id="hops" style="width:95%">
				<ul>
<?php 			
		for($i = 0;$i < sizeof($result['other']['hops']); $i++) {
?>					<li id="hop_<?php print $i; ?>">
<?php   		print $result['other']['hops'][$i]['amount']." ";
			if( $this->functions->recursive_in_array( $result['other']['hops'][$i]['measure'] , $measures_list , true ) ) {
			 	$imatch = $this->functions->recursive_array_search( $result['other']['hops'][$i]['measure'] , $measures_list );
				print $measures_list[$imatch]['name'].($result['other']['hops'][$i]['amount'] > 1 ? "s " : " ");
			} 
			if( $this->functions->recursive_in_array( $result['other']['hops'][$i]['id'] , $hops_list , true ) ) {
			 	$imatch = $this->functions->recursive_array_search( $result['other']['hops'][$i]['id'] , $hops_list );
				print "<i>".$hops_list[$imatch]['name']."</i> ";
			} 
			if( strcasecmp($result['other']['hops'][$i]['time'],"infusion") == 0 || strcasecmp($result['other']['hops'][$i]['time'],"Primary dry hop") == 0 || strcasecmp($result['other']['hops'][$i]['time'],"Secondary dry hop") == 0 ) {
				print " via " . $result['other']['hops'][$i]['time'];
			} else print $result['other']['hops'][$i]['time']." mins";
?>
					</li>
<?php
		}
 ?>				</ul>
			</div>
		</div>
<?php } ?>
