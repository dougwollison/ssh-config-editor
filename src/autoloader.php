<?php
/**
 * SSH Edit Autoloader
 *
 * @package SSH_Edit
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

	// Default to framework directory
	$basesdir = __DIR__ . '/includes/';

	// Convert to lowercase, hyphenated form
	$name = preg_replace( '/[^a-z0-9]+/i', '-', strtolower( $fullname ) );

	// Loop through each class type and try to load it
	$types = array( 'abstract', 'class', 'interface', 'trait' );
	foreach ( $types as $type ) {
		// Create the full path
		$file = $basesdir . $type . '-' . $name . '.php';

		// Test if the file exists, load if so
		if ( file_exists( $file ) ) {
			require( $file );
			break;
		}
	}
}

spl_autoload_register( __NAMESPACE__ . '\autoload' );
