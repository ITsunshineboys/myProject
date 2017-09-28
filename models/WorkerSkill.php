<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "worker_skill".
 *
 * @property integer $id
 * @property string $skill
 */
class WorkerSkill extends \yii\db\ActiveRecord
{
    const SKILL_FH=',';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'worker_skill';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['skill'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'skill' => '工人特长',
        ];
    }
    /**
     *all skill
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function  GetAllsKills(){
        return self::find()->asArray()->all();

    }
    /**
     * 工人自己特长ids
     * @param $uid
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getWorkerSkillids($uid){
        $data=Worker::find()->where(['uid'=>$uid])->asArray()->select('skill_ids')->all();
        if(!$data){
            return null;
        }
        return $data;
    }
    /**
     * 除了工人特长其他的特长的名称
     * @param $uid
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getOtherSkillname($uid){
        $skill_ids=self::getWorkerSkillids($uid);
        if(!$skill_ids){
            return null;
        }
        foreach ($skill_ids as $skill_id){
            $skill_name=self::find()->where( "id not in ({$skill_id['skill_ids']})")->asArray()->all();

        }
        if(!$skill_name){
            return null;
        }
        return $skill_name;
    }
    /**
     * 设置特长
     * @param $uid
     * @param $skill_id
     * @return int
     */
    public static function getSetSkillids($uid,$skill_id){
        $worker=Worker::find()->where(['uid'=>$uid])->one();
        if(!$worker){
            $code=1000;
            return $code;
        }
        if($worker->skill_ids==null){
            $worker->skill_ids=$skill_id;
        }else{
            $worker->skill_ids=$worker->skill_ids.self::SKILL_FH.$skill_id;
        }
        if(!$worker->save(false)){
            $code=500;
            return $code;
        }
       return 200;
    }
    /**
     * 删除工人特长
     * @param $uid
     * @param $skill_id
     */
    public static function DelWorkerSkill($uid,$skill_id){
        $worker=Worker::find()->where(['uid'=>$uid])->one();
        $data=explode(',',$worker->skill_ids);
        foreach ($data as $k=>$vule){
            if($vule==$skill_id){
                unset($data[$k]);
               $a=implode(',',$data);
              $worker->skill_ids=$a;
              if(!$worker->save(false)){
                  $code=500;
                  return $code;
              }
              return 200;
            }
        }

    }
    /**
     * 获取工人特长名称
     * @param $uid
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getWorkerSkillname($uid){
        $skill_ids=self::getWorkerSkillids($uid);
        if(!$skill_ids){
            return null;
        }
        foreach ($skill_ids as $skill_id){
            $skill_name=self::find()->where( "id in ({$skill_id['skill_ids']})")->asArray()->all();
        }
        if(!$skill_name){
            return null;
        }
        return $skill_name;
    }
}
