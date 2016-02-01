<?php

namespace Xlib\XListaDados;

class XRemoveButton extends XButton {

    protected $elementId ;
    protected $elementClass ;
    protected $iconClass ;
    protected $style ;
	protected $cellParams     = array ( ) ;
	protected $label          = "" ;
	protected $rowID          = "" ;
	protected $data ;
	protected $onClick        = "if (!confirm('Tem certeza que deseja excluir?')) return false ;" ;
	// protected $onClick     = "" ;
	protected $buttonType     = "submit";
	protected $attributes     = array ( );
	protected $hideIf_list    = array ( ) ;
	protected $disableIf_list = array ( ) ;
	protected $styleIf_list   = array ( ) ;
	protected $queryList      = "";

    public function __construct ( $label = "" , $class = "btn-xs btn-danger" , $iconClass = "glyphicon glyphicon-remove" , $query = "" ) {
		$this->label        = $label ;
		$this->elementClass = $class ;
		$this->iconClass    = $iconClass ;
		if ( !empty ( $query ) ) $this->queryList = is_array ( $query ) ? $query : array ( $query );		
		// if ( !empty ( $query ) && !strpos ( $query , "?" ) ) throw new Exception ( "É necessário ter um placeholder \"?\" para o id do registro" );
    }

    public static function getInstance ( $label , $class = "" , $iconClass = "" , $query = "" ) {
        $instance = new Xlib_XListaDados_XButton ( $label , $class , $iconClass , $query ) ;
        return $instance ;
    }

    public function __toString ( ) {

        $attributeSetString = '';

        $attributeSet = array (
            'type'           => $this->buttonType ,
            'onClick'        => $this->onClick,
            'class'          => "btn " . $this->elementClass ,
            'data-row-id'    => $this->rowID ,
            'style'          => $this->style
        ) ;

        foreach ( $this->cellParams as $cellParam ) {
            $attributeSet[strtolower($cellParam)] = $this->data[$cellParam] ;
        }

        if ( $this->isDisabled ( ) ) {
            $this->attributes['disabled'] = 'disabled';
        } else {
            unset ( $this->attributes['disabled'] );
        }

        foreach ( $this->attributes as $key => $value ) $attributeSet[$key] = $value ;
        foreach ( $attributeSet as $key => $value )     $attributeSetString .= " $key=\"$value\" " ;

        $output =
            "<form method=\"POST\" action=\"\" >" .
            	"<input type=\"hidden\" name=\"XLLD_Action\" value=\"remove\">" .
            	"<input type=\"hidden\" name=\"rowID\" value=\"" . $this->rowID . "\">" .
            	"<input type=\"hidden\" name=\"objectKey\" value=\"" . $this->objectKey . "\">" .
            	"<button $attributeSetString>" .
                	"<span class=\"".$this->iconClass."\" ></span> " .
                	$this->label .
	            "</button>" .
            "</form>"
        ;

        return $output;

    }

    public function process ( ) {

    	if ( !isset ( $_POST['XLLD_Action'] ) ) return false ;
    	if ( $_POST['XLLD_Action'] !== "remove" ) return false;
    	if ( $_POST['objectKey'] !== $this->objectKey ) return false ;

    	if ( empty ( $this->queryList ) ) {
	    	$queryList = array ( $this->getListaDadosRef()->getListaDb()->getRemoveQuery($_POST['rowID']) ) ;
    	} else {
	    	$queryList = $this->queryList ;
	    	
	    	foreach ( $queryList as &$query ) {
	    		$query = $this->getListaDadosRef()->getListaDb()->bind( $query , array ( $_POST['rowID'] ) );
	    	}
    	}

    	try {

	    	foreach ( $queryList as $singleQuery ) {
		    	if ( !$this->getListaDadosRef()->getListaDb()->query($singleQuery) ) throw new \Exception ( "Não foi possível excluir" );
	    	}

	    	\Xlib\Response::addFeedback ( "Excluído com sucesso!" , "success" );
    	} catch (Exception $e) {
    		\Xlib\Response::addFeedback ( $e->getMessage() , "danger" ) ;
    	}

    	header ( "Location: " . $_SERVER['REQUEST_URI']);
    	exit;

    }

    public function addHideIf ( $condition ) {
//        @TODO
//        $hideIf_list
    }

    public function isDisabled ( ) {

        $data = $this->data ; // só para conveniencia do programador ;)

//        foreach ( $this->data as $key => $val ) {
//            if ( !preg_match ( '//')
//            $$key = $val ;
//        }

        foreach ( $this->disableIf_list as $condition ) {
            if ( eval ( "return ( $condition ) ; " ) ) return true ;
        }
        return false ;
    }

    public function addDisableIf ( $condition ) {
        $this->disableIf_list[] = $condition ;
        return $this;
    }

    public function addStyleIf ( $condition , $style ) {
//        @TODO
//        $this->styleIf_list
    }

    public function setRowID ( $id ) {
        $this->rowID = $id ;
        return $this;
    }

    public function setData ( $data , $paramList = array ( ) ) {
        $this->cellParams = $paramList;
        $this->data = $data ;
        return $this;
    }

    public function onClick ( $function ) {
        $this->onClick = $function;
        return $this;
    }

    public function align ( $direction ) {
        return $this->addStyle ( "float:$direction" );
    }

    public function addStyle ( $style ) {
        $styleList = explode ( ";" , $this->style );
        $styleList[] = $style ;
        $this->style = implode ( ";" , $styleList );
        return $this;
    }

    public function setType ( $type ) {
        $this->buttonType = $type ;
        return $this;
    }

    public function setAttribute ( $key , $value = "" ) {
        $this->attributes[$key] = $value;
        return $this;
    }

}
