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

By using this wrapper you can specify which field of the model must be treated as multilanguage. All you must do is to add the key "multilang" to true in the desidered property:

        protected static $_properties = array(
            'id',
            ...
            ...
            'page_title' => array(
                'data_type' => 'varchar',
                'label' => 'Title',
                'form' => array(
                    'type'=>'text',
                    'class'=>'span6'
                ),
                'multilang' => true
            ),
        );

This wrapper expose the following static functions:

**set_form_values($existing_values = null,$action)**

This takes every *$_properties* sets in the caller model and build an array that can be used in a view for adding or editing the resource. This is useful for manage field values for elements like select or radio with ease.

By default the function build an associative array with all properties of model as keys, every one with empty string as value, but you can pass an object or an array with the values for the fields.
A field "_additional_languages" will be created for those fields specified as multilanguage.

For example, by calling:

    $form_values = Model_Page::set_form_values($page,\Uri::create("admin/pages/edit/$id"));
    $this->template->set_global('form_values',$form_values);

in a controller for editing a page the result array will be like this:

    $form_values(
        <field 1...n> => <values>
        '_additional_languages' => array(
            <lang> => array(
                <field 1...n> => <values>
            )
        )
    );

So, you can write something like this in a view:

    <form action="{{ form_values.action }}" method="POST">
        {% if additional_languages is empty %}
            <input type="text" id="form_page_title" name="page_title" placeholder="Page title" value="{{ form_values.page_title }}" />
        {% else %}
            <input data-lang="{{ default_language }}" type="text" id="form_page_title" name="page_title" placeholder="Page title ({{default_language}})" value="{{ form_values.page_title }}" />
            {% for lang in additional_languages %}
                <input data-lang="{{ lang }}" type="text" id="form_page_title_{{ lang }}" name="{{ lang }}[page_title]" placeholder="Page title ({{lang}})" value="{{ form_values._additional_languages["#{lang}"].page_title }}" />
            {% endfor %}
        {% endif %}
        ...
    </form>

This template works great in a adding context, because if *null* in passed as first argument, the array will be filled with empty values.

**filter_for_language($instance,$lang)**

Format the object for displaying filtering all the content that is not of specified language.

    [Controller]

    $page = Model_Page::find($id);
    if($page){
        $page = Model_Page::filter_for_language($page,"en");
        ...
    }

    [View]

    <h1>{{page.title}}</h1> <!-- the title will be in english -->

**prepare_for_viewing($model)**

A wrapper for filter_for_language that filters for current language.

    $page = Model_Page::find($id);
    if($page){
        $page = Model_Page::filter_for_language($page);
        ...
    }

    [View]

    <h1>{{page.title}}</h1> <!-- the title will be in client language -->

**prepare_for_saving($id = null,$values = null)**

Build a model that can be saved. If $id in *null* then the function assumes that a new resource must be created; If $values is *null* the $_POST values will be used.

    $page_validation = Model_Page::validate();
    if($page_validation->run()){
       //if validation was successful
       $new_page = Model_Page::prepare_for_saving();
       ...
       $new_page->save();
    }

**validate()**

Returns the a validation object for the model.















