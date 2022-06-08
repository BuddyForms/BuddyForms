<?php
/** @var int $size */
/** @var string[] $errors */
?>

<div class="bf-alert error is-dismissible">
	<strong class="alert-heading"><?php echo esc_html__( 'The following errors were found:', 'buddyforms' ); ?></strong>
	<ul style="padding: 0; padding-inline-start: 40px;">
		<li><?php echo esc_html( $errors_string ); ?></li>
	</ul>
</div>
