# Todo application

This application serves very small showcase of how I would set up a PHP/Symfony project with Docker development
environment.

## Usage

To run the application, all you need to have installed are `docker` and `make`.

Run

    make shell

to open the PHP container with a shell.

> You can suffix all `make` commands with `XDEBUG=1` to enable remote step-debugging. I chose to not automatically
> enable xdebug, because it makes the php container significantly slower.
>
> The code to switch between xdebug and not-xdebug can be found in `infra/xdebug.mk` that, when `XDEBUG=1` is set, uses
> `infra/docker-compose.xdebug.yml` and `infra/xdebug.ini` to enable xdebug.

You can then use the Symfony command line interface to interact with the Todo list as follows:

    bin/console todo:list // This is the main command, that will guide you forward

The following commands are available for mutation:

    bin/console todo:add [item number obtained from the todo list view]
    bin/console todo:remove [item number obtained from the todo list view]
    bin/console todo:check [item number obtained from the todo list view]
    bin/console todo:uncheck [item number obtained from the todo list view]

## Technical choices

### What I did do

- I chose a data store on the filesystem, because that is low-maintenance and eliminates the need for an extra piece of
  software like a database. It survives the death of a container without a problem, as well.
- I chose to offer a development environment in Docker (rather than, for example, using the Symfony server or command
  line tool on the host system) to make sure everyone can use it (you don't need PHP, you just need Docker and gnu make).
- In the `src` folder, you'll find the `Domain`, `Edges`, and `Infrastructure`. The domain is the code that is the core
  of the application: it contains the interface that stays the same, whatever data storage or frontend you'll offer. The
  infrastructure, in this case, implements that domain interface (or specifically the interface called `TodoList` which
  acts as a repository for `Item`s) to be able to store them on the filesystem. Lastly, the edges are the user-facing
  frontends which are in this case Symfony console commands (but could of course be replaced by a HTML frontend, an API
  of some sort, or a punchcard reader for all I care ;-)).

### Quality control

This codebase is covered by
- static analysis with PHPStan, this makes sure we catch structural bugs early;
- code style checks with PHP_CodeSniffer, this makes sure we know code will be more readable and maintainable;
- unit and end-to-end/edge tests with PHPUnit (see `tests/`), that prove that the functionality that we intend this
  application to have works, and keeps working when we come back and change stuff;
- mutation testing of said tests with Infection, that makes sure we write tests that actually test what we want to test,
  mutation testing serves as a test of the tests.

To run the quality control suite, use either one the following commands:

    make test
    make test XDEBUG=1 // to run the tests with step-debugging enabled

### What I didn't do

- I did not include an HTML frontend, as discussed, as that would have taken me relatively long while you are not trying
  to hire me for a frontend position.
- I did toy around with the idea of making this application use a Command and Event bus, to process domain events and
  commands. This would be a useful improvement for the future, when multiple edges would be in play. But for now, as we
  only have the cli edge, it makes the application overly complex.
- I did toy around with the idea of making an interactive "one command"-application, but that turned out to be a little
  more difficult (or at least time-intensive) than I hoped. I dived into that rabbit hole and spent some time on it
  before remembering the "keep it MVP" instruction for this assessment. I think the current interface, that always ends
  with the list of "Logical next commands" is relatively usable in the meantime.
- There are some technical improvements that could be made, but that I didn't choose to do at the current time. They are
  on the backlog below.


## Backlog

- [x] Set up a development environment;
- [x] Set up quality control (phpmd, codesniffer, infection, phpstan);
- [x] Install Symfony
- [x] Write the domain tests (testing the outer edges of the domain);
- [x] Implement the domain so that the domain tests succeed.
- [x] Write the edge a.k.a. end-to-end a.k.a. integration tests (testing the command line interface);
- [x] Implement the command line interface with a Symfony command;
- [ ] Move items up and down in the list;
- [ ] Move the `check` and `uncheck` methods from the `Item` class to the `TodoList` class to be more in line with the
`removeAnItem`, and to eliminate the need for an explicit `overwriteAllItems` call;
- [ ] Share the code that is used to retrieve the `lineNumber` argument from the command line interface between the edge
commands that use it.

## Known issues

- For some reason, `make` thinks it should run `composer install` every time `test` or `shell` is ran, because the
  `vendor/composer/installed.json` file is always older than `composer.lock`, which it should not be;
- It's a design choice, looking at the scope of this project, to not account for multiple processes/users/people to use
  this application at the same time. If you do, you might walk into race conditions where list and item number
  shorthands have changed in between two commands.
