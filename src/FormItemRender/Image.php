<?php
namespace Encore\Admin\Sysconf\FormItemRender;

use Encore\Admin\Sysconf\Sysconf;

class Image extends BaseRender{

    public function build(){
        parent::build();
        $this->item->options([
            'deleteUrl' => url('/upload/delete'),
            'deleteExtraData' => [
                '_token' => csrf_token()
            ],
            'uploadUrl' => Sysconf::url('upload'),
            'uploadExtraData' => [
                '_token' => csrf_token()
            ]
        ]);
    }


}