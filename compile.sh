#!/usr/bin/env php -d phar.readonly=0
<?php
define( 'SRC_DIR', __DIR__ . '/src' );
define( 'BUILD_DIR',  __DIR__ . '/build' );
define( 'BUILD_FILE', BUILD_DIR . '/sshedit.phar' );

$phar = new Phar( BUILD_FILE, FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_FILENAME, basename( BUILD_FILE ) );

$phar->startBuffering();

function add_files( $directory, $phar ) {
	$files = scandir( SRC_DIR . $directory );

	foreach ( $files as $file ) {
		if ( strpos( $file, '.' ) === 0 ) {
			continue;
		}

		$local = $directory . '/' . $file;
		$path = SRC_DIR . $local;

		if ( is_file( $path ) ) {
			$phar->addFile( $path, $local );
		} elseif ( is_dir( $path ) ) {
			add_files( $local, $phar );
		}
	}
}

add_files( '', $phar );

$phar->setStub( <<<STUB
#!/usr/bin/env php
<?php
Phar::mapPhar();
include 'phar://sshedit.phar/index.php';
__HALT_COMPILER();
?>
STUB
);

$phar->stopBuffering();

chmod( BUILD_FILE, 0775 );
