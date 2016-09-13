<?php
/**
 * Description of ModelAbstract
 * Crie uma model (objeto DAO) extendendo esta classe para manter seu código limpo, livre de queries e
 * operações de banco de dados.
 *
 * compativel com zend framework 2
 *
 * @author hugo.ferreira
 */

namespace Xlib;
// use Zend\Db\Adapter\Adapter;

class ModelAbstract {

    /**
     * o objeto $db (MDB2) usado como padrão em todos os projetos da empresa
     * @var object, precisa ser passado no construtor
     */
    private static $db = null ;

    private static $queryHistory = array() ;
    private static $totalExecutionTime = 0 ;

    private static $hasError = false ;
    private static $instances = 0 ;

    /**
     * É necessário passar o $db usado como padrão nos projetos de intranet da RPC,
     * geralmente criado pelo db/conexao.inc@init.php
     * @param MDB2 $db objeto MDB2 do framework Pear
     */
    public function __construct ( $db = null ) {
        ModelAbstract::$instances++;
        if ( $db !== null ) ModelAbstract::setDB ( $db );
        if ( method_exists ( $this , "init" ) ) {
            $this->init();
        }
    }

    public static function getInstance ( $db = null ) {
	return new ModelAbstract($db);
    }

    public static function setDB ( $db ) {
        ModelAbstract::$db = $db;
    }

    public static function getDB ( ) {
        return ModelAbstract::$db;
    }


    private function _fetch ( $method , $query , $db = false ) {

        try {

			$start_time = microtime ( TRUE ) ;
			$output     = array ( );
			if ( $db === false ) $db = ModelAbstract::$db;

            switch ($method) {
            	case 'One':
					$statement = ModelAbstract::$db->query($query);
					$res       = $statement->execute();
					$output    = array ( );
					$output    = $res->current();
					if ( $output !== false ) $output = array_shift ( $output );
        		break;
            	case 'Row':
					$statement = ModelAbstract::$db->query($query);
					$res       = $statement->execute();
					$output    = array ( );
					$output    = $res->current();
        		break;
            	case 'All':
					$statement = ModelAbstract::$db->query($query);
					$res       = $statement->execute();
					$res->buffer();
					$output    = array ( );
					$count     = $res->count() ;
					for ( $i = 0 ; $i < $count ; $i++ ) {
						$out      = $res->current();
						$output[] = $out;
						$res->next();
					}
        		break;
            	default:
            		die("method " . $method . " not implemented in ModelAbstract");
            }

            ModelAbstract::logQuery ( $query , $start_time , true , '0' , '' , get_class($this) , $db );

        } catch ( Exception $err ) {

            ModelAbstract::logQuery ( $query , $start_time , false , 0 , $res->userinfo , get_class ( $this ) , $db );

            throw new Exception ( $err->getMessage () ) ;

        }

        return $output ;

    }

    public function fetchOne ( $query , array $bindList = array ( ) ) {
        if ( !empty ( $bindList ) ) $query = $this->bind ( $query , $bindList );
        return ModelAbstract::_fetch ( 'One' , $query );
    }

    public function fetchRow ( $query , array $bindList = array ( ) , $db = false ) {
        if ( !empty ( $bindList ) ) $query = $this->bind ( $query , $bindList );
        return ModelAbstract::_fetch ( 'Row' , $query , $db );
    }

    public function fetchAll ( $query , array $bindList = array ( ) , $db = false ){
        if ( !empty ( $bindList ) ) $query = $this->bind ( $query , $bindList );
        return ModelAbstract::_fetch ( 'All' , $query , $db );
    }

