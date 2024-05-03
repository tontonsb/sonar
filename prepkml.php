<?php

use Tontonsb\Sonar\{Config, Processor};

require __DIR__.'/vendor/autoload.php';

Config::set('base', 'https://sonar.glaive.pro/kml');
Config::set('placeholder', 'URLTOKEN');
Config::set('location', $argv[1] ?? null);

Processor::process();
