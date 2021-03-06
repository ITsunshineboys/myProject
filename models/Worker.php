<?php

namespace app\models;

use app\services\StringService;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\Query;


/**
 * This is the model class for table "worker".
 *
 * @property integer $id
 * @property string $uid
 * @property string $project_manager_id
 * @property string $province_code
 * @property string $city_code
 * @property string $district_code
 * @property string $nickname
 * @property string $icon
 * @property string $follower_number
 * @property double $comprehensive_score
 * @property integer $feedback
 * @property string $order_total
 * @property string $order_done
 * @property integer $level
 * @property string $create_time
 * @property string $signature
 * @property string $balance
 * @property string $pay_password
 * @property string $address
 * @property integer $status
 * @property string $worker_type_id
 * @property string $skill_ids
 * @property integer $work_year
 * @property string $availableamount
 */
class Worker extends \yii\db\ActiveRecord
{
    const SK_ING=2;
    const DAI_STATUS=1;
    const WorkerRoleId=2;
    const ORDER_BEGIN=3;
    const STATUS_OFFLINE = 0;
    const STATUS_DESC_NO_REVIE='未认证';
    const STATUS_DESC_WAIT_REVIEW = '等待审核';
    const STATUS_DESC_ONLINE_APP = '已认证';
    const STATUS_DESC_NOT_APPROVED = '审核未通过';
    const STATUSES=[
        self::STATUS_OFFLINE=>self::STATUS_DESC_NO_REVIE,
        self::DAI_STATUS=>self::STATUS_DESC_WAIT_REVIEW,
        self::WorkerRoleId=>self::STATUS_DESC_ONLINE_APP,
        self::ORDER_BEGIN=>self::STATUS_DESC_NOT_APPROVED
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'worker';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'worker_type_id','province_code','city_code','level'], 'integer'],
        ];
    }

    /**
     * Get total number of workers
     *
     * @return int
     */
    public static function totalNumber()
    {
        return (int)self::find()->count();
    }

    public function beforeSave($insert)
    {
        $insert && $this->create_time = time();
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public static function setSignature($uid, $signature)
    {
        $worker = self::getWorkerByUid($uid);

        if ($signature) {
            $worker->signature = $signature;
            $worker->save(false);
        }

        return true;
    }

    public static function getWorkerByUid($uid)
    {
        return self::find()->where(['uid' => $uid])->one();
    }

    public static function getLaborByWorkerId($worker_id)
    {
        $worker = self::find()->where(['id' => $worker_id])->one();
        $labor_cost_id = (int)$worker->labor_cost_id;
        return LaborCost::find()->where(['id' => $labor_cost_id])->one();
    }
    /**
     * 获取工程订单 智管工地 数量
     * @param $user_id
     * @return array|int
     */
    public static function getordertypebystatus($user_id){
        $data=[];
        $worker_id=Worker::getWorkerByUid($user_id)->id;

        if(!$worker_id){
            $code=1000;
            return $code;
        }
        $count_worker_order=WorkerOrder::find()
            ->select('order_no')
            ->where(['uid'=>$user_id,'worker_id'=>$worker_id,'is_old'=>self::STATUS_OFFLINE])
            ->asArray()
            ->count();
        $count_worker_place=WorkerOrder::find()
            ->select('order_no')
            ->where(['uid'=>$user_id])
            ->andWhere(['worker_id'=>$worker_id])
            ->andWhere(['status'=>self::SK_ING])
            ->andWhere(['is_old'=>self::STATUS_OFFLINE])
            ->asArray()
            ->count();

        $data['worker_count_order']=$count_worker_order;
        $data['worker_order_place']=$count_worker_place;
        return $data;
    }
    /**
     * 工人账户详情
     * @param $uid
     * @return array|bool|null
     */
    public static function getWorkerAccount($uid){
        $query=new Query();
        $array=$query
            ->from('worker as w')
            ->select('w.nickname,w.icon,w.worker_type_id,w.examine_status,w.comprehensive_score,wr.rank_name,u.aite_cube_no')
            ->leftJoin('user as u','w.uid=u.id')
            ->leftJoin('worker_rank as wr','wr.id=w.level')
            ->where(['w.uid'=>$uid])
            ->one();
            $array['worker_type_id']=WorkerService::getparenttype($array['worker_type_id']);
            $array['examine_status']=self::STATUSES[$array['examine_status']];
            $array['worker_no']=$array['aite_cube_no'];
        if(!$array){
            return null;
        }
        return $array;
    }
    /**
     * 工人实名认证
     * @param array $post
     * @param $uid
     * @param ActiveRecord|null $operator
     * @return int
     */
    public static function Certification(array $post,$uid,ActiveRecord $operator = null){
        $worker=Worker::find()->where(['uid'=>$uid])->one();
        $worker->worker_type_id=$post['worker_type_id'];
        $worker->province_code=$post['province_code'];
        $worker->city_code=$post['city_code'];
        $worker->uid=$uid;
        $worker->work_year=$post['work_year'];
        $worker->nickname=$post['nickname'];
        $worker->examine_status=self::DAI_STATUS;
        $transaction = Yii::$app->db->beginTransaction();
        try{
            if(!$worker->save(false)){
                $transaction->rollBack();
                $code = 500;
                return $code;
            }

            if($post['identity_no'] && !StringService::checkIdentityCardNo($post['identity_no'])){
                $transaction->rollBack();
                $code = 1000;
                return $code;
            }
            $user=User::find()->where(['id'=>$uid])->one();
            $user->identity_no=$post['identity_no'];
            $user->identity_card_front_image=$post['identity_card_front_image'];
            $user->identity_card_back_image=$post['identity_card_back_image'];
            if(!$user->save(false)){
                $transaction->rollBack();
                $code = 500;
                return $code;
            }

            if (!UserRole::addUserRole($uid, Yii::$app->params['workerRoleId'], $operator)) {
                $transaction->rollBack();
                $code = 500;
                return $code;
            }

            $transaction->commit();
            $code=200;
            return $code;
        }catch (Exception $e){
            $transaction->rollBack();
            $code = 500;
            return $code;
        }
    }
    /**
     * 工人详情
     * @param $worker_id
     * @return array|bool|null
     */
    public static function workerinfos($worker_id){
        $query=new Query();
        $array=$query->from('worker ')
            ->select('uid,worker_type_id,nickname,province_code,city_code,work_year,feedback,signature,skill_ids,order_done,level,')
            ->where(['id'=>$worker_id])
            ->one();
        if($array){
            $array['province']=District::findByCode($array['province_code'])['name'];
            $array['city']=District::findByCode($array['city_code'])['name'];
            $worker_type=WorkerService::getparenttype($array['worker_type_id']);

            $rank=WorkerRank::find()->where(['id'=>$array['level']])->one()->rank_name;
            $array['worker_type_rank']=$rank.$worker_type;
            unset($array['worker_type_id']);
            unset($array['skill_ids']);
            unset($array['province_code']);
            unset($array['city_code']);
            unset($array['level']);
            $skills=WorkerSkill::getWorkerSkillname($array['uid']);
            foreach ($skills as $k=>&$vule){
                $array['skills'][$k]=$vule;
            }
           return $array;
        }else{
            return null;
        }

    }
    /**
     * 工人本月收入
     * @param $uid
     * @return mixed|string
     */
    public static function worker_monthly_income($uid){
        $worker_id=Worker::find()->asArray()->where(['uid'=>$uid])->one()['id'];
        list($start_time,$end_time)=StringService::startEndDate('month');
        $start_time=(int)strtotime($start_time);
        $end_time=(int)strtotime($end_time);
        $income=WorkerOrder::find()
            ->where(['worker_id'=>$worker_id,'status'=>5,'is_old'=>1])
            ->andWhere('end_time >='.$start_time and 'end_time <='.$end_time)
            ->asArray()
            ->sum('amount');
        $income=sprintf('%.2f',(float)$income*0.01);
        return $income;
    }

    /**
     * 统计本月的工人开工天数
     * @param $worker_id
     * @return int
     */
    public static function worker_start_days($worker_id){
        $time_type = 'month';
        list($start_time,$end_time)= StringService::startEndDate($time_type);
        $data = WorkerOrder::getWorkDaysByTimeArea($worker_id, $start_time,$end_time);
        return count($data);

    }

    public static function findByCode($where = [],$size,$page)
    {
        $offset = ($page - 1) * $size;

        $select = 'worker.order_total,worker.create_time,worker.status,user.legal_person,user.mobile,user.aite_cube_no,worker_type.worker_name';
        $details = self::find()
            ->select($select)
            ->where($where)
            ->leftJoin('user','user.id = worker.uid')
            ->leftJoin('worker_type','worker_type.id = worker.worker_type_id')
            ->offset($offset)
            ->limit($size)
            ->groupBy('user.mobile')
            ->asArray()
            ->all();


        foreach ($details as &$one_details){
            $one_details['create_time'] = date('Y-m-d H:i',$one_details['create_time']);
        }

        return [
            'total' => (int)self::find()->where($where)->asArray()->count(),
            'page'  => $page,
            'size'  => $size,
            'details' => $details
        ];
    }

    /**
     * 基本信息查询
     * @param array $select
     * @param array $where
     * @return array|null|ActiveRecord
     */
    public static function basicMessage($where = [])
    {
        // 还差一个工号没查询
        $select = "worker.icon,worker.native_place,worker.status,user.mobile,user.aite_cube_no,user.username,user.create_time,user_role.review_time";
        $message = self::find()
            ->select($select)
            ->where($where)
            ->leftJoin('user','user.id = worker.uid')
            ->leftJoin('user_role','user_role.user_id = user.id')
            ->asArray()
            ->one();
        $message['create_time'] = date('Y-m-d H:i',$message['create_time']);
        $message['review_time'] = date('Y-m-d H:i',$message['review_time']);

        return $message;
    }

    /**
     * 角色信息 查询
     * @param array $where
     * @return array|null|ActiveRecord
     */
    public static function roleMessage($where = [])
    {
        // 工程质量 服务态度  出勤打卡  没查询
        $select = "wt.worker_name,wr.rank_name,wg.growth_value,worker.province_code,worker.city_code,ws.skill,ur.review_status,worker.comprehensive_score,worker.feedback";


        $message = self::find()
            ->select($select)
            ->where($where)
            ->leftJoin('worker_type as wt','worker.worker_type_id = wt.id')
            ->leftJoin('worker_rank as wr','wt.id = wr.worker_type_id')
            ->leftJoin('worker_growth as wg','worker.id = wg.worker_id')
            ->leftJoin('worker_skill as ws','worker.skill_ids = ws.id')
            ->leftJoin('user as u','worker.uid = u.id')
            ->leftJoin('user_role as ur','ur.user_id = u.id')
            ->asArray()
            ->one();


        $province = District::findByCode($message['province_code']);
        $city = District::findByCode($message['city_code']);
        unset($message['province_code']);
        unset($message['city_code']);
        $message['province'] = $province->name;
        $message['city'] = $city->name;

        return $message;
    }


}
