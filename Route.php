<?php

namespace Xlib;

class Route {
	
	protected $defaultRoute 	= "/:controller:/:action:/";
	// protected $defaultRoute = "/:module:/:controller:/:action:/"
	protected $controllerPath 	= "" ;
	protected $module 			= "" ;
	protected $controller 		= "Index" ;
	protected $action 			= "index" ;

	public function __construct ( ) {
		if ( !empty ( $_SERVER['PATH_INFO'] ) ) $this->makeRoute ( $_SERVER['PATH_INFO'] ) ;
		
	}

	public function makeRoute ( $routeInputString ) {

		$defaultRouteArr 	= explode ( "/" , preg_replace ( '/^\/|\/$/', "" , $this->defaultRoute ) );
		$requestRoute 		= explode ( "/" , preg_replace ( '/^\/|\/$/', "" , $routeInputString ) );

		for ( $i = 0 ; $i < count ( $defaultRouteArr ) ; $i++ ) {
			switch ( $defaultRouteArr[$i] ) {
				case ':module:':		$this->module = $requestRoute[$i]; 			break ;
				case ':controller:':	
					$this->controller = empty ( $requestRoute[$i] ) ? "index" : $requestRoute[$i] ; 		
				break ;
				case ':action:':		
					$this->action = empty ( $requestRoute[$i] ) ? "index" : $requestRoute[$i] ; 			
				break ;
				default : throw new Exception ( "Bad route configuration" );
			}
		}
// pr($this->controller);
// pr($this->action);
		unset ( $i );
		$key 	= null ;
		$value 	= null ;

		for ( $i = count ( $defaultRouteArr ) ; $i < count ( $requestRoute ) ; $i++ ) {
			if ( $key === null ) {
				$key = $requestRoute[$i];
				Request::set($key,"");
				continue ;
			} else if ( $value === null ) {
				$value = $requestRoute[$i];
				continue ;
			} else {
				Request::set($key,$value);
				$key = $value = null ;
			}

		}

	}

	public function dispatch ( ) {

		$fileName = realpath($this->controllerPath . DIRECTORY_SEPARATOR . ucwords($this->controller) . "Controller.php") or $fileName = realpath($this->controllerPath . DIRECTORY_SEPARATOR . $this->controller . "Controller.php") ;
		if ( !$fileName ) return $this->error404 ( );
		
		require_once ( $fileName );
		// pr($fileName);
		// pr($this->controller);
		
		$className  = ucwords($this->controller)."Controller";
		$Controller = new $className();
		$Controller->preDispatch();
		$actionName = $this->action . "Action" ;
		$Controller->$actionName();

	}

	public function error404 ( ) {
		$dump = array (
			'path' => $this->controllerPath . DIRECTORY_SEPARATOR . ucwords($this->controller) . "Controller.php" ,
			'realpath' => realpath($this->controllerPath . DIRECTORY_SEPARATOR . ucwords($this->controller) . "Controller.php"),
		);
		pr($dump);
		die ( "página não encontrada" ) ;
	}

	public function setModule ( $module ) {
		$this->module = $module ;
	}

	public function setController ( $controller ) {
		$this->controller = $controller ;
	}

	public function setAction ( $action ) {
		$this->action = $action ;
	}

	public function setControllerPath ( $controllerPath ) {
		$this->controllerPath = $controllerPath ;
	}

}

