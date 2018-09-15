<?php

class BuddyFormsSubmissionPage {
	/**
	 * @var BuddyForms_Submissions_List_Table
	 */
	private $bf_submissions_table;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'buddyforms_create_submissions_page' ) );

		add_filter( 'set-screen-option', array( $this, 'buddyforms_submissions_set_option' ), 10, 3 );
		add_action( 'admin_init', array( $this, 'redirect_after_delete' ) );

	}

	public function buddyforms_create_submissions_page() {
		$buddyforms_submission_admin_page = add_submenu_page( 'edit.php?post_type=buddyforms', __( 'Submissions', 'buddyforms' ), __( 'Submissions', 'buddyforms' ), 'activate_plugins', 'buddyforms_submissions', array( $this, 'buddyforms_submissions_screen' ) );
		add_action( "load-$buddyforms_submission_admin_page", array( $this, 'buddyforms_submissions_add_options' ) );
	}

	public function buddyforms_submissions_add_options() {
		$option = 'per_page';
		$args   = array(
			'label'   => 'Entries',
			'default' => 10,
			'option'  => 'entries_per_page'
		);
		add_screen_option( $option, $args );

		$this->bf_submissions_table = new BuddyForms_Submissions_List_Table;
	}

	public function buddyforms_submissions_screen() {
		global $buddyforms, $current_screen, $parent_file, $form_slug, $post_id;

		// Check that the user is allowed to update options
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'buddyforms' ) );
		} ?>
        <div id="post" class="bf_admin_wrap wrap">
			<?php
			include( BUDDYFORMS_INCLUDES_PATH . '/admin/admin-header.php' );
			?>
            <hr style="margin-bottom: 0px !important;"/><?php
			$this->bf_submissions_table->prepare_items();
			if ( isset( $_GET['form_slug'] ) ) {
				$current_screen->set_parentage( $parent_file );
				//$current_screen->render_screen_options();
				$current_screen->render_screen_meta();
			}

			?>

            <div id="icon-users" class="icon32"><br/></div>
            <div id="buddyforms_admin_main_menu" class="">
                <ul>
                    <li>

                        <h4>Select a form to display the submissions</h4>
                        <script type="text/javascript">
                          jQuery(document).ready(function (jQuery) {
                            jQuery('#buddyforms_admin_menu_submissions_form_select').change(function () {
                              window.location = '?post_type=buddyforms&page=buddyforms_submissions&form_slug=' + this.value
                            })

                            jQuery('.metabox-prefs input:checkbox').each(function () {
                              var colID = jQuery(this).attr('id')
                              var hasCheckedAtt = document.getElementById(colID).hasAttribute('checked')
                              if (!hasCheckedAtt) {
                                document.getElementById(colID).checked = false
                              }

                            })

                          })
                        </script>
                        <select id="buddyforms_admin_menu_submissions_form_select">
                            <option value="none">Select Form</option>
							<?php foreach ( $buddyforms as $form_slug => $form ) { ?>
                                <option <?php isset( $_GET['form_slug'] ) ? selected( $_GET['form_slug'], $form_slug ) : ''; ?> value="<?php echo $form_slug ?>">
									<?php echo $form['name']; ?>
                                </option>
							<?php } ?>
                        </select>
                    </li>
                </ul>
            </div>

			<?php if ( isset( $_GET['form_slug'] ) && ! isset( $_GET['entry'] ) ) { ?>
                <form id="filter" method="get">
                    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
					<?php $this->bf_submissions_table->display(); ?>
                </form>
			<?php } ?>

			<?php if ( isset( $_GET['action'] ) && isset( $_GET['entry'] ) ) {
				$form_slug = get_post_meta( $_GET['entry'], '_bf_form_slug', true );
				$post_id   = $_GET['entry'];
				require_once( BUDDYFORMS_INCLUDES_PATH . 'admin/submission-single.php' );
			} ?>
        </div>
		<?php
	}

    function redirect_after_delete() {
        global $buddyforms;

        $action    = isset( $_GET['action'] ) ? $_GET['action'] : "";
        $entry     = isset( $_GET['post'] ) ? $_GET['post'] : "";
        $form_slug = isset( $_GET['form_slug'] ) ? $_GET['form_slug'] : "";
        if ( $action === 'delete' ) {
            $buddyFData = isset( $buddyforms[ $form_slug ]['form_fields'] ) ? $buddyforms[ $form_slug ]['form_fields'] : [];
            foreach ( $buddyFData as $key => $value ) {

                $field = $value['slug'];
                $type  = $value['type'];
                if ( $type == 'upload' ) {
                    //Check if the option Delete Files When Remove Entry is ON.
                    $can_delete_files = isset( $value['delete_files'] ) ? true : false;
                    if ( $can_delete_files ) {
                        // If true then Delete the files attached to the entry
                        $column_val   = get_post_meta( $entry, $field, true );
                        if(!empty($column_val)){
                            $attachmet_id = explode( ",", $column_val );
                            foreach ( $attachmet_id as $id ) {
                                wp_delete_attachment( $id, true );
                            }

                        }

                    }

                }
            }
        }

        if ( isset( $_GET['page'] ) && $_GET['page'] == 'buddyforms_submissions' && isset( $_GET['entry'] ) ) {
            if ( ! get_post( $_GET['entry'] ) ) {
                wp_redirect( '?post_type=buddyforms&page=buddyforms_submissions&form_slug=' . $_GET['form_slug'] );
            }
        }
    }

	/**
	 * @param $status
	 * @param $option
	 * @param $value
	 *
	 * @return mixed
	 */
	function buddyforms_submissions_set_option( $status, $option, $value ) {
		return $value;
	}
}

