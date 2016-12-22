<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb,$current_user;
		if( ( $_SERVER['REQUEST_METHOD'] == 'POST' ) && check_admin_referer( "batch-add", "session" ) ) {

			// We need to be more strict with form data. Even though wpdb->insert automatically cleans input, it
			// does not convert HTML. This gets output to browser as is, let's encode it.

			$_POST['other']['meta_desc'] = htmlentities( $_POST['other']['meta_desc'], ENT_NOQUOTES );

			$result = $this->brew->add_data(
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
			if($result) {
			?>
				<script type="text/javascript">
					window.location = "<?php print admin_url( 'admin.php?page=hackzach_brewing_panel_edit&brew='.$result.'&add' ); ?>"
				</script>	
			<?php 
			}
			else {
				print "FAIL:"; $wpdb->show_errors();$wpdb->print_error();
			}
		}
?>
			<div class="wrap" id="hackzach_container">
				<h2><?php print __( 'Start New Brew' , 'hackzach-brewing-panel' ); ?></h2>
				<form id="brew_form" action="<?php echo $_SERVER['PHP_SELF']."?page=".$_GET['page'] ?>" onSubmit="window.forms.reindex_all()" method="POST">
					<?php wp_nonce_field( "batch-add" , "session" , true ); ?>
					<div id="brew_detail_form">
					<center><img src="<?php print plugins_url( 'hackzach-brewing-panel/assets/ajax-loader.gif' ) ?>" title="Loading..." /></center>
					</div>
				</form>
			</div>
			<div id="calculators" class="modal-ui">
				<?php include plugin_dir_path( __FILE__ )."/calculators-page.php"; ?>
			</div>