<?php
/**
 * The CLI Shell.
 *
 * @package SSH_Edit
 *
 * @since 1.0.0
 */
namespace SSHEdit;

/**
 * The CLI shell.
 *
 * The primary interface for SSH Edit.
 *
 * @api
 *
 * @since 1.0.0
 */
class CLI extends Shell {
	/**
	 * The "host" to display before the command prompt.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const NAME = 'sshedit';

	/**
	 * The "path" to display alongside the "host".
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $path = array();

	/**
	 * The ID of the current Section being edited.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $section;

	/**
	 * The ID of the current Alias being edited.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $alias;

	/**
	 * Initailize the CLI.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file The file to load.
	 */
	public function __construct( $file = null ) {
		echo "Welcome to SSH Edit. Type \"help\" for options, or \"quit\" to exit.\n";

		while ( is_null( $file ) || ! file_exists( $file ) ) {
			$file = $this->prompt( "Please provide the path to an SSH config file to create/edit:" );

			if ( $file && ! file_exists( $file ) )  {
				echo "File not found.\n";
			}
		}

		$this->config = new Config( $file );

		parent::__construct();
	}

	/**
	 * Update the path before each loop begins.
	 *
	 * @since 1.0.0
	 */
	protected function before_loop() {
		$this->path = "{$this->section}/{$this->alias}";
	}

	/**
	 * Remove the " to " keyword before parsing the command.
	 *
	 * @since 1.0.0
	 *
	 * @param string $command The command string provided.
	 *
	 * @return array The command and arguments extracted.
	 */
	protected function parse_command( $command ) {
		$command = str_replace( ' to ', ' ', $command );

		return parent::parse_command( $command );
	}
}