    public function fetchLimit ( $query , $start , $maxRows , array $bindList = array ( ) ) {
		
		// a linha abaixo é uma solução paleativa
		if ( $start < 0 ) $start = 0 ;

        if ( !empty ( $bindList ) ) $query = $this->bind ( $query , $bindList );

        try {
        	
            $start_time = microtime ( TRUE ) ;
			$statement = ModelAbstract::$db->query ( $query . " LIMIT $start , $maxRows" );
			$res       = $statement->execute();
			$res->buffer();
			$output    = array ( );
			$count     = $res->count() ;
			for ( $i = 0 ; $i < $count ; $i++ ) {
				$out      = $res->current();
				$output[] = $out;
				$res->next();
			}

			return $output ;

            ModelAbstract::logQuery ( $query , $start_time , true , '0' , '' , get_class($this) );

        } catch ( Exception $err ) {

            ModelAbstract::logQuery ( $query , $start_time , false , '0' , $err->getMessage() , get_class($this) );

            throw new Exception ( "Erro na consulta. Favor informar o desenvolvedor" ) ;
            //echo $err->getTraceAsString ( ) ;
        }


    }

    /**
     * faz bind seguro em uma query parametrizada com ?
     * @param string $query query parametrizada (ou não) com '?' nos parâmetros
     * @param array $bindList array contendo os valores dos parâmetros. pode ser omitido, nesse caso a query será
     * executada como está. valores NULL nos parâmetros serão respeitados e gravados no banco como NULL
     * @return string query com os parâmetros adicionados de forma segura
    */
    public function bind ( $query , array $bindList ) {
    	$pos = 0 ;
        while ( $pos = strpos ( $query , "?" , $pos ) )  {
            $value = array_shift ( $bindList ) ;
            if ( $value !== null ) {
                $value = "'" . addslashes ( $value ) . "'" ;
            } else {
                $value = " null " ;
            }
            $query = substr_replace ( $query , $value , $pos , 1 ) ;
            $pos += strlen($value);
        }
        return $query ;
    }

    public function query ( $query , array $bindList = array ( ) , $db = false ) {
    	if ( !empty ( $bindList ) ) $query = $this->bind ( $query , $bindList ) ;
    	return $this->_query ( $query , $db ) ;
    }

    /**
     * Executa uma query independente da classe connector que esteja sendo usada
     * @param type $query
     * @return type
     * @throws Exception
     */
    private function _query ( $query , $db ) {

    	if ( $db === false ) $db = ModelAbstract::$db;

        try {
            $start_time = microtime ( TRUE ) ;

			$statement = ModelAbstract::$db->query($query);
			$res       = $statement->execute();

            ModelAbstract::logQuery ( $query , $start_time , true , '0' , '' , get_class($this) , $db );
            return $res->getResource() ;

        } catch ( Exception $err ) {
            $errorMessage = $err->getMessage();
            if ( DEBUG ) $errorMessage .= "<br>" . $res->userinfo;
            ModelAbstract::logQuery ( $query , $start_time , false , 0 , $errorMessage , get_class ( $this ) , $db );
            throw new Exception ( $err->getMessage () ) ;
        }

    }

    public static function getQueryHistory ( ) {
        return array (
            'totalTime' => ModelAbstract::$totalExecutionTime ,
            'queryList' => ModelAbstract::$queryHistory
        ) ;
    }

    public static function dumpQueries ( ) {

        $queries = ModelAbstract::getQueryHistory();

        $db = ModelAbstract::getDB();

        $output = '<div style="background:#000;color:#0F0;font-size:12px;font-family:courier new;width:95%;padding:10px;" >' .
            '<span style="color:#0F0;font-size:1.3em;">Connection default: ' .
            // strtoupper($db->dsn['username']."@".$db->dsn['hostspec']).
            '</span>' .
        '</div>' ;

        $count = 1;
        foreach ( $queries['queryList'] as $query ) {

            $output .= '<div style="background:#000;color:#0F0;font-size:12px;font-family:courier new;width:95%;padding:10px;" >' .
                '<div style="width:100%;height:20px;overflow:hidden;white-space:nowrap">Query '.$count++.' ['.$query['class'].']: ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------</div>' .
                ModelAbstract::queryBeautifier ( $query['query'] ) .
//                ModelAbstract::queryBeautifier ( str_replace ( "\n" , "<br>" , $query['query'] ) ) .
                '<span style="color:#FF0;">' .
                    '<br><br>Time: ' . $query['time'] .
                    '<br>Status: ' . $query['status'] .
                '</span>' .
//            '<br><div style="width:100%;height:10px;overflow:hidden;">---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------</div>' .
                $query['backtrace'] .
            '</div>' ;

        }
        $output .= '<div style="background:#000;color:#FF0;font-size:12px;font-family:courier new;width:95%;padding:10px;" >' .
            "<br>Total Time: " . $queries['totalTime'] .
        '</div>' ;

//        $output .= "<pre>" . print_r ( ModelAbstract::$db->dsn , true ) . "</pre>" ;

        return $output ;
    }

