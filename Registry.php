<?php

namespace xlibrary;

class Registry {

	public static $namespace = 'SUPERGLOBALS' ;
	private static $registryArray = array ();

    public static function set ( $key , $value ) {
    	Registry::$registryArray[Registry::$namespace][$key] = $value ;
    }

    public static function get ( $key ) {
    	return Registry::$registryArray[Registry::$namespace][$key];
    }

}