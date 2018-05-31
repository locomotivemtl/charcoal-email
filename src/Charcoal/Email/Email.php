<?php

namespace Charcoal\Email;

// Dependencies from `PHP`

use InvalidArgumentException;

// Module `charcoal-factory` dependencies
use Charcoal\Factory\FactoryInterface;

// Module `charcoal-view` dependencies
use Charcoal\View\ViewableInterface;
use Charcoal\View\ViewableTrait;

// Module `charcoal-queue` dependencies
use Charcoal\Queue\QueueableInterface;
use Charcoal\Queue\QueueableTrait;

// Intra module (`charcoal-email`) dependencies
use Charcoal\Email\EmailInterface;
use Charcoal\Email\Config\EmailConfig;
use Charcoal\Email\Service\EmailParser;
use Charcoal\Email\Service\EmailSender;
use Charcoal\Email\Template\GenericEmailTemplate;

/**
 * Default implementation of the `EmailInterface`.
 */
class Email implements
    EmailInterface,
    QueueableInterface,
    ViewableInterface
{
    use QueueableTrait;
    use ViewableTrait;

    /**
     * The campaign ID.
     *
     * @var string $campaign
     */
    private $campaign = '';

    /**
     * The recipient email address(es).
     *
     * @var array $to
     */
    private $to = [];

    /**
     * The CC recipient email address(es).
     *
     * @var array $cc
     */
    private $cc = [];

    /**
     * The BCC recipient email address(es).
     *
     * @var array $bcc
     */
    private $bcc = [];

    /**
     * The sender's email address.
     *
     * @var string|null $from
     */
    private $from = '';

    /**
     * The email address to reply to the message.
     *
     * @var string $replyTo
     */
    private $replyTo = '';

    /**
     * The email subject.
     *
     * @var string
     */
    private $subject = '';

    /**
     * The HTML message body.
     *
     * @var string
     */
    private $messageHtml = '';

    /**
     * The plain-text message body.
     *
     * @var string
     */
    private $messageTxt = '';

    /**
     * @var array
     */
    private $attachments = [];

    /**
     * The data to pass onto the view controller.
     *
     * @var array
     */
    private $templateData = [];

    /**
     * Whether the email should be logged.
     *
     * @var boolean $log
     */
    private $logEnabled = true;

    /**
     * Whether the email should be tracked.
     *
     * @var boolean $track
     */
    private $trackEnabled = false;

    /**
     * @var FactoryInterface
     */
    private $templateFactory;

    /**
     * @var EmailParser
     */
    private $parser;

    /**
     * @var EmailSender
     */
    private $sender;

    /**
     * Construct a new Email object.
     *
     * @param array $data Dependencies and settings.
     */
    public function __construct(array $data)
    {
        // ViewableInterface dependencies
        $this->setView($data['view']);
        $this->setTemplateFactory($data['templateFactory']);

        $this->setParser($data['parser']);
        $this->setSender($data['sender']);
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
     * Get the campaign identifier.
     *
     * If it has not been explicitely set, it will be auto-generated (with uniqid).
     *
     * @return string
     */
    public function campaign()
    {
        if ($this->campaign === '') {
            $this->campaign = uniqid();
        }

        return $this->campaign;
    }

    /**
     * Set the recipient email address(es).
     *
     * @param string|array $email The recipient email address(es).
     * @throws InvalidArgumentException If the email address is invalid.
     * @return self
     */
    public function setTo($email)
    {
        if (is_string($email)) {
            $email = [ $email ];
        }

        if (!is_array($email)) {
            throw new InvalidArgumentException(
                'Must be an array of recipients.'
            );
        }

        $this->to = [];

        // At this point, `$email` can be an _email array_ or an _array of emails_...
        if (isset($email['email'])) {
            // Means we're not dealing with multiple emails
            $this->addTo($email);
        } else {
            foreach ($email as $recipient) {
                $this->addTo($recipient);
            }
        }

        return $this;
    }

    /**
     * Add a recipient email address.
     *
     * @param  mixed $email The recipient email address to add.
     * @throws InvalidArgumentException If the email address is invalid.
     * @return self
     */
    public function addTo($email)
    {
        $this->to[] = $this->parser->parse($email);
        return $this;
    }

    /**
     * Get the recipient's email addresses.
     *
     * @return string[]
     */
    public function to()
    {
        return $this->to;
    }

    /**
     * Set the carbon copy (CC) recipient email address(es).
     *
     * @param string|array $email The CC recipient email address(es).
     * @throws InvalidArgumentException If the email address is invalid.
     * @return self
     */
    public function setCc($email)
    {
        if (is_string($email)) {
            $email = [ $email ];
        }

        if (!is_array($email)) {
            throw new InvalidArgumentException(
                'Must be an array of CC recipients.'
            );
        }

        $this->cc = [];

        // At this point, `$email` can be an _email array_ or an _array of emails_...
        if (isset($email['email'])) {
            // Means we're not dealing with multiple emails
            $this->addCc($email);
        } else {
            foreach ($email as $recipient) {
                $this->addCc($recipient);
            }
        }

        return $this;
    }

    /**
     * Add a CC recipient email address.
     *
     * @param mixed $email The CC recipient email address to add.
     * @throws InvalidArgumentException If the email address is invalid.
     * @return self
     */
    public function addCc($email)
    {
        $this->cc[] = $this->parser->parse($email);
        return $this;
    }

    /**
     * Get the CC recipient's email address.
     *
     * @return string[]
     */
    public function cc()
    {
        return $this->cc;
    }

    /**
     * Set the blind carbon copy (BCC) recipient email address(es).
     *
     * @param string|array $email The BCC recipient email address(es).
     * @throws InvalidArgumentException If the email address is invalid.
     * @return self
     */
    public function setBcc($email)
    {
        if (is_string($email)) {
            // Means we have a straight email
            $email = [ $email ];
        }

        if (!is_array($email)) {
            throw new InvalidArgumentException(
                'Must be an array of BCC recipients.'
            );
        }

        $this->bcc = [];

        // At this point, `$email` can be an _email array_ or an _array of emails_...
        if (isset($email['email'])) {
            // Means we're not dealing with multiple emails
            $this->addBcc($email);
        } else {
            foreach ($email as $recipient) {
                $this->addBcc($recipient);
            }
        }

        return $this;
    }

    /**
     * Add a BCC recipient email address.
     *
     * @param mixed $email The BCC recipient email address to add.
     * @throws InvalidArgumentException If the email address is invalid.
     * @return self
     */
    public function addBcc($email)
    {
        $this->bcc[] = $this->parser->parse($email);
        return $this;
    }

    /**
     * Get the BCC recipient's email address.
     *
     * @return string[]
     */
    public function bcc()
    {
        return $this->bcc;
    }

    /**
     * Set the sender's email address.
     *
     * @param  string|array $email An email address.
     * @throws InvalidArgumentException If the email is not a string or an array.
     * @return self
     */
    public function setFrom($email)
    {
        $this->from = $this->parser->parse($email);
        return $this;
    }

    /**
     * Get the sender's email address.
     *
     * @return string|null
     */
    public function from()
    {
        return $this->from;
    }

    /**
     * Set email address to reply to the message.
     *
     * @param  mixed $email The sender's "Reply-To" email address.
     * @throws InvalidArgumentException If the email is not a string or an array.
     * @return self
     */
    public function setReplyTo($email)
    {
        $this->replyTo = $this->parser->parse($email);
        return $this;
    }

    /**
     * Get email address to reply to the message.
     *
     * @return string
     */
    public function replyTo()
    {
        return $this->replyTo;
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
     * Get the email subject.
     *
     * @return string The emails' subject.
     */
    public function subject()
    {
        return $this->subject;
    }

    /**
     * Set the email's HTML message body.
     *
     * @param  string $body The HTML message body.
     * @throws InvalidArgumentException If the message is not a string.
     * @return self
     */
    public function setMessageHtml($body)
    {
        if (!is_string($body)) {
            throw new InvalidArgumentException(
                'HTML message needs to be a string'
            );
        }

        $this->messageHtml = $body;

        return $this;
    }

    /**
     * Get the email's HTML message body.
     *
     * If the message is not explitely set, it will be
     * auto-generated from a template view.
     *
     * @return string
     */
    public function messageHtml()
    {
        if ($this->messageHtml === '') {
            $this->messageHtml = $this->generateMessageHtml();
        }
        return $this->messageHtml;
    }

    /**
     * Set the email's plain-text message body.
     *
     * @param string $body The message's text body.
     * @throws InvalidArgumentException If the parameter is invalid.
     * @return self
     */
    public function setMessageTxt($body)
    {
        if (!is_string($body)) {
            throw new InvalidArgumentException(
                'Plan-text message needs to be a string'
            );
        }

        $this->messageTxt = $body;

        return $this;
    }

    /**
     * Get the email's plain-text message body.
     *
     * If the plain-text message is not explitely set,
     * it will be auto-generated from the HTML message.
     *
     * @return string
     */
    public function messageTxt()
    {
        if ($this->messageTxt === '') {
            $this->messageTxt = $this->stripHtml($this->messageHtml());
        }

        return $this->messageTxt;
    }

    /**
     * Set the email's attachments.
     *
     * @param  array $attachments The file attachments.
     * @return self
     */
    public function setAttachments(array $attachments)
    {
        foreach ($attachments as $att) {
            $this->addAttachment($att);
        }

        return $this;
    }

    /**
     * Add an attachment to the email.
     *
     * @param  mixed $attachment A single file attachment.
     * @return self
     */
    public function addAttachment($attachment)
    {
        $this->attachments[] = $attachment;
        return $this;
    }

    /**
     * Get the email's attachments.
     *
     * @return array
     */
    public function attachments()
    {
        return $this->attachments;
    }

    /**
     * Set the template data for the view.
     *
     * @param array $data The template data.
     * @return Email Chainable
     */
    public function setTemplateData(array $data)
    {
        $this->templateData = $data;
        return $this;
    }

    /**
     * Get the template data for the view.
     *
     * @return array
     */
    public function templateData()
    {
        return $this->templateData;
    }

    /**
     * Enable or disable logging for this particular email.
     *
     * @param  boolean $log The log flag.
     * @return self
     */
    public function setLogEnabled($log)
    {
        $this->logEnabled = !!$log;
        return $this;
    }

    /**
     * Determine if logging is enabled for this particular email.
     *
     * @return boolean
     */
    public function logEnabled()
    {
        return $this->logEnabled;
    }

    /**
     * Enable or disable tracking for this particular email.
     *
     * @param boolean $track The track flag.
     * @return self
     */
    public function setTrackEnabled($track)
    {
        $this->trackEnabled = !!$track;
        return $this;
    }

    /**
     * Determine if tracking is enabled for this particular email.
     *
     * @return boolean
     */
    public function trackEnabled()
    {
        return $this->trackEnabled;
    }


    /**
     * Set the email's data.
     *
     * @param array $data The data to set.
     * @return Email Chainable
     */
    public function setData(array $data)
    {
        foreach ($data as $prop => $val) {
            $func = [$this, 'set'.ucfirst($prop)];
            if (is_callable($func)) {
                call_user_func($func, $val);
            } else {
                $this->{$prop} = $val;
            }
        }

        return $this;
    }

    /**
     * Send the email to all recipients.
     *
     * @return boolean Success / Failure.
     */
    public function send()
    {
        return $this->sender->send($this);
    }

    /**
     * Enqueue the email for each recipient.
     *
     * @param mixed $ts A date/time to initiate the queue processing.
     * @return boolean Success / Failure.
     */
    public function queue($ts = null)
    {
        return$this->sender->queue($this, $ts);
    }

    /**
     * Get the custom view controller for rendering
     * the email's HTML message.
     *
     * Unlike typical `ViewableInterface` objects, the view controller is not
     * the email itself but an external "email" template.
     *
     * @see    ViewableInterface::viewController()
     * @return \Charcoal\App\Template\TemplateInterface|array
     */
    public function viewController()
    {
        $templateIdent = $this->templateIdent();

        if (!$templateIdent) {
            return [];
        }

        $template = $this->templateFactory->create($templateIdent);
        $template->setData($this->templateData());

        return $template;
    }

    /**
     * Get the email's HTML message from the template, if applicable.
     *
     * @see    ViewableInterface::renderTemplate()
     * @return string
     */
    protected function generateMessageHtml()
    {
        $templateIdent = $this->templateIdent();

        if (!$templateIdent) {
            $message = '';
        } else {
            $message = $this->renderTemplate($templateIdent);
        }

        return $message;
    }

    /**
     * Convert an HTML string to plain-text.
     *
     * @param string $html The HTML string to convert.
     * @return string The resulting plain-text string.
     */
    protected function stripHtml($html)
    {
        $str = html_entity_decode($html);

        // Strip HTML (Replace br with newline, remove "invisible" tags and strip other tags)
        $str = preg_replace('#<br[^>]*?>#siu', "\n", $str);
        $str = preg_replace(
            [
                '#<applet[^>]*?.*?</applet>#siu',
                '#<embed[^>]*?.*?</embed>#siu',
                '#<head[^>]*?>.*?</head>#siu',
                '#<noframes[^>]*?.*?</noframes>#siu',
                '#<noscript[^>]*?.*?</noscript>#siu',
                '#<noembed[^>]*?.*?</noembed>#siu',
                '#<object[^>]*?.*?</object>#siu',
                '#<script[^>]*?.*?</script>#siu',
                '#<style[^>]*?>.*?</style>#siu'
            ],
            '',
            $str
        );
        $str = strip_tags($str);

        // Trim whitespace
        $str = str_replace("\t", '', $str);
        $str = preg_replace('#\n\r|\r\n#', "\n", $str);
        $str = preg_replace('#\n{3,}#', "\n\n", $str);
        $str = preg_replace('# {2,}#', ' ', $str);
        $str = implode("\n", array_map('trim', explode("\n", $str)));
        $str = trim($str)."\n";
        return $str;
    }


    /**
     * @param FactoryInterface $factory The factory to use to create email template objects.
     * @return Email Chainable
     */
    private function setTemplateFactory(FactoryInterface $factory)
    {
        $this->templateFactory = $factory;
        return $this;
    }

    /**
     * @param EmailParser $parser The email parser service / helper.
     * @return void
     */
    private function setParser(EmailParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param EmailSender $sender The email sender service / helper.
     * @return void
     */
    private function setSender(EmailSender $sender)
    {
        $this->sender = $sender;
    }
}
