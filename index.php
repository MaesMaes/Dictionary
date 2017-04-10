<?php

require "vendor/autoload.php";


$start = microtime( true );

//\liw\app\App::createJSONData();
\liw\app\App::showJSONData( 'app/tmp/ModelsList.json' );

echo microtime( true ) - $start . '<Br/>';