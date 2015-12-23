<?php

namespace Xlib;

class Upload {

	public $name      = null ;
	public $hash_name = null ;
	public $type      = null ;
	public $tmp_name  = null ;
	public $error     = null ;
	public $size      = null ;

	public function __construct ( $uploadFile ) {
		$this->name      = $uploadFile['name'] ;
		$this->type      = $uploadFile['type'] ;
		$this->tmp_name  = $uploadFile['tmp_name'] ;
		$this->error     = $uploadFile['error'] ;
		$this->size      = $uploadFile['size'] ;
		$this->hash_name = Upload::hashName ( $this->name );
	}

	/**
	 * move file to another directory with a given path/filename
	 * @param  [type] $destination full path of file, including filename and extension
	 * @return [type]              [description]
	 */
	public function saveAs ( $destination ) {
		return move_uploaded_file ( $this->tmp_name , $destination );
	}

	/**
	 * just save file to a given directory using hashed filename as default
	 * @param  [type] $folderName folder name
	 * @return [type]             [description]
	 */
	public function moveTo ( $folderName ) {
		return move_uploaded_file ( $this->tmp_name , realpath($folderName) . DIRECTORY_SEPARATOR . $this->hash_name ) ;
	}

    /**
     *
     * @param type $key
     * @return boolean
     * @throws Exception
     */
    public static function getFile ( $key ) {

        if ( empty ( $_FILES[$key] ) ) return false ;

        switch ( $_FILES[$key]['error'] ) {
            case UPLOAD_ERR_OK          : break ; // 0
            case UPLOAD_ERR_INI_SIZE    : throw new Exception ( "Tamanho de arquivo ultrapassa o limite de " . ini_get( 'upload_max_filesize' ) ) ; // 1
            case UPLOAD_ERR_FORM_SIZE   : throw new Exception ( "Tamanho de arquivo ultrapassa o limite permitido" ) ; // 2
            case UPLOAD_ERR_PARTIAL     : throw new Exception ( "O upload foi feito parcialmente" ) ; // 3
            case UPLOAD_ERR_NO_FILE     : throw new Exception ( "Não foi feito o upload do arquivo, favor tentar novamente" ) ; //4
            case UPLOAD_ERR_NO_TMP_DIR  : throw new Exception ( "O servidor não possui um diretório temporário" ) ; //5
            case UPLOAD_ERR_CANT_WRITE  : throw new Exception ( "Erro ao escrever no disco" ) ; //6
            case UPLOAD_ERR_EXTENSION   : throw new Exception ( "Uma extensão do PHP parou o upload do arquivo" ) ; //7
            default                     : throw new Exception ( "Erro de upload não identificado" ) ; //8
        }

        return $_FILES[$key] ;

    }

    public static function getImageFile ( $key , $allowedMimeTypes = array ( ) ) {
    	
    	if ( !$file = Request::getFile ( $key ) ) return false ;

    	if ( empty ( $allowedMimeTypes ) ) $allowedMimeTypes = MimeType::getDefaultInternetImageTypeList ( );

		if ( !in_array ( $file['type'] , $allowedMimeTypes ) ) throw new Exception\NotAllowedFileType();
		
		// IMPLEMENTAR VERIFICAÇÃO DA EXTENSÃO DA IMAGEM
			// $extList = array_keys ( $allowedMimeTypes );
			// $fileExt = EXTENSAO EXTRAIDA DE => $file['name'] ;
			// if ( !in_array ( $fileExt , $extList ) ) throw new Exception\NotAllowedFileType();

		return new Upload ( $file );

		// return $file ;
				
    }

    public static function getFiles ( ) {
        if ( empty ( $_FILES ) ) return false ;
        return $_FILES ;
    }

    /**
     *
     * @param type $key
     * @return boolean
     */
    public static function hasFile ( $key ) {
        if ( empty ( $_FILES[$key] ) ) return false ;
        return true ;
    }

    public static function hashName ( $name ) {
    	$ext = preg_replace ( '/(.*)(\.\w+)$/' , "$2" , $name ) ;
    	$hash = md5 ( $name . mt_rand ( ) . time ( ) ) ;
    	return $hash . $ext ;
    }

}