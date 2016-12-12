<?php
namespace App\View\Helper;

use Cake\View\Helper;

/**
 * Print helper for render numbers
 */
class PrintHelper extends Helper
{

    public function decimal($number,$decimal = 2){
        return number_format($number,$decimal,',','.');
    }

    public function round($number,$decimal = 2){
    	return number_format( round($number,$decimal) ,$decimal,',','.');	
    }

    public function dot($number){
    	return number_format($number,0,',','.');	
    }

}
