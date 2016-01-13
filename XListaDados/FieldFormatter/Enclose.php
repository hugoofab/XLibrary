<?php

namespace Xlib\XListaDados\FieldFormatter;

class Enclose extends \Xlib\XListaDados\FieldFormatterAbstract {

    protected $tagName = "";
    protected $attributeAsString = "";
    
    public function __construct ( $tagName , Array $attributeList  ) {
        $this->tagName = $tagName ;
        $this->attributeAsString = $this->getAttributeSetAsString ( $attributeList ) ;
    }
    
    public function format ( $dataIn ) {
        return "<" . $this->tagName . " $this->attributeAsString >" . $dataIn . "</" . $this->tagName . ">" ;
    }
    
    public function getAttributeSetAsString ( Array $attributeSet = null ) {
        
        $attributeSetString = '';
        if ( empty ( $attributeSet ) ) $attributeSet = $this->getAttributeSet ( );
        
        foreach ( $attributeSet as $key => $value ) $attributeSetString .= " $key=\"$value\" " ;
        
        return $attributeSetString ; 
        
    }
    
}