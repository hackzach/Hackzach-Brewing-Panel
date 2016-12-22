<?php

class Hackzach_Brewing_Panel_List_Table extends WP_Core_List_Table {

    protected $item_actions;
    protected $bulk_actions;

    protected $item_name;
    protected $default_sort;

    protected $all_columns;
    protected $sortable_columns;

    private $options;

    private $object;

    private $limit;

    private $filter;

    function __construct($object, $item_name){
        global $status, $page;

	$this->object		= $object;  

	switch($item_name) {
		case 'brew' :
			$this->limit = 5;

			$this->default_sort = 'serial';

			$this->filter = (!empty($_REQUEST['filter'])) ? esc_sql($_REQUEST['filter']) : 'my_brews';

			$this->all_columns	= array(
							'serial' => 'Serial',
							'bottle' => '# Bottles',
							'name' => 'Name',
							'type' => 'Type',
							'date' => 'Modified By',
							'stage' => 'Stage',
							//'yeast' => 'Yeast',
							//'actual_fg' => 'Final Gravity',
							'other' => 'Description'
						);
			$this->sortable_columns	= array(
							'serial' => 'Serial',
							'bottle' => '# Bottles',
							'name' => 'Name',
							'type' => 'Type',
							'date' => 'Modified By',
							'stage' => 'Stage'
							//'yeast' => 'Yeast',
							//'actual_fg' => 'Final Gravity'
						);
			$this->item_actions	= array(
				array(
					'name' 	=> 'view',
					'title'	=> 'View',
					'url' 	=> site_url("batch-lookup/"),
					'class' => 'edit-action'
				),
				array(
					'name' 	=> 'edit',
					'title'	=> 'Edit',
					'url' 	=> admin_url( "admin.php?page=hackzach_brewing_panel_edit"),
					'class' => 'edit-action'
				),
				array(
					'name' 	=> 'permissions',
					'title'	=> 'Permissions',
					'url' 	=> admin_url( "admin.php?page=hackzach_brewing_panel_edit&tab=permissions")
				),
				array(
					'name' 	=> 'delete',
					'title'	=> 'Delete',
					'url' 	=> admin_url( "admin.php?page=".$_REQUEST['page']."&action=delete"),
					'class' => 'delete-action'
				)
			);

			if( ( current_user_can('edit_brews') ||	//   Admin has permission for bulk action
				 ( empty($_REQUEST['filter'] ) || // Default tab for brews is 'My Brews"
				 	'my_brews' === $_REQUEST['filter'] ) // They can bulk delete their own brews
			     ) ) {
        			$this->bulk_actions	= array(
        		    			'delete'    => 'Delete'
        				);
			} else {
				$this->bulk_actions = array();
			}
		break;
		case 'ingredient' :

			$this->limit = 10;

			$this->default_sort = 'id';

			$this->filter = (!empty($_REQUEST['filter'])) ? esc_sql($_REQUEST['filter']) : 'fermentables';

			$this->all_columns		= array(
								'fermentables' => 
									array(
										'id' => 'ID',
										'name' => 'Name',
										'type' => 'Type', 
										'country' => 'Country',
		  								'category' => 'Category',
		  								'color' => 'Color',
		  								'ppg' => 'PPG'
									),
								'hops' =>
									array(
										'id' => 'ID',
										'name' => 'Name',
										'type' => 'Type', 
										'alpha_acid' => 'Alpha Acids', 
										'beta_acid' => 'Beta Acids', 
										'cohumulone' => 'Cohumulone', 
										'myrcene_oil' => 'Myrcene Oil', 
										'humulene_oil' => 'Humulene Oil', 
										'caryophyllene_oil' => 'Caryophyllene Oil', 
										'farnesene_oil' => 'Farnesene Oil'
									)
						);
			$this->sortable_columns		= array(
								'fermentables' => 
									array(
										'id' => 'ID',
										'name' => 'Name',
										'type' => 'Type', 
										'country' => 'Country',
		  								'category' => 'Category',
		  								'color' => 'Color',
		  								'ppg' => 'PPG'
									),
								'hops' =>
									array(
										'id' => 'ID',
										'name' => 'Name',
										'type' => 'Type', 
										'alpha_acid' => 'Alpha Acids', 
										'beta_acid' => 'Beta Acids', 
										'cohumulone' => 'Cohumulone', 
										'myrcene_oil' => 'Myrcene Oil', 
										'humulene_oil' => 'Humulene Oil', 
										'caryophyllene_oil' => 'Caryophyllene Oil', 
										'farnesene_oil' => 'Farnesene Oil'
									)
						);
			$this->item_actions = array(
				array(
					'name' 	=> 'edit',
					'title'	=> 'Edit',
					'url' 	=> admin_url( "options-general.php?page=hackzach_brewing_panel_settings&tab=$this->filter"),
					'class' => 'edit-action'
				)
			);

			if( current_user_can('edit_plugins') ) { //   Admin has permission for bulk action
        			$this->bulk_actions	= array(
        		    			'delete'    => 'Delete'
        				);
			} else {
				$this->bulk_actions = array();
			}

		break;
	}

	$this->item_name	= $item_name;

	$this->options		= get_option('hbp_settings_general');

	$defaults = array(
            		'singular'  => $item_name,  
            		'plural'    => $item_name.'s',  
            		'ajax'      => true       
        	);

        //Set parent defaults
        parent::__construct( $defaults );
    }

    function column_default($item, $column_name){
		return $item[$column_name];
    }

    function column_serial($item){
        //Return the title contents
        return sprintf('<span id="%2$s">%1$s</span>',
            /*%1$s*/ $this->options['hbp_serial_prefix']."-".$item['serial'],
            /*%2$s*/ $item['serial']
        );
    }
    function column_name($item){
	switch($this->item_name) {
		case 'brew' :
			if(!empty($this->item_actions)) {
        			foreach($this->item_actions as $action) {
					switch( $action['name'] ) {
						case 'view' :
							$actions[$action['name']] = sprintf('<a href="%s%s"%s>%s</a>',$action['url'],$item['serial'],(!empty($action['class']) ? ' class="'.$action['class'].'"' : ''),$action['title']);
						break;
						case 'edit' :
							if($this->object->privileges->is_collaborator($item['serial']) ) {
								if( $this->object->brew->is_locked($item['serial']) == 1 ) $actions[$action['name']] = 'In Use';
								else $actions[$action['name']] = sprintf('<a href="%s&%s=%s"%s>%s</a>',$action['url'],$this->item_name,$item['serial'],(!empty($action['class']) ? ' class="'.$action['class'].'"' : ''),$action['title']);
							}
						break;
						case 'permissions' :
							if($this->object->privileges->is_owner($item['serial'])) {
								$actions[$action['name']] = sprintf('<a href="%s&%s=%s"%s>%s</a>',$action['url'],$this->item_name,$item['serial'],(!empty($action['class']) ? ' class="'.$action['class'].'"' : ''),$action['title']);
							}
						break;
						case 'delete' :
							if($this->object->privileges->is_owner($item['serial'])) {
								$actions[$action['name']] = sprintf('<a id="%s" href="%s&%s=%s"%s>%s</a>',$item['serial'],$action['url'],$this->item_name,$item['serial'],(!empty($action['class']) ? ' class="'.$action['class'].'"' : ''),$action['title']);
							}
						break;
					}
				}
			}
		break;
		case 'ingredient' :
			if(!empty($this->item_actions)) {
        			foreach($this->item_actions as $action) {
					if( current_user_can('edit_plugins' ) ) {
						$actions[$action['name']] = sprintf('<a href="%s&%s=%s"%s>%s</a>',$action['url'],substr($this->filter,0,-1),$item['id'],(!empty($action['class']) ? ' class="'.$action['class'].'"' : ''),$action['title']);
					}
				}
			}
		break;
	}
        //Return the title contents
        return sprintf('<span id="%2$s">%1$s %3$s</span>',
            /*%1$s*/ $item['name'],
            /*%2$s*/ $item['id'],
            /*%3$s*/ $this->row_actions($actions)
        );
    }
    function column_date($item){

        return sprintf('<b><i>%s</i></b> on <small>%s</small>',
	    $this->object->functions->get_display_name($item['modify_id']),
            date_format(date_create($item['date']), $this->options['hbp_short_date'])
        );
    }

    function column_other($item){

        return sprintf('<p>%s</p>',
	    stripslashes($item['other']['meta_desc'])
        );
    }

