<?php

function buddyforms_metabox_form_setup() {
	global $post;

	if ( $post->post_type != 'buddyforms' ) {
		return;
	}

	// Get the BuddyForms Options
	$buddyform = get_post_meta( get_the_ID(), '_buddyforms_options', true );

	// Get all post types
	$post_types = get_post_types( array( 'show_ui' => true ), 'names', 'and' );

	// Generate the Post Type Array 'none' == Contact Form
	$post_types['bf_submissions'] = 'none';

	$post_types = buddyforms_sort_array_by_Array($post_types, array('bf_submissions'));

	// Remove the 'buddyforms' post type from the post type array
	unset( $post_types['buddyforms'] );


	// Get all Pages
	$pages = get_pages( array(
		'sort_order'  => 'asc',
		'sort_column' => 'post_title',
		'parent'      => 0,
		'post_type'   => 'page',
		'post_status' => 'publish'
	) );

	// Generate teh Pages Array
	$all_pages = Array();
	$all_pages['none'] = 'none';
	foreach ( $pages as $page ) {
		$all_pages[ $page->ID ] = $page->post_title;
	}


	// Get all values or set the default
	$slug                       = $post->post_name;
	$singular_name              = isset( $buddyform['singular_name'] )              ? stripslashes( $buddyform['singular_name'] )   : '';
	$after_submit               = isset( $buddyform['after_submit'] )               ? $buddyform['after_submit']                    : 'display_message';
	$after_submission_page      = isset( $buddyform['after_submission_page'] )      ? $buddyform['after_submission_page']           : 'false';
	$after_submission_url       = isset( $buddyform['after_submission_url'] )       ? $buddyform['after_submission_url']            : '';
	$post_type                  = isset( $buddyform['post_type'] )                  ? $buddyform['post_type']                       : 'false';

	$message_text_default       = $post_type == 'false' ? 'Your Message has been Submitted Successfully' : 'The [form_singular_name] [post_title] has been successfully Submitted!<br>1. [post_link]<br>2. [edit_link]';
	$after_submit_message_text  = isset( $buddyform['after_submit_message_text'] )  ? $buddyform['after_submit_message_text']       : $message_text_default;

	$attached_page              = isset( $buddyform['attached_page'] )              ? $buddyform['attached_page']                   : 'false';
	$status                     = isset( $buddyform['status'] )                     ? $buddyform['status']                          : 'false';
	$comment_status             = isset( $buddyform['comment_status'] )             ? $buddyform['comment_status']                  : 'false';
	$revision                   = isset( $buddyform['revision'] )                   ? $buddyform['revision']                        : 'false';
	$admin_bar                  = isset( $buddyform['admin_bar'] )                  ? $buddyform['admin_bar']                       : 'false';
	$edit_link                  = isset( $buddyform['edit_link'] )                  ? $buddyform['edit_link']                       : 'all';
	$bf_ajax                    = isset( $buddyform['bf_ajax'] )                    ? $buddyform['bf_ajax']                         : 'false';
	$list_posts_option          = isset( $buddyform['list_posts_option'] )          ? $buddyform['list_posts_option']               : 'list_all_form';
	$list_posts_style           = isset( $buddyform['list_posts_style'] )           ? $buddyform['list_posts_style']                : 'list';




	// Create The Form Array
	$form_setup     = array();

	//
	// After Submission
	//
	$element = new Element_Radio( '<b>' . __( "After Submission", 'buddyforms' ) . '</b>', "buddyforms_options[after_submit]", array(
		'display_message'    => __('Display After Submission Message', 'buddyforms'),
		'display_page'       => __('Display Page Contents', 'buddyforms'),
		'redirect'           => __('Redirect to url', 'buddyforms'),
	), array(
		'value' => $after_submit,
		'class' => 'bf_hidden_checkbox'
	) );

	$element->setAttribute( 'bf_hidden_checkbox', 'sadad' );
	$form_setup['After Submission'][] = $element;


	// Attached Page
	$element = new Element_Select( '<b>' . __( "After Submission Page", 'buddyforms' ) . '</b>', "buddyforms_options[after_submission_page]", $all_pages, array(
		'value'     => $after_submission_page,
		'shortDesc' => __('Select the Page from where the content gets displayed', 'buddyforms'),
		'class'     => 'sadad'
	) );

	$element->setAttribute( 'hidden', 'sadad' );
	$form_setup['After Submission'][] = $element;

	$form_setup['After Submission'][] = new Element_URL( '<b>' . __( "Redirect URL", 'buddyforms' ), "buddyforms_options[after_submission_url]", array(
		'value'     => $after_submission_url,
		'shortDesc' => __('Enter a valid URL', 'buddyforms'),
		'class'     => 'hidden'
	) );


	$form_setup['After Submission'][]              = new Element_Textarea( '<b>' . __( 'After Submission Message Text', 'buddyforms' ) . '</b>', "buddyforms_options[after_submit_message_text]", array(
		'rows'      => 3,
		'style'     => "width:100%",
		'value'     => $after_submit_message_text,
		'shortDesc' => $post_type == 'false'
			? __('Add a after Submission Message', 'buddyforms')
			: __( ' You can use special shortcodes to add dynamic content:<br>[form_singular_name] = Singular Name<br>[post_title] = The Post Title<br>[post_link] = The Post Permalink<br>[edit_link] = Link to the Post Edit Form', 'buddyforms' )
	) );

	$form_setup['After Submission'][] = new Element_Checkbox( '<b>' . __( 'AJAX', 'buddyforms' ) . '</b>', "buddyforms_options[bf_ajax]", array( 'bf_ajax' => __( 'Disable ajax form submission', 'buddyforms' ) ), array(
		'shortDesc' => __( '', 'buddyforms' ),
		'value'     => $bf_ajax
	) );


	//
	// Create Content
	//
	$form_setup['Create Content'][] = new Element_Select( '<b>' . __( "Post Type", 'buddyforms' ) . '</b>', "buddyforms_options[post_type]", $post_types, array(
		'value'    => $post_type,
	) );

	$form_setup['Create Content'][] = new Element_Select( '<b>' . __( "Status", 'buddyforms' ) . '</b>', "buddyforms_options[status]", array(
		'publish',
		'pending',
		'draft'
	), array( 'value' => $status ) );

	$form_setup['Create Content'][] = new Element_Select( '<b>' . __( "Comment Status", 'buddyforms' ) . '</b>', "buddyforms_options[comment_status]", array(
		'open',
		'closed'
	), array( 'value' => $comment_status ) );

	$form_setup['Create Content'][] = new Element_Checkbox( '<b>' . __( 'Revision', 'buddyforms' ) . '</b>', "buddyforms_options[revision]", array( 'Revision' => __( 'Enable frontend revision control', 'buddyforms' ) ), array( 'value' => $revision ) );

	$form_setup['Create Content'][] = new Element_Radio( '<b>' . __( "Overwrite Frontend 'Edit Post' Link", 'buddyforms' ) . '</b>', "buddyforms_options[edit_link]", array(
		'none'          => 'None',
		'all'           => __( "All Edit Links", 'buddyforms' ),
		'my-posts-list' => __( "Only in My Posts List", 'buddyforms' )
	), array(
		'view'      => 'vertical',
		'value'     => $edit_link,
		'shortDesc' => __( 'The link to the backend will be changed to use the frontend editing.', 'buddyforms' )
	) );

	$form_setup['Create Content'][] = new Element_Radio( '<b>' . __( "Overwrite \"General\" After Submission Options", 'buddyforms' ) . '</b>', "buddyforms_options[after_submit]", array(
		'display_form'       => 'Display the Form and Message',
		'display_post'       => 'Display the Post',
	), array( 'value' => $after_submit, 'id' => 'after_submit_hidden' . $slug, 'class' => 'after_submit_hidden' ) );

	$form_setup['Create Content'][] = new Element_Textbox( '<b>' . __( "Singular Name", 'buddyforms' ), "buddyforms_options[singular_name]", array(
		'value'    => $singular_name,
		'shortDesc' => 'The Single Name is used by other plugins and Navigation ( Display Books, Add Book )'
	) );



	//
	// Edit Submissions
	//

	// Attached Page
	$form_setup['Edit Submissions'][] = new Element_Select( '<b>' . __( "Let logged-in user see and manage there submissions ", 'buddyforms' ) . '</b>' . __( 'Page', 'buddyforms' ), "buddyforms_options[attached_page]", $all_pages, array(
		'value'     => $attached_page,
		'shortDesc' => '
	    Associate a Page with a BuddyForm. The page you select will be used to build the form URLs:<br>
	    Create: page_name/create/form_name<br>
	    View: page_name/view/form_name<br>
	    Edit: page_name/edit/form_name<br>
		<br><br>
	    Pro Tips:<br>
	    Different BuddyForms can be associated with the same Page.<br>
	    You don’t have to use the auto-generated URLs -- you can add a BuddyForm or list of Posts to any Page or Post using shortcodes.<br>'
	) );

	$form_setup['Edit Submissions'][] = new Element_Checkbox( '<b>' . __( 'Admin Bar', 'buddyforms' ) . '</b>', "buddyforms_options[admin_bar]", array( 'Admin Bar' => __( 'Add to Admin Bar', 'buddyforms' ) ), array( 'value' => $admin_bar ) );

	$form_setup['Edit Submissions'][] = new Element_Radio( '<b>' . __( "Overwrite \"General\" After Submission Options", 'buddyforms' ) . '</b>', "buddyforms_options[after_submit]", array(
		'display_posts_list' => 'Display the User\'s Post List',
	), array( 'value' => $after_submit, 'id' => 'after_submit_hidden' . $slug, 'class' => 'after_submit_hidden' ) );

	$form_setup['Edit Submissions'][] = new Element_Radio( '<b>' . __( "List Posts Options", 'buddyforms' ) . '</b>', "buddyforms_options[list_posts_option]", array(
		'list_all_form' => 'List all Author Posts created with this Form',
		'list_all'      => 'List all Author Posts of the PostType'
	), array( 'value' => $list_posts_option, 'shortDesc' => '' ) );


	$form_setup['Edit Submissions'][] = new Element_Radio( '<b>' . __( "List Style", 'buddyforms' ) . '</b>', "buddyforms_options[list_posts_style]", array(
		'list'  => 'List',
		'table' => 'Table'
	), array( 'value' => $list_posts_style, 'shortDesc' => 'Do you want to list post in a ul li list or as table.' ) );


	// Check if form elements exist and sort the form elements
	if ( is_array( $form_setup ) ) {
		$form_setup = buddyforms_sort_array_by_Array( $form_setup, array( 'After Submission', 'Create Content', 'Edit Submissions' ) );
	}

	// Display all Form Elements in a nice Tab UI and List them in a Table
	?>

	<div class="tabs tabbable tabs-left" id="buddyforms_formbuilder_settings">
		<ul class="nav nav-tabs nav-pills">
			<?php
			$i = 0;
			foreach ( $form_setup as $tab => $fields ) {
				$tab_slug = sanitize_title($tab); ?>
			<li class="<?php echo $i == 0 ? 'active' : '' ?>"><a
					href="#<?php echo $tab_slug; ?>"
					data-toggle="tab"><?php echo $tab; ?></a>
				</li><?php
				$i ++;
			}
			// Allow other plugins to add new sections
			do_action('buddyforms_form_setup_nav_li_last');
			?>

		</ul>
		<div class="tab-content">
			<?php
			$i = 0;
			foreach ( $form_setup as $tab => $fields ) {
				$tab_slug = sanitize_title($tab);
				?>
				<div class="tab-pane fade in <?php echo $i == 0 ? 'active' : '' ?>"
				     id="<?php echo $tab_slug; ?>">
					<div class="buddyforms_accordion_general">
						<table class="wp-list-table widefat posts striped">
							<tbody>
							<?php foreach($fields as $field_key => $field ) {

								$type  = $field->getAttribute( 'type' );
								$class = $field->getAttribute( 'class' );

								?>

								<tr id="row_form_title" class="<?php echo $class ?>">
									<th scope="row">
										<label for="form_title"><?php echo $field->getLabel() ?></label>
									</th>
									<td>
										<?php echo $field->render() ?>
										<p class="description"><?php echo $field->getShortDesc() ?></p>
									</td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
				<?php
				$i ++;
			}
			// Allow other plugins to hook there content for there nav into the tab content
			do_action('buddyforms_form_setup_tab_pane_last');
			?>
		</div>
	</div>

	<?php

}