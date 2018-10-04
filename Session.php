<?php

namespace Xlib;

class Session {

	public static $namespace = 'SUPERGLOBALS' ;

    public static function set ( $key , $value ) {
    	$_SESSION[Session::$namespace][$key] = $value ;
    	return $value ;
    }

    public static function push ( $key , $value ) {
    	if ( !isset($_SESSION[Session::$namespace][$key]) || !is_array ( $_SESSION[Session::$namespace][$key] ) ) $_SESSION[Session::$namespace][$key] = array ( );
    	$_SESSION[Session::$namespace][$key][] = $value ;
    	return $value ;
    }

    public static function pull ( $array , $key = null ) {
    	if ( !is_array ( $_SESSION[Session::$namespace][$array] ) ) return null ;
    	if ( $key === null ) {
    		return $_SESSION[Session::$namespace][$array] ;    		
    	} else {
    		return $_SESSION[Session::$namespace][$array][$key] ;
    	}
    }

    public static function get ( $key ) {
    	return isset ( $_SESSION[Session::$namespace][$key] ) ? $_SESSION[Session::$namespace][$key] : null ;
    }

    public static function renew (  ) {
    	session_regenerate_id ( true ) ;
    }

    public static function getId ( ) {
    	return session_id ( );
    }

    public static function rm ( $key = null ) {
    	if ( !empty ( $key ) ) {
    		unset ( $_SESSION[Session::$namespace][$key] );
    	} else {
    		unset ( $_SESSION );
    	}
    }


}