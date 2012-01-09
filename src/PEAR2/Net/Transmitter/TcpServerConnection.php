<?php

/**
 * ~~summary~~
 * 
 * ~~description~~
 * 
 * PHP version 5
 * 
 * @category  Net
 * @package   PEAR2_Net_Transmitter
 * @author    Vasil Rangelov <boen.robot@gmail.com>
 * @copyright 2011 Vasil Rangelov
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @version   GIT: $Id$
 * @link      http://pear2.php.net/PEAR2_Net_Transmitter
 */
/**
 * The namespace declaration.
 */
namespace PEAR2\Net\Transmitter;

/**
 * A transmitter for connections to a socket server.
 * 
 * This is a convinience wrapper for functionality of socket server connections.
 * Used to ensure data integrity. Server handling is not part of the class in
 * order to allow its usage as part of various server implementations (e.g. fork
 * and/or sequential).
 * 
 * @category Net
 * @package  PEAR2_Net_Transmitter
 * @author   Vasil Rangelov <boen.robot@gmail.com>
 * @license  http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @link     http://pear2.php.net/PEAR2_Net_Transmitter
 */
class TcpServerConnection extends NetworkStream
{

    /**
     * @var string The IP address of the connected client.
     */
    protected $peerIP;

    /**
     * @var int The port of the connected client.
     */
    protected $peerPort;

    /**
     * Creates a new connection with the specified options.
     * 
     * @param resource $server  A socket server, created with
     * {@link stream_socket_server()}.
     * @param float    $timeout The timeout for the connection.
     */
    public function __construct($server, $timeout = null)
    {
        if (!self::isStream($server)) {
            throw $this->createException('Invalid server supplied.', 8);
        }
        $timeout
            = null == $timeout ? ini_get('default_socket_timeout') : $timeout;

        try {
            parent::__construct(
                @stream_socket_accept($server, $timeout, $peername)
            );
            $portString = strrchr($peername, ':');
            $this->peerPort = (int) substr($portString, 1);
            $ipString = substr(
                $peername, 0, strlen($peername) - strlen($portString)
            );
            if (strpos($ipString, '[') === 0
                && strpos(strrev($ipString), ']') === 0
            ) {
                $ipString = substr($ipString, 1, strlen($ipString) - 2);
            }
            $this->peerIP = $ipString;
        } catch (Exception $e) {
            throw $this->createException('Failed to initialize connection.', 9);
        }
    }
    
    /**
     * Gets the IP address of the connected client.
     * 
     * @return string The IP address of the connected client.
     */
    public function getPeerIP()
    {
        return $this->peerIP;
    }
    
    /**
     * Gets the port of the connected client.
     * 
     * @return int The port of the connected client.
     */
    public function getPeerPort()
    {
        return $this->peerPort;
    }

    /**
     * Creates a new exception.
     * 
     * Creates a new exception. Used by the rest of the functions in this class.
     * 
     * @param string $message The exception message.
     * @param int    $code    The exception code.
     * 
     * @return SocketException The exception to then be thrown.
     */
    protected function createException($message, $code = 0)
    {
        return new SocketException($message, $code);
    }

}