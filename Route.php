<?php

namespace Xlib;

class Route {
	
	protected $defaultRoute    = "/:controller:/:action:/";
	// protected $defaultRoute = "/:module:/:controller:/:action:/"
	protected $controllerPath  = "" ;
	protected $viewPath        = "";
	protected $module          = "" ;
	protected $controller      = "Index" ;
	protected $action          = "index" ;
	protected $routes          = array ( );

	public function __construct ( ) {
		if ( !empty ( $_SERVER['PATH_INFO'] ) ) $this->makeRoute ( $_SERVER['PATH_INFO'] ) ;
		
	}

	public function makeRoute ( $routeInputString ) {

		$defaultRouteArr 	= explode ( "/" , preg_replace ( '/^\/|\/$/', "" , $this->defaultRoute ) );
		$requestRoute 		= explode ( "/" , preg_replace ( '/^\/|\/$/', "" , $routeInputString ) );

		for ( $i = 0 ; $i < count ( $defaultRouteArr ) ; $i++ ) {
			switch ( $defaultRouteArr[$i] ) {
				case ':module:':	 $this->module     = $requestRoute[$i] ; break ;
				case ':controller:': $this->controller = empty ( $requestRoute[$i] ) ? "index" : $requestRoute[$i] ; break ;
				case ':action:':	 $this->action     = empty ( $requestRoute[$i] ) ? "index" : $requestRoute[$i] ; break ;
				default : throw new Exception ( "Bad route configuration" );
			}
		}

		unset ( $i );
		$key 	= null ;
		$value 	= null ;

		for ( $i = count ( $defaultRouteArr ) ; $i < count ( $requestRoute ) ; $i++ ) {
			if ( $key === null ) {
				$key = $requestRoute[$i];
				if ( !isset ( $_POST[$key] ) ) Request::set($key,"");
			} else if ( $value === null ) {
				$value = $requestRoute[$i];
				if ( !isset ( $_POST[$key] ) ) Request::set($key,$value);
				$key = $value = null ;
			}
		}

	}

	/**
	 * se estiver setado o atributo "template"  na controller, faz o carregamento da mesma
	 * e incluir a saída da action no atributo $this->actionOutput para ser exibido no template
	 * se não estiver setado, apenas renderizar a view com os atributos assinados na controller
	 * @return [type] [description]
	 */
	public function dispatch ( ) {

		$fileName = realpath($this->controllerPath . DIRECTORY_SEPARATOR . ucwords($this->controller) . "Controller.php") or $fileName = realpath($this->controllerPath . DIRECTORY_SEPARATOR . $this->controller . "Controller.php") ;
		if ( !$fileName ) return $this->error404 ( "Controller não encontrada" );
		
		require_once ( $fileName );
		
		$className  = ucwords($this->controller)."Controller";
		$Controller = new $className();
		$Controller->preDispatch();
		$actionName = $this->action . "Action" ;
		if ( !method_exists ( $Controller , $actionName ) ) return $this->error404 ( "Action não encontrada" );
		$Controller->$actionName();
		if ( $viewFileName = realpath ( $this->viewPath . "/" . strtolower ( $this->controller ) . "/" . $this->action . ".html" ) ) {
			if ( $templateFileName = $Controller->getTemplate ( ) ) {
				ob_start();
				$Controller->processView ( $viewFileName ) ;
				$this->actionOutput = ob_get_contents();
				ob_end_clean();
				require ( $templateFileName );
			} else {
				$Controller->processView ( $viewFileName ) ;
			}
		}

	}

	public function error404 ( $message ) {
		$dump = array (
			'path' => $this->controllerPath . DIRECTORY_SEPARATOR . ucwords($this->controller) . "Controller.php" ,
			'realpath' => realpath($this->controllerPath . DIRECTORY_SEPARATOR . ucwords($this->controller) . "Controller.php"),
		);
		// pr($dump);
		throw new \Exception ( $message );
	}

	public function addRoute ( $url , Array $resource ) {

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

	public function setViewPath ( $viewPath ) {
		$this->viewPath = $viewPath ;
	}

}

