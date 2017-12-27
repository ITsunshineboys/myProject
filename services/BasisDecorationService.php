<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/6 0006
 * Time: 下午 14:42
 */
namespace app\services;

use app\controllers\OwnerController;
use app\models\Effect;
use app\models\EngineeringStandardCarpentryCoefficient;
use app\models\EngineeringStandardCraft;
use app\models\GoodsAttr;
use app\models\GoodsCategory;
use app\models\ProjectView;
use app\models\WorkerCraftNorm;
use yii\helpers\Json;

class BasisDecorationService
{
    const WALL_SPACE = 4;
    const BRICK_UNITS = 1000;
    const GOODS_PRICE_UNITS = 100;

    const DEFAULT_VALUE = [
      'value1' => 0,
      'value2' => 1,
    ];

    private static $_goodsNames;
    private static $_carpentryNames;

    const GOODS_IDS = [
        'reticle'=>32,
        'wire'=>43,
        'spool'=>30,
        'bottom_case'=>40,
        'pvc'=>37,
        'ppr'=>33,
        'waterproof_coating'=>56,
        'plasterboard'=>22,
        'keel'=>9,
        'lead_screw'=>12,
        'concave_line'=>28,
        'lamp'=>130,
        'curtains'=>128,
        'river_sand'=>6,
        'cement'=>172,
        'self_leveling'=>36,
        'putty'=>38,
        'emulsion_varnish_primer'=>24,
        'emulsion_varnish_surface'=>25,
        'land_plaster'=>5,
        'closet'=>83,
        'wood_floor'=>17,
        'aluminium_alloy'=>79,
        'bath_heater'=>61,
        'ventilator'=>62,
        'ceiling_light'=>63,
        'tap'=>75,
        'marble'=>52,
        'sofa'=>110,
        'bed'=>121,
        'night_table'=>123,
        'kitchen_ventilator'=>106,
        'stove'=>108,
        'upright_air_conditioner'=>117,
        'hang_air_conditioner'=>119,
        'central_air_conditioner'=>120,
        'mattress'=>170,
        'shower_partition'=>152,
        'sprinkler'=>146,
        'bath_cabinet'=>140,
        'closestool'=>144,
        'squatting_pan'=>150,
        'elbow'=>35,
        'tiling'=>'贴砖',
        'wall_brick'=>45,
        'floor_tile'=>44,
        'air_brick'=>3,
        'stairs'=>82,
        'timber_door' => 80,
        'slab' => 13,
    ];

    /**
     * goods name
     */
    const GOODS_NAME =[
        'reticle'=>'网线',
        'wire'=>'电线',
        'spool'=>'线管',
        'bottom_case'=>'底盒',
        'pvc'=>'PVC管',
        'ppr'=>'PPR水管',
        'waterproof_coating'=>'防水涂料',
        'plasterboard'=>'石膏板',
        'keel'=>'龙骨',
        'lead_screw'=>'丝杆',
        'concave_line'=>'阴角线',
        'lamp'=>'灯具',
        'curtains'=>'窗帘',
        'river_sand'=>'河沙',
        'cement'=>'水泥',
        'self_leveling'=>'自流平',
        'putty'=>'腻子',
        'emulsion_varnish_primer'=>'乳胶漆底漆',
        'emulsion_varnish_surface'=>'乳胶漆面漆',
        'land_plaster'=>'石膏粉',
        'closet'=>'衣柜',
        'wood_floor'=>'木地板',
        'aluminium_alloy'=>'铝合金门窗',
        'bath_heater'=>'浴霸',
        'ventilator'=>'换气扇',
        'ceiling_light'=>'吸顶灯',
        'tap'=>'水龙头',
        'marble'=>'人造大理石',
        'sofa'=>'沙发',
        'bed'=>'床',
        'night_table'=>'床头柜',
        'kitchen_ventilator'=>'抽油烟机',
        'stove'=>'灶具',
        'upright_air_conditioner'=>'立柜式空调',
        'hang_air_conditioner'=>'挂壁式空调',
        'central_air_conditioner'=>'中央空调',
        'mattress'=>'床垫',
        'shower_partition'=>'淋浴隔断',
        'sprinkler'=>'花洒套装',
        'bath_cabinet'=>'浴柜',
        'closestool'=>'马桶',
        'squatting_pan'=>'蹲便器',
        'elbow'=>'弯头',
        'tiling'=>'贴砖',
        'wall_brick'=>'墙砖',
        'floor_tile'=>'地砖',
        'air_brick'=>'空心砖',
        'stairs'=>'楼梯',
        'timber_door' => '木门',
        'slab' => '细木工板',
    ];

    /**
     * house message
     */
    const HOUSE_MESSAGE = [
        'kitchen_area'=>'厨房面积',
        'toilet_area' =>'卫生间面积',
        'bedroom_area' =>'卧室面积',
        'hall_area' =>'客厅面积',
        'kitchen_waterproof' =>'厨房防水',
        'toilet_waterproof' =>'卫生间防水',
        'modelling_length' =>'造型长度石膏板',
        'flat_area' =>'平顶面积石膏板',
        'bedroom' =>'卧室',
        'kitchen' =>'厨房',
        'toilet' =>'卫生间',
        'household' =>'入户',
        'drawing_room_balcony' =>'客厅阳台',
        'passage' =>'客厅卧室过道',
        'live_balcony' =>'生活阳台',
        'guest_restaurant' =>'客餐厅',
        'hall' =>'客厅',

    ];

    /**
     * units
     */
    const UNITS =[
        'length' =>'长度',
        'breadth' =>'宽度',
        'texture' =>'材质',
        'long' =>'长',
        'wide' =>'宽',
        'high' =>'高',
        'area' =>'面积',
    ];

    /**
     * backman details
     */
    const BACKMAN_DETAILS =[
        'dismantle_12_area'=>'拆除12墙面积',
        'dismantle_24_area'=>'拆除24墙面积',
        'new_12_area'=>'新建12墙面积',
        'new_24_area'=>'新建24墙面积',
        'repair_length'=>'补烂长度',
        'clear_12'=>'清运12墙',
        'clear_24'=>'清运24墙',
        'vehicle_12_area'=>'运渣车12墙面积',
        'vehicle_24_area'=>'运渣车24墙面积',
        'vehicle_cost'=>'运渣车费用',
        '12_cement_dosage'=>'12墙水泥用量',
        '24_cement_dosage'=>'24墙水泥用量',
        'repair_cement_dosage'=>'补烂水泥用量',
        '12_river_sand_dosage'=>'12墙河沙用量',
        '24_river_sand_dosage'=>'24墙河沙用量',
        'repair_river_sand_dosage'=>'补烂河沙用量',
    ];

    const CARPENTRY_DETAILS =[
      'keel_sculpt'=>'龙骨做造型长度',
      'screw_rod_sculpt'=>'丝杆做造型长度',
      'plasterboard_sculpt'=>'石膏板造型长度',
      'plasterboard_area'=>'石膏板平顶面积',
      'tv_day'=>'电视墙需要天数',
      'tv_plasterboard'=>'电视墙所需石膏板',
      'keel_area'=>'龙骨做平顶面积',
      'screw_rod_area'=>'丝杆做平顶面积',
    ];

    /**
     * engineering_standard_craft 表 id
     */
    const CARPENTRY_DETAILS_IDS=[
        'reticle'=>1,//网线
        'strong_spool'=>2,//强电线管
        'wire'=>3,//电线
        'weak_spool'=>4,//弱电线管
        'ppr'=>5,//PPR水管用料
        'pvc'=>6,//PVC管用料
        'putty'=>12,//1平方腻子用量
        'waterproof'=>7,//防水涂剂用料
        'keel_sculpt'=>9,//1根龙骨做造型长度
        'screw_rod_sculpt'=>10,//1根丝杆做造型长度
        'plasterboard_sculpt'=>11,//1张石膏板造型长度
        'emulsion_varnish_primer'=>13,//1平方乳胶漆底漆
        'emulsion_varnish_surface'=>14,//1平方乳胶漆面漆
        'concave_line'=>15,//1米阴角线用量
        'land_plaster'=>16,//1平方石膏粉费用
        'plasterboard_area'=>32,//1张石膏板平顶面积
        'tv_day'=>33,//电视墙需要天数
        'tv_plasterboard'=>34,//电视墙所需石膏板
        'keel_area'=>35,//1根龙骨做平顶面积
        'screw_rod_area'=>36,//1根丝杆做平顶面积
        'tv_slab'=>37,//电视墙用细木工板
        'cement'=>18,//水泥用量
        'self_leveling'=>19,//自流平用量
        'river_sand'=>20,//河沙用量

    ];

    /**
     * EngineeringStandardCraft id 优化
     * @return array
     */
    public static function DetailsId2Title(){
       $titles = [];
        foreach (self::CARPENTRY_DETAILS_IDS as $k=> &$v){
           $title = EngineeringStandardCraft::CraftsAllbyId($v);
            if ($title) {
                $titles[$k] = $title['project_details'];
            } else {
                $titles[$k] = '';
            }
        }
        return $titles;
    }
    /**
     * 根据分类id查出分类名称
     * @return array
     */
    public static function id2Title()
    {
        $titles = [];
        foreach (self::GOODS_IDS as $k => &$v) {
            $cate = GoodsCategory::find()->where(['id' => $v, 'deleted' => 0])->one();
            if ($cate) {
                $titles[$k] = $cate->title;
            } else {
                $titles[$k] = '';
            }
        }
        return $titles;
    }

    /**
     * Map(GOODS_IDS => goodsNames())
     *
     * @return array
     */
    public static function goodsNames()
    {
        if (!self::$_goodsNames) {
            $idTitles = GoodsCategory::find()
                ->select(['id', 'title'])
                ->where(['deleted' => 0, 'level' => GoodsCategory::LEVEL3])
                ->andWhere(['in', 'id', self::GOODS_IDS])
                ->asArray()
                ->all();

            $idTitles2 = [];
            foreach ($idTitles as $v) {
                $idTitles2[$v['id']] = $v['title'];
            }

            $idTitles3 = [];
            foreach (self::GOODS_IDS as $k => $v) {
                $idTitles3[$k] = isset($idTitles2[$v]) ? $idTitles2[$v] : '';
            }

            self::$_goodsNames = $idTitles3;
        }

        return self::$_goodsNames;
    }



