<?php
/*
  Plugin Name: Группировка характеристик товаров
  Description: Сортирует характеристики по группам если были найдены совпадения в карточке товара, Шорткод: [group-property id=""] где id="" id товара, если совпадения по группам не найдены то все характеристики выводятся списком как обычно.
  Author: Румянцев Олег
  Version: 0.1
 */

new GroupProperty;
class GroupProperty
{
	private static $lang = array();
	private static $pluginName = 'group_property';
	private static $path;
	
	public function __construct()
	{
		mgActivateThisPlugin(__FILE__, array(__CLASS__, 'activate'));
		mgAddAction(__FILE__, array(__CLASS__, 'pageSettingsPlugin'));
		mgAddShortcode('group-property', array(__CLASS__, 'handlerShortcode'));
		
		self::$path = PLUGIN_DIR.'group-property';
	}
	
	static function activate()
	{
		self::createTable();
	}
	
	static function createTable()
	{
	    DB::query("
	     CREATE TABLE IF NOT EXISTS `".PREFIX.self::$pluginName."` (
	      `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Порядковый номер записи',
		  `name` text NULL COMMENT 'Имя группы',
	      `property` text NULL COMMENT 'Характеристики',      
	      `sort` int(11) NULL COMMENT 'Порядок сортировки',
	      PRIMARY KEY (`id`)
	    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

	    if(MG::getSetting(self::$pluginName.'-option') == null){
	      $arPluginParams = array(
	      	'delete_property' => 'false'
	      );
	      MG::setOption(array('option' => self::$pluginName.'-option', 'value' => addslashes(serialize($arPluginParams))));
	    }
	}
	
	static function prepareSettingsPage()
	{
		echo '
			<link rel="stylesheet" href="'.SITE.'/'.self::$path.'/css/settings.css" type="text/css" />
			<script type="text/javascript" src="'.SITE.'/'.self::$path.'/js/admin.js"></script>
		';
	}

	// Возвращает ids характеристик которые уже распределенны по 
	// группам
	static function getGroupProperty(){
		$groups = self::getEntity();
		if(!empty($groups)){
			$ids = array();
			foreach($groups as $group){
				if(!empty($group['property'])){
					foreach($group['property'] as $property){
						$ids[] = $property['id'];
					}
				}
			}
			return $ids;
		}
	}

	static function getAllProperty(){
		$ids 	= self::getGroupProperty();
		$result = array();
		if(!empty($ids)){
			$ids   = implode($ids, ', ');
			$query = DB::query("SELECT id, name FROM `".PREFIX."property` WHERE id NOT IN({$ids}) ORDER BY id DESC");
			if(DB::numRows($query) != 0){
				while($row = DB::fetchAssoc($query)){
					$result[] = $row;
				}
			}
		} else {
			$query = DB::query("SELECT id, name FROM `".PREFIX."property` ORDER BY id DESC");
			if(DB::numRows($query) != 0){
				while($row = DB::fetchAssoc($query)){
					$result[] = $row;
				}
			}
		}
		return $result;
	}

	static function getEntity($id = null){
		if($id){
			$query = DB::query("SELECT * FROM `".PREFIX.self::$pluginName."` WHERE id={$id}");
		} else {
			$query = DB::query("SELECT * FROM `".PREFIX.self::$pluginName."` ORDER BY `sort`");
		}

		$result = array();

		if(DB::numRows($query) != 0){
		  while($row = DB::fetchAssoc($query)){
		    $row['property'] = unserialize($row['property']);
		    if(!empty($row['property'])){
		    	foreach($row['property'] as $key => $val){
		    		$q_property = DB::query("SELECT id, name FROM `".PREFIX."property` WHERE id = ".$val."");
		    		$r_property = DB::fetchAssoc($q_property);
		    		$row['property'][$key] = $r_property;
		    	}
		    }
		    $result[] = $row;
		  }
		}

		return $result;
	}

	static function getPluginOptions(){
		$option  = MG::getSetting(self::$pluginName.'-option');
		$option  = stripslashes($option);
		return unserialize($option);
	}
	
	static function pageSettingsPlugin()
	{
		$pluginName  = self::$pluginName;
		$entity      = self::getEntity();
		$newProperty = self::getAllProperty();
		$options     = self::getPluginOptions();

		
		self::prepareSettingsPage();
		include('page-settings.php');		
	}

	static function handlerShortcode($args){
		$model   = new Models_Product;
		$product = $model->getProduct($args['id']);

		$data = array(
			'thisUserFields' => $product['thisUserFields'],
			'groupProperty'  => self::getEntity()
		);

		$realDocumentRoot = str_replace(DIRECTORY_SEPARATOR.'mg-plugins'.DIRECTORY_SEPARATOR.'group-property', '', dirname(__FILE__));
		ob_start();
		include($realDocumentRoot.DIRECTORY_SEPARATOR.self::$path.DIRECTORY_SEPARATOR.'printPropertys.php');
		return ob_get_clean();	
	}
}