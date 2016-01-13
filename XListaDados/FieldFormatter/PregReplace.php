<?php

namespace Xlib\XListaDados\FieldFormatter;

class PregReplace extends \Xlib\XListaDados\FieldFormatterAbstract {

	private $ereg ;
	private $replace ;

	public function __construct ( $ereg , $replace ) {
		$this->ereg    = $ereg ;
		$this->replace = $replace ;
	}

    public function format ( $dataIn ) {
    	return preg_replace ( $this->ereg , $this->replace , $dataIn ) ;
    }


}