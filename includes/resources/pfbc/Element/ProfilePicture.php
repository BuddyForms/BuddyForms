<?php
/**
 * Class Element_Upload
 */
class Element_ProfilePicture extends Element_Textbox
{
    /**
     * @var int
     */
    public $bootstrapVersion = 3;
    /**
     * @var array
     */
   // protected $_attributes = array("type" => "file","file_limit"=>"","accepted_files"=>"","multiple_files"=>"","delete_files"=>"","description"=>"","mandatory"=>"");
    public function render()
    {
        global $buddyforms;
        $this->add_scripts();
        ob_start();
        parent::render();
        $box = ob_get_contents();
        ob_end_clean();
        $id = $this->getAttribute('id');
       //$part=   bp_get_template_part( 'members/single/profile/change-avatar' );
        bp_attachments_get_template_part( 'avatars/index' );
    }

    function  custom_avatar_scripts(){
        bp_attachments_enqueue_scripts( 'BP_Attachment_Avatar' );

        // Add Some actions for Theme backcompat.
        add_action( 'bp_after_profile_avatar_upload_content', 'bp_avatar_template_check' );
        add_action( 'bp_after_group_admin_content',           'bp_avatar_template_check' );
        add_action( 'bp_after_group_avatar_creation_step',    'bp_avatar_template_check' );
    }
    public function add_scripts(  ) {
        global $bp;

        $user_id = get_current_user_id();
        $bp->displayed_user->id = $user_id;
        $bp->displayed_user->domain = bp_core_get_user_domain( $bp->displayed_user->id );
        $bp->displayed_user->userdata = bp_core_get_core_userdata( $bp->displayed_user->id );
        $bp->displayed_user->fullname = bp_core_get_user_displayname( $bp->displayed_user->id );
        $url = buddypress()->plugin_url . 'bp-core/js/';
        $path = buddypress()->plugin_dir;
        require_once  $path.'bp-core/bp-core-attachments.php';
        require_once $path.'bp-core/classes/class-bp-attachment-avatar.php';
        require_once $path.'bp-core/bp-core-cssjs.php';
        $this->custom_avatar_scripts();
       // bp_core_avatar_scripts() ;
        //$bp_attachmett = new BP_Attachment_Avatar();
        //$bp_attachmett->script_data();
       // bp_attachments_enqueue_scripts();
        wp_enqueue_script( 'bp-avatar', "{$url}avatar.js", array( 'jquery' ) );
        wp_enqueue_script( 'bp-plupload', "{$url}bp-plupload.js", array( 'plupload', 'jquery', 'json2', 'wp-backbone' ) );


    }


    function renderJS()
    {
        $id = $this->getAttribute('id');
        echo "jQuery(\"#bp-avatar-upload a\").click();";


    }
}