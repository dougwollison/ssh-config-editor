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
class Section extends Item {
	/**
	 * A list of alias items.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $items = array();

	/**
	 * Check wether the item (or it's children) has changed or not.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Wether or not this item or it's children have changed.
	 */
	public function has_changed() {
		foreach ( $this->items as $item ) {
			if ( $item->has_changed() ) {
				return true;
			}
		}

		return $this->changed;
	}
}
