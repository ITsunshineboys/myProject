<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use app\services\StringService;
use Yii;
use yii\db\ActiveRecord;

class BrandApplicationImage extends ActiveRecord
{
    const FIELDS_AUTHORIZATION = ['authorization_name', 'image'];

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'brand_application_image';
    }

    /**
     * Add brand application images by images etc.
     *
     * @param ActiveRecord $brandApplication BrandApplication model
     * @param array $images images
     * @return int
     */
    public static function addByAttrs(ActiveRecord $brandApplication, array $images, array $authorizationNames)
    {
        $code = 1000;

        if ($authorizationNames) {
            if (StringService::checkEmptyElement($authorizationNames)
                || StringService::checkEmptyElement($images)
            ) {
                return $code;
            }
        }

        if ($images) {
            if (StringService::checkEmptyElement($images)
                || StringService::checkEmptyElement($authorizationNames)
            ) {
                return $code;
            }
        }

        foreach ($images as $i => $image) {
            $brandApplicationImage = new self;
            $brandApplicationImage->brand_application_id = $brandApplication->id;
            $brandApplicationImage->image = $image;
            $brandApplicationImage->authorization_name = $authorizationNames[$i];

            if (!$brandApplicationImage->validate()) {
                return $code;
            }

            if (!$brandApplicationImage->save()) {
                $code = 500;
                return $code;
            }
        }

        $code = 200;
        return $code;
    }

    /**
     * Get authorization info by application id
     *
     * @param $applicationId application id
     * @param array $select select fields
     * @return array
     */
    public static function findImagesByApplicationId($applicationId, array $select = self::FIELDS_AUTHORIZATION)
    {
        return self::find()
            ->select($select)
            ->where(['brand_application_id' => $applicationId])
            ->asArray()
            ->all();
    }
}