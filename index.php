<?php
require 'vendor/autoload.php';
require 'lib/readConfig.php';

// Read config file
$config = readConfig('config');

if ($config === FALSE) {
    die('Config file not found');
}

// Start Slim
$app = new \Slim\Slim();

$app->post('/comments', function () use ($app) {
    $data = $app->request()->post();

    // Checking for the honey pot
    if ((isset($data['company'])) && (!empty($data['company']))) {
        return;
    }

    // Checking for mandatory fields
    if ((!isset($data['name'])) ||
        (!isset($data['email'])) ||
        (!isset($data['message'])) ||
        (!isset($data['post'])))
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

    $shellCommand = './new-comment.sh';
    $shellCommand .= ' --name ' . escapeshellarg($data['name']);
    $shellCommand .= ' --date ' . escapeshellarg($date);
    $shellCommand .= ' --hash \'' . $emailHash . '\'';
    $shellCommand .= ' --post ' . escapeshellarg($data['post']);
    $shellCommand .= ' --message ' . escapeshellarg($message);

    if (isset($data['url'])) {
        $shellCommand .= ' --url ' . escapeshellarg($data['url']);
    }

    exec($shellCommand, $output);

    $response['hash'] = $emailHash;
    $response['date'] = $date;
    $response['message'] = $message;

    echo(json_encode($response));
});

$app->run();

?>