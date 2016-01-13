<?php

namespace Xlib\XListaDados\FieldFormatter;

class CPF extends \Xlib\XListaDados\FieldFormatterAbstract {

    public function getInstance ( ) {
        return new CPF ;
    }

    public function format ( $dataIn ) {

    	if ( preg_match ( '/^\d{11}$/' , $dataIn ) ) {
    		return preg_replace ( '/^(\d{3})(\d{3})(\d{3})(\d{2})/' , "$1.$2.$3-$4" , $dataIn );
    	}

        return $dataIn;
    }


}