    /**
     *   防水  水路  强电  弱电人工费
     * @param string $points
     * @param array $labor
     * @return float
     *
     */
    public static function laborFormula($points,$day_points)
    {
        $p  = !empty($points)    ? $points    : self::DEFAULT_VALUE['value1'];
//        $l  = !empty($labor)     ? $labor     : self::DEFAULT_VALUE['value2'];
        $d  = !empty($day_points)? $day_points: self::DEFAULT_VALUE['value1'];
        //人工费：（电路总点位÷【每天做工点位】）×【工人每天费用】
        return ($p / $d);
    }


    public static function P($points,$day_points,$labor)
    {
        $p  = !empty($points)    ? $points    : self::DEFAULT_VALUE['value1'];
        $l  = !empty($labor)     ? $labor     : self::DEFAULT_VALUE['value2'];
        $d  = !empty($day_points)? $day_points: self::DEFAULT_VALUE['value1'];
        //人工费：（电路总点位÷【每天做工点位】）×【工人每天费用】
        return ceil(($p / $d)) * $l;
    }

    /**
     * 水电所需材料处理
     * @param string $points
     * @param array $goods
     * @param string $crafts
     * @return mixed
     */
    public static function quantity($points,$goods,$crafts)
    {
        //TODO 修改
        $project= EngineeringStandardCraft::CraftsAllbyId($points)['project'];
        $project=='强电工艺'?$title=self::DetailsId2Title()['strong_spool']:$title=self::DetailsId2Title()['weak_spool'];

        foreach ($crafts as $craft) {
            switch ($craft) {
                case $craft['project_details'] == self::DetailsId2Title()['reticle'] || $craft['project_details'] == self::DetailsId2Title()['wire']:
                    $material = $craft['material'];
                    break;
                case $craft['project_details'] == $title:
                    $spool = $craft['material'];
                    break;
            }
        }

        $goods_id = [];
        foreach ($goods as $one) {
            switch ($one) {
                case $one['title'] == self::goodsNames()['reticle'] || $one['title'] == self::goodsNames()['wire']:
                    $goods_price = $one['platform_price'];
                    $goods_procurement = $one['purchase_price_decoration_company'];
                    $goods_id [] = $one['id'];
                    break;
                case $one['title'] ==self::goodsNames()['spool']:
                    $spool_price = $one['platform_price'];
                    $spool_procurement = $one['purchase_price_decoration_company'];
                    $goods_id [] = $one['id'];
                    break;
                case $one['title'] ==self::goodsNames()['bottom_case']:
                    $bottom_case = $one['platform_price'];
                    $bottom_procurement = $one['purchase_price_decoration_company'];
                    $goods_id [] = $one['id'];
                    break;
            }
        }
        $ids = GoodsAttr::findByGoodsIdUnit($goods_id);
        if ($ids == null){
            $code = 1061;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }
        foreach ($ids as $one_unit) {
            switch ($one_unit) {
                case $one_unit['title'] == self::goodsNames()['reticle'] || $one_unit['title'] == self::goodsNames()['wire']:
                    $goods_value = $one_unit['value'];
                    break;
                case $one_unit['title'] == self::goodsNames()['spool']:
                    $spool_value = $one_unit['value'];
                    break;
            }
        }
        $electricity = self::plumberFormula($points,$material,$goods_value,$goods_price,$goods_procurement,$spool,$spool_value,$spool_price,$spool_procurement,$bottom_case,$bottom_procurement);


        return $electricity;

    }

    /**
     * 水电工 值
     * @param $points
     * @param $material
     * @param $goods_value
     * @param $goods_price
     * @param $goods_procurement
     * @param $spool
     * @param $spool_value
     * @param $spool_price
     * @param $spool_procurement
     * @param $bottom_case
     * @param $bottom_procurement
     * @return mixed
     */

    public static function plumberFormula($points,$material,$goods_value,$goods_price,$goods_procurement,$spool,$spool_value,$spool_price,$spool_procurement,$bottom_case,$bottom_procurement)
    {
        //线路个数计算 ,线路费用计算
        $electricity['wire_quantity'] = self::goodsNumber($points,$material,$goods_value);
        $electricity['wire_cost'] = round($electricity['wire_quantity'] * $goods_price,2);
        $electricity['wire_procurement'] = round($electricity['wire_quantity'] * $goods_procurement,2);

        //线管个数计算,线管费用计算
        $electricity['spool_quantity'] = self::goodsNumber($points,$spool,$spool_value);
        $electricity['spool_cost'] =  round($electricity['spool_quantity'] * $spool_price,2);
        $electricity['spool_procurement'] =  round($electricity['spool_quantity'] * $spool_procurement,2);

        // 底盒个数计算.底盒费用计算
        $electricity['bottom_quantity'] = $points;
        $electricity['bottom_cost'] = round($points * $bottom_case,2);
        $electricity['bottom_procurement'] = round($points * $bottom_procurement,2);

        //总费用
        $electricity['total_cost'] = $electricity['wire_cost'] + $electricity['spool_cost'] + $electricity['bottom_cost'];
        return  $electricity;
    }

    /**
     * 商品数量 计算公式
     * @param $points
     * @param $craft
     * @param $goods_value
     * @return float
     */
    public function goodsNumber($points,$craft,$goods_value)
    {
//       个数2：（弱电点位×【10m】÷抓取的商品的长度）
        $number = ceil($points * $craft / $goods_value);

        return $number;

    }


    /**
     * 水路商品
     * @param string $points
     * @param array $goods
     * @param string $crafts
     * @return float
     */
    public static function waterwayGoods($points,$goods,$crafts)
    {
        foreach ($goods as $one) {
            switch ($one) {
                case $one['title'] == self::goodsNames()['pvc']:
                    $pvc_price = $one['platform_price'];
                    $pvc_procurement = $one['purchase_price_decoration_company'];
                    $goods_id [] = $one['id'];
                    break;
                case $one['title'] == self::goodsNames()['ppr']:
                    $ppr_price = $one['platform_price'];
                    $ppr_procurement = $one['purchase_price_decoration_company'];
                    $goods_id [] = $one['id'];
                    break;
            }
        }

        $ids = GoodsAttr::findByGoodsIdUnit($goods_id);

        if ($goods == null){
            $code = 1061;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }
        //TODO 修改 用分类名称 查询
        foreach ($ids as $one_unit) {
            switch ($one_unit) {
                case $one_unit['title'] == self::goodsNames()['ppr']:
                    $ppr_value = $one_unit['value'];
                    break;
                case $one_unit['title'] == self::goodsNames()['pvc']:
                    $pvc_value = $one_unit['value'];
                    break;
            }
        }//todo 工艺详情 修改了 不能用 分类名称
        foreach ($crafts as $craft) {
            switch ($craft) {
                case $craft['project_details'] == self::DetailsId2Title()['ppr']:
                    $ppr = $craft['material'];
                    break;
                case $craft['project_details'] == self::DetailsId2Title()['pvc']:
                    $pvc =  $craft['material'];
                    break;
            }
        }


//           PPR费用：个数×抓取的商品价格
//           个数：（水路总点位×【2m】÷抓取的商品的长度）
//           PVC费用：个数×抓取的商品价格
//           个数：（水路总点位×【2m】÷抓取的商品的长度）
        $waterway['ppr_quantity'] = ceil($points * $ppr / $ppr_value);
        $waterway['pvc_quantity'] = ceil($points * $pvc / $pvc_value);

        $waterway['ppr_cost'] = round($waterway['ppr_quantity'] * $ppr_price,2);
        $waterway['pvc_cost'] = round($waterway['pvc_quantity'] * $pvc_price,2);

        $waterway['ppr_procurement'] = round($waterway['pvc_quantity'] * $ppr_procurement,2);
        $waterway['pvc_procurement'] = round($waterway['pvc_quantity'] * $pvc_procurement,2);

        $waterway['total_cost'] =  $waterway['ppr_cost'] + $waterway['pvc_cost'];
        return $waterway;
    }

    /**
     * 防水面积计算
     * @param array $arr
     * @param string $house_area
     * @param int $quantity
     * @return float
     */
    public static  function waterproofArea($area,$height,$house_area,$quantity = 1)
    {


//            厨房地面面积：【x】%×（房屋面积)
        $ground = $area * $house_area;
//            厨房墙面积：（厨房地面积÷厨房个数）开平方×【0.3m】×4 ×厨房个数
        $sqrt = sqrt($ground / $quantity);
        $wall_space = $sqrt * $height * self::WALL_SPACE * $quantity;
//            厨房防水面积：厨房地面积+厨房墙面积
        $all_area = $ground + $wall_space;
        $total_area = round($all_area,2);

        return $total_area;

    }

    /**
     * 防水商品
     * @param string $points
     * @param array $goods
     * @param string $crafts
     * @return float
     */
    public static function waterproofGoods($points,$goods,$crafts)
    {

        foreach ($crafts as $craft) {
            $material = $craft['material'];
        }

        if (count($goods) == count($goods, 1)) {
            $goods_platform_price = $goods['platform_price'];
            $goods_price = $goods['purchase_price_decoration_company'];
            $goods_id [] = $goods['id'];
        } else {
            foreach ($goods as $one) {
                $goods_platform_price = $one['platform_price'];
                $goods_price = $one['purchase_price_decoration_company'];
                $goods_id [] = $one['id'];
            }
        }

        $ids = GoodsAttr::findByGoodsIdUnit($goods_id);
        foreach ($ids as $one_unit) {
            if ($one_unit['title'] == self::goodsNames()['waterproof_coating']) {
                $goods_value = $one_unit['value'];
            }
        }

//            个数：（防水总面积×【1.25】÷抓取的商品的KG）
        $waterproof['quantity'] = ceil($points * $material /$goods_value);
//            防水涂剂费用：个数×抓取的商品价格
        $waterproof['cost'] =  round($waterproof['quantity'] * $goods_platform_price,2);
        $waterproof['procurement'] =  round($waterproof['quantity'] * $goods_price,2);
        return $waterproof;
    }

