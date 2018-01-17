<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/6 0006
 * Time: 下午 14:42
 */
namespace app\services;

use app\controllers\OwnerController;
use app\models\Apartment;
use app\models\Effect;
use app\models\EngineeringStandardCarpentryCoefficient;
use app\models\EngineeringStandardCraft;
use app\models\Goods;
use app\models\GoodsAttr;
use app\models\GoodsCategory;
use app\models\GoodsStyle;
use app\models\ProjectView;
use app\models\Series;
use app\models\Style;
use app\models\WorkerCraftNorm;
use app\models\WorkerType;
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
//    private static $_carpentryNames;

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
//                ->where(['deleted' => 0, 'level' => GoodsCategory::LEVEL3])
                // TODO   不需要下架状态  Wch
                ->where(['level' => GoodsCategory::LEVEL3])
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
     * @param int $points
     * @param int $day_points
     * @return float
     *
     */
    public static function laborFormula($points,$day_points)
    {
        $p = !empty($points)    ? $points    : self::DEFAULT_VALUE['value1'];
        $d = !empty($day_points)? $day_points: self::DEFAULT_VALUE['value1'];

        //人工费：（电路总点位÷【每天做工点位】）×【工人每天费用】
        return self::algorithm(6,$p,$d);
    }


    /**
     * 商品属性抓取
     * @param $goods
     * @param $value
     * @param $name
     * @param $int
     * @return array
     */
    public static function goodsAttr($goods,$value,$name,$int = 1)
    {

        $one_goods = [];
        if (!is_array($goods)){
            foreach ($goods as $one){
                if ($one['title'] == $value){
                    $one_goods[] = $one;
                }
            }
        }else{
            $one_goods[] = $goods;
        }




//        $style = self::style($one_goods);
        //  抓取利润最大的商品
        $max_goods = self::profitMargin($one_goods);
        switch ($int){
            case $int == 1 ;
                $goods_attr = GoodsAttr::findByGoodsIdUnit($max_goods['id'],$name);
                break;
            case $int == 2 ;
                $goods_attr = GoodsAttr::findByGoodsIdUnits($max_goods['id'],$name);
        }
        return [$max_goods,$goods_attr];
    }


    /**
     * 强弱电价格计算
     * @param $int
     * @param $points
     * @param $craft
     * @param $goods
     * @return mixed
     */
    public static function plumberFormula($int,$points,$goods,$craft = 1,$craft1=1,$points1=1)
    {
        switch ($int){
            case $int == 1:
                $electricity['quantity'] = (int)ceil(self::algorithm(4,$points,$craft,$goods[1]['value']));
                $electricity['cost'] = round(self::algorithm(1,$electricity['quantity'],$goods[0]['platform_price']),2);
                $electricity['procurement'] = round(self::algorithm(1,$electricity['quantity'],$goods[0]['purchase_price_decoration_company']),2);
                break;
            case $int == 2:
                $electricity['quantity'] = (int)ceil($points);
                $electricity['cost'] = round(self::algorithm(1,$points,$goods[0]['platform_price']),2);
                $electricity['procurement'] = round(self::algorithm(1,$points,$goods[0]['purchase_price_decoration_company']),2);
                break;
            case $int == 3:
                $quantity = self::algorithm(4,$points1,$craft,$goods[1]['value']);
                $quantity1 = self::algorithm(4,$points,$craft1,$goods[1]['value']);
                $electricity['quantity'] = (int)ceil(self::algorithm(3,$quantity,$quantity1));
                $electricity['cost'] = round(self::algorithm(1,$electricity['quantity'],$goods[0]['platform_price']),2);
                $electricity['procurement'] = round(self::algorithm(1,$electricity['quantity'],$goods[0]['purchase_price_decoration_company']),2);
                break;
        }

        $goods[0]['quantity'] = $electricity['quantity'];
        $goods[0]['cost'] = $electricity['cost'];
        $goods[0]['procurement'] = $electricity['procurement'];

        return  $goods[0];
    }

    /**
     * 水路商品
     * @param int $int
     * @param string $points
     * @param array $goods
     * @param string $craft
     * @return float
     */
    public static function waterwayGoods($int,$points,$craft,$goods)
    {
        switch ($int){
            case $int == 1:
                $value['quantity'] = (int)ceil(self::algorithm(4,$points,$craft,$goods[1]['value']));
                $value['cost'] =  round(self::algorithm(1,$value['quantity'],$goods[0]['platform_price']),2);
                $value['procurement'] = round(self::algorithm(1,$value['quantity'],$goods[0]['purchase_price_decoration_company']),2);
                break;
        }
        $goods[0]['quantity'] = $value['quantity'];
        $goods[0]['cost'] = $value['cost'];
        $goods[0]['procurement'] = $value['procurement'];

        return $goods[0];
    }

    /**
     * 防水面积计算
     * @param $ratio
     * @param $height
     * @param $get
     * @param $room
     * @param $wall
     * @return int
     */
    public static  function waterproofArea($ratio,$height,$get,$room,$wall)
    {


//            厨房地面面积：【x】%×（房屋面积)
        $ground_area = self::algorithm(1,$ratio,$get['area']);
//            厨房墙面积：（厨房地面积÷厨房个数）开平方×【0.3m】×4 ×厨房个数
        $sqrt = sqrt(self::algorithm(6,$ground_area,$room));
        // 开平方 * 层高
        $value = self::algorithm(1,$sqrt,$height);
        $value1 = self::algorithm(1,$wall,$room);
        // 墙面积
        $wall_area = self::algorithm(1,$value,$value1);


        // 总面积
        $total = self::algorithm(3,$ground_area,$wall_area);

        return $total;

    }

    /**
     * 防水商品
     * @param string $area
     * @param array $goods
     * @param string $crafts
     * @return float
     */
    public static function waterproofGoods($area,$crafts,$goods)
    {

//            个数：（防水总面积×【1.25】÷抓取的商品的KG）
        $value['quantity'] = (int)ceil(self::algorithm(4,$area,$crafts,$goods[1]['value']));
//            防水涂剂费用：个数×抓取的商品价格
        $value['cost'] =  round(self::algorithm(1,$value['quantity'],$goods[0]['platform_price']),2);
        $value['procurement'] =  round(self::algorithm(1,$value['quantity'],$goods[0]['purchase_price_decoration_company']),2);

        $goods[0]['quantity'] = $value['quantity'];
        $goods[0]['cost'] = $value['cost'];
        $goods[0]['procurement'] = $value['procurement'];

        return $goods[0];
    }



    /**
     * 木作计算公式
     * @param $int
     * @param $length
     * @param $area
     * @param $goods
     * @param $value
     * @param $value1
     * @param $value2
     * @return mixed
     */
    public static function carpentryPlasterboardCost($int,$length,$area,$goods,$value=1,$value1=1,$value2=1)
    {

        switch ($int){
            case $int == 1 ;
                // 石膏板费用：个数×商品价格     个数：（造型长度÷【2.5】m+平顶面积÷【2.5】m²+【1】张）
                $modelling_length = self::algorithm(6,$length,$value);
                $flat_area = self::algorithm(6,$area,$value1);
                $cost['quantity'] = (int)ceil(self::algorithm(5,$modelling_length,$flat_area,$value2));
                $cost['cost'] = round(self::algorithm(1,$cost['quantity'],$goods[0]['platform_price']),2);
                $cost['procurement'] = round(self::algorithm(1,$cost['quantity'],$goods[0]['purchase_price_decoration_company']),2);
                break;
            case $int == 2;
                // 龙骨费用：个数×商品价格   个数=个数1+个数2 个数1：（造型长度÷【1.5m】） 个数2：（平顶面积÷【1.5m²】）
                $modelling_length = self::algorithm(6,$length,$value);
                $flat_area = self::algorithm(6,$area,$value1);
                $cost['quantity'] = (int)ceil(self::algorithm(3,$modelling_length,$flat_area));
                $cost['cost'] = round(self::algorithm(1,$cost['quantity'],$goods[0]['platform_price']),2);
                $cost['procurement'] = round(self::algorithm(1,$cost['quantity'],$goods[0]['purchase_price_decoration_company']),2);
                break;
            case $int == 3;
                // 木工板费用：个数×商品价格   个数：【1】
                $cost['quantity'] = (int)ceil($length);
                $cost['cost'] = round(self::algorithm(1,$cost['quantity'],$goods[0]['platform_price']),2);
                $cost['procurement'] = round(self::algorithm(1,$cost['quantity'],$goods[0]['purchase_price_decoration_company']),2);
                break;


        }

        $goods[0]['quantity'] = $cost['quantity'];
        $goods[0]['cost'] = $cost['cost'];
        $goods[0]['procurement'] = $cost['procurement'];

        return $goods[0];
    }



    /**
     * 乳胶漆面积计算公式
     * @param $ratio
     * @param $area
     * @param $tall
     * @param $value
     * @param int $wall
     * @return array
     */
    public static function paintedArea($ratio,$area,$tall,$value,$wall = 4)
    {
//        卧室地面积：【z】%×（房屋面积）
        $ground_area = self::algorithm(1,$ratio,$area);
//        卧室墙面积：（卧室地面积÷卧室个数）开平方×【1.8m】×4 ×卧室个数
        $sqrt = sqrt(self::algorithm(6,$ground_area,$value));
        $wall_area = self::algorithm(9,$sqrt,$tall,$wall,$value);

//        卧室底漆面积=卧室地面积+卧室墙面积
        $total_area = round(self::algorithm(3,$ground_area,$wall_area),2);
        return [$total_area,$ground_area];
    }

    /**
     * 乳胶漆周长计算公式
     * @param $area
     * @param $value
     * @param int $wall
     * @return int
     */
    public  static function paintedPerimeter($area,$value,$wall = 4)
    {
        //      （卧室地面积÷卧室个数）开平方×4×卧室个数
        $sqrt = sqrt(self::algorithm(6,$area,$value));
        $v = self::algorithm(10,$sqrt,$wall,$value);

        return $v;
    }

    /**
     * 乳胶漆计算公式
     * @param $int
     * @param $area
     * @param $craft
     * @param $goods
     * @return mixed
     */
    public static function paintedCost($int,$area,$craft,$goods)
    {
        switch ($int){
            case $int == 1:
                $value ['quantity'] = (int)ceil(self::algorithm(4,$area,$craft,$goods[1]['value']));
                $value ['cost'] = round(self::algorithm(1,$value ['quantity'],$goods[0]['platform_price']),2);
                $value ['procurement'] = round(self::algorithm(1,$value ['quantity'],$goods[0]['purchase_price_decoration_company']),2);
                break;
            case $int == 2:
                $value ['quantity'] = (int)ceil(self::algorithm(4,$craft,$area,$goods[1]['value']));
                $value ['cost'] = round(self::algorithm(1,$value ['quantity'],$goods[0]['platform_price']),2);
                $value ['procurement'] = round(self::algorithm(1,$value ['quantity'],$goods[0]['purchase_price_decoration_company']),2);
        }

        $goods[0]['quantity'] = $value ['quantity'];
        $goods[0]['cost'] = $value ['cost'];
        $goods[0]['procurement'] = $value ['procurement'];

        return $goods[0];
    }

    /**
     * 泥作面积
     * 公式  （卫生间地面积÷卫生间个数）开平方×【2.4m】×4 ×卫生间个数
     * @param $area
     * @param $high
     * @param int $quantity
     * @param int $wall
     * @return int
     */
    public static function mudMakeArea($area,$high,$quantity = 1,$wall = 4)
    {
        $sqrt= sqrt(self::algorithm(6,$area,$quantity));
        $wall_area = self::algorithm(9,$sqrt,$high,$wall,$quantity);

        return $wall_area;
    }

    /**
     * 泥作费用
     * @param $int
     * @param $area
     * @param $craft
     * @param $goods
     * @return mixed
     */
    public static function mudMakeCost($int,$area,$craft,$goods)
    {
        switch ($int){
            case $int ==1:
                $value['quantity'] = (int)ceil(self::algorithm(4,$area,$craft,$goods[1]['value']));
                $value['cost'] = round(self::algorithm(1,$value['quantity'],$goods[0]['platform_price']),2);
                $value['procurement'] = round(self::algorithm(1,$value['quantity'],$goods[0]['purchase_price_decoration_company']),2);
                break;
            case $int == 2:
                $area_ = self::algorithm(1,$goods[1][0]['value'],$goods[1][1]['value']);
                $value['quantity'] = (int)ceil(self::algorithm(6,$area,$area_));
                $value['cost'] = round(self::algorithm(1,$value['quantity'],$goods[0]['platform_price']),2);
                $value['procurement'] = round(self::algorithm(1,$value['quantity'],$goods[0]['purchase_price_decoration_company']),2);
                break;
            case $int == 3:
                foreach ($goods[0]['attr'] as $one_goods){
                    if ($one_goods['name']  == '长度' ){
                        $v = $one_goods['value'];
                    }
                    if ($one_goods['name']  == '宽度' ){
                        $v1 = $one_goods['value'];
                    }
                }
                $area_ = self::algorithm(1,$v,$v1);
                $value['quantity'] = ceil(self::algorithm(6,$area,$area_));
                $value['cost'] = round(self::algorithm(1,$value['quantity'],$goods[0]['platform_price']),2);
                $value['procurement'] = round(self::algorithm(1,$value['quantity'],$goods[0]['purchase_price_decoration_company']),2);
                unset($goods[0]['attr']);
                break;

        }

        $goods[0]['quantity'] = $value['quantity'];
        $goods[0]['cost'] = $value['cost'];
        $goods[0]['procurement'] = $value['procurement'];

        return $goods[0];
    }

    /**
     * 杂工拆除
     * @param $int
     * @param $get
     * @param $day_12
     * @param $day_24
     * @return int
     */
    public static function wallArea($int,$get,$day_12,$day_24=1)
    {
        switch ($int){
            case $int == 1:
                $_12 = self::algorithm(6,$get['12_dismantle'],$day_12);
                $_24 = self::algorithm(6,$get['24_dismantle'],$day_24);
                $_day = self::algorithm(3,$_12,$_24);
                break;
            case $int == 2:
                $_12 = self::algorithm(6,$get['12_new_construction'],$day_12);
                $_24 = self::algorithm(6,$get['12_new_construction'],$day_24);
                $_day = self::algorithm(3,$_12,$_24);
                break;
            case $int == 3:
                $_day = self::algorithm(6,$get['repair'],$day_12);
                break;
        }


        return $_day;

    }

    /**
     * 杂工清运费
     * @param $int
     * @param $get
     * @param $craft
     * @param $craft1
     * @param $fare
     * @return mixed
     */
    public static function haveBuildingScrap($int,$get,$craft,$craft1=20,$fare=300)
    {
        switch ($int){
            case $int == 1:
                //  有建渣点
                $value['wall'] = round(self::algorithm(1,$get,$craft),2);
                $value['cost'] =  $value['wall'];
                break;
            case $int == 2:
                //  无建渣点
                $cost = ceil(self::algorithm(1,$get,$craft));
                $value_ = ceil(self::algorithm(6,$get,$craft1));
                $value['wall'] = round(self::algorithm(1,$value_,$fare),2);
                $value['cost'] =  self::algorithm(3,$cost,$value['wall']);

        }

        return $value;
    }



    /**
     * 条件判断
     * @param $goods
     * @param $get
     * @return array|bool
     */
    public static function judge($goods,$get)
    {

        $v = [];
        $style = Style::find()->select('style')->where(['id'=>$get['style']])->one();
        $series = Series::find()->select('series')->where(['id'=>$get['series']])->one();
        foreach ($goods as $one_goods) {
            if ($one_goods['style_name'] == $style->style
                && $one_goods['series_name'] == null){
                $v[] = $one_goods;
            }

            if ($one_goods['style_name'] == null
                && $one_goods['series_name'] == $series->series){
                $v[] = $one_goods;
            }
            if ($one_goods['style_name'] == $style->style
                && $one_goods['series_name'] == $series->series){
                $v[] = $one_goods;
            }
            if ($one_goods['style_name'] == null
                && $one_goods['series_name'] == null){
                $v[] = $one_goods;
            }
        }

        return $v;
    }


    /**
     * 利润率最大
     * @param $goods
     * @param $value
     *  $value 传值不一样 抓取规则不同 默认值为1
     * @return mixed
     */
    public static function profitMargin($goods,$value = 1)
    {
        var_dump($goods);die;
        switch ($value){
            case $value == 1:
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
                break;
            case $value == 2:
                return false;
                break;
        }

    }

    /**
     * 有计算公式商品换算
     * @param $goods
     * @param $post
     * @return mixed
     */
    public static function formula($goods,$post)
    {

        foreach ($goods as $one_goods) {
            switch ($one_goods) {
                case $one_goods['title'] == self::goodsNames()['wood_floor'] && $one_goods['series_id'] == $post['series']: // 木地板
                    //木地板面积=卧室地面积    卧室地面积=【z】%×（房屋面积） 木地板费用：个数×抓取的商品价格 个数：（木地板面积÷抓取木地板面积）
                    $goods_area = GoodsAttr::findByGoodsIdUnits($one_goods['id'],'');
                    foreach ($goods_area as $one_goods_area) {
                        if ($one_goods_area['name'] == self::UNITS['length']) {
                            $length = $one_goods_area['value'];
                        }
                        if ($one_goods_area['name'] == self::UNITS['breadth']) {
                            $breadth = $one_goods_area['value'];
                        }
                    }
                    $area = round(self::algorithm(1,$length,$breadth),2);
                    $one_goods['quantity'] = (int)ceil(self::algorithm(6,$post['bedroom_area'],$area));
                    $one_goods['cost'] = round(self::algorithm(1,$one_goods['platform_price'],$one_goods['quantity']),2);
                    $one_goods['procurement'] = round(self::algorithm(1,$one_goods['purchase_price_decoration_company'],$one_goods['quantity']),2);
                    $wood_floor [] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['marble']: // 大理石
                    if ($post['window'] > 1) {
                        $one_goods['quantity'] = $post['window'];
                        $one_goods['cost'] = round(self::algorithm(1,$one_goods['platform_price'], $one_goods['quantity']),2);
                        $one_goods['procurement'] = round(self::algorithm(1,$one_goods['purchase_price_decoration_company'],$one_goods['quantity']),2);
                        $marble [] = $one_goods;
                    }else {
                        $marble = null;
                    }
                    break;
                case $one_goods['title'] == self::goodsNames()['elbow']: // 弯头
                    $one_goods['quantity'] = (int)$post['toilet'] * 4;
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $elbow[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['timber_door'] && $one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style']: //木门
                    $one_goods['quantity'] = (int)$post['bedroom'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $timber_door[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['bath_heater'] && $one_goods['series_id'] == $post['series'] : //浴霸
                    $one_goods['quantity'] = (int)$post['toilet'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $bath_heater[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['ventilator'] && $one_goods['series_id'] == $post['series'] : //换气扇
                    $one_goods['quantity'] = (int)$post['toilet'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $ventilator[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['ceiling_light'] && $one_goods['series_id'] == $post['series'] : //吸顶灯
                    $one_goods['quantity'] = (int)$post['toilet'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $ceiling_light[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['tap'] && $one_goods['series_id'] == $post['series'] : //水龙头
                    $one_goods['quantity'] = (int)$post['toilet'] + $post['kitchen'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $tap[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['bed'] && $one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style']: //床
                    $one_goods['quantity'] = (int)$post['bedroom'] ;
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $bed[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['night_table'] && $one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style']: //床头柜
                    $one_goods['quantity'] = (int)$post['bedroom'] * 2;
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $night_table[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['kitchen_ventilator'] && $one_goods['series_id'] == $post['series'] : //抽油烟机
                    $one_goods['quantity'] = (int)$post['kitchen'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $kitchen_ventilator[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['stove'] && $one_goods['series_id'] == $post['series'] : //灶具
                    $one_goods['quantity'] = (int)$post['kitchen'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $stove[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['upright_air_conditioner'] && $one_goods['series_id'] == $post['series'] : //立柜空调
                    $one_goods['quantity'] = (int)$post['hall'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $upright_air_conditioner[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['hang_air_conditioner'] && $one_goods['series_id'] == $post['series'] : //壁挂空调
                    $one_goods['quantity'] = (int)$post['bedroom'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $hang_air_conditioner[] = $one_goods;
                    break;
//                case $one_goods['title'] == self::goodsNames()['lamp'] && $one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style'] : //灯具
////                    $attr = self::goodsAttr($one_goods, self::goodsNames()['lamp'], '', 2);
////                    $one_goods['quantity'] = (int)$post['bedroom'];
////                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
////                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
//                    $lamp[] = $one_goods;
//                    break;
                case $one_goods['title'] == self::goodsNames()['mattress'] && $one_goods['series_id'] == $post['series'] : //床垫
                    $one_goods['quantity'] = (int)$post['bedroom'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $mattress[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['closestool'] && $one_goods['series_id'] == $post['series'] : //马桶
                    $one_goods['quantity'] = (int)$post['toilet'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $closestool[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['bath_cabinet'] && $one_goods['series_id'] == $post['series'] : //浴柜
                    $one_goods['quantity'] = (int)$post['toilet'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $bath_cabinet[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['sprinkler'] && $one_goods['series_id'] == $post['series'] : //花洒套装
                    $one_goods['quantity'] = (int)$post['toilet'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $sprinkler[] = $one_goods;
                    break;
                case $one_goods['title'] == self::goodsNames()['shower_partition']: //淋浴隔断
                    $one_goods['quantity'] = (int)$post['toilet'];
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
        $style = self::style($goods_material);

        return $style;

    }

    /**
     * 有计算公式 单个商品
     * @param $goods
     * @param $post
     * @return float|null
     */
    public static function oneFormula($goods,$post)
    {

        switch ($goods){
            case $goods['title'] == self::goodsNames()['wood_floor']:  // 木地板
                $goods_area = GoodsAttr::findByGoodsIdUnits($goods['id'],'');
                foreach ($goods_area as $one_goods_area) {
                    if ($one_goods_area['name'] == self::UNITS['length']) {
                        $length = $one_goods_area['value'];
                    }
                    if ($one_goods_area['name'] == self::UNITS['breadth']) {
                        $breadth = $one_goods_area['value'];
                    }
                }
                $area = round($length * $breadth,2);
                $quantity = ceil($post['bedroom_area'] / $area);
                break;
            case $goods['title'] == self::goodsNames()['marble']:  // 大理石
                if ($post['window'] > 1) {
                    $quantity = ceil($post['window']);
                }else {
                    $quantity = null;
                }
                break;
            case $goods['title'] == self::goodsNames()['elbow']:  // 弯头
                $quantity['quantity'] = ceil($post['toilet'] * 4);
                break;
            case $goods['title'] == self::goodsNames()['timber_door']:   //木门
                $quantity = ceil($post['bedroom']);
                break;
            case $goods['title'] == self::goodsNames()['bath_heater']:   //浴霸
                $quantity= ceil($post['toilet']);
                break;
            case $goods['title'] == self::goodsNames()['ventilator']:   //换气扇
                $quantity = ceil($post['toilet']);
                break;
            case $goods['title'] == self::goodsNames()['ceiling_light']:   //吸顶灯
                $quantity= ceil($post['toilet']);
                break;
            case $goods['title'] == self::goodsNames()['tap']:   //水龙头
                $quantity = ceil($post['toilet'] + $post['kitchen']);
                break;
            case $goods['title'] == self::goodsNames()['bed']:   //床
                $quantity = ceil($post['bedroom']) ;
                break;
            case $goods['title'] == self::goodsNames()['night_table']:   //床头柜
                $quantity = ceil($post['bedroom'] * 2);
                break;
            case $goods['title'] == self::goodsNames()['kitchen_ventilator']:   //抽油烟机
                $quantity = ceil($post['kitchen']);
                break;
            case $goods['title'] == self::goodsNames()['stove']:   //灶具
                $quantity = ceil($post['kitchen']);
                break;
            case $goods['title'] == self::goodsNames()['upright_air_conditioner']:    //立柜空调
                $quantity = ceil($post['hall']);
                break;
            case $goods['title'] == self::goodsNames()['hang_air_conditioner']:   //壁挂空调
                $quantity = ceil($post['bedroom']);
                break;
            case $goods['title'] == self::goodsNames()['lamp']:   //灯具
                $quantity = ceil($post['bedroom']);
                break;
            case $goods['title'] == self::goodsNames()['mattress']:  //床垫
                $quantity = ceil($post['bedroom']);
                break;
            case $goods['title'] == self::goodsNames()['closestool']:   //马桶
                $quantity = ceil($post['toilet']);
                break;
            case $goods['title'] == self::goodsNames()['bath_cabinet']:   //浴柜
                $quantity = ceil($post['toilet']);
                break;
            case $goods['title'] == self::goodsNames()['sprinkler']:   //花洒套装
                $quantity = ceil($post['toilet']);
                break;
            case $goods['title'] == self::goodsNames()['shower_partition']:  //淋浴隔断
                $quantity = ceil($post['toilet']);
                break;
        }

        return $quantity;
    }

    /**
     *  无分类的商品
     * @param $goods
     * @param $assort_material
     * @param $post
     * @return mixed
     */
    public static function withoutAssortGoods($goods,$assort_material,$post)
    {
        foreach ($goods as &$one){
            foreach ($assort_material as $value){
                if ($one['title'] == $value['title']){
                    $one['quantity'] =(int)ceil($value['quantity']);
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
        $style = self::style($material);
        return $style;
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
                        $secondary_bedroom = self::algorithm(1,$one_points['count'],1);
                        break;
                    case $get['bedroom'] > 2:
                        $secondary_bedroom = self::algorithm(2,$one_points['count'],$get['bedroom']);
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
                $all = self::algorithm(1,$one_points['count'],$get['hall']);
            }

            // 次卧
            if ($one_points['title'] == OwnerController::ROOM_DETAIL['secondary_bedroom']){
                switch ($one_points) {
                    case $get['bedroom'] == 1:
                        $secondary_bedroom =  0;
                        break;
                    case $get['bedroom'] == 2:
                        $secondary_bedroom = self::algorithm(1,$one_points['count'],1);
                        break;
                    case $get['bedroom'] > 2:
                        $secondary_bedroom = self::algorithm(2,$one_points['count'],$get['bedroom']) ;
                        break;
                }
            }

            // 厨房
            if ($one_points['title'] == OwnerController::ROOM_DETAIL['kitchen']){
                $kitchen = self::algorithm(1,$one_points['count'],$get['kitchen']);
            }

            // 卫生间
            if ($one_points['title'] == OwnerController::ROOM_DETAIL['toilet']){
                $toilet = self::algorithm(1,$one_points['count'],$get['toilet']);;
            }


            if ($one_points['title'] != OwnerController::ROOM_DETAIL['hall'] && $one_points['title'] != OwnerController::ROOM_DETAIL['secondary_bedroom'] && $one_points['title'] != OwnerController::ROOM_DETAIL['kitchen'] && $one_points['title'] != OwnerController::ROOM_DETAIL['toilet']){
                $other +=  $one_points['count'];
            }
        }
        //  强电总点位
        $current_points = $all + $secondary_bedroom + $kitchen + $toilet + $other;

        return $current_points;
    }

    /**
     * 水路总点位
     * @param $points
     * @param $get
     * @return mixed
     */
    public static function waterwayPoints($points,$get)
    {
        $other = 0;
        foreach ($points as $one){
            switch ($one){
                case $one['title'] == OwnerController::ROOM_DETAIL['toilet']:
//                    $toilet_waterway_points = $one['count'] * $get['toilet'];
                    $toilet_waterway_points = self::algorithm(1,$one['count'],$get['toilet']);
                    break;
                case $one['title'] == OwnerController::ROOM_DETAIL['kitchen']:
                    $kitchen_waterway_points = $get['kitchen'] * $one['count'];
                    break;
                case $one['title'] != OwnerController::ROOM_DETAIL['toilet'] && $one['title'] != OwnerController::ROOM_DETAIL['kitchen']:
                    $other += $one['count'];
                    break;
            }

        }

        $waterway_count = $toilet_waterway_points + $kitchen_waterway_points + $other;

        return $waterway_count;
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


    /**
     * 计算公式
     * @param $int
     *   int 传值不一样 所用计算公式也不一样
     * @param $value
     * @param $value1
     * @param $value2
     * @param $value3
     * @return int
     */
    public static function algorithm($int,$value,$value1,$value2 = 1,$value3 = 1)
    {
        switch ($int){
            case $int == 1:
                $result = $value * $value1;
                break;
            case $int == 2:
                $result = $value * ($value1 - 1);
                break;
            case $int == 3:
                $result = $value + $value1;
                break;
            case $int == 4:
                // （点位×【10m】÷抓取的商品的长度）
                $result = $value * $value1 / $value2;
                break;
            case $int == 5:
                $result = $value + $value1 + $value2;
                break;
            case $int == 6:
                $result = $value / $value1;
                break;
            case $int == 7:
                // 造型长度÷【每天做造型长度】×系列系数1×风格系数1
                $result = $value / $value1 * $value2 * $value3;
                break;
            case $int == 8:
                // （造型天数+平顶天数）×【工人每天费用】
                $result = ($value + $value1) * $value2;
                break;
            case $int == 9:
                // （造型天数+平顶天数）×【工人每天费用】
                $result = $value * $value1 * $value2 * $value3;
                break;
            case $int == 10:
                $result = $value * $value1 * $value2;
                break;
            case $int == 11:
                $result = $value + $value1 + $value2 + $value3;
                break;
            case $int == 12:
                $result = ($value / $value1) * $value2;
                break;
            case $int == 13:
                $result = $value * $value1 + $value2;
                break;
            case $int == 14:
                $result = $value / $value1 / $value2;
                break;
        }

        return $result;
    }

    /**
     * 杂工 材料计算公式
     * @param $int
     * @param $get
     * @param $value
     * @param $value1
     * @param $value2
     * @param $goods
     * @return mixed
     */
    public static function handyman($int,$get,$value,$value1,$value2,$goods)
    {
        switch ($int){
            case $int == 1:
                $repair = self::algorithm(1,$get['repair'],$value);
                $new_12 = self::algorithm(1,$get['12_new_construction'],$value1);
                $new_24 = self::algorithm(1,$get['24_new_construction'],$value2);
                $dosage = self::algorithm(5,$repair,$new_12,$new_24);
                $max['quantity'] = (int)ceil(self::algorithm(6,$dosage,$goods[1]['value']));
                $max['cost'] = round(self::algorithm(1,$max['quantity'],$goods[0]['platform_price']),2);
                $max['procurement'] = round(self::algorithm(1,$max['quantity'],$goods[0]['purchase_price_decoration_company']),2);
                break;
            case $int == 2:
//                  空心砖费用：个数×抓取的商品价格
//                  个数：（空心砖用量）
//                  空心砖用量=12墙新建面积÷长÷高+24墙新建面积÷宽÷高
                foreach ($goods[1] as $one_goods){
                    if ($one_goods['name'] == '长'){
                        $length = $one_goods['value'];
                    }
                    if ($one_goods['name'] == '宽'){
                        $width = $one_goods['value'];
                    }
                    if ($one_goods['name'] == '高'){
                        $altitude = $one_goods['value'];
                    }
                }
                $new_12 = self::algorithm(14,$get['12_new_construction'],$length,$altitude);
                $new_24 = self::algorithm(14,$get['24_new_construction'],$width,$altitude);
                $dosage = self::algorithm(3,$new_12,$new_24);
                $max['quantity'] = (int)ceil($dosage);
                $max['cost'] = round(self::algorithm(1,$max['quantity'],$goods[0]['platform_price']),2);
                $max['procurement'] = round(self::algorithm(1,$max['quantity'],$goods[0]['purchase_price_decoration_company']),2);
                break;

        }

        $goods[0]['quantity'] = $max['quantity'];
        $goods[0]['cost'] = $max['cost'];
        $goods[0]['procurement'] = $max['procurement'];

        return $goods[0];

    }

    /**
     * 风格 系列的id转换
     * @param $goods
     * @return mixed
     */
    public static function style($goods)
    {
        foreach ($goods as &$one_goods){
            if ($one_goods['style_id'] > 0){
                $goods_style = GoodsStyle::styleIdsByGoodsId($one_goods['id']);
                $goods_style[] =  $one_goods['style_id'];
                $style = Style::find()->asArray()->select('style')->where(['in','id',$goods_style])->all();
                $style_ = [];
                foreach ($style as $one_style){
                    $style_[] = $one_style['style'];
                }
                $one_goods['style_name'] = implode('、',$style_);
                unset($one_goods['style_id']);
            }else{
                $one_goods['style_name'] = '';
                unset($one_goods['style_id']);
            }

            if ($one_goods['series_id'] > 0){
                $goods_style[] =  $one_goods['series_id'];
                $style = Series::find()->asArray()->select('series')->where(['in','id',$goods_style])->all();
                $style_ = [];
                foreach ($style as $one_style){
                    $style_[] = $one_style['series'];
                }
                $one_goods['series_name'] = implode('、',$style_);
                unset($one_goods['series_id']);
            }else{
                $one_goods['series_name'] = '';
                unset($one_goods['series_id']);
            }
        }
        return $goods;
    }

    /**
     * 腻子 底漆 面漆 阴角线  自流平 的默认数量
     * @param $goods
     * @param $get
     * @return float|int
     */
    public static function count($goods,$get)
    {
        $goods_attr = GoodsAttr::findByGoodsIdUnit($goods['id'],'');
        $craft = WorkerType::craft(OwnerController::CRAFT_NAME['oil_paint'],$get['city']);
        foreach ($craft as $one_value){
            // 腻子用量
            if ($one_value['id'] == OwnerController::ROOM['putty']){
                $putty = $one_value['material'];
            }
            // 底漆用量
            if ($one_value['id'] == OwnerController::ROOM['undercoat']){
                $undercoat = $one_value['material'];
            }
            // 面漆用量
            if ($one_value['id'] == OwnerController::ROOM['finishing']){
                $finishing = $one_value['material'];
            }
            // 阴角线用量
            if ($one_value['id'] == OwnerController::ROOM['wire']){
                $wire = $one_value['material'];
            }
        }

        $craft_ = WorkerType::craft(OwnerController::CRAFT_NAME['tiler'],$get['city']);
        foreach ($craft_ as $oneValue){
            // 自流平用量
            if ($oneValue['id'] == OwnerController::ROOM['self_leveling']){
                $self_leveling = $oneValue['material'];
            }
        }



        // 房间百分比查询
        $ratio = ProjectView::findById(68,10000);
        foreach ($ratio as $value_){
            // 客厅百分比
            if ($value_['id'] == OwnerController::ROOM['hall_area']){
                $hall_area = $value_['project_value'];
            }
            // 卧室百分比
            if ($value_['id'] == OwnerController::ROOM['bedroom_area']){
                $bedroom_area = $value_['project_value'];
            }
        }


        // 其它面积查询
        $other = Apartment::find()->asArray()->where(['points_id'=>5])->andWhere(['<=','min_area',$get['area']])->andWhere(['>=','max_area',$get['area']])->all();
        foreach ($other as $one_other){
            if ($one_other['project_name'] == '其他乳胶漆面积'){
                $v = $one_other['project_value'];
            }
//            if ($one_other['project_name'] == '其他腻子面积'){
//                $v1 = $one_other['project_value'];
//            }
            if ($one_other['project_name'] == '其他阴角线长度'){
                $v2 = $one_other['project_value'];
            }
        }

        // 底漆面积
        $hall_ = self::paintedArea($hall_area,$get['area'],$get['high'],$get['hall'],3);
        $bedroom_ = self::paintedArea($bedroom_area,$get['area'],$get['high'],$get['bedroom'],4);
        $total_area = self::algorithm(5,$hall_[0],$bedroom_[0],$v);
        // 周长
        $bedroom_length = self::paintedPerimeter($bedroom_[1],$get['bedroom'],4);
        $hall__length = self::paintedPerimeter($hall_[1],$get['hall'],3);
        $total_length = self::algorithm(5,$bedroom_length,$hall__length,$v2);




        switch ($goods['category_id']){
            case $goods['category_id'] == 38: // 腻子面积
                $value = ceil(self::algorithm(4,$total_area,$putty,$goods_attr['value']));
                break;
            case $goods['category_id']  == 24:// 底漆
                $value = ceil(self::algorithm(4,$total_area,$undercoat,$goods_attr['value']));
                break;
            case $goods['category_id']  == 25: // 面漆
                $surface_area = $total_area * 2;
                $value = ceil(self::algorithm(4,$surface_area,$finishing,$goods_attr['value']));
                break;
            case $goods['category_id']  == 28:// 阴角线
                $value = ceil(self::algorithm(4,$total_length,$wire,$goods_attr['value']));
                break;
            case $goods['category_id']  == 36: // 自流平
                $value = ceil(self::algorithm(4,$hall_[0],$self_leveling,$goods_attr['value']));
                break;
            default: // 自流平
                $value = 0;
                break;
        }

        return $value;
    }


    /**
     * 灯具 计算
     * @param $goods
     * @param $get
     * @return mixed
     */
    public static function lamp($goods,$get)
    {

        foreach ($goods as $oneGoods){

            if(strpos($oneGoods[1]['value'],'客厅') !== false) {
                $oneGoods[0]['quantity'] = (int)ceil($get['hall']);
                $oneGoods[0]['cost'] = round(self::algorithm(1,$oneGoods[0]['quantity'],$oneGoods[0]['platform_price']),2);
                $oneGoods[0]['procurement'] = round(self::algorithm(1,$oneGoods[0]['quantity'],$oneGoods[0]['purchase_price_decoration_company']),2);
                $hallLamp [] = $oneGoods[0];
            }

//
//            if(strpos($oneGoods[1]['value'],'卧室') !== false) {
//                $oneGoods[0]['quantity'] = (int)ceil($get['bedroom']);
//                $oneGoods[0]['cost'] = round(self::algorithm(1,$oneGoods[0]['quantity'],$oneGoods[0]['platform_price']),2);
//                $oneGoods[0]['procurement'] = round(self::algorithm(1,$oneGoods[0]['quantity'],$oneGoods[0]['purchase_price_decoration_company']),2);
//                $bedroomLamp[] = $oneGoods[0];
//            }else{
//                $bedroomLamp[] = [];
//            }
//
//
//            if(strpos($oneGoods[1]['value'],'卫生间') !== false) {
//                $oneGoods[0]['quantity'] = (int)ceil($get['bedroom']);
//                $oneGoods[0]['cost'] = round(self::algorithm(1,$oneGoods[0]['quantity'],$oneGoods[0]['platform_price']),2);
//                $oneGoods[0]['procurement'] = round(self::algorithm(1,$oneGoods[0]['quantity'],$oneGoods[0]['purchase_price_decoration_company']),2);
//                $toiletLamp[] = $oneGoods[0];
//            }else{
//                $toiletLamp[] = [];
//            }
//
//
//            if(strpos($oneGoods[1]['value'],'厨房') !== false) {
//                $oneGoods[0]['quantity'] = (int)ceil($get['kitchen']);
//                $oneGoods[0]['cost'] = round(self::algorithm(1,$oneGoods[0]['quantity'],$oneGoods[0]['platform_price']),2);
//                $oneGoods[0]['procurement'] = round(self::algorithm(1,$oneGoods[0]['quantity'],$oneGoods[0]['purchase_price_decoration_company']),2);
//                $kitchenLamp[] = $oneGoods[0];
//            }else{
//                $kitchenLamp[] = [];
//            }
        }

        $hallLampAll = array_filter($hallLamp);
        var_dump($hallLampAll);die;

//        $toiletLampAll = array_filter($toiletLamp);
//        $bedroomLampAll = array_filter($bedroomLamp);
//        $kitchenLampAll = array_filter($kitchenLamp);

        $goodsLamp[] = self::profitMargin($hallLampAll);
        var_dump($goodsLamp); echo 111;
        die;
        $goodsLamp[] = self::profitMargin($toiletLampAll);
        $goodsLamp[] = self::profitMargin($bedroomLampAll);
        $goodsLamp[] = self::profitMargin($kitchenLampAll);


        $style = self::style($goodsLamp);
        var_dump($style);die;

        return $style;
    }
}
