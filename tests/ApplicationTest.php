<?php

use Github\Api\PullRequest;
use Github\Client;
use RepoCleaner\Application;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    public function testRunSkipsWhitelistedBranches(): void
    {
        // Create a mock for the PullRequest API
        $prData = [
            ['number' => 1, 'head' => ['ref' => 'upgrade/branch'], 'user' => ['login' => 'spryker-bot']],
        ];

        $repositoryName = 'repoName';
        $repositoryOwner = 'repoOwner';

        $pullRequestApiMock = $this->createMock(PullRequest::class);
        $pullRequestApiMock->expects($this->once())
            ->method('all')
            ->with($repositoryOwner, $repositoryName, ['state' => 'open'])
            ->willReturn($prData);
        $pullRequestApiMock->expects($this->once())
            ->method('update')
            ->willReturn(null);

        $referencesMock = $this->createMock(\Github\Api\GitData\References::class);
        $referencesMock->expects($this->once())
            ->method('remove')
            ->with($repositoryOwner, $repositoryName, 'heads/upgrade/branch');

        $gitDataApiMock = $this->createMock(\Github\Api\GitData::class);

        $gitDataApiMock->expects($this->once())
            ->method('references')
            ->willReturn($referencesMock);

        // Create a mock for the Client class and configure it to return the PullRequest API mock
        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->exactly(3))
            ->method('api')
            ->willReturnCallback(function (string $apiName) use ($pullRequestApiMock, $gitDataApiMock) {
                return match ($apiName) {
                    'pull_request' => $pullRequestApiMock,
                    'git' => $gitDataApiMock,
                };
            });

        // Create an instance of the Application class with the mock Client
        $application = new Application($clientMock);

        // Capture the output of the run method
        ob_start();
        $application->run($repositoryName, $repositoryOwner, ['master']);
        $output = ob_get_clean();

        $this->assertSame('', $output);
    }

    public function testRunSkipsWhitelistedAndNonUpgraderBranches(): void
    {
        $prData = [
            ['number' => 1, 'head' => ['ref' => 'master'], 'user' => ['login' => 'other-user']],
            ['number' => 2, 'head' => ['ref' => 'upgrade/branch'], 'user' => ['login' => 'spryker-bot']],
        ];

        $repositoryName = 'repoName';
        $repositoryOwner = 'repoOwner';

        $pullRequestApiMock = $this->createMock(PullRequest::class);
        $pullRequestApiMock->expects($this->once())
            ->method('all')
            ->with($repositoryOwner, $repositoryName, ['state' => 'open'])
            ->willReturn($prData);
        $pullRequestApiMock->expects($this->once())
            ->method('update')
            ->willReturn(null);

        $referencesMock = $this->createMock(\Github\Api\GitData\References::class);
        $referencesMock->expects($this->once())
            ->method('remove')
            ->with($repositoryOwner, $repositoryName, 'heads/upgrade/branch');

        $gitDataApiMock = $this->createMock(\Github\Api\GitData::class);

        $gitDataApiMock->expects($this->once())
            ->method('references')
            ->willReturn($referencesMock);

        // Create a mock for the Client class and configure it to return the PullRequest API mock
        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->exactly(3))
            ->method('api')
            ->willReturnCallback(function (string $apiName) use ($pullRequestApiMock, $gitDataApiMock) {
                return match ($apiName) {
                    'pull_request' => $pullRequestApiMock,
                    'git' => $gitDataApiMock,
                };
            });

        $application = new Application($clientMock);

        ob_start();
        $application->run($repositoryName, $repositoryOwner, ['master']);
        $output = ob_get_clean();

        $this->assertSame('master is whitelisted, skipping...' . PHP_EOL, $output);
    }
}






