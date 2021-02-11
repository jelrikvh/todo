# Todo application

This application serves very small showcase of how I would set up a PHP/Symfony project with Docker development
environment.

## Usage

Run

    make shell

to open the PHP container with a shell.

Run

    make start

to run the application in the PHP container.

Run

    make test

to run all test suites and quality checks.

## Backlog

- [ ] Set up quality control (phpmd, codesniffer, infection, phpstan);
- [ ] Write the domain tests (testing the outer edges of the domain; i.e. Commands and Events);
- [ ] Write the edge a.k.a. end-to-end a.k.a. integration tests (testing the command line interface);
- [ ] Implement the domain so that the domain tests succeed;
- [ ] Implement the command line interface with a Symfony command;
