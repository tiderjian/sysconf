<?php
namespace Encore\Admin\Sysconf;

use Encore\Admin\Extension;
use Encore\Admin\Form;
use Encore\Admin\Sysconf\Form\Field\Select;
use Encore\Admin\Sysconf\Form\Field\SysconfJson;
use Encore\Admin\Sysconf\Form\Field\SysconfSelect;
use Encore\Admin\Sysconf\FormItemRender\BaseRender;
use Encore\Admin\Sysconf\FormItemRender\Buildable;
use Illuminate\Support\Facades\Artisan;

class Sysconf extends Extension{

    const FORM_ITEM_RENDER_NAMESPACE = 'Encore\\Admin\\Sysconf\\FormItemRender\\';

    
    /**
     * @var string
     */
    protected $name = "sysconf";

    protected static $baseType = [
        'Number' => 'Number',
        'Text' => 'Text',
        'Textarea' => 'Textarea',
        'Slider' => 'Slider',
        'Checkbox' => 'Checkbox',
        'Color' => 'Color',
        'Currency' => 'Currency',
        'Date' => 'Date',
        'DateRange' => 'DateRange',
        'Decimal' => 'Decimal',
        'Editor' => 'Editor',
        'Email' => 'Email',
        'File' => 'File',
        'Icon' => 'Icon',
        'Image' => 'Image',
        'Ip' => 'Ip',
        'Mobile' => 'Mobile',
        'Listbox' => 'Listbox',
        'MultipleImage' => 'MultipleImage',
        'MultipleFile' => 'MutilpleFile',
        'MultipleSelect' => 'MultipleSelect',
        'Number' => 'Number',
        'Radio' => 'Radio',
        'Rate' => 'Rate',
        'Select' => 'Select',
        'SwitchField' => 'SwitchField',
        'Tags' => 'Tags',
        'Url' => 'Url',
    ];

    protected static $baseJsonTemplate = [
        'Number' => [ 'max' => '', 'min' => ''],
        'Textarea' => [ 'rows' => '5'],
        'Select' => [ 'options' => [ '' => '' ]],
        'Slider' => ['max' => '', 'min' => '', 'step' => '1', 'postfix' => ''],
        'Checkbox' => [ 'options' => [ '' => '' ]],
        'Date' => ['options' => [ 'format' => 'YYYY-MM-DD']],
        'DateRange' => ['options' => [ 'format' => 'YYYY-MM-DD']],
        'Listbox' => ['options' => [ '' => '' ], 'setting' => [ 'selectorMinimalHeight' => 300 ]],
        'Radio' => ['options' => [ '' => '' ], 'stacked' => false],
    ];

    protected static $extendType = [];

    protected static $type = [];

    protected static $extendJsonTemplate = [];

    protected static $jsonTemplate = [];

    /**
     * Bootstrap this package.
     *
     * @return void
     */
    public static function boot()
    {
        if(parent::boot()){
            static::registerRoutes();

            Form::extend('sysconfSelect', SysconfSelect::class);
            Form::extend('sysconfJson', SysconfJson::class);
        }
    }


    /**
     * Register routes for laravel-admin.
     *
     * @return void
     */
    protected static function registerRoutes()
    {
        $uri = parent::config('name', 'sysconf');
        $controller = parent::config('sysconf_controller', 'Encore\Admin\Sysconf\Controllers\SysconfController');

        parent::routes(function ($router)  use($uri, $controller){
            /* @var \Illuminate\Routing\Router $router */
            $router->get("{$uri}", "$controller@index");
            $router->post("{$uri}", "$controller@store");
            $router->get("{$uri}/config", "$controller@config");
            $router->get("{$uri}/config/create", "$controller@configCreate");
            $router->post("{$uri}/config", "$controller@configStore");
            $router->get("{$uri}/config/{id}/edit", "$controller@configEdit");
            $router->put("{$uri}/config/{id}", "$controller@configUpdate");
            $router->get("{$uri}/config/{id}", "$controller@configShow");
            $router->delete("{$uri}/config/{id}", "$controller@configDestroy");

            $router->put("{$uri}/group/{group}", "{$controller}@groupUpdate");
            $router->post("{$uri}/group", "{$controller}@groupStore");
            $router->delete("{$uri}/group/{group}", "{$controller}@groupDestroy");

            $router->post("{$uri}/upload", "$controller@upload");
        });
    }

    /**
     * {@inheritdoc}
     */
    public static function import()
    {
        $uri = parent::config('name', 'sysconf');

        parent::createMenu('System config', $uri, 'fa-toggle-on', 2);

        Artisan::call("vendor:publish", [
            '--provider' => SysconfServiceProvider::class
        ]);
    }


    public static function extend($slug, $title){
        self::$extendType[$slug] = $title;
    }

    public static function getType(){
        if(self::$type){
            return self::$type;
        }

        self::$type = array_merge(self::$baseType, self::$extendType);
        return self::$type;
    }

    public static function getJsonTemplate($type){
        if(self::$jsonTemplate){
            return array_has(self::$jsonTemplate, $type) ? self::$jsonTemplate[$type] : null;
        }

        self::$jsonTemplate = array_merge(self::$baseJsonTemplate, self::$extendJsonTemplate);
        return array_has(self::$jsonTemplate, $type) ? self::$jsonTemplate[$type] : null;
    }

    public static function buildFormItem(Form $form, \Encore\Admin\Sysconf\Models\Sysconf $conf){
         if(class_exists(self::FORM_ITEM_RENDER_NAMESPACE . ucfirst($conf->type))){
             $class = self::FORM_ITEM_RENDER_NAMESPACE . ucfirst($conf->type);
             $builder = new $class($form, $conf);
         }
         else{
             $builder = new BaseRender($form, $conf);
         }
         
         if(!$builder instanceof Buildable){
             throw new RuntimeException('No Buildable interface implemented.');
         }

         $builder->build();
    }

    public static function url(string $path = null){
        $url = url('/' . trim(config('admin.route.prefix', 'admin'), '/') . '/' . parent::config('name', 'sysconf'));
        if(!is_null($path)){
            $url .= '/' . trim($path, '/');
        }
        return $url;
    }

}