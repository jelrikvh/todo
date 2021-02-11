# Todo application

This application serves very small showcase of how I would set up a PHP/Symfony project with Docker development
environment.

## Usage

Run

    make shell

to open the PHP container with a shell.

Run

    make test

to run all test suites and quality checks.

## Backlog

- [x] Set up a development environment;
- [x] Set up quality control (phpmd, codesniffer, infection, phpstan);
- [x] Install Symfony
- [x] Write the domain tests (testing the outer edges of the domain);
- [x] Implement the domain so that the domain tests succeed.
- [x] Write the edge a.k.a. end-to-end a.k.a. integration tests (testing the command line interface);
- [x] Implement the command line interface with a Symfony command;
- [ ] Move the `check` and `uncheck` methods from the `Item` class to the `TodoList` class to be more in line with the
`removeAnItem`, and to eliminate the need for an explicit `overwriteAllItems` call.

## Known issues

- For some reason, `make` thinks it should run `composer install` every time `test` or `shell` is ran, because the
  `vendor/composer/installed.json` file is always older than `composer.lock`, which it should not be;
- It's a design choice, looking at the scope of this project, to not account for multiple processes/users/people to use
  this application at the same time. If you do, you might walk into race conditions where list and item number
  shorthands have changed in between two commands.
