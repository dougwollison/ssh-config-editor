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
	 * The path to the realine history file.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $command_log = '~/.sshedit_history';

	/**
	 * The config file currently being edited.
	 *
	 * @since 1.0.0
	 *
	 * @var Config
	 */
	protected $config = null;

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
	 */
	public function __construct() {
		echo "Welcome to SSH Edit. Type \"help\" for options, or \"quit\" to exit.\n";
		
		$arguments = $_SERVER['argv'];
		array_shift( $arguments );
		
		if ( isset( $arguments[0] ) ) {
			$file = $arguments[0];
			
			if ( ! file_exists( $file ) && ! is_writable( dirname( $file ) ) ) {
				echo "Provided file not found.\n";
			}
		} else {
			$file = getcwd() . '/.ssh/config';
			if ( ! file_exists( $file ) ) {
				if ( basename( dirname( getcwd() ) ) == '.ssh' ) {
					$file = getcwd() . '/config';
				} else {
					$file = self::realpath( '~/.ssh/config' );
				}
			}
		}

		if ( $file && file_exists( $file ) ) {
			$file = realpath( $file );
			$this->config = new Config( $file );
			echo "Loaded file '{$file}' for editing.\n";
		} else {
			echo "Please use the \"open\" command to load a config file.\n";
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
	 * Ensure $config is set, print error otherwise.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Wether or not the config is set.
	 */
	protected function has_config() {
		if ( is_null( $this->config ) ) {
			echo "ERROR: No config file opened/loaded yet.\n";
			return false;
		}
		return true;
	}

	/**
	 * Check if there unsaved changes, confirm action.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Wether or not to abort the action.
	 */
	protected function is_unsaved() {
		if ( $this->config && $this->config->has_changed() ) {
			if ( ! $this->confirm( "You have unsaved changes. Discard?" ) ) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Resolve the provided section/alias arguments.
	 *
	 * Namely, if we are in a section already, assume alias was passed to $section.
	 *
	 * @since 1.0.0
	 *
	 * @param string &$section The section requested, by reference.
	 * @param string &$alias   The alias requested, by reference.
	 */
	protected function resolve_target( &$section, &$alias ) {
		if ( $section && $alias ) {
			return false;
		}
		
		if ( $section && $this->section ) {
			$alias = $section;
			$section = $this->section;
		}
	}

	/**
	 * Find the target item requested based on provided section/alias.
	 *
	 * @since 1.0.0
	 *
	 * @param string $section Optional The section to find.
	 * @param string $alias   Optioanl The alias to find.
	 *
	 * @return Item The found target item. FALSE on failure.
	 */
	protected function find_target( $section = null, $alias = null ) {
		$target = $this->config;

		$this->resolve_target( $section, $alias );

		if ( $section ) {
			if ( ! $target->exists( $section ) ) {
				echo "Section {$section} not found.\n";
				return false;
			}
			$target = $target->fetch( $section );

			if ( $alias ) {
				if ( ! $target->exists( $alias ) ) {
					echo "Alias {$alias} not found in section {$section}.\n";
					return false;
				}

				$target = $target->fetch( $alias );
			}
		}
		
		return $target;
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

save [DESTINATION]
Save the compiled config file.

print [SECTION [ALIAS]]
Print out the compiled config file.

dump [SECTION [ALIAS]]
Dump the entire object for a section/alias.

quit
Take a wild guess.


HELP;
	}

	/**
	 * Select a section/alias to use.
	 *
	 * @since 1.0.0
	 */
	public function cmd_select( $section = null, $alias = null ) {
		if ( ! $section ) {
			echo "Please provide a section/alias ID.\n";
			return;
		}
		
		if ( strpos( $section, '/' ) !== false ) {
			list( $section, $alias ) = array_pad( explode( '/', $section ), 2, null );
		}
		
		if ( $section && ! $this->config->exists( $section ) ) {
			echo "Section '{$section}' not found.\n";
			return;
		}
		
		$this->section = $section;
		
		if ( $alias ) {
			if ( $this->config->fetch( $section )->exists( $alias ) ) {
				echo "Alias '{$alias}' not found in section '{$section}'.\n";
				return;
			}
			
			$this->alias = $alias;
			echo "Selected alias '{$alias}' in section '{$section}'.\n";
		}
	}

	/**
	 * Alias of cmd_select().
	 */
	protected function cmd_cd() {
		$this->cmd_select();
	}

	/**
	 * List sections/aliases/properties.
	 *
	 * @since 1.0.0
	 */
	public function cmd_list( $section = null, $alias = null ) {
		if ( ! ( $target = $this->find_target( $section, $alias ) ) ) {
			return;
		}
		
		$columns = array( '@' => 'Property', '$' => 'Value' );
		
		if ( is_a( $target, __NAMESPACE__ . '\\Items' ) ) {
			$columns = array( '@' => 'Entry', 'comment' => 'Description' );
		}
		
		return $this->pretty_table( $columns, $target->dump() );
	}

	/**
	 * Alias of cmd_list().
	 */
	protected function cmd_show() {
		$this->cmd_list();
	}

	/**
	 * Alias of cmd_list().
	 */
	protected function cmd_ls() {
		$this->cmd_list();
	}

	/**
	 * Create a section/alias.
	 *
	 * @since 1.0.0
	 */
	public function cmd_add( $type = null, $id = null, $parent = null ) {
		if ( ! $type ) {
			echo "Specify a type of entry to add; 'section' or 'alias'.\n";
			return;
		}
		
		if ( ! $id ) {
			$id = $this->prompt( 'Please provide an ID.' );
			if ( ! $id ) {
				echo "You must specify an ID.\n";
				return;
			}
		}
		
		switch ( $type ) {
			case 'section':
				$section = $this->config->add( $id );
				$section->set( 'comment', $this->prompt( 'Describe this section:' ) );
				$this->section = $id;
				
				echo "Section created and selected.\n";
				break;
				
			case 'alias':
				if ( $parent ) {
					if ( ! $this->config->exists( $parent ) ) {
						echo "Unknown section '{$parent}'.\n";
						return;
					}
					$this->section = $parent;
				} elseif ( ! $this->section ) {
					$section = $this->config->fetch( '(unsorted)' ) ?: $this->config->add( '(unsorted)' );
					$this->section = $section->get( 'id' );
				}
				
				$section = $this->config->fetch( $this->section );
				
				$alias = $section->add( $id );
				$alias->set( 'comment', $this->prompt( 'Describe this section:' ) );
				
				$alias->set( 'hostname', $this->prompt( 'Enter the host name.' ) );
				$alias->set( 'user', $this->prompt( 'Enter the username.' ) );
				$alias->set( 'port', $this->prompt( 'Enter the port number.', 22 ) );
				
				$default_path = basename( $this->config->get( 'file' ) ) . '/' . $this->section . '/' . $id;
				
				$keyfile = self::realpath( $this->prompt( 'Enter the path to the public key file.', $default_path ) );
				
				if ( ! file_exists( $keyfile ) ) {
					if ( $this->confirm( 'Key file does not exist. Create one there?' ) ) {
						$keyfile = escapeshellarg( $keyfile );
						echo "Redirecting you to SSH Keygen; creating an RSA 4096 key...\n";
						passthru( "ssh-keygen -t RSA -b 4096 -f $keyfile" );
					}
				}
				
				$alias->set( 'identityfile', $keyfile );
				
				$this->alias = $id;
				
				echo "Alias created and selected.\n";
				break;
				
			default:
				echo "Unknown type specified; please specify 'section' or 'alias'.\n";
				break;
		}
	}

	/**
	 * Edit a section/alias.
	 *
	 * @since 1.0.0
	 */
	public function cmd_edit() {
	}

	/**
	 * Delete a section/alias.
	 *
	 * @since 1.0.0
	 */
	public function cmd_delete() {
	}

	/**
	 * Open a config file for editing.
	 *
	 * @since 1.0.0
	 *
	 * @param $file The file to open.
	 */
	public function cmd_open( $file = null ) {
		if ( $this->is_unsaved() ) {
			return;
		}

		if ( ! $file ) {
			echo "Please specify a file to open.\n";
			return;
		}

		$file = self::realpath( $file );

		if ( file_exists( $file ) ) {
			$this->config = new Config( $file );
			echo "File loaded and ready for editing.\n";
			return;
		} else if ( is_writable( dirname( $file ) ) ) {
			$this->config = new Config( $file );
			echo "File open and ready for editing.\n";
			return;
		}

		echo "File not found and location not writable.\n";
		return;
	}

	/**
	 * Print out the compiled config or a section/alias in it.
	 *
	 * @since 1.0.0
	 *
	 * @param string $section Optional The section to print.
	 * @param string $alias   Optional The alias to print.
	 */
	public function cmd_print( $section = null, $alias = null ) {
		if ( ! ( $target = $this->find_target( $section, $alias ) ) ) {
			return;
		}

		echo $target->compile();
	}

	/**
	 * Dump a section/alias in it.
	 *
	 * @since 1.0.0
	 *
	 * @param string $section Optional The section to print.
	 * @param string $alias   Optional The alias to print.
	 */
	public function cmd_dump( $section = null, $alias = null ) {
		$target = $this->find_target( $section, $alias );

		print_r( $target );
	}

	/**
	 * Save the current config.
	 *
	 * @since 1.0.0
	 *
	 * @param $file The file to save to.
	 */
	public function cmd_save( $file = null ) {
		if ( ! $this->has_config() ) {
			return;
		}

		if ( ! is_writable( $file ) ) {
			echo "Unable to write to {$file}\n";
			return;
		}

		$this->config->save( $file );

		echo "Config saved to {$file}.\n";
	}

	/**
	 * Quit assuming no changes have been made.
	 *
	 * @since 1.0.0
	 */
	public function cmd_quit() {
		if ( $this->is_unsaved() ) {
			return;
		}

		parent::cmd_quit();
	}
}
