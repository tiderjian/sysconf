<?php
namespace Encore\Admin\Sysconf\FormItemRender;

class Image extends BaseRender{

    public function build(){
        $item = parent::build();
        $item->options(["initialPreviewShowDelete" => false, 'showRemove' => true]);
    }
}