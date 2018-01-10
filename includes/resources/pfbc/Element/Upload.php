<?php

/**
 * Class Element_Upload
 */
class Element_Upload extends Element_Textbox
{
    /**
     * @var int
     */
    public $bootstrapVersion = 3;
    /**
     * @var array
     */
    protected $_attributes = array("type" => "file","file_limit"=>"",);

    public function render()
    {
        ob_start();
        parent::render();
        $box = ob_get_contents();
        ob_end_clean();
        $max_size =  $this->getAttribute('file_limit');
        $id = $this->getAttribute('id');
        $box = str_replace("class=\"form-control\"", "class=\"dropzone\"", $box);
        $box = "<div class=\"dropzone dz-clickable\" id=\"$id\" file_limit='$max_size'>
                                 <div class=\"dz-default dz-message\" data-dz-message=\"\">
                                      <span>Drop files here to upload</span>
                                 </div>
                </div>";
        if ($this->bootstrapVersion == 3) {
            echo $box;
        } else {
            echo preg_replace("/(.*)(<input .*\/>)(.*)/i",
                '${1}<label class="file">${2}<span class="file-custom"></span></label>${3}', $box);
        }
    }

    function renderJS()
    {
        $id = $this->getAttribute('id');
        //echo 'jQuery("#' . $id . '").dropzone({ url: "/uploads" });';
    }


}
