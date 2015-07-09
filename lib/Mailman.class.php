<?php

class Mailman {
	public function __construct($key, $domain, $from) {
		$this->domain = $domain;
		$this->from = $from;
		$this->mailgun = new Mailgun\Mailgun($key);
	}

	public function send($to, $subject, $message, $content) {
		foreach ($content as $placeholder => $value) {
			$message = str_replace($placeholder, $value, $message);
		}

		return $this->mailgun->sendMessage($this->domain, array(
		    'from'    => $this->from,
		    'to'      => $to,
		    'subject' => $subject,
		    'html'    => $message
		));
	}
}

?>