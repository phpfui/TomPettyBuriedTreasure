<?php

namespace App\WWW;

class ContactUs extends \App\View\WWWBase implements \PHPFUI\Interfaces\NanoClass
	{
	public function home() : void
		{
		$title = 'Contact Us';
		$this->page->addHeader($title);
		$form = new \PHPFUI\Form($this->page);
		$submit = new \PHPFUI\Submit($title);

		if ($form->isMyCallback($submit))
			{
			\PHPFUI\Session::setFlash('post', $_POST);

			if (\strlen($_POST['name'] ?? '') < 10)
				{
				\PHPFUI\Session::setFlash('alert', 'Your name is not long enough');
				$this->page->redirect();

				return;
				}

			if (! \strlen($_POST['message'] ?? ''))
				{
				\PHPFUI\Session::setFlash('alert', 'You need to tell us something!');
				$this->page->redirect();

				return;
				}

			if (! \filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL))
				{
				\PHPFUI\Session::setFlash('alert', 'Your email address is not valid');
				$this->page->redirect();

				return;
				}

			if (($_POST['titleId'] ?? 0) != 1 || ($_POST['artistId'] ?? 0) != 171)
				{
				\PHPFUI\Session::setFlash('alert', 'You don\'t appear to be a Buried Treasure fan');
				$this->page->redirect();

				return;
				}
			$settings = new \App\Settings\Admin();

			if ($settings->slackWebhook)
				{
				$guzzle = new \GuzzleHttp\Client(['verify' => false, 'http_errors' => false]);
				$client = new \Maknz\Slack\Client($settings->slackWebhook, [], $guzzle);
				$client->send("{$_SERVER['SERVER_NAME']}\nBuried Treasure\nContact: {$_POST['name']} {$_POST['email']}\n{$_POST['message']}");
				\PHPFUI\Session::setFlash('success', 'Thanks for contacting us. We will try to get back to you shortly.');
				}
			else
				{
				\PHPFUI\Session::setFlash('alert', 'Sorry, we are not accepting submisssions at this time.');
				}

			$this->page->redirect();

			return;
			}
		$post = \PHPFUI\Session::getFlash('post');
		$name = new \PHPFUI\Input\Text('name', 'Your Name', $post['name'] ?? '');
		$name->setRequired();
		$form->add($name);
		$email = new \PHPFUI\Input\Email('email', 'Your email address', $post['email'] ?? '');
		$email->setRequired();
		$form->add($email);
		$message = new \PHPFUI\Input\TextArea('message', 'What can we help you with?', $post['message'] ?? '');
		$message->setRequired();
		$form->add($message);


		$fanBox = new \PHPFUI\FieldSet('Prove you are a Buried Treasure fan');
		$titlePicker = new \App\UI\Picker($this->page, 'title', 'What was the most played song on the Buried Treasure Show?', new \App\Record\Title());
		$fanBox->add($titlePicker->getEditControl());
		$artistPicker = new \App\UI\Picker($this->page, 'artist', 'What was Tom Petty\'s band\'s name?', new \App\Record\Artist());
		$fanBox->add($artistPicker->getEditControl());
		$form->add($fanBox);
		$form->add($submit);
		$this->page->addPageContent($form);
		}
	}
