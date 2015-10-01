<?php
namespace common\components;

use yii\helpers\Inflector;
use yii\helpers\Url ;
class Path{

    private $path ;
    private $runtimepath ;
    private $url ;
    private $runtimeurl ;
    
    private $model ;
    private $attribute ;

    public static function init($model, $attribute, $folder="media", $runtimefolder="rtmedia") {
        $_this = new self ;
        
        $_this->model = $model ;
        $_this->attribute = $attribute ;
        
        $_attribute = Inflector::pluralize(strtolower($attribute));
        $_model = Inflector::pluralize(\yii\helpers\StringHelper::basename(strtolower(get_class($model)))); ;
        
        $_this->url = "/{$folder}/{$_model}/{$_attribute}/" ;
        $_this->runtimeurl = "/{$runtimefolder}/{$_model}/{$_attribute}/";
        
        $_this->path = "@frontend/web{$_this->url}";
        $_this->runtimepath = "@frontend/web{$_this->runtimeurl}";
        
        
        return $_this;
    }
    
    public static function delete($model,$attributes) {
        foreach ($attributes as $attribute) {
            $pathmanager = self::init($model, $attribute);
            
            if(!empty($model->$attribute) && file_exists($pathmanager->getPath().$model->$attribute))
                unlink ($pathmanager->getPath().$model->$attribute);
            if(is_dir($pathmanager->getRuntimePath()))
            {
                $attributes = glob($pathmanager->getRuntimePath().'*'); // get all file names
                foreach($attributes as $attribute){ // iterate files
                  if(is_file($attribute))
                    unlink($attribute); // delete file
                }
                rmdir($pathmanager->getRuntimePath());
            }
        }
    }
    
    public function getPath()
    {        
        return \Yii::getAlias($this->path);
    }
    
    public function getUrl()
    {
        $model = $this->model;
        $attribute = $this->attribute ;
        return Url::to($this->url).$model->$attribute ;
    }
    
    public function getRuntimePath()
    {        
        $model = $this->model;
        return \Yii::getAlias($this->runtimepath).$model->id.'/';
    }
    
    public function getRuntimeUrl()
    {        
        $model = $this->model;
        return Url::to($this->runtimeurl).$model->id.'/' ;        
    }
    
}
?>

