<?php
function buddyforms_custom_post_type_edit_form_after_title($post){
	if ($post->post_type != 'bf-post-types') {
		return;
	}
	$buddyforms_custom_post_type = get_post_meta($post->ID, '_buddyforms_custom_post_type', true);
	wp_enqueue_script('thickbox');
	wp_enqueue_style('thickbox');
	?>

	<script>

		function saveThickboxContent() {
			// Get values from input fields
			const pluralLabel = document.getElementById('plural-label').value;
			const singularLabel = document.getElementById('singular-label').value;
			const slug = document.getElementById('slug').value;

			// You can now use these values as needed, for example, update the entry's data attributes
			const selectedEntry = document.querySelector('.entry.selected');
			selectedEntry.setAttribute('data-plural-label', pluralLabel);
			selectedEntry.setAttribute('data-singular-label', singularLabel);
			selectedEntry.setAttribute('data-slug', slug);

			// Hide Thickbox modal
			tb_remove();
		}
	</script>

	<style>
		.entry {
			width: 350px;
			height: 220px;
			background-color: #f0f0f0;
			border: 1px solid #ccc;
			margin: 10px;
			display: inline-block;
			cursor: pointer;
			transition: transform 0.2s;
			overflow: hidden;
			background-size: cover;
			background-position: center;
		}

		.selected {
			border: 2px solid green;
		}

		.deselected {
			opacity: 0.5; /* Adjust the opacity for deselected entries */
		}

		.entry.directory {
			background: #f0f0f0 url('none');
		}

		.entry.blog {
			background: #f0f0f0 url('none');
		}

		.entry.pages {
			background: #f0f0f0 url('none');
		}

		.entry.hidden {
			background: #f0f0f0 url('none');
		}

		.entry:hover {
			transform: scale(1.05);
		}

		.entry .description {
			padding: 10px;
			background-color: #fff;
			text-align: left;
			height: 100%;
		}

		.entry p {
			font-size: 20px;
		}

		.entry span.dashicons {
			font-size: 50px;
			text-align: left;
			float: left;
			padding: 10px;
		}

		div#screen-meta-links {
			display: none;
		}

		#thickbox-content {
			max-width: 400px;
			margin: 20px;
		}

		label {
			display: block;
			margin-bottom: 5px;
		}

		input {
			width: 100%;
			padding: 8px;
			margin-bottom: 10px;
			box-sizing: border-box;
		}

		button {
			background-color: #4caf50;
			color: white;
			padding: 10px 15px;
			border: none;
			border-radius: 4px;
			cursor: pointer;
		}

		button:hover {
			background-color: #45a049;
		}
		.hidemessage{
			display: none;
		}
	</style>

	<div id="thickbox-content" style="display: none;">
		<label for="name">Post Type Slug <span class="required">*</span></label>
		<p id="slugchanged" class="hidemessage">Slug has changed<span class="dashicons dashicons-warning"></span></p>
		<p id="slugexists" class="hidemessage">Slug already exists<span class="dashicons dashicons-warning"></span></p>
		<input type="text" id="name" name="buddyforms_custom_post_type[name]"
			   value="<?php echo isset($buddyforms_custom_post_type['name']) ? $buddyforms_custom_post_type['name'] : '' ?>"
			   maxlength="20"
			   aria-required="true" required="true"><br>
		<p class="buddyforms-field-description description">The post type name/slug. Used for
			various queries for post type content.</p>
		<p class="buddyforms-slug-details">Slugs may only contain lowercase alphanumeric
			characters, dashes, and underscores.</p>
		<p>DO NOT EDIT the post type slug unless also planning to migrate posts. Changing the
			slug registers a new post type entry.</p>
		<div class="buddyforms-spacer"><input type="checkbox" id="update_post_types"
											  name="update_post_types[]"
											  value="update_post_types"><label
					for="update_post_types">Migrate posts to newly renamed post
				type?</label><br></div>

		<label for="label">Plural Label <span class="required">*</span></label>
		<input type="text" id="label" name="buddyforms_custom_post_type[label_plural]"
			   value="<?php echo isset($buddyforms_custom_post_type['label_plural']) ? $buddyforms_custom_post_type['label_plural'] : ''; ?>"
			   aria-required="true"
			   required="true" placeholder="(e.g. Movies)">
		<p class="buddyforms-field-description description">Used for the post type admin menu item.</p>

		<label for="singular_label">Singular Label <span class="required">*</span></label>
		<input type="text" id="singular_label"
			   name="buddyforms_custom_post_type[singular_label]"
			   value="<?php echo isset($buddyforms_custom_post_type['label_singular']) ? $buddyforms_custom_post_type['label_singular'] : ''; ?>"
			   aria-required="true" required="true" placeholder="(e.g. Movie)">
		<p class="buddyforms-field-description description">Used when a singular label is needed.</p></td>

		<button onclick="saveThickboxContent()">Save</button>
	</div>

	<h1>Quickly Select Your Use Case and Customize It to Suit Your Needs</h1>
	<p>Choose the use case that best fits your requirements and customize it further in the advanced section if
		necessary.</p>


	<div class="entry directory dashicons" data-usecase="directory" onclick="selectEntry(this)">
		<span class="dashicons dashicons-list-view"></span>
		<p>Directory with Filter and Search</p>
		<div class="description">Advanced directory with a search and filters to make it easily filterable by any
			form element. It's suitable for real estate listings, company directories, geo directories and various
			other types of directories.
		</div>
	</div>

	<div class="entry blog dashicons" data-usecase="blog" onclick="selectEntry(this)">
		<span class="dashicons dashicons-admin-post"></span>
		<p>Blog or Magazine Style</p>
		<div class="description">This use case is perfect for creating a blog or magazine-style website with an
			archive of posts. It provides a chronological display of your articles for readers to explore.
		</div>
	</div>

	<div class="entry pages dashicons" data-usecase="pages" onclick="selectEntry(this)">
		<span class="dashicons dashicons-admin-page"></span>
		<p>WordPress Pages Style Hierarchy</p>
		<div class="description">Create a hierarchy of pages in a WordPress-style structure, with parent pages and
			child pages. This use case is ideal for organizing and presenting content in a structured manner.
		</div>
	</div>

	<div class="entry hidden dashicons" data-usecase="hidden" onclick="selectEntry(this)">
		<span class="dashicons dashicons-hidden"></span>
		<p>Hidden Public Post Type</p>
		<div class="description">This use case allows you to create a hidden post type that is publicly accessible
			but doesn't have an archive. The post exists only through its URL, making it useful for unique,
			standalone content.
		</div>
	</div>

	<div class="entry hidden dashicons" data-usecase="hidden" onclick="selectEntry(this)">
		<span class="dashicons dashicons-privacy"></span>
		<p>Private Post Type</p>
		<div class="description">Create exclusive, private posts for logged-in users. User registration is required for
			access, ensuring a secure and controlled viewing experience.
		</div>
	</div>

	<div class="entry hidden dashicons" data-usecase="hidden" onclick="selectEntry(this)">
		<span class="dashicons dashicons-welcome-view-site"></span>
		<p>Private Internal Directory</p>
		<div class="description">Private directory with search and filters to make it easily filterable by any form
			element. It's built for internal use, such as on intranets, to list companies, suppliers, or other
			company-relevant data.
		</div>
	</div>
	<script>
		function selectEntry(entry) {

			// Remove 'selected' class from all entries
			const entries = document.querySelectorAll('.entry');
			entries.forEach(e => e.classList.remove('selected'));

			// Add 'selected' class to the clicked entry
			entry.classList.add('selected');

			// Open Thickbox modal
			tb_show('Entry Settings', '#TB_inline?width=300&height=250&inlineId=thickbox-content');

			// Prevent default Thickbox closing behavior when clicking outside the modal
			jQuery('#TB_overlay').unbind('click');
		}
	</script>

	<?php


	echo '<h2>Plural Label</h2>';
}

