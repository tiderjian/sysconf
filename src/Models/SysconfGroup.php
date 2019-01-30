<?php
namespace Encore\Admin\Sysconf\Models;

use Encore\Admin\Traits\AdminBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class SysconfGroup extends Model implements Sortable {

    use AdminBuilder, SoftDeletes, SortableTrait;

    protected $fillable = ['title', 'permission'];

    public $sortable = [
        'order_column_name' => 'sort',
        'sort_when_creating' => true
    ];

    public function sysconfs(){
        return $this->hasMany(Sysconf::class, 'group');
    }

    public static function groups(){
        $byOrder = 'sort';

        $self = new static();
        return $self->orderByRaw($byOrder)->get();
    }
}