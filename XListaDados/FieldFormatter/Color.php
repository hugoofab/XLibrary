<?php

namespace Xlib\XListaDados\FieldFormatter;

class Color extends \Xlib\XListaDados\FieldFormatterAbstract {
    
    public function format ( $dataIn ) {
    	$color = preg_match ( '/^#[a-zA-Z0-9]+/' , $dataIn ) ? $dataIn : "#" . $dataIn ;
        return "<div style=\"display:block;background:$color;font-family:courier new;text-align:center;font-weight:bold;\">" . $dataIn . "</div>";
    }
    
    
}