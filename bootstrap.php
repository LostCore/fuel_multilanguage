<?php

\Config::load("multilang",true);
if(\Config::get("multilang.active",false)){
    if(!\Package::loaded('i18n')) throw new \Exception("The package 'i18n' must be active for 'multilang' to work");
}

require_once(__DIR__ . '/classes/helpers.php');

Autoloader::add_core_namespace('Multilang');

Autoloader::add_classes(array(
	'Multilang\\Model'                  		=> __DIR__.'/classes/model.php',
    'Multilang\\Controller_Template'    		=> __DIR__.'/classes/controller_template.php',
    'Multilang\\Uri'                    		=> __DIR__.'/classes/uri.php',
    'Multilang\\Observer_Multilang'     		=> __DIR__.'/classes/observers/multilang.php',
    'Multilang\\Observer_Slug'          		=> __DIR__.'/classes/observers/slug.php',
    'Multilang\\Multilang_TwigExtension' 	    => __DIR__.'/classes/twig_extension.php',
));