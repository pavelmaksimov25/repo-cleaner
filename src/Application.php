<?php

namespace RepoCleaner;

use Github\Client;

class Application
{
    /**
     * @param Client $client Authenticated Github client
     */
    public function __construct(private Client $client)
    {
    }

    /**
     * @param string $repositoryName
     * @param string $repositoryOwner
     * @param array<string> $whitelistedBranches
     * @return void
     */
    public function run(
        string $repositoryName,
        string $repositoryOwner,
        array $whitelistedBranches = ['master', 'main', 'rc', 'internal'],
    ): void {
        $prs = $this->fetchOpenPrsInTheRepository($repositoryName, $repositoryOwner);
        foreach ($prs as $pr) {
            if (in_array($pr['base']['ref'], $whitelistedBranches, true)) {
                echo $pr['base']['ref'] . ' is whitelisted, skipping...' . PHP_EOL;
                continue;
            }

            $this->removePR($repositoryName, $repositoryOwner, $pr['number']);
            $this->removeBranch($repositoryName, $repositoryOwner, $pr['head']['ref']);
        }
    }

    /**
     * @param string $repositoryName
     * @param string $repositoryOwner
     * @return array<array>
     */
    private function fetchOpenPrsInTheRepository(
        string $repositoryName,
        string $repositoryOwner
    ): array {
        return $this->client->api('pull_request')->all($repositoryOwner, $repositoryName, ['state' => 'open']);
    }

    /**
     * @param string $repositoryName
     * @param string $repositoryOwner
     * @param int $prNumber
     * @return void
     */
    private function removePR(
        string $repositoryName,
        string $repositoryOwner,
        int $prNumber
    ): void {
        $this->client->api('pull_request')->update($repositoryOwner, $repositoryName, $prNumber, ['state' => 'closed']);
    }

    /**
     * @param string $repositoryName
     * @param string $repositoryOwner
     * @param string $branchName
     * @return void
     */
    private function removeBranch(
        string $repositoryName,
        string $repositoryOwner,
        string $branchName
    ): void {
        $this->client->api('git')->references()->remove($repositoryOwner, $repositoryName, 'heads/'.$branchName);
    }
}