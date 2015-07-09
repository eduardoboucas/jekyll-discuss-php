<?php

require 'vendor/autoload.php';
require 'lib/Mailman.class.php';

define('CONFIG_FILE', '/var/opt/jekyll-discuss/config');

// Read config file
if (!$config = parse_ini_file(CONFIG_FILE)) {
    die('Config file not found');
}

// Start Slim
$app = new \Slim\Slim();

$app->get('/unsubscribe/:post/:subscriber', function ($post, $subscriber) use ($config) {
    $subscriptions = Flintstone\Flintstone::load($post, array('dir' => $config['SUBSCRIPTIONS_DATABASE']));
    $removed = false;

    if (file_exists($config['SUBSCRIPTIONS_DATABASE'] . '/' . $post . '.dat')) {
        if ($subscriptions->get($subscriber)) {
            $subscriptions->delete($subscriber);
            $removed = true;
        }
    }

    if ($removed) {
        echo('Subscription removed!');
    } else {
        echo('Oops, something went wrong here.');
    }
});

$app->post('/comments', function () use ($app, $config) {
    $data = array_map('trim', $app->request()->post());

    // Checking for the honey pot
    if ((isset($data['company'])) && (!empty($data['company']))) {
        return;
    }

    // Checking for mandatory fields
    if ((!isset($data['name']) || empty($data['name'])) ||
        (!isset($data['email']) || empty($data['email'])) ||
        (!isset($data['message']) || empty($data['message'])) ||
        (!isset($data['post-url']) || empty($data['post-url'])) ||
        (!isset($data['post']) || empty($data['post'])))
    {
        echo('Mandatory fields are missing.');
        return;
    }

    // Grab current date
    $date = date('M d, Y, g:i a');

    // Create email hash
    $emailHash = md5(trim(strtolower($data['email'])));

    // Parse markdown
    $message = Parsedown::instance()
                ->setMarkupEscaped(true)
                ->setUrlsLinked(false)
                ->text($data['message']);

    // Prepare shell command
    $shellCommand = './new-comment.sh';
    $shellCommand .= ' --config \'' . CONFIG_FILE . '\'';
    $shellCommand .= ' --name ' . escapeshellarg($data['name']);
    $shellCommand .= ' --date ' . escapeshellarg($date);
    $shellCommand .= ' --hash \'' . $emailHash . '\'';
    $shellCommand .= ' --post ' . escapeshellarg($data['post']);
    $shellCommand .= ' --message ' . escapeshellarg($message);

    if (isset($data['url'])) {
        $shellCommand .= ' --url ' . escapeshellarg($data['url']);
    }

    // Run shell command
    exec($shellCommand, $output);

    // Prepare response
    $response['hash'] = $emailHash;
    $response['date'] = $date;
    $response['message'] = $message;

    // Send response
    echo(json_encode($response));

    // Prepare email notifications
    $mailer = new Mailman($config['MAILGUN_KEY'], $config['MAILGUN_DOMAIN'], $config['MAILGUN_FROM']);

    // Read subscription data
    $subscriptions = Flintstone\Flintstone::load($data['post'], array('dir' => $config['SUBSCRIPTIONS_DATABASE']));
    $subscribers = $subscriptions->getKeys();

    // Emailing subscribers (and not the author of the comment)
    foreach ($subscribers as $subscriber) {
        $subscription = $subscriptions->get($subscriber);

        if ($subscription['email'] != $data['email']) {
            $mailer->send($subscription['email'], 'New comment', file_get_contents('templates/new-comment.html'), array(
                '{{ subscriber }}' => $subscription['name'],
                '{{ commenter }}' => $data['name'],
                '{{ link }}' => $data['post-url'],
                '{{ unsubscribe }}' => 'https://aws.bouc.as/jekyll-discuss/unsubscribe/' . $data['post'] .  '/' . $subscriber
            ));            
        }
    }

    // Emailing myself
    $mailer->send($subscription['email'], 'New comment', file_get_contents('templates/admin-new-comment.html'), array(
        '{{ commenter }}' => $data['name'],
        '{{ link }}' => $data['post-url']
    ));    

    // Adding a new subscription if necessary
    if (isset($data['subscribe']) && ($data['subscribe'] == 'subscribe')) {
        $subscriptions->set(md5($emailHash . $date), array(
            'email' => $data['email'], 
            'name' => $data['name']
        ));
    }
});

$app->run();

?>