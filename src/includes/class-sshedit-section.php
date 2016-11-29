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
}
