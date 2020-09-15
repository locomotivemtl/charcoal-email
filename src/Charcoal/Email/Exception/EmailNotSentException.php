<?php

namespace Charcoal\Email\Exception;

use Exception;

/**
 * EmailNotSentException
 *
 * Exception thrown when sending an email fails and there is absolutely no doubt no email has been sent.
 */
class EmailNotSentException extends Exception
{
}