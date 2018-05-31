Charcoal Email
==============

Sending emails (with _PHPMailer_) and logging and queue management.


# How to install

The preferred (and only supported) way of installing _charcoal-email_ is with **composer**:

```shell
★ composer require locomotivemtl/charcoal-email
```

## Dependencies

-   [`PHP 5.6+`](http://php.net)
-   [`phpmailer/phpmailer`](https://github.com/PHPMailer/PHPMailer)
-   [`locomotivemtl/charcoal-config`](https://github.com/locomotivemtl/charcoal-config)
-   [`locomotivemtl/charcoal-queue`](https://github.com/locomotivemtl/charcoal-queue)
-   [`locomotivemtl/charcoal-app`](https://github.com/locomotivemtl/charcoal-app)

## Optional dependencies

-   [`pimple/pimple`](http://pimple.sensiolabs.org/)
    -   Dependency injection Container (required for the [Service Provider](#service-provider))

> 👉 All optional depedencies are required for development. All other development dependencies, which are optional when using charcoal-email in a project, are described in the [Development](#development) section of this README file.

# Usage

A simple example:
```php
$email = $container['email'];
$email->setTo('recipient@example.com');
$email->setFrom('"Company inc." <company.inc@example.com>');
$email->setSubject("Email subject");
$email->setTemplateIdent('foo/email/simple-email');
$email->setTemplateData([
    'foo' => 'bar'
]);
$ret = $email->send();
```

Full example:

```php
$email = $container['email'];
$email->setData([
    'campaign'  => 'Campaign identifier'
    'to'    => [
        'recipient@example.com',
        '"Some guy" <someguy@example.com>',
        [
            'name'  => 'Other guy',
            'email' => 'otherguy@example.com'
        ]
    ],
    'bcc'   => 'shadow@example.com'
    'from'  => '"Company inc." <company.inc@example.com>',
    'replyTo' => [
        'name'  => 'Jack CEO',
        'email' => 'jack@example.com'
    ],
    'subject'       => $this->translator->trans('Email subject'),
    'templateIdent' => 'foo/email/default-email',
    'templateData'  => [
        'foo'   => 'bar'
    ],
    'attachments'   => [
        'foo/bar.pdf',
        'foo/baz.pdf'
    ]
]);
$email->send();

// Alternately, to send at a later date, use the queue system:
$email->queue('in 5 minutes');
```

# Email Config

The entire email system can be configured from the main app config, in the `email` config key.

```json
{
    "email": {
        "smtp": [
            "hostname": "smtp.example.com",
            "port": 25,
            "security": "tls",
            "username": "user@example.com",
            "password": "password",
        ],
        
        "defaultFrom": "webproject@example.com",
        "defaultReplyTo": "webproject@example.com",
        "defaultTrackEnabled": false,
        "defaultLogEnabled": true
    }
}

```

* Note that the `smtp` section is optional. By default, SMTP is disabled (local sendmail is used).

> It is recommended to use a single configuration file (ex: `config/email.json`) and include it in your main config.

# Service Provider

All email services can be quickly register to a (`pimple`) container with `\Charcoal\Email\ServiceProvider\EmailServiceProvider`.

**Provided services:**

| Service       | Type                | Description |
| ------------- | ------------------- | ----------- |
| **email**     | `Charcoal\Email\Email`        | An email object (factory). |
| **email/factory** | `Charcoal\Factory\FactoryInterface` | An email factory, to create email objects. |


Also available are the following helpers:

| Helper Service    | Type                | Description |
| ----------------- | ------------------- | ----------- |
| **email/config**  | `Charocoal\Email\Config\EmailConfig` | Email configuration.
| **email/config/smtp** | `Charocoal\Email\Config\SmtpConfig` | SMTP configuration.
| **email/view**    | `Charcoal\View\ViewInterface`   | The view object to render email templates (`$container['view']`).
| **email/parser** | `Charcoal\Email\Service\EmailParser` | Helper to parse emails as RFC-822 strings or name/email array. |
| **email/sender** | `Charcoal\Email\Service\EmailSender` | Service that queues or actually sends the email. |
| **email/tracker** | `Charcoal\Email\Service\EmailTracker` | Service that logs and tracks sent emails. |


> 👉 For charcoal projects, simply add this provider to your config to enable:
>
> ```json
> {
>   "service_providers": {
>       "charcoal/email/service-provider/email": {}
>   }
> }
> ```

## Service dependencies

For the _email_ service provider to work properly, the following services are expected to e registerd on the same container:

-   `config`
-   `view`
-   `model/factory`

# Logging

By default, all emails sent through the Charcoal Email system are logged in the database. This function is provided by `\Charcoal\Email\Service\EmailTracker` via the `\Charcoal\Email\Object\EmailLog` object.

To disable logging for a specific email:

```php
$email->setLogEnabled(false);
```

# Development

To install the development environment:

```shell
★ composer install --prefer-source
```

To run the scripts (phplint, phpcs and phpunit):

```shell
★ composer test
```

## Development dependencies

-   `phpunit/phpunit`
-   `squizlabs/php_codesniffer`
-   `satooshi/php-coveralls`

## Continuous Integration

| Service | Badge | Description |
| ------- | ----- | ----------- |
| [Travis](https://travis-ci.org/locomotivemtl/charcoal-email) | [![Build Status](https://travis-ci.org/locomotivemtl/charcoal-email.svg?branch=master)](https://travis-ci.org/locomotivemtl/charcoal-email) | Runs code sniff check and unit tests. Auto-generates API documentation. |
| [Scrutinizer](https://scrutinizer-ci.com/g/locomotivemtl/charcoal-email/) | [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/locomotivemtl/charcoal-email/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/locomotivemtl/charcoal-email/?branch=master) | Code quality checker. Also validates API documentation quality. |
| [Coveralls](https://coveralls.io/github/locomotivemtl/charcoal-email) | [![Coverage Status](https://coveralls.io/repos/github/locomotivemtl/charcoal-email/badge.svg?branch=master)](https://coveralls.io/github/locomotivemtl/charcoal-email?branch=master) | Unit Tests code coverage. |
| [Sensiolabs](https://insight.sensiolabs.com/projects/54058388-3b5d-47e3-8185-f001232d31f7) | [![SensioLabsInsight](https://insight.sensiolabs.com/projects/54058388-3b5d-47e3-8185-f001232d31f7/mini.png)](https://insight.sensiolabs.com/projects/54058388-3b5d-47e3-8185-f001232d31f7) | Another code quality checker, focused on PHP. |

## Coding Style

The Charcoal-Email module follows the Charcoal coding-style:

-   [_PSR-1_](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)
-   [_PSR-2_](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
-   [_PSR-4_](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md), autoloading is therefore provided by _Composer_.
-   [_phpDocumentor_](http://phpdoc.org/) comments.
-   Read the [phpcs.xml](phpcs.xml) file for all the details on code style.

> Coding style validation / enforcement can be performed with `composer phpcs`. An auto-fixer is also available with `composer phpcbf`.

## Authors

-   Mathieu Ducharme <mat@locomotive.ca>
-   Chauncey McAskill <chauncey@locomotive.ca>
-   Benjamin Roch <benjamin@locomotive.ca>
-   [Locomotive](https://locomotive.ca)

# License

**The MIT License (MIT)**

_Copyright © 2018 Locomotive inc._
> See [Authors](#authors).

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
