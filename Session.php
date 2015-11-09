<?php

namespace Xlib;

class Session {

	public static $namespace = 'SUPERGLOBALS' ;

    public static function set ( $key , $value ) {
    	$_SESSION[Session::$namespace][$key] = $value ;
    	return $value ;
    }

    public static function get ( $key ) {
    	return $_SESSION[Session::$namespace][$key];
    }

    public static function renew (  ) {
    	session_regenerate_id ( true ) ;
    }

    public static function getId ( ) {
    	return session_id ( );
    }


}