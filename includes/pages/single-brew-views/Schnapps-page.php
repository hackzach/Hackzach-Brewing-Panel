<?php
if ( ! defined( 'ABSPATH' ) ) exit;
 ?>
				<?php include plugin_dir_path(__FILE__)."/common/header.php"; ?>
				<?php include plugin_dir_path(__FILE__)."/common/stages.php"; 
				print_stages_array( Array( 
					Array( "frm" , "ferment" , "Fermenting" ) ,
					Array( "dst" , "distill" , "Distilled" ) , 
					Array( "cnd" , "condition" , "Conditioning" ) , 
					Array( "btl" , "bottle" , "Bottled" )  
				) , $result , $options );
				?>
				<?php include plugin_dir_path(__FILE__)."/common/gravity_yeast.php"; ?>
				<?php include plugin_dir_path(__FILE__)."/common/fermentables-loop.php"; ?>
				<?php include plugin_dir_path(__FILE__)."/common/hops-loop.php"; ?>
				<?php include plugin_dir_path(__FILE__)."/common/additions-loop.php"; ?>
				<?php include plugin_dir_path(__FILE__)."/common/distill-loop.php"; ?>
		</div>
		<div id="<?php print $this->_token; ?>_stage_details" class="brew-column">
			<div id="notes_holder">
				<?php include plugin_dir_path(__FILE__)."/common/stage_notes.php";
				print_notes_array($this, Array( 
					Array( "fmt" , "ferment" , "Fermenting" ) , 
					Array( "dst" , "distill" , "Distilling" ) ,
					Array( "cnd" , "condition" , "Conditioning" ) ,
					Array( "btl" , "bottle" , "Bottling" ) 
				) , $result, $options );
				 ?>
			</div>
		</div>
</div>
<?php
		if( current_user_can( 'edit_brews' ) || $this->privileges->is_collaborator($brew) ) {
			print "<div id=\"edit_link\"><a href='".admin_url( 'admin.php?page=hackzach_brewing_panel_edit&brew='.$brew )."'>Edit Brew</a></div>";
		}
?>