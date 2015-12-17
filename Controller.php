<?php

namespace Xlib;

class Controller {

	protected $Response ;
	protected $template = null;

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

	public function processView ( $view ) {
		require ( $view );
	}

	public function setTemplate ( $template ) {
		$this->template = $template ;
	}
	
	public function getTemplate ( ) {
		if ( empty ( $this->template ) ) return false ;
		return realpath ( $this->template );
	}
		
}

