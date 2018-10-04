<?php

namespace Xlib;

class Feedback {

	private static $warningTemplate = "
		<div class="alert alert-warning fade in">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<i class="im-warning alert-icon s24"></i>
			__message__
		</div>	
	" ;

	private static $errorTemplate = "
		<div class="alert alert-danger fade in">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<i class="im-cancel alert-icon s24"></i>
			__message__
		</div>
	" ;

	private static $successTemplate = "
		<div class="alert alert-success fade in">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<i class="im-checkmark3 alert-icon s24"></i>
			__message__
		</div>	
	" ;

	private static $infoTemplate = "
		<div class="alert alert-info fade in">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<i class="im-info alert-icon s24"></i>
			__message__
		</div>
	
	" ;

	private static $messages = [];

	public static function setWarnTemplate ( $template ) {
		self::$warningTemplate = $template;
	}

	public static function setErrorTemplate ( $template ) {
		self::$errorTemplate = $template;
	}

	public static function setSuccessTemplate ( $template ) {
		self::$successTemplate = $template;
	}

	public static function setInfoTemplate ( $template ) {
		self::$infoTemplate = $template;
	}

	public static function compileTemplate ( $message , $template ) {
		return preg_replace ( '/__message__/' , $message , $template ) ;
	}

	public static function warn ( $message , $template = "" ) {
		if ( empty ( $template ) ) $template = self::$warningTemplate ;
		Session::push( 'FEEDBACK_MESSAGES' , self::compileTemplate( $message , $template ) );
	}

	public static function error ( $message , $template = "" ) {
		if ( empty ( $template ) ) $template = self::$errorTemplate ;
		Session::push( 'FEEDBACK_MESSAGES' , self::compileTemplate( $message , $template ) );
	}

	public static function success ( $message , $template = "" ) {
		if ( empty ( $template ) ) $template = self::$successTemplate ;
		Session::push( 'FEEDBACK_MESSAGES' , self::compileTemplate( $message , $template ) );
	}

	public static function info ( $message , $template = "" ) {
		if ( empty ( $template ) ) $template = self::$infoTemplate ;
		Session::push( 'FEEDBACK_MESSAGES' , self::compileTemplate( $message , $template ) );
	}

	public static function getAsHTML ( ) {
		$arrayMessages = Session::get('FEEDBACK_MESSAGES');
		$output = "";
		foreach ( $arrayMessages as $item ) {
			$output .= $item;
		} 
		return $output;
	}

}