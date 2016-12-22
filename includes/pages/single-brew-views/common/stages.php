<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
	<div id="stage_holder">
		<div id="stages" style="width:90%">
		<hr>
<?php
	function print_stages_array( $stages , $result , $options ) {

		foreach( $stages as $stage ) {
			$array_key = $stage[1]."_date"; ?>

				<div id="<?php print $stage[0]; ?>_date" style="width:90%"><?php if($result['stage'] == $stage[1]) print "<b>".$stage[2]."</b>"; else print $stage[2]; ?>
				<?php if( isset($result[$array_key]) && $result[$array_key] != '0000-00-00 00:00:00' ) print "&nbsp;&nbsp;<i style=\"float:right\">" . date_format(date_create($result[$array_key]), $options['hbp_date_format']) . "</i>"; ?></div>
				<input id="<?php print $stage[0]; ?>" name="<?php print $stage[0]; ?>_date" placeholder="Date" type="hidden" value="<?php print $result[$array_key] ?>">
<?php		}
	} ?>
		</div>
	</div>