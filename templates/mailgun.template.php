<?php

function mailgunMessage($name, $url) {
	$subject = 'New comment on Eduardo saying things';

	$message = 'Hi,<br><br>';
	$message .= 'There\'s a new comment in Eduardo saying things, by ' . $name . '.<br><br>';
	$message .= 'Click <a href="' . $url . '">here</a> to see it.<br><br>';
	$message .= 'Best,<br>';
	$message .= '-- You';

	return array('subject' => $subject, 'message' => $message);
}