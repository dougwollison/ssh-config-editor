<?php
/**
 * The Section Collection.
 *
 * @package SSH_Edit
 *
 * @since 1.0.0
 */
namespace SSHEdit;

/**
 * The Section collection.
 *
 * A collection of related ssh aliases.
 *
 * @api
 *
 * @since 1.0.0
 */
class Section extends Items {
	/**
	 * The class name to use for new items.
	 *
	 * @since 1.0.0
	 *
	 * @param string
	 */
	const CHILD_CLASS = 'Alias';

	/**
	 * Compile into SSH config file format.
	 *
	 * @since 1.0.0
	 *
	 * @return string The formatted data.
	 */
	public function compile() {
		$output =
		"# [{$this->id}]\n" .
		"# {$this->comment}\n" .
		"\n";

		$this->sort();
		foreach ( $this->items as $alias ) {
			$output .= $alias->compile();
		}

		return "{$output}\n";
	}
}