    /**
     * 地面面积计算公式
     * @param array $arr
     * @return int|mixed
     */
    public static function groundArea($arr)
    {
        $all_area = [];
        //总面积
        $all_area ['hostToilet_area'] =  $arr ['hostToilet_area'];
        $all_area ['kitchen_area'] =  $arr ['kitchen_area'];
        $all_area ['toilet_balcony_area'] =  $arr ['toilet_balcony_area'];
        $all_area ['kitchen_balcony_area'] =  $arr ['kitchen_balcony_area'];
        $area = 0;
        foreach ($all_area as $v=>$k) {
            $area += $k;
        }
        return $area;
    }

    /**
     * 墙面空间计算
     * @param array $arr
     * @return int|mixed
     */
    public static function wallSpace($arr)
    {
        $all_area = [];
        $toilet_wall_space_high = '1.8';
        $kitchen_wall_space_high = '0.3';

        //总周长
        $all_area ['toilet_perimeter'] =  $arr ['toilet_perimeter'];
        $all_area ['kitchen_perimeter'] =  $arr ['kitchen_perimeter'];
        $all_area ['toilet_balcony_perimeter'] =  $arr ['toilet_balcony_perimeter'];
        $all_area ['kitchen_balcony_perimeter'] =  $arr ['kitchen_balcony_perimeter'];
        $area_all = [];
        $area_all[] = $all_area ['toilet_perimeter'] * $toilet_wall_space_high;
        $area_all[] = $all_area ['toilet_balcony_perimeter'] * $toilet_wall_space_high;
        $area_all[] = $all_area ['kitchen_perimeter'] * $kitchen_wall_space_high;
        $area_all[] = $all_area ['kitchen_balcony_perimeter'] * $kitchen_wall_space_high;
        $area = 0;
        foreach ($area_all as $v=>$k) {
            $area += $k;
        }
        return $area;
    }

    /**
     * 木作人工计算公式
     * @param $modelling_day
     * @param $flat_day
     * @param int $video_wall
     * @param $worker_day_cost
     * @return float
     */
    public static function carpentryLabor($modelling_day,$flat_day,$video_wall = 1,$worker_day_cost)
    {
        if(!empty($modelling_day) && !empty($flat_day) ) {
            //人工费：（造型天数+平顶天数+【1】天）×【工人每天费用】
            $artificial_fee = ceil($modelling_day + $flat_day + $video_wall) * $worker_day_cost;
        }
       return $artificial_fee;
    }

    /**
     * 木作造型长度计算
     * @param array $arr
     * @param array $coefficient_all
     * @param string $series
     * @return mixed
     */
    public static function  carpentryModellingLength($arr,$series=1)
    {

        $length = $arr['modelling_length'];
        $engineering = EngineeringStandardCarpentryCoefficient::find()
            ->where(['and',['series_or_style'=>0],['coefficient'=>2]])
            ->asArray()
            ->all();
        if ($engineering){
            foreach ($engineering as $engineering_one) {
                if( $engineering_one['project'] == $series) {
                    $series_one = $engineering_one['value']*0.01;
                }
            }
        }

        //造型长度=基本造型长度×系列系数2
        return $length* $series_one;
    }

    /**
     * 造型天数计算公式
     * @param string $modelling
     * @param string $day_modelling
     * @param string $series_all
     * @param string $style_all
     * @param int $series
     * @param int $style
     * @return float|int
     */
    public static function carpentryModellingDay($modelling,$day_modelling,$series =1,$style =1)
    {

        $series_ = EngineeringStandardCarpentryCoefficient::find()
            ->where(['and',['series_or_style'=>0],['coefficient'=>1]])
            ->asArray()
            ->all();
        foreach ($series_ as $one_series){
            if ($one_series['project'] == $series){
                $series_one = $one_series['value']*0.01;
            }
        }


        $style_ = EngineeringStandardCarpentryCoefficient::find()
            ->where(['and',['series_or_style'=>1],['coefficient'=>1]])
            ->asArray()
            ->all();
        foreach ($style_ as $one_style){
            if ($one_style['project'] == $style){
                $style_one = $one_style['value']*0.01;
            }
        }

//            造型天数=造型长度÷【每天做造型长度】×系列系数1×风格系数1
        $modelling_day = $modelling / $day_modelling * $series_one * $style_one;
        return $modelling_day;
    }

    /**
     * 平顶天数计算公式
     * @param array $area
     * @param string $day_area
     * @param string $series_all
     * @param string $style_all
     * @param int $series
     * @param int $style
     * @return float|int
     */
    public static function flatDay($area,$day_area,$series = 1,$style = 1)
    {
        //平顶面积
        $flat_area = $area['flat_area'];

        $series_ = EngineeringStandardCarpentryCoefficient::find()
            ->where(['and',['series_or_style'=>0],['coefficient'=>3]])
            ->asArray()
            ->all();
        foreach ($series_ as $one_series){
            if ($one_series['project'] == $series){
                $series_one = $one_series['value']*0.01;
            }
        }


        $style_ = EngineeringStandardCarpentryCoefficient::find()
            ->where(['and',['series_or_style'=>1],['coefficient'=>2]])
            ->asArray()
            ->all();
        foreach ($style_ as $one_style){
            if ($one_style['project'] == $style){
                $style_one = $one_style['value']*0.01;
            }
        }


        //平顶天数=平顶面积÷【每天做平顶面积】×系列系数3×风格系数2
        $flat_day = $flat_area / $day_area * $series_one * $style_one;
        return $flat_day;
    }

    /**
     * 木作石膏板计算公式
     * @param string $modelling_length
     * @param string $flat_area
     * @param float $meter
     * @param array $goods
     * @param int $video_wall
     * @return float
     */
    public static function carpentryPlasterboardCost($modelling_length,$flat_area,$goods,$crafts)
    {
        $plasterboard = [];
        foreach ($goods as $goods_price ) {
            if($goods_price['title'] == self::goodsNames()['plasterboard']) {
                $plasterboard = $goods_price;
            }
        }

        foreach ($crafts as $craft) {

           if ($craft['project_details'] == self::DetailsId2Title()['plasterboard_sculpt']){

               $plasterboard_sculpt = $craft['material'];
           }

           if ($craft['project_details'] == self::DetailsId2Title()['plasterboard_area']){
               $plasterboard_area = $craft['material'];
           }

           if ($craft['project_details'] == self::DetailsId2Title()['tv_plasterboard']){
               $tv_plasterboard  = $craft['material'];
           }
        }

//            个数：（造型长度÷【2.5】m+平顶面积÷【2.5】m²+【1】张）
        $plasterboard_cost['quantity'] = ceil($modelling_length / $plasterboard_sculpt + $flat_area / $plasterboard_area + $tv_plasterboard);

//            石膏板费用：个数×商品价格
        $plasterboard_cost['cost'] = round($plasterboard_cost['quantity'] * $plasterboard['platform_price'],2);
        $plasterboard_cost['procurement'] = round($plasterboard_cost['quantity'] * $plasterboard['purchase_price_decoration_company'],2);
        return $plasterboard_cost;
    }

    /**
     * 木作龙骨计算公式
     * @param string $modelling_length
     * @param string $flat_area
     * @param float $modelling_meter
     * @param float $flat_meter
     * @param array $goods
     * @return float
     */
    public static function carpentryKeelCost($modelling_length,$flat_area,$goods,$crafts)
    {
        if(!empty($modelling_length) &&!empty($flat_area) && !empty($goods)) {
            foreach ($goods as $price) {
                if($price['title'] == self::goodsNames()['keel']) {
                    $goods_price = $price;
                }
            }


            foreach ($crafts as $craft) {
                if($craft['project_details'] == self::DetailsId2Title()['keel_sculpt']) {
                    $keel_sculpt = $craft['material'];
                }
                if($craft['project_details'] == self::DetailsId2Title()['keel_area']) {
                    $keel_area = $craft['material'];
                }
            }
//          个数=个数1+个数2
//          个数1：（造型长度÷【1.5m】）
//          个数2：（平顶面积÷【1.5m²】）

            $keel_cost['quantity'] = ceil($modelling_length / $keel_sculpt + $flat_area /$keel_area);
//          主龙骨费用：个数×商品价格
            $keel_cost['cost'] = round($keel_cost['quantity'] * $goods_price['platform_price'],2);
            $keel_cost['procurement'] = round($keel_cost['quantity'] * $goods_price['purchase_price_decoration_company'],2);
        }
        return $keel_cost;
    }

    /**
     * 木作丝杆计算公式
     * @param string $modelling_length
     * @param string $flat_area
     * @param int $modelling_meter
     * @param int $flat_meter
     * @param array $goods
     * @return float
     */
    public static function carpentryPoleCost($modelling_length,$flat_area,$goods,$crafts)
    {

        if(!empty($modelling_length) && !empty($flat_area) && !empty($goods)) {
            foreach ($goods as $price) {
                if($price['title'] == self::goodsNames()['lead_screw']) {
                    $goods_price = $price;
                }
            }

            foreach ($crafts as $craft) {
                if($craft['project_details'] == self::DetailsId2Title()['screw_rod_sculpt']) {
                    $screw_rod_sculpt = $craft['material'];
                }
                if($craft['project_details'] == self::DetailsId2Title()['screw_rod_area']) {
                    $screw_rod_area = $craft['material'];
                }
            }

//            个数=个数1+个数2
//            个数1：（造型长度÷【2m】）
//            个数2：（平顶面积÷【2m²】
            $pole_cost['quantity'] = ceil($modelling_length / $screw_rod_sculpt + $flat_area / $screw_rod_area);

//            丝杆费用：个数×抓取的商品价格
            $pole_cost['cost'] = round($pole_cost['quantity'] * $goods_price['platform_price'],2);

            $pole_cost['procurement'] = round($pole_cost['quantity'] * $goods_price['purchase_price_decoration_company'],2);
        }
        return $pole_cost;
    }

