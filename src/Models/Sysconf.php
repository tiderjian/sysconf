<?php
namespace Encore\Admin\Sysconf\Models;

use Encore\Admin\Traits\AdminBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use mysql_xdevapi\Schema;

class Sysconf extends Model{

    use AdminBuilder, SoftDeletes;

    const SLUG_PREFIX = 'sysconf-';

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
        if(self::getOriginalSlug($key) === false){
            return parent::setAttribute($key, $value);
        }
        $this->attributes['value'] = $value;

        return $this;
    }

    public static function getOriginalSlug($slug){
        return Str::startsWith($slug, self::SLUG_PREFIX) ? Str::after($slug, self::SLUG_PREFIX) : false;
    }

}