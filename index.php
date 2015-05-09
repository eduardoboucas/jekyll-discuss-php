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

    // Create email hash
    $emailHash = md5(trim(strtolower($data['email'])));

    // Parse markdown
    $Parsedown = new Parsedown();
    $message = escapeshellarg($Parsedown->text($data['message']));

    $shellCommand = './new-comment.sh';
    $shellCommand .= ' --name ' . escapeshellarg($data['name']);
    $shellCommand .= ' --hash \'' . $emailHash . '\'';
    $shellCommand .= ' --post ' . escapeshellarg($data['post']);
    $shellCommand .= ' --message ' . $message;

    if (isset($data['url'])) {
        $shellCommand .= ' --url ' . escapeshellarg($data['url']);
    }

    exec($shellCommand, $output);

    $response['hash'] = $emailHash;
    $response['message'] = $message;

    echo(json_encode($response));
});

$app->run();

?>