    public static function carpentryBlockboard($goods,$post)
    {


        foreach ($goods as $one_goods){
            if ($one_goods['title'] == self::goodsNames()['slab']){
                $blockboard = $one_goods;
            }
        }

        $a = EngineeringStandardCraft::find()
            ->asArray()
            ->where(['project'=>OwnerController::PROJECT_NAME['carpentry']])
            ->andWhere(['project_details'=>self::DetailsId2Title()['tv_slab']])
            ->andWhere(['city_code'=>$post['city']])
            ->one();
        if ($a){
            $tv = $a['material']/100;
        }else{
            $tv = 1;
        }

//      个数
        $pole_cost['quantity'] = (int)$tv;
//      费用
        $pole_cost['cost'] = round($pole_cost['quantity'] * $blockboard['platform_price'],2);
        $pole_cost['procurement'] = round($pole_cost['quantity'] * $blockboard['purchase_price_decoration_company'],2);

        return $pole_cost;
    }

    /**
     * 乳胶漆面积计算公式
     * @param array $area
     * @param string $house_area
     * @param string $bedroom
     * @param float $tall
     * @param int $wall
     * @return array|string
     */
    public static function paintedArea($area,$house_area ,$bedroom ,$tall= 2.8,$wall = 4)
    {
//        卧室地面积：【z】%×（房屋面积）
            $ground_area = $area * ($house_area / 100);
//        卧室墙面积：（卧室地面积÷卧室个数）开平方×【1.8m】×4 ×卧室个数
            $wall_space_area =  sqrt($ground_area / $bedroom) * $tall * $wall * $bedroom;
//        卧室底漆面积=卧室地面积+卧室墙面积
            $total_area = round($ground_area + $wall_space_area,2);
        return [$total_area,$ground_area];
    }

    /**
     * 乳胶漆周长计算公式
     * @param array $area
     * @param string $house_area
     * @param string $bedroom
     * @param int $wall
     * @return float|string
     */
    public  static function paintedPerimeter($area,$house_area,$bedroom ,$wall = 4)
    {

//        卧室地面积：【z】%×（房屋面积）
            $ground_area =  $area * ($house_area / 100);
//        卧室周长：（卧室地面积÷卧室个数）开平方×4×卧室个数
            $wall_space_perimeter = sqrt($ground_area / $bedroom) * $wall * $bedroom;

        return $wall_space_perimeter;
    }

