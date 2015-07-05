<?php

require 'vendor/autoload.php';
include 'templates/mailgun.template.php';

define('CONFIG_FILE', '/var/opt/jekyll-discuss/config');

// Read config file
$config = parse_ini_file(CONFIG_FILE);

if (!$config) {
    die('Config file not found');
}

// Start Slim
$app = new \Slim\Slim();

$app->post('/comments', function () use ($app) {
    $data = array_map('trim', $app->request()->post());

    // Checking for the honey pot
    if ((isset($data['company'])) && (!empty($data['company']))) {
        return;
    }

    // Checking for mandatory fields
    if ((!isset($data['name']) || empty($data['name'])) ||
        (!isset($data['email']) || empty($data['email'])) ||
        (!isset($data['message']) || empty($data['message'])) ||
        (!isset($data['post']) || empty($data['post'])))
    {
        echo('Mandatory fields are missing.');
        return;
    }

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

    // Send Mailgun notification
    $mailgun = new Mailgun\Mailgun($config['MAILGUN_KEY']);
    $message = mailgunMessage($data['name'], $data['post']);

    $mailgun->sendMessage($config['MAILGUN_DOMAIN'], array(
        'from'    => $config['MAILGUN_FROM'], 
        'to'      => $config['MAILGUN_TO'], 
        'subject' => $message['subject'], 
        'text'    => $message['message']
    ));
});

$app->run();

?>