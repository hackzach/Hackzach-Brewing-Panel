<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
			<div id="hops_holder">
					<h4>Hops <input type="button" id="addHop" class="add-type" name="hops" value="Add"></h4>
				<div id="hops">
<?php
		for($i = 0;$i < sizeof($result['other']['hops']); $i++) {
?>				<div id="hop_<?php print $i; ?>">
				<select class="form_data" name="other[hops][%IDX%][id]">
<?php					for ($j = 0; $j < sizeof($hops_list); $j++) {
?>						<option value="<?php print $hops_list[$j]['id']; ?>"<?php ( ( $result['other']['hops'][$i]['id'] == $hops_list[$j]['id'] ) ? print " selected" : print "" );  ?>><?php print $hops_list[$j]['name']; ?></option>
<?php					} 
?>
				</select>
				<input class="form_data" name="other[hops][%IDX%][amount]" style="width:75px;text-align:right;" value="<?php print $result['other']['hops'][$i]['amount']; ?>" placeholder="Amount" type="text">
				<select class="form_data" name="other[hops][%IDX%][measure]">
<?php					for ($j = 0; $j < sizeof($measures_list); $j++) {
						if($measures_list[$j]['type'] == "weight") {
?>							<option value="<?php print $measures_list[$j]['id']; ?>"<?php ($result['other']['hops'][$i]['measure'] == $measures_list[$j]['id'] ? print " selected" : print "" );  ?>><?php print $measures_list[$j]['name'];($result['other']['hops'][$i]['amount'] > 1 ? print "s" : print "&nbsp;&nbsp;" ); ?></option>
<?php						}
					} 
?>				</select>
				<input class="form_data" name="other[hops][%IDX%][time]" style="width:95px;text-align:right;" value="<?php print $result['other']['hops'][$i]['time']; ?>" placeholder="Boil Time" type="text">
				<input name="delete" value="delete" onclick="jQuery('#hop_<?php print $i; ?>').remove()" type="button">
				</div>
<?php
		}
 ?>
				</div>
			</div>