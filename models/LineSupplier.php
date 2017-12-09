<?php

namespace app\models;

use app\services\ModelService;
use Symfony\Component\DomCrawler\Field\InputFormField;
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

    const PAGE_SIZE_DEFAULT=12;

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
     */
    public static function pagination($where = [], $page = 1, $size = self::PAGE_SIZE_DEFAULT)
    {
        $orderBy = 'L.id DESC';
        $select ="S.shop_no,L.supplier_id,L.district_code,L.address,L.status,S.shop_name,S.type_shop,S.category_id,C.title,C.path,C.parent_title";
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
     * 开关线下体验店商家
     * @param $post
     * @return int
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
            ->select('id')
            ->where(['shop_no'=>$post['shop_no']])
            ->one();
        if (!$supplier)
        {
            $code=1000;
            return $code;
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
