<?php
if ( ! defined( 'ABSPATH' ) ) exit;
	function print_notes_array($object, $stages, $options ) {
		global $wpdb;
		$table_brewing_notes = $wpdb->prefix . 'brewing_notes';
		foreach( $stages as $stage ) {
			$stage_notes = $wpdb->get_results($wpdb->prepare( " SELECT * FROM $table_brewing_notes WHERE serial = '%d' AND stage = '%s'",$_POST['brew'], $stage[1]),ARRAY_A);
			$stage_notes = stripslashes_deep($stage_notes);
			$array_key = $stage[1]."_notes";
?>
				<h4><?php print $stage[2]; ?> Notes <input type="button" name="add_note" value="Add" onClick="window.forms.addNote('<?php print $array_key ?>', '<?php print $stage[0]; ?>', '<?php print $stage[1] ?>')"></h4>
	<div id="<?php print $stage[1] ?>_notes">
<?php				for($i=0;$i<sizeof($stage_notes);$i++) { ?>
					<div id="<?php print $array_key ?>_<?php print $stage_notes[$i]['id']; ?>"> 
<?php					if( ( get_current_user_id() == $stage_notes[$i]['user_id'] ) || current_user_can('edit_brews') ) { ?>
						<div id="<?php print $array_key ?>_<?php print $stage_notes[$i]['id']; ?>_editable" class="note_editable">
						   <span onClick="window.forms.noteViewable('<?php print $array_key ?>_<?php print $stage_notes[$i]['id']; ?>')">
							<span id="<?php print $stage_notes[$i]['id']; ?>_date"><strong><?php print date_format(date_create($stage_notes[$i]['date']), $options['hbp_date_format']); ?> by <?php print $object->functions->get_display_name($stage_notes[$i]['user_id']) ?></strong></span> <input name="delete" value="delete" onclick="deleteNote('<?php print $array_key ?>_<?php print $stage_notes[$i]['id'] ?>', '<?php print $stage[0]; ?>', '<?php print $i ?>')" type="button"><br>
				<?php		if( NULL != $stage_notes[$i]['modify_id'] ) { ?>
							<small>Modified <?php print date_format(date_create($stage_notes[$i]['modify_date']), $options['hbp_date_format']); ?> by <?php print $object->functions->get_display_name($stage_notes[$i]['modify_id']) ?></small><br>
				<?php		} ?>		   </span>
							<input class="form_data" name="<?php print $stage[0]; ?>_notes[<?php print $i ?>][date]" type="hidden" value="<?php print $stage_notes[$i]['date']; ?>">
							<input class="form_data" name="<?php print $stage[0]; ?>_notes[<?php print $i ?>][id]" type="hidden" value="<?php print $stage_notes[$i]['id']; ?>">
							<textarea class="form_data" name="<?php print $stage[0]; ?>_notes[<?php print $i ?>][notes]" rows="6" cols="69" id="<?php print $array_key ?>_<?php print $stage_notes[$i]['id']; ?>_textarea" onkeydown="window.forms.noteSave('<?php print $array_key ?>_<?php print $stage_notes[$i]['id']; ?>', event)" placeholder="Enter Notes"><?php print $stage_notes[$i]['notes']; ?></textarea>
						</div>
<?php					}  ?>
						<div id="<?php print $array_key ?>_<?php print $stage_notes[$i]['id']; ?>_view" class="note_view" <?php print ( ( get_current_user_id() == $stage_notes[$i]['user_id'] ) || current_user_can('edit_brews') ? "onClick=\"window.forms.noteEditable('".$array_key."_".$stage_notes[$i]['id']."')\"" : "" ); ?>>
							<span id="<?php print $stage_notes[$i]['id']; ?>_date"><strong><?php print date_format(date_create($stage_notes[$i]['date']), $options['hbp_date_format']); ?> by <?php print $object->functions->get_display_name($stage_notes[$i]['user_id']) ?></strong></span><br>
				<?php		if( NULL != $stage_notes[$i]['modify_id'] ) { ?>
							<small>Modified <?php print date_format(date_create($stage_notes[$i]['modify_date']), $options['hbp_date_format']); ?> by <?php print $object->functions->get_display_name($stage_notes[$i]['modify_id']) ?></small><br>
				<?php		} ?>
							<p id="<?php print $array_key ?>_<?php print $stage_notes[$i]['id']; ?>_note"><?php print $stage_notes[$i]['notes']; ?></p>
						</div>
					</div>
<?php	
				} 
		
?>
				</div>
<?php
		}
	}	?>