<?php
/**
 * Para usar, crie uma classe que extenda esta (Xlib\DtoAbstract),
 * crie os atributos e metodos get e set, principalmente set que serão necessários para carregar
 * os atributos.
 *
 * o padrão para criação dos metodos é getVariablename onde o nome do atributo deve estar em minúsculo
 * exceto o primeiro caractere
 */

namespace Xlib;

/**
 * Description of Abstract
 *
 * @author hugo.ferreira
 */
abstract class DtoAbstract {

	/**
	 * vamos fazer essa classe salvar os dados futuramente?
	 */
	protected $tableName = null ;
	protected $idName    = null ;

    /**
	 * caso tenha passado um array de chave => valor com chaves identicas aos atributos declarados no objeto filho
	 * vamos setar usando o metodo set se ele existir
	 * os nomes dos atributos devem ser todos em minusculo
	 *
	*/
	final public function __construct ( $arrayData = null ) {

		$this->reset();

		if ( !empty ( $arrayData ) ) $this->loadFromArray( $arrayData );

		$this->init ( );

	}

	// ainda precisa consertar isso
	// public function getErrorMessages ( ) {
		
	// 	$output = array ( );
	// 	$paramList = get_object_vars($this);

	// 	if ( !empty ( $paramList ) && is_array ( $paramList ) ) {
	// 		foreach ( $paramList as $key => $val ) {
	// 			$method = "validate" . ucwords ( strToLower ( $key ) ) ;
	// 			if ( method_exists ( $this , $method ) ) {
	// 				try {
	// 					$this->{$method}( ) ;
	// 				} catch (Exception $e) {
	// 					$output[$key] = $e->getMessage ( ) ;
	// 				}
	// 			}
	// 		}
	// 	}

	// 	return $output ;

	// }

	public function loadFromArray ( $arrayData = array ( ) ) {

		if ( !empty ( $arrayData ) && is_array ( $arrayData ) ) {
			foreach ( $arrayData as $key => $val ) {
				$method = "set" . ucwords ( strToLower ( $key ) ) ;
				if ( method_exists ( $this , $method ) ) {
					$this->{$method}( $val ) ;
				}
			}
		}

	}

    /**
     * precisa ser sobrescrito na classe filha
     */
    abstract public function isValid ( ) ;

    /**
     * inicializa as classes concretas
     * @return [type] [description]
     */
    abstract public function init ( );

    /**
     * reconfigura todos os parâmetros
     * @return [type] [description]
     */
    abstract public function reset ( );

	/**
	 * retorna true se todos os atributos deste objeto estiverem vazios
	 * retorna false se ao menos um atributo contiver algum valor
	*/
	public function isEmpty ( ) {
		$atributos = get_object_vars ( $this ) ;
		foreach ( $atributos as $key => $val ) {
			if ( !empty ( $val ) ) {
				return false ;
			}
		}
		return true ;
	}

	/**
	 * retorna true se todos os atributos deste objeto estiverem preenchidos
	 * retorna false se algum atributo não tiver valor algum atribuido
	*/
	public function isFull ( ) {
		$atributos = get_object_vars ( $this ) ;
		foreach ( $atributos as $key => $val ) {
			if ( empty ( $val ) ) {
				return false ;
			}
		}
		return true ;
	}

	/**
	 * retorna o proprio objeto convertido em array
	*/
	public function toArray ( ) {
		return (array) $this ;
	}

}