<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
			<div id="additions_holder">
				<h4>Additions <input type="button" class="add-type" name="additions" id="addAddition" value="Add"></h4>
				<div id="additions">
<?php
				for($i=0;$i<sizeof($result['other']['additions']);$i++) { ?>
					<div id="addition_<?php print $i; ?>">
						<input class="form_data" name="other[additions][%IDX%][date]" value="<?php print $result['other']['additions'][$i]['date']; ?>" type="hidden">
						<select class="form_data" name="other[additions][%IDX%][id]">
<?php					for ($j = 0; $j < sizeof($fermentable_list); $j++) {
						if($fermentable_list[$j]['category'] == "Sugar" || $fermentable_list[$j]['category'] == "Adjunct") {
?>							<option value="<?php print $fermentable_list[$j]['id']; ?>"<?php ( ($result['other']['additions'][$i]['id'] == $fermentable_list[$j]['id'] ) ? print " selected" : print "" );  ?>><?php print $fermentable_list[$j]['name']; ?></option>
<?php						}
					} 
?>
						</select>
						<input class="form_data" name="other[additions][%IDX%][amount]" value="<?php print $result['other']['additions'][$i]['amount']; ?>" style="width:60px;text-align:right;" type="text" placeholder="Amount">
						<select class="form_data" name="other[additions][%IDX%][measure]">
<?php					for ($j = 0; $j < sizeof($measures_list); $j++) {
						if($measures_list[$j]['type'] == "weight") {
?>							<option value="<?php print $measures_list[$j]['id']; ?>"<?php ($result['other']['additions'][$i]['measure'] == $measures_list[$j]['id'] ? print " selected" : print "" );  ?>><?php print $measures_list[$j]['name'];($result['other']['additions'][$i]['amount'] > 1 ? print "s" : print "&nbsp;&nbsp;" ); ?></option>
<?php						}
					} 

				$original_gravity = $result['og']; // Start with original gravity
				for( $j = 0; $j<count($result['other']['additions']); $j++ ) {
					if( ( $result['other']['additions'][$j]['gravity'] > $result['og'] ) && ( $j != $i ) ) { // The gravity is theoretical in nature and not the current addition
						$result['other']['additions'][$j]['gravity'] = ($result['other']['additions'][$j]['gravity']-$result['og'])+1; // Convert to subjective gravity
					}
						$original_gravity += ($result['other']['additions'][$j]['gravity']-1); // Add all additions to original gravity
				} 
	
?>						</select>
						<input class="form_data" name="other[additions][%IDX%][gravity]" value="<?php print $result['other']['additions'][$i]['gravity']; ?>" style="width:60px;text-align:right;" type="text" placeholder="Gravity" onChange="window.forms.calc_abv( parseFloat(<?php print ($original_gravity-1); ?>)+parseFloat(jQuery(this).val()) , parseFloat(jQuery('#fg_expt').val()) , 'expt_abv' )">
						<input name="delete" value="delete" onclick="window.forms.calc_abv( parseFloat(<?php print ($original_gravity+1); ?>)-parseFloat(jQuery('#addition_gravity_<?php print $i; ?>').val()) , parseFloat(jQuery('#fg_expt').val()) , 'expt_abv' );jQuery('#addition_<?php print $i; ?>').remove()" type="button">
					</div>
<?php				} ?>
				</div>
			</div>