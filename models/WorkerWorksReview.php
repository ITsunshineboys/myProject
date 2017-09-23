<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "worker_works_review".
 *
 * @property integer $id
 * @property integer $works_id
 * @property integer $star
 * @property integer $uid
 * @property integer $role_id
 * @property integer $create_time
 * @property string $review
 */
class WorkerWorksReview extends \yii\db\ActiveRecord
{
    const SIZE=2;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'worker_works_review';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['works_id', 'star', 'uid', 'role_id', 'create_time'], 'integer'],
            [['review'], 'string', 'max' => 350],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'works_id' => '工人作品id',
            'star' => '作品评分',
            'uid' => '用户id',
            'role_id' => '用户角色id',
            'review' => '评论内容',
        ];
    }

    public function beforeSave($insert)
    {
        $insert && $this->create_time = time();
        return parent::beforeSave($insert);
    }

    public static function getOwenerPLone($worker_id){
        $query=(new Query())->from('worker_works_review as wwr')
            ->select('wwr.*,u.nickname,u.icon,r.name,')
            ->leftJoin('user as u','wwr.uid=u.id')
            ->leftJoin('role as r','r.id=wwr.role_id')
            ->leftJoin('worker_works as ww','ww.id=wwr.works_id')
            ->where(['ww.worker_id'=>$worker_id])
            ->orderBy('create_time Desc')
            ->limit(self::SIZE)
            ->all();
        $data=[];
        if($query){
            foreach ($query as $k=>&$value){
                $resview_count=count(self::find()->where(['works_id'=>$value['works_id']])->all());
                $value['create_time']=date('Y-m-d',$value['create_time']);

                unset($value['id']);
                unset($value['works_id']);
                unset($value['uid']);
                unset($value['role_id']);
                $data['resview_count']=$resview_count;
                $data[]=$value;

            }
            return $data;
        }else{
            return null;
        }
    }
}
