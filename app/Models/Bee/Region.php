<?php

namespace App\Models\Bee;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Region extends Model
{
   use SoftDeletes;

    protected $connection = 'mysql';
    protected $table = 'v2_regions';

    protected $primaryKey = 'region_name';
    public $incrementing = false;

    const CacheKey = 'region-parameters';
    const SEPARATOR = '-';

    const COUNTRY = 'country';
    const PROVINCE = 'province';
    const CITY = 'city';
    const AREA = 'area';

    const ALL_REGION_NAME = '*';

    protected static $serial = [self::COUNTRY, self::PROVINCE, self::CITY, self::AREA];

    const CacheLifetime = 14400000; //分钟 - 1万天

    /**
     * 获取递归的树
     * @param $key
     * @param string $name
     * @return array
     */
    public static function getTree($key='', $name='', $depth = 1)
    {
        $key = $key ? : self::$serial[0];

        $cache_key = self::CacheKey.self::SEPARATOR.$key;
        $regions = Cache::remember($cache_key, self::CacheLifetime, function() use ($key){

            return Region::select('region_key', 'region_name', 'region_value')->key($key)->get();
        });

        if($regions->count() > 0){

            $tree = self::recruitTree($regions, $depth);

            return $tree->values();
        }else{

            return [];
        }
    }

    /**
     * override for parent-children value combine. useful for form select2 setting
     * @param array $columns
     * @return mixed
     */
    public static function all($columns=['*'])
    {
        $regions = Cache::remember('region-all-list', self::CacheLifetime, function() {

            $all = parent::all();
            $refer = $all->pluck('region_value', 'region_name');
            $list = $all->map(function($item, $key) use ($refer){

                $name = $item->region_name;
                $parent_name = substr($name, 0, strrpos($name, self::SEPARATOR));
                if($parent_name){

                    $parent_value = $refer[$parent_name];
                    $item->region_value = $parent_value.' / '.$item->region_value;
                }

                return $item;
            });

            return $list;
        });

        return $regions;
    }

    private static function recruitTree($roots, $sk)
    {
        $sc = count(self::$serial);
        foreach ($roots as $region){

            $region_key = self::$serial[$sk];
            $cache_key = self::CacheKey.self::SEPARATOR.$region->region_name;
            $regions = Cache::remember($cache_key, self::CacheLifetime, function() use ($region, $region_key){

                return Region::select('region_key', 'region_name', 'region_value')
                    ->key($region_key)->nameAs($region->region_name.self::SEPARATOR)->get();
            });

            if($regions->count() > 0 && $sk < $sc - 1 ){

                self::recruitTree($regions, $sk + 1);
            }

            $region->offsetSet('children', $regions);
        }

        return $roots;
    }


    public static function clear($key='')
    {
        Cache::forget(self::CacheKey);
        Cache::forget('region-all-list');
    }

    /**
     * 获取面包屑式的参数列表，当connector为false时，返回数组
     * @param $name
     * @param bool $connector
     * @param array $ignore 选择性隐藏
     * @param int $limit 显示的层级，0 表示全部显示
     */
    public static function getBreadcrumb($regionName, $connector='/', $ignore=null, $limit=0)
    {
        $region_name_ps = explode(self::SEPARATOR, $regionName);

        $region_names = [];
        foreach ($region_name_ps as $i => $rp){

            $name = array_first($region_name_ps);
            if($i > 0){

                for($j = 1; $j <= $i; $j++){

                    $name .= self::SEPARATOR.$region_name_ps[$j];
                }
            }

            $region_names[] = $name;
        }

        if($limit !== 0){

            $region_names = array_slice($region_names, -$limit, $limit);
        }

        $query = Region::select('region_key', 'region_name', 'region_value')
            ->whereIn('region_name', $region_names)
            ->orderByRaw(DB::raw("FIELD(region_name, '".implode("','", $region_names)."')"));
        if($ignore){

            $query->whereNotIn('region_key', $ignore);
        }

        $regions = $query->get();

        if($connector){

            return $regions->implode('region_value', $connector);
        }else{

            return $regions;
        }
    }

    public function getParentRegionName()
    {
        return self::getBreadcrumb($this->region_name);
    }

    /**
     * ==================================================
     * RELATIONS
     * ==================================================
     */

    /**
     * ==================================================
     * SCOPES
     * ==================================================
     */
    public function scopeKey($query, $key)
    {
        return $query->where('region_key', $key);
    }

    public function scopePrevName($query, $name)
    {
        return $query->where('prev_region_name', $name);
    }

    public function scopeKeyAs($query, $key)
    {
        return $query->where('region_key', 'like', $key.'%');
    }

    public function scopeNameAs($query, $name)
    {
        return $query->where('region_name', 'like', $name.'%');
    }

    public function scopeNotall($query)
    {
        return $query->where($this->getTable().'.region_name', '<>', self::ALL_REGION_NAME);
    }
}
