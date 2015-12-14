[![Build Status](https://travis-ci.org/ublaboo/mailing.svg?branch=master)](https://travis-ci.org/ublaboo/mailing)
[![Latest Stable Version](https://poser.pugx.org/ublaboo/mailing/v/stable)](https://packagist.org/packages/ublaboo/mailing)
[![License](https://poser.pugx.org/ublaboo/mailing/license)](https://packagist.org/packages/ublaboo/mailing)
[![Total Downloads](https://poser.pugx.org/ublaboo/mailing/downloads)](https://packagist.org/packages/ublaboo/mailing)

Mailing
=======

## Configuration

Start with registering extension in `config.neon`:

    extension:
        mailing: Ublaboo\Mailing\DI\MailingExtension

Now there are several config options.

    mailing:
        do: both # log|send|both
        log_diretory: '%appDir%/../log/mails' # this is default option
        mail_images_base_path: %wwwDir% # this is default option
        mails: []

Let's discuss each of these options:

##### do

This option may have one of three choices.
- `log` means all mails will be just stored on local disk in log directory. All mails are saved in `.eml` format (with possible images and attachments).
- `send` will only send all mails, but not log.
- `both` will do both.

##### log_directory

That one is pretty obvious. Directory, where mail files (`eml`) will be stored.

##### mail_images_base_path

This is the path, where `Nette\Mail\Message` will look in and try to find all images that can be embed in mail.

##### mails

This array filled with your parameters (probably mail addresses - like recipients, senders, etc) will be avalible in instance of each your mail class (read more) so you can easily set and recipient, bcc, cc or sender for particular mail.

E.g.:

    mailing:
        mails: [
            default_sender: foo@bar.baz
        ]

## Usage

##### Create mail class

Once you have registered mailing extension, you can create new mail class:

```php
namespace App;

use Nette,
	Ublaboo\Mailing\Mail,
	Ublaboo\Mailing\IComposableMail;

class ContactMail extends Mail implements IComposableMail
{

	/**
	 * There you will always have your mail addresses from configuration file
	 * @var array
	 */
	protected $mails;


	public function compose(Nette\Mail\Message $message, $params = NULL)
	{
		$message->setFrom($this->mails['default_sender']);
		$message->addTo($params['recipient']);
	}

}
```

##### Now the mail itself

```php
use Nette,
    Ublaboo;

class HomepagePresenter extends Nette\Application\UI\Presenter
{

    /**
     * @var Ublaboo\Mailing\MailFactory
     * @inject
     */
    public $mailFactory;


    public function actionDefault()
    {
        $params = ['recipient' => 'hello@hello.hello'];
        $this->mailFactory->createByType('App\Mailing\ContactMail', $params);
    }

}
```

##### You see the `$params` variable?

That variable is passed to the compose method of you mail class. And also is the array expanded and passed (exapanded) into mail template. So example. Say you pass these parameters to `$mailFactory`:

```php
$params = [
    'recipient' => 'john@doe.example',
    'name' => 'John Doe'
];
```

Than in your template, you will always have these parameters avalible:

```html
<!DOCTYPE html>
<html>
<head>
	<title>Contact mail</title>
</head>
<body>
    Helo, {$name}
    <br>
    Your email is: {$recipient}
</body>
</html>
```

##### mail template

Now, there is some convention in directory structure and you should use it as well. Doesn't metter where you put your mails, but the `Mail` class (which your mails will inherit from) will look for template latte files in `<same_directory_as_your_mails_are>/templates`. The name of template has to be in camel_case. E.g.:

    app/
        Mailing/
            ContactMail.php
            templates/
                contact_mail.latte

But that is only a recommendation. You can whenever change your template file path by `Mail::setTemplateFile()`. E.g.:

**In your mail class:**

```php
# ...

public function compose(Nette\Mail\Message $message, $params = NULL)
{
	# ...
	
	$this->setTemplateFile(__DIR__ . '/templates/contact_mail.latte');
}

**From outside**

$mail = $mailFactory->createByType('App\Mailing\ContactMail', ['recipient' => 'hello@hello.hello']);
$mail->setTemplateFile('super_awesome_template.latte');

# ...
```

##### Aaaaaand .... send it! - complete example in `Presenter`:

```php
use Nette,
    Ublaboo;

class HomepagePresenter extends Nette\Application\UI\Presenter
{

    /**
     * @var Ublaboo\Mailing\MailFactory
     * @inject
     */
    public $mailFactory;


    public function actionDefault()
    {
        $params = ['recipient' => 'hello@hello.hello'];
        $this->mailFactory->createByType('App\Mailing\ContactMail', $params)->send();
    }

}
```

##### Cool.

And - if you set the config option `do` to `log` od `both`, than you can look inside your log directory for generated `.eml` file. Easy..
