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
        $this->add_styles();
        $this->add_scripts();
        ob_start();
        parent::render();
        $box = ob_get_contents();
        ob_end_clean();
        $id = $this->getAttribute('id');

        $box = str_replace("class=\"form-control\"", "class=\"dropzone\"", $box);
        $box = "<div class=\"bp-avatar-status\"><p class=\"warning\">If you'd like to delete the existing profile photo but not upload a new one, please use the delete tab.</p></div>";
        if ($this->bootstrapVersion == 3) {
          //  echo $box;
        } else {
            echo preg_replace("/(.*)(<input .*\/>)(.*)/i",
                '${1}<label class="file">${2}<span class="file-custom"></span></label>${3}', $box);
        }
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

    public function add_styles(){

        global $buddyforms;

        $min = bp_core_get_minified_asset_suffix();
        $url = buddypress()->plugin_url . 'bp-core/css/avatar.css';
        $verssion = bp_get_version();
      //  wp_register_style( 'bp-avatar', $url );
      /*  $admin_bar_file = apply_filters( 'bp_core_admin_bar_css', "{$url}admin-bar{$min}.css" );
        $styles = apply_filters( 'bp_core_register_common_styles', array(
            'bp-admin-bar' => array(
                'file'         => $admin_bar_file,
                'dependencies' => array( 'admin-bar' )
            ),
            'bp-avatar' => array(
                'file'         => "{$url}avatar{$min}.css",
                'dependencies' => array( 'jcrop' )
            ),
        ) );

        foreach ( $styles as $id => $style ) {
            wp_register_style( $id, $style['file'], $style['dependencies'], bp_get_version() );

            wp_style_add_data( $id, 'rtl', true );
            if ( $min ) {
                wp_style_add_data( $id, 'suffix', $min );
            }
        }*/
    }
    function renderJS()
    {
        $id = $this->getAttribute('id');
        $jscript = " var entries = 
        
        ";
        echo $jscript;
        $min = bp_core_get_minified_asset_suffix();
        $url = buddypress()->plugin_url . 'bp-core/js/';
        $scripts = array(
            // Legacy.
            'bp-confirm'        => array( 'file' => "{$url}confirm{$min}.js", 'dependencies' => array( 'jquery' ), 'footer' => false ),
            'bp-widget-members' => array( 'file' => "{$url}widget-members{$min}.js", 'dependencies' => array( 'jquery' ), 'footer' => false ),
            'bp-jquery-query'   => array( 'file' => "{$url}jquery-query{$min}.js", 'dependencies' => array( 'jquery' ), 'footer' => false ),
            'bp-jquery-cookie'  => array( 'file' => "{$url}vendor/jquery-cookie{$min}.js", 'dependencies' => array( 'jquery' ), 'footer' => false ),
            'bp-jquery-scroll-to' => array( 'file' => "{$url}vendor/jquery-scroll-to{$min}.js", 'dependencies' => array( 'jquery' ), 'footer' => false ),

            // Version 2.1.
            'jquery-caret' => array( 'file' => "{$url}vendor/jquery.caret{$min}.js", 'dependencies' => array( 'jquery' ), 'footer' => true ),
            'jquery-atwho' => array( 'file' => "{$url}vendor/jquery.atwho{$min}.js", 'dependencies' => array( 'jquery', 'jquery-caret' ), 'footer' => true ),

            // Version 2.3.
            'bp-plupload' => array( 'file' => "{$url}bp-plupload{$min}.js", 'dependencies' => array( 'plupload', 'jquery', 'json2', 'wp-backbone' ), 'footer' => true ),
            'bp-avatar'   => array( 'file' => "{$url}avatar{$min}.js", 'dependencies' => array( '' ), 'footer' => true ),
            'bp-webcam'   => array( 'file' => "{$url}webcam{$min}.js", 'dependencies' => array( 'bp-avatar' ), 'footer' => true ),

            // Version 2.4.
            'bp-cover-image' => array( 'file' => "{$url}cover-image{$min}.js", 'dependencies' => array(), 'footer' => true ),

            // Version 2.7.
            'bp-moment'    => array( 'file' => "{$url}vendor/moment-js/moment{$min}.js", 'dependencies' => array(), 'footer' => true ),
            'bp-livestamp' => array( 'file' => "{$url}vendor/livestamp{$min}.js", 'dependencies' => array( 'jquery', 'bp-moment' ), 'footer' => true ),
        );
        $scripts = apply_filters( 'bp_core_register_common_scripts', $scripts );


        $version = bp_get_version();
        foreach ( $scripts as $id => $script ) {
            wp_register_script( $id, $script['file'], $script['dependencies'], $version, $script['footer'] );
        }
    }
}