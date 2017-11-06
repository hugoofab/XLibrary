<?php

namespace Xlib\XListaDados\FieldFormatter;

class Image extends \Xlib\XListaDados\FieldFormatterAbstract {
    
    protected $address ;
    protected $attributeList ;

    public function __construct ( $address , array $attributeList = array ( ) ) {
    	$this->address = $address;
    	$this->attributeList = $attributeList;
    }

    public function format ( $dataIn ) {
    	$attributes = "";
    	$imageUrl = $this->address . $dataIn ;
    	foreach ( $this->attributeList as $key => $val ) {
    		$attributes .= " $key=\"$val\" " ;
    	}
        return "<img src=\"$imageUrl\" $attributes />" ;
    }
    
    
}