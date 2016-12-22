<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
			<div id="fermentables_holder">
					<h4>Fermentables <input type="button" id="addFermentable" class="add-type" name="fermentables" value="Add"></h4>
				<div id="fermentables">
<?php 
		for($i = 0;$i < sizeof($result['fermentables']); $i++) {
?>				<div id="fermentable_<?php print $i; ?>">
				<select class="form_data" name="fermentable[%IDX%][id]">
<?php					for ($j = 0; $j < sizeof($fermentable_list); $j++) {
?>						<option value="<?php print $fermentable_list[$j]['id']; ?>"<?php ( ($result['fermentables'][$i]['id'] == $fermentable_list[$j]['id'] ) ? print " selected" : print "" );  ?>><?php print $fermentable_list[$j]['name']; ?></option>
<?php					} 
?>
				</select>
				<input class="form_data" name="fermentable[%IDX%][amount]" style="width:75px;text-align:right;" value="<?php print $result['fermentables'][$i]['amount']; ?>" placeholder="Amount" type="text">
				<select class="form_data" name="fermentable[%IDX%][measure]">
<?php					for ($j = 0; $j < sizeof($measures_list); $j++) {
						if($measures_list[$j]['type'] == "weight") {
?>							<option value="<?php print $measures_list[$j]['id']; ?>"<?php ($result['fermentables'][$i]['measure'] == $measures_list[$j]['id'] ? print " selected" : print "" );  ?>><?php print $measures_list[$j]['name'];($result['fermentables'][$i]['amount'] > 1 ? print "s" : print "&nbsp;&nbsp;" ); ?></option>
<?php						}
					} 
?>				</select>
				<input name="delete" value="delete" onclick="jQuery('#fermentable_<?php print $i; ?>').remove()" type="button">
				</div>
<?php
		}
 ?>
				</div>
			</div>