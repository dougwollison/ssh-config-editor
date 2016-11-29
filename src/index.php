<?php
require( __DIR__ . '/autoloader.php' );

$file = getcwd() . '/.ssh/config';
if ( basename( dirname( getcwd() ) ) == '.ssh' ) {
	$file = getcwd() . '/config';
}

new SSHEdit\CLI( $file );
