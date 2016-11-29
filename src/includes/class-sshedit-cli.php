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

		if ( $file && file_exists( $file ) ) {
			$this->config = new Config( $file );
		} else {
			echo "No file available for editing, please use the \"open\" command.\n";
		}

		parent::__construct();
	}

	/**
	 * Update the path before each loop begins.
	 *
	 * @since 1.0.0
	 */
	protected function before_loop() {
		$this->path = array( $this->section, $this->alias );
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

	/**
	 * Print the list of available commands.
	 *
	 * @since 1.0.0
	 */
	protected function cmd_help() {
		echo <<<HELP

open SOURCE
Open and parse a config file for editing.

dump [SECTION [ALIAS]]
Dump the entire object for a section/alias.

list [SECTION [ALIAS]]
List entries/details for a section/alias.

select [section|alias] ID
Select a section/alias to use for subsequent commands.

add [section ID|alias ID [to SECTION]]
Add a section/alias. Will prompt for values.

edit [SECTION [ALIAS]]
Edit a section/alias. Will prompt for changes.

delete [SECTION [ALIAS]]
Delete a section/entry.

print [SECTION [ALIAS]]
Print out the compiled config file.

save [DESTINATION]
Save the compiled config file.

quit
Take a wild guess.


HELP;
	}
}