    public function limitQuery ( $query , $start , $linhas ) {

die("precisa implementar");

    //     try {

    //         $start_time = microtime ( TRUE ) ;

    //         if ( ModelAbstract::$dbClass === 'DB_oci8' ) {
    //             $result = ModelAbstract::$db->limitQuery ( $query , $start , $linhas ) ;
    // //        } else if ( ModelAbstract::$dbClass === 'Zend_Db_Adapter_Mysqli' ) {
    //         } else {
    //             throw new Exception ( "Invalid DB Connector" ) ;
    //         }
    //         ModelAbstract::logQuery ( $query , $start_time , true , $linhas , '' , get_class ( $this ) );

    //         return $result ;

    //     } catch ( Exception $err ) {
    //         ModelAbstract::logQuery ( $query , $start , $linhas , '0' , '' , get_class($this) );
    //         throw new Exception ( $err->getMessage () ) ;
    //     }

    }

    /**
     *
     */
    private static function logQuery ( $query , $startTime = false , $executionStatus = true , $linhas = '0' , $errorMessage = '' , $class = '' , $db = false ) {

		$backTrace = debug_backtrace ();
		array_shift($backTrace);
		$backTrace = array_reverse( $backTrace );

		$backtraceOutput = '<br>';
        foreach( $backTrace as $key => $bt ) {
        	if ( $bt['file'] === __file__ ) continue ;
        	// foreach ( $bt['args'] as &$arg ) if ( gettype ( $arg ) === 'object' ) $arg = "Object of " . get_class($arg) ;
        	foreach ( $bt['args'] as &$arg ) {
    			$rand = md5(mt_rand());
        		if ( gettype ( $arg ) === 'object' ) {
        			$arg = "<a style=\"cursor:pointer\" onclick=\"var el=document.getElementById('$rand');if (el.style.display==='none'){el.style.display='block'}else{el.style.display='none'}\"><span style=\"color:#088\">Object of:</span> " . get_class($arg) . "</a>" .
        			"<div id=\"$rand\" style=\"display:none;border:1px solid #000;width:600px;height:150px;overflow:scroll;position:absolute;background:#000;z-index:9999999;\"><pre>" . print_r ( $arg , true ) . "</pre></div>" ;
        		} else if ( gettype ( $arg ) === 'array' ) {
        			$arg = "<a style=\"cursor:pointer\" onclick=\"var el=document.getElementById('$rand');if (el.style.display==='none'){el.style.display='block'}else{el.style.display='none'}\"><span style=\"color:#088\">Array:</span>  " . count ( $arg ) . " elements</a>" .
        			"<div id=\"$rand\" style=\"display:none;border:1px solid #000;width:600px;height:150px;overflow:scroll;position:absolute;background:#000;z-index:9999999;\"><pre>" . print_r ( $arg , true ) . "</pre></div>" ;
        		} else {
        			$arg = "<span style=\"color:#088\">" . gettype ( $arg ) . ":</span> " . $arg ;
        		}
        	}
			$implode         = @implode ( "</span>] , [<span style=\"color:#0FF;\">" , $bt['args'] ) ;
			$function        = $bt['function'] . " ( [<span style=\"color:#0FF;\">" . $implode . "</span>] ) " ;
			// if ( is_dir ( ROOT_DIR ) ) $bt['file'] = str_replace( ROOT_DIR , '' , $bt['file'] );
			$backtraceOutput .= "\n<span style=\"font-family:courier new;font-size:10px;\"><span style=\"margin-top:0px;color:#888;padding-left:4px;\">" . $bt['file'] . ":" . $bt['line'] . "&nbsp;</span>-&gt;" . $function . "</span><br>";
        }

		if ( $db === false ) $db = ModelAbstract::$db;

        if ( $class ) {
            // $class = "<span style=\"color:#FF0\">" . strtoupper ( $db->dsn['username']."@".$db->dsn['hostspec'] ) . "</span> - class " . $class . "()";
        } else {
            // $class = "<span style=\"color:#FF0\">" . strtoupper ( $db->dsn['username']."@".$db->dsn['hostspec'] ) . "</span> - class ModelAbstract::()";
        }

        if ( !$executionStatus ) {
            ModelAbstract::$hasError = true ;
        }

        if ( $executionStatus ) {
            $executionStatus = 'OK';
        } else if ( $errorMessage == '' ) {
            $executionStatus = '<span style="background:#A00;color:#FFF;font-weight:bold;">&nbsp;ERROR&nbsp;</span>';
        } else {
            $formatedMessage = '';
            if ( preg_match ( '/.*Error message:\s*([^\]]+)/' , $errorMessage , $resultArr ) ) $formatedMessage .= "<br/>&nbsp;" . $resultArr[1] . "&nbsp;";
            if ( preg_match ( '/.*Native message:\s*([^\]]+)/' , $errorMessage , $resultArr ) ) $formatedMessage .= "<br/>&nbsp;Native Message: " . $resultArr[1] ;
            if ( $formatedMessage !== '' ) $errorMessage = '<span style="background:#A00;color:#FFF;font-weight:bold;">&nbsp;ERROR&nbsp;</span>' . $formatedMessage ;
            $executionStatus = '<span style="background:#A00;color:#FFF;font-weight:bold;">' . $errorMessage . '&nbsp;</span>' ;
        }

        if ( $startTime !== false ) {
            ModelAbstract::$totalExecutionTime += $endTime = microtime ( TRUE ) - $startTime ;
        } else {
            $endTime = $startTime = '-' ;
        }

        ModelAbstract::$queryHistory[] = array (
			'query'     => $query ,
			'time'      => $endTime ,
			'status'    => $executionStatus ,
			'class'     => $class ,
			'backtrace' => $backtraceOutput
        );

    }

