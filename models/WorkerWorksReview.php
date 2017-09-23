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
 * @property string $review
 */
class WorkerWorksReview extends \yii\db\ActiveRecord
{
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
            [['works_id', 'star', 'uid', 'role_id'], 'integer'],
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

    public static function getOwenerPLone($worker_id){
        $query=(new Query())->from('worker_works_review as wwr')
            ->select('wwr.*,u.nickname,u.icon,r.name,')
            ->leftJoin('user as u','wwr.uid=u.id')
            ->leftJoin('role as r','r.id=wwr.role_id')
            ->leftJoin('worker_works as ww','ww.id=wwr.works_id')
            ->where(['ww.worker_id'=>$worker_id])
            ->orderBy('id Desc')
            ->one();
        $query['resview_count']=count(self::find()->where(['works_id'=>$query['works_id']])->all());
        $query['create_time']=date('Y-m-d',$query['create_time']);
        if($query){
            unset($query['id']);
            unset($query['works_id']);
            unset($query['uid']);
            unset($query['role_id']);
            return $query;
        }else{
            return null;
        }
    }
}
