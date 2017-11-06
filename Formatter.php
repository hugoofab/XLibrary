<?php

namespace Xlib;

class Formatter {

	/**
	* convert dd/mm/yyyy to yyyy-mm-dd
	*/
    public function dateToDb ( $date ) {
    	$newTime = strtotime ( str_replace('/', '-', $date ) ) ;
        return date ( 'Y-m-d', $newTime );
    }

    /**
    * convert 1.000.000,10 to (float) 1000000.10
    */
    public function brMoneyToFloat ( $money ) {
    	$money = preg_replace('/[^\d\,]/', '', $money ) ;
		$money = (float) preg_replace('/,/','.',$money);
		return $money ;
    }

}