    public function startTransaction ( ) {

    	die ( "precisa ser implementado");

        // try {
        //     $start_time = microtime ( TRUE )  ;

        //     if ( ModelAbstract::$dbClass === 'Zend_Db_Adapter_Mysqli' ) {
        //         return ModelAbstract::$db->beginTransaction();
        //     } else {
        //         throw new Exception ( "Invalid Db Connector" ) ;
        //     }

        //     ModelAbstract::logQuery ( 'START TRANSACTION' , $start_time , true , '0' , '' , get_class($this) );

        // } catch ( Exception $err ) {
        //     throw new Exception ( $err->getMessage () ) ;
        //     //echo $err->getTraceAsString ( ) ;
        // }

	}

	public function commit ( ) {
die ( "precisa ser implementado");
        // try {
        //     $start_time = microtime ( TRUE )  ;

        //     if ( ModelAbstract::$dbClass === 'Zend_Db_Adapter_Mysqli' ) {
        //         return ModelAbstract::$db->commit();
        //     } else {
        //         throw new Exception ( "Invalid Db Connector" ) ;
        //     }

        //     ModelAbstract::logQuery ( 'COMMIT' , $start_time , true , '0' , '' , get_class($this) );

        // } catch ( Exception $err ) {
        //     throw new Exception ( $err->getMessage () ) ;
        //     //echo $err->getTraceAsString ( ) ;
        // }
	}

	public function rollback ( ) {
		die ( "precisa ser implementado");
        // try {
        //     $start_time = microtime ( TRUE )  ;

        //     if ( ModelAbstract::$dbClass === 'Zend_Db_Adapter_Mysqli' ) {
        //         return ModelAbstract::$db->rollback();
        //     } else {
        //         throw new Exception ( "Invalid Db Connector" ) ;
        //     }

        //     ModelAbstract::logQuery ( 'ROLLBACK' , $start_time , true , '0' , '' , get_class($this) );

        // } catch ( Exception $err ) {
        //     throw new Exception ( $err->getMessage () ) ;
        //     //echo $err->getTraceAsString ( ) ;
        // }
	}

