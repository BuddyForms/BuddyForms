<div class="tk-container tk-is-fluid">
<!--	<div class="tk-notification tk-is-primary">-->
<!--		This container is <strong>fluid</strong>: it will have a 32px gap on either side, on any-->
<!--		viewport size.-->
<!--	</div>-->
	<div class="tk-tabs tk-is-toggle tk-is-fullwidth">
		<ul>
			<li data-bf-editor-section-button="1" class="tk-is-active">
				<a>
					<span class="tk-icon tk-is-small"><i class="tk-fas tk-fa-list-alt" aria-hidden="true"></i></span>
					<span><?php _e( 'Editor', 'buddyforms' ); ?></span>
				</a>
			</li>
			<li data-bf-editor-section-button="2">
				<a>
					<span class="tk-icon tk-is-small"><i class="tk-fas tk-fa-tools" aria-hidden="true"></i></span>
					<span><?php _e( 'Settings', 'buddyforms' ); ?></span>
				</a>
			</li>
			<li data-bf-editor-section-button="3">
				<a>
					<span class="tk-icon tk-is-small"><i class="tk-fas tk-fa-palette" aria-hidden="true"></i></span>
					<span><?php _e( 'Designer', 'buddyforms' ); ?></span>
				</a>
			</li>
		</ul>
	</div>


	<section data-bf-editor-section="1" hidden="hidden">
		<?php buddyforms_form_editor_elements( $post ); ?>
	</section>


	<section data-bf-editor-section="2" hidden="hidden">
		<?php buddyforms_metabox_form_setup(); ?>
	</section>


	<section data-bf-editor-section="3" hidden="hidden">
		<?php buddyforms_metabox_form_designer(); ?>
	</section>
</div>
