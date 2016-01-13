<?php

namespace Xlib\XListaDados\FieldFormatter;

class FloatRight extends \Xlib\XListaDados\FieldFormatterAbstract {

    public function format ( $dataIn ) {
        return "<div style=\"float:right;\">" . $dataIn . "</div>";
    }
    
    
}