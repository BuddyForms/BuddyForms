<?php

function bf_import_export_screen(){ ?>

    <div id="bf_admin_wrap" class="wrap">

        <div class="credits">
            <p>
                <a class="buddyforms" href="http://buddyforms.com" title="BuddyForms" target="_blank"><img src="<?php echo plugins_url( 'img/buddyforms-s.png' , __FILE__ ); ?>" title="BuddyForms" /></a>
                - &nbsp; <?php _e( 'Collaborative Publishing and Form Magic for WordPress.', 'buddyforms' ); ?>
            </p>
        </div>

        <h1 style="line-height: 58px; margin-top: 20px;"><div style="font-size: 52px; margin-top: -2px; float: left; margin-right: 15px;" class="tk-icon-buddyforms"></div> BuddyForms <span class="version">Beta 1.0</span></h1>

        <div id="bf_support_nav" class="button-nav">
            <a class="btn btn-small" href="https://themekraft.zendesk.com/hc/en-us/categories/200022561-BuddyForms" title="BuddyForms Documentation" target="_new"><i class="icon-list-alt"></i> Documentation</a>
            <a onClick="script: Zenbox.show(); return false;" class="btn btn-small" href="#" title="Write us. Bugs. Ideas. Whatever."><i class="icon-comment"></i> Submit a support ticket</a>
            <!--            &nbsp; &nbsp;-->
            <a class="btn btn-small" href="https://themekraft.zendesk.com/hc/communities/public/topics/200001402-BuddyForms-Ideas" title="Add and vote for ideas in our Ideas Forums!" target="_new"><i class="icon-plus-sign"></i> Submit your ideas</a>
            <a class="btn btn-small" href="https://themekraft.zendesk.com/hc/communities/public/topics/200001402-BuddyForms-Ideas" title="Learn, share and discuss with other users in our free community forums!" target="_new"><i class="icon-circle-arrow-right"></i> Visit community forums</a>
        </div>

        <hr />
		
		<div id="post-body">
			<div id="post-body-content">  
				<?php bf_import_export_tabs(); ?>
			</div>
		</div>
	</div>
	
<?php 
}

function bf_import_export_control(){
		if (isset($_POST['action'])) {
		
			$options = get_option('x2_loop_designer_options');
			$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';
			switch ( $action ) {
				case 'Export':
					$method = 'Export';
					$done = 'cap_serialize_export';
					header('Content-Type: text/csv; charset=utf-8');
					header('Content-Disposition: attachment; filename=loop-designer-export.txt');
					// create a file pointer connected to the output stream
					echo serialize( $options );
					exit();
					break;
				case 'Import':
					$method = 'Import';
						if(empty($_FILES['file']['tmp_name']))
							return ;
						$data = unserialize( implode ('', file ($_FILES['file']['tmp_name'])));
						update_option("x2_loop_designer_options", $data);
					break;
				case 'Reset to default':
					$method = 'remove-all';
						delete_option("x2_loop_designer_options");
				break;
			}	

	}
	
}

function bf_import_export_tabs() { ?>
	
		<ul id="bf_import_export_tabs" class="nav nav-tabs">
		  <li class="active"><a href="#export" data-toggle="tab">Export</a></li>
		  <li><a href="#import" data-toggle="tab">Import</a></li>
		</ul>
		<div class="tab-content">
		  <div class="tab-pane active" id="export"><?php bf_export_tab(); ?></div>
		  <div class="tab-pane" id="import"><?php bf_import_tab(); ?></div>
		</div>

	<?php
}

function bf_export_tab(){ 
	$buddyforms = get_option('buddyforms_options'); ?>

    <h2><?php _e('Export Forms', 'buddyforms')?></h2>
    <p><?php _e('Select the forms you want to export', 'buddyforms')?></p>
    <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
	
	<?php bf_get_forms_table($buddyforms['buddyforms']); ?>
	
        <p class="submit alignleft">
            <input id="bf_export" name="bf_export" class="btn btn-primary" type="button" value="<?php _e('Export Selected Forms','buddyforms');?>" />
        </p>
	</form>

<?php }

function bf_import_tab(){ ?>

    <h2><?php _e('Import Forms', 'buddyforms')?></h2>
    <p><?php _e('Select the forms you want to import', 'buddyforms')?></p>
	<input type="file" id="files" name="files[]" multiple />
	<output id="list"></output>
<?php 
}

function bf_get_forms_table($buddyforms) { ?>
	<table class="wp-list-table widefat fixed posts">	
	<thead>
		<tr>
			<th scope="col" id="cb" class="manage-column column-cb check-column" style="">
				<label class="screen-reader-text" for="cb-select-all-1">Select All</label>
				<input id="cb-select-all-1" type="checkbox">
			</th>
			<th scope="col" id="name" class="manage-column column-comment sortable desc" style="">Name</th>
			<th scope="col" id="slug" class="manage-column column-description" style="">Slug</th>
			<th scope="col" id="attached-post-type" class="manage-column column-status" style="">Attached Post Type</th>
			<th scope="col" id="attached-page" class="manage-column column-status" style="">Attached Page</th>

	</thead>
	<?php foreach ($buddyforms as $key => $buddyform) {?>
		<tr>
			<th scope="row" class="check-column">
				<label class="screen-reader-text" for="aid-<?php echo $buddyform['slug'] ?>"><?php echo $buddyform['name']; ?></label>
				<input type="checkbox" name="bf_export_form_slugs[]" value="<?php echo $buddyform['slug'] ?>" id="aid-<?php echo $buddyform['slug'] ?>">
			</th>
			<td class="slug column-slug">
				<?php echo isset($buddyform['name']) ? $buddyform['name']: '--'; ?>
			</td>
			<td class="slug column-slug">
				<?php echo isset($buddyform['slug']) ? $buddyform['slug']: '--'; ?>
			</td>
			<td class="slug column-slug">
				<?php echo isset($buddyform['post_type']) ? $buddyform['post_type']: '--'; ?>
			</td>
			<td class="slug column-slug">
				<?php echo isset($buddyform['attached_page']) ? get_the_title($buddyform['attached_page']): '--'; ?>
			</td>
		</tr>
	<?php } ?>
	</table>
<?php }
