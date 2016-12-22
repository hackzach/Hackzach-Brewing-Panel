<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
	<div id="<?php print $this->_token; ?>_general_brew" class="brew-column"><br>
				<?php print $options['hbp_serial_prefix']; ?>-<input name="serial" maxlength="5" style="width:55px" placeholder="XXXXX" type="text" readonly>-
				<input name="bottle" maxlength="3" value="000" style="width:38px" type="text" readonly><br>
				<input name="brewName" placeholder="Brew Name" type="text">