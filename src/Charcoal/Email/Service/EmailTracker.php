<?php

namespace Charcoal\Email\Service;

use Charcoal\Factory\FactoryInterface;
use PHPMailer\PHPMailer\PHPMailer;

use Charcoal\Email\Email;
use Charcoal\Email\Object\EmailLog;

/**
 * Email tracker and logger service.
 */
class EmailTracker
{
    /**
     * @var FactoryInterface
     */
    private $logFactory;

    /**
     * @param array $data Tracker dependencies.
     */
    public function __construct(array $data)
    {
        $this->setLogFactory($data['logFactory']);
    }

    /**
     * @param Email     $email  The email object that was sent.
     * @param PHPMailer $mailer The mailer that was used.
     * @return void
     */
    public function logSent(Email $email, PHPMailer $mailer)
    {
        if ($email->logEnabled() === false) {
            // This email should not be logged.
            return;
        }

        $recipients = array_merge(
            $email->to(),
            $email->cc(),
            $email->bcc()
        );

        foreach ($recipients as $to) {
            $log = $this->logFactory->create(EmailLog::class);

            $log->setType('email');
            $log->setAction('send');

            $log->setRawResponse($mailer);

            $log->setMessageId($mailer->getLastMessageID());
            $log->setCampaign($email->campaign());

            $log->setSendDate('now');

            $log->setFrom($mailer->From);
            $log->setTo($to);
            $log->setSubject($email->subject());

            $log->save();
        }
    }

    /**
     * @param FactoryInterface $factory The log object factory.
     * @return void
     */
    private function setLogFactory(FactoryInterface $factory)
    {
        $this->logFactory = $factory;
    }
}
