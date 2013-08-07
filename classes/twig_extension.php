<?php
/**
 * Created by LostCore
 * Date: 03/04/13
 * Time: 09.40
 */

namespace Multilang;

use \Twig_Extension;
use \Twig_Function_Function;
use \Twig_Function_Method;

class Multilang_TwigExtension extends \Twig_Extension{

    public function getName(){
   		return 'multilang_twig';
   	}

    public function getFunctions(){
        return array_merge(parent::getFunctions(),array(
           '_t' => new Twig_Function_Function('Intl::_t')
        ));
    }
}