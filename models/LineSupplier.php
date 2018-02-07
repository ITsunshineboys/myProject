<?php

namespace app\models;

use app\services\ModelService;
use Yii;
use yii\db\Exception;
use yii\db\Query;

/**
 * This is the model class for table "line_supplier".
 *
 * @property integer $id
 * @property integer $supplier_id
 * @property integer $district_code
 * @property string $address
 * @property integer $status
 * @property integer $create_time
 * @property integer $mobile
 */
class LineSupplier extends \yii\db\ActiveRecord
{

    const STATUS_ON_LINE=2;
    const STATUS_OFF_LINE=1;
    const PAGE_SIZE_DEFAULT=12;
    const  LINE_USER='线下店购买用户';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'line_supplier';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['supplier_id', 'district_code', 'address', 'create_time','mobile'], 'required'],
            [['supplier_id', 'district_code', 'status', 'create_time','mobile'], 'integer'],
            [['address'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'supplier_id' => 'Supplier ID',
            'district_code' => 'District Code',
            'address' => 'Address',
            'status' => 'Status',
            'create_time' => 'Create Time',
            'mobile'=>'Mobile'
        ];
    }

    /**
     * @param array $where
     * @param int $page
     * @param int $size
     * @return array
     * @throws Exception
     */
    public static function pagination($where = [], $page = 1, $size = self::PAGE_SIZE_DEFAULT)
    {
        $orderBy = 'L.id DESC';
        $select ="S.shop_no,L.supplier_id,L.district_code,L.address,L.status,S.shop_name,S.type_shop,S.category_id,C.title,C.path,C.parent_title,S.status as type,L.id as line_supplier_id";
        $offset = ($page - 1) * $size;
        $List = (new Query())
            ->from(self::tableName().' as L')
            ->leftJoin(Supplier::tableName().' as S','L.supplier_id=S.id')
            ->leftJoin(GoodsCategory::tableName().' as C','C.id=S.category_id')
            ->select($select)
            ->where($where)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->all();

        foreach ($List as &$list)
        {
                $list['type_shop']=Supplier::TYPE_SHOP[$list['type_shop']];
                $category_arr=explode(',',$list['path']);
                $first_category=GoodsCategory::find()
                    ->select('path,title,parent_title')
                    ->where(['id'=>$category_arr[0]])
                    ->one();
                $list['category']=$first_category->title.'-'.$list['parent_title'].'-'.$list['title'];
                $list['district']=LogisticsDistrict::GetLineDistrictByDistrictCode($list['district_code']);
            if ($list['status']== self::STATUS_ON_LINE )
            {
                if (
                    $list['type']!=Supplier::STATUS_ONLINE
                    && $list['type']!=Supplier::STATUS_OFFLINE
                    && $list['type']!=Supplier::STATUS_APPROVED
                )
                {
                    $list['status']=self::STATUS_OFF_LINE;
                    self::closeLineSupplier($list['line_supplier_id']);
                }
            }
                unset($list['line_supplier_id']);
                unset($list['type']);
                unset($list['title']);
                unset($list['path']);
                unset($list['parent_title']);
                unset($list['category_id']);
        }
        $total=(new Query())
            ->from(self::tableName().' as L')
            ->leftJoin(Supplier::tableName().' as S','L.supplier_id=S.id')
            ->select($select)
            ->where($where)
            ->count();
        return ModelService::pageDeal($List, $total, $page, $size);
    }

    /**
     * @param $line_supplier_id
     * @return int
     * @throws Exception
     */
    public  static  function  closeLineSupplier($line_supplier_id)
    {
        $tran = Yii::$app->db->beginTransaction();
        try{
            $LineSupplier=self::findOne($line_supplier_id);
            $LineSupplier->status=self::STATUS_OFF_LINE;
            if (!$LineSupplier->save(false))
            {
                $tran->rollBack();
                $code=500;
                return $code;
            }
            $tran->commit();
            $code=200;
            return $code;
        }catch (\Exception $e){
            $tran->rollBack();
            $code=500;
            return $code;
        }
    }

    /**
     * 开关线下体验店商家
     * @param $post
     * @return int
     * @throws Exception
     */
    public  static  function  SwitchLineSupplierStatus($post)
    {
        if (
            !array_key_exists('status',$post)
            ||!array_key_exists('shop_no',$post)
        )
        {
            $code=1000;
            return $code;
        }
        if ((int)$post['status']!==1 && (int)$post['status']!==2)
        {
            $code=1000;
            return $code;
        }
        $supplier=Supplier::find()
            ->select('id,status')
            ->where(['shop_no'=>$post['shop_no']])
            ->one();
        if (!$supplier)
        {
            $code=1000;
            return $code;
        }
        if ($supplier->status!= Supplier::STATUS_OFFLINE && $supplier->status!=Supplier::STATUS_ONLINE
            && $supplier->status!=Supplier::STATUS_APPROVED)
        {
            $code=1089;
            return $code;
        }
       if ($supplier->status!=Supplier::STATUS_ONLINE)
       {
           if ((int)$post['status']==self::STATUS_ON_LINE)
           {
               $code=1107;
               return $code;
           }
       }
        $LineSupplier=self::find()
            ->where(['supplier_id'=>$supplier->id])
            ->one();
        if (!$LineSupplier)
        {
            $code=1000;
            return $code;
        }

        $tran = Yii::$app->db->beginTransaction();
        try{
            $LineSupplier->status=(int)$post['status'];
            if (!$LineSupplier->save(false))
            {
                $code=500;
                $tran->rollBack();
                return $code;
            }
            if ($post['status']==self::STATUS_OFF_LINE)
            {
                $where='line_supplier_id='.$LineSupplier->id;
                LineSupplierGoods::updateAll(['status'=>LineSupplierGoods::STATUS_OFF_LINE],$where);
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


    /**
     * @param $district_code
     * @return array
     */
    public  static  function  FindLineSupplierByDistrictCode($district_code,$keyword)
    {
        $data=(new Query())
            ->from(self::tableName().' as L')
            ->select('S.shop_name,L.district_code,L.mobile,L.address,L.id as line_id')
            ->leftJoin(Supplier::tableName().' as S','S.id=L.supplier_id')
            ->where(" L.district_code  like '%{$district_code}%'")
            ->andWhere(" S.shop_name  like '%{$keyword}%'")
            ->all();
        foreach ($data as &$list)
        {
            $list['district']=LogisticsDistrict::getdistrict($list['district_code']).$list['address'];
            unset($list['address']);
            unset($list['district_code']);
        }
        return $data;

    }


    /**
     * 删除线下体验店商家
     * @param $shop_no
     * @return int
     */
    public  static  function  DelLineSupplier($shop_no)
    {
        $supplier=Supplier::find()->where(['shop_no'=>$shop_no])->one();
        if (!$supplier)
        {
            $code=1000;
            return $code;
        }
        $lineSupplier=LineSupplier::find()->where(['supplier_id'=>$supplier->id])->one();
        if (!$lineSupplier)
        {
            $code=1000;
            return $code;
        }
        $tran = Yii::$app->db->beginTransaction();
        try{
            $lineSupplierGoods=LineSupplierGoods::find()->where(['line_supplier_id'=>$lineSupplier->id])->all();
            foreach ($lineSupplierGoods as &$list)
            {
                $res=$list->delete();
                if (!$res)
                {
                    $tran->rollBack();
                    $code=500;
                    return $code;
                }
            }
            $res1=$lineSupplier->delete();
            if (!$res1)
            {
                $code=500;
                return $code;
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


    public  static  function  _extraData($viewData)
    {
        $data=self::find()
            ->where(['supplier_id'=>$viewData['id']])
            ->asArray()
            ->one();
        if ($data)
        {
            $viewData['is_offline_shop']='是';
            $viewData['line_district']=LogisticsDistrict::GetLineDistrictByDistrictCode($data['district_code']).'-'.$data['address'];
            $viewData['line_mobile']=$data['mobile'];
        }else{
            $viewData['is_offline_shop']='否';
            $viewData['line_district']='';
            $viewData['line_mobile']='';
        }
        return $viewData;

    }
}
