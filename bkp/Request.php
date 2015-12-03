<?php

namespace Xlib;

class Request {

    public static function get ( $key , $default = "" ) {
        if ( strpos ( $key , "[" ) ) return Request::getArrayRecursiveKey ( $key , $_REQUEST , $default ) ;
        if ( isset ( $_REQUEST[$key] ) ) return Request::basicFilter ( $_REQUEST[$key] ) ;
        return $default ;
    }


    // CHECAR A NECESSIDADE DESSES METODOS

			    /**
			     * preciso ver o que isso faz e a real necessidade para que isso esteja nesta classe genérica
			     * se não justificar a existencia deste método aqui, veja se é usado nos projetos atuais e 
			     * remova caso não seja necessário
			     * @param  [type] $key     [description]
			     * @param  [type] $REQUEST [description]
			     * @param  string $default [description]
			     * @return [type]          [description]
			     */
			    public static function getArrayRecursiveKey (  $key , $REQUEST , $default = "") {
			        $value      = $REQUEST;
			        $key        = str_replace( "]" , "" , $key );
			        $keyList    = explode ( "[" , $key );
			        foreach ( $keyList as $currKey ) {
			            if ( !isset ( $value[$currKey] ) ) return $default ;
			            $value = $value[$currKey];
			        }
			        return $value ;
			    }

			    /**
			     * recebe um array de keys e retorna true se AO MENOS UMA delas estiver setada
			     * @param array $keyList lista de chaves para buscar no request
			     * @return boolean
			     */
			    public static function isSetOneOf ( Array $keyList ) {
			        foreach ( $keyList as $key ) {
			            $val = Request::get($key) ;
			            if ( !empty ( $val ) ) return true ;
			        }
			        return false ;
			    }

			    /**
			     * recebe um array de keys e retorna true se TODAS delas estiverem setadas
			     * @param array $keyList lista de chaves para buscar no request
			     * @return boolean
			     */
			    public static function isSetAllOf ( Array $keyList ) {
			        foreach ( $keyList as $key ) {
			            $val = Request::get($key);
			            if ( empty ( $val ) ) return false ;
			        }
			        return true ;
			    }

	// CHECAR A NECESSIDADE DESSES METODOS ACIMA

	public static function getAsArray ( $source = "REQUEST" ) {
		
		switch ( strtoupper ( $source ) ) {
			case 'REQUEST' : $source = $_REQUEST ; break ;
			case 'POST'    : $source = $_POST    ; break ;
			case 'GET'     : $source = $_GET     ; break ;
			default        : $source = $_REQUEST ;
		}

		$output = array ( );
		foreach ( $source as $key => $value ) $output[$key] = $value ;
		return $output ;

	}

	public static function getArray ( $key , $default = array ( ) ) {
		$output = Request::get ( $key , $default ) ;
		if ( empty ( $output ) ) return array ( );
		if ( !is_array ( $output ) ) return array ( $output );
		return $output ;
	}

	public static function getIntArray ( $key , $default = array ( ) ) {
		$output = Request::get ( $key , $default ) ;
		if ( empty ( $output ) ) return array ( );
		if ( !is_array ( $output ) ) return array ( (int) $output );
		foreach ( $output as &$out ) $out = (int) $out ;
		return $output ;
	}

    public static function getInt ( $key , $default = 0 ) {
        return (int) preg_replace ( '/[^\d]+/' , '' , Request::get($key,$default) ) ;
    }

    public static function getPost ( $key , $default = "" ) {
        if ( strpos ( $key , "[" ) ) return Request::getArrayRecursiveKey ( $key , $_POST , $default ) ;
        if ( isset ( $_POST[$key] ) ) return Request::basicFilter ( $_POST[$key] ) ;
        return $default ;
    }

    public static function getGet ( $key , $default = "" ) {
        if ( isset ( $_GET[$key] ) ) return Request::basicFilter ( $_GET[$key] ) ;
        return $default ;
    }

    public static function set ( $key , $value ) {
        $_REQUEST[$key] = $value ;
    }

    public static function setPost ( $key , $value ) {
        $_POST[$key] = $value ;
    }

    public static function setGet ( $key , $value ) {
        $_GET[$key] = $value ;
    }

    public static function isPost ( ) {
        return !empty ( $_POST );
    }

    public static function isGet ( ) {
        return !empty ( $_GET );
    }

    public static function getURI ( ) {
    	$URI = "//";
    	$URI .= $_SERVER['HTTP_HOST'] ;
    	$URI .= $_SERVER['REQUEST_URI'] ;
    	return $URI ;
    }

    /**
     * adiciona uma mensagem de feedback para ser capturada na view
     * @param string $feedback  mensagem (text|html) de feedback
     * @param string $type      success|info|warning|danger que é respectivamente verde|azul|amarelo|vermelho
     * @param string|boolean    $icon TRUE=usa icone default do $type. FALSE=não usa icone. STRING=usa um icone qualquer do twitter bootstrap
     * @param string $nameSpace namespace para dividir certas partes da aplicação que se encontra na mesma sessão
     */
	public static function addFeedback ( $feedback , $type = "info" , $icon = true , $nameSpace = "defaultNameSpace" ) {

        // $feedback = translateError ( $feedback ) ;

        $typeToIcon = array (
            "success"   => "glyphicon-ok-sign" ,
            "info"      => "info-sign",
            "warning"   => "warning-sign" ,
            "danger"    => "exclamation-sign"
        ) ;
        if ( $icon === true ) $icon = $typeToIcon[$type];

		$_SESSION['USER-FEEDBACK-MESSAGES'][$nameSpace][] = array (
			'message'	=> $feedback ,
			'type'		=> $type ,
            'icon'      => $icon
		) ;

	}

    /**
     * recupera todas as mensagens de feedback para exibir na view e em seguida, apaga da sessão para evitar que se exiba novamente
     * @param type $nameSpace   namespace para dividir certas partes da aplicação que se encontra na mesma sessão
     * @return string
     */
	public static function getFeedback ( $nameSpace = "defaultNameSpace" ) {

        if ( empty ( $_SESSION['USER-FEEDBACK-MESSAGES'][$nameSpace] ) ) return "";
        $output = "";

        foreach ( $_SESSION['USER-FEEDBACK-MESSAGES'][$nameSpace] as $key => $feedback ) {
            $icon = ( $feedback['icon'] !== false ) ? "<span class=\"glyphicon " . $feedback['icon'] . "\"></span> " : "" ;
            $output .= "<div class=\"alert alert-" . $feedback['type'] . "\">" ;
            $output .= "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>";
            $output .= "$icon" . $feedback['message'] . "</div>" ;
            unset ( $_SESSION['USER-FEEDBACK-MESSAGES'][$nameSpace][$key] ) ;
        }

        return $output ;

	}

    public static function basicFilter ( $data ) {
        if ( gettype ( $data ) === 'string' ) {
            return trim($data);
        }
        return $data ;
    }

    public static function getFiles ( ) {
        return Upload::getFiles ( );
    }

    /**
     *
     * @param type $key
     * @return boolean
     */
    public static function hasFile ( $key ) {
        return Upload::hasFile ( $key );
    }

    /**
     *
     * @param type $key
     * @return boolean
     * @throws Exception
     */
    public static function getFile ( $key ) {
    	return Upload::getFile ( $key );
    }

}