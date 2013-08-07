<?php

namespace Multilang;

class Controller_Template extends \Fuel\Core\Controller_Template{

    var $language_info = array();

    public function __construct(\Request $request){
        parent::__construct($request);
        $this->language_info['default'] = \Intl::forge()->getDefaultLanguage();
        $this->language_info['active'] = \Config::get('multilang.active_languages');
        $this->language_info['additionals'] = \Config::get('multilang.additional_languages');
    }

	public function before(){
        parent::before();
        \Config::load("multilang",true);
        \Config::set("multilang.rewrite_uri",false);

        if(\Config::get('multilang.active')){
            $this->template->set_global('default_language',$this->language_info['default']);
            $this->template->set_global('active_languages',$this->language_info['active']);
            $this->template->set_global('additional_languages',$this->language_info['additionals']);
        }
    }
}
 
