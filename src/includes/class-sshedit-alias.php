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

	/**
	 * Compile into SSH config file format.
	 *
	 * @since 1.0.0
	 *
	 * @return string The formatted data.
	 */
	public function compile() {
		return
		"Host {$this->id}\n" .
		"# {$this->comment}\n" .
		"	HostName {$this->host_name}\n" .
		"	IdentityFile {$this->identity_file}\n" .
		"	User {$this->user}\n" .
		"	Port {$this->port}\n";
	}
	
	/**
	 * Dump the alias as an array.
	 *
	 * @since 1.0.0
	 *
	 * @return array The alias attributes in array form.
	 */
	public function dump() {
		return array(
			'Host' => $this->id,
			'HostName' => $this->host_name,
			'IdentityFile' => $this->identity_file,
			'User' => $this->user,
			'Port' => $this->port,
		);
	}
}
