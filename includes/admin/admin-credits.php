<style>
    #message{display:none;}
    .error {
        display: none;
    }
    h1{
        display:none;
    }

</style>

<script>

    jQuery(document).ready(function() {

        jQuery('#screen-meta-links').hide();
        jQuery('body').find('h1:first').css('line-height', '58px');
        jQuery('body').find('h1:first').css('font-size', '30px');
        //jQuery('body').find('h1:first').addClass('tk-icon-buddyforms');
        jQuery('body').find('h1:first').html('<div style="font-size: 52px; margin-top: -5px; float: left; margin-right: 15px;" class="tk-icon-buddyforms"></div> ' +
            'BuddyForms <small style="float:right; font-size: 20px; padding-top: 23px;" >Version <?php echo BUDDYFORMS_VERSION ?></small>'
        );
        jQuery('h1').show();
    }); //  ready

</script>

