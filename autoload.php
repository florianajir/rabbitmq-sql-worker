<?php
use Doctrine\Common\Annotations\AnnotationRegistry;

$autoloadFile = __DIR__ . '/vendor/autoload.php';
if (!is_file($autoloadFile)) {
    throw new \RuntimeException('Did not find vendor/autoload.php. Did you run "composer install --dev"?');
}

$loader = require $autoloadFile;

AnnotationRegistry::registerAutoloadNamespace(
    'JMS\Serializer\Annotation',
    __DIR__ . '/vendor/jms/serializer/src'
);
AnnotationRegistry::registerLoader('class_exists');

return $loader;
