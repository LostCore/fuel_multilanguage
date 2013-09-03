<?php
/**
 * Created by LostCore
 * Date: 05/03/13
 * Time: 12.40
 */

namespace Multilang;

/**
 * @usage class MyModel extends Multilang\Model
 */
class Model extends \Orm\Model{
    public static function set_form_values($existing_values = null,$action){
        $form_values = array();

        foreach(static::$_properties as $k => $v){
            if(self::_is_exluded($v)) continue;

            if(is_int($k)){
                $property_name = $v;
            }
            else{
                $property_name = $k;
            }
            $form_values[$property_name] = "";

            if(self::_is_a_select($v)){
                $form_values[$property_name] = static::$_properties[$property_name]['form']['options'];
            }
            if(isset($v['multilang']) && $v['multilang'] == true && \Fuel\Core\Config::get("multilang.active",false)){
                foreach(\Fuel\Core\Config::get('multilang.additional_languages') as $lang){
                    $form_values['_additional_languages'][$lang][$property_name] = "";
                }
                unset($lang);
            }
        }
        unset($k);
        unset($v);
        unset($property_name);

        if(isset($existing_values)){
            if(is_object($existing_values)){
                foreach(static::$_properties as $k => $v){
                    try{
                        if(self::_is_exluded($v)) continue;

                        //$predefined = $existing_values->get($k);

                        if(is_int($k)){
                            $predef['value'] = $existing_values->get($v);
                            $predef['name'] = $v;
                        }
                        else{
                            $predef['value'] = $existing_values->get($k);
                            $predef['name'] = $k;
                        }

                        if(is_array($predef['value']) && self::_is_multilang_active() && self::_is_multilang($v)){
                            if(self::_is_a_select($v)){
                                //$form_values[$k."_default"] = $predefined[0];
                                $form_values[$predef['name']."_default"] = $predef['value'][0];
                                foreach(\Fuel\Core\Config::get('multilang.additional_languages') as $lang){
                                    //if(isset($predefined[$lang]))
                                        //$form_values['_additional_languages'][$lang][$k."_default"] = $predefined[$lang];                                    if(isset($predefined[$lang]))
                                    if(isset($predef['value'][$lang]))
                                        $form_values['_additional_languages'][$lang][$predef['name']."_default"] = $predef['value'][$lang];
                                }
                                unset($lang);
                            }else{
                                //$form_values[$k] = $predefined[0];
                                $form_values[$predef['name']] = $predef['value'][0];
                                foreach(\Fuel\Core\Config::get('multilang.additional_languages') as $lang){
                                    //if(isset($predefined[$lang]))
                                        //$form_values['_additional_languages'][$lang][$k] = $predefined[$lang];
                                    if(isset($predef['value'][$lang]))
                                        $form_values['_additional_languages'][$lang][$predef['name']] = $predef['value'][$lang];
                                }
                                unset($lang);
                            }
                        }else{
                            if(self::_is_a_select($v)){
                                //$form_values[$k."_default"] = $predefined;
                                $form_values[$predef['name']."_default"] = $predef['value'];
                            }else{
                                //$form_values[$k] = $predefined;
                                $form_values[$predef['name']] = $predef['value'];
                            }
                        }
                    }catch(\OutOfBoundsException $e){
                        continue;
                    }
                }
            }elseif(is_array($existing_values)){
                foreach(static::$_properties as $k => $v){
                    if(self::_is_exluded($v)) continue;
                    if(isset($existing_values[$k])){
                        if(self::_is_a_select($v)){
                            $form_values[$k."_default"] = $existing_values[$k];
                        }else{
                            $form_values[$k] = $existing_values[$k];
                        }
                    }
                }
            }
            unset($k);
            unset($v);
        }

        $form_values['action'] = $action;

        return $form_values;
    }

    /**
   	 * Returns the a validation object for the model.
   	 *
   	 * @return  object  Validation object
   	 */
    public static function validate(){
        $validation = \Validation::forge(\Str::random('alnum', 32));
        foreach(static::$_properties as $k=>$v){
            if(isset($v['validation'])){
                $validation->add_field($v,$v['label'],$v['validation']);
            }
        }

        return $validation;
    }

    /**
     * Filter the instance data fields for selected language.
     * @static
     * @param $instance
     * @param $lang
     * @return mixed
     */
    public static function filter_for_language($instance,$lang){
        if(is_object($instance)){
            foreach($instance as $k=>$v){
                $prop = self::property($k);
                if(is_array($v) && (isset($prop['multilang']) && $prop['multilang'] == true)){
                    if(\Config::get('multilang.active',false) && isset($v[$lang])){
                        //if multilanguage is active, take only the active language (if exists)
                        $instance->$k = $v[$lang];
                    }else{
                        $instance->$k = $v[0];
                    }
                }
            }
        }elseif(is_array($instance)){
            foreach($instance as $k=>$v){
                if(is_array($v)){
                    if(\Config::get('multilang.active',false) && isset($v[$lang])){
                        $instance[$k] = $v[$lang];
                    }else{
                        $instance[$k] = $v[0];
                    }
                }
            }
        }
        return $instance;
    }

    public static function prepare_for_saving($id = null,$values = null){
        if(!isset($values)) $values = \Input::post();

        if(\Fuel\Core\Config::get("multilang.active",false)){
            //Distribute the "$lang" key values among the field keys
            foreach(\Fuel\Core\Config::get('multilang.additional_languages') as $lang){
                if(isset($values[$lang])){
                    foreach($values[$lang] as $field_key => $field_value){
                        if(is_array($values[$field_key])){
                            $values[$field_key][$lang] = $field_value;
                        }else{
                            $values[$field_key] = array($values[$field_key],$lang=>$field_value);
                        }
                    }
                }
                unset($values[$lang]);
            }
        }

        if(!isset($id)){ //create new obj with the $values
            $array_for_creation = array();
            foreach(static::$_properties as $k=>$v){
                if(is_string($k) || is_integer($k)){ //workaround per errore stranissimo (guid, created_at e updated_at nn li legge come stringhe)
                    if(array_key_exists($k,$values)){
                        $array_for_creation[$k] = $values[$k];
                    }
                }
                if(is_string($v) || is_integer($v)){
                    if(array_key_exists($v,$values)){
                        $array_for_creation[$v] = $values[$v];
                    }
                }
            }
            $new_obj = self::forge($array_for_creation);
            return $new_obj;
        }else{
            $obj = self::find($id); //put new $values in the obj
            foreach($obj as $k=>$v){
                if(isset($values[$k]))
                    $obj->$k = $values[$k];
            }
            return $obj;
        }
    }

    public static function prepare_for_viewing($m){
        $m = static::filter_for_language($m,\Intl::getCurrentLanguage());

        return $m;
    }

    private static function _is_multilang_active(){
        return \Fuel\Core\Config::get("multilang.active",false);
    }

    private static function _is_multilang($property){
        if(isset($property['multilang']) && $property['multilang'] == true) return true;
        else return false;
    }

    private static function _is_exluded($property){
        if( (isset($property['form']['type']) && $property['form']['type'] == false) || $property == "id" )
            return true;
        return false;
    }

    private static function _is_a_select($property){
        if(isset($property['form']['type']) && $property['form']['type'] == "select")
            return true;
        return false;
    }

    private static function _is_a_radio($property){
        if(isset($property['form']['type']) && $property['form']['type'] == "radio")
            return true;
        return false;
    }

    private static function _is_a_checkbox($property){
        if(isset($property['form']['type']) && $property['form']['type'] == "checkbox")
            return true;
        return false;
    }
}
 
