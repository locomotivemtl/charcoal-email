<?php

namespace Charcoal\Email\Service;

use Exception;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

// From `phpmailer/phpmailer`
use PHPMailer\PHPMailer\PHPMailer;

use Charcoal\Factory\FactoryInterface;

use Charcoal\Email\Email;
use Charcoal\Email\Config\SmtpConfig;
use Charcoal\Email\Object\EmailQueueItem;
use Charcoal\Email\Service\EmailParser;
use Charcoal\Email\Service\EmailTracker;

/**
 *
 */
class EmailSender implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var string
     */
    private $defaultFrom = 'charcoal@example.com';

    /**
     * @var SmtpConfig|null
     */
    private $smtpConfig;

    /**
     * @var FactoryInterface
     */
    private $queueItemFactory;

    /**
     * @var EmailParser
     */
    private $parser;

    /**
     * @var EmailTracker
     */
    private $tracker;

    /**
     * @param array $data Sender dependencies.
     */
    public function __construct(array $data)
    {
        $this->setLogger($data['logger']);

        $this->setQueueItemFactory($data['queueItemFactory']);

        $this->setParser($data['parser']);
        $this->setTracker($data['tracker']);

        if (isset($data['smtpConfig'])) {
            $this->setSmtpConfig($data['smtpConfig']);
        }

        if (isset($data['defaultFrom'])) {
            $this->setDefaultFrom($data['defaultFrom']);
        }
    }

    /**
     * @param Email $email The Email to send.
     * @return boolean
     */
    public function send(Email $email)
    {
        try {
            // A new instance must be created for every email we send...
            $mailer = $this->createPhpMailer();

            // Setting from (sender) field.
            $from = $email->from();
            if ($from === '') {
                $from = $this->defaultFrom;
            }
            $fromArr = $this->parser->toArray($from);
            $mailer->setFrom($fromArr['email'], $fromArr['name']);

            // Setting reply-to field, if required.
            $replyTo = $email->replyTo();
            if ($replyTo) {
                $replyArr = $this->parser->toArray($replyTo);
                $mailer->addReplyTo($replyArr['email'], $replyArr['name']);
            }

            // Setting to (recipients) field(s).
            $to = $email->to();
            foreach ($to as $recipient) {
                $toArr = $this->parser->toArray($recipient);
                $mailer->addAddress($toArr['email'], $toArr['name']);
            }

            // Setting cc (carbon-copy) field(s).
            $cc = $email->cc();
            foreach ($cc as $ccRecipient) {
                $ccArr = $this->parser->toArray($ccRecipient);
                $mailer->addCC($ccArr['email'], $ccArr['name']);
            }

            // Setting bcc (black-carbon-copy) field(s).
            $bcc = $email->bcc();
            foreach ($bcc as $bccRecipient) {
                $bccArr = $this->parser->toArray($bccRecipient);
                $mailer->addBCC($bccArr['email'], $bccArr['name']);
            }

            // Setting attachment(s), if required.
            $attachments = $email->attachments();
            foreach ($attachments as $att) {
                $mailer->addAttachment($att);
            }

            $mailer->isHTML(true);

            $mailer->Subject = $email->subject();
            $mailer->Body    = $email->messageHtml();
            $mailer->AltBody = $email->messageTxt();

            $ret = $mailer->send();

            $this->postSend($ret, $email, $mailer);

            return $ret;
        } catch (Exception $e) {
            $this->logger->error(
                sprintf('Error sending email: %s', $e->getMessage())
            );

            return false;
        }
    }

    /**
     * @param Email                          $email The email to queue.
     * @param string|\DateTimeInterface|null $ts    The time at which the queue item should be processed (the email be sent).
     * @return boolean
     */
    public function queue(Email $email, $ts = null)
    {
        $recipients = $email->to();
        $author     = $email->from();
        if ($author === '') {
            $author = $this->defaultFrom;
        }
        $subject    = $email->subject();
        $messageHtml = $email->messageHtml();
        $messageTxt  = $email->messageTxt();
        $campaign   = $email->campaign();
        $queueId    = $email->queueId();

        foreach ($recipients as $to) {
            $queueItem = $this->queueItemFactory->create(EmailQueueItem::class);

            $queueItem->setTo($to);
            $queueItem->setFrom($author);
            $queueItem->setSubject($subject);

            $queueItem->setMessageHtml($messageHtml);
            $queueItem->setMessageTxt($messageTxt);

            $queueItem->setCampaign($campaign);

            $queueItem->setProcessingDate($ts);
            $queueItem->setQueueId($queueId);

            $queueItem->save();
        }

        return true;
    }


    /**
     * @param boolean   $ret    The returned status.
     * @param Email     $email  The Email that was sent.
     * @param PHPMailer $mailer The mailer that was used.
     * @return void
     */
    private function postSend($ret, Email $email, PHPMailer $mailer)
    {
        if ($ret === false) {
            $this->logger->error(
                sprintf('Could not send email to "%s"', $email->to())
            );
            return;
        }

        $this->logger->debug(
            sprintf('Email "%s" sent successfully.', $email->subject()),
            $email->to()
        );

        $this->tracker->logSent($email, $mailer);
    }

    /**
     * @return PHPMailer
     */
    private function createPhpMailer()
    {
        $mailer = new PhpMailer(true);
        $mailer->CharSet = 'UTF-8';

        if ($this->smtpConfig && $this->smtpConfig['hostname'] !== '') {
            $mailer->isSMTP();
            $mailer->Host = $this->smtpConfig['hostname'];
            $mailer->Port = $this->smtpConfig['port'];
            $mailer->SMTPAuth = $this->smtpConfig['auth'];
            $mailer->Username = $this->smtpConfig['username'];
            $mailer->Password = $this->smtpConfig['password'];
            $mailer->SMTPSecure = $this->smtpConfig['security'];
        }
        return $mailer;
    }

    /**
     * @param SmtpConfig $config SMTP Configuration.
     * @return void
     */
    private function setSmtpConfig(SmtpConfig $config)
    {
        $this->smtpConfig = $config;
    }

    /**
     * @param FactoryInterface $factory The Queue Item factory, to create email queue item objects.
     * @return void
     */
    private function setQueueItemFactory(FactoryInterface $factory)
    {
        $this->queueItemFactory = $factory;
    }


    /**
     * @param EmailParser $parser Email parser service / helper.
     * @return void
     */
    private function setParser(EmailParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param EmailTracker $tracker Email tracker service / helper.
     * @return void
     */
    private function setTracker(EmailTracker $tracker)
    {
        $this->tracker = $tracker;
    }

    /**
     * @param mixed $from The default from email.
     * @return void
     */
    private function setDefaultFrom($from)
    {
        $this->defaultFrom = $this->parser->parse($from);
    }
}
