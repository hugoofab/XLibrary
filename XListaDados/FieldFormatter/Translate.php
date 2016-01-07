<?php

namespace Xlib\XListaDados\FieldFormatter;

class Translate extends \Xlib\XListaDados\FieldFormatterAbstract {

	protected $translateArray = array ( ) ;

    public function __construct ( $translateArray ) {
        $this->translateArray = $translateArray ;
    }

    public function format ( $dataIn ) {
    	if ( isset ( $this->translateArray[$dataIn] ) ) return $this->translateArray[$dataIn];
        return  $dataIn ;
    }

}