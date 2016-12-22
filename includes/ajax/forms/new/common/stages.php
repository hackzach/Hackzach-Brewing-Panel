<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
	<input id="volume" name="other[volume]" value="<?php print $result['other']['volume'] ?>" style="width:98px" type="text" placeholder="Batch Size">
		<select name="other[volume_units]">
<?php	for ($j = 0; $j < sizeof($measures_list); $j++) {
		if($measures_list[$j]['type'] == "volume") {
?>			<option value="<?php print $measures_list[$j]['id']; ?>"<?php ($measures_list[$j]['id'] == "8" ? print " selected" : print "" );  ?>><?php print $measures_list[$j]['name'];($result['other']['volume'] > 1 ? print "s" : print "&nbsp;&nbsp;" ); ?></option>
<?php		}
	} ?></select>
	<div id="stage_holder">
		<h4>Stage</h4>
		<div id="stages">
<?php	function print_stages_array( $stages ) {
		foreach( $stages as $stage ) {
?>				<div class="brew-stages">
					<span id="<?php print $stage[0]; ?>_datepicker"><input name="stage" value="<?php print $stage[1]; ?>" type="radio"><?php print $stage[2]; ?></span>
					<input id="<?php print $stage[0]; ?>_holder" name="<?php print $stage[0]; ?>_date" placeholder="Date" type="text" style="display:none">
				</div>
<?php		}
	}	?>