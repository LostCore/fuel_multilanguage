<?php
/**
 * Created by LostCore
 * Date: 05/03/13
 * Time: 11.42
 */

namespace Multilang;

class Observer_Multilang extends \Orm\Observer{

    public static $fields = array();
    protected $_fields;

    public function __construct($class){
        $props = $class::observers(get_class($this));
        $this->_fields = isset($props['fields']) ? $props['fields'] : static::$fields;
    }

    public function before_save($model){
        foreach($this->_fields as $field){
            if(is_array($model->$field)){
                $serialized_value = serialize($model->$field);
                if(is_serialized($serialized_value))
                    $model->$field = $serialized_value;
                else
                    throw new \Exception("Unable to serialize '$model->$field' properly");
            }
        }
    }

    public function after_load($model){
        foreach($this->_fields as $field){
            //\Fuel\Core\Debug::dump($field,$model->$field,$this->is_serialized($model->$field));
            if(is_serialized($model->$field)){
                $model->$field = unserialize($model->$field);
            }
        }
    }
}
 
