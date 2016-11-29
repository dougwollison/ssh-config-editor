<?php
/**
 * SSH Edit Autoloader
 *
 * @package SSH_Edit
 * @subpackage Root
 *
 * @since 1.0.0
 */

namespace SSHEdit;

/**
 * The class autoloader.
 *
 * @since 1.0.0
 *
 * @param string $fullname The full name of the class to load.
 */
function autoload( $fullname ) {
	// Trim preceding backslash
	$fullname = ltrim( $fullname, '\\' );

	// Abort if not within the root namespace
	if ( strpos( $fullname, __NAMESPACE__ ) !== 0 ) {
		return;
	}

	// Remove the root namespace
	$name = substr( $fullname, strlen( __NAMESPACE__ ) + 1 );

	// Default to framework directory
	$basesdir = __DIR__ . '/includes/';

	// Convert to lowercase, hyphenated form
	$name = preg_replace( '/[^a-z0-9\\\]+/i', '-', strtolower( $name ) );

	// Loop through each class type and try to load it
	$types = array( 'abstract', 'class', 'interface', 'trait' );
	foreach ( $types as $type ) {
		// Prefix the last part of the name with the type
		$file = preg_replace( '/([a-z0-9\-]+)$/i', "{$type}-$1", $name );

		// Create the full path
		$file = $basesdir . str_replace( '\\', DIRECTORY_SEPARATOR, $file ) . '.php';

		// Test if the file exists, load if so
		if ( file_exists( $file ) ) {
			require( $file );
			break;
		}
	}
}

spl_autoload_register( __NAMESPACE__ . '\autoload' );
