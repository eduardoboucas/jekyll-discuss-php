<?php

function buildNotification($name, $post, $link) {
	list($name) = explode(' ', $author);

	$message = 'Hi ' . $name . ',<br><br>';
	$message .= 'Someone else commented on the post \"' . $post . '\" that you subscribed to.<br>';
	$message .= '<a href="">Click here</a> to go to the post and see what\'s up.<br><br>';
	$message .= 'Best,<br>Eduardo';

	return $message;
}

function isUserSubscribed($emailAddress, $subscriptions) {
	foreach ($subscriptions as $subscription) {
		if ($subscription['email'] == $emailAddress) {
			return true;
		}
	}

	return false;
}

?>