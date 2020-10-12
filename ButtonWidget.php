<?php
/**
 * Date: 19.05.15
 * Time: 13:57
 *
 * This file is part of the ramprasadm1986 project.
 *
 * (c) ramprasadm1986 project <http://github.com/ramprasadm1986/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace ramprasadm1986\elfinder;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * Class ButtonWidget
 *
 * @package ramprasadm1986\elfinder
 */
class ButtonWidget extends Widget{
	public $language;

	public $filter;

	public $tag = 'button';
	public $name = 'Browse';
	public $options = [];

	protected $_managerOptions;

	public $width = 'auto';
	public $height = 'auto';

	public $template = '{input}{button}';

	public $controller = 'elfinder';

	public $path; // work with PathController

	public $startPath;

	public $callback;

	public function init()
	{
		parent::init();

		if(empty($this->language))
			$this->language = ElFinder::getSupportedLanguage(\Yii::$app->language);

		if(empty($this->options['id']))
			$this->options['id'] = $this->getId();
		else
			$this->setId($this->options['id']);

		$managerOptions = [];
		if(!empty($this->filter))
			$managerOptions['filter'] = $this->filter;

		$managerOptions['callback'] = $this->getId()."_manager";

		if(!empty($this->language))
			$managerOptions['lang'] = $this->language;

		if(!empty($this->path))
			$managerOptions['path'] = $this->path;

		$params = $managerOptions;
		if(!empty($this->startPath))
			$params['#'] = ElFinder::genPathHash($this->startPath);

		$this->_managerOptions['url'] = ElFinder::getManagerUrl($this->controller, $params);
		$this->_managerOptions['width'] = $this->width;
		$this->_managerOptions['height'] = $this->height;
		$this->_managerOptions['id'] = $managerOptions['callback'];
	}

	public function run(){
		AssetsCallBack::register($this->getView());

		echo Html::tag($this->tag, $this->name, $this->options);

		$this->getView()->registerJs("ramprasadm1986.elFinder.register(" . Json::encode($this->_managerOptions['id']) . ", " . Json::encode($this->callback) . ");"); // register callback Function
		$this->getView()->registerJs("\$(document).on('click', '#" . $this->options['id'] . "', function(){ramprasadm1986.elFinder.openManager(" . Json::encode($this->_managerOptions) . ");});");//on click button open manager
	}
}