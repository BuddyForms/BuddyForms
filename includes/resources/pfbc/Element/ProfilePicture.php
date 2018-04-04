<?php
/**
 * Class Element_Upload
 */
class Element_Profile_Picture extends Element_Textbox
{
    /**
     * @var int
     */
    public $bootstrapVersion = 3;
    /**
     * @var array
     */
    protected $_attributes = array("type" => "file","file_limit"=>"","accepted_files"=>"","multiple_files"=>"","delete_files"=>"","description"=>"","mandatory"=>"");
    public function render()
    {
        global $buddyforms;
        ob_start();
        parent::render();
        $box = ob_get_contents();
        ob_end_clean();
        $id = $this->getAttribute('id');

        $box = str_replace("class=\"form-control\"", "class=\"dropzone\"", $box);
        $box = "<div class=\"dropzone dz-clickable\" id=\"$id\" >
                                 <div class=\"dz-default dz-message\" data-dz-message=\"\">
                                  
                                      <span>Hola</span>
                                 </div>
                                <input type='text' style='visibility: hidden' name='$id' value='' id='field_$id'  />
                                 
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
        $jscript = " var entries = 
        
        ";
        echo $jscript;
    }
}