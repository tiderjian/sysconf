<?php
namespace Encore\Admin\Sysconf\Form\Field;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Form\Field\Select as BaseSelect;
use Illuminate\Support\Str;

class SysconfSelect extends BaseSelect{

    protected $view = "admin::form.select";

    public function fillJsonEditor($field, $sourceUrl){
        if (Str::contains($field, '.')) {
            $field = $this->formatName($field);
            $class = str_replace(['[', ']'], '_', $field);
        } else {
            $class = $field;
        }

        $script = <<<EOT
$('{$this->getElementClassSelector()}').on('select2:select', function () {
    $.pjax({url: "$sourceUrl?sysconfType=" + this.value, container: '.$class'});
});
$('{$this->getElementClassSelector()}').on('select2:unselect', function () {
    $.pjax({url: "$sourceUrl", container: '.$class'});
});
EOT;

        Admin::script($script);

        return $this;
    }
}