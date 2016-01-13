<?php

namespace Xlib\XListaDados\FieldFormatter;

class Bold extends \Xlib\XListaDados\FieldFormatterAbstract {
    
    public function format ( $dataIn ) {
        return "<strong>" . $dataIn . "</strong>";
    }
    
    
}