function buddyforms_submission_page_init() {
	new BuddyFormsSubmissionPage();
}

add_action( 'init', 'buddyforms_submission_page_init' );

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Class BuddyForms_Submissions_List_Table
 */
class BuddyForms_Submissions_List_Table extends WP_List_Table {

	/**
	 * BuddyForms_Submissions_List_Table constructor.
	 */
	function __construct() {

		//Set parent defaults
		parent::__construct( array(
			'singular' => 'Submission',     //singular name of the listed records
			'plural'   => 'Submissions',    //plural name of the listed records
			'ajax'     => false            //does this table support ajax?
		) );

	}

	/**
	 * @param WP_Post $item
	 *
	 * @return string
	 */
    function column_ID( $item ) {
        global $buddyforms;

        $form_slug = isset( $_GET['form_slug'] ) ? $_GET['form_slug'] : "";
        $actions = array(
            'edit'   => sprintf( '<a href="post.php?post=%s&action=%s">Edit</a>', $item->ID, 'edit' ),
            'delete' => '<a href="' . get_delete_post_link( $item->ID, '', true ) . '&form_slug='.$form_slug.'" class="submitdelete deletion" onclick="return confirm(\'Are you sure you want to delete that entry?\');" title="Delete">Delete</a>',
        );

        if ( isset( $buddyforms[ $_GET['form_slug'] ]['post_type'] ) && $buddyforms[ $_GET['form_slug'] ]['post_type'] == 'bf_submissions' ) {
            $actions['edit'] = sprintf( '<a href="?post_type=buddyforms&page=%s&action=%s&entry=%s&form_slug=%s">View Submission</a>', $_REQUEST['page'], 'edit', $item->ID, $_GET['form_slug'] );
        }

        // Return the title contents
        return sprintf( '<span style="color:silver">%1$s</span>%2$s',
            $item->ID,
            $this->row_actions( $actions )
        );
    }

	/**
	 * @param WP_Post $item
	 * @param string $column_name
	 */
	function column_default( $item, $column_name ) {
		$bf_value = get_post_meta( intval( $item->ID ), $column_name, true );
		$bf_field = buddyforms_get_form_field_by_slug( $_GET['form_slug'], $column_name );
		if ( $bf_field !== false ) {
			$this->get_column_values( $column_name, $bf_field['type'], $item, $bf_value );
		}
		if ( $column_name == 'Date' ) {
			echo get_the_date( 'F j, Y', $item->ID );
		}
	}

