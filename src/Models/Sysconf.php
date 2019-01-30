<?php
namespace Encore\Admin\Sysconf\Models;

use Encore\Admin\Traits\AdminBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sysconf extends Model{

    use AdminBuilder, SoftDeletes;

    protected $casts =[
        'extra' => 'json'
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        parent::__construct($attributes);
    }

    public function sysconfGroup(){
        return $this->belongsTo(SysconfGroup::class, 'group');
    }


    public function setAttribute($key, $value)
    {
        if(key_exists($key, $this->attributes)){
            return parent::setAttribute($key, $value);
        }
        $this->attributes['value'] = $value;

        return $this;
    }
}