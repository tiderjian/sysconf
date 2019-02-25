<?php
namespace Encore\Admin\Sysconf\FormItemRender;

use Encore\Admin\Form;
use Encore\Admin\Sysconf\Models\Sysconf;

class BaseRender implements Buildable {
    
    protected $form;
    protected $item;
    protected $sysconf;
    
    public function __construct(Form $form, Sysconf $sysconf)
    {
        $this->form = $form;
        $this->sysconf = $sysconf;
    }

    public function build(){
        $this->item = call_user_func_array([$this->form, lcfirst($this->sysconf->type)], [Sysconf::SLUG_PREFIX . $this->sysconf->slug, $this->sysconf->title]);
        $this->item->fill([Sysconf::SLUG_PREFIX . $this->sysconf->slug => $this->sysconf->value]);
        return $this->item;
    }
}