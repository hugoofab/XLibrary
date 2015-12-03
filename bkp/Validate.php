<?php

namespace Xlib;

class Validate {
	
    /**
     * Valida uma data no formato DD/MM/YYYY
     * @param string $date string no formato DD/MM/YYYY
     */
    public static function date ( $date ) {
        
        if ( !preg_match ( '/^\d\d\/\d\d\/\d\d\d\d$/' , $date ) ) return false ;
        $dateArr = explode ( "/" , $date );
        $totalDiasMes = date ( "t" , mktime ( 0 , 0 , 0 , $dateArr[1] , 1 , $dateArr[2] ) );
        if ( $dateArr[1] > 12 ) return false ;
        if ( $dateArr[0] > $totalDiasMes ) return false ;
        
        return true ;
        
    }
    
    public static function cpf ( $number ) {
        die ( "Precisa implementar");
    }

    public static function cnpj ( $number ) {
        die ( "Precisa implementar");
    }

    public static function email ( $mail ) {
    	return filter_var ( $mail , FILTER_VALIDATE_EMAIL ) ;
    }

    public static function ip ( $ip ) {
    	filter_var($ip, FILTER_VALIDATE_IP);
    }

    public static function url ( $url ) {
    	return preg_match ( "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i" , $url ) ;
	}

}