add_action('edit_form_top', 'buddyforms_custom_post_type_edit_form_after_title');

function buddyforms_edit_form_after_title($post)
{
	if ($post->post_type == 'bf-post-types') {
		buddyforms_post_types_custom_box_html($post);
	}
}

add_action('edit_form_advanced', 'buddyforms_edit_form_after_title');

function buddyforms_custom_post_type_save_postdata($post_id)
{
	if (array_key_exists('buddyforms_custom_post_type', $_POST)) {
		update_post_meta(
				$post_id,
				'_buddyforms_custom_post_type',
				$_POST['buddyforms_custom_post_type']
		);
	}
}

add_action('save_post', 'buddyforms_custom_post_type_save_postdata');


function buddyforms_post_types_custom_box_html($post)
{
	$buddyforms_custom_post_type = get_post_meta($post->ID, '_buddyforms_custom_post_type', true);


	/** This filter is documented in wp-admin/edit-tag-form.php */
	$editable_slug = apply_filters('editable_slug', $post->post_name, $post);
	?>
	<h2>Post Type Slug</h2>
	<input name="post_name" type="text" class="large-text" id="post_name"
		   value="<?php echo esc_attr($editable_slug); ?>"/>
	<?php
//	echo '<pre>';
//	print_r($buddyforms_custom_post_type);
//	echo '</pre>';

	?>
	<div id="poststuff">


		<h1>Advanced Settings</h1>
		<p>Explore all available options for post types in WordPress and adjust them to your needs.</p>

		<div id="buddyforms_panel_pt_advanced_settings" class="buddyforms-section buddyforms-settings postbox closed">
			<div class="postbox-header">
				<h2 class="hndle ui-sortable-handle">
					<span>Supports</span>
				</h2>
				<div class="handle-actions hide-if-no-js">
					<button type="button" class="handlediv" aria-expanded="false">
						<span class="screen-reader-text">Toggle panel: General settings</span>
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
				</div>
			</div>
			<div class="inside">
				<div class="main">
					<table class="form-table buddyforms-table">
						<tbody>
						<tr>
							<th scope="row">
								<label for="name">Post Type Slug</label> <span class="required">*</span>
								<p id="slugchanged" class="hidemessage">Slug has changed<span
											class="dashicons dashicons-warning"></span></p>
								<p id="slugexists" class="hidemessage">Slug already exists<span
											class="dashicons dashicons-warning"></span></p>
							</th>
							<td><input type="text" id="name" name="buddyforms_custom_post_type[name]"
									   value="<?php echo isset($buddyforms_custom_post_type['name']) ? $buddyforms_custom_post_type['name'] : '' ?>"
									   maxlength="20"
									   aria-required="true" required="true"><br>
								<p class="buddyforms-field-description description">The post type name/slug. Used for
									various queries for post type content.</p>
								<p class="buddyforms-slug-details">Slugs may only contain lowercase alphanumeric
									characters, dashes, and underscores.</p>
								<p>DO NOT EDIT the post type slug unless also planning to migrate posts. Changing the
									slug registers a new post type entry.</p>
								<div class="buddyforms-spacer"><input type="checkbox" id="update_post_types"
																	  name="update_post_types[]"
																	  value="update_post_types"><label
											for="update_post_types">Migrate posts to newly renamed post
										type?</label><br></div>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="label">Plural Label</label> <span class="required">*</span></th>
							<td><input type="text" id="label" name="buddyforms_custom_post_type[label_plural]"
									   value="<?php echo isset($buddyforms_custom_post_type['label_plural']) ? $buddyforms_custom_post_type['label_plural'] : ''; ?>"
									   aria-required="true"
									   required="true" placeholder="(e.g. Movies)"><span class="visuallyhidden">(e.g. Movies)</span><br>
								<p class="buddyforms-field-description description">Used for the post type admin menu
									item.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="singular_label">Singular Label</label> <span
										class="required">*</span></th>
							<td><input type="text" id="singular_label"
									   name="buddyforms_custom_post_type[singular_label]"
									   value="<?php echo isset($buddyforms_custom_post_type['label_singular']) ? $buddyforms_custom_post_type['label_singular'] : ''; ?>"
									   aria-required="true" required="true" placeholder="(e.g. Movie)"><span
										class="visuallyhidden">(e.g. Movie)</span><br>
								<p class="buddyforms-field-description description">Used when a singular label is
									needed.</p></td>
						</tr>
						<tr>
							<th scope="row">Supports<p>Add support for various available post editor features on the
									right. A checked value means the post type feature is supported.</p>
								<p>Use the "None" option to explicitly set "supports" to false.</p>
								<p>Featured images and Post Formats need theme support added, to be used.</p>
								<p>
									<a href="https://developer.wordpress.org/reference/functions/add_theme_support/#post-thumbnails"
									   target="_blank" rel="noopener">Theme support for featured images</a><br><a
											href="https://wordpress.org/support/article/post-formats/" target="_blank"
											rel="noopener">Theme support for post formats</a></p></th>
							<td>
								<fieldset tabindex="0">
									<legend class="screen-reader-text">Post type options</legend>
									<input type="checkbox" id="title" name="cpt_supports[]" value="title"
										   checked="checked"><label for="title">Title</label><br><input type="checkbox"
																										id="editor"
																										name="cpt_supports[]"
																										value="editor"
																										checked="checked"><label
											for="editor">Editor</label><br><input type="checkbox" id="thumbnail"
																				  name="cpt_supports[]"
																				  value="thumbnail"
																				  checked="checked"><label
											for="thumbnail">Featured Image</label><br><input type="checkbox"
																							 id="excerpts"
																							 name="cpt_supports[]"
																							 value="excerpt"><label
											for="excerpts">Excerpt</label><br><input type="checkbox" id="trackbacks"
																					 name="cpt_supports[]"
																					 value="trackbacks"><label
											for="trackbacks">Trackbacks</label><br><input type="checkbox"
																						  id="custom-fields"
																						  name="cpt_supports[]"
																						  value="custom-fields"><label
											for="custom-fields">Custom Fields</label><br><input type="checkbox"
																								id="comments"
																								name="cpt_supports[]"
																								value="comments"><label
											for="comments">Comments</label><br><input type="checkbox" id="revisions"
																					  name="cpt_supports[]"
																					  value="revisions"><label
											for="revisions">Revisions</label><br><input type="checkbox" id="author"
																						name="cpt_supports[]"
																						value="author"><label
											for="author">Author</label><br><input type="checkbox" id="page-attributes"
																				  name="cpt_supports[]"
																				  value="page-attributes"><label
											for="page-attributes">Page Attributes</label><br><input type="checkbox"
																									id="post-formats"
																									name="cpt_supports[]"
																									value="post-formats"><label
											for="post-formats">Post Formats</label><br><input type="checkbox" id="none"
																							  name="cpt_supports[]"
																							  value="none"><label
											for="none">None</label><br></fieldset>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="custom_supports">Custom "Supports"</label>
								<p>Use this input to register custom "supports" values, separated by commas. Learn about
									this at <a
											href="http://docs.pluginize.com/article/28-third-party-support-upon-registration"
											target="_blank" rel="noopener">Custom "Supports"</a></p></th>
							<td><input type="text" id="custom_supports"
									   name="buddyforms_custom_post_type[custom_supports]" value=""
									   aria-required="false"><br>
								<p class="buddyforms-field-description description">Provide custom support slugs
									here.</p></td>
						</tr>
						<tr>
							<th scope="row">Taxonomies<p>Add support for available registered taxonomies.</p></th>
							<td>
								<fieldset tabindex="0">
									<legend class="screen-reader-text">Taxonomy options</legend>
									<input type="checkbox" id="category" name="cpt_addon_taxes[]"
										   value="category"><label for="category">Categories (WP Core)</label><br><input
											type="checkbox" id="post_tag" name="cpt_addon_taxes[]"
											value="post_tag"><label for="post_tag">Tags (WP Core)</label><br></fieldset>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="buddyforms_panel_pt_basic_settings" class="buddyforms-section postbox closed">
			<div class="postbox-header">
				<h2 class="hndle ui-sortable-handle">
					<span>Directories and Archive</span>
				</h2>
				<div class="handle-actions hide-if-no-js">
					<button type="button" class="handlediv" aria-expanded="false">
						<span class="screen-reader-text">Toggle panel: Basic settings</span>
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
				</div>
			</div>
			<div class="inside">
				<div class="main">
					<table class="form-table buddyforms-table">
						<tbody>
						<tr>
							<th scope="row"><label for="has_archive">Has Archive</label>
								<p>If left blank, the archive slug will default to the post type slug.</p></th>
							<td><select id="has_archive" name="buddyforms_custom_post_type[has_archive]">
									<option value="0" selected="selected">False</option>
									<option value="1">True</option>
								</select>
								<p class="buddyforms-field-description description">(default: false) Whether or not the
									post type will have a post type archive URL.</p><br><input type="text"
																							   id="has_archive_string"
																							   name="buddyforms_custom_post_type[has_archive_string]"
																							   value=""
																							   aria-required="false"
																							   placeholder="Slug to be used for archive URL."><span
										class="visuallyhidden">Slug to be used for archive URL.</span></td>
						</tr>
						<tr>
							<th scope="row"><label for="exclude_from_search">Exclude From Search</label></th>
							<td><select id="exclude_from_search"
										name="buddyforms_custom_post_type[exclude_from_search]">
									<option value="0" selected="selected">False</option>
									<option value="1">True</option>
								</select>
								<p class="buddyforms-field-description description">(default: false) Whether or not to
									exclude posts with this post type from front end search results. This also excludes
									from taxonomy term archives.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="hierarchical">Hierarchical</label></th>
							<td><select id="hierarchical" name="buddyforms_custom_post_type[hierarchical]">
									<option value="0" selected="selected">False</option>
									<option value="1">True</option>
								</select>
								<p class="buddyforms-field-description description">(default: false) Whether or not the
									post type can have parent-child relationships. At least one published content item
									is needed in order to select a parent.</p></td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>


		<div id="buddyforms_panel_pt_basic_settings" class="buddyforms-section postbox closed">
			<div class="postbox-header">
				<h2 class="hndle ui-sortable-handle">
					<span>Capabilities and Permission</span>
				</h2>
				<div class="handle-actions hide-if-no-js">
					<button type="button" class="handlediv" aria-expanded="false">
						<span class="screen-reader-text">Toggle panel: Basic settings</span>
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
				</div>
			</div>
			<div class="inside">
				<div class="main">
					<table class="form-table buddyforms-table">
						<tbody>
						<tr>
							<th scope="row"><label for="capability_type">Capability Type</label></th>
							<td><input type="text" id="capability_type"
									   name="buddyforms_custom_post_type[capability_type]" value="post"
									   aria-required="false"><br>
								<p class="buddyforms-field-description description">The post type to use for checking
									read, edit, and delete capabilities. A comma-separated second value can be used for
									plural version.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="public">Public</label></th>
							<td><select id="public" name="buddyforms_custom_post_type[public]">
									<option value="0">False</option>
									<option value="1" selected="selected">True</option>
								</select>
								<p class="buddyforms-field-description description">(Custom Post Type UI default: true)
									Whether or not posts of this type should be shown in the admin UI and is publicly
									queryable.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="publicly_queryable">Publicly Queryable</label></th>
							<td><select id="publicly_queryable" name="buddyforms_custom_post_type[publicly_queryable]">
									<option value="0">False</option>
									<option value="1" selected="selected">True</option>
								</select>
								<p class="buddyforms-field-description description">(default: true) Whether or not
									queries can be performed on the front end as part of parse_request()</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="show_ui">Show UI</label></th>
							<td><select id="show_ui" name="buddyforms_custom_post_type[show_ui]">
									<option value="0">False</option>
									<option value="1" selected="selected">True</option>
								</select>
								<p class="buddyforms-field-description description">(default: true) Whether or not to
									generate a default UI for managing this post type.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="show_in_nav_menus">Show in Nav Menus</label></th>
							<td><select id="show_in_nav_menus" name="buddyforms_custom_post_type[show_in_nav_menus]">
									<option value="0">False</option>
									<option value="1" selected="selected">True</option>
								</select>
								<p class="buddyforms-field-description description">(Custom Post Type UI default: true)
									Whether or not this post type is available for selection in navigation menus.</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="delete_with_user">Delete with user</label></th>
							<td><select id="delete_with_user" name="buddyforms_custom_post_type[delete_with_user]">
									<option value="0" selected="selected">False</option>
									<option value="1">True</option>
								</select>
								<p class="buddyforms-field-description description">(buddyforms default: false) Whether
									to delete posts of this type when deleting a user.</p></td>
						</tr>

						</tbody>
					</table>
				</div>
			</div>
		</div>


		<div id="buddyforms_panel_pt_basic_settings" class="buddyforms-section postbox closed">
			<div class="postbox-header">
				<h2 class="hndle ui-sortable-handle">
					<span>Menu</span>
				</h2>
				<div class="handle-actions hide-if-no-js">
					<button type="button" class="handlediv" aria-expanded="false">
						<span class="screen-reader-text">Toggle panel: Basic settings</span>
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
				</div>
			</div>
			<div class="inside">
				<div class="main">
					<table class="form-table buddyforms-table">
						<tbody>

						<tr>
							<th scope="row"><label for="menu_position">Menu Position</label>
								<p>See
									<a href="https://developer.wordpress.org/reference/functions/register_post_type/#menu_position"
									   target="_blank" rel="noopener">Available options</a> in the "menu_position"
									section. Range of 5-100</p></th>
							<td><input type="text" id="menu_position" name="buddyforms_custom_post_type[menu_position]"
									   value="" aria-required="false"><br>
								<p class="buddyforms-field-description description">The position in the menu order the
									post type should appear. show_in_menu must be true.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="show_in_menu">Show in Menu</label>
								<p>"Show UI" must be "true". If an existing top level page such as "tools.php" is
									indicated for second input, post type will be sub menu of that.</p></th>
							<td><select id="show_in_menu" name="buddyforms_custom_post_type[show_in_menu]">
									<option value="0">False</option>
									<option value="1" selected="selected">True</option>
								</select>
								<p class="buddyforms-field-description description">(default: true) Whether or not to
									show the post type in the admin menu and where to show that menu.</p><br><input
										type="text" id="show_in_menu_string"
										name="buddyforms_custom_post_type[show_in_menu_string]" value=""
										aria-required="false"><br>
								<p class="buddyforms-field-description description">The top-level admin menu page file
									name for which the post type should be in the sub menu of.</p></td>
						</tr>
						<tr>
							<th scope="row">
								<div id="menu_icon_preview"></div>
								<label for="menu_icon">Menu Icon</label></th>
							<td><input type="text" id="menu_icon" name="buddyforms_custom_post_type[menu_icon]" value=""
									   aria-required="false" placeholder="(Full URL for icon or Dashicon class)"><span
										class="visuallyhidden">(Full URL for icon or Dashicon class)</span><br>
								<p class="buddyforms-field-description description">Image URL or <a
											href="https://developer.wordpress.org/resource/dashicons/" target="_blank"
											rel="noopener">Dashicon class name</a> to use for icon. Custom image should
									be 20px by 20px.</p>
								<div class="buddyforms-spacer"><input id="buddyforms_choose_dashicon"
																	  class="button dashicons-picker" type="button"
																	  value="Choose dashicon">
									<div class="buddyforms-spacer"><input id="buddyforms_choose_icon" class="button "
																		  type="button" value="Choose image icon"></div>
								</div>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="buddyforms_panel_pt_basic_settings" class="buddyforms-section postbox closed">
			<div class="postbox-header">
				<h2 class="hndle ui-sortable-handle">
					<span>Rest API</span>
				</h2>
				<div class="handle-actions hide-if-no-js">
					<button type="button" class="handlediv" aria-expanded="false">
						<span class="screen-reader-text">Toggle panel: Basic settings</span>
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
				</div>
			</div>
			<div class="inside">
				<div class="main">
					<table class="form-table buddyforms-table">
						<tbody>


						<tr>
							<th scope="row"><label for="show_in_rest">Show in REST API</label></th>
							<td><select id="show_in_rest" name="buddyforms_custom_post_type[show_in_rest]">
									<option value="0">False</option>
									<option value="1" selected="selected">True</option>
								</select>
								<p class="buddyforms-field-description description">(Custom Post Type UI default: true)
									Whether or not to show this post type data in the WP REST API.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="rest_base">REST API base slug</label></th>
							<td><input type="text" id="rest_base" name="buddyforms_custom_post_type[rest_base]" value=""
									   aria-required="false" placeholder="Slug to use in REST API URLs."><span
										class="visuallyhidden">Slug to use in REST API URLs.</span></td>
						</tr>
						<tr>
							<th scope="row"><label for="rest_controller_class">REST API controller class</label></th>
							<td><input type="text" id="rest_controller_class"
									   name="buddyforms_custom_post_type[rest_controller_class]" value=""
									   aria-required="false"
									   placeholder="(default: WP_REST_Posts_Controller) Custom controller to use instead of WP_REST_Posts_Controller."><span
										class="visuallyhidden">(default: WP_REST_Posts_Controller) Custom controller to use instead of WP_REST_Posts_Controller.</span>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="rest_namespace">REST API namespace</label></th>
							<td><input type="text" id="rest_namespace"
									   name="buddyforms_custom_post_type[rest_namespace]" value="" aria-required="false"
									   placeholder="(default: wp/v2) To change the namespace URL of REST API route."><span
										class="visuallyhidden">(default: wp/v2) To change the namespace URL of REST API route.</span>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="buddyforms_panel_pt_advanced_settings" class="buddyforms-section buddyforms-settings postbox closed">
			<div class="postbox-header">
				<h2 class="hndle ui-sortable-handle">
					<span>Advanced Settings</span>
				</h2>
				<div class="handle-actions hide-if-no-js">
					<button type="button" class="handlediv" aria-expanded="false">
						<span class="screen-reader-text">Toggle panel: Basic settings</span>
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
				</div>
			</div>
			<div class="inside">
				<div class="main">
					<table class="form-table buddyforms-table">
						<tbody>

						<tr>
							<th scope="row"><label for="can_export">Can Export</label></th>
							<td><select id="can_export" name="buddyforms_custom_post_type[can_export]">
									<option value="0" selected="selected">False</option>
									<option value="1">True</option>
								</select>
								<p class="buddyforms-field-description description">(default: false) Can this post_type
									be exported.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="rewrite">Rewrite</label></th>
							<td><select id="rewrite" name="buddyforms_custom_post_type[rewrite]">
									<option value="0">False</option>
									<option value="1" selected="selected">True</option>
								</select>
								<p class="buddyforms-field-description description">(default: true) Whether or not
									WordPress should use rewrites for this post type.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="rewrite_slug">Custom Rewrite Slug</label></th>
							<td><input type="text" id="rewrite_slug" name="buddyforms_custom_post_type[rewrite_slug]"
									   value="" aria-required="false" placeholder="(default: post type slug)"><span
										class="visuallyhidden">(default: post type slug)</span><br>
								<p class="buddyforms-field-description description">Custom post type slug to use instead
									of the default.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="rewrite_withfront">With Front</label></th>
							<td><select id="rewrite_withfront" name="buddyforms_custom_post_type[rewrite_withfront]">
									<option value="0">False</option>
									<option value="1" selected="selected">True</option>
								</select>
								<p class="buddyforms-field-description description">(default: true) Should the permalink
									structure be prepended with the front base. (example: if your permalink structure is
									/blog/, then your links will be: false-&gt;/news/, true-&gt;/blog/news/).</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="query_var">Query Var</label></th>
							<td><select id="query_var" name="buddyforms_custom_post_type[query_var]">
									<option value="0">False</option>
									<option value="1" selected="selected">True</option>
								</select>
								<p class="buddyforms-field-description description">(default: true) Sets the query_var
									key for this post type.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="query_var_slug">Custom Query Var Slug</label></th>
							<td><input type="text" id="query_var_slug"
									   name="buddyforms_custom_post_type[query_var_slug]" value="" aria-required="false"
									   placeholder="(default: post type slug) Query var needs to be true to use."><span
										class="visuallyhidden">(default: post type slug) Query var needs to be true to use.</span><br>
								<p class="buddyforms-field-description description">Custom query var slug to use instead
									of the default.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="register_meta_box_cb">Metabox callback</label></th>
							<td><input type="text" id="register_meta_box_cb"
									   name="buddyforms_custom_post_type[register_meta_box_cb]" value=""
									   aria-required="false"><br>
								<p class="buddyforms-field-description description">Provide a callback function that
									sets up the meta boxes for the edit form. Do `remove_meta_box()` and
									`add_meta_box()` calls in the callback. Default null.</p></td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="buddyforms_panel_pt_additional_labels" class="buddyforms-section buddyforms-labels postbox closed">
			<div class="postbox-header">
				<h2 class="hndle ui-sortable-handle">
					<span>Labels</span>
				</h2>
				<div class="handle-actions hide-if-no-js">
					<button type="button" class="handlediv" aria-expanded="false">
						<span class="screen-reader-text">Toggle panel: Basic settings</span>
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
				</div>
			</div>
			<div class="inside">
				<div class="main">
					<table class="form-table buddyforms-table">
						<tbody>
						<tr>
							<th scope="row"><label for="description">Post Type Description</label></th>
							<td><textarea id="description" name="buddyforms_custom_post_type[description]" rows="4"
										  cols="40"></textarea><br>
								<p class="buddyforms-field-description description">Perhaps describe what your custom
									post type is used for?</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="menu_name">Menu Name</label></th>
							<td><input type="text" id="menu_name" name="buddyforms_custom_post_type[menu_name]"
									   value="<?php echo isset($buddyforms_custom_post_type['menu_name']) ? $buddyforms_custom_post_type['menu_name'] : ''; ?>"
									   aria-required="false" placeholder="(e.g. My Movies)" data-label="My item"
									   data-plurality="plural"><span class="visuallyhidden">(e.g. My Movies)</span><br>
								<p class="buddyforms-field-description description">Custom admin menu name for your
									custom post type.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="all_items">All Items</label></th>
							<td><input type="text" id="all_items" name="buddyforms_custom_post_type[label_all_items]"
									   value="<?php echo isset($buddyforms_custom_post_type['label_add_new']) ? $buddyforms_custom_post_type['label_all_items'] : ''; ?>"
									   aria-required="false" placeholder="(e.g. All Movies)" data-label="All item"
									   data-plurality="plural"><span class="visuallyhidden">(e.g. All Movies)</span><br>
								<p class="buddyforms-field-description description">Used in the post type admin
									submenu.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="add_new">Add New</label></th>
							<td><input type="text" id="add_new" name="buddyforms_custom_post_type[add_new]"
									   value="<?php echo isset($buddyforms_custom_post_type['label_add_new']) ? $buddyforms_custom_post_type['label_add_new'] : ''; ?>"
									   aria-required="false" placeholder="(e.g. Add New)" data-label="Add new"
									   data-plurality="plural"><span class="visuallyhidden">(e.g. Add New)</span><br>
								<p class="buddyforms-field-description description">Used in the post type admin
									submenu.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="add_new_item">Add New Item</label></th>
							<td>
								<input type="text" id="add_new_item" name="buddyforms_custom_post_type[add_new_item]"
									   value=""
									   aria-required="false" placeholder="(e.g. Add New Movie)"
									   data-label="Add new item" data-plurality="singular"><span class="visuallyhidden">(e.g. Add New Movie)</span><br>
								<p class="buddyforms-field-description description">Used at the top of the post editor
									screen for a new post type post.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="edit_item">Edit Item</label></th>
							<td><input type="text" id="edit_item" name="buddyforms_custom_post_type[edit_item]" value=""
									   aria-required="false" placeholder="(e.g. Edit Movie)" data-label="Edit item"
									   data-plurality="singular"><span
										class="visuallyhidden">(e.g. Edit Movie)</span><br>
								<p class="buddyforms-field-description description">Used at the top of the post editor
									screen for an existing post type post.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="new_item">New Item</label></th>
							<td><input type="text" id="new_item" name="buddyforms_custom_post_type[new_item]" value=""
									   aria-required="false" placeholder="(e.g. New Movie)" data-label="New item"
									   data-plurality="singular"><span
										class="visuallyhidden">(e.g. New Movie)</span><br>
								<p class="buddyforms-field-description description">Post type label. Used in the admin
									menu for displaying post types.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="view_item">View Item</label></th>
							<td><input type="text" id="view_item" name="buddyforms_custom_post_type[view_item]" value=""
									   aria-required="false" placeholder="(e.g. View Movie)" data-label="View item"
									   data-plurality="singular"><span
										class="visuallyhidden">(e.g. View Movie)</span><br>
								<p class="buddyforms-field-description description">Used in the admin bar when viewing
									editor screen for a published post in the post type.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="view_items">View Items</label></th>
							<td><input type="text" id="view_items" name="buddyforms_custom_post_type[view_items]"
									   value=""
									   aria-required="false" placeholder="(e.g. View Movies)" data-label="View item"
									   data-plurality="plural"><span
										class="visuallyhidden">(e.g. View Movies)</span><br>
								<p class="buddyforms-field-description description">Used in the admin bar when viewing
									editor screen for a published post in the post type.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="search_items">Search Item</label></th>
							<td><input type="text" id="search_items" name="buddyforms_custom_post_type[search_items]"
									   value=""
									   aria-required="false" placeholder="(e.g. Search Movies)" data-label="Search item"
									   data-plurality="plural"><span
										class="visuallyhidden">(e.g. Search Movies)</span><br>
								<p class="buddyforms-field-description description">Used as the text for the search
									button on post type list screen.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="not_found">Not Found</label></th>
							<td><input type="text" id="not_found" name="buddyforms_custom_post_type[not_found]" value=""
									   aria-required="false" placeholder="(e.g. No Movies found)"
									   data-label="No item found" data-plurality="plural"><span class="visuallyhidden">(e.g. No Movies found)</span><br>
								<p class="buddyforms-field-description description">Used when there are no posts to
									display on the post type list screen.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="not_found_in_trash">Not Found in Trash</label></th>
							<td><input type="text" id="not_found_in_trash"
									   name="buddyforms_custom_post_type[not_found_in_trash]"
									   value="" aria-required="false" placeholder="(e.g. No Movies found in Trash)"
									   data-label="No item found in trash" data-plurality="plural"><span
										class="visuallyhidden">(e.g. No Movies found in Trash)</span><br>
								<p class="buddyforms-field-description description">Used when there are no posts to
									display on the post type list trash screen.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="parent">Parent</label></th>
							<td><input type="text" id="parent" name="buddyforms_custom_post_type[parent]" value=""
									   aria-required="false"
									   placeholder="(e.g. Parent Movie:)" data-label="Parent item:"
									   data-plurality="singular"><span
										class="visuallyhidden">(e.g. Parent Movie:)</span><br>
								<p class="buddyforms-field-description description">Used for hierarchical types that
									need a colon.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="featured_image">Featured Image</label></th>
							<td><input type="text" id="featured_image"
									   name="buddyforms_custom_post_type[featured_image]" value=""
									   aria-required="false" placeholder="(e.g. Featured image for this movie)"
									   data-label="Featured image for this item" data-plurality="singular"><span
										class="visuallyhidden">(e.g. Featured image for this movie)</span><br>
								<p class="buddyforms-field-description description">Used as the "Featured Image" phrase
									for the post type.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="set_featured_image">Set Featured Image</label></th>
							<td><input type="text" id="set_featured_image"
									   name="buddyforms_custom_post_type[set_featured_image]"
									   value="" aria-required="false"
									   placeholder="(e.g. Set featured image for this movie)"
									   data-label="Set featured image for this item" data-plurality="singular"><span
										class="visuallyhidden">(e.g. Set featured image for this movie)</span><br>
								<p class="buddyforms-field-description description">Used as the "Set featured image"
									phrase for the post type.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="remove_featured_image">Remove Featured Image</label></th>
							<td><input type="text" id="remove_featured_image"
									   name="buddyforms_custom_post_type[remove_featured_image]"
									   value="" aria-required="false"
									   placeholder="(e.g. Remove featured image for this movie)"
									   data-label="Remove featured image for this item" data-plurality="singular"><span
										class="visuallyhidden">(e.g. Remove featured image for this movie)</span><br>
								<p class="buddyforms-field-description description">Used as the "Remove featured image"
									phrase for the post type.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="use_featured_image">Use Featured Image</label></th>
							<td><input type="text" id="use_featured_image"
									   name="buddyforms_custom_post_type[use_featured_image]"
									   value="" aria-required="false"
									   placeholder="(e.g. Use as featured image for this movie)"
									   data-label="Use as featured image for this item" data-plurality="singular"><span
										class="visuallyhidden">(e.g. Use as featured image for this movie)</span><br>
								<p class="buddyforms-field-description description">Used as the "Use as featured image"
									phrase for the post type.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="archives">Archives</label></th>
							<td><input type="text" id="archives" name="buddyforms_custom_post_type[archives]" value=""
									   aria-required="false" placeholder="(e.g. Movie archives)"
									   data-label="item archives" data-plurality="singular"><span
										class="visuallyhidden">(e.g. Movie archives)</span><br>
								<p class="buddyforms-field-description description">Post type archive label used in nav
									menus.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="insert_into_item">Insert into item</label></th>
							<td><input type="text" id="insert_into_item"
									   name="buddyforms_custom_post_type[insert_into_item]" value=""
									   aria-required="false" placeholder="(e.g. Insert into movie)"
									   data-label="Insert into item" data-plurality="singular"><span
										class="visuallyhidden">(e.g. Insert into movie)</span><br>
								<p class="buddyforms-field-description description">Used as the "Insert into post" or
									"Insert into page" phrase for the post type.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="uploaded_to_this_item">Uploaded to this Item</label></th>
							<td><input type="text" id="uploaded_to_this_item"
									   name="buddyforms_custom_post_type[uploaded_to_this_item]"
									   value="" aria-required="false" placeholder="(e.g. Uploaded to this movie)"
									   data-label="Upload to this item" data-plurality="singular"><span
										class="visuallyhidden">(e.g. Uploaded to this movie)</span><br>
								<p class="buddyforms-field-description description">Used as the "Uploaded to this post"
									or "Uploaded to this page" phrase for the post type.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="filter_items_list">Filter Items List</label></th>
							<td><input type="text" id="filter_items_list"
									   name="buddyforms_custom_post_type[filter_items_list]" value=""
									   aria-required="false" placeholder="(e.g. Filter movies list)"
									   data-label="Filter item list" data-plurality="plural"><span
										class="visuallyhidden">(e.g. Filter movies list)</span><br>
								<p class="buddyforms-field-description description">Screen reader text for the filter
									links heading on the post type listing screen.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="items_list_navigation">Items List Navigation</label></th>
							<td><input type="text" id="items_list_navigation"
									   name="buddyforms_custom_post_type[items_list_navigation]"
									   value="" aria-required="false" placeholder="(e.g. Movies list navigation)"
									   data-label="item list navigation" data-plurality="plural"><span
										class="visuallyhidden">(e.g. Movies list navigation)</span><br>
								<p class="buddyforms-field-description description">Screen reader text for the
									pagination heading on the post type listing screen.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="items_list">Items List</label></th>
							<td><input type="text" id="items_list" name="buddyforms_custom_post_type[items_list]"
									   value=""
									   aria-required="false" placeholder="(e.g. Movies list)" data-label="item list"
									   data-plurality="plural"><span
										class="visuallyhidden">(e.g. Movies list)</span><br>
								<p class="buddyforms-field-description description">Screen reader text for the items
									list heading on the post type listing screen.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="attributes">Attributes</label></th>
							<td><input type="text" id="attributes" name="buddyforms_custom_post_type[attributes]"
									   value=""
									   aria-required="false" placeholder="(e.g. Movies Attributes)"
									   data-label="item attributes" data-plurality="plural"><span
										class="visuallyhidden">(e.g. Movies Attributes)</span><br>
								<p class="buddyforms-field-description description">Used for the title of the post
									attributes meta box.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="name_admin_bar">"New" menu in admin bar</label></th>
							<td><input type="text" id="name_admin_bar"
									   name="buddyforms_custom_post_type[name_admin_bar]" value=""
									   aria-required="false" placeholder="(e.g. Movie)" data-label="item"
									   data-plurality="singular"><span class="visuallyhidden">(e.g. Movie)</span><br>
								<p class="buddyforms-field-description description">Used in New in Admin menu bar.
									Default "singular name" label.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="item_published">Item Published</label></th>
							<td><input type="text" id="item_published"
									   name="buddyforms_custom_post_type[item_published]" value=""
									   aria-required="false" placeholder="(e.g. Movie published)"
									   data-label="item published" data-plurality="singular"><span
										class="visuallyhidden">(e.g. Movie published)</span><br>
								<p class="buddyforms-field-description description">Used in the editor notice after
									publishing a post. Default "Post published." / "Page published."</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="item_published_privately">Item Published Privately</label></th>
							<td><input type="text" id="item_published_privately"
									   name="buddyforms_custom_post_type[item_published_privately]" value=""
									   aria-required="false"
									   placeholder="(e.g. Movie published privately.)"
									   data-label="item published privately." data-plurality="singular"><span
										class="visuallyhidden">(e.g. Movie published privately.)</span><br>
								<p class="buddyforms-field-description description">Used in the editor notice after
									publishing a private post. Default "Post published privately." / "Page published
									privately."</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="item_reverted_to_draft">Item Reverted To Draft</label></th>
							<td><input type="text" id="item_reverted_to_draft"
									   name="buddyforms_custom_post_type[item_reverted_to_draft]"
									   value="" aria-required="false" placeholder="(e.g. Movie reverted to draft)"
									   data-label="item reverted to draft." data-plurality="singular"><span
										class="visuallyhidden">(e.g. Movie reverted to draft)</span><br>
								<p class="buddyforms-field-description description">Used in the editor notice after
									reverting a post to draft. Default "Post reverted to draft." / "Page reverted to
									draft."</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="item_scheduled">Item Scheduled</label></th>
							<td><input type="text" id="item_scheduled"
									   name="buddyforms_custom_post_type[item_scheduled]" value=""
									   aria-required="false" placeholder="(e.g. Movie scheduled)"
									   data-label="item scheduled" data-plurality="singular"><span
										class="visuallyhidden">(e.g. Movie scheduled)</span><br>
								<p class="buddyforms-field-description description">Used in the editor notice after
									scheduling a post to be published at a later date. Default "Post scheduled." / "Page
									scheduled."</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="item_updated">Item Updated</label></th>
							<td><input type="text" id="item_updated" name="buddyforms_custom_post_type[item_updated]"
									   value=""
									   aria-required="false" placeholder="(e.g. Movie updated)"
									   data-label="item updated." data-plurality="singular"><span
										class="visuallyhidden">(e.g. Movie updated)</span><br>
								<p class="buddyforms-field-description description">Used in the editor notice after
									updating a post. Default "Post updated." / "Page updated."</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="enter_title_here">Add Title</label></th>
							<td><input type="text" id="enter_title_here"
									   name="buddyforms_custom_post_type[enter_title_here]" value=""
									   aria-required="false" placeholder="(e.g. Add Movie)" data-label="Add item"
									   data-plurality="singular"><span
										class="visuallyhidden">(e.g. Add Movie)</span><br>
								<p class="buddyforms-field-description description">Placeholder text in the "title"
									input when creating a post. Not exportable.</p></td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<?php
}


class Michael_Ecklunds_Admin_Customizer
{
	function __construct()
	{
		add_action('in_admin_header', array($this, 'in_admin_header'));
	}

	function in_admin_header()
	{
		global $wp_meta_boxes;
		unset($wp_meta_boxes['bf-post-types']['side']['core']['submitdiv']);
		unset($wp_meta_boxes['bf-post-types']['normal']['core']['slugdiv']);
	}
}

new Michael_Ecklunds_Admin_Customizer();
