<?php
namespace Encore\Admin\Sysconf\FormItemRender;

use Encore\Admin\Form;
use Encore\Admin\Sysconf\Models\Sysconf;

class BaseRender implements Buildable {
    
    protected $form;
    
    public function __construct(Form $form, Sysconf $sysconf)
    {
        $this->form = $form;
        $this->sysconf = $sysconf;
    }

    public function build(){
        $item = call_user_func_array([$this->form, lcfirst($this->sysconf->type)], [$this->sysconf->slug, $this->sysconf->title]);
        $item->fill([$this->sysconf->slug => $this->sysconf->value]);
        return $item;
    }
}