    function column_cb($item){
        return sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'], 
            /*$2%s*/ $item[$this->default_sort]
        );
    }

    function get_columns(){
	if( !empty($this->all_columns) ) {
		switch($this->item_name) {
			case 'brew' :
				if( current_user_can('edit_brews') ||
					(empty($_REQUEST['filter'] ) ||
						'my_brews' === $_REQUEST['filter'])
				) { 
					$all_columns['cb'] = '<input type="checkbox" />';
				}
				foreach($this->all_columns as $column=>$title) {
					$all_columns[$column] = $title;
				}
			break;
			case 'ingredient' :
				if( current_user_can('edit_plugins') ) { 
					$all_columns['cb'] = '<input type="checkbox" />';
				}
				switch($this->filter) {
					case 'fermentables' :
						foreach($this->all_columns['fermentables'] as $column=>$title) {
							$all_columns[$column] = $title;
						}
					break;
					case 'hops' :
						foreach($this->all_columns['hops'] as $column=>$title) {
							$all_columns[$column] = $title;
						}
					break;
				}
			break;
		}
	}
	return $all_columns;
    }

    function get_sortable_columns() {
	if( !empty($this->sortable_columns) ) {
		switch($this->item_name) {
			case 'brew' :
				$columns = array_keys($this->sortable_columns);
				foreach($columns as $column) {
					$sortable_columns[$column] = array( $column, false);
				}
			break;
			case 'ingredient' :
				switch($this->filter) {
					case 'fermentables' :
						$columns = array_keys($this->sortable_columns['fermentables']);
						foreach($columns as $column) {
							$sortable_columns[$column] = array( $column, false);
						}
					break;
					case 'hops' :
						$columns = array_keys($this->sortable_columns['hops']);
						foreach($columns as $column) {
							$sortable_columns[$column] = array( $column, false);
						}
					break;
				}
			break;
		}
	}
	return $sortable_columns;
 
    }
    function single_row( $item ) {
		echo '<tr id="row_'.$item[$this->default_sort].'">';
		$this->single_row_columns( $item );
		echo '</tr>';
    }

	/**
	 *
	 * Override the column header display to include the current filter as a class
	 *
	 */
    function print_column_headers( $with_id = true ) {
		list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

		$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		$current_url = remove_query_arg( 'paged', $current_url );

		if ( isset( $_GET['orderby'] ) ) {
			$current_orderby = $_GET['orderby'];
		} else {
			$current_orderby = '';
		}

		if ( isset( $_GET['order'] ) && 'desc' === $_GET['order'] ) {
			$current_order = 'desc';
		} else {
			$current_order = 'asc';
		}

		if ( ! empty( $columns['cb'] ) ) {
			static $cb_counter = 1;
			$columns['cb'] = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' . __( 'Select All' ) . '</label>'
				. '<input id="cb-select-all-' . $cb_counter . '" type="checkbox" />';
			$cb_counter++;
		}

		foreach ( $columns as $column_key => $column_display_name ) {
			$class = array( 'manage-column', $this->filter,  "column-$column_key" );

			if ( in_array( $column_key, $hidden ) ) {
				$class[] = 'hidden';
			}

			if ( 'cb' === $column_key )
				$class[] = 'check-column';
			elseif ( in_array( $column_key, array( 'posts', 'comments', 'links' ) ) )
				$class[] = 'num';

			if ( $column_key === $primary ) {
				$class[] = 'column-primary';
			}

			if ( isset( $sortable[$column_key] ) ) {
				list( $orderby, $desc_first ) = $sortable[$column_key];

				if ( $current_orderby === $orderby ) {
					$order = 'asc' === $current_order ? 'desc' : 'asc';
					$class[] = 'sorted';
					$class[] = $current_order;
				} else {
					$order = $desc_first ? 'desc' : 'asc';
					$class[] = 'sortable';
					$class[] = $desc_first ? 'asc' : 'desc';
				}

				$column_display_name = '<a href="' . esc_url( add_query_arg( compact( 'orderby', 'order' ), $current_url ) ) . '"><span>' . $column_display_name . '</span><span class="sorting-indicator"></span></a>';
			}

			$tag = ( 'cb' === $column_key ) ? 'td' : 'th';
			$scope = ( 'th' === $tag ) ? 'scope="col"' : '';
			$id = $with_id ? "id='$column_key'" : '';

			if ( !empty( $class ) )
				$class = "class='" . join( ' ', $class ) . "'";

			echo "<$tag $scope $id $class>$column_display_name</$tag>";
		}
    }

    function get_bulk_actions() {
        return $this->bulk_actions;
    }

    function search_box( $text, $input_id, $placeholder = NULL ) {
		$input_id = $input_id . '-search-input';
		$placeholder = (!empty($placeholder) ? 'placeholder="'.$placeholder.'" ': '' );

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		}
		if ( ! empty( $_REQUEST['order'] ) ) {
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
		}
		if ( ! empty( $this->_pagination_args['per_page'] ) ) {
			echo '<input type="hidden" name="limit" value="' . esc_attr( $this->_pagination_args['per_page'] ) . '" />';
		}
			echo '<input type="hidden" name="filter" value="' . esc_attr( $this->filter ) . '" />';

		if ( ! empty( $_REQUEST['post_mime_type'] ) ) {
			echo '<input type="hidden" name="post_mime_type" value="' . esc_attr( $_REQUEST['post_mime_type'] ) . '" />';
		}
		if ( ! empty( $_REQUEST['detached'] ) ) {
			echo '<input type="hidden" name="detached" value="' . esc_attr( $_REQUEST['detached'] ) . '" />';
		}
	?>
	<p class="search-box">
		<label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
		<input type="search" id="<?php echo $input_id ?>" name="search" value="<?php echo $_REQUEST['search']; ?>" <?php echo $placeholder ?>/>
	</p>
	<?php
    }

    function extra_tablenav( $which ) {
        global $wpdb;
        if ( $which == "top" ){
    ?>
           <div class="alignleft actions bulkactions">
                <select id="<?php print $this->_args['singular'] ?>_limit_top" name="limit" class="<?php print $this->_args['singular'] ?>_limit">
		    <option value="5"<?php print ( empty($_REQUEST['limit']) ? ( 5 == $this->limit ? " selected" : "" ) : ( 5 == $_REQUEST['limit'] ? " selected" : "") ); ?>>5 per Page</option>
		    <option value="10"<?php print ( empty($_REQUEST['limit']) ? ( 10 == $this->limit ? " selected" : "" ) : ( 10 == $_REQUEST['limit'] ? " selected" : "") ); ?>>10 per Page</option>
		    <option value="15"<?php print ( empty($_REQUEST['limit']) ? ( 15 == $this->limit ? " selected" : "" ) : ( 15 == $_REQUEST['limit'] ? " selected" : "") ); ?>>15 per Page</option>
		    <option value="25"<?php print ( empty($_REQUEST['limit']) ? ( 25 == $this->limit ? " selected" : "" ) : ( 25 == $_REQUEST['limit'] ? " selected" : "") ); ?>>25 per Page</option>
		    <option value="50"<?php print ( empty($_REQUEST['limit']) ? ( 50 == $this->limit ? " selected" : "" ) : ( 50 == $_REQUEST['limit'] ? " selected" : "") ); ?>>50 per Page</option>
                </select>
            </div>
	<span id="loading"></span>
    <?php
        }
        if ( $which == "bottom" ){
    ?>
            <div class="alignleft actions bulkactions">
                <select id="<?php print $this->_args['singular'] ?>_limit_bottom" name="limit" class="<?php print $this->_args['singular'] ?>_limit">
		    <option value="5"<?php print ( empty($_REQUEST['limit']) ? ( 5 == $this->limit ? " selected" : "" ) : ( 5 == $_REQUEST['limit'] ? " selected" : "") ); ?>>5 per Page</option>
		    <option value="10"<?php print ( empty($_REQUEST['limit']) ? ( 10 == $this->limit ? " selected" : "" ) : ( 10 == $_REQUEST['limit'] ? " selected" : "") ); ?>>10 per Page</option>
		    <option value="15"<?php print ( empty($_REQUEST['limit']) ? ( 15 == $this->limit ? " selected" : "" ) : ( 15 == $_REQUEST['limit'] ? " selected" : "") ); ?>>15 per Page</option>
		    <option value="25"<?php print ( empty($_REQUEST['limit']) ? ( 25 == $this->limit ? " selected" : "" ) : ( 25 == $_REQUEST['limit'] ? " selected" : "") ); ?>>25 per Page</option>
		    <option value="50"<?php print ( empty($_REQUEST['limit']) ? ( 50 == $this->limit ? " selected" : "" ) : ( 50 == $_REQUEST['limit'] ? " selected" : "") ); ?>>50 per Page</option>
                </select>
            </div>
	<span id="loading"></span>
    <?php
        }
    }

    function current_action() {
		if ( isset( $_REQUEST['filter_action'] ) && ! empty( $_REQUEST['filter_action'] ) )
			return false;
		// We use 'action' to request AJAX Pages, we need to use 'action2' for AJAX actions
		if ( isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] && !defined( 'DOING_AJAX' ) )
			return $_REQUEST['action'];

		if ( isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] )
			return $_REQUEST['action2'];

		return false;
    }

    function process_bulk_action() {
        if( 'delete'===$this->current_action() ) {
		$delete_items = $_REQUEST[$this->_args['singular']];
		if( !empty( $delete_items ) ) {
	?>				<div class="list-actions" style="float:left">
							<ul>
	<?php		switch( $this->item_name ) {
				case 'brew' :
					if( is_array( $delete_items ) ) {
						foreach($delete_items as $brew) {
							if( $this->object->privileges->is_owner($brew) || current_user_can('delete_brews') ) {
								if($this->object->brew->delete_data($brew) ) {
									print "<li>Brew ".$brew." deleted.</li>\n";
								} else print "<li>Could not delete brew $brew.</li>\n";
							} else print "<li>You do not have permission to do this.</li>\n";
						}
					} else {
						if( $this->object->privileges->is_owner($delete_items) || current_user_can('delete_brews') ) {
								if($this->object->brew->delete_data($delete_items) ) {
									print "<li>Brew ".$delete_items." deleted.</li>\n";
								} else print "<li>Could not delete brew $delete_items.</li>\n";
						} else print "<li>You do not have permission to do this.</li>\n";
					}
				break;
				case 'ingredient' :
					if( current_user_can( 'edit_plugins' )  ) {
						switch($this->filter) {
							case 'fermentables' :
								if( is_array( $delete_items ) ) {
									foreach($delete_items as $fermentable) {
										//if( ) {
											print "<li>Fermentable ".$fermentable." deleted.</li>\n";
										//} else print "<li>Could not delete fermentable $fermentable.</li>\n";
									}
								} else {
										//if( ) {
											print "<li>Fermentable ".$delete_items." deleted.</li>\n";
										//} else print "<li>Could not delete fermentable $delete_items.</li>\n";
								}
							break;
							case 'hops' :
								if( is_array( $delete_items ) ) {
									foreach($delete_items as $hop) {
										//if( ) {
											print "<li>Hop ".$hop." deleted.</li>\n";
										//} else print "<li>Could not delete hop $hhop.</li>\n";
									}
								} else {
										//if( ) {
											print "<li>Hop ".$delete_items." deleted.</li>\n";
										//} else print "<li>Could not delete hop $delete_items.</li>\n";
								}
							break;
						}
					} else print "<li>You do not have permission to do this.</li>\n";
				break;
			}
	?>
							</ul>
					</div>
	<?php
        	}
	}
        			if('true' === $_REQUEST['ajax_delete']) die();
    }

    function prepare_items() {
        $this->process_bulk_action();
	global $wpdb;

	$table_brewing_panel 		= $wpdb->prefix . 'brewing_panel';
	$table_brew_owners 		= $wpdb->prefix . 'brew_owners';
	$table_brew_collaborators	= $wpdb->prefix . 'brew_collaborators';

	$table_brewing_hops		= $wpdb->prefix . 'brewing_hops';
	$table_brewing_ingredients 	= $wpdb->prefix . 'brewing_fermentables';

        $all_columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($all_columns, $hidden, $sortable);

        $current_page = $this->get_pagenum();

       	$limit = (!empty($_REQUEST['limit'])) ? absint($_REQUEST['limit']) : $this->limit;
	$offset = ( $current_page - 1 ) * $limit;

        $sort 	= (!empty($_REQUEST['orderby'])) ? esc_sql($_REQUEST['orderby']) : $this->default_sort; 
        $order 	= (!empty($_REQUEST['order'])) ? esc_sql($_REQUEST['order']) : 'asc'; 
 	$search = (!empty($_REQUEST['search'])) ? esc_sql($_REQUEST['search']) : NULL;

	switch($this->item_name) {
		case 'brew' :
			// Brews Querying
			$condition = ( strcasecmp($this->filter,'recent') === 0 ? "AND" : "WHERE");		

			$search_query = ( !empty($search) ? " $condition (".
					" $table_brewing_panel.serial LIKE '%$search%'".
					" OR $table_brewing_panel.name LIKE '%$search%'".
					" OR $table_brewing_panel.ferment_date LIKE '%$search%'".
					" OR $table_brewing_panel.bottle_date LIKE '%$search%'".
					" OR $table_brewing_panel.type LIKE '%$search%'".
					" OR $table_brewing_panel.yeast LIKE '%$search%'".
					" OR $table_brewing_panel.og LIKE '%$search%'".
					" OR $table_brewing_panel.actual_fg LIKE '%$search%'".
					" OR $table_brewing_panel.stage LIKE '%$search%'".
					" )" : "");

			$user 		= 	get_user_by('slug', $search );

			$collab_query 	= ($user ?
						" $table_brew_collaborators.collaborator = '".$user->ID."'".
						" OR $table_brewing_panel.modify_id = '".$user->ID."'"
					: "");

			$owner_query	= ($user ?
						" OR $table_brew_owners.owner = '".$user->ID."'"
					: "");

			$search_user 	= ($user ?
						" LEFT OUTER JOIN $table_brew_owners".
						" ON( $table_brew_owners.serial = $table_brewing_panel.serial )".
						" LEFT OUTER JOIN $table_brew_collaborators".
						" ON( $table_brew_collaborators.serial = $table_brewing_panel.serial )".
						" WHERE ( $collab_query $owner_query )" : "$search_query");

			$recent_user_search = ($user ?
						"OR $collab_query"
						: "$search_query" );

			$mybrew_search_user 	= ($user ?
						" INNER JOIN $table_brew_collaborators".
						" ON( $table_brew_collaborators.serial = $table_brewing_panel.serial )".
						" WHERE ( $collab_query )" : "$search_query");

			switch($this->filter) {
				case 'recent' :
					$this->items = $wpdb->get_results(
						" SELECT $table_brewing_panel.* FROM $table_brewing_panel".
						" LEFT OUTER JOIN $table_brew_owners".
						" ON( $table_brew_owners.serial = $table_brewing_panel.serial )".
						" LEFT OUTER JOIN $table_brew_collaborators".
						" ON( $table_brew_collaborators.serial = $table_brewing_panel.serial )".
						" WHERE ( $table_brew_collaborators.collaborator = '".get_current_user_id()."'".
						" OR $table_brew_owners.owner = '".get_current_user_id()."'".
						" $recent_user_search )".
						" ORDER BY $table_brewing_panel.date DESC LIMIT 0,5",ARRAY_A); 
					$total = $wpdb->num_rows;
				break;
				case 'my_brews' :
					$total = $wpdb->get_var(
						" SELECT COUNT($table_brewing_panel.serial) FROM $table_brewing_panel ".
						" INNER JOIN $table_brew_owners".
						" ON( $table_brewing_panel.serial = $table_brew_owners.serial".
						" AND $table_brew_owners.owner = '".get_current_user_id()."' )".
						" $mybrew_search_user"
					);

					$this->items = $wpdb->get_results(
						" SELECT $table_brewing_panel.* FROM $table_brewing_panel ".
						" INNER JOIN $table_brew_owners".
						" ON( $table_brewing_panel.serial = $table_brew_owners.serial".
						" AND $table_brew_owners.owner = '".get_current_user_id()."' )".
						" $mybrew_search_user ORDER BY $table_brewing_panel.$sort $order LIMIT $offset,$limit",
					ARRAY_A);
				break;
				case 'collaborations' :
					$total = $wpdb->get_var(
						" SELECT COUNT($table_brewing_panel.serial) FROM $table_brewing_panel".
						" INNER JOIN $table_brew_collaborators". 
						" ON( $table_brewing_panel.serial = $table_brew_collaborators.serial".
						" AND $table_brew_collaborators.collaborator = '".get_current_user_id()."' )".
						" $search_user"
					);
					$this->items = $wpdb->get_results(
						" SELECT $table_brewing_panel.* FROM $table_brewing_panel".
						" INNER JOIN $table_brew_collaborators". 
						" ON( $table_brewing_panel.serial = $table_brew_collaborators.serial".
						" AND $table_brew_collaborators.collaborator = '".get_current_user_id()."' )".
						" $search_user ORDER BY $table_brewing_panel.$sort $order LIMIT $offset,$limit",
					ARRAY_A);
				break;
				case 'all_brews' :
					if( current_user_can('edit_brews') ) {
						$total = $wpdb->get_var(
							" SELECT COUNT($table_brewing_panel.serial) FROM $table_brewing_panel ".
							" $search_user");

						$this->items = $wpdb->get_results(
							" SELECT $table_brewing_panel.* FROM $table_brewing_panel ".
							" $search_user ORDER BY $sort $order LIMIT $offset,$limit ", ARRAY_A);
					} else $total = 0;
				break;
			}
			if( $total ) { // Unserialize the columns we need to use here
				for($i=0; $i<count($this->items); $i++ ) {
					$this->items[$i]['other'] = unserialize( $this->items[$i]['other'] );
				}
			}
		break;
		case 'ingredient' :
			// Ingredients Querying

			switch($this->filter) {
				case 'fermentables' :
					$search_query = ( !empty($search) ? " WHERE (".
						" id LIKE '%$search%'".
						" OR name LIKE '%$search%'".
						" OR type LIKE '%$search%'".
						" OR country LIKE '%$search%'".
						" OR category LIKE '%$search%'".
						" OR color LIKE '%$search%'".
						" OR ppg LIKE '%$search%' ) " : "");

					$total = $wpdb->get_var(
						" SELECT COUNT(`id`) FROM $table_brewing_ingredients ".
						" $search_query");

					$this->items = $wpdb->get_results(
						" SELECT * FROM $table_brewing_ingredients ".
						" $search_query ORDER BY $sort $order LIMIT $offset,$limit ", ARRAY_A);
				break;
				case 'hops' :
					$search_query = ( !empty($search) ? " WHERE (".
						" id LIKE '%$search%'".
						" OR name LIKE '%$search%'".
						" OR type LIKE '%$search%'".
						" OR alpha_acid LIKE '%$search%'".
						" OR beta_acid LIKE '%$search%'".
						" OR cohumulone LIKE '%$search%'".
						" OR myrcene_oil LIKE '%$search%'".
						" OR humulene_oil LIKE '%$search%'".
						" OR caryophyllene_oil LIKE '%$search%'".
						" OR farnesene_oil LIKE '%$search%') " : "");

					$total = $wpdb->get_var(
						" SELECT COUNT(`id`) FROM $table_brewing_hops ".
						" $search_query");

					$this->items = $wpdb->get_results(
						" SELECT * FROM $table_brewing_hops ".
						" $search_query ORDER BY $sort $order LIMIT $offset,$limit ", ARRAY_A);
				break;
			}
		break;
	}

        $this->set_pagination_args( array(
            'total_items' => $total, 
            'per_page'    => $limit,
            'total_pages' => ceil($total/$limit)
        ) );
    }

    function display() {
		wp_nonce_field( 'ajax-list-table-nonce', 'list_table_nonce' );
		parent::display();
    }

    function ajax_response() {
		check_ajax_referer( 'ajax-list-table-nonce', 'list_table_nonce' );
		$this->prepare_items();
		extract( $this->_args );
		extract( $this->_pagination_args, EXTR_SKIP );
		ob_start();
		if ( ! empty( $_REQUEST['no_placeholder'] ) )
			$this->display_rows();
		else
			$this->display_rows_or_placeholder();
		$rows = ob_get_clean();
		ob_start();
		$this->print_column_headers();
		$headers = ob_get_clean();
		ob_start();
		$this->pagination('top');
		$pagination_top = ob_get_clean();
		ob_start();
		$this->pagination('bottom');
		$pagination_bottom = ob_get_clean();
		$response = array( 'rows' => $rows );
		$response['pagination']['top'] = $pagination_top;
		$response['pagination']['bottom'] = $pagination_bottom;
		$response['column_headers'] = $headers;

		die( json_encode( $response ) );
    }
	/**
	 * Override pagination function to show the list type instead of 'items'.
	 */
	function pagination( $which ) {
		if ( empty( $this->_pagination_args ) ) {
			return;
		}

		$total_items = $this->_pagination_args['total_items'];
		$total_pages = $this->_pagination_args['total_pages'];
		$infinite_scroll = false;
		if ( isset( $this->_pagination_args['infinite_scroll'] ) ) {
			$infinite_scroll = $this->_pagination_args['infinite_scroll'];
		}

		if ( 'top' === $which && $total_pages > 1 ) {
			$this->screen->render_screen_reader_content( 'heading_pagination' );
		}
		switch($this->item_name) {
			case 'brew' :
				$item_string = $this->_args['singular'];
			break;
			case 'ingredient' :
				$item_string = substr($this->filter,0,-1);
			break;
		}
		$output = '<span class="displaying-num">' . sprintf( _n( '1 '.$item_string, '%s '.$item_string.'s', $total_items), number_format_i18n( $total_items )  ) . '</span>';

		$current = $this->get_pagenum();

		$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );

		$current_url = remove_query_arg( array( 'hotkeys_highlight_last', 'hotkeys_highlight_first' ), $current_url );

		$page_links = array();

		$total_pages_before = '<span class="paging-input">';
		$total_pages_after  = '</span>';

		$disable_first = $disable_last = $disable_prev = $disable_next = false;

 		if ( $current == 1 ) {
			$disable_first = true;
			$disable_prev = true;
 		}
		if ( $current == 2 ) {
			$disable_first = true;
		}
 		if ( $current == $total_pages ) {
			$disable_last = true;
			$disable_next = true;
 		}
		if ( $current == $total_pages - 1 ) {
			$disable_last = true;
		}

		if ( $disable_first ) {
			$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&laquo;</span>';
		} else {
			$page_links[] = sprintf( "<a class='first-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( remove_query_arg( 'paged', $current_url ) ),
				__( 'First page' ),
				'&laquo;'
			);
		}

		if ( $disable_prev ) {
			$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&lsaquo;</span>';
		} else {
			$page_links[] = sprintf( "<a class='prev-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( add_query_arg( 'paged', max( 1, $current-1 ), $current_url ) ),
				__( 'Previous page' ),
				'&lsaquo;'
			);
		}

		if ( 'bottom' === $which ) {
			$html_current_page  = $current;
			$total_pages_before = '<span class="screen-reader-text">' . __( 'Current Page' ) . '</span><span id="table-paging" class="paging-input">';
		} else {
			$html_current_page = sprintf( "%s<input class='current-page' id='current-page-selector' type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' />",
				'<label for="current-page-selector" class="screen-reader-text">' . __( 'Current Page' ) . '</label>',
				$current,
				strlen( $total_pages )
			);
		}
		$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
		$page_links[] = $total_pages_before . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . $total_pages_after;

		if ( $disable_next ) {
			$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&rsaquo;</span>';
		} else {
			$page_links[] = sprintf( "<a class='next-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( add_query_arg( 'paged', min( $total_pages, $current+1 ), $current_url ) ),
				__( 'Next page' ),
				'&rsaquo;'
			);
		}

		if ( $disable_last ) {
			$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&raquo;</span>';
		} else {
			$page_links[] = sprintf( "<a class='last-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( add_query_arg( 'paged', $total_pages, $current_url ) ),
				__( 'Last page' ),
				'&raquo;'
			);
		}

		$pagination_links_class = 'pagination-links';
		if ( ! empty( $infinite_scroll ) ) {
			$pagination_links_class = ' hide-if-js';
		}
		$output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

		if ( $total_pages ) {
			$page_class = $total_pages < 2 ? ' one-page' : '';
		} else {
			$page_class = ' no-pages';
		}
		$this->_pagination = "<div class='pagination'><div class='tablenav-pages{$page_class}'>$output</div></div>";

		echo $this->_pagination;
	}
}
/**
 * Administration API: WP_Core_List_Table class
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 */

