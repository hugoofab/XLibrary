<?php

namespace Xlib\XListaDados\FieldFormatter;

class Center extends \Xlib\XListaDados\FieldFormatterAbstract {
    
    public function format ( $dataIn ) {
        return "<center>" . $dataIn . "</center>";
    }
    
    
}