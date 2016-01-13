<?php

namespace Xlib\XListaDados\FieldFormatter;

class Sufix extends \Xlib\XListaDados\FieldFormatterAbstract {
    
    protected $sufix = "";
    
    public function __construct ( $sufix ) {
        $this->sufix = $sufix;
    }
    
    public function format ( $dataIn ) {
        return $this->sufix . $dataIn ;
    }
    
}