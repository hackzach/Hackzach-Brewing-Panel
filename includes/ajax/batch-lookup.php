<?php
if ( ! defined( 'ABSPATH' ) ) exit;
	global $wpdb;
	$table_name = $wpdb->prefix . 'brewing_panel';

	//$nonce 		= ( wp_verify_nonce( $_POST['session'] , "batch-search" ) ? true : die() );
	$brew 		= esc_sql($_POST['brew']);
	$sort 		= ( !empty( $_POST['sort'] ) ? esc_sql($_POST['sort']) : 'serial' );
	$order 		= ( !empty( $_POST['order'] ) ? esc_sql($_POST['order']) : 'ASC' );
	$current_page 	= ( !empty( $_POST['pagenum'] ) ? esc_sql( $_POST['pagenum'] ) : 1 );
	$search 	= esc_sql( $wpdb->esc_like( $_POST['search'] ) );

	$options = get_option('hbp_settings_general');

	$post_parent = $_POST['parent'];

	$limit = 5;
	$offset = ( $current_page - 1 ) * $limit;
	?>
<table>
	<thead>
		<tr>
			<th><?php $this->rendering->batch_lookup_sorting_url('serial', 'Serial No.', $post_parent, $search, $sort, $order); ?></th>
			<th><?php $this->rendering->batch_lookup_sorting_url('name', 'Name', $post_parent, $search, $sort, $order); ?></th>
			<th><?php $this->rendering->batch_lookup_sorting_url('stage', 'Stage', $post_parent, $search, $sort, $order); ?></th>
			<th><?php $this->rendering->batch_lookup_sorting_url('bottle', '# Bottles', $post_parent, $search, $sort, $order); ?></th>
		<!--	<th><?php $this->rendering->batch_lookup_sorting_url('date', 'Date', $post_parent, $search, $sort, $order); ?></th> -->
		</tr>
	</thead>
	<tbody>
	<?php
	$search = str_ireplace( $options['hbp_serial_prefix']."-" , "" , $search );
	$search = str_ireplace( $options['hbp_serial_prefix'] , "" , $search );
	$search = '%'.$search.'%';

	$search_query = (!empty($_POST['search']) ? "(serial LIKE '$search'".
			" OR name LIKE '$search'".
			" OR ferment_date LIKE '$search'".
			" OR bottle_date LIKE '$search'".
			" OR type LIKE '$search'".
			" OR stage LIKE '$search') AND " : "");

	$privacy = ( !$this->privileges->is_collaborator($brew) || !current_user_can('edit_brews') ? 'PRIVATE <> true' : '' );

	$result = $wpdb->get_results(" SELECT * FROM $table_name WHERE $search_query $privacy ORDER BY $sort $order LIMIT $offset,$limit ",ARRAY_A);

	$total = $wpdb->get_var(" SELECT COUNT(`serial`) FROM $table_name WHERE $search_query $privacy");

	$num_pages = ceil( $total / $limit );

	foreach( $result as $brew ) {
	?>
		<tr>
			<td><a href="<?php print get_permalink( $post_parent ).$brew['serial'] ?>/"><?php print $options['hbp_serial_prefix']; ?>-<?php print $brew['serial'] ?></a></td>
			<td><?php print $brew['name'] ?> <?php print $brew['type'] ?></td>
			<td><?php print (!empty($brew['stage']) ? $brew['stage'] : 'processing') ?></td>
			<td><?php print $brew['bottle'] ?></td>
		<!--	<td><?php print date_format(date_create($brew['date']), $options['hbp_date_format']); ?></td> -->
		</tr>
	<?php }
	?>
	</tbody>
	</table>
	<?php
	if( $post_parent !== false ) {
		$page_links = paginate_links( array(
			'base' => add_query_arg( 'pagenum', '%#%' , get_permalink( $post_parent ) ),
			'format' => '',
			'prev_text' => __( '&laquo;', 'hackzach_brewing_panel' ),
			'next_text' => __( '&raquo;', 'hackzach_brewing_panel' ),
			'total' => $num_pages,
			'current' => $current_page
		) );
		if ( $page_links ) {
	?>
		<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0"><?php print $total; ?> batch<?php print ($total > 1 ? 'es' : '' ); ?>.
	<?php
			print $page_links;
	?>
		</div></div>
	<?php
		}
	}