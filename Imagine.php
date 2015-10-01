<?php
namespace common\components;

use yii\imagine\Image;

class Imagine extends \yii\base\Component {
    
    Public function avatar($_operation,$model,$attribute,$params=null)
    {
        $operation = $params===null ? \Yii::$app->params['imagine'][$_operation]:$params;
        
        $pathmanager = Path::init($model, $attribute);
        
        $photo = $model->$attribute ;
        $prefix = $operation['prefix'];        
        $avatar = $prefix.$photo ;
        $avatarurl = $pathmanager->getRuntimeUrl() ;
        $runtimepath = $pathmanager->getRuntimePath() ;
        $source = $pathmanager->getPath().$photo;
        $quality = isset($operation['quality'])?$operation['quality']:90;
        
        if(!is_dir($source) && file_exists($source))
        {
            $destination = $runtimepath.$avatar ;
            if(!file_exists($destination))
            {
                if(!is_dir($runtimepath))
                    mkdir ($runtimepath,0777,true);
                
                if(empty($operation))
                    return null ;
                $filepath = $source ;

                $image = Image::getImagine();
                $image->open($filepath);

                if(isset($operation['filters']))
                    foreach ($operation['filters'] as $key=>$filter) {
                        array_unshift($filter,$filepath);
                        $image = call_user_func_array(['yii\imagine\Image', $key], $filter);
                        $image->save($destination);
                        $filepath = $destination ;
                    }
                if(isset($operation['effects']))
                    foreach ($operation['effects'] as $key => $effect) {
                        $newImage = $image->effects() ;
                        $newImage = call_user_func_array([$newImage, $key], $effect);
                    }

                $image->save($destination,['quality'=> $quality]);
            }
        }
        return $avatarurl.$avatar;
    }
}
