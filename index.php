<?php

const HOST = 'localhost';
const LOGIN = 'root';
const PASSWORD = '';

/**
 * Class Database
 * Упрощенный класс обеспечивающий уровень абстракции для
 * соединения с базой MySQL.
 * Для тестового задания.
 */
class Database {
	public $connection;
	private $host;
	private $login;
	private $password;

	public function __construct($host, $login, $password)
	{
		$this->host = $host;
		$this->login = $login;
		$this->password = $password;
	}

	public function connect()
	{
		$this->connection = mysql_connect($this->host, $this->login, $this->password);
	}

	public function select_db($db_name)
	{

		mysql_select_db($db_name, $this->connection);
	}

	public function fetch_array($result)
	{
		return mysql_fetch_array($result);
	}

	public function __destruct()
	{
		mysql_close($this->connection);
	}

	public function query($query)
	{
		return mysql_query($query, $this->connection);
	}
}

/**
 * Class Object
 */
final class Object {
	private $id = 0;
	private $name = "";
	private $status = 0;
	private $changed = FALSE;
	private $db;
	private $obj_state_changed = FALSE;

	public function __construct($id)
	{
		$this->id = $id;
	}

	public function init()
	{
		$this->db = new Database(HOST, LOGIN, PASSWORD);
		$this->db->connect();
		$this->db->select_db('test');
		$result = $this->db->query("SELECT name, status FROM objects WHERE id = {$this->id}");
		$values = $this->db->fetch_array($result);
		$this->name = $values['name'];
		$this->status = $values['status'];
	}

	public function __get($name)
	{
		return $this->$name;
	}

	public function __set($name, $value)
	{
		if (empty($value))
		{
			echo "value can't be empty!";
			return;
		}
		switch ($name) {
			case 'id':
				echo 'Property "id" is not writeable!';
				break;
			case 'name':
				if (is_string($value))
				{
					$this->$name = $value;
					$this->obj_state_changed = TRUE;
				}
				else
				{
					echo 'Property "name" can be of type string only!';
				}
				break;
			case 'status':
				if (is_int($value))
				{
					$this->$name = $value;
					$this->obj_state_changed = TRUE;
				}
				else
				{
					echo 'Property "status" can be of type int only!';
				}
				break;
			case 'changed':
				if (is_bool($value))
				{
					$this->$name = $value;
					$this->obj_state_changed = TRUE;
				}
				else
				{
					echo 'Property "changed" can be of type bool only!';
				}
				break;
		}
	}

	public function save()
	{
		if ($this->obj_state_changed) {
			$this->db->query("UPDATE objects SET name = '{$this->name}', status = {$this->status} WHERE id = {$this->id}");
			$this->obj_state_changed = FALSE;
		}
	}
}

// Part 1
$obj = new Object(1);
$obj->init();
$obj->status = 100500;
$obj->save();

// Part 2 (SQL)
$db = new Database(HOST, LOGIN, PASSWORD);
$db->connect();
$db->select_db('test');
$result = $db->query('SELECT login FROM users
			    	  INNER JOIN objects ON objects.id = users.object_id');
while ($user = $db->fetch_array($result))
{
	echo $user['login']."<br />";
}
