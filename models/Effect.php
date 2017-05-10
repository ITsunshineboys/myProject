<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/8 0008
 * Time: 下午 14:34
 */
namespace app\models;

use yii\db\ActiveRecord;

class Effect extends ActiveRecord
{
    public $province;
    public $city;
    public $district;
    public $street;
    public $toponymy;
    public $area;
    public $high;
    public $room;
    public $hall;
    public $toilet;
    public $kitchen;
    public $window;
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'effect';
    }

    public function rules()
    {
        return [
            [['province', 'city','district','street','toponymy','area','high','room','hall','toilet','kitchen','window'], 'required'],
            [['province', 'city','district','street','toponymy'],'string'],
            [['area','high','room','hall','toilet','kitchen','window'],'number']
        ];
    }
    /**
     * @param $toponymy
     * @param $street
     * @return array|ActiveRecord[]
     */
    public function districtSearch($search = '')
    {
        if (!empty($search))
        {
            $detail = $this->find()->where( ['or',['like','toponymy',$search],['like','street',$search]])->all() ;
        }else{
            echo '传入的值有错';
            exit;
        }

        return $detail;
    }
}