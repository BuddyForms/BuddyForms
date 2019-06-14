<?php
/** @var int $size */
/** @var string[] $errors */
?>

<div class="bf-alert error is-dismissible">
    <strong class="alert-heading"><?php echo _n( 'The following error was found:', 'The following errors were found:', $size, 'buddyforms' ) ?></strong>
    <ul>
        <?php echo $errors ?>
    </ul>
</div>
