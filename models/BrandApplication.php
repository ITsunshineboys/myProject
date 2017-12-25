<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use app\services\ModelService;
use Yii;
use yii\db\ActiveRecord;

class BrandApplication extends ActiveRecord
{
    const PAGE_SIZE_DEFAULT = 12;
    /**
     * @var array admin fields
     */
    const FIELDS_ADMIN = [
        'id',
        'brand_name',
        'create_time',
        'authorization_start',
        'authorization_end',
        'review_status',
        'review_note',
        'category_title',
        'supplier_name',
        'mobile',
        'review_time',
        'images',
    ];

    /**
     * @var array admin fields
     */
    const FIELDS_REVIEW_ADMIN = [
        'id',
        'brand_name',
        'brand_logo',
        'create_time',
        'authorization_start',
        'authorization_end',
        'review_status',
        'review_note',
        'category_title',
        'supplier_name',
        'mobile',
//        'review_time',
        'images',
    ];
    const FIELDS_EXTRA = ['images'];

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'brand_application';
    }

    /**
     * Add brand application by attributes
     *
     * @param ActiveRecord $user
     * @param $attrs brand application attributes
     * @return BrandApplication|int
     */
    public static function addByAttrs(ActiveRecord $user, $attrs)
    {
        $supplier = Supplier::find()->where(['uid' => $user->id])->one();

        $brandApplication = new self;
        $brandApplication->attributes = $attrs;
        $brandApplication->authorization_start = strtotime($brandApplication->authorization_start);
        $brandApplication->authorization_end = strtotime($brandApplication->authorization_end);
        $brandApplication->mobile = $user->mobile;
        $brandApplication->supplier_id = $supplier->id;
        $brandApplication->supplier_name = $supplier->shop_name;
        $brandApplication->create_time = time();

        if (!$brandApplication->validate()) {
            $code = 1000;
            return $code;
        }

        $category = GoodsCategory::findOne($brandApplication->category_id);
        $brand = GoodsBrand::findOne($brandApplication->brand_id);

        $brandApplication->brand_name = $brand->name;
        $brandApplication->brand_logo = $brand->logo;
        $brandApplication->category_title = $category->fullTitle();
        if (!$brandApplication->save()) {
            $code = 500;
            return $code;
        }

        return $brandApplication;
    }

    /**
     * Get brand application list
     *
     * @param  array $where search condition
     * @param  array $select select fields default all fields
     * @param  int $page page number default 1
     * @param  int $size page size default 12
     * @param  string $orderBy order by fields default id desc
     * @return array
     */
    public static function pagination($where = [], $select = [], $page = 1, $size = self::PAGE_SIZE_DEFAULT, $orderBy = 'id DESC')
    {
        $selectOld = $select;

        $select = array_diff($select, self::FIELDS_EXTRA);

        $offset = ($page - 1) * $size;
        $brandApplicationList = self::find()
            ->select($select)
            ->where($where)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->asArray()
            ->all();

        foreach ($brandApplicationList as &$brandApplication) {
            if (isset($brandApplication['create_time'])) {
                $brandApplication['create_time'] = date('Y-m-d H:i', $brandApplication['create_time']);
            }

            if (isset($brandApplication['review_time'])) {
                $brandApplication['review_time'] = $brandApplication['review_time']
                    ? date('Y-m-d H:i', $brandApplication['review_time'])
                    : '';
            }

            if (isset($brandApplication['authorization_start'])) {
                $brandApplication['authorization_start'] = date('Y-m-d', $brandApplication['authorization_start']);
            }

            if (isset($brandApplication['authorization_end'])) {
                $brandApplication['authorization_end'] = date('Y-m-d', $brandApplication['authorization_end']);
            }

            if (isset($brandApplication['review_status'])) {
                $brandApplication['review_status' . ModelService::SUFFIX_FIELD_DESCRIPTION] = Yii::$app->params['reviewStatuses'][$brandApplication['review_status']];
            }

            if (in_array('images', $selectOld)) {
                $brandApplication['images'] = BrandApplicationImage::findImagesByApplicationId($brandApplication['id']);
            }
        }

        return $brandApplicationList;
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['category_id', 'brand_id', 'authorization_start', 'authorization_end'], 'required'],
            [['brand_id'], 'validateBrandId'],
            [['category_id'], 'validateCategoryId'],
            [['review_status'], 'in', 'range' => ModelService::REVIEW_STATUSES, 'on' => ModelService::SCENARIO_REVIEW],
        ];
    }

    /**
     * Validates brand_id
     *
     * @param string $attribute brand_id to validate
     * @return bool
     */
    public function validateBrandId($attribute)
    {
        if ($this->$attribute > 0
            && GoodsBrand::find()->where(['id' => $this->$attribute, 'status' => GoodsBrand::STATUS_ONLINE])->exists()
        ) {
            return true;
        }

        $this->addError($attribute);
        return false;
    }

    /**
     * Validates category_id
     *
     * @param string $attribute category_id to validate
     * @return bool
     */
    public function validateCategoryId($attribute)
    {
        $where = [
            'id' => $this->$attribute,
            'deleted' => GoodsCategory::STATUS_OFFLINE,
            'level' => GoodsCategory::LEVEL3
        ];

        if ($this->$attribute > 0
            && GoodsCategory::find()->where($where)->exists()
        ) {
            return true;
        }

        $this->addError($attribute);
        return false;
    }
}