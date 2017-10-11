<?php

namespace app\models;

use app\services\ModelService;
use Yii;
use yii\data\Pagination;
use yii\db\Query;

/**
 * This is the model class for table "worker_works_review".
 *
 * @property integer $id
 * @property integer $works_id
 * @property integer $pid
 * @property integer $star
 * @property integer $uid
 * @property integer $role_id
 * @property integer $create_time
 * @property string $review
 */
class WorkerWorksReview extends \yii\db\ActiveRecord
{
    const VIEW_SIZE=5;
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
            [['review'], 'string', 'max' => 70],
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

    /**
     * 工人详情页
     * @param $worker_id
     * @return array|null
     */
    public static function getOwenerPLone($worker_id){
        $query=(new Query())->from('worker_works_review as wwr')
            ->select('wwr.*,u.nickname,u.icon,r.name,')
            ->leftJoin('user as u','wwr.uid=u.id')
            ->leftJoin('role as r','r.id=wwr.role_id')
            ->leftJoin('worker_works as ww','ww.id=wwr.works_id')
            ->where(['ww.worker_id'=>$worker_id])
            ->andWhere(['wwr.pid'=>WorkerWorks::STUAT_LIN])
            ->orderBy('create_time Desc')
            ->all();
        $resview_count=count($query);
        $data=[];
        $data['resview_count']=$resview_count;
        if($query){
            $query=array_slice($query,0,2);//取最近评论2条数据
            foreach ($query as $k=>&$value){
                $value['create_time']=date('Y-n-j',$value['create_time']);
                $value['worker_reply']=self::find()->asArray()->select('review')->where(['works_id'=>$value['works_id']])->andWhere(['pid'=>$value['id']])->one()['review'];
                unset($value['uid']);
                unset($value['role_id']);
                $data[]=$value;
            }
            return $data;
        }else{
            return null;
        }
    }
    /**
     * 获取工人所有作品评论
     * @param $worker_id
     * @param int $page
     * @param int $size
     * @return array
     */
    public static function getworkerallviews($worker_id,$page=1,$size=self::VIEW_SIZE){
        $query=(new Query())
            ->select('wr.*')
            ->from('worker_works_review as wr')
            ->leftJoin('worker_works as ww','ww.id=wr.works_id')
            ->where(['wr.pid'=>WorkerWorks::STUAT_LIN])
            ->andWhere(['ww.worker_id'=>$worker_id]);
            $count = $query->count();
         $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $size, 'pageSizeParam' => false]);
        $arr = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        if($arr){
            foreach ($arr as &$value){
                $value['role']=Role::find()->asArray()->where(['id'=>$value['role_id']])->one()['name'];
                $value['icon']=User::find()->asArray()->where(['id'=>$value['uid']])->one()['icon'];
                $value['nickname']=User::find()->asArray()->where(['id'=>$value['uid']])->one()['nickname'];
                $value['worker_reply']=WorkerWorksReview::find()->asArray()->where(['pid'=>$value['id']])->one()['review'];
                unset($value['uid']);
                unset($value['role_id']);
            }
        }else{
            $arr='';
        }
      return  $data = ModelService::pageDeal($arr, $count, $page, $size);


    }
    /**
     * 工人回复评论添加
     * @param $uid
     * @param $view_id
     * @param $review
     * @param $works_id
     * @return int
     */
    public static function WorkerRelpy($uid,$view_id,$review,$works_id){
        $role_id=User::find()
            ->select('last_role_id_app')
            ->where(['id'=>$uid])
            ->one()
            ->last_role_id_app;
        if(!$role_id){
            $code=1000;
            return $code;
        }
        $worker_review=new WorkerWorksReview();
        $worker_review->role_id=$role_id;
        $worker_review->uid=$uid;
        $worker_review->works_id=$works_id;
        $worker_review->pid=$view_id;
        $worker_review->review=$review;
        if(!$worker_review->validate()){
            $code=1000;
            return $code;
        }
        if(!$worker_review->save()){
            $code=500;
            return $code;
        }
        return 200;

    }
}
