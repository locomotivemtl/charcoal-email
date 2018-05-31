<?php

namespace Charcoal\Email;

/**
 * Email contract
 */
interface EmailInterface
{
    /**
     * Set the campaign ID.
     *
     * @param  string $campaign The campaign identifier.
     * @return self
     */
    public function setCampaign($campaign);

    /**
     * Get the campaign identifier.
     *
     * @return string
     */
    public function campaign();

    /**
     * Set the recipient email address(es).
     *
     * @param string|array $email The recipient email address(es).
     * @return self
     */
    public function setTo($email);

    /**
     * Add a recipient email address.
     *
     * @param  mixed $email The recipient email address to add.
     * @return self
     */
    public function addTo($email);

    /**
     * Get the recipient's email address.
     *
     * @return string[]
     */
    public function to();

    /**
     * Set the carbon copy (CC) recipient email address(es).
     *
     * @param string|array $email The CC recipient email address(es).
     * @return self
     */
    public function setCc($email);

    /**
     * Add a CC recipient email address.
     *
     * @param mixed $email The CC recipient email address to add.
     * @return self
     */
    public function addCc($email);

    /**
     * Get the CC recipient's email address.
     *
     * @return string[]
     */
    public function cc();

    /**
     * Set the blind carbon copy (BCC) recipient email address(es).
     *
     * @param string|array $email The BCC recipient email address(es).
     * @return self
     */
    public function setBcc($email);

    /**
     * Add a BCC recipient email address.
     *
     * @param mixed $email The BCC recipient email address to add.
     * @return self
     */
    public function addBcc($email);

    /**
     * Get the BCC recipient's email address.
     *
     * @return string[]
     */
    public function bcc();

    /**
     * Set the sender's email address.
     *
     * @param  string|array $email An email address.
     * @return self
     */
    public function setFrom($email);

    /**
     * Get the sender's email address.
     *
     * @return string
     */
    public function from();

    /**
     * Set email address to reply to the message.
     *
     * @param  mixed $email The sender's "Reply-To" email address.
     * @return self
     */
    public function setReplyTo($email);

    /**
     * Get email address to reply to the message.
     *
     * @return string
     */
    public function replyTo();

    /**
     * Set the email subject.
     *
     * @param  string $subject The email subject.
     * @return self
     */
    public function setSubject($subject);

    /**
     * Get the email subject.
     *
     * @return string The emails' subject.
     */
    public function subject();

    /**
     * Set the email's HTML message body.
     *
     * @param  string $body The HTML message body.
     * @return self
     */
    public function setMessageHtml($body);

    /**
     * Get the email's HTML message body.
     *
     * @return string
     */
    public function messageHtml();

    /**
     * Set the email's plain-text message body.
     *
     * @param string $body The message's text body.
     * @return self
     */
    public function setMessageTxt($body);

    /**
     * Get the email's plain-text message body.
     *
     * @return string
     */
    public function messageTxt();

    /**
     * Set the email's attachments.
     *
     * @param  array $attachments The file attachments.
     * @return self
     */
    public function setAttachments(array $attachments);

    /**
     * Add an attachment to the email.
     *
     * @param  mixed $attachment A single file attachment.
     * @return self
     */
    public function addAttachment($attachment);

    /**
     * Get the email's attachments.
     *
     * @return array
     */
    public function attachments();

    /**
     * Set the template data for the view.
     *
     * @param array $data The template data.
     * @return Email Chainable
     */
    public function setTemplateData(array $data);

    /**
     * Get the template data for the view.
     *
     * @return array
     */
    public function templateData();

    /**
     * Enable or disable logging for this particular email.
     *
     * @param  boolean $log The log flag.
     * @return self
     */
    public function setLogEnabled($log);

    /**
     * Determine if logging is enabled for this particular email.
     *
     * @return boolean
     */
    public function logEnabled();

    /**
     * Enable or disable tracking for this particular email.
     *
     * @param boolean $track The track flag.
     * @return self
     */
    public function setTrackEnabled($track);

    /**
     * Determine if tracking is enabled for this particular email.
     *
     * @return boolean
     */
    public function trackEnabled();

    /**
     * Set the email's data.
     *
     * @param array $data The data to set.
     * @return Email Chainable
     */
    public function setData(array $data);

    /**
     * Send the email to all recipients.
     *
     * @return boolean Success / Failure.
     */
    public function send();

    /**
     * Enqueue the email for each recipient.
     *
     * @param mixed $ts A date/time to initiate the queue processing.
     * @return boolean Success / Failure.
     */
    public function queue($ts = null);
}
