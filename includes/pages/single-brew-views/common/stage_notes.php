<?php
if ( ! defined( 'ABSPATH' ) ) exit;
	function print_notes_array($object, $stages , $result, $options ) {
			global $wpdb;
			$table_brewing_notes = $wpdb->prefix.'brewing_notes';
		foreach( $stages as $stage ) {
			$stage_notes = $wpdb->get_results($wpdb->prepare( " SELECT * FROM $table_brewing_notes WHERE serial = '%d' AND stage = '%s'",$result['serial'], $stage[1]),ARRAY_A);
			$array_key = $stage[1]."_notes";
			if(!empty($stage_notes)) {
			$stage_notes = stripslashes_deep($stage_notes);
?>
				<h5 title="Click to toggle notes."><span onClick="jQuery('#<?php print $stage[1] ?>_notes').toggle('clip');"><?php print $stage[2]; ?> Notes</span></h5>
				<div id="<?php print $stage[1] ?>_notes" style="display:none">
<?php				for($i=0;$i<sizeof($stage_notes);$i++) {
?>
					<div id="<?php print $stage[0]; ?>_</php print $stage_notes[$i]['id'] ?>_notes">
						<div id="<?php print $array_key ?>_<?php print $stage_notes[$i]['id']; ?>_view">
							<span id="<?php print $stage_notes[$i]['id']; ?>_date"><strong><?php print date_format(date_create($stage_notes[$i]['date']), $options['hbp_date_format']); ?> by <?php print $object->functions->get_display_name($stage_notes[$i]['user_id']) ?></strong></span><br>
<?php						if( NULL != $stage_notes[$i]['modify_id'] ) {
?>
							<small>Modified <?php print date_format(date_create($stage_notes[$i]['modify_date']), $options['hbp_date_format']); ?> by <?php print $object->functions->get_display_name($stage_notes[$i]['modify_id']) ?></small><br>
<?php						}
?>
							<p id="<?php print $stage_notes[$i]['id']; ?>_note"><?php print $stage_notes[$i]['notes']; ?></p>
						</div>
					</div>
<?php				}
?>
				</div>
<?php			}
		}
	}