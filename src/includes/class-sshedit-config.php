<?php
/**
 * The Config Collection.
 *
 * @package SSH_Edit
 *
 * @since 1.0.0
 */
namespace SSHEdit;

/**
 * The Config collection.
 *
 * A collection of Section collections.
 *
 * @api
 *
 * @since 1.0.0
 */
class Config extends Items {
	/**
	 * The class name to use for new items.
	 *
	 * @since 1.0.0
	 *
	 * @param string
	 */
	const CHILD_CLASS = 'Section';

	/**
	 * The file to load from/write to.
	 *
	 * @since 1.0.0
	 *
	 * @param string
	 */
	protected $file;

	/**
	 * Load a file and parse it's contents.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file The file to load from (and later save to).
	 */
	public function __construct( $file ) {
		$this->file = $file;

		if ( file_exists( $file ) ) {
			$section = null;
			$alias = null;

			$data = file_get_contents( $file );
			foreach ( explode( "\n", $data ) as $line ) {
				if ( preg_match( '/^# \[(\w+)\]/', $line, $matches ) ) {
					$section = $this->add( $matches[1], array(), 'silent' );
				} else
				if ( preg_match( '/^# =+ (.+) =+/', $line, $matches ) ) {
					if ( ! $section ) {
						$section = $this->add( '(unsorted)' );
					}
					$section->set( 'comment', $matches[1], 'silent' );
				} else
				if ( preg_match( '/^Host (.+)/', $line, $matches ) ) {
					if ( ! $section ) {
						$section = $this->add( '(unsorted)' );
					}
					$alias = $section->add( $matches[1], array(), 'silent' );
				} else
				if ( preg_match( '/^# (.+)/', $line, $matches ) ) {
					if ( $alias ) {
						$alias->set( 'comment', $matches[1], 'silent' );
					}
				} else
				if ( preg_match( '/^\s+(\w+) (.+)/', $line, $matches ) ) {
					if ( $alias ) {
						$alias->set( $matches[1], $matches[2], 'silent' );
					}
				}
			}

			$this->changed = false;
		}
	}

	/**
	 * Compile into SSH config file format.
	 *
	 * @since 1.0.0
	 *
	 * @return string The formatted data.
	 */
	public function compile() {
		$output = "## Built with SSH Edit.\n\n";

		$this->sort();
		foreach ( $this->items as $section ) {
			$output .= trim( $section->compile() ) ."\n\n";
		}

		return trim( $output ) ."\n\n";
	}

	/**
	 * Save the compiled output to a the file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file Optional. A specific file to save to (defaults to the $this->file).
	 */
	public function save( $file = null ) {
		$file = $file ?: $this->file;

		$contents = $this->compile();

		file_put_contents( $file, $contents );
	}
}
