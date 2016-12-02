<?php
/**
 * The Item Model Framework.
 *
 * @package SSH_Edit
 *
 * @since 1.0.0
 */
namespace SSHEdit;

/**
 * The Item model.
 *
 * Framework for models.
 *
 * @internal Extended by the Alias model and the Items framework.
 *
 * @since 1.0.0
 */
abstract class Item {
	/**
	 * The id of the item.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * The item's parent object.
	 *
	 * @since 1.0.0
	 *
	 * @var mixed
	 */
	protected $parent;

	/**
	 * The order of the item within it's parent.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	protected $order = 0;

	/**
	 * The description of the item.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $comment = '';

	/**
	 * A flag for wether or not the item has changed.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected $changed = false;

	/**
	 * Create a new item.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id         The ID of the item.
	 * @param array  $properties The initial property values to use.
	 */
	public function __construct( $id, $properties ) {
		$this->id = $id;

		foreach ( $properties as $property => $value ) {
			$this->set( $property, $value, 'silent' );
		}
	}

	/**
	 * Get the value of a property.
	 *
	 * @since 1.0.0
	 *
	 * @param string $property The property to get the value of.
	 *
	 * @return mixed The value of the property.
	 */
	public function get( $property ) {
		$property = strtolower( $property );
		if ( property_exists( $this, $property ) ) {
			return $this->$property;
		}
		return null;
	}

	/**
	 * Set the value of a property.
	 *
	 * @since 1.0.0
	 *
	 * @param string $property The property to get the value of.
	 * @param mixed  $value    The value to use.
	 * @param bool   $silent   Optional Wether or not to update $changed.
	 *
	 * @return mixed The value of the property.
	 */
	public function set( $property, $value, $silent = false ) {
		$property = strtolower( $property );
		if ( property_exists( $this, $property ) ) {
			$this->$property = $value;

			if ( ! $silent ) {
				$this->changed = true;
			}
		}
		return null;
	}

	/**
	 * Check wether the item has changed or not.
	 *
	 * @since 1.0.0
	 *
	 * @return bool The value of the $changed flag.
	 */
	public function has_changed() {
		return $this->changed;
	}
	
	/**
	 * Dump the item as an array.
	 *
	 * @since 1.0.0
	 *
	 * @return array The object in array form.
	 */
	public function dump() {
		return array(
			'id' => $this->id,
			'order' => $this->order,
			'comment' => $this->comment,
		);
	}
}
