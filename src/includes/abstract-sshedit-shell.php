<?php
/**
 * The Shell Controller.
 *
 * @package SSH_Edit
 *
 * @since 1.0.0
 */
namespace SSHEdit;

/**
 * The Shell controller.
 *
 * A basis for the CLI.
 *
 * @internal Extended by the CLI controller.
 *
 * @since 1.0.0
 */
abstract class Shell {
	/**
	 * The "host" to display before the command prompt.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const NAME = 'mish';

	/**
	 * The "path" to display alongside the "host".
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $path = array();

	/**
	 * Initailize the CLI.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file The file to load.
	 */
	public function __construct() {
		while ( true ) {
			$this->before_loop();

			$command = $this->prompt( static::NAME . ':' . implode( '/', $this->path ) . ' $' );

			list( $command, $args ) = $this->parse_command( $command );

			$method = "cmd_$command";
			if ( method_exists( $this, $method ) ) {
				call_user_func_array( array( $this, $method ), $args );
			} else {
				echo "Command not found.\n";
			}

			$this->after_loop();
		}
	}

	protected function before_loop() {}
	protected function after_loop() {}

	/**
	 * Issue a readline, with a optional default value handling.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message The message to display.
	 * @param mixed  $default Optional The default value to display/use.
	 *
	 * @return mixed The result of the readline() command.
	 */
	protected function prompt( $message, $default = null ) {
		$prompt = "{$message} ";
		if ( $default ) {
			$prompt .= "(default: {$default}) ";
		}

		return readline( $prompt ) ?: $default;
	}

	/**
	 * Issue a readline, returning wether the user ender yes/no.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message The message to display.
	 *
	 * @return bool Wether or not they said yes.
	 */
	protected function confirm( $message ) {
		$result = $this->prompt("{$message} [y|n]: " );
		return preg_match( '/y|yes/', $result );
	}

	/**
	 * Parse a command string into it's command and arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param string $command The command string to parse.
	 *
	 * @return array The extracted command and arguments list.
	 */
	protected function parse_command( $command ) {
		$args = explode( ' ', $command );
		$command = array_shift( $args );

		return array( $command, $args );
	}
	
	/**
	 * Print a pretty table to display data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns The columns of the table.
	 * @param array $data    The rows data for the table.
	 */
	protected function pretty_table( $columns, $data ) {
		$rows = array( $columns );
	
		$maxlengths = array_map( 'strlen', $columns );
	
		foreach ( $data as $key => $value ) {
			$row = array();
	
			foreach ( $columns as $flag => $label ) {
				$val = null;
				if ( $flag == '@' ) {
					$val = $key;
				} else
				if ( $flag == '$' ) {
					$val = $value;
				} else
				if ( is_array( $value ) ) {
					$val = $value[ $flag ];
				} else
				if ( is_a( $value, __NAMESPACE__ . '\\Item' ) ) {
					$val = $value->get( $flag );
				}
	
				$maxlengths[ $flag ] = max( array( strlen( $val ), $maxlengths[ $flag ] ) );
	
				$row[ $flag ] = $val;
			}
	
			$rows[] = $row;
		}
	
		$width = array_sum( $maxlengths ) + ( count( $columns ) * 2 ) + 3;
		$border = str_repeat( '=', $width ) ."\n";
	
		$output = $border;
		foreach ( $rows as $i => $row ) {
			foreach ( $row as $flag => $cell ) {
				$output .= '| ';
				$output .= str_pad( $cell, $maxlengths[ $flag ], ' ' );
				$output .= ' ';
			}
			$output .= '|';
	
			$output .= "\n";
	
			if ( ! $i ) {
				$output .= $border;
			}
		}
		$output .= $border;
	
		echo"\n{$output}\n";
	}

	/**
	 * Default Quit handler.
	 *
	 * @since 1.0.0
	 */
	protected function cmd_quit() {
		echo"Bye!\n";
		exit;
	}

	/**
	 * Alias of cmd_quit().
	 */
	protected function cmd_exit() {
		$this->cmd_quit();
	}
}
