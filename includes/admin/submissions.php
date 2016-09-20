<?php
function buddyforms_create_submissions_page()  {
	add_submenu_page(
		'edit.php?post_type=buddyforms',
		'BuddyForms Wizard',
		'Form Wizard',
		'manage_options',
		'post-new.php?post_type=buddyforms&wizard=1'
	);
	$hook = add_submenu_page( 'edit.php?post_type=buddyforms', __( 'Submissions', 'buddyforms' ), __( 'Submissions', 'buddyforms' ), 'manage_options', 'bf_submissions', 'bf_submissions_screen' );
	add_action( "load-$hook", 'bf_submissions_add_options' );
}

add_action('admin_init', 'redirect_after_delete');
function redirect_after_delete(){

	if( isset($_GET['page']) && $_GET['page'] == 'bf_submissions' && isset( $_GET['entry'] )){
		if(!get_post($_GET['entry'])){
			wp_redirect('?post_type=buddyforms&page=bf_submissions&form_slug='.$_GET['form_slug']);
		}
	}
}

add_action( 'admin_menu', 'buddyforms_create_submissions_page' );
function bf_submissions_add_options() {
	global $bf_submissions_table;

	$option = 'per_page';
	$args   = array(
		'label'   => 'Entries',
		'default' => 10,
		'option'  => 'entries_per_page'
	);
	add_screen_option( $option, $args );

	//Create an instance of our package class...
	$bf_submissions_table = new bf_submissions_List_Table;

}

add_filter( 'set-screen-option', 'bf_submissions_set_option', 10, 3 );
function bf_submissions_set_option( $status, $option, $value ) {
	return $value;
}

function bf_submissions_screen() {
	global $buddyforms, $bf_submissions_table, $form_slug, $post_id;

	// Check that the user is allowed to update options
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'buddyforms' ) );
	} ?>

	<div id="bf_admin_wrap" class="wrap">

		<?php
		include( 'bf-admin-header.php' );

		// echo '<pre>';
		// print_r($buddyforms);
		// echo '</pre>';

		//Fetch, prepare, sort, and filter our data...
		$bf_submissions_table->prepare_items();
		?>

		<div id="icon-users" class="icon32"><br/></div>
		<div id="buddyforms_admin_main_menu" class="">
			<ul>
				<li>

					<h4>Select a form to display the submissions</h4>
					<script type="text/javascript">
						jQuery(document).ready(function (jQuery) {
							jQuery("#buddyforms_admin_menu_submissions_form_select").change(function () {
								window.location = '?post_type=buddyforms&page=bf_submissions&form_slug=' + this.value
							});
						});
					</script>
					<select id="buddyforms_admin_menu_submissions_form_select">
						<option value="none">Select Form</option>
						<?php foreach ( $buddyforms as $form_slug => $form ) { ?>
							<option <?php isset($_GET['form_slug']) ? selected( $_GET['form_slug'], $form_slug ) : ''; ?> value="<?php echo $form_slug ?>">
								<?php echo $form['name']; ?>
							</option>
						<?php } ?>
					</select>
				</li>
			</ul>
		</div>

		<?php if( isset( $_GET['form_slug'] ) && !isset( $_GET['entry'] ) ) { ?>
			<form id="filter" method="get">
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
				<?php $bf_submissions_table->display(); ?>
			</form>
		<?php } ?>

		<?php if(isset($_GET['action']) && isset($_GET['entry'])) {
			$form_slug = get_post_meta($_GET['entry'], '_bf_form_slug', true);
			$post_id   = $_GET['entry'];
			buddyforms_locate_template( 'buddyforms/submissions-single.php' );
		} ?>
	</div>
	<?php
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class bf_submissions_List_Table extends WP_List_Table {

	function __construct() {
		global $status, $page, $buddyforms;

		//Set parent defaults
		parent::__construct( array(
			'singular' => 'Submission',     //singular name of the listed records
			'plural'   => 'Submissions',    //plural name of the listed records
			'ajax'     => false            //does this table support ajax?
		) );

	}

	function column_ID( $item ) {
		global $buddyforms;

		$actions = array(
			'edit'   => sprintf( '<a href="post.php?post=%s&action=%s">Edit</a>',  $item['ID'], 'edit' ),
			'delete' => '<a href="' . get_delete_post_link( $item['ID'] , '', true ) . '" class="submitdelete deletion" onclick="return confirm(\'Are you sure you want to delete that entry?\');" title="Delete">Delete</a>',
		);

		if(isset($buddyforms[$_GET['form_slug']]['post_type']) && $buddyforms[$_GET['form_slug']]['post_type'] == 'bf_submissions'){
			$actions['edit'] = sprintf( '<a href="?post_type=buddyforms&page=%s&action=%s&entry=%s&form_slug=%s">View Form</a>', $_REQUEST['page'], 'edit', $item['ID'], $_GET['form_slug'] );
		}

		// Return the title contents
		return sprintf( '<span style="color:silver">%1$s</span>%2$s',
			$item['ID'],
			$this->row_actions( $actions )
		);
	}

	function column_default( $item, $column_name ) {
		global $buddyforms;

		$column_val = get_post_meta( $item['ID'], $column_name, true);

		if(is_array($column_val)){
			foreach($column_val as $key => $val){
				echo $val;
			}
		} else {
			echo $column_val;
		}
		if($column_name == 'Date'){
			echo get_the_date( 'F j, Y', $item['ID'] );
		}
	}


	function get_columns() {
		global $buddyforms;

		$columns = array(
			'ID'           => 'ID',
			'Date'           => 'Date',
		);

		if(isset($_GET['form_slug']) && isset($buddyforms[$_GET['form_slug']]['form_fields'])){
			foreach($buddyforms[$_GET['form_slug']]['form_fields'] as $key => $field){
				$columns[$field['slug']] = $field['name'];
			}

		}
		return $columns;
	}

	function prepare_items() {
		global $wpdb;

		$per_page = $this->get_items_per_page( 'entries_per_page', 10 );

		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->process_bulk_action();

		$data = array();
		if ( isset( $_GET['form_slug'] ) ) {
			$sql_args   = array( 'ID', 'post_title', 'post_author' );
			$sql_select = implode( ', ', $sql_args );

			$customkey   = '_bf_form_slug'; // set to your custom key
			$customvalue = '';
			$customvalue = $_GET['form_slug'];
			$data        = $wpdb->get_results( "SELECT $sql_select FROM $wpdb->posts, $wpdb->postmeta WHERE ID = $wpdb->postmeta.post_id AND meta_key = '$customkey' AND meta_value = '$customvalue' ORDER BY post_date DESC", ARRAY_A );
		}


		$current_page = $this->get_pagenum();
		$total_items  = count( $data );

		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		$this->items = $data;

		$this->set_pagination_args( array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
			'total_pages' => ceil( $total_items / $per_page )   //WE have to calculate the total number of pages
		) );
	}
}
