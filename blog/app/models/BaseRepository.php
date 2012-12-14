<?php

abstract class BaseRepository extends \Nette\Object {
	/**
	 * Database
	 * @var Nette\Database\Connection
	 */
	protected $connection;
	
	/**
	 * Table name
	 * @var string
	 */
	protected $table;
	
	
	public function __construct(\Nette\Database\Connection $connection) {
		$this->connection = $connection;
	}
	
	protected function getTable() {
		return $this->connection->table($this->table);
	}
	
	/**
	 * Returns all results
	 * @return Nette\Database\Table
	 */
	public function findAll() {
		return $this->getTable();
	}
	
	/**
	 * Returns all results that meet the given critera
	 * @param array $criteria
	 * @return Nette\Database\Table
	 */
	public function findBy(array $criteria) {
		return $this->getTable()->where($criteria);
	}
	
	/**
	 * Returns one result that meets the given criteria
	 * @param array $criteria
	 * @return type
	 */
	public function findOneBy(array $criteria) {
		return $this->getTable()->where($criteria)->fetch();
	}
	
	/**
	 * Inserts data into the table
	 * @param array $data
	 * @return Nette\Database\Table\ActiveRow inserted row
	 */
	public function insert(array $data) {
		return $this->getTable()->insert($data);
	}
}