    /**
     *
     * @param array $dsn    um array completo com as chaves phptype , username , password e hostspec
     * phptype é opcional. nesse caso será setado como 'oci8'
     * @param string $connector 'Zend_Db_Adapter_Mysqli' or 'DB_oci8'
     * @throws Exception
     * @return $db armazena em uma variável estática e em seguida retorna
     */
    public static function connect ( $dsn ) {
die ( "precisa ser implementado");


        // try {
        //     $start_time = microtime ( TRUE ) ;

        //     if ( !isset ( $dsn['phptype'] ) ) $dsn['phptype'] = 'oci8';

        //     if ( $connector === 'Zend_Db_Adapter_Mysqli' ) {

        //         $db = MDB2::singleton ( $dsn ) ;

        //         // if ( PEAR::isError ( $db ) ) {
        //         //     if ( DEBUG ) pr ( $db );
        //         //     throw new Exception ( "Não foi possível conectar" ) ;
        //         // }

        //         $db->setOption ( 'persistent' , true ) ;
        //         $db->setOption ( 'field_case' , CASE_UPPER ) ;
        //         $db->setFetchMode ( MDB2_FETCHMODE_ASSOC ) ;
        //         $db->loadModule ( 'Extended' ) ;

        //     }

        //     ModelAbstract::setDB ( $db ) ;

        //     ModelAbstract::logQuery ( 'CONNECT' , $start_time , true , '0' , '' , get_class($this) );

        // } catch ( Exception $err ) {
        //     ModelAbstract::logQuery ( 'CONNECT' , $start_time , true , '0' , '' , get_class($this) );
        //     throw new Exception ( $err->getMessage () ) ;
        // }

        // return ModelAbstract::$db ;

    }

    // /**
    //  *
    //  * @param type $schemaAtDB
    //  * @return array
    //  * @throws Exception
    //  */
    // public static function getDsn ( $schemaAtDB ) {

    //     $schemaAtDB = strtoupper($schemaAtDB);

    //     $dsnList = array (
    //         'SCHEMA@SERVER' => array(
    //             'phptype'  => '',
    //             'username' => '',
    //             'password' => '',
    //             'hostspec' => ''
    //         )
    //     ) ;

    //     if ( array_key_exists ( $schemaAtDB , $dsnList ) ) {
    //         return $dsnList[$schemaAtDB];
    //     } else {
    //         throw new Exception ( "Combinação de SCHEMA e DB desconhecida" ) ;
    //     }

    // }

    public function __destruct ( ) {
        ModelAbstract::$instances--;
        if ( ModelAbstract::$instances > 0 ) return false ;
        if ( empty ( ModelAbstract::$queryHistory ) ) return false ;

        if ( ( ModelAbstract::$hasError && DEBUG === TRUE ) || SHOW_SQL_QUERIES === true ) {
            echo ModelAbstract::dumpQueries() . "<br><br>" ;
            ModelAbstract::$queryHistory = null ;
        }

    }

    public static function queryBeautifier ( $query ) {
        $query = preg_replace ( '/("[^"]*")/i' , "<strong style=\"color:#F80;\">$1</strong>" , $query );
        $query = preg_replace ( '/(\'[^\']*\')/i' , "<strong style=\"color:#F80;\">$1</strong>" , $query );
        $query = preg_replace ( '/(\(|\))/i' , "<strong style=\"font-weight:bold;color:#0FF;\"> $1 </strong>" , $query );
        $query = preg_replace ( '/\b(select|from|inner join|join|left join|right join|where|between|decode|is|null|to_char|to_date|sum|nvl|count|group by|order by|and|or|on|as)\b/i' , "<strong style=\"color:#FF0;text-transform:uppercase;\">$1</strong>" , $query );
        $query = preg_replace ( '/\b(union)\b/i' , "<strong style=\"color:#F00;text-transform:uppercase;\">$1</strong>" , $query );
//        $query = preg_replace ( '/\b(select)\b/i' , "$1\n" , $query );
//        $query = preg_replace ( '/\b(and|inner join|left join|join|right join)\b/i' , "\n$1" , $query );
//        $query = preg_replace ( '/\b(where|group by|order by)\b/i' , "\n\n$1" , $query );

        return $query ;
    }

}
