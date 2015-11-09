<?php

namespace Xlib;

class Controller {

	protected $public = false ;
	protected $Response ;

	public function __construct ( ) {
		$this->Response = new Response();
	}

	public function preDispatch ( ) {

	}

	public function isPublic ( ) {
		return $this->public ;
	}
		
}

