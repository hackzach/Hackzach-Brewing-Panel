<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
	<input id="volume" name="other[volume]" value="<?php print $result['other']['volume'] ?>" style="width:98px" type="text" placeholder="Batch Size">
		<select name="other[volume_units]">
<?php	for ($j = 0; $j < sizeof($measures_list); $j++) {
		if($measures_list[$j]['type'] == "volume") {
?>			<option value="<?php print $measures_list[$j]['id']; ?>"<?php ((!empty($result['other']['volume_units']) ? $result['other']['volume_units'] : "8") == $measures_list[$j]['id'] ? print " selected" : print "" );  ?>><?php print $measures_list[$j]['name'];($result['other']['volume'] > 1 ? print "s" : print "&nbsp;&nbsp;" ); ?></option>
<?php		}
	} ?>
		</select><br>
	<div id="stage_holder">
		<h4>Stage</h4>
		<div id="stages">
<?php
	function print_stages_array($object, $stages , $result , $options ) {

		foreach( $stages as $stage ) {

			$array_key = $stage[1]."_date";
?>				<div class="brew-stages">
					<input name="stage" value="<?php print $stage[1]; ?>" type="radio"<?php if($result['stage'] == $stage[1]) print " checked"; ?>>
					<span id="<?php print $stage[0]; ?>_datepicker" style="width:75%"<?php print ($result[$array_key] != '0000-00-00 00:00:00' ? ' title="'.$object->functions->time_elapsed_string($result[$array_key],true).'"' : ''); ?>><?php print $stage[2]; ?>
					<?php if( isset($result[$array_key]) && $result[$array_key] != '0000-00-00 00:00:00' ) print "&nbsp;&nbsp;<i style=\"float:right\">" . date_format(date_create($result[$array_key]), $options['hbp_date_format']) . "</i>"; ?></span>
					<input id="<?php print $stage[0]; ?>_holder" name="<?php print $stage[0]; ?>_date" placeholder="Date" type="text" style="display:none" value="<?php print $result[$array_key] ?>">
				</div>
<?php		}
	}