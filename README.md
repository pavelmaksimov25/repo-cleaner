# U Repository Cleaner
The U Repository Cleaner is a command-line tool designed to help clean up pull requests (PRs) and branches 
in a GitHub repository made by the Spryker Code Upgrader. It provides functionalities to filter and process 
open PRs based on specified conditions, such as whitelisted branches and PR creators. 
The tool is particularly useful for maintaining a clean repository by automating the removal of unnecessary PRs and branches.

# Usage
To use the U Repository Cleaner, execute the following command in your terminal:
```bash
php bin/repoclean.php github-org repository-name whitelisted-branch1,whitelisted-branch2,whitelisted-branchN
```
Replace the placeholders with the actual values:

- github-org: The GitHub organization or username that owns the repository.
- repository-name: The name of the repository to clean.
- whitelisted-branch1,whitelisted-branch2,whitelisted-branchN: Comma-separated list of branch names that 
  should be whitelisted and skipped during processing.


# Features
- Fetches open pull requests in the specified repository.
- Filters and processes pull requests based on whitelisted branches.
- Removes pull requests and their associated branches if certain conditions are met.
- Supports automation of repository maintenance tasks.

# Getting Started
1. Clone this repository to your local machine.
2. Install the required dependencies using Composer:
    ```bash
    composer install
    ```
3. Configure the GitHub API credentials in the config/github.php file.

4. Run the command with the appropriate arguments to clean the repository.

# Example

Assuming you want to clean a repository owned by the GitHub organization myorg, named my-repo, and you want to 
whitelist the branches master, main, and dev, you would execute the following command:

```bash
php bin/repoclean.php myorg my-repo master,main,dev
```

The tool will process the open PRs in the repository, skipping the whitelisted branches, and remove any PRs that do not meet the specified criteria.

Contributors
Pavlo Maksymov pavlo.maksymov@spryker.com
Spryker SDK Team
Copilot
ChatGpt

# License
This project is licensed under the MIT License - see the LICENSE file for details.

Feel free to customize the README description further according to your project's specific details and requirements. Make sure to provide clear instructions for installation, configuration, and usage.