<?php

namespace Multilang;

class Uri extends \Fuel\Core\Uri
{
    public static function create($uri = null, $variables = array(), $get_variables = array(), $secure = null){
        \Config::load("multilang",true);
        if(\Config::get("multilang.active",false) && \Config::get("multilang.rewrite_uri",true)){
            $intl = \Intl::forge();
            $current_lang = \Intl::getCurrentLanguage();
            $default_lang = $intl->getDefaultLanguage();
            $client_lang = \Intl::getClientLanguage();
            if($current_lang != $default_lang OR $current_lang != $client_lang){
                if($uri == "/"){
                    $uri = "/$current_lang";
                }else{
                    $uri = $current_lang."/".$uri;
                }
            }
        }
        $url = parent::create($uri,$variables,$get_variables,$secure);
        return $url;
    }
}