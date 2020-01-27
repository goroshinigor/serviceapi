<?php

class CRMAPIClient
{

	public $status;
	public $msg;

	private $attr;

	public function __construct(){
		$this->status = true;
	}

	public function set($data){

		$this->attr['id'] = (int)$data['id'];
		$this->attr['isDeleted'] = (int)$data['isDeleted'];
        $this->attr['memberId'] = (string)$data['memberId'];

        $this->attr['memberType'] = (string)$data['memberType'];

        $this->attr['lastName'] = (string)$data['lastName'];
		$this->attr['firstName'] = (string)$data['firstName'];
		$this->attr['middleName'] = (string)$data['middleName'];
		$this->attr['email'] = (string)$data['email'];
		$this->attr['phoneNumber'] = (string)$data['phoneNumber'];
        $this->attr['password'] = (string)$data['password'];

        $this->attr['gender'] = (int)$data['gender'];
        $this->attr['birthday'] = CRMAPIWee::dateConverter((string)$data['birthday'],'iso_date');

	}

	public function setFromDB($data){
		// Возможность инициализации с помощью разных иденитифакторов

        $id = (int)$data['id'];
        $memberId = (string)$data['memberId'];

		if($memberId != ""){
            $res = CRMAPIDB::prepare("SELECT * FROM crmapi_clients WHERE memberId = :memberId ");
            $res->bindParam("memberId",$memberId);
            $res->execute();
            $r = $res->fetch(PDO::FETCH_ASSOC);
            $this->set($r);
		}

        if($id != ""){
            $res = CRMAPIDB::prepare("SELECT * FROM crmapi_clients WHERE id = :id ");
            $res->bindParam("id",$id);
            $res->execute();
            $r = $res->fetch(PDO::FETCH_ASSOC);
            $this->set($r);
        }

        if($r['id'] == 0){
            $this->status = false; $this->msg['code'] = 60101; // Не удалось найти Клиента по указанным данным
        }

	}

	public function getData(){
		return $this->attr;
	}

    public function getDataPublic(){

	    $t = $this->attr;
	    unset($t['id']);
        unset($t['password']);
        unset($t['isDeleted']);
        unset($t['memberType']);
        return $t;

    }

	public function getJSON(){
		return json_encode($this->getData());
	}

	// Проверки данных
	public function checkData(){

	    if($this->attr['id'] > 0){ $sql_id = " AND id != '".(int)$this->attr['id']."' "; }

//        if($this->status and (strlen($this->attr['lastName']) == 0 or strlen($this->attr['lastName']) > 32) ){
//            $this->status = false; $this->msg['code'] = 60110; // Не соответствие кол-ва символов для фамилии
//        }
//		if($this->status and !preg_match("/^[А-Яа-яЁёЇїІіЄєҐґ]+[- ’']?[А-Яа-яЁёЇїІіЄєҐґ]+$/iu",$this->attr['lastName'])){
//			$this->status = false; $this->msg['code'] = 60111; // Не допустимый набор символов для фамилии
//		}
//        if($this->status and (strlen($this->attr['firstName']) == 0 or strlen($this->attr['firstName']) > 32) ){
//            $this->status = false; $this->msg['code'] = 60112; // Не соответствие кол-ва символов для имени
//        }
//        if($this->status and !preg_match("/^[А-Яа-яЁёЇїІіЄєҐґ]+[- ’']?[А-Яа-яЁёЇїІіЄєҐґ]+$/iu",$this->attr['firstName'])){
//            $this->status = false; $this->msg['code'] = 60113; // Не допустимый набор символов для имени
//        }
//        if($this->status and (strlen($this->attr['middleName']) == 0 or strlen($this->attr['middleName']) > 32) ){
//            $this->status = false; $this->msg['code'] = 60114; // Не соответствие кол-ва символов для отчества
//        }
//        if($this->status and !preg_match("/^[А-Яа-яЁёЇїІіЄєҐґ]+[- ’']?[А-Яа-яЁёЇїІіЄєҐґ]+$/iu",$this->attr['middleName'])){
//            $this->status = false; $this->msg['code'] = 60115; // Не допустимый набор символов для отчества
//        }

        if($this->status and !preg_match("/^\+[0-9]{12}$/iu",$this->attr['phoneNumber'])){
            $this->status = false; $this->msg['code'] = 60116; // Не допустимый набор символов для телефона
        }
        if($this->status and strlen($this->attr['password']) < 5 ){
            $this->status = false; $this->msg['code'] = 60117; // Не допустимый набор символов для пароля
        }

        if($this->status){
            $res = CRMAPIDB::prepare("SELECT id FROM crmapi_clients WHERE phoneNumber = :phoneNumber ".$sql_id);
            $res->bindParam("phoneNumber",$this->attr['phoneNumber']);
            $res->execute();
            $r = $res->fetch(PDO::FETCH_ASSOC);
            if($r['id'] > 0){
                $this->status = false; $this->msg['code'] = 60118; // Указанный номер телефона уже зарегестрирован в базе данных
            }
        }

	}

	// Общий метод сохранения объекта, где выбираем, либо добавить, либо обновить
	public function save(){
		if($this->status){

			if($this->attr['id'] > 0){
				$this->change();
			} else {
				$this->add();
			}

		}
		return $this->attr['memberId'];
	}

	// Создание объекта
	public function add(){
		if($this->status){
			$this->checkData();
		}
		if($this->status){
            $this->attr['password'] = password_hash($this->attr['password'],PASSWORD_DEFAULT);
			$this->toDB('insert');
		}
        $this->generateMemberId();

        return $this->attr['memberId'];
	}

	// Изменение существующего объекта
	public function change(){
		if($this->status){
			$this->checkData();
		}
		if($this->status){
			$this->toDB('update');
		}
	}

    // Удаление существующего объекта
    public function delete(){
        if($this->status){
            $res = CRMAPIDB::prepare("DELETE FROM crmapi_clients WHERE memberId = :memberId");
            $res->bindParam("memberId",$this->attr['memberId']);
            $ok = $res->execute();
            if($ok) {
                $this->status = false;
                $this->msg['code'] = 60140; // Клиент успешно удален;
            } else {
                $this->status = false;
                $this->msg['code'] = 60141; // Не удалось удалить Клиента;
            }
        }
    }

    // Изменение существующего пароля
    public function changeWithPass(){
        if($this->status){
            $this->checkData();
        }
        if($this->status){
            $this->attr['password'] = password_hash($this->attr['password'],PASSWORD_DEFAULT);
            $this->toDB('update');
        }
    }

    // Функция сохранения параметров объекта
	private function toDB($action){

		if($this->status and ($action == 'insert' or $action == 'update')){

            CRMAPIDB::beginTransaction();

			if($action == 'insert'){ $sql1 = "INSERT INTO "; $sql3 = ""; }
			if($action == 'update'){ $sql1 = "UPDATE "; $sql3 = "WHERE id = '".$this->attr['id']."' "; }

			// формируем запрос на основании свойств объекта и типа вставки - insert или update
			$sql = $sql1." crmapi_clients SET ";

			// перебираем свойства и вносим их в запрос без неоткорых полей
			foreach($this->attr as $k => $v){
				if(
					$k != 'id' and
					$k != 'isDeleted'
				){
					$sql .= $k." = :".$k.", ";
				}
			}
			// чистим последнюю запятую
			$sql = substr($sql,0,-2);

			// добаляем данные под ситуацию для insert или change
			$sql .= " ".$sql3;

			// регистрируем запрос
			$res = CRMAPIDB::prepare($sql);

			// наполняем данными наш запрос, так же перебирая все свойства объекта
			foreach($this->attr as $k => $v){
				if(preg_match("/:".$k."\b/im",$sql)){
					$res->bindValue($k,$v);
				}
			}

			// выполняем запрос
			$ok = $res->execute();
//			print_r($res->errorInfo());

			if($ok){
				$this->status = true;
				if($this->attr['id'] <= 0) $this->attr['id'] = CRMAPIDB::r()->lastInsertId();
			} else {
                $this->status = false;
                $this->msg['code'] = 60130; // Не удалось сохранить Клиента";

            }

			// Принимаем решение по транзакции БД
			if($this->status){
                CRMAPIDB::commit(); // подтверждаем транзакцию в БД
			} else {
                CRMAPIDB::rollBack();
			}

		}

		return $this->attr['id'];

	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// генерим глобальный номер для Филиала и сохраняем его
	private function generateMemberId(){
		if($this->status ){

			$this->attr['memberId'] = sprintf("%'.09d",$this->attr['id']);

			// сохраняем
			$res = CRMAPIDB::query("UPDATE crmapi_clients SET memberId = '".$this->attr['memberId']."' WHERE id = '".$this->attr['id']."' ");

		}
		return $this->attr['memberId'];
	}

}

?>