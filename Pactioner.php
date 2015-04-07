<?
class Pactioner extends Actioner{
	private $pluginName = 'group_property';

	// Сортирует характеристики в группе 
	public function sortedProperty(){
		$this->messageSucces = 'Операция выполнена';
		$this->messageError  = 'Ошибка выполнения операции';

		$propertys = array();
		if(!empty($_POST['propertys'])){
			foreach($_POST['propertys'] as $item){
				$propertys[$item] = $item;
			}
		}

		$save = array(
			'property' => serialize($propertys)
		); 

		if(DB::query('UPDATE `'.PREFIX.$this->pluginName.'` SET '.DB::buildPartQuery($save).' WHERE id = '.$_POST['idGroup'].''))
			return true;

		return false;
	}

	// Сортирует группы характеристик
	public function sortedGroup(){
		$this->messageSucces = 'Операция выполнена';
		$this->messageError  = 'Ошибка выполнения операции';

		$position = 1;
		foreach($_POST['groups'] as $id){
			DB::query("UPDATE `".PREFIX.$this->pluginName."` SET sort={$position} WHERE id={$id}");
			$position++;
		}

		return true;
	}

	// Поиск характеристики по имени, которой еще нет в группе
	public function searchProperty(){ 
		$ids    = GroupProperty::getGroupProperty();
		$result = array();
		$query  = $_POST['query'];
		if(!empty($query)){

			if(!empty($ids)){
				$ids   = implode($ids, ', ');
				$query = DB::query("SELECT id, name FROM `".PREFIX."property` WHERE id NOT IN({$ids}) AND name LIKE '{$query}%' LIMIT 10");
			} else {
				$query = DB::query("SELECT id, name FROM `".PREFIX."property` WHERE name LIKE '{$query}%' LIMIT 10");
			}
			if(DB::numRows($query) != 0){
				while($row = DB::fetchAssoc($query)){
					$result[] = $row;
				}
			}

		} else {
			// Если передан пустой запрос - отображаем все добавленные характеристики
			$result = GroupProperty::getAllProperty();
		}
		echo json_encode($result);
	}

	// Добавляет характеристики в группу
	public function addPropertyInGroup(){
		$this->messageSucces = 'Операция выполнена';
		$this->messageError  = 'Ошибка выполнения операции';

		if(!empty($_POST['data'])){
			$query = DB::query("SELECT property FROM `".PREFIX.$this->pluginName."` WHERE id={$_POST['idGroup']}");
			$row   = DB::fetchAssoc($query);
			$data  = array();

			if(!empty($row['property'])){
				$data = unserialize($row['property']);
			}

			$data   = array_merge($data, $_POST['data']);
			$result = array();

			foreach($data as $key => $val){
				$result[$val] = $val;
			}

			$save = array(
				'property' => serialize($result)
			);

			if(DB::query('UPDATE `'.PREFIX.$this->pluginName.'` SET '.DB::buildPartQuery($save).' WHERE id = '.$_POST['idGroup'].''))
				return true;

			return false;
		}
	}

	public function saveOptions(){
		$this->messageSucces = 'Настройки успешно сохранены';
		$this->messageError  = 'Ошибка выполнения операции';
		
		if (!empty($_POST['data'])) {
		  MG::setOption(array('option' => 'group_property-option', 'value' => addslashes(serialize($_POST['data']))));
		}
		
		return true;
	}

	// Удаляет характеристику из группы
	public function deletePropertyInGroup(){
		$this->messageSucces = 'Характеристика удаленна';
		$this->messageError  = 'Ошибка выполнения операции';

		$query = DB::query("SELECT property FROM `".PREFIX.$this->pluginName."` WHERE id={$_POST['idGroup']}");
		$row   = DB::fetchAssoc($query);
		$data  = unserialize($row['property']);

		unset($data[$_POST['idEl']]);

		$save = array(
			'property' => serialize($data)
		);

		$options = GroupProperty::getPluginOptions();
		if($options['delete_property'] == 'true'){
			DB::query('
			      DELETE
			      FROM `'.PREFIX.'property`
			      WHERE id = '.DB::quote($_POST['idEl'], true)) &&
			      DB::query('
			      DELETE
			      FROM `'.PREFIX.'product_user_property`
			      WHERE property_id = '.DB::quote($_POST['idEl'], true)) &&
			      DB::query('
			      DELETE
			      FROM `'.PREFIX.'category_user_property`
			      WHERE property_id = '.DB::quote($_POST['idEl'], true));
		}

		if(DB::query('UPDATE `'.PREFIX.$this->pluginName.'` SET '.DB::buildPartQuery($save).' WHERE id = '.$_POST['idGroup'].''))
			return true;

		return false;
	}

	public function getGroup(){
		$data = GroupProperty::getEntity($_POST['idGroup']);
		if(!empty($data[0]['property'])){
			$res = array();
			foreach($data[0]['property'] as $item){
				$res[] = $item;
			}
			echo json_encode($res);
		} else {
			echo json_encode($data[0]['property']);
		}
	}

	public function createGroup(){
		$this->messageSucces = 'Группа успешно создана';
		$this->messageError  = 'Ошибка выполнения операции';

		$save = array(
			'name' => $_POST['nameGroup']
		);

		if(DB::buildQuery('INSERT INTO `'.PREFIX.$this->pluginName.'` SET ', $save)){
			$lastId = DB::insertId();
			DB::query('UPDATE `'.PREFIX.$this->pluginName.'` SET sort='.$lastId.' WHERE id='.$lastId.'');
			$this->data['nameGroup'] = $_POST['nameGroup'];
			$this->data['idGroup']   = $lastId;
			return true;
		}

		return false;
	}

	public function deleteGroup(){
		$this->messageSucces = 'Группа удаленна';
		$this->messageError  = 'Ошибка выполнения операции';

		if (DB::query('DELETE FROM `'.PREFIX.$this->pluginName.'` WHERE `id`= '.$_POST['idGroup']))
			return true;
		return false;
	}

	public function editGroup(){
		$this->messageSucces = 'Группа отредактированна';
		$this->messageError  = 'Ошибка выполнения операции';

		$save = array(
			'name' => $_POST['nameGroup']
		);

		if(DB::query('UPDATE `'.PREFIX.$this->pluginName.'` SET '.DB::buildPartQuery($save).' WHERE id='.$_POST['idGroup'].'')){
			return true;
		}

		return false;
	}
}