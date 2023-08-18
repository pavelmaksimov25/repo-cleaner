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
        foreach ($this->fetchOpenPrsInTheRepository($repositoryName, $repositoryOwner) as $pr) {
            if (in_array($pr['head']['ref'], $whitelistedBranches, true)) {
                echo $pr['head']['ref'] . ' is whitelisted, skipping...' . PHP_EOL;
                continue;
            }

            if (!$this->isPrCreatedByUpgrader($pr)) {
                echo $pr['head']['ref'] . ' is not created by the Upgrader, skipping...' . PHP_EOL;
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

    private function isPrCreatedByUpgrader(array $pr): bool
    {
        if (!isset($pr['user']['login']) && $pr['user']['login'] !== 'spryker-bot') {
            return false;
        }

        if (!isset($pr['head']['ref'])) {
            return false;
        }

        return str_contains($pr['head']['ref'], 'upgrade/') || str_contains($pr['head']['ref'], 'upgradebot/');
    }
}