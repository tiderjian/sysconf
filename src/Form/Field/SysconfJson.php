<?php
namespace Encore\Admin\Sysconf\Form\Field;

use Encore\Admin\Form\Field;

class SysconfJson extends Field{

    protected $view = 'laravel-admin-sysconf::jsoneditor';

    protected static $css = [
        'vendor/laravel-admin-ext/sysconf/jsoneditor-5.24.6/dist/jsoneditor.min.css',
    ];

    protected static $js = [
        'vendor/laravel-admin-ext/sysconf/jsoneditor-5.24.6/dist/jsoneditor.min.js',
    ];

    public function render()
    {
        $json = old($this->column, $this->value());

        if (empty($json)) {
            $json = [];
        }

        if (empty($options)) {
            $options = "{}";
        }

        parent::addVariables([
            'json' => $json,
            'options' => $options
        ]);

        return parent::render();
    }
}