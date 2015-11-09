<?php

namespace Xlib;

class Controller {

	protected $Response ;

	public function __construct ( ) {
		$this->Response = new Response();
		$this->init ( );
	}

	public function init ( ) {
		// este metodo não precisa fazer nada. deve ser sobrescrito em uma sub-classe
		// mas não é obrigatório
	}

	public function preDispatch ( ) {

	}
		
}

