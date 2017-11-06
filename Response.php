<?php

namespace Xlib;

class Response {

	protected $status     = 'ok';
	protected $message    = '' ;
	protected $data       = array ( );
	protected $outputType = 'json' ;
	protected $command    = '';

    public function setType ( $type ) {
        if ( !in_array ( $type , array ( 'json' ) ) ) throw new Exception ( "Tipo desconhecido" ) ;
        $this->outputType = $type ;
    }

    public function setMessage ( $message ) {
    	$this->message = $message ;
    	return $this ;
    }

    public function setError ( $error ) {

    	if ( $error instanceof Exception ) {
			$error = $error->getMessage ( );
		} else if ( gettype ( $error ) === 'object' && method_exists ( $error , 'getMessage' ) ) {
            $error = $error->getMessage ( );
        }

        $this->status = 'error' ;
        $this->message = $error ;
        return $this ;

    }

	/**
	 * @return string
	 */
	public function getCommand()
	{
		return $this->command;
	}

	/**
	 * @param string $command
	 */
	public function setCommand($command)
	{
		$this->command = $command;
	}

	/**
	 * @param string $statusCode
	 */
	public function setStatus($status)
	{
		$this->status = $status;
	}

	/**
	 * seta dados para serem enviados.
	 * @param $data
	 * @param null $value
	 * @return $this
	 */
    public function setData ( $data , $value = null ) {
		if ( $value == null ) {
			$this->data = $data ;
		} else {
			$this->data[$data] = $value ;
		}
        return $this ;
    }

    public function __toString (  ) {
        
	    // para evitar problemas de encoding, pode-se fazer um utf8_encode ma mensagem ou dados da saida
	   header("Content-Type:application/json; charset=utf-8");

        if ( isset($_SERVER['HTTP_ACCEPT_ENCODING']) && substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') ) {
            ini_set('zlib.output_compression', 'on');
            header('Content-Encoding:gzip');
        }
	    
        $response = array (
            'status'    => $this->status ,
            'message'   => $this->message ,
            'data'      => $this->data
        ) ;

        if ( !empty ( $this->command ) ) {
        	$response['cmd'] = $this->command ;
		}

        switch ( $this->outputType ) {
            case "json" :  return Json::encode ( $response ) ;
        }

    }

    public function flush ( ) {
    	print $this ;
    	exit ;
    }

    /**
     * adiciona uma mensagem de feedback para ser capturada na view
     * @param string $feedback  mensagem (text|html) de feedback
     * @param string $type      success|info|warning|danger que é respectivamente verde|azul|amarelo|vermelho
     * @param string|boolean    $icon TRUE=usa icone default do $type. FALSE=não usa icone. STRING=usa um icone qualquer do twitter bootstrap
     * @param string $nameSpace namespace para dividir certas partes da aplicação que se encontra na mesma sessão
     */
	public static function addFeedback ( $feedback , $type = "info" , $icon = true , $nameSpace = "defaultNameSpace" ) {

		if ( $feedback instanceof Exception ) {
			$feedback = $feedback->getMessage();
			$type     = "danger";
		}

        // $feedback = translateError ( $feedback ) ;

        $typeToIcon = array (
			"sucess"  => "glyphicon-ok-sign" ,
			"success" => "glyphicon-ok-sign" ,
			"info"    => "info-sign",
			"warning" => "warning-sign" ,
			"danger"  => "exclamation-sign"
        ) ;
        if ( $icon === true ) $icon = $typeToIcon[$type];

		$_SESSION['USER-FEEDBACK-MESSAGES'][$nameSpace][] = array (
			'message'	=> $feedback ,
			'type'		=> $type ,
            'icon'      => $icon
		) ;

	}

	public static function forceUserDownloadByFile ( $filename ) {
		header('Content-Type: application/octet-stream');
		header("Content-Transfer-Encoding: Binary");
		header("Content-disposition: attachment; filename=\"" . basename($filename) . "\"");
		echo readfile($file_url);
	}

	public static function forceUserDownloadByString ( $fileContent , $filename ) {
		header('Content-Type: application/octet-stream');
		header("Content-Transfer-Encoding: Binary");
		header("Content-disposition: attachment; filename=\"" . $filename . "\"");
		echo $fileContent;
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

}