/**
 * Base class for displaying a list of items in an ajaxified HTML table.
 *
 * @since 3.1.0
 * @access private
 */
class WP_Core_List_Table {

	/**
	 * The current list of items.
	 *
	 * @since 3.1.0
	 * @access public
	 * @var array
	 */
	public $items;

	/**
	 * Various information about the current table.
	 *
	 * @since 3.1.0
	 * @access protected
	 * @var array
	 */
	protected $_args;

	/**
	 * Various information needed for displaying the pagination.
	 *
	 * @since 3.1.0
	 * @access protected
	 * @var array
	 */
	protected $_pagination_args = array();

	/**
	 * The current screen.
	 *
	 * @since 3.1.0
	 * @access protected
	 * @var object
	 */
	protected $screen;

	/**
	 * Cached bulk actions.
	 *
	 * @since 3.1.0
	 * @access private
	 * @var array
	 */
	private $_actions;

	/**
	 * Cached pagination output.
	 *
	 * @since 3.1.0
	 * @access private
	 * @var string
	 */
	private $_pagination;

	/**
	 * The view switcher modes.
	 *
	 * @since 4.1.0
	 * @access protected
	 * @var array
	 */
	protected $modes = array();

	/**
	 * Stores the value returned by ->get_column_info().
	 *
	 * @since 4.1.0
	 * @access protected
	 * @var array
	 */
	protected $_column_headers;

