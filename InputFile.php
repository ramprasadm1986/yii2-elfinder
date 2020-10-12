<?php
/**
 * Date: 23.01.14
 * Time: 1:27
 */

namespace ramprasadm1986\elfinder;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;



class InputFile extends InputWidget{
	public $language;

	public $filter;

	public $buttonTag = 'button';
	public $buttonName = 'Browse';
	public $buttonOptions = [];

	protected $_managerOptions;

	public $width = 'auto';
	public $height = 'auto';

	public $template = '{input}{button}';

	public $controller = 'elfinder';

	public $path; // work with PathController

	public $multiple;

	public $startPath;
    
    public $defaultImage='';

	public function init()
	{
		parent::init();

		if(empty($this->language))
			$this->language = ElFinder::getSupportedLanguage(Yii::$app->language);

		if(empty($this->buttonOptions['id']))
			$this->buttonOptions['id'] = $this->options['id'].'_button';

		$this->buttonOptions['type'] = 'button';

		$managerOptions = [];
		if(!empty($this->filter))
			$managerOptions['filter'] = $this->filter;

		$managerOptions['callback'] = $this->options['id'];

		if(!empty($this->language))
			$managerOptions['lang'] = $this->language;

		if (!empty($this->multiple))
			$managerOptions['multiple'] = $this->multiple;

		if(!empty($this->path))
			$managerOptions['path'] = $this->path;

		$params = $managerOptions;
		if(!empty($this->startPath))
			$params['#'] = ElFinder::genPathHash($this->startPath);

		$this->_managerOptions['url'] = ElFinder::getManagerUrl($this->controller, $params);
		$this->_managerOptions['width'] = $this->width;
		$this->_managerOptions['height'] = $this->height;
		$this->_managerOptions['id'] = $this->options['id'];
	}

	/**
	 * Runs the widget.
	 */
	public function run()
	{
		if ($this->hasModel()) {
				
			$attr = $this->attribute;
            $hidden = $this->model->{$attr} ? '' : 'display:none;';
			$img=$this->model->{$attr};
            $images=explode(",",$img);
            
			if($this->defaultImage && $img==""){
				$img=$this->defaultImage;
				$hidden='';
			}
            
            if(count($images)>1){
                
                $images_tag="";
                foreach($images as $key=>$images)
                    $images_tag.='<img id="' . $this->options['id']."-".$key . '-thumb" class="thumbnail" src="' . $images . '" style="max-width: 150px; max-height: 150px;margin:0px; float:left; ' . $hidden . '" />';
                    
                $replace['{image}']='<div id="'.$this->options['id'].'-thumbGroup" >'.$images_tag.'</div>';
            }
            else{
                
                if (!empty($this->multiple)){
                    $replace['{image}'] = '<div id="'.$this->options['id'].'-thumbGroup" ><img id="' . $this->options['id'] . '-thumb" class="thumbnail" src="' . $img . '" style="max-width: 150px; max-height: 150px;margin:0px; float:left; ' . $hidden . '" /></div>';
                }
                else{
                    $replace['{image}'] = '<img id="' . $this->options['id'] . '-thumb" class="thumbnail" src="' . $img . '" style="max-width: 150px; max-height: 150px;margin:0px; ' . $hidden . '" />';
                }
            }
            
            $replace['{input}'] = Html::activeTextInput($this->model, $this->attribute, $this->options);
		} else {
			$replace['{input}'] = Html::textInput($this->name, $this->value, $this->options);
		}

		$replace['{button}'] = Html::tag($this->buttonTag,$this->buttonName, $this->buttonOptions);


		echo strtr($this->template, $replace);

		AssetsCallBack::register($this->getView());

		
        
        if (!empty($this->multiple))
			$this->getView()->registerJs("ramprasadm1986.elFinder.register(" . Json::encode($this->options['id']) . ", function(files, id){ var _f = []; for (var i in files) { _f.push(files[i].url); } \$('#' + id).val(_f.join(', ')).trigger('change', [files, id]);  \$('#' + id+'-thumbGroup').empty();  for (var i in files) { var ele='<img id=\"'+id+'-'+i+'-thumb\" class=\"thumbnail\" src=\"'+ files[i].url +'\"  style=\"max-width: 150px; max-height: 150px;margin:0px; float:left;\" />'; \$( '#' + id+'-thumbGroup' ).append(ele); } return true;}); $(document).on('click','#" . $this->buttonOptions['id'] . "', function(){ramprasadm1986.elFinder.openManager(" . Json::encode($this->_managerOptions) . ");});");
		else
			$this->getView()->registerJs("ramprasadm1986.elFinder.register(" . Json::encode($this->options['id']) . ", function(file, id){ \$('#' + id).val(file.url).trigger('change', [file, id]); $('#' + id + '-thumb').attr('src', file.url ).show(); return true;}); $(document).on('click', '#" . $this->buttonOptions['id'] . "', function(){ramprasadm1986.elFinder.openManager(" . Json::encode($this->_managerOptions) . ");});");
	}
}
