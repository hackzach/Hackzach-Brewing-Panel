<?php
if ( ! defined( 'ABSPATH' ) ) exit;
	function print_notes_array( $stages ) {

		foreach( $stages as $stage ) {
?>
				<h4><?php print $stage[2]; ?> Notes <input type="button" class="add_note" name="addNote" value="Add" onClick="window.forms.addNote('<?php print $stage[1]; ?>_notes', '<?php print $stage[0]; ?>', '<?php print $stage[1] ?>')"></h4>
				<div id="<?php print $stage[1]; ?>_notes"></div>
				
<?php
		}
	}	?>