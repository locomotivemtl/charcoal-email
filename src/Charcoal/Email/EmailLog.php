<?php

namespace Charcoal\Email;

use DateTime;
use DateTimeInterface;
use Exception;
use InvalidArgumentException;

// From 'charcoal-core'
use Charcoal\Model\AbstractModel;

/**
 * Email log
 */
class EmailLog extends AbstractModel
{
    use EmailAwareTrait;

    /**
     * Type of log (e.g., "email").
     *
     * @var string $type
     */
    protected $type;

    /**
     * The action logged (e.g., "send").
     *
     * @var string $action
     */
    protected $action;

    /**
     * The Message-ID (Unique message identifier)
     *
     * @var string $messageId
     */
    protected $messageId;

    /**
     * The campaign ID.
     *
     * @var string $campaign
     */
    protected $campaign;

    /**
     * The sender's email address.
     *
     * @var string $from
     */
    protected $from;

    /**
     * The recipient's email address.
     *
     * @var string $to
     */
    protected $to;

    /**
     * The email subject.
     *
     * @var string $subject
     */
    protected $subject;

    /**
     * Whether the email has been semt.
     *
     * Error code (0 = success)
     *
     * @var integer $sendStatus
     */
    protected $sendStatus;

    /**
     * The error message from a failed send.
     *
     * @var string $sendError
     */
    protected $sendError;

    /**
     * When the email should be sent.
     *
     * @var DateTimeInterface|null $sendTs
     */
    protected $sendTs;

    /**
     * The current IP address at the time of the log.
     *
     * @var string $ip
     */
    protected $ip;

    /**
     * The current session ID at the time of the log.
     *
     * @var string $sessionId
     */
    protected $sessionId;

    /**
     * Get the primary key that uniquely identifies each queue item.
     *
     * @return string
     */
    public function key()
    {
        return 'id';
    }

    /**
     * Set the type of log.
     *
     * @param  string $type The log type. (e.g., "email").
     * @throws InvalidArgumentException If the log type is not a string.
     * @return self
     */
    public function setType($type)
    {
        if (!is_string($type)) {
            throw new InvalidArgumentException(
                'Log type must be a string.'
            );
        }

        $this->type = $type;

        return $this;
    }

    /**
     * Set the logged action.
     *
     * @param  string $action The log action (e.g., "send").
     * @throws InvalidArgumentException If the action is not a string.
     * @return self
     */
    public function setAction($action)
    {
        if (!is_string($action)) {
            throw new InvalidArgumentException(
                'Action must be a string.'
            );
        }

        $this->action = $action;

        return $this;
    }

    /**
     * Set the Message-ID.
     *
     * @param string $messageId The Message-ID.
     * @throws InvalidArgumentException If the Message-ID is not a string.
     * @return self
     */
    public function setMessageId($messageId)
    {
        if (!is_string($messageId)) {
            throw new InvalidArgumentException(
                'Message-ID must be a string.'
            );
        }

        $this->messageId = $messageId;

        return $this;
    }

    /**
     * Set the campaign ID.
     *
     * @param  string $campaign The campaign identifier.
     * @throws InvalidArgumentException If the campaign is invalid.
     * @return self
     */
    public function setCampaign($campaign)
    {
        if (!is_string($campaign)) {
            throw new InvalidArgumentException(
                'Campaign must be a string'
            );
        }

        $this->campaign = $campaign;

        return $this;
    }

    /**
     * Set the sender's email address.
     *
     * @param  string|array $email An email address.
     * @throws InvalidArgumentException If the email address is invalid.
     * @return self
     */
    public function setFrom($email)
    {
        $this->from = $this->parseEmail($email);
        return $this;
    }

    /**
     * Set the recipient's email address.
     *
     * @param  string|array $email An email address.
     * @return self
     */
    public function setTo($email)
    {
        $this->to = $this->parseEmail($email);
        return $this;
    }

    /**
     * Set the email subject.
     *
     * @param  string $subject The email subject.
     * @throws InvalidArgumentException If the subject is not a string.
     * @return self
     */
    public function setSubject($subject)
    {
        if (!is_string($subject)) {
            throw new InvalidArgumentException(
                'Subject needs to be a string'
            );
        }

        $this->subject = $subject;

        return $this;
    }

    /**
     * @param  string $status The mailer's status code or description.
     * @return self
     */
    public function setSendStatus($status)
    {
        $this->sendStatus = $status;
        return $this;
    }

    /**
     * @param  string $errorMessage The mailer's error code or description.
     * @return self
     */
    public function setSendError($errorMessage)
    {
        $this->sendError = $errorMessage;
        return $this;
    }

    /**
     * @param  null|string|DateTime $ts The "send date" datetime value.
     * @throws InvalidArgumentException If the ts is not a valid datetime value.
     * @return self
     */
    public function setSendTs($ts)
    {
        if ($ts === null) {
            $this->sendTs = null;
            return $this;
        }

        if (is_string($ts)) {
            try {
                $ts = new DateTime($ts);
            } catch (Exception $e) {
                throw new InvalidArgumentException($e->getMessage());
            }
        }

        if (!($ts instanceof DateTimeInterface)) {
            throw new InvalidArgumentException(
                'Invalid "Send Date" value. Must be a date/time string or a DateTime object.'
            );
        }

        $this->sendTs = $ts;
        return $this;
    }

    /**
     * @param mixed $ip The IP adress.
     * @return self
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @param string $sessionId The session identifier.
     * @return self
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
        return $this;
    }

    /**
     * @see    StorableTrait::preSave()
     * @return boolean
     */
    protected function preSave()
    {
        parent::preSave();

        $ip = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
        $sessionId = session_id();

        $this->setIp($ip);
        $this->setSessionId($sessionId);

        if ($this['sendTs'] === null) {
            $this->setSendTs('now');
        }

        return true;
    }
}
