<?php
namespace Encore\Admin\Sysconf\FormItemRender;

class Number extends BaseRender{

    public function build(){
        $item = parent::build();

        if($this->sysconf->extra['min'] || $this->sysconf->extra['min'] == 0 ){
            $item->min($this->sysconf->extra['min']);
        }

        if($this->sysconf->extra['max'] || $this->sysconf->extra['max'] == 0 ){
            $item->max($this->sysconf->extra['max']);
        }
    }
}