<?php
namespace ClebinGames\SpecTiledTool;

/**
 * Class representing an object map
 */
class ObjectMap {

    private $num = 0;
    private $data = false;
    private $filename = false;
    private $objects = [];
    private $output = [];

    public function __construct($num, $layer)
    {
        $this->num = 0;
        $this->ReadLayer($layer['objects']);
    }

    public function SetData($data)
    {
        $this->data = $data;
    }

    public function SetName($name)
    {
        $this->name = SpecTiledTool::GetConvertedCodeName($name.'-object-map');
        $this->filename = SpecTiledTool::GetConvertedFilename($name.'-object-map');
    }

    public function GetOutputFilename()
    {
        $filename = SpecTiledTool::GetOutputFolder();
        $filename .= $this->filename.'.'.SpecTiledTool::GetOutputFileExtension();
        
        return $filename;
    }

    /**
     * Read an Tiled object layer
     */
    public function ReadLayer($layer)
    {
        // loop through objects on layer
        foreach($layer as $json) {

            echo 'Found object "'.$json['name'].'"'.CR;

            // create new object
            $obj = new GameObject($json);

            // add to array
            $this->objects[] = $obj;
        }

        foreach($this->objects as $obj) {
            // add to output array
            $this->output[] = $obj->GetIndex();
            $this->output[] = $obj->GetRow();
            $this->output[] = $obj->GetCol();
        }

    }

    /**
     * 
     * Get code for screen in currently set language
     */
    public function GetCode()
    {
        switch( SpecTiledTool::GetFormat() ) {
            case 'c':
                return $this->GetC();
                break;
            default:
                return $this->GetAsm();
            break;
        }
    }

    public function GetC()
    {
        return SpecTiledTool::GetCArray(
            $this->name, 
            $this->output, 
            10
        ).CR;
    }

    public function GetAsm()
    {
        // add array length at the beginning
        array_unshift($this->output, sizeof($this->output));
        
        $str = 'SECTION '.SpecTiledTool::GetCodeSection().CR;

        $str .= SpecTiledTool::GetAsmArray(
            $this->name, 
            $this->output, 
            10,
            8
        ).CR;
        
        return $str;
    }
}
