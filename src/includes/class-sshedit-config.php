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
			$section = $this->add( '(unsorted)' );
			$alias = null;

			foreach ( explode("\n", $data ) as $line ) {
				if ( preg_match( '/^# \[(\w+)\]/', $line, $matches ) ) {
					$section = $this->add( $matches[1], null, 'silent' );
				} else
				if ( preg_match( '/^# =+ (.+) =+/', $line, $matches ) ) {
					if ( $section ) {
						$section->comment( $matches[1] );
					}
				} else
				if ( preg_match( '/^Host (.+)/', $line, $matches ) ) {
					$alias = $section->add( $matches[1], null, 'silent' );
				} else
				if ( preg_match( '/^# (.+)/', $line, $matches ) ) {
					if ( $alias ) {
						$alias->comment( $matches[1] );
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
}
