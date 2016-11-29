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
}