    /**
     * 乳胶漆 费用计算公式
     * @param array $goods
     * @param array $crafts
     * @param array $area
     * @return int
     */
    public static function paintedCost($goods,$craft,$area)
    {
        $goods_value = GoodsAttr::findByGoodsIdUnit($goods['id']);
        if ($goods_value == null){
            $code = 1061;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $goods_value_one = '';
        foreach ($goods_value as $value) {
            if ($goods['title'] == self::goodsNames()['concave_line']) {
                if ($value['name'] == self::UNITS['length'] && $value['title'] == self::goodsNames()['concave_line']) {
                    $goods_value_one = $value['value'];
                } elseif($value['name'] !== self::UNITS['texture']) {
                    $goods_value_one = $value['value'];
                }
            } else {
                $goods_value_one = $value['value'];
            }
        }

//        个数：（腻子面积×【0.33kg】÷抓取的商品的规格重量）
        $putty_cost ['quantity'] = ceil($area * $craft['material'] / $goods_value_one);

//        腻子费用：个数×商品价格
        $putty_cost ['cost']  =  round($putty_cost['quantity'] * $goods['platform_price'],2);
        $putty_cost ['procurement']  = round($putty_cost['quantity'] * $goods['purchase_price_decoration_company'],2);
        return $putty_cost;
    }

    /**
     * 泥作面积
     * @param array $ground_area
     * @param array $craft
     * @param int $quantity
     * @param int $wall
     * @return float
     */
    public static function mudMakeArea($ground_area,$craft,$quantity = 1,$wall = 4)
    {

        //        （卫生间地面积÷卫生间个数）开平方×【2.4m】×4 ×卫生间个数
        $sqrt= sqrt($ground_area / $quantity);
        $wall_area = $sqrt * $craft * $wall * $quantity;

        return $wall_area;
    }

    /**
     * 泥作费用
     * @param int $area
     * @param array $goods
     * @param int $craft
     * @param string $project
     * @return mixed
     */
    public static function mudMakeCost($area,$goods,$craft,$goods_attr,$project)
    {

        foreach ($goods as $one) {
            if ($one['title'] == $project) {
                $goods_price = $one['platform_price'];
                $goods_procurement = $one['purchase_price_decoration_company'];
            }
        }
        $goods_unit = 0;
        foreach ($goods_attr as $one_goods_attr) {
            if ($one_goods_attr['title'] == $project) {
                $goods_unit = $one_goods_attr['value'];
            }
        }
        if ($goods_unit == 0){
            $goods_unit = 1;
        }

        //        个数：（水泥面积×【15kg】÷抓取的商品的KG）
        $mud_make['quantity'] = ceil($area * $craft / $goods_unit);
        //        水泥费用:个数×抓取的商品价格
        $mud_make['cost'] = round($mud_make['quantity'] * $goods_price,2);
        $mud_make['procurement'] = round($mud_make['quantity'] * $goods_procurement,2);
        return $mud_make;
    }

    /**
     * 杂工拆除
     * @param array $get_area
     * @param $day_area
     * @return mixed
     */
    public static function wallArea($get_area,$day_area,$_area)
    {
        foreach ($day_area as $skill) {
            switch ($skill) {
                case $skill['worker_kind_details'] == self::BACKMAN_DETAILS['dismantle_12_area']:
                    $dismantle_12 = $skill;
                    break;
                case $skill['worker_kind_details'] == self::BACKMAN_DETAILS['dismantle_24_area']:
                    $dismantle_24 = $skill;
                    break;
                case $skill['worker_kind_details'] == self::BACKMAN_DETAILS['new_12_area']:
                    $new_construction_12 = $skill;
                    break;
                case $skill['worker_kind_details'] == self::BACKMAN_DETAILS['new_24_area']:
                    $new_construction_24 = $skill;
                    break;
                case $skill['worker_kind_details'] == self::BACKMAN_DETAILS['repair_length']:
                    $repair = $skill;
                    break;
            }
        }
//        12墙拆除天数=12墙拆除面积÷【每天拆除12墙面积】
        $day['dismantle_12'] = $get_area['12_dismantle'] / $dismantle_12['quantity'];
//        24墙拆除天数=24墙拆除面积÷【每天拆除24墙面积】
        $day['dismantle_24'] = $get_area['24_dismantle'] / $dismantle_24['quantity'];
//        ①拆除天数=12墙拆除天数+24墙拆除天数
        $day['dismantle_day'] = $day['dismantle_12'] + $day['dismantle_24'];

//        12墙新建天数=12墙新建面积÷【每天新建12墙面积】
        $day['new_construction_12'] = $get_area['12_new_construction'] / $new_construction_12['quantity'];
//        24墙新建天数=24墙新建面积÷【每天新建24墙面积】
        $day['new_construction_24'] = $get_area['24_new_construction'] / $new_construction_24['quantity'];
//        ②新建天数=12墙新建天数+24墙新建天数
        $day['new_construction_day'] =  $day['new_construction_12'] + $day['new_construction_24'];

//        ③补烂天数=补烂长度÷【每天补烂长度】
        $day['repair_day'] = $get_area['repair'] / $repair['quantity'];

//        总天数=拆除天数+新建天数+补烂天数
        $day['total_day'] = ceil($day['dismantle_day'] + $day['new_construction_day'] + $day['repair_day'] + $_area);

        return $day;
    }

    /**
     * 杂工清运有建渣点
     * @param array $get_area
     * @param array $craft
     * @return mixed
     */
    public static function haveBuildingScrap($get_area,$craft)
    {
        $clear_12 = 0;
        $clear_24 = 0;
        foreach ($craft as $one_craft) {
            switch ($one_craft) {
                case $one_craft['project_details'] == self::BACKMAN_DETAILS['clear_12']:
                    $clear_12 = $one_craft['material'];
                    break;
                case $one_craft['project_details'] == self::BACKMAN_DETAILS['clear_24']:
                    $clear_24 = $one_craft['material'];
                    break;
            }
        }
//            运到小区楼下费用=（12墙拆除面积）×【40】
        $transportation_cost['12_wall'] = $get_area['12_dismantle'] * $clear_12;
//            运到小区楼下费用=（12墙拆除面积）×【20】
        $transportation_cost['24_wall'] = $get_area['24_dismantle'] * $clear_24;
//            清运建渣费用=清运24墙费用+清运12墙费用
        $transportation_cost['cost'] = $transportation_cost['12_wall'] + $transportation_cost['24_wall'];
        return $transportation_cost;
    }

    /**
     * 杂工清运无建渣点
     * @param array $get_area
     * @param array $craft
     * @return mixed
     */
    public static function nothingBuildingScrap($get_area,$craft)
    {

        $clear_12 = 0;
        $vehicle_12_area = 0;
        $clear_24 = 0;
        $vehicle_24_area = 0;
        $vehicle_cost = 0;

        foreach ($craft as &$one_craft) {

            switch ($one_craft) {
                case $one_craft['project_details'] == self::BACKMAN_DETAILS['clear_12']:
                    $clear_12 = $one_craft['material'];
                    break;
                case $one_craft['project_details'] == self::BACKMAN_DETAILS['vehicle_12_area']:
                    $vehicle_12_area = $one_craft['material'];
                    break;
                case $one_craft['project_details'] == self::BACKMAN_DETAILS['clear_24']:
                    $clear_24 = $one_craft['material'];
                    break;
                case $one_craft['project_details'] == self::BACKMAN_DETAILS['vehicle_24_area']:
                    $vehicle_24_area = $one_craft['material'];
                    break;
                case $one_craft['project_details'] == self::BACKMAN_DETAILS['vehicle_cost']:
                    $vehicle_cost = $one_craft['material'];
                    break;
            }

        }

//            运到小区楼下费用=（12墙拆除面积）×【40】
        $transportation_cost['12_wall'] = $get_area['12_dismantle'] * $clear_12;
//            单独外运费用=（12墙拆除面积÷【20】）×【300】
        $transportation_cost['12_wall_transportation'] = ceil($get_area['12_dismantle'] / $vehicle_12_area) * $vehicle_cost;
//            清运12墙费用=运到小区楼下费用+单独外运费用
        $transportation_cost['12_wall_cost'] = $transportation_cost['12_wall'] + $transportation_cost['12_wall_transportation'];


//            运到小区楼下费用=（24墙拆除面积）×【20】
        $transportation_cost['24_wall'] = $get_area['24_dismantle'] * $clear_24;
//            单独外运费用=（24墙拆除面积÷【10】）×【300】
        $transportation_cost['24_wall_transportation'] = ceil($get_area['24_dismantle'] / $vehicle_24_area) * $vehicle_cost;
//            清运24墙费用=运到小区楼下费用+单独外运费用
        $transportation_cost['24_wall_cost'] = $transportation_cost['24_wall'] + $transportation_cost['24_wall_transportation'];


//            清运建渣费用=清运24墙费用+清运12墙费用
        $transportation_cost['cost'] = $transportation_cost['12_wall_cost'] +  $transportation_cost['24_wall_cost'];
        return $transportation_cost;
    }

    /**
     * 杂工水泥计算公式
     * @param array $get_area
     * @param array $craft
     * @param array $goods
     * @return mixed
     */
    public static function cementCost($get_area,$craft,$goods,$goods_attr)
    {
        $cement_12 = 0;
        $cement_24 = 0;
        $repair = 0;
        foreach ($craft as $one_craft) {
            switch ($one_craft) {
                case $one_craft['project_details'] == self::BACKMAN_DETAILS['12_cement_dosage']:
                    $cement_12 = $one_craft['material'];
                    break;
                case $one_craft['project_details'] == self::BACKMAN_DETAILS['24_cement_dosage']:
                    $cement_24 = $one_craft['material'];
                    break;
                case $one_craft['project_details'] == self::BACKMAN_DETAILS['repair_cement_dosage']:
                    $repair = $one_craft['material'];
                    break;
            }
        }
        $value = 0;
        foreach ($goods_attr as $one_goods) {
            $value = $one_goods['value'];
        }

//            水泥用量=新建用量+补烂用量
//        新建用量=12墙新建面积×【10kg】+24墙新建面积×【15kg】+补烂长度×【2kg】
        $new_12 = $get_area['12_new_construction'] * $cement_12;
        $new_24 = $get_area['24_new_construction'] * $cement_24;
        $new_repair = $get_area['repair'] * $repair;
        $new_dosage = $new_12 + $new_24 + $new_repair;

        if ($new_dosage == 0){
            $cement['quantity'] = 0;
            $cement['cost'] = 0;
            $cement['procurement'] =0;
            return $cement;
        }


//        个数：（水泥用量÷抓取的商品的KG）
        $cement['quantity'] = ceil($new_dosage / $value);
//        水泥费用：个数×抓取的商品价格
        $cement['cost'] = round($cement['quantity'] * $goods['platform_price'],2);
        $cement['procurement'] = round($cement['quantity'] * $goods['purchase_price_decoration_company'],2);
        return $cement;
    }

    /**
     * 杂工空心砖计算公式
     * @param array $get_area
     * @param array $goods
     * @param array $goods_standard
     * @return mixed
     */
    public static function brickCost($get_area,$goods,$goods_standard )
    {
        if ($get_area && $goods && $goods_standard) {
            $length = 0;
            $wide = 0;
            $high = 0;
            foreach ($goods_standard as $standard) {
                if ($standard['name'] == self::UNITS['long']) {
                    $length = $standard['value'] / self::BRICK_UNITS;
                }
                if ($standard['name'] == self::UNITS['wide']) {
                    $wide = $standard['value'] / self::BRICK_UNITS;
                }
                if ($standard['name'] == self::UNITS['high']) {
                    $high = $standard['value'] / self::BRICK_UNITS;
                }
            }
//        空心砖费用：个数×抓取的商品价格
//        个数：（空心砖用量）
//        空心砖用量=12墙新建面积÷长÷高+24墙新建面积÷宽÷高
            $dosage_12 = $get_area['12_new_construction'] / $length / $wide;
            $dosage_24 = $get_area['24_new_construction'] / $wide / $high;
            $brick['quantity'] = ceil($dosage_12 + $dosage_24);
            $brick['cost'] = round($brick['quantity'] * $goods['platform_price'],2);
            $brick['procurement'] = round($brick['quantity'] * $goods['purchase_price_decoration_company'],2);
        } else {
            $brick['quantity'] = 0;
            $brick['cost'] = $brick['quantity'] * $goods['platform_price'];
            $brick['procurement'] = $brick['quantity'] * $goods['purchase_price_decoration_company'];
        }
        return $brick;
    }

    /**
     * @param array $get_area
     * @param array $goods
     * @param array $craft
     * @return mixed
     */
    public static function riverSandCost($get_area,$goods,$craft,$goods_attr)
    {

        if ($get_area && $goods && $craft) {
            foreach ($craft as $one_craft) {
                if ($one_craft['project_details'] == self::BACKMAN_DETAILS['12_river_sand_dosage']) {
                    $river_sand_12 = $one_craft['material'];

                }
                if ($one_craft['project_details'] == self::BACKMAN_DETAILS['24_river_sand_dosage']) {
                    $river_sand_24 = $one_craft['material'];
                }
                if ($one_craft['project_details'] == self::BACKMAN_DETAILS['repair_river_sand_dosage']) {
                    $river_sand_repair = $one_craft['material'];
                }

            }

            $value = '';
            foreach ($goods_attr as $one_goods_attr) {
                $value = $one_goods_attr['value'];
            }
//              河沙用量=新建用量+补烂用量
//              新建用量=12墙新建面积×【3kg】+24墙新建面积×【3kg】+补烂长度×【2kg】
            $dosage_12 = $get_area['12_new_construction'] * $river_sand_12;
            $dosage_24 = $get_area['24_new_construction'] * $river_sand_24;
            $dosage_repair = $get_area['repair'] * $river_sand_repair;
            $river_sand_dosage = $dosage_12 + $dosage_24 + $dosage_repair;
//              个数：（河沙用量÷抓取的商品的KG）
            $river_sand['quantity'] =  ceil($river_sand_dosage / $value);
//              河沙费用：个数×抓取的商品价格
            $river_sand['cost'] =   round($river_sand['quantity'] * $goods['platform_price'],2);
            $river_sand['procurement'] =   round($river_sand['quantity'] * $goods['purchase_price_decoration_company'],2);
        } else {
            $river_sand['quantity'] =  0;
            $river_sand['cost'] =  0;
            $river_sand['procurement'] = 0;
        }
        return $river_sand;
    }

    /**
     * 木地板计算公式
     * @param array $bedroom_area
     * @param string $area
     * @param array $goods
     * @return mixed
     */
    public static function woodFloorCost($bedroom_area,$area,$goods,$nature)
    {
        if ($bedroom_area && $area && $goods) {
            foreach ($bedroom_area as $one_bedroom) {
                //        卧室地面积=【z】%×（房屋面积）
                $bedroom = $one_bedroom['project_value'] * $area;
            }
//        木地板面积=卧室地面积
            $wood_floor_area =  $bedroom;

            foreach ($nature as $one_nature) {
                if ($one_nature['name'] == self::UNITS['length'] ) {
                    $length = $one_nature['value'] / self::BRICK_UNITS;
                }
                if ($one_nature['name'] == self::UNITS['length'] ) {
                    $wide = $one_nature['value'] / self::BRICK_UNITS;
                }
            }
            $wood_floor_area_1 = $length * $wide;
            //        个数：（木地板面积÷抓取木地板面积）
            $wood_floor['quantity'] = ceil($wood_floor_area / $wood_floor_area_1);
            //        木地板费用：个数×抓取的商品价格
            $wood_floor['cost'] = $wood_floor['quantity'] * $goods['platform_price'];
        }
        return $wood_floor;
    }

    /**
     * 大理石计算公式
     * @param string $post
     * @param array $goods
     * @return mixed
     */
    public static function marbleCost($post,$goods)
    {
        //        个数=飘窗米数
        $marble['quantity'] = $post;
        //        大理石费用：个数×抓取的商品价格
        $marble['cost'] = $marble['quantity'] * $goods['platform_price'];
        return $marble;
    }

    /**
     * 价格转化
     * @param $goods
     * @return array
     */
    public static function priceConversion($goods)
    {
        foreach ($goods as &$one_goods) {
            $one_goods['platform_price'] =  $one_goods['platform_price'] / self::GOODS_PRICE_UNITS;
            $one_goods['supplier_price'] =  $one_goods['supplier_price'] / self::GOODS_PRICE_UNITS;
            $one_goods['purchase_price_decoration_company'] =  $one_goods['purchase_price_decoration_company'] / self::GOODS_PRICE_UNITS;
        }

        return $goods;
    }

    /**
     * 条件判断
     * @param $goods
     * @param $post
     * @return array|bool
     */
    public static function judge($goods,$post)
    {
        foreach ($goods as $one_goods) {
            switch ($one_goods) {
                case $one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style']:
                     $goods_judge[] = $one_goods;
                    break;
                default:
                    $goods_judge[] = $one_goods;
                    break;
            }
        }
        return $goods_judge;
    }

    /**
     * 泥作规格
     * @param $goods
     * @return mixed
     */
    public static function mudMakeMaterial($goods)
    {
        if ($goods) {
            $property = [];
            $id = [];
            foreach ($goods as $one_goods) {
                $id[] = $one_goods['id'];
            }
            $goods_property = GoodsAttr::findByGoodsIdUnit($id);
            if ($goods_property == null){
                $code = 1061;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                    'data' => $goods,
                ]);
            }
            foreach ($goods_property as $one_goods_property) {
                switch ($one_goods_property) {
                    case $one_goods_property['title'] == self::goodsNames()['river_sand']:
                        $property['river_sand']['title'] = self::goodsNames()['river_sand'];
                        $property['river_sand']['value'] = $one_goods_property['value'];
                        break;
                    case $one_goods_property['title'] == self::goodsNames()['cement']:
                        $property['concrete']['title'] = self::goodsNames()['cement'];
                        $property['concrete']['value'] = $one_goods_property['value'];
                        break;
                    case $one_goods_property['title'] == self::goodsNames()['self_leveling']:
                        $property['self_leveling']['title'] = self::goodsNames()['self_leveling'];
                        $property['self_leveling']['value'] = $one_goods_property['value'];
                        break;
                }
            }

            return $property;
        }
    }

    /**
     * 利润率最大
     * @param $goods
     * @return mixed
     */
    public static function profitMargin($goods)
    {
        if (count($goods) == count($goods, 1)) {
            return $goods;
        } elseif ($goods == null){
            return new \stdClass;
        } else {
            $max =[];
            $len = count($goods);
            for ($i=0; $i<$len; $i++){
                if ($i==0){
                    $max = $goods[$i];
                    continue;
                }
                if ($goods[$i]['profit_rate']>$max['profit_rate']){
                    $max = $goods[$i];
                }

            }
            return $max;
        }
    }

    /**
     * 乳胶漆风格和系列
     * @param $goods_price
     * @param $crafts
     * @param $post
     * @return mixed
     */
    public static function coatingSeriesAndStyle($goods_price,$post)
    {
        foreach ($goods_price as $goods) {
            switch ($goods) {
                case $goods['title'] == self::goodsNames()['putty'] && $goods['series_id'] == $post['series']:
                    $all_goods[] = $goods;
                    $goods_max = self::profitMargin($goods);
                    $goods_all ['putty'] = $goods_max;
                    break;
                case $goods['title'] == self::goodsNames()['emulsion_varnish_primer'] && $goods['series_id'] == $post['series']:
                    $all_goods[] = $goods;
                    $goods_max = self::profitMargin($goods);
                    $goods_all ['primer'] = $goods_max;
                    break;
                case $goods['title'] == self::goodsNames()['emulsion_varnish_surface'] && $goods['series_id'] == $post['series']:
                    $all_goods[] = $goods;
                    $goods_max = self::profitMargin($goods);
                    $goods_all ['finishing_coat'] = $goods_max;
                    break;
                case $goods['title'] == self::goodsNames()['concave_line'] && $goods['style_id'] == $post['style']:
                    $all_goods[] = $goods;
                    $goods_max = self::profitMargin($goods);
                    $goods_all ['concave_line'] = $goods_max;
                    break;
                case $goods['title'] == self::goodsNames()['land_plaster'] && $goods['style_id'] == 0 && $goods['series_id'] == 0:
                    $all_goods[] = $goods;
                    $goods_max = self::profitMargin($goods);
                    $goods_all ['gypsum_powder'] = $goods_max;
                    break;
            }
        }
        return $goods_all;
    }


    /**
     * 强电点位
     * @param $points
     * @param $post
     * @return mixed
     */
    public static function strongCurrentPoints($points,$post)
    {
        foreach ($points as $one_points) {
            switch ($one_points) {
                case $one_points['place'] == self::HOUSE_MESSAGE['bedroom']:
                    $bedroom = $one_points['points_total'] * $post['bedroom'];
                    break;
                case $one_points['place'] == self::HOUSE_MESSAGE['kitchen']:
                    $kitchen = $one_points['points_total'] * $post['kitchen'];
                    break;
                case $one_points['place'] == self::HOUSE_MESSAGE['toilet']:
                    $toilet = $one_points['points_total'] * $post['toilet'];
                    break;
                case $one_points['place'] == self::HOUSE_MESSAGE['household']:
                    $register = $one_points['points_total'];
                    break;
                case $one_points['place'] == self::HOUSE_MESSAGE['drawing_room_balcony']:
                    $balcony = $one_points['points_total'];
                    break;
                case $one_points['place'] == self::HOUSE_MESSAGE['passage']:
                    $passage = $one_points['points_total'];
                    break;
                case $one_points['place'] == self::HOUSE_MESSAGE['live_balcony']:
                    $live_balcony= $one_points['points_total'];
                    break;
            }
            if ($one_points['place'] == self::HOUSE_MESSAGE['guest_restaurant']) {
                if ($post['hall'] != 2) {
                    $hall = $one_points['points_total'] * $post['hall'];
                } else {
                    $hall = $one_points['points_total'] + 2;
                }
            }
        }
        $strong_current_points = $hall+$bedroom+$kitchen+$toilet+$register+$balcony+$passage+$live_balcony;
        return $strong_current_points;
    }

    /**
     * 墙砖面积计算
     * @param $id
     * @return bool|float|int
     */
    public static function wallBrickAttr($id)
    {
        if ($id) {
            $goods_attr = GoodsAttr::findByGoodsIdUnit($id);
            if ($goods_attr == null){
                $code = 1061;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
            foreach ($goods_attr as $one_goods_attr) {
                if ($one_goods_attr['name'] == self::UNITS['length']) {
                    $length = $one_goods_attr['value'] / self::BRICK_UNITS;
                }
                if ($one_goods_attr['name'] == self::UNITS['breadth']) {
                    $wide = $one_goods_attr['value'] / self::BRICK_UNITS;
                }
            }
            $goods_area = $length * $wide;
            return $goods_area;
        }else
        {
            return false;
        }
    }

    /**
     * 泥作地砖
     * @param $goods
     * @return array|bool
     */
    public static function floorTile($goods)
    {
        $id = [];
        $goods_attr_details = [];
        foreach ($goods as $one_goods) {
            $id [] = $one_goods['id'];
        }
        $goods_attr = GoodsAttr::findByGoodsIdUnit($id);

        foreach ($goods_attr as $one_goods_attr) {
            switch ($one_goods_attr) {
                case $one_goods_attr['value'] == self::HOUSE_MESSAGE['kitchen']:
                    $kitchen_id = $one_goods_attr['goods_id'];
                    $goods_attr_details['kitchen']['id'] = $one_goods_attr['goods_id'];
                    $goods_attr_details['kitchen']['name'] = $one_goods_attr['value'];
                    break;
                case $one_goods_attr['value'] == self::HOUSE_MESSAGE['toilet']:
                    $toilet_id = $one_goods_attr['goods_id'];
                    $goods_attr_details['toilet']['id'] = $one_goods_attr['goods_id'];
                    $goods_attr_details['toilet']['name'] = $one_goods_attr['value'];
                    break;
                case $one_goods_attr['value'] == self::HOUSE_MESSAGE['hall']:
                    $hall_id = $one_goods_attr['goods_id'];
                    $goods_attr_details['hall']['id'] = $one_goods_attr['goods_id'];
                    $goods_attr_details['hall']['name'] = $one_goods_attr['value'];
                    break;
            }
        }
        foreach ($goods_attr as  $goods_area) {
            switch ($goods_area) {
                case $goods_area['goods_id'] == $kitchen_id:
                    if ($goods_area['name'] == self::UNITS['length']) {
                        $kitchen_length = $goods_area['value'] / self::BRICK_UNITS;
                    }
                    if ($goods_area['name'] == self::UNITS['breadth']) {
                        $kitchen_wide = $goods_area['value'] / self::BRICK_UNITS;
                    }
                    break;
                case $goods_area['goods_id'] == $toilet_id:
                    if ($goods_area['name'] == self::UNITS['length']) {
                        $toilet_length = $goods_area['value'] / self::BRICK_UNITS;
                    }
                    if ($goods_area['name'] == self::UNITS['breadth']) {
                        $toilet_wide = $goods_area['value'] / self::BRICK_UNITS;
                    }
                    break;
                case $goods_area['goods_id'] == $hall_id:
                    if ($goods_area['name'] == self::UNITS['length']) {
                        $hall_length = $goods_area['value'] / self::BRICK_UNITS;
                    }
                    if ($goods_area['name'] == self::UNITS['breadth']) {
                        $hall_wide = $goods_area['value'] / self::BRICK_UNITS;
                    }
                    break;
            }
        }
        foreach ($goods as $goods_price)
        {
            switch ($goods_price)
            {
                case $goods_price['id'] == $kitchen_id:
                    $goods_attr_details['kitchen']['price'] =  $goods_price['platform_price'];
                    $goods_attr_details['kitchen']['purchase_price_decoration_company'] =  $goods_price['purchase_price_decoration_company'];
                    break;
                case $goods_price['id'] == $toilet_id:
                    $goods_attr_details['toilet']['price'] =  $goods_price['platform_price'];
                    $goods_attr_details['toilet']['purchase_price_decoration_company'] =  $goods_price['purchase_price_decoration_company'];
                    break;
                case $goods_price['id'] == $hall_id:
                    $goods_attr_details['hall']['price'] =  $goods_price['platform_price'];
                    $goods_attr_details['hall']['purchase_price_decoration_company'] =  $goods_price['purchase_price_decoration_company'];
                    break;
            }
        }
        $goods_attr_details['kitchen']['area'] = $kitchen_length * $kitchen_wide;
        $goods_attr_details['toilet']['area'] = $toilet_length * $toilet_wide;
        $goods_attr_details['hall']['area'] = $hall_length * $hall_wide;
        return $goods_attr_details;
    }


    public static function formula($goods,$post)
    {

        foreach ($goods as $one_goods){
            switch ($one_goods){
                case $one_goods['title'] == self::goodsNames()['wood_floor'] && $one_goods['series_id'] == $post['series']: // 木地板
                //木地板面积=卧室地面积    卧室地面积=【z】%×（房屋面积） 木地板费用：个数×抓取的商品价格 个数：（木地板面积÷抓取木地板面积）
                    $goods_area = GoodsAttr::findByGoodsIdUnit($one_goods['id']);
                    foreach ($goods_area as $one_goods_area) {
                        if ($one_goods_area['name'] == self::UNITS['length']) {
                            $length = $one_goods_area['value'] / self::BRICK_UNITS;
                        }
                        if ($one_goods_area['name'] == self::UNITS['breadth']) {
                            $breadth = $one_goods_area['value'] / self::BRICK_UNITS;
                        }
                    }
                    $area = $length * $breadth;
                    $one_goods['quantity'] = ceil($post['bedroom_area'] / $area);
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $wood_floor [] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['marble']: // 大理石
                    if ($post['window'] > 1) {
                        $one_goods['quantity'] = $post['window'];
                        $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                        $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                        $marble [] = $one_goods;
                    }else {
                        $marble = null;
                    }
                    break;
                case $one_goods['title'] == self::goodsNames()['elbow']: // 弯头
                    $one_goods['quantity'] = $post['toilet'] * 4;
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $elbow[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['timber_door'] && $one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style']: //木门
                    $one_goods['quantity'] = $post['bedroom'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $timber_door[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['bath_heater'] && $one_goods['series_id'] == $post['series'] : //浴霸
                    $one_goods['quantity'] = $post['toilet'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $bath_heater[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['ventilator'] && $one_goods['series_id'] == $post['series'] : //换气扇
                    $one_goods['quantity'] = $post['toilet'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $ventilator[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['ceiling_light'] && $one_goods['series_id'] == $post['series'] : //吸顶灯
                    $one_goods['quantity'] = $post['toilet'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $ceiling_light[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['tap'] && $one_goods['series_id'] == $post['series'] : //水龙头
                    $one_goods['quantity'] = $post['toilet'] + $post['kitchen'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $tap[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['bed'] && $one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style']: //床
                    $one_goods['quantity'] = $post['bedroom'] ;
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $bed[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['night_table'] && $one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style']: //床头柜
                    $one_goods['quantity'] = $post['bedroom'] * 2;
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $night_table[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['kitchen_ventilator'] && $one_goods['series_id'] == $post['series'] : //抽油烟机
                    $one_goods['quantity'] = $post['kitchen'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $kitchen_ventilator[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['stove'] && $one_goods['series_id'] == $post['series'] : //灶具
                    $one_goods['quantity'] = $post['kitchen'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $stove[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['upright_air_conditioner'] && $one_goods['series_id'] == $post['series'] : //立柜空调
                    $one_goods['quantity'] = $post['hall'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $upright_air_conditioner[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['hang_air_conditioner'] && $one_goods['series_id'] == $post['series'] : //壁挂空调
                    $one_goods['quantity'] = $post['bedroom'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $hang_air_conditioner[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['lamp'] && $one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style'] : //灯具
                    $one_goods['quantity'] = $post['bedroom'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $lamp[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['mattress'] && $one_goods['series_id'] == $post['series'] : //床垫
                    $one_goods['quantity'] = $post['bedroom'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $mattress[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['closestool'] && $one_goods['series_id'] == $post['series'] : //马桶
                    $one_goods['quantity'] = $post['toilet'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $closestool[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['bath_cabinet'] && $one_goods['series_id'] == $post['series'] : //浴柜
                    $one_goods['quantity'] = $post['toilet'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $bath_cabinet[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['sprinkler'] && $one_goods['series_id'] == $post['series'] : //花洒套装
                    $one_goods['quantity'] = $post['toilet'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $sprinkler[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['shower_partition']: //淋浴隔断
                    $one_goods['quantity'] = $post['toilet'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $shower_partition[] = $one_goods;
                    break;
            }
        }

        $wf = isset($wood_floor) ? $wood_floor :[];
        $ma = isset($marble) ? $marble :[];
        $el = isset($elbow) ? $elbow :[];
        $td = isset($timber_door) ? $timber_door :[];
        $bh = isset($bath_heater) ? $bath_heater :[];
        $ve = isset($ventilator) ? $ventilator :[];
        $cl= isset($ceiling_light) ? $ceiling_light :[];
        $ta = isset($tap) ? $tap :[];
        $be = isset($bed) ? $bed :[];
        $nt = isset($night_table) ? $night_table :[];
        $kv = isset($kitchen_ventilator) ? $kitchen_ventilator :[];
        $st = isset($stove) ? $stove :[];
        $uac = isset($upright_air_conditioner) ? $upright_air_conditioner :[];
        $hac = isset($hang_air_conditioner) ? $hang_air_conditioner :[];
        $l = isset($lamp) ? $lamp :[];
        $m = isset($mattress) ? $mattress :[];
        $cs = isset($closestool) ? $closestool :[];
        $bc = isset($bath_cabinet) ? $bath_cabinet :[];
        $spr = isset($sprinkler) ? $sprinkler :[];
        $sp = isset($shower_partition) ? $shower_partition :[];

        $material []  = self::profitMargin($wf);
        $material []  = self::profitMargin($ma);
        $material []  = self::profitMargin($el);
        $material []  = self::profitMargin($td);
        $material []  = self::profitMargin($bh);
        $material []  = self::profitMargin($ve);
        $material []  = self::profitMargin($cl);
        $material []  = self::profitMargin($ta);
        $material []  = self::profitMargin($be);
        $material []  = self::profitMargin($nt);
        $material []  = self::profitMargin($kv);
        $material []  = self::profitMargin($st);
        $material []  = self::profitMargin($uac);
        $material []  = self::profitMargin($hac);
        $material []  = self::profitMargin($l);
        $material []  = self::profitMargin($m);
        $material []  = self::profitMargin($bc);
        $material []  = self::profitMargin($spr);
        $material []  = self::profitMargin($sp);
        $material []  = self::profitMargin($cs);

        $goods_material = [];
        foreach ($material as $one){
            if($one != null){
                $goods_material[] =   $one;
            }
        }

        return $goods_material;
    }


    /**
     * 有计算公式 单个商品
     * @param $goods
     * @param $post
     * @return float|null
     */
    public static function oneFormula($goods,$post)
    {
        // 木地板
        if ($goods['title'] == self::goodsNames()['wood_floor']){
            $goods_area = GoodsAttr::findByGoodsIdUnit($goods['id']);
            foreach ($goods_area as $one_goods_area) {
                if ($one_goods_area['name'] == self::UNITS['length']) {
                    $length = $one_goods_area['value'] / self::BRICK_UNITS;
                }
                if ($one_goods_area['name'] == self::UNITS['breadth']) {
                    $breadth = $one_goods_area['value'] / self::BRICK_UNITS;
                }
            }
            $area = $length * $breadth;
            $quantity = ceil($post['bedroom_area'] / $area);

        }

        // 大理石
        if ($goods['title'] == self::goodsNames()['marble']) {
            if ($post['window'] > 1) {
                $quantity = ceil($post['window']);
            }else {
                $quantity = null;
            }
        }

        // 弯头
        if ($goods['title'] == self::goodsNames()['elbow']) {
            $quantity['quantity'] = ceil($post['toilet'] * 4);
        }

        //木门
        if ($goods['title'] == self::goodsNames()['timber_door']) {
            $quantity = ceil($post['bedroom']);
        }

        //浴霸
        if ($goods['title'] == self::goodsNames()['bath_heater']) {
            $quantity= ceil($post['toilet']);
        }

        //换气扇
        if ($goods['title'] == self::goodsNames()['ventilator']) {
            $quantity = ceil($post['toilet']);
        }

        //吸顶灯
        if ($goods['title'] == self::goodsNames()['ceiling_light']) {
            $quantity= ceil($post['toilet']);
        }

        //水龙头
        if ($goods['title'] == self::goodsNames()['tap']) {
            $quantity = ceil($post['toilet'] + $post['kitchen']);
        }

        //床
        if ($goods['title'] == self::goodsNames()['bed']) {
            $quantity = ceil($post['bedroom']) ;
        }

        //床头柜
        if ($goods['title'] == self::goodsNames()['night_table']) {
            $quantity = ceil($post['bedroom'] * 2);
        }

        //抽油烟机
        if ($goods['title'] == self::goodsNames()['kitchen_ventilator']) {
            $quantity = ceil($post['kitchen']);
        }

        //灶具
        if ($goods['title'] == self::goodsNames()['stove']) {
            $quantity = ceil($post['kitchen']);
        }

        //立柜空调
        if ($goods['title'] == self::goodsNames()['upright_air_conditioner']) {
            $quantity = ceil($post['hall']);
        }

        //壁挂空调
        if ($goods['title'] == self::goodsNames()['hang_air_conditioner']) {
            $quantity = ceil($post['bedroom']);
        }

        //灯具
        if ($goods['title'] == self::goodsNames()['lamp']) {
            $quantity = ceil($post['bedroom']);
        }

        //床垫
        if ($goods['title'] == self::goodsNames()['mattress']) {
            $quantity = ceil($post['bedroom']);
        }

        //马桶
        if ($goods['title'] == self::goodsNames()['closestool']) {
            $quantity = ceil($post['toilet']);
        }

        //浴柜
        if ($goods['title'] == self::goodsNames()['bath_cabinet']) {
            $quantity = ceil($post['toilet']);
        }

        //花洒套装
        if ($goods['title'] == self::goodsNames()['sprinkler']) {
            $quantity = ceil($post['toilet']);
        }

        //淋浴隔断
        if ($goods['title'] == self::goodsNames()['shower_partition']) {
            $quantity = ceil($post['toilet']);
        }

        return $quantity;
    }

    /**
     * 电工材料利润最大
     * @param $goods
     * @param $material_price
     * @return array
     */
    public static function electricianMaterial($goods,$material_price)
    {
        foreach ($goods as $one_weak_current) {
            switch ($one_weak_current) {
                case $one_weak_current['title'] == self::goodsNames()['reticle']
                    || $one_weak_current['title'] == self::goodsNames()['wire']:
                    $one_weak_current['quantity'] = $material_price['wire_quantity'];
                    $one_weak_current['cost'] = $material_price['wire_cost'];
                    $one_weak_current['procurement'] = $material_price['wire_procurement'];
                    $wire [] =  $one_weak_current;
                    break;
                case $one_weak_current['title'] == self::goodsNames()['spool']:
                    $one_weak_current['quantity'] = $material_price['spool_quantity'];
                    $one_weak_current['cost'] = $material_price['spool_cost'];
                    $one_weak_current['procurement'] = $material_price['spool_procurement'];
                    $spool [] =  $one_weak_current;
                    break;
                case $one_weak_current['title'] == self::goodsNames()['bottom_case']:
                    $one_weak_current['quantity'] = $material_price['bottom_quantity'];
                    $one_weak_current['cost'] = $material_price['bottom_cost'];
                    $one_weak_current['procurement'] = $material_price['bottom_procurement'];
                    $bottom [] =  $one_weak_current;
                    break;
            }
        }

        if (!$wire && !$spool && !$bottom){
            return false;
        }


        $material ['total_cost'] = round($material_price['total_cost'],2);
        $material ['material'] [] = self::profitMargin($wire);
        $material ['material'] [] = self::profitMargin($spool);
        $material ['material'] [] = self::profitMargin($bottom);
        return $material;
    }

    /**
     * 水路材料
     * @param $goods
     * @param $material_price
     * @return array
     */
    public static function waterwayMaterial($goods,$material_price)
    {
        foreach ($goods as $one_waterway_current) {
            switch ($one_waterway_current) {
                case $one_waterway_current['title'] == self::goodsNames()['ppr'];
                    $one_waterway_current['quantity'] = $material_price['ppr_quantity'];
                    $one_waterway_current['cost'] = $material_price['ppr_cost'];
                    $one_waterway_current['procurement'] = $material_price['ppr_procurement'];
                    $ppr[] = $one_waterway_current;
                    break;
                case $one_waterway_current['title'] == self::goodsNames()['pvc'];
                    $one_waterway_current['quantity'] = $material_price['pvc_quantity'];
                    $one_waterway_current['cost'] = $material_price['pvc_cost'];
                    $one_waterway_current['procurement'] = $material_price['pvc_procurement'];
                    $pvc[] = $one_waterway_current;
                    break;
            }
        }
        if (!$ppr && !$pvc){
            return false;
        }
        $material ['total_cost'] = round($material_price['total_cost'],2);
        $material ['material'][] = BasisDecorationService::profitMargin($ppr);
        $material ['material'][] = BasisDecorationService::profitMargin($pvc);
        return $material;
    }


    /**
     * 无分类的商品
     * @param $without_assort_goods_price
     * @param $assort_material
     * @param $post
     * @return array
     */
    public static function withoutAssortGoods($goods,$assort_material,$post)
    {
        foreach ($goods as &$one){
            foreach ($assort_material as $value){
                if ($one['title'] == $value['title']){
                    $one['quantity'] =(int)$value['quantity'];
                    $one['cost'] = round($one['quantity'] * $one['platform_price'],2);
                    $one['procurement'] = round($one['quantity'] * $one['purchase_price_decoration_company'],2);
                }
            }
        }
        foreach ($goods as $goods_value){
            if ($goods_value['series_id'] == $post['series'] && $goods_value['style_id'] == $post['style']){
                $series_style_goods[] =  $goods_value;
            }elseif ($goods_value['series_id'] == $post['series'] && $goods_value['style_id'] == 0){
                $series_style_goods[] =  $goods_value;
            }elseif ($goods_value['style_id'] == $post['style'] && $goods_value['series_id'] == 0){
                $series_style_goods[] =  $goods_value;
            }elseif ($goods_value['series_id'] == 0 && $goods_value['style_id'] == 0){
                $series_style_goods[] =  $goods_value;
            }
        }


        $effect = Effect::array_group_by($series_style_goods,'title');
        foreach ($effect as $c){
            $material[] = self::profitMargin($c);
        }
//        $material[] = self::profitMargin($effect['茶几']);
        return $material;
    }

    public static function carpentryGoods($goods_price,$keel_cost,$pole_cost,$plasterboard_cost,$material_cost,$blockboard)
    {

        $material_total = [];
        foreach ($goods_price as &$one_goods_price) {
            switch ($one_goods_price) {
                case $one_goods_price['title'] == BasisDecorationService::goodsNames()['plasterboard']:
                    $one_goods_price['quantity'] = $plasterboard_cost['quantity'];
                    $one_goods_price['cost'] = $plasterboard_cost['cost'];
                    $one_goods_price['procurement'] = $plasterboard_cost['procurement'];
                    $plasterboard [] = $one_goods_price;
                    break;
                case $one_goods_price['title'] == BasisDecorationService::goodsNames()['keel']:
                    $one_goods_price['quantity'] = $keel_cost['quantity'];
                    $one_goods_price['cost'] = $keel_cost['cost'];
                    $one_goods_price['procurement'] = $keel_cost['procurement'];
                    $keel [] = $one_goods_price;
                    break;
                case $one_goods_price['title'] == BasisDecorationService::goodsNames()['lead_screw']:
                    $one_goods_price['quantity'] = $pole_cost['quantity'];
                    $one_goods_price['cost'] = $pole_cost['cost'];
                    $one_goods_price['procurement'] = $pole_cost['procurement'];
                    $pole [] = $one_goods_price;
                    break;
                case $one_goods_price['title'] == BasisDecorationService::goodsNames()['slab']:
                    $one_goods_price['quantity'] = $blockboard['quantity'];
                    $one_goods_price['cost'] = $blockboard['cost'];
                    $one_goods_price['procurement'] = $blockboard['procurement'];
                    $slab [] = $one_goods_price;
                    break;
            }
        }

        $material_total['material'][] = BasisDecorationService::profitMargin($plasterboard);
        $material_total['material'][] = BasisDecorationService::profitMargin($keel);
        $material_total['material'][] = BasisDecorationService::profitMargin($pole);
        $material_total['material'][] = BasisDecorationService::profitMargin($slab);
        $material_total['total_cost'][] =  round($material_cost,2);
        return $material_total;
    }

    /**
     * 弱电总点位
     * @param $points
     * @param $get
     * @return mixed
     */
    public static function weakPoints($points,$get)
    {
        $other = 0;
        foreach ($points as $one_points){

            if ($one_points['title'] == OwnerController::ROOM_DETAIL['hall']){
                $all = $one_points['count'] * $get['hall'];
            }

            if ($one_points['title'] == OwnerController::ROOM_DETAIL['secondary_bedroom']){
                switch ($get['bedroom']) {
                    case $get['bedroom'] == 1:
                        $secondary_bedroom = 0;
                        break;
                    case $get['bedroom'] == 2:
                        $secondary_bedroom = (int)$one_points['count'] * 1;
                        break;
                    case $get['bedroom'] > 2:
                        $secondary_bedroom = (int)$one_points['count'] * ($get['bedroom'] - 1);
                        break;
                }
            }

            if ($one_points['title'] != OwnerController::ROOM_DETAIL['secondary_bedroom'] && $one_points['title'] != OwnerController::ROOM_DETAIL['hall']){
                $other +=  $one_points['count'];
            }
        }
        $weak_points = $all + $secondary_bedroom + $other;

        return $weak_points;
    }

    /**
     * 强电总点位
     * @param $points
     * @param $get
     * @return mixed
     */
    public static function strongPoints($points,$get)
    {
        $other = 0;
        foreach ($points as $one_points){
            //客厅
            if ($one_points['title'] == OwnerController::ROOM_DETAIL['hall']){
                $all = $one_points['count'] * $get['hall'];
            }

            // 次卧
            if ($one_points['title'] == OwnerController::ROOM_DETAIL['secondary_bedroom']){
                switch ($one_points) {
                    case $get['bedroom'] == 1:
                        $secondary_bedroom =  0;
                        break;
                    case $get['bedroom'] == 2:
                        $secondary_bedroom = $one_points['count'] * 1 ;
                        break;
                    case $get['bedroom'] > 2:
                        $secondary_bedroom = $one_points['count'] * ($get['bedroom'] - 1) ;
                        break;
                }
            }

            // 厨房
            if ($one_points['title'] == OwnerController::ROOM_DETAIL['kitchen']){
                $kitchen = $one_points['count'] * $get['kitchen'] ;
            }

            // 卫生间
            if ($one_points['title'] == OwnerController::ROOM_DETAIL['toilet']){
                $toilet = $one_points['count'] * $get['toilet'] ;
            }


            if ($one_points['title'] != OwnerController::ROOM_DETAIL['hall'] && $one_points['title'] == OwnerController::ROOM_DETAIL['secondary_bedroom'] && $one_points['title'] != OwnerController::ROOM_DETAIL['kitchen'] && $one_points['title'] != OwnerController::ROOM_DETAIL['toilet']){
                $other +=  $one_points['count'];
            }
        }
        //  强电总点位
        $current_points = $all + $secondary_bedroom + $kitchen + $toilet + $other;

        return $current_points;
    }

    /**
     * 判断是否有计算公式
     * @param $category
     * @param $judge
     * @return bool
     */
    public static function judgeGoods($category,$judge)
    {

        foreach ($judge as $value)
        {
           if ($value == $category){
                return true;
           }
        }
        return false;
    }
}
