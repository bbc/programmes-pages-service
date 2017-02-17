<?php
use Doctrine\Common\Annotations\AnnotationRegistry;
use Gedmo\DoctrineExtensions;

error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('UTC');

// Make sure we've got a DB with a schema in it
system(__DIR__ . '/../vendor/bin/doctrine orm:schema-tool:drop --force > /dev/null');
system(__DIR__ . '/../vendor/bin/doctrine orm:schema-tool:create > /dev/null');
