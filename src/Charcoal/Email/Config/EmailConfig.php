<?php

namespace Charcoal\Email\Config;

// From `PHP`
use InvalidArgumentException;

// From `charcoal-core`
use Charcoal\Config\AbstractConfig;

use Charcoal\Email\Service\EmailParser;

/**
 * Email configuration.
 */
class EmailConfig extends AbstractConfig
{
    /**
     * The default sender's email address.
     *
     * @var string
     */
    private $defaultFrom = 'charcoal@example.com';

    /**
     * Set the default sender's email address.
     *
     * @param  string|array $email The default "From" email address.
     * @return self
     */
    public function setDefaultFrom($email)
    {
        $this->defaultFrom = $email;
        return $this;
    }

    /**
     * Get the sender email address.
     *
     * @return string
     */
    public function defaultFrom()
    {
        return $this->defaultFrom;
    }
}
