#!/usr/bin/env php
<?php
function show_run($text, $command, $canFail = false)
{
    echo "\n* $text\n$command\n";
    passthru($command, $return);
    if (0 !== $return && !$canFail) {
        echo "\n/!\\ The command returned $return\n";
        exit(1);
    }
}
show_run("doctrine clear-metadata", "php bin/console doctrine:cache:clear-metadata");
show_run("doctrine clear-query", "php bin/console doctrine:cache:clear-query");
show_run("doctrine clear-result", "php bin/console doctrine:cache:clear-result");
show_run("Destroying cache dir manually", "rm -rf var/cache/*");
show_run("Creating directories for app kernel", "mkdir -p var/cache/dev var/cache/prod var/cache/test var/logs");
show_run("Clean and warmup prod cache", "php bin/console cache:clear --env=prod");
show_run("Clean and warmup dev cache", "php bin/console cache:clear --env=dev");
show_run("Changing permissions", "chmod -R 777 var/cache var/logs var/sessions web/files");
exit(0);
