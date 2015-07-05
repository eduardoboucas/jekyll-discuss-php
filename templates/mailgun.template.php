<?php

function mailgunMessage($name, $url) {
	$subject = 'New comment on Eduardo saying things';

	$message = 'Hi there,<br><br>';
	$message .= $name . ' just commented on <em>Eduardo saying things</em>. Click <a href="https://eduardoboucas.com' . $url . '">here</a> to see it.<br><br>';
	$message .= 'Best,<br>';
	$message .= 'You';

	return array('subject' => $subject, 'message' => $message);
}