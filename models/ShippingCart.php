<?php

namespace app\models;

use Yii;
use yii\db\Exception;
use yii\db\Query;


/**
 * This is the model class for table "shipping_cart".
 *
 * @property integer $id
 * @property string $uid
 * @property string $role_id
 * @property string $goods_id
 * @property string $goods_num
 * @property integer $create_time
 */
class ShippingCart extends \yii\db\ActiveRecord 
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shipping_cart';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'role_id', 'goods_id', 'create_time'], 'required'],
            [['uid', 'role_id', 'goods_id', 'goods_num', 'create_time'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'role_id' => 'Role ID',
            'goods_id' => 'Goods ID',
            'goods_num' => 'Goods Num',
            'create_time' => 'Create Time',
        ];
    }


    /**
     * @param $user
     * @return array
     */
    public  static  function  ShippingList($user)
    {
        $data=self::find()
            ->where(['uid'=>$user->id])
            ->andWhere(['role_id'=>$user->last_role_id_app])
            ->asArray()
            ->all();
        if (!$data)
        {
            return [
                'normal_goods'=>[],
                'invalid_goods'=>[]
            ];
        }
        foreach ($data as  &$list)
        {
            $go=Goods::findOne($list['goods_id']);
            if ($go)
            {
                $supplierIds[]=$go->toArray()['supplier_id'];
            }

        }
        $supIds=[];
        foreach ($supplierIds as &$supplierId)
        {
            if (!in_array($supplierId,$supIds))
            {
                $supIds[]=$supplierId;
            }
        }
        switch ($user->last_role_id_app)
        {
            case 1:
                $money='supplier_price';
                break;
            case 6:
                $money='supplier_price';
                break;
            case 7:
                $money='platform_price';
                break;
        }
        $mix=[];
        foreach ($supIds as $supId)
        {
//           $supplier=Supplier::find()
//                ->select(['shop_name'])
//                ->where(['id'=>$supId])
//                ->one();
//            if (!$supplier)
//            {
//                $code=1000;
//                return $code;
//            }
            $Goods=(new Query())
                ->from(self::tableName().' as s')
                ->select("g.id,g.cover_image,g.title,g.{$money},g.left_number,s.goods_num,g.status")
                ->leftJoin(Goods::tableName().' as g','g.id=s.goods_id')
                ->where(['s.uid'=>$user->id])
                ->andWhere(['s.role_id'=>$user->last_role_id_app])
                ->andWhere(['g.supplier_id'=>$supId])
                ->andWhere('g.status =2')
                ->all();
            foreach ($Goods as &$list)
            {
                $list['platform_price']=GoodsOrder::switchMoney($list[$money]*0.01);
                if ($money!='platform_price')
                {
                    unset($list[$money]);
                }
            }
            if ($Goods)
            {
                $mix[]=[
                    'shop_name'=>Supplier::find()
                        ->select(['shop_name'])
                        ->where(['id'=>$supId])
                        ->one()
                        ->shop_name,
                    'goods'=>$Goods,
                ];
            }
        }

        $invalid_goods=(new Query())
            ->from(self::tableName().' as s')
            ->select("g.id,g.cover_image,g.title,g.{$money},g.left_number,s.goods_num,g.status")
            ->leftJoin(Goods::tableName().' as g','g.id=s.goods_id')
            ->where(['s.uid'=>$user->id])
            ->andWhere(['s.role_id'=>$user->last_role_id_app])
            ->andWhere('g.status !=2')
            ->all();
       foreach ($invalid_goods as &$list)
       {
           $list['platform_price']=GoodsOrder::switchMoney($list[$money]*0.01);
           if ($money!='platform_price')
           {
               unset($list[$money]);
           }
       }
        return [
            'normal_goods'=>$mix,
            'invalid_goods'=>$invalid_goods
        ];
    }


    /**
     * 删除购物车商品
     * @param array $carts
     * @return int
     */
    public static function DelShippingCartData($carts = [],$andWhere = [])
    {
        $tran = Yii::$app->db->beginTransaction();
        try{
            foreach ($carts as &$cart)
            {
                $ca=self::find()
                    ->where(['goods_id'=>$cart])
                    ->andWhere($andWhere)
                    ->one();
                if (!$ca)
                {
                    $code=1000;
                    $tran->rollBack();
                    return $code;
                }
                $res=$ca->delete();
                if (!$res)
                {
                    $code=500;
                    $tran->rollBack();
                    return $code;
                }
            }
            $tran->commit();
            $code=200;
            return $code;
        }catch (Exception $e){
            $tran->rollBack();
            $code=500;
            return $code;
        }
    }


}
