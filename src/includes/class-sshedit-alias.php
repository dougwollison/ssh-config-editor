<?php
/**
 * The Alias Model.
 *
 * @package SSH_Edit
 *
 * @since 1.0.0
 */
namespace SSHEdit;

/**
 * The Alias model.
 *
 * A representation of an ssh alias.
 *
 * @api
 *
 * @since 1.0.0
 */
class Alias extends Item {
	/**
	 * The host name for the connection.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $host_name;

	/**
	 * The identity file/key for the connection.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $identity_file;

	/**
	 * The username for the connection.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $user;

	/**
	 * The port for the connection.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	protected $port = 22;
}
