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

    // We're looking for the honey pot field and testing mandatory fields
    if ((isset($data['company'])) ||
        (!isset($data['name'])) || 
        (!isset($data['email'])) || 
        (!isset($data['message'])) || 
        (!isset($data['post']))) 
    {
        echo(0);
        return;
    }

    $emailHash = md5(trim(strtolower($data['email'])));

    $shellCommand = './new-comment.sh';
    $shellCommand .= ' --name ' . escapeshellarg($data['name']);
    $shellCommand .= ' --hash \'' . $emailHash . '\'';
    $shellCommand .= ' --post ' . escapeshellarg($data['post']);
    $shellCommand .= ' --message ' . escapeshellarg($data['message']);

    if (isset($data['url'])) {
        $shellCommand .= ' --url ' . escapeshellarg($data['url']);
    }
    
    $output = exec($shellCommand);
});

$app->run();

?>