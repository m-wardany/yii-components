<?php
namespace common\components;

use yii\web\UploadedFile;


class Uploader extends \yii\base\Component
{
    public function upload($model,$attribute,$file = null)
    {
        if ($model->validate()) 
        {
            $file = $file===null?UploadedFile::getInstance($model, $attribute):$file;
            if(isset($file->extension))
            {
                $pathmanager = Path::init($model, $attribute);
                
                $filename = rand(0,9999).'_'.md5($file->baseName)  . '.' . $file->extension;
                $path = $pathmanager->getPath();
                $runtimepath = $pathmanager->getRuntimePath();
                $temp = $model->isNewRecord?null:$model->oldattributes[$attribute];
                if(!is_dir($path))
                    mkdir ($path,0777,true);
                
                if($file->saveAs($path .$filename))
                {
                    $model->$attribute = $filename ;

                    // deleting old file
                    if(!$model->isNewRecord && !empty($temp) && $temp != $filename && file_exists($path.$temp))
                        unlink ($path.$temp);
                    
                    // remove old chached files
                    if(!$model->isNewRecord && is_dir($runtimepath))
                    {
                        $files = glob($runtimepath.'*'); // get all file names
                        foreach($files as $file){ // iterate files
                          if(is_file($file))
                            unlink($file); // delete file
                        }
                    }
                }                
            }
            return true;
        }
        else {
            return false;
        }
    }
    
    function multiUpload($model, $attribute,$submodel, $submodelattribute,$relationalattribute) {
        $newmodel = new $submodel ;
        
        $files = UploadedFile::getInstances($model,$attribute); 
        foreach ($files as $key => $file) {
            $newmodel = new $submodel ;
            $newmodel->$relationalattribute = $model->id ;
            if($this->upload($newmodel, $submodelattribute,$file))
                    $newmodel->save();
        }
    }
}

