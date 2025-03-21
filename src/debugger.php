<?php

namespace Eugenefvdm;

if (!function_exists('Eugenefvdm\\debugger')) {
function debugger( $message, $variable = '' ) {
	$serverAddr     = $_SERVER['SERVER_ADDR'];
	$dateTimeFormat = date( 'Y-m-d H:i:s' );
	$prefix         = "[$dateTimeFormat] $serverAddr.PREFIX: ";
	if ( is_array( $variable ) or is_object( $variable ) ) {
	    $variable = print_r( $variable, 1 );
	} else if ( gettype( $variable ) == 'boolean' ) {
	    $variable = "(Boolean: $variable)";
	}
	file_put_contents( './log_' . date( "dmY" ) . '.log', $prefix . $message . $variable . "\n", FILE_APPEND );
}
}