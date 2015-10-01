<?php
namespace common\components ;

use yii\helpers\StringHelper;

class Save{
    public function all($model, $attribute) {
        $model->unlinkAll($attribute,true);
        $class = StringHelper::basename(get_class($model));
        $relation = $model->getRelation($attribute);
        $relatedmodel = $relation->modelClass ;
        if(isset($_POST[$class][$attribute]))
        {
            $newdata = $_POST[$class][$attribute];
            foreach($newdata as $id)
            {
                $item = $relatedmodel::find()->where("id = {$id}")->one();
                if(count($item))
                {
                    $model->link($attribute, $item);
                }
            }
        }
    }
}
