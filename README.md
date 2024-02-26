[![Build Status](https://travis-ci.org/ublaboo/mailing.svg?branch=master)](https://travis-ci.org/ublaboo/mailing)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ublaboo/mailing/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ublaboo/mailing/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/ublaboo/mailing/v/stable)](https://packagist.org/packages/ublaboo/mailing)
[![License](https://poser.pugx.org/ublaboo/mailing/license)](https://packagist.org/packages/ublaboo/mailing)
[![Total Downloads](https://poser.pugx.org/ublaboo/mailing/downloads)](https://packagist.org/packages/ublaboo/mailing)
[![Gitter](https://img.shields.io/gitter/room/nwjs/nw.js.svg)](https://gitter.im/ublaboo/help)

# Mailing

Extension for Nette Framework: Easy & object-oriented way of sending & logging mails

Mailing extension lets you send/log emails in the object oriented world.

## Overview

### Downloading Mailing

Mailing is available through composer package ublaboo/mailing:

```
composer require ublaboo/mailing
```

### MailFactory

MailFactory gives you a way to create your mails instances. In the background it sets some parameters, decides whether to log and email or not, tries to find mail template etc.

### Mail

Mail is the base class you will extend in each of your email cases (classes). In your particular mail class you will set email senders/recipients/cc/.., basepath (if you want to send inline images), attach files, etc. There are also available config parameters (if you have put them in there through config.neon).

You will send it via Mail::send() method.

#### Create mail class

Once you have registered mailing extension, you can create new mail class and then get MailFactory from DIC to send it:

```php
namespace App\Mailing;

use Ublaboo\Mailing\IMessageData;

class ContactMailData implements IMessageData
{

	public function __construct(
		public readonly string $recipient,
	) 
	{
	}

}
```

```php
namespace App\Mailing;

use InvalidArgumentException;
use Nette\Mail\Message;
use Ublaboo\Mailing\Mail;
use Ublaboo\Mailing\IComposableMail;
use Ublaboo\Mailing\IMessageData;

class ContactMail extends Mail implements IComposableMail
{

	public function compose(Message $message, ?IMessageData $mailData): void
	{
		if (!$mailData instanceof ContactMailData) {
			throw new InvalidArgumentException();
		}
	
		$message->setFrom($this->mailAddresses['defaultSender']);
		$message->addTo($mailData->recipient);
	}

}
```

```php
namespace App\Presenters;

use App\Mailing\ContactMail;
use App\Mailing\ContactMailData;
use Nette\Application\UI\Presenter;
use Nette\DI\Attributes\Inject;
use Ublaboo\Mailing\MailFactory;

class HomepagePresenter extends Presenter
{

	#[Inject]
	public MailFactory $mailFactory;

	public function actionDefault(): void
	{
		$mail = $this->mailFactory->createByType(
			ContactMail::class, 
			new ContactMailData(
				recipient: 'hello@hello.hello'
			),
		);
		
		$mail->send();
	}

}
```

Example mail template:

```html
<!DOCTYPE html>
	<html>
	<head>
		<title>Contact mail</title>
	</head>
	<body>
		Helo, {$mailData->name}
		<br>
		Your email is: {$mailData->recipient}
	</body>
</html>
```

### Mail templates

Now, there is some convention in directory structure and you should stick to it. It doesn't matter where you put your mails, but the Mail class (which your mails will inherit from) will look for template latte files in `<same_directory_as_your_mails_are</templates`. The name of particular template has to be in camel_case naming convention. E.g.:

```
app/
	Mailing/
		ContactMail.php
		templates/
			ContactMail.latte
```

But that is only a recommendation. You can always change your template file path by `Mail::setTemplateFile()`. Eg:

```php
# ...

public function compose(Message $message, ?IMessageData $mailData): void
{
	# ...
	
	$this->setTemplateFile(__DIR__ . '/templates/ContactMail.latte');
}
```

Or from the outside:

```php
# ...

$mail = $mailFactory->createByType(ContactMail::class, new ContactMailData(recipient: 'hello@hello.hello']));
$mail->setTemplateFile('super_awesome_template.latte');
```

#### No templates

Of course you don't have to send mails with templates, you can just use plaintext mail body. You would do that probably in your mail class: 

```php
# ...

public function compose(Message $message, ?IMessageData $mailData): void
{
	# ...
	
	$message->setBody('Hello');
}
```

## Configuration

There are some configuration options available like whether to log (or send, or both), where to log, where to find inline images from, etc

Start with registering extension in config.neon:

```
extensions:
	mailing: Ublaboo\Mailing\DI\MailingExtension
```

There are several config options:

```
mailing:
	do: both # log|send|both
	logDirectory: '%appDir%/../log/mails' # this is default option
	mailImagesBasePath: %wwwDir% # this is default option
	mails: []
```

Let's discuss each of these options:

**do**

In this option you may choose between these three directives:

- **log** means all mails will be just stored on local disk in log directory. All mails are saved in .eml format (with possible images and attachments)
- **send** will only send all mails, but not log
- **both** will do both

**logDirectory**

That one is pretty obvious. Directory, where mail files (`.eml`) will be stored.

**mailImagesBasePath**

This is the path, where `Nette\Mail\Message` will look for all images that can be inline embedded in mail.

**mails**

This array filled with your parameters (probably mail addresses - like recipients, senders, etc) will be available in instance of each of your mail class (read more) so that you can easily set sender and recipient, bcc, cc or whatever you need for particular mail.

E.g.:

```
mailing:
	mails: [
		defaultSender: foo@bar.baz
	]
```

## Log

By default, MailLogger is logging all sent Mails in log directory in format <year>/<month>/<day>/<mail_name.eml>. As you can see, the .eml extension is used to easily open an email file in all desktop clients.
