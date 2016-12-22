<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<small>Modified <?php print date_format(date_create($result['date']), $options['hbp_date_format']); ?> by <?php print $this->functions->get_display_name($result['modify_id']) ?></small><br>
<div id="<?php print $this->_token; ?>_general_brew" class="brew-column">
				<?php print $options['hbp_serial_prefix']; ?>-<input name="serial" maxlength="5" style="width:55px" type="text" value="<?php print $result['serial'] ?>" readonly>-
				<input name="bottle" maxlength="3" style="width:38px" type="text" value="<?php print $result['bottle'] ?>"><br>
				<input name="brewName" placeholder="Brew Name" type="text" style="width:140px;text-align:right" value="<?php print $result['name'] ?>">