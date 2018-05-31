<?php

namespace Charcoal\Email\Config;

use InvalidArgumentException;

// From `charcoal-core`
use Charcoal\Config\AbstractConfig;

/**
 * SMTP configuration
 */
class SmtpConfig extends AbstractConfig
{
    /**
     * The SMTP hostname.
     *
     * @var string
     */
    private $hostname = '';

    /**
     * The SMTP port.
     *
     * @var integer
     */
    private $port = 0;

    /**
     * The SMTP security type (tls, ssl or empty).
     *
     * @var string
     */
    private $security = '';

    /**
     * Whether SMTP requires authentication.
     *
     * @var boolean
     */
    private $auth = false;

    /**
     * The SMTP username.
     *
     * @var string
     */
    private $username = '';

    /**
     * The SMTP password.
     *
     * @var string
     */
    private $password = '';


    /**
     * Set the SMTP hostname to be used.
     *
     * @param  string $hostname The SMTP hostname.
     * @throws InvalidArgumentException If the SMTP hostname is not a string.
     * @return self
     */
    public function setHostname($hostname)
    {
        if (!is_string($hostname)) {
            throw new InvalidArgumentException(
                'SMTP Hostname must be a string.'
            );
        }

        $this->hostname = $hostname;

        return $this;
    }

    /**
     * Get the SMTP hostname.
     *
     * @return string
     */
    public function hostname()
    {
        return $this->hostname;
    }

    /**
     * Set the SMTP port to be used.
     *
     * @param  integer $port The SMTP port.
     * @throws InvalidArgumentException If the SMTP port is not an integer.
     * @return self
     */
    public function setPort($port)
    {
        if (!is_int($port)) {
            throw new InvalidArgumentException(
                'SMTP Port must be an integer.'
            );
        }

        $this->port = $port;

        return $this;
    }

    /**
     * Get the SMTP port.
     *
     * @return integer
     */
    public function port()
    {
        return $this->port;
    }

    /**
     * Set whether SMTP requires authentication.
     *
     * @param  boolean $auth The SMTP authentication flag (if auth is required).
     * @return self
     */
    public function setAuth($auth)
    {
        $this->auth = !!$auth;
        return $this;
    }

    /**
     * Determine if SMTP requires authentication.
     *
     * @return boolean
     */
    public function auth()
    {
        return $this->auth;
    }

    /**
     * Set the SMTP username to be used.
     *
     * @param  string $username The SMTP username, if using authentication.
     * @throws InvalidArgumentException If the SMTP username is not a string.
     * @return self
     */
    public function setUsername($username)
    {
        if (!is_string($username)) {
            throw new InvalidArgumentException(
                'SMTP Username must be a string.'
            );
        }

        $this->username = $username;

        return $this;
    }

    /**
     * Get the SMTP username.
     *
     * @return string
     */
    public function username()
    {
        return $this->username;
    }

    /**
     * Set the SMTP password to be used.
     *
     * @param  string $password The SMTP password, if using authentication.
     * @throws InvalidArgumentException If the SMTP password is not a string.
     * @return self
     */
    public function setPassword($password)
    {
        if (!is_string($password)) {
            throw new InvalidArgumentException(
                'SMTP Password must be a string.'
            );
        }

        $this->password = $password;

        return $this;
    }

    /**
     * Get the SMTP password.
     *
     * @return string
     */
    public function password()
    {
        return $this->password;
    }

    /**
     * Set the SMTP security type to be used (forcing uppercase).
     *
     * @param  string $security The SMTP security type (empty, "TLS", or "SSL").
     * @throws InvalidArgumentException If the security type is not valid (empty, "TLS", or "SSL").
     * @return self
     */
    public function setSecurity($security)
    {
        $security = strtoupper($security);
        $validSecurity = [ '', 'TLS', 'SSL' ];

        if (!in_array($security, $validSecurity)) {
            throw new InvalidArgumentException(
                'SMTP Security is not valid. Must be "", "TLS" or "SSL".'
            );
        }

        $this->security = $security;

        return $this;
    }

    /**
     * Get the SMTP security type.
     *
     * @return string
     */
    public function security()
    {
        return $this->security;
    }
}
