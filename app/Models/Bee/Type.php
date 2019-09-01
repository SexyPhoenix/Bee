<?php

namespace App\Models\Bee;

use App\Models\Bee\Contracts\TypeConstantInterface as TypeConstant;
use App\Models\Bee\Contracts\TypeAttrConstantInterface as TypeAttrConstant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Type extends Model
{
    protected $table = 'v2_bee_type';

    protected $primaryKey = 'id';
    
    const BEE_TYPE = 'bee:type:all';

    public function scopeManage($query) 
    {
      return $query->where('is_manage', 0);
    }

    public static function getTypeList()
    {   
        return Cache::rememberForever(self::BEE_TYPE, function(){

                 $result = self::select('id', 'name', 'classify', 'is_manage', 'desc', 'component')
                             ->orderBy('order_id', 'ASC')
                             ->get();   
                             
                 return $result->toArray();

               });

    }

    public static function getMappingType($type = null)
    {   
        $mapping = [

            TypeConstant::TYPE_BING_JIA      => TypeConstant::OLD_BING_JIA, //病假
            TypeConstant::TYPE_SHI_JIA       => TypeConstant::OLD_SHI_JIA, //事假
            TypeConstant::TYPE_YEAR_REST     => TypeConstant::OLD_YEAR_REST, //年休假
            TypeConstant::TYPE_GONG_CHU      => TypeConstant::OLD_GONG_CHU, //公出
            TypeConstant::TYPE_CHU_CHAI      => TypeConstant::OLD_CHU_CHAI,//出差
            TypeConstant::TYPE_TIAO_XIU      => TypeConstant::OLD_TIAO_XIU, //调休休息
            TypeConstant::TYPE_GONG_JIA      => TypeConstant::OLD_GONG_JIA, //公假
            TypeConstant::TYPE_HUN_JIA       => TypeConstant::OLD_HUN_JIA, //婚假
            TypeConstant::TYPE_CHI_DAO       => TypeConstant::OLD_CHI_DAO, //迟到
            TypeConstant::TYPE_ZAO_TUI       => TypeConstant::OLD_ZAO_TUI, //早退
            TypeConstant::TYPE_KUANG_GONG    => TypeConstant::OLD_KUANG_GONG, //旷工
            TypeConstant::TYPE_CHAN_JIA      => TypeConstant::OLD_CHAN_JIA, //产假
            TypeConstant::TYPE_HU_LI_JIA     => TypeConstant::OLD_HU_LI_JIA, //护理假
            TypeConstant::TYPE_SANG_JIA      => TypeConstant::OLD_SANG_JIA, //丧假
            TypeConstant::TYPE_JIA_BAN       => TypeConstant::OLD_JIA_BAN, //加班
            TypeConstant::TYPE_CHAN_JIAN_JIA => TypeConstant::OLD_CHAN_JIAN_JIA, //产检假
            TypeConstant::TYPE_BU_RU_JIA     => TypeConstant::OLD_BU_RU_JIA, //哺乳假
            TypeConstant::TYPE_JIA_BAN_GONG  => TypeConstant::OLD_JIA_BAN_GONG, //在家办公

            TypeAttrConstant::ATTR_WORK_RI       => TypeConstant::OLD_JIA_BAN_WORK_RI, //工作日
            TypeAttrConstant::ATTR_REST_RI       => TypeConstant::OLD_JIA_BAN_REST_RI, //休息日
            TypeAttrConstant::ATTR_LAW_RI        => TypeConstant::OLD_JIA_BAN_LAW_RI, //法定假日
            TypeAttrConstant::ATTR_GONG_ZUO_RI   => TypeConstant::OLD_JIA_BAN_GONG_ZUO_RI, //工作日换调休
            TypeAttrConstant::ATTR_ZHOU_MO       => TypeConstant::OLD_JIA_BAN_ZHOU_MO, //周末换调休
            TypeAttrConstant::ATTR_NO_ALLOWANCE  => TypeConstant::OLD_CHAN_JIA_NO_ALLOWANCE, //无津贴
            // TypeAttrConstant::ATTR_ODD_YEAR      => TypeConstant::OLD_YEAR_ODD, //单年
            // TypeAttrConstant::ATTR_EVEN_YEAR     => TypeConstant::OLD_YEAR_EVEN //双年
        ];

        return is_null($type) ? $mapping : $mapping[$type];

    }

    public static function getMappingTypeRefer($type = null)
    {   
        $mapping = [

            TypeConstant::TYPE_BING_JIA      => '病假',
            TypeConstant::TYPE_SHI_JIA       => '事假',
            TypeConstant::TYPE_YEAR_REST     => '年休假',
            TypeConstant::TYPE_GONG_CHU      => '公出',
            TypeConstant::TYPE_CHU_CHAI      => '出差',
            TypeConstant::TYPE_TIAO_XIU      => '调休休息',
            TypeConstant::TYPE_GONG_JIA      => '公假',
            TypeConstant::TYPE_HUN_JIA       => '婚假',
            TypeConstant::TYPE_CHI_DAO       => '迟到',
            TypeConstant::TYPE_ZAO_TUI       => '早退',
            TypeConstant::TYPE_KUANG_GONG    => '旷工',
            TypeConstant::TYPE_CHAN_JIA      => '产假',
            TypeConstant::TYPE_HU_LI_JIA     => '护理假',
            TypeConstant::TYPE_SANG_JIA      => '丧假',
            TypeConstant::TYPE_JIA_BAN       => '加班',
            TypeConstant::TYPE_CHAN_JIAN_JIA => '产检假',
            TypeConstant::TYPE_BU_RU_JIA     => '哺乳假',
            TypeConstant::TYPE_JIA_BAN_GONG  => '在家办公',

            TypeAttrConstant::ATTR_WORK_RI       => '工作日',
            TypeAttrConstant::ATTR_REST_RI       => '休息日',
            TypeAttrConstant::ATTR_LAW_RI        => '法定假日',
            TypeAttrConstant::ATTR_GONG_ZUO_RI   => '工作日换调休',
            TypeAttrConstant::ATTR_ZHOU_MO       => '周末换调休',
            TypeAttrConstant::ATTR_HAS_ALLOWANCE => '有津贴',
            TypeAttrConstant::ATTR_NO_ALLOWANCE  => '无津贴'
            // TypeAttrConstant::ATTR_ODD_YEAR      => TypeConstant::OLD_YEAR_ODD, //单年
            // TypeAttrConstant::ATTR_EVEN_YEAR     => TypeConstant::OLD_YEAR_EVEN //双年
        ];

        return is_null($type) ? $mapping : $mapping[$type];

    }

}