	public function get_column_values( $field_slug, $field_type, $item, $bf_value ) {
		switch ( $field_type ) {
			case 'title':
				$bf_value = get_the_title( $item->ID );
				break;
			case 'content':
				$post = get_post( $item->ID );

				$content = str_replace( ']]>', ']]&gt;', $post->post_content );
				$content = strip_shortcodes( $content );

				/**
				 * Filters the number of words in an excerpt.
				 *
				 * @since 2.7.0
				 *
				 * @param int $number The number of words. Default 55.
				 */
				$excerpt_length = apply_filters( 'excerpt_length', 55 );
				/**
				 * Filters the string in the "more" link displayed after a trimmed excerpt.
				 *
				 * @since 2.9.0
				 *
				 * @param string $more_string The string shown within the more link.
				 */
				$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );
				$bf_value     = wp_trim_words( $content, $excerpt_length, $excerpt_more );
				break;
			case 'upload':
				$result        = '';
				$attachment_id = explode( ",", $bf_value );
				foreach ( $attachment_id as $id ) {
					if ( ! empty( $id ) ) {
						$url    = wp_get_attachment_url( $id );
						$result .= " <a style='vertical-align: top;' target='_blank' href='" . $url . "'>$id</a>,";
					}
				}
				$bf_value = ( ! empty( $result ) ) ? rtrim( trim( $result ), ',' ) : '';
				break;
			case 'Date':
				$bf_value = get_the_date( 'F j, Y', $item->ID );
				break;
			case 'category':
			case 'tags':
				if ( is_array( $bf_value ) ) {
					$result = array();
					foreach ( $bf_value as $key => $val ) {
						$result[] = ( $field_type == 'tags' ) ? get_tag( $val )->name : get_the_category_by_ID( $val );
					}
					$bf_value = implode( ',', $result );
				}
				break;
			case 'status':
				$bf_value = buddyforms_get_post_status_readable( get_post_status( $item->ID ) );
				break;
			default:
				if ( is_array( $bf_value ) ) {
					$str_result = '';
					foreach ( $bf_value as $key => $val ) {
						$str_result .= $val;
					}
					$bf_value = $str_result;
				} else {
					$bf_value = wp_trim_words( $bf_value, 25 );
				}
				break;
		}
		echo apply_filters( "bf_submission_column_default", $bf_value, $item, $field_type, $field_slug );
	}


	function prepare_items() {
		global $wpdb;

		$per_page = $this->get_items_per_page( 'entries_per_page', 10 );

		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = $this->get_column_info();

		$this->get_bulk_actions();

		$data = array();
		if ( isset( $_GET['form_slug'] ) ) {
			$customkey   = '_bf_form_slug'; // set to your custom key
			$customvalue = ! empty( $_GET['form_slug'] ) ? $_GET['form_slug'] : '';
			$sql_args    = array( 'ID', 'post_title', 'post_author' );
			$sql_select  = implode( ', ', $sql_args );
			$data        = $wpdb->get_results( $wpdb->prepare( "SELECT {$sql_select} FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE ID = {$wpdb->postmeta}.post_id AND meta_key = %s AND meta_value = %s ORDER BY post_date DESC", $customkey, $customvalue ) );
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

	/**
	 * @return array
	 */
	function get_columns() {
		global $buddyforms;

		$columns = array(
			'ID'   => 'ID',
			'Date' => 'Date',
		);

		if ( isset( $_GET['form_slug'] ) && isset( $buddyforms[ $_GET['form_slug'] ]['form_fields'] ) ) {
			foreach ( $buddyforms[ $_GET['form_slug'] ]['form_fields'] as $key => $field ) {
				if ( $field['slug'] != 'user_pass' ) {
					$columns[ $field['slug'] ] = $field['name'];
				}
			}

		}

		return $columns;
	}
}
