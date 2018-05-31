<?php

namespace Charcoal\Email\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

use Charcoal\Factory\GenericFactory;

use Charcoal\Email\Email;
use Charcoal\Email\EmailInterface;
use Charcoal\Email\Config\EmailConfig;
use Charcoal\Email\Config\SmtpConfig;
use Charcoal\Email\Service\EmailParser;
use Charcoal\Email\Service\EmailSender;
use Charcoal\Email\Service\EmailTracker;
use Charcoal\Email\Template\GenericEmailTemplate;

/**
 * Email Service Provider
 *
 * Can provide the following services to a Pimple container:
 *
 * - `email/config`
 * - `email/view`
 * - `email/factory`
 * - `email` (_factory_)
 */
class EmailServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container A pimple container instance.
     * @return void
     */
    public function register(Container $container)
    {
        $this->registerConfig($container);
        $this->registerParser($container);
        $this->registerSender($container);
        $this->registerTracker($container);

        /**
         * @param Container $container Pimple DI container.
         * @return \Charcoal\View\ViewInterface
         */
        $container['email/view'] = function (Container $container) {
            return $container['view'];
        };

        /**
         * @param Container $container Pimple DI container.
         * @return \Charcoal\Factory\FactoryInterface
         */
        $container['email/template/factory'] = function(Container $container) {
            return new GenericFactory([
                'resolver_options' => [
                    'suffix' => 'Template'
                ],
                'default_class' => GenericEmailTemplate::class,
                'arguments' => [[
                    'container' => $container,
                    'logger'    => $container['logger']
                ]]
            ]);
        };

        /**
         * @param Container $container Pimple DI container.
         * @return \Charcoal\Factory\FactoryInterface
         */
        $container['email/factory'] = function(Container $container) {
            return new GenericFactory([
                'map' => [
                    'email' => Email::class
                ],
                'base_class' => EmailInterface::class,
                'default_class' => Email::class,
                'arguments' => [[
                    'config'    => $container['email/config'],
                    'view'      => $container['email/view'],
                    'templateFactory' => $container['template/factory'],
                    'parser'    => $container['email/parser'],
                    'sender'    => $container['email/sender'],
                    'tracker'   => $container['email/tracker']

                ]]
            ]);
        };

        /**
         * @param Container $container Pimple DI container.
         * @return \Charcoal\Email\EmailInterface
         */
        $container['email'] = $container->factory(function (Container $container) {
            return $container['email/factory']->create('email');
        });
    }

    /**
     * @param Container $container A pimple container instance.
     * @return void
     */
    private function registerConfig(Container $container)
    {
        /**
         * @param Container $container Pimple DI container.
         * @return \Charcoal\Email\Config\EmailConfig
         */
        $container['email/config'] = function (Container $container) {
            $appConfig = isset($container['config']) ? $container['config'] : [];
            $emailConfig = new EmailConfig($appConfig['email']);
            return $emailConfig;
        };

        /**
         * @param Container $container Pimple DI container.
         * @return \Charcoal\Email\Config\SmtpConfig
         */
        $container['email/config/smtp'] = function (Container $container) {
            $emailConfig = $container['email/config'];
            return new SmtpConfig($emailConfig['smtp']);
        };
    }

    /**
     * @param Container $container A pimple container instance.
     * @return void
     */
    private function registerParser(Container $container)
    {
        $container['email/parser'] = function () {
            return new EmailParser();
        };
    }

    /**
     * @param Container $container A pimple container instance.
     * @return void
     */
    private function registerSender(Container $container)
    {
        $container['email/sender'] = function(Container $container) {
            return new EmailSender([
                'logger'        => $container['logger'],
                'smtpConfig'    => $container['email/config/smtp'],
                'queueItemFactory' => $container['model/factory'],
                'parser'        => $container['email/parser'],
                'tracker'       => $container['email/tracker'],
                'defaultFrom'   => $container['email/config']['defaultFrom']
            ]);
        };
    }

    /**
     * @param Container $container A pimple container instance.
     * @return void
     */
    private function registerTracker(Container $container)
    {
        $container['email/tracker'] = function (Container $container) {
            return new EmailTracker([
                'logFactory' => $container['model/factory']
            ]);
        };
    }
}
