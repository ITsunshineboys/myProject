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
            return [];
        }
        foreach ($data as  &$list)
        {
            $supplierIds[]=Goods::findOne($list['goods_id'])
                ->toArray()
            ['supplier_id'];
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
        foreach ($supIds as $supId)
        {
            $mix[]=[
                'shop_name'=>Supplier::find()
                    ->select(['shop_name'])
                    ->where(['id'=>$supId])
                    ->one()
                    ->shop_name,
                'goods'=>(new Query())
                    ->from(self::tableName().' as s')
                    ->select("g.id,g.cover_image,g.title,g.{$money},g.left_number,s.goods_num,g.status")
                    ->leftJoin(Goods::tableName().' as g','g.id=s.goods_id')
                    ->where(['s.uid'=>$user->id])
                    ->andWhere(['s.role_id'=>$user->last_role_id_app])
                    ->andWhere(['g.supplier_id'=>$supId])
                    ->all(),
            ];
        }
        return $mix;
    }


    /**
     * 删除购物车商品
     * @param array $carts
     * @return int
     */
    public static function DelShippingCartData($carts = [])
    {
        $tran = Yii::$app->db->beginTransaction();
        try{
            foreach ($carts as &$cart)
            {
                $ca=self::findOne($cart);
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