	/**
	 * {@internal Missing Summary}
	 *
	 * @access protected
	 * @var array
	 */
	protected $compat_fields = array( '_args', '_pagination_args', 'screen', '_actions', '_pagination' );

	/**
	 * {@internal Missing Summary}
	 *
	 * @access protected
	 * @var array
	 */
	protected $compat_methods = array( 'set_pagination_args', 'get_views', 'get_bulk_actions', 'bulk_actions',
		'row_actions', 'months_dropdown', 'view_switcher', 'comments_bubble', 'get_items_per_page', 'pagination',
		'get_sortable_columns', 'get_column_info', 'get_table_classes', 'display_tablenav', 'extra_tablenav',
		'single_row_columns' );

	/**
	 * Constructor.
	 *
	 * The child class should call this constructor from its own constructor to override
	 * the default $args.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @param array|string $args {
	 *     Array or string of arguments.
	 *
	 *     @type string $plural   Plural value used for labels and the objects being listed.
	 *                            This affects things such as CSS class-names and nonces used
	 *                            in the list table, e.g. 'posts'. Default empty.
	 *     @type string $singular Singular label for an object being listed, e.g. 'post'.
	 *                            Default empty
	 *     @type bool   $ajax     Whether the list table supports AJAX. This includes loading
	 *                            and sorting data, for example. If true, the class will call
	 *                            the {@see _js_vars()} method in the footer to provide variables
	 *                            to any scripts handling AJAX events. Default false.
	 *     @type string $screen   String containing the hook name used to determine the current
	 *                            screen. If left null, the current screen will be automatically set.
	 *                            Default null.
	 * }
	 */
	public function __construct( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'plural' => '',
			'singular' => '',
			'ajax' => false,
			'screen' => null,
		) );

		$this->screen = convert_to_screen( $args['screen'] );

		add_filter( "manage_{$this->screen->id}_columns", array( $this, 'get_columns' ), 0 );

		if ( !$args['plural'] )
			$args['plural'] = $this->screen->base;

		$args['plural'] = sanitize_key( $args['plural'] );
		$args['singular'] = sanitize_key( $args['singular'] );

		$this->_args = $args;

		if ( $args['ajax'] ) {
			// wp_enqueue_script( 'list-table' );
			add_action( 'admin_footer', array( $this, '_js_vars' ) );
		}

		if ( empty( $this->modes ) ) {
			$this->modes = array(
				'list'    => __( 'List View' ),
				'excerpt' => __( 'Excerpt View' )
			);
		}
	}

	/**
	 * Make private properties readable for backwards compatibility.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $name Property to get.
	 * @return mixed Property.
	 */
	public function __get( $name ) {
		if ( in_array( $name, $this->compat_fields ) ) {
			return $this->$name;
		}
	}

	/**
	 * Make private properties settable for backwards compatibility.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $name  Property to check if set.
	 * @param mixed  $value Property value.
	 * @return mixed Newly-set property.
	 */
	public function __set( $name, $value ) {
		if ( in_array( $name, $this->compat_fields ) ) {
			return $this->$name = $value;
		}
	}

	/**
	 * Make private properties checkable for backwards compatibility.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $name Property to check if set.
	 * @return bool Whether the property is set.
	 */
	public function __isset( $name ) {
		if ( in_array( $name, $this->compat_fields ) ) {
			return isset( $this->$name );
		}
	}

	/**
	 * Make private properties un-settable for backwards compatibility.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $name Property to unset.
	 */
	public function __unset( $name ) {
		if ( in_array( $name, $this->compat_fields ) ) {
			unset( $this->$name );
		}
	}

	/**
	 * Make private/protected methods readable for backwards compatibility.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param callable $name      Method to call.
	 * @param array    $arguments Arguments to pass when calling.
	 * @return mixed|bool Return value of the callback, false otherwise.
	 */
	public function __call( $name, $arguments ) {
		if ( in_array( $name, $this->compat_methods ) ) {
			return call_user_func_array( array( $this, $name ), $arguments );
		}
		return false;
	}

	/**
	 * Checks the current user's permissions
	 *
	 * @since 3.1.0
	 * @access public
	 * @abstract
	 */
	public function ajax_user_can() {
		die( 'function WP_Core_List_Table::ajax_user_can() must be over-ridden in a sub-class.' );
	}

	/**
	 * Prepares the list of items for displaying.
	 * @uses WP_Core_List_Table::set_pagination_args()
	 *
	 * @since 3.1.0
	 * @access public
	 * @abstract
	 */
	public function prepare_items() {
		die( 'function WP_Core_List_Table::prepare_items() must be over-ridden in a sub-class.' );
	}

	/**
	 * An internal method that sets all the necessary pagination arguments
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param array|string $args Array or string of arguments with information about the pagination.
	 */
	protected function set_pagination_args( $args ) {
		$args = wp_parse_args( $args, array(
			'total_items' => 0,
			'total_pages' => 0,
			'per_page' => 0,
		) );

		if ( !$args['total_pages'] && $args['per_page'] > 0 )
			$args['total_pages'] = ceil( $args['total_items'] / $args['per_page'] );

		// Redirect if page number is invalid and headers are not already sent.
		if ( ! headers_sent() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) && $args['total_pages'] > 0 && $this->get_pagenum() > $args['total_pages'] ) {
			wp_redirect( add_query_arg( 'paged', $args['total_pages'] ) );
			exit;
		}

		$this->_pagination_args = $args;
	}

	/**
	 * Access the pagination args.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @param string $key Pagination argument to retrieve. Common values include 'total_items',
	 *                    'total_pages', 'per_page', or 'infinite_scroll'.
	 * @return int Number of items that correspond to the given pagination argument.
	 */
	public function get_pagination_arg( $key ) {
		if ( 'page' === $key ) {
			return $this->get_pagenum();
		}

		if ( isset( $this->_pagination_args[$key] ) ) {
			return $this->_pagination_args[$key];
		}
	}

	/**
	 * Whether the table has items to display or not
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @return bool
	 */
	public function has_items() {
		return !empty( $this->items );
	}

	/**
	 * Message to be displayed when there are no items
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function no_items() {
		_e( 'No items found.' );
	}

	/**
	 * Display the search box.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @param string $text The search button text
	 * @param string $input_id The search input id
	 */
	public function search_box( $text, $input_id ) {
		if ( empty( $_REQUEST['s'] ) && !$this->has_items() )
			return;

		$input_id = $input_id . '-search-input';

		if ( ! empty( $_REQUEST['orderby'] ) )
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		if ( ! empty( $_REQUEST['order'] ) )
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
		if ( ! empty( $_REQUEST['post_mime_type'] ) )
			echo '<input type="hidden" name="post_mime_type" value="' . esc_attr( $_REQUEST['post_mime_type'] ) . '" />';
		if ( ! empty( $_REQUEST['detached'] ) )
			echo '<input type="hidden" name="detached" value="' . esc_attr( $_REQUEST['detached'] ) . '" />';
?>
<p class="search-box">
	<label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
	<input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>" />
	<?php submit_button( $text, 'button', '', false, array('id' => 'search-submit') ); ?>
</p>
<?php
	}

	/**
	 * Get an associative array ( id => link ) with the list
	 * of views available on this table.
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @return array
	 */
	protected function get_views() {
		return array();
	}

	/**
	 * Display the list of views available on this table.
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function views() {
		$views = $this->get_views();
		/**
		 * Filter the list of available list table views.
		 *
		 * The dynamic portion of the hook name, `$this->screen->id`, refers
		 * to the ID of the current screen, usually a string.
		 *
		 * @since 3.5.0
		 *
		 * @param array $views An array of available list table views.
		 */
		$views = apply_filters( "views_{$this->screen->id}", $views );

		if ( empty( $views ) )
			return;

		$this->screen->render_screen_reader_content( 'heading_views' );

		echo "<ul class='subsubsub'>\n";
		foreach ( $views as $class => $view ) {
			$views[ $class ] = "\t<li class='$class'>$view";
		}
		echo implode( " |</li>\n", $views ) . "</li>\n";
		echo "</ul>";
	}

	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @return array
	 */
	protected function get_bulk_actions() {
		return array();
	}

	/**
	 * Display the bulk actions dropdown.
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param string $which The location of the bulk actions: 'top' or 'bottom'.
	 *                      This is designated as optional for backwards-compatibility.
	 */
	protected function bulk_actions( $which = '' ) {
		if ( is_null( $this->_actions ) ) {
			$no_new_actions = $this->_actions = $this->get_bulk_actions();
			/**
			 * Filter the list table Bulk Actions drop-down.
			 *
			 * The dynamic portion of the hook name, `$this->screen->id`, refers
			 * to the ID of the current screen, usually a string.
			 *
			 * This filter can currently only be used to remove bulk actions.
			 *
			 * @since 3.5.0
			 *
			 * @param array $actions An array of the available bulk actions.
			 */
			$this->_actions = apply_filters( "bulk_actions-{$this->screen->id}", $this->_actions );
			$this->_actions = array_intersect_assoc( $this->_actions, $no_new_actions );
			$two = '';
		} else {
			$two = '2';
		}

		if ( empty( $this->_actions ) )
			return;

		echo '<label for="bulk-action-selector-' . esc_attr( $which ) . '" class="screen-reader-text">' . __( 'Select bulk action' ) . '</label>';
		echo '<select name="action' . $two . '" id="bulk-action-selector-' . esc_attr( $which ) . "\">\n";
		echo '<option value="-1">' . __( 'Bulk Actions' ) . "</option>\n";

		foreach ( $this->_actions as $name => $title ) {
			$class = 'edit' === $name ? ' class="hide-if-no-js"' : '';

			echo "\t" . '<option value="' . $name . '"' . $class . '>' . $title . "</option>\n";
		}

		echo "</select>\n";

		submit_button( __( 'Apply' ), 'action', '', false, array( 'id' => "doaction$two" ) );
		echo "\n";
	}

	/**
	 * Get the current action selected from the bulk actions dropdown.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @return string|false The action name or False if no action was selected
	 */
	public function current_action() {
		if ( isset( $_REQUEST['filter_action'] ) && ! empty( $_REQUEST['filter_action'] ) )
			return false;

		if ( isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] )
			return $_REQUEST['action'];

		if ( isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] )
			return $_REQUEST['action2'];

		return false;
	}

	/**
	 * Generate row actions div
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param array $actions The list of actions
	 * @param bool $always_visible Whether the actions should be always visible
	 * @return string
	 */
	protected function row_actions( $actions, $always_visible = false ) {
		$action_count = count( $actions );
		$i = 0;

		if ( !$action_count )
			return '';

		$out = '<div class="' . ( $always_visible ? 'row-actions visible' : 'row-actions' ) . '">';
		foreach ( $actions as $action => $link ) {
			++$i;
			( $i == $action_count ) ? $sep = '' : $sep = ' | ';
			$out .= "<span class='$action'>$link$sep</span>";
		}
		$out .= '</div>';

		$out .= '<button type="button" class="toggle-row"><span class="screen-reader-text">' . __( 'Show more details' ) . '</span></button>';

		return $out;
	}

	/**
	 * Display a monthly dropdown for filtering items
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @global wpdb      $wpdb
	 * @global WP_Locale $wp_locale
	 *
	 * @param string $post_type
	 */
	protected function months_dropdown( $post_type ) {
		global $wpdb, $wp_locale;

		/**
		 * Filter whether to remove the 'Months' drop-down from the post list table.
		 *
		 * @since 4.2.0
		 *
		 * @param bool   $disable   Whether to disable the drop-down. Default false.
		 * @param string $post_type The post type.
		 */
		if ( apply_filters( 'disable_months_dropdown', false, $post_type ) ) {
			return;
		}

		$extra_checks = "AND post_status != 'auto-draft'";
		if ( ! isset( $_GET['post_status'] ) || 'trash' !== $_GET['post_status'] ) {
			$extra_checks .= " AND post_status != 'trash'";
		} elseif ( isset( $_GET['post_status'] ) ) {
			$extra_checks = $wpdb->prepare( ' AND post_status = %s', $_GET['post_status'] );
		}

		$months = $wpdb->get_results( $wpdb->prepare( "
			SELECT DISTINCT YEAR( post_date ) AS year, MONTH( post_date ) AS month
			FROM $wpdb->posts
			WHERE post_type = %s
			$extra_checks
			ORDER BY post_date DESC
		", $post_type ) );

		/**
		 * Filter the 'Months' drop-down results.
		 *
		 * @since 3.7.0
		 *
		 * @param object $months    The months drop-down query results.
		 * @param string $post_type The post type.
		 */
		$months = apply_filters( 'months_dropdown_results', $months, $post_type );

		$month_count = count( $months );

		if ( !$month_count || ( 1 == $month_count && 0 == $months[0]->month ) )
			return;

		$m = isset( $_GET['m'] ) ? (int) $_GET['m'] : 0;
?>
		<label for="filter-by-date" class="screen-reader-text"><?php _e( 'Filter by date' ); ?></label>
		<select name="m" id="filter-by-date">
			<option<?php selected( $m, 0 ); ?> value="0"><?php _e( 'All dates' ); ?></option>
<?php
		foreach ( $months as $arc_row ) {
			if ( 0 == $arc_row->year )
				continue;

			$month = zeroise( $arc_row->month, 2 );
			$year = $arc_row->year;

			printf( "<option %s value='%s'>%s</option>\n",
				selected( $m, $year . $month, false ),
				esc_attr( $arc_row->year . $month ),
				/* translators: 1: month name, 2: 4-digit year */
				sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $month ), $year )
			);
		}
?>
		</select>
<?php
	}

	/**
	 * Display a view switcher
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param string $current_mode
	 */
	protected function view_switcher( $current_mode ) {
?>
		<input type="hidden" name="mode" value="<?php echo esc_attr( $current_mode ); ?>" />
		<div class="view-switch">
<?php
			foreach ( $this->modes as $mode => $title ) {
				$classes = array( 'view-' . $mode );
				if ( $current_mode === $mode )
					$classes[] = 'current';
				printf(
					"<a href='%s' class='%s' id='view-switch-$mode'><span class='screen-reader-text'>%s</span></a>\n",
					esc_url( add_query_arg( 'mode', $mode ) ),
					implode( ' ', $classes ),
					$title
				);
			}
		?>
		</div>
<?php
	}

	/**
	 * Display a comment count bubble
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param int $post_id          The post ID.
	 * @param int $pending_comments Number of pending comments.
	 */
	protected function comments_bubble( $post_id, $pending_comments ) {
		$approved_comments = get_comments_number();

		$approved_comments_number = number_format_i18n( $approved_comments );
		$pending_comments_number = number_format_i18n( $pending_comments );

		$approved_only_phrase = sprintf( _n( '%s comment', '%s comments', $approved_comments ), $approved_comments_number );
		$approved_phrase = sprintf( _n( '%s approved comment', '%s approved comments', $approved_comments ), $approved_comments_number );
		$pending_phrase = sprintf( _n( '%s pending comment', '%s pending comments', $pending_comments ), $pending_comments_number );

		// No comments at all.
		if ( ! $approved_comments && ! $pending_comments ) {
			printf( '<span aria-hidden="true">—</span><span class="screen-reader-text">%s</span>',
				__( 'No comments' )
			);
		// Approved comments have different display depending on some conditions.
		} elseif ( $approved_comments ) {
			printf( '<a href="%s" class="post-com-count post-com-count-approved"><span class="comment-count-approved" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></a>',
				esc_url( add_query_arg( array( 'p' => $post_id, 'comment_status' => 'approved' ), admin_url( 'edit-comments.php' ) ) ),
				$approved_comments_number,
				$pending_comments ? $approved_phrase : $approved_only_phrase
			);
		} else {
			printf( '<span class="post-com-count post-com-count-no-comments"><span class="comment-count comment-count-no-comments" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></span>',
				$approved_comments_number,
				$pending_comments ? __( 'No approved comments' ) : __( 'No comments' )
			);
		}

		if ( $pending_comments ) {
			printf( '<a href="%s" class="post-com-count post-com-count-pending"><span class="comment-count-pending" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></a>',
				esc_url( add_query_arg( array( 'p' => $post_id, 'comment_status' => 'moderated' ), admin_url( 'edit-comments.php' ) ) ),
				$pending_comments_number,
				$pending_phrase
			);
		} else {
			printf( '<span class="post-com-count post-com-count-pending post-com-count-no-pending"><span class="comment-count comment-count-no-pending" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></span>',
				$pending_comments_number,
				$approved_comments ? __( 'No pending comments' ) : __( 'No comments' )
			);
		}
	}

	/**
	 * Get the current page number
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @return int
	 */
	public function get_pagenum() {
		$pagenum = isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 0;

		if ( isset( $this->_pagination_args['total_pages'] ) && $pagenum > $this->_pagination_args['total_pages'] )
			$pagenum = $this->_pagination_args['total_pages'];

		return max( 1, $pagenum );
	}

	/**
	 * Get number of items to display on a single page
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param string $option
	 * @param int    $default
	 * @return int
	 */
	protected function get_items_per_page( $option, $default = 20 ) {
		$per_page = (int) get_user_option( $option );
		if ( empty( $per_page ) || $per_page < 1 )
			$per_page = $default;

		/**
		 * Filter the number of items to be displayed on each page of the list table.
		 *
		 * The dynamic hook name, $option, refers to the `per_page` option depending
		 * on the type of list table in use. Possible values include: 'edit_comments_per_page',
		 * 'sites_network_per_page', 'site_themes_network_per_page', 'themes_network_per_page',
		 * 'users_network_per_page', 'edit_post_per_page', 'edit_page_per_page',
		 * 'edit_{$post_type}_per_page', etc.
		 *
		 * @since 2.9.0
		 *
		 * @param int $per_page Number of items to be displayed. Default 20.
		 */
		return (int) apply_filters( $option, $per_page );
	}

	/**
	 * Display the pagination.
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param string $which
	 */
	protected function pagination( $which ) {
		if ( empty( $this->_pagination_args ) ) {
			return;
		}

		$total_items = $this->_pagination_args['total_items'];
		$total_pages = $this->_pagination_args['total_pages'];
		$infinite_scroll = false;
		if ( isset( $this->_pagination_args['infinite_scroll'] ) ) {
			$infinite_scroll = $this->_pagination_args['infinite_scroll'];
		}

		if ( 'top' === $which && $total_pages > 1 ) {
			$this->screen->render_screen_reader_content( 'heading_pagination' );
		}

		$output = '<span class="displaying-num">' . sprintf( _n( '%s item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

		$current = $this->get_pagenum();

		$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );

		$current_url = remove_query_arg( array( 'hotkeys_highlight_last', 'hotkeys_highlight_first' ), $current_url );

		$page_links = array();

		$total_pages_before = '<span class="paging-input">';
		$total_pages_after  = '</span>';

		$disable_first = $disable_last = $disable_prev = $disable_next = false;

 		if ( $current == 1 ) {
			$disable_first = true;
			$disable_prev = true;
 		}
		if ( $current == 2 ) {
			$disable_first = true;
		}
 		if ( $current == $total_pages ) {
			$disable_last = true;
			$disable_next = true;
 		}
		if ( $current == $total_pages - 1 ) {
			$disable_last = true;
		}

		if ( $disable_first ) {
			$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&laquo;</span>';
		} else {
			$page_links[] = sprintf( "<a class='first-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( remove_query_arg( 'paged', $current_url ) ),
				__( 'First page' ),
				'&laquo;'
			);
		}

		if ( $disable_prev ) {
			$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&lsaquo;</span>';
		} else {
			$page_links[] = sprintf( "<a class='prev-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( add_query_arg( 'paged', max( 1, $current-1 ), $current_url ) ),
				__( 'Previous page' ),
				'&lsaquo;'
			);
		}

		if ( 'bottom' === $which ) {
			$html_current_page  = $current;
			$total_pages_before = '<span class="screen-reader-text">' . __( 'Current Page' ) . '</span><span id="table-paging" class="paging-input">';
		} else {
			$html_current_page = sprintf( "%s<input class='current-page' id='current-page-selector' type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' />",
				'<label for="current-page-selector" class="screen-reader-text">' . __( 'Current Page' ) . '</label>',
				$current,
				strlen( $total_pages )
			);
		}
		$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
		$page_links[] = $total_pages_before . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . $total_pages_after;

		if ( $disable_next ) {
			$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&rsaquo;</span>';
		} else {
			$page_links[] = sprintf( "<a class='next-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( add_query_arg( 'paged', min( $total_pages, $current+1 ), $current_url ) ),
				__( 'Next page' ),
				'&rsaquo;'
			);
		}

		if ( $disable_last ) {
			$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&raquo;</span>';
		} else {
			$page_links[] = sprintf( "<a class='last-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( add_query_arg( 'paged', $total_pages, $current_url ) ),
				__( 'Last page' ),
				'&raquo;'
			);
		}

		$pagination_links_class = 'pagination-links';
		if ( ! empty( $infinite_scroll ) ) {
			$pagination_links_class = ' hide-if-js';
		}
		$output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

		if ( $total_pages ) {
			$page_class = $total_pages < 2 ? ' one-page' : '';
		} else {
			$page_class = ' no-pages';
		}
		$this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

		echo $this->_pagination;
	}

	/**
	 * Get a list of columns. The format is:
	 * 'internal-name' => 'Title'
	 *
	 * @since 3.1.0
	 * @access public
	 * @abstract
	 *
	 * @return array
	 */
	public function get_columns() {
		die( 'function WP_Core_List_Table::get_columns() must be over-ridden in a sub-class.' );
	}

	/**
	 * Get a list of sortable columns. The format is:
	 * 'internal-name' => 'orderby'
	 * or
	 * 'internal-name' => array( 'orderby', true )
	 *
	 * The second format will make the initial sorting order be descending
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @return array
	 */
	protected function get_sortable_columns() {
		return array();
	}

	/**
	 * Gets the name of the default primary column.
	 *
	 * @since 4.3.0
	 * @access protected
	 *
	 * @return string Name of the default primary column, in this case, an empty string.
	 */
	protected function get_default_primary_column_name() {
		$columns = $this->get_columns();
		$column = '';

		if ( empty( $columns ) ) {
			return $column;
		}

		// We need a primary defined so responsive views show something,
		// so let's fall back to the first non-checkbox column.
		foreach ( $columns as $col => $column_name ) {
			if ( 'cb' === $col ) {
				continue;
			}

			$column = $col;
			break;
		}

		return $column;
	}

	/**
	 * Public wrapper for WP_Core_List_Table::get_default_primary_column_name().
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return string Name of the default primary column.
	 */
	public function get_primary_column() {
		return $this->get_primary_column_name();
	}

	/**
	 * Gets the name of the primary column.
	 *
	 * @since 4.3.0
	 * @access protected
	 *
	 * @return string The name of the primary column.
	 */
	protected function get_primary_column_name() {
		$columns = get_column_headers( $this->screen );
		$default = $this->get_default_primary_column_name();

		// If the primary column doesn't exist fall back to the
		// first non-checkbox column.
		if ( ! isset( $columns[ $default ] ) ) {
			$default = WP_Core_List_Table::get_default_primary_column_name();
		}

		/**
		 * Filter the name of the primary column for the current list table.
		 *
		 * @since 4.3.0
		 *
		 * @param string $default Column name default for the specific list table, e.g. 'name'.
		 * @param string $context Screen ID for specific list table, e.g. 'plugins'.
		 */
		$column  = apply_filters( 'list_table_primary_column', $default, $this->screen->id );

		if ( empty( $column ) || ! isset( $columns[ $column ] ) ) {
			$column = $default;
		}

		return $column;
	}

	/**
	 * Get a list of all, hidden and sortable columns, with filter applied
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @return array
	 */
	protected function get_column_info() {
		// $_column_headers is already set / cached
		if ( isset( $this->_column_headers ) && is_array( $this->_column_headers ) ) {
			// Back-compat for list tables that have been manually setting $_column_headers for horse reasons.
			// In 4.3, we added a fourth argument for primary column.
			$column_headers = array( array(), array(), array(), $this->get_primary_column_name() );
			foreach ( $this->_column_headers as $key => $value ) {
				$column_headers[ $key ] = $value;
			}

			return $column_headers;
		}

		$columns = get_column_headers( $this->screen );
		$hidden = get_hidden_columns( $this->screen );

		$sortable_columns = $this->get_sortable_columns();
		/**
		 * Filter the list table sortable columns for a specific screen.
		 *
		 * The dynamic portion of the hook name, `$this->screen->id`, refers
		 * to the ID of the current screen, usually a string.
		 *
		 * @since 3.5.0
		 *
		 * @param array $sortable_columns An array of sortable columns.
		 */
		$_sortable = apply_filters( "manage_{$this->screen->id}_sortable_columns", $sortable_columns );

		$sortable = array();
		foreach ( $_sortable as $id => $data ) {
			if ( empty( $data ) )
				continue;

			$data = (array) $data;
			if ( !isset( $data[1] ) )
				$data[1] = false;

			$sortable[$id] = $data;
		}

		$primary = $this->get_primary_column_name();
		$this->_column_headers = array( $columns, $hidden, $sortable, $primary );

		return $this->_column_headers;
	}

	/**
	 * Return number of visible columns
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @return int
	 */
	public function get_column_count() {
		list ( $columns, $hidden ) = $this->get_column_info();
		$hidden = array_intersect( array_keys( $columns ), array_filter( $hidden ) );
		return count( $columns ) - count( $hidden );
	}

	/**
	 * Print column headers, accounting for hidden and sortable columns.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @staticvar int $cb_counter
	 *
	 * @param bool $with_id Whether to set the id attribute or not
	 */
	public function print_column_headers( $with_id = true ) {
		list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

		$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		$current_url = remove_query_arg( 'paged', $current_url );

		if ( isset( $_GET['orderby'] ) ) {
			$current_orderby = $_GET['orderby'];
		} else {
			$current_orderby = '';
		}

		if ( isset( $_GET['order'] ) && 'desc' === $_GET['order'] ) {
			$current_order = 'desc';
		} else {
			$current_order = 'asc';
		}

		if ( ! empty( $columns['cb'] ) ) {
			static $cb_counter = 1;
			$columns['cb'] = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' . __( 'Select All' ) . '</label>'
				. '<input id="cb-select-all-' . $cb_counter . '" type="checkbox" />';
			$cb_counter++;
		}

		foreach ( $columns as $column_key => $column_display_name ) {
			$class = array( 'manage-column', "column-$column_key" );

			if ( in_array( $column_key, $hidden ) ) {
				$class[] = 'hidden';
			}

			if ( 'cb' === $column_key )
				$class[] = 'check-column';
			elseif ( in_array( $column_key, array( 'posts', 'comments', 'links' ) ) )
				$class[] = 'num';

			if ( $column_key === $primary ) {
				$class[] = 'column-primary';
			}

			if ( isset( $sortable[$column_key] ) ) {
				list( $orderby, $desc_first ) = $sortable[$column_key];

				if ( $current_orderby === $orderby ) {
					$order = 'asc' === $current_order ? 'desc' : 'asc';
					$class[] = 'sorted';
					$class[] = $current_order;
				} else {
					$order = $desc_first ? 'desc' : 'asc';
					$class[] = 'sortable';
					$class[] = $desc_first ? 'asc' : 'desc';
				}

				$column_display_name = '<a href="' . esc_url( add_query_arg( compact( 'orderby', 'order' ), $current_url ) ) . '"><span>' . $column_display_name . '</span><span class="sorting-indicator"></span></a>';
			}

			$tag = ( 'cb' === $column_key ) ? 'td' : 'th';
			$scope = ( 'th' === $tag ) ? 'scope="col"' : '';
			$id = $with_id ? "id='$column_key'" : '';

			if ( !empty( $class ) )
				$class = "class='" . join( ' ', $class ) . "'";

			echo "<$tag $scope $id $class>$column_display_name</$tag>";
		}
	}

	/**
	 * Display the table
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function display() {
		$singular = $this->_args['singular'];

		$this->display_tablenav( 'top' );

		$this->screen->render_screen_reader_content( 'heading_list' );
?>
<table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>">
	<thead>
	<tr>
		<?php $this->print_column_headers(); ?>
	</tr>
	</thead>

	<tbody id="the-list"<?php
		if ( $singular ) {
			echo " data-wp-lists='list:$singular'";
		} ?>>
		<?php $this->display_rows_or_placeholder(); ?>
	</tbody>

	<tfoot>
	<tr>
		<?php $this->print_column_headers( false ); ?>
	</tr>
	</tfoot>

</table>
<?php
		$this->display_tablenav( 'bottom' );
	}

	/**
	 * Get a list of CSS classes for the list table table tag.
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @return array List of CSS classes for the table tag.
	 */
	protected function get_table_classes() {
		return array( 'widefat', 'fixed', 'striped', $this->_args['plural'] );
	}

	/**
	 * Generate the table navigation above or below the table
	 *
	 * @since 3.1.0
	 * @access protected
	 * @param string $which
	 */
	protected function display_tablenav( $which ) {
		if ( 'top' === $which ) {
			wp_nonce_field( 'bulk-' . $this->_args['plural'] );
		}
		?>
	<div class="tablenav <?php echo esc_attr( $which ); ?>">

		<?php if ( $this->has_items() ): ?>
		<div class="alignleft actions bulkactions">
			<?php $this->bulk_actions( $which ); ?>
		</div>
		<?php endif;
		$this->extra_tablenav( $which );
		$this->pagination( $which );
?>

		<br class="clear" />
	</div>
<?php
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param string $which
	 */
	protected function extra_tablenav( $which ) {}

	/**
	 * Generate the tbody element for the list table.
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function display_rows_or_placeholder() {
		if ( $this->has_items() ) {
			$this->display_rows();
		} else {
			echo '<tr class="no-items"><td class="colspanchange" colspan="' . $this->get_column_count() . '">';
			$this->no_items();
			echo '</td></tr>';
		}
	}

	/**
	 * Generate the table rows
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function display_rows() {
		foreach ( $this->items as $item )
			$this->single_row( $item );
	}

	/**
	 * Generates content for a single row of the table
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @param object $item The current item
	 */
	public function single_row( $item ) {
		echo '<tr>';
		$this->single_row_columns( $item );
		echo '</tr>';
	}

	/**
	 *
	 * @param object $item
	 * @param string $column_name
	 */
	protected function column_default( $item, $column_name ) {}

	/**
	 *
	 * @param object $item
	 */
	protected function column_cb( $item ) {}

	/**
	 * Generates the columns for a single row of the table
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param object $item The current item
	 */
	protected function single_row_columns( $item ) {
		list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			$classes = "$column_name column-$column_name";
			if ( $primary === $column_name ) {
				$classes .= ' has-row-actions column-primary';
			}

			if ( in_array( $column_name, $hidden ) ) {
				$classes .= ' hidden';
			}

			// Comments column uses HTML in the display name with screen reader text.
			// Instead of using esc_attr(), we strip tags to get closer to a user-friendly string.
			$data = 'data-colname="' . wp_strip_all_tags( $column_display_name ) . '"';

			$attributes = "class='$classes' $data";

			if ( 'cb' === $column_name ) {
				echo '<th scope="row" class="check-column">';
				echo $this->column_cb( $item );
				echo '</th>';
			} elseif ( method_exists( $this, '_column_' . $column_name ) ) {
				echo call_user_func(
					array( $this, '_column_' . $column_name ),
					$item,
					$classes,
					$data,
					$primary
				);
			} elseif ( method_exists( $this, 'column_' . $column_name ) ) {
				echo "<td $attributes>";
				echo call_user_func( array( $this, 'column_' . $column_name ), $item );
				echo $this->handle_row_actions( $item, $column_name, $primary );
				echo "</td>";
			} else {
				echo "<td $attributes>";
				echo $this->column_default( $item, $column_name );
				echo $this->handle_row_actions( $item, $column_name, $primary );
				echo "</td>";
			}
		}
	}

	/**
	 * Generates and display row actions links for the list table.
	 *
	 * @since 4.3.0
	 * @access protected
	 *
	 * @param object $item        The item being acted upon.
	 * @param string $column_name Current column name.
	 * @param string $primary     Primary column name.
	 * @return string The row actions HTML, or an empty string if the current column is the primary column.
	 */
	protected function handle_row_actions( $item, $column_name, $primary ) {
		return $column_name === $primary ? '<button type="button" class="toggle-row"><span class="screen-reader-text">' . __( 'Show more details' ) . '</span></button>' : '';
 	}

	/**
	 * Handle an incoming ajax request (called from admin-ajax.php)
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function ajax_response() {
		$this->prepare_items();

		ob_start();
		if ( ! empty( $_REQUEST['no_placeholder'] ) ) {
			$this->display_rows();
		} else {
			$this->display_rows_or_placeholder();
		}

		$rows = ob_get_clean();

		$response = array( 'rows' => $rows );

		if ( isset( $this->_pagination_args['total_items'] ) ) {
			$response['total_items_i18n'] = sprintf(
				_n( '%s item', '%s items', $this->_pagination_args['total_items'] ),
				number_format_i18n( $this->_pagination_args['total_items'] )
			);
		}
		if ( isset( $this->_pagination_args['total_pages'] ) ) {
			$response['total_pages'] = $this->_pagination_args['total_pages'];
			$response['total_pages_i18n'] = number_format_i18n( $this->_pagination_args['total_pages'] );
		}

		die( wp_json_encode( $response ) );
	}

	/**
	 * Send required variables to JavaScript land
	 *
	 * @access public
	 */
	public function _js_vars() {
		$args = array(
			'class'  => get_class( $this ),
			'screen' => array(
				'id'   => $this->screen->id,
				'base' => $this->screen->base,
			)
		);

		printf( "<script type='text/javascript'>list_args = %s;</script>\n", wp_json_encode( $args ) );
	}
}