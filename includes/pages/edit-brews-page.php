<?php
if ( ! defined( 'ABSPATH' ) ) exit;
	global $wpdb;

	$table_brewing_panel 		= $wpdb->prefix . 'brewing_panel';

	$options = get_option('hbp_settings');

	$brew = $_REQUEST['brew'];

	$brew_exists = $wpdb->get_var( $wpdb->prepare(" SELECT COUNT(`serial`) FROM $table_brewing_panel WHERE serial = %d",$brew) );

if( !empty($brew) && ( $brew_exists == 1 ) ) {
		$tab = ( !empty( $_REQUEST['tab'] ) ? urlencode($_REQUEST['tab']) : 'brew_info' );

		if( isset( $_GET['add'] ) ) {
			if( !$result = $this->privileges->make_owner_new_brew( $brew, get_current_user_id() ) ) {
				?>
					<div class="list-actions" style="float:left"><p>Unable to link ownership.</p></div>
				<?php
			}
		}

		if( ( $_SERVER['REQUEST_METHOD'] == 'POST' ) ) {
			if( check_admin_referer( "batch-edit-".$brew , "session" ) ) {
				switch($tab) {
					case 'brew_info' :
						if( $this->brew->is_locked($brew) !== 1 && ( current_user_can( 'brew') && $this->privileges->is_collaborator($brew) ) || current_user_can('edit_brews') ) {
							// We need to be more strict with form data. Even though wpdb->insert automatically cleans input, it
							// does not convert HTML. This gets output to browser as is, let's encode it.
							$_POST['other']['meta_desc'] = htmlentities( $_POST['other']['meta_desc'], ENT_NOQUOTES );

							$result = $this->brew->update_data(
								$brew,
								$_POST['bottle'],
								$_POST['type'],
								$_POST['brewName'],
								$_POST['stage'],
								$_POST['frm_date'],
								$_POST['dst_date'],
								$_POST['cnd_date'],
								$_POST['age_date'],
								$_POST['btl_date'],
								 $_POST['fmt_notes'],
								 $_POST['dst_notes'],
								 $_POST['cnd_notes'],
								 $_POST['age_notes'],
								 $_POST['btl_notes'],
								$_POST['og'],
								$_POST['yeast'],
								serialize($_POST['fermentable']),
								$_POST['fg_expt'],
								$_POST['fg_actl'],
								serialize($_POST['other'])
							);
						}
					break;
					case 'permissions' :
						if( ( current_user_can( 'brew') && $this->privileges->is_owner($brew) ) || current_user_can('edit_brews') ) {
							if( !empty($_POST['collaborators']['add'])) {
								foreach($_POST['collaborators']['add'] as $collaborator) {
									$this->privileges->add_collaborator($brew, $collaborator);
								}
							}
							if( !empty($_POST['collaborators']['remove'])) {
								foreach($_POST['collaborators']['remove'] as $collaborator) {
									$this->privileges->remove_collaborator($brew, $collaborator);
								}
							}
							$privacy = ( isset($_POST['privacy']) ? true : false );
							$wpdb->update($table_brewing_panel,
								 array( 	'private' 	=> $privacy ), // The column(s) to update
								 array(		'serial' 	=> $brew) // Where clause(s)
							);

						}
					break;
				}
			
			}
			else { ?>
				<center><h4>You do not have permission to do this.</h4></center>
		<?php 	}
		}

		add_thickbox(); // Modal for Calculators link in brew forms
?>
					<div class="wrap" id="hackzach_container">
				<?php $this->rendering->hbp_brew_forms_tabs($tab, $brew); ?><br>
						<form id="brew_form" action="<?php echo $_SERVER['PHP_SELF']."?page=".$_GET['page']."&brew=".$brew ?>" onSubmit="window.forms.reindex_all()" method="POST">
							<?php wp_nonce_field( "batch-edit-".$brew , "session" , true ); ?>
							<div id="brew_detail_form">
								<center><img src="<?php print plugins_url( 'hackzach-brewing-panel/assets/ajax-loader.gif' ) ?>" title="Loading..." /></center>
							</div>
						</form>
					</div>

		<div id="calculators" class="modal-ui">
			<?php include plugin_dir_path( __FILE__ )."/calculators-page.php"; ?>
		</div>
		<div id="dialogs" class="modal-ui">
			<div id="unlock-error" class="thickbox"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Could not unlock brew. Others will not be able to make changes until the session expires.</p></div>
			<div id="lock-error" class="thickbox"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Could not lock brew. Others may be able to make changes while you are working.</p></div>
			<div id="in-use" class="thickbox"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>This brew is being edited by <strong><?php print $this->functions->get_display_name($this->brew->lock_id($brew)) ?></strong> since <i><?php print date_format(date_create($this->brew->lock_time($brew)), $options['hbp_date_format']) ?></i>.</p></div>
		</div>
<?php 	
 } else { ?>
				<script type="text/javascript">
					window.location = "<?php print admin_url( 'admin.php?page=hackzach_brewing_panel&filter=recent') ?>"
				</script>
<?php } ?>