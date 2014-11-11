<?php

Autoloader::add_namespace('Patlite', __DIR__ . '/classes/');

Autoloader::add_core_namespace('Patlite');

Autoloader::add_classes(array(
    'Patlite\\Patlite' => __DIR__ . '/classes/patlite.php',
    'Patlite\\PatliteException' => __DIR__ . '/classes/patlite.php',
));

