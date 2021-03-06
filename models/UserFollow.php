<?php

namespace app\models;

use app\services\ModelService;
use Yii;
use yii\data\Pagination;
use yii\db\Exception;
use yii\db\Query;

/**
 * This is the model class for table "user_follow".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $role_id
 * @property integer $follow_id
 * @property string $follow_time
 * @property string $unfollow_time
 * @property integer $status
 */
class UserFollow extends \yii\db\ActiveRecord
{

    const USER_FOLLOW = 'user_follow';
    const DESIGNER = 'designer';
    const DECO_COM = 'decoration_company';
    const SUPPLIER = 'supplier';
    const TABLE_BY_ROLE_ID = [
        3 => self::DESIGNER,
        5 => self::DECO_COM,
        6 => self::SUPPLIER
    ];
    const  UN_FOLLOW=0;
    const  HAVE_FOLLOW=1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_follow';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'role_id', 'follow_id', 'follow_time', 'unfollow_time', 'status'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'role_id' => '3: 设计师 5: 装修公司 6: 店铺',
            'follow_id' => 'Follow ID',
            'follow_time' => '关注时间',
            'unfollow_time' => '取关时间',
            'status' => '0: 不关注 1: 关注 ',
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->follow_time = time();
            $this->status = 1;
        }
        return parent::beforeSave($insert);
    }

    /**
     * @param $user
     * @param $role_id
     * @param $page
     * @param $page_size
     * @return array
     */
    public static function getFollowList($user, $role_id, $page, $page_size)
    {
        $table = self::TABLE_BY_ROLE_ID[$role_id];
        $nickname = 'nickname';
        if ($table == 'supplier')
        {
            $nickname = 'shop_name';
        }

        $query = (new Query())
            ->from(self::tableName() . ' as  u')
            ->leftJoin($table . ' as  f', 'u.follow_id = f.id')
            ->select(['u.id', 'u.follow_id', 'u.role_id', 'u.status', 'f.' . $nickname . ' as nickname', 'f.icon', 'f.follower_number','u.follow_id as supplier_id'])
            ->where(['u.role_id' => $user->last_role_id_app,'u.user_follow_role_id'=>$role_id, 'u.status' => 1, 'u.user_id' => $user->id]);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $page_size, 'pageSizeParam' => false]);
        $arr = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return ModelService::pageDeal($arr, $count, $page, $page_size);
    }

    /**
     * @param $id
     * @return int
     * @throws Exception
     * @throws \Exception
     */
    public static function toggleStatus($id)
    {

        $follow = self::findOne($id);
        if (!$follow)
        {
            return 1000;
        }

        $user_follow_role_id = (int)$follow->user_follow_role_id;
        if (!array_key_exists($user_follow_role_id, self::TABLE_BY_ROLE_ID))       {
            return 1000;
        }
        $table = Role::CheckUserRole($user_follow_role_id)
            ->where(['id'=>$follow->follow_id])
            ->one();
        $status=$follow->status;
        if ($status) {
            $follow->status = 0;
            $follow->unfollow_time = time();
            $table->follower_number -= 1;
        } else {
            $follow->status = 1;
            $follow->unfollow_time = 0;
            $follow->follow_time = time();
            $table->follower_number += 1;
        }
        $tran = Yii::$app->db->beginTransaction();
        try {
            if (!$follow->save(false))
            {
                $tran->rollBack();
            }
            if (!$table->save(false))
            {
                $tran->rollBack();
            }
            $tran->commit();
            return 200;
        } catch (\Exception $e) {
            $tran->rollBack();
            return 1000;
        }
    }


    /**
     * @param $user
     * @param $supplier_id
     * @param $status
     * @return int
     * @throws Exception
     */
    public static  function UserFlowShop($user,$supplier_id,$status)
    {
            $time=time();
            $supplier=Supplier::findOne($supplier_id);
            if (!$supplier)
            {
                $code=1000;
                return $code;
            }
            $tran = Yii::$app->db->beginTransaction();
            try{
                $follow=self::find()
                    ->where(['user_id'=>$user->id,'role_id'=>$user->last_role_id_app])
                    ->andWhere(['follow_id'=>$supplier_id])
                    ->andWhere(['user_follow_role_id'=>\Yii::$app->params['supplierRoleId']])
                    ->one();
                if (!$follow)
                {
                    if ($status==self::UN_FOLLOW)
                    {
                        $tran->rollBack();
                        $code=1000;
                        return $code;
                    }
                    $follow=new self();
                    $follow->status=$status;
                    $follow->user_id=$user->id;
                    $follow->role_id=$user->last_role_id_app;
                    $follow->follow_id=$supplier_id;
                    $follow->user_follow_role_id=\Yii::$app->params['supplierRoleId'];
                    if ($status==self::HAVE_FOLLOW)
                    {
                        $supplier->follower_number+=1;
                        $follow->follow_time=$time;
                    }
                }else
                {

                    if ($follow->status==$status)
                    {
                        if ($status==self::UN_FOLLOW)
                        {
                            $follow->unfollow_time=$time;
                        }else{
                            $follow->follow_time=$time;
                        }
                    }else
                    {
                        if ($status==self::UN_FOLLOW)
                        {
                            $supplier->follower_number-=1;
                            $follow->unfollow_time=$time;
                        }else{
                            $supplier->follower_number+=1;
                            $follow->follow_time=$time;
                        }
                    }
                    $follow->status=$status;
                }


                if (!$follow->save(false))
                {
                    $tran->rollBack();
                }
                if (!$supplier->save(false))
                {
                    $tran->rollBack();
                }
                $tran->commit();
                return 200;
            }catch (Exception $e){
                $tran->rollBack();
                $code=500;
                return $code;
            }
    }


    /**
     * @param $supplierId
     * @return int
     */
    public static  function CheckIsFollow($supplierId)
    {
        $user = \Yii::$app->user->identity;
        if (!$user)
        {
            return self::UN_FOLLOW;
        }
        $follow=self::find()
            ->where(['user_id'=>$user->id,'role_id'=>$user->last_role_id_app])
            ->andWhere(['follow_id'=>$supplierId])
            ->andWhere(['status'=>self::HAVE_FOLLOW])
            ->one();
        return $follow?self::HAVE_FOLLOW:self::UN_FOLLOW;
    }
}
