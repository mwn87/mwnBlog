<?php

abstract class BaseRepository extends \Nette\Object {
	/**
	 * Databaze
	 * @var Nette\Database\Connection
	 */
	protected $connection;
	
	/**
	 * Nazev tabulky
	 * @var string
	 */
	protected $table;
	
	
	public function __construct(\Nette\Database\Connection $connection) {
		$this->connection = $connection;
	}
	
	protected function getTable() {
		return $this->connection->table($this->table);
	}
	
	public function findAll() {
		return $this->getTable();
	}
	
	public function findBy(array $criteria) {
		return $this->getTable()->where($criteria);
	}
}