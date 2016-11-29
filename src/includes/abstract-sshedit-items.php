<?php
/**
 * The Items Collection.
 *
 * @package SSH_Edit
 *
 * @since 1.0.0
 */
namespace SSHEdit;

/**
 * The Items collection.
 *
 * Framework for colections.
 *
 * @internal Extended by the Section and Config collections.
 *
 * @since 1.0.0
 */
abstract class Items extends Item {
	/**
	 * The class name to use for new items.
	 *
	 * @since 1.0.0
	 *
	 * @param string
	 */
	const CHILD_CLASS = 'Item';

	/**
	 * A list of alias items.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $items = array();

	/**
	 * Add an item.
	 *
	 * @since 1.0.0
	 *
	 * @param Item|string $item       The Item object or ID for a new one.
	 * @param array       $properties Optional The initial property values to use.
	 * @param bool        $silent     Optional Wether or not to update $changed.
	 *
	 * @return Item the added item.
	 */
	public function add( $item, $properties = array(), $silent = false ) {
		$class = __NAMESPACE__ . '\\' . static::CHILD_CLASS;
		if ( ! is_a( $item, $class ) ) {
			$item = new $class( $item, $properties );
		}

		$this->items[ $item->id ] = $item;

		if ( ! $silent ) {
			$this->changed = true;
		}
	}

	/**
	 * Check if an item exists.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id The ID of the item to check.
	 *
	 * @return bool Wether or not the item exists.
	 */
	public function exists( $id ) {
		return isset( $this->items[ $id ] );
	}

	/**
	 * Fetch an existing item.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id The ID of the item to retrieve.
	 *
	 * @return Item The item if it was found, NULL otherwise.
	 */
	public function fetch( $id ) {
		if ( $this->exists( $id ) ) {
			return $this->items[ $id ];
		}
		return null;
	}

	/**
	 * Delete an existing item.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id The ID of the item to delete.
	 */
	public function delete( $id ) {
		unset( $this->items[ $id ] );
	}

	/**
	 * Update the items list to reflect their new order.
	 *
	 * @since 1.0.0
	 */
	public function sort() {
		uksort( $this->items, array( __CLASS__, 'sort_helper' ) );
	}

	/**
	 * Sort the Item objects by their order, followed by id.
	 *
	 * @since 1.0.0
	 *
	 * @param Item $a The item to sort.
	 * @param Item $b The item to sort against.
	 */
	protected static function sort_helper( $a, $b ){
		$a_order = $a->get( 'order' );
		$b_order = $b->get( 'order' );

		if ( $a_order == $b_order ) {
			return $a->get( 'id' ) < $b->get( 'id' ) ? -1 : 1;
		}
		return ( $a_order < $b_order ) ? -1 : 1;
	}

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
