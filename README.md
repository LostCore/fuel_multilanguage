#FuelPHP Multilanguage Package

* Version 1.0

## Description

This is a collection of class that aim to help in building multilanguage websites with FuelPHP. **It uses i18n packages functions.**

## Development Team

* Riccardo D'Angelo - Project Manager, Developer ([http://www.riccardodangelo.com/](http://www.riccardodangelo.com/))

## Files

*classes/* **controller_template.php**

A wrapper of FuelPHP controller_template with multilanguage capabilities.

*classes/* **model.php**

A wrapper of FuelPHP model with multilanguage capabilities

*classes/* **helpers.php**

A collection of helper functions

## Documentation

### Default configs

active => true
additional_languages => array() the languages you want to provide apart of the main one

### Controller Template

This wrapper automatically initialize the following variables in the constructor:

*Array* language_info{
    default => <the default language. es: it, en... taken by \Intl::forge()->getDefaultLanguage() >
    additionals => <additional supported languages, taken by \Config::get('multilang.additional_languages')
}

In before() method these variables are globally passed to template:

    $this->template->set_global('default_language',$this->language_info['default']);
    $this->template->set_global('additional_languages',$this->language_info['additionals']);

### Model







