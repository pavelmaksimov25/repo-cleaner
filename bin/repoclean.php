<?php

require_once dirname(__DIR__).'/vendor/autoload.php';

use Github\AuthMethod;
use RepoCleaner\Application;

$token = getenv('GITHUB_TOKEN');
if (!$token) {
    throw new \RuntimeException('GITHUB_TOKEN env variable is missing');
}

$client = new \Github\Client();
$client->authenticate($token, null, AuthMethod::ACCESS_TOKEN);
$app = new Application($client);
$app->run($argv[2], $argv[1], explode(',', $argv[3] ?? 'master,main,rc,internal'));
