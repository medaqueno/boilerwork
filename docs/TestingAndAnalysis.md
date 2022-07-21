# Testing

PhpUnit tests may be included in tests folder. *deminy/counit* is installed as dev dependency if needed to allow some specific tests under Swoole.

Run:

``` bash
./vendor/bin/phpunit --color --testdox tests
```

# Análisis de código y QA

A pre-commit git hook exists, and has been moved to git repository. Execute (only works if git > 2.9.0):

``` bash
git config --local core.hooksPath .githooks/
```

## QA

``` bash
vendor/bin/phpcs --standard=PSR12 --parallel=4 -p app
vendor/bin/phpstan analyse app tests --level=9
vendor/bin/parallel-lint -j 4 --exclude .git --exclude vendor ./
```

