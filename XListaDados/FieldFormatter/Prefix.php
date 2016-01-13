<?php

namespace Xlib\XListaDados\FieldFormatter;

class Prefix extends \Xlib\XListaDados\FieldFormatterAbstract {

    protected $prefix = "";
    
    public function __construct ( $prefix ) {
        $this->prefix = $prefix;
    }
    
    public function format ( $dataIn ) {
        return $this->prefix . $dataIn ;
    }
    
}