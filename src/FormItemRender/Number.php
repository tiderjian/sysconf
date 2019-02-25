<?php
namespace Encore\Admin\Sysconf\FormItemRender;

class Number extends BaseRender{

    public function build(){
        parent::build();

        if($this->sysconf->extra['min'] || $this->sysconf->extra['min'] == 0 ){
            $this->item->min($this->sysconf->extra['min']);
        }

        if($this->sysconf->extra['max'] || $this->sysconf->extra['max'] == 0 ){
            $this->item->max($this->sysconf->extra['max']);
        }
    }
}