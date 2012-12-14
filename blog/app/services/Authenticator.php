<?php

class Authenticator extends Nette\Object implements \Nette\Security\IAuthenticator {
	/**
	 * Repository router
	 * @var RepositoryRouter
	 */
	protected $repository;
	
	/**
	 * Authenticator configuration
	 * @var array
	 */
	protected $config;
	
	/**
	 * Module namespace
	 * @var string
	 */
	protected $namespace = 'FrontModule';
	
	
	public function __construct(RepositoryRouter $repository, $config, \Nette\Security\User $user) {
		$this->repository = $repository;
		$this->config = $config;
		$this->namespace = $user->getStorage()->getNamespace();
	}
	
	public function authenticate(array $credentials) {
		list($username, $password) = $credentials;
		
		$user = $this->repository->user->findOneBy(array('username' => $username));
		
		if(!$user) {
			throw new Nette\Security\AuthenticationException('Wrong username or password', self::IDENTITY_NOT_FOUND);
		}
		
		if($user->password != $this->calculateHash($password)) {
			throw new Nette\Security\AuthenticationException('Wrong username or password', self::INVALID_CREDENTIAL);
		}
		
		if(!$user->active) {
			throw new Nette\Security\AuthenticationException('This user is either blocked, or not approved', self::NOT_APPROVED);
		}
		
		
		$group = $this->repository->group->findOneBy(array('id' => $user->groups_id));
		
		if(!$group) {
			throw new Nette\Security\AuthenticationException('This user is not assigned to any group', self::FAILURE);
		}
		
		if(!$group->active) {
			throw new Nette\Security\AuthenticationException('This user is either blocked, or not approved', self::NOT_APPROVED);
		}
		
		if($group->role != 'admin' && $this->namespace == 'AdminModule') {
			throw new Nette\Security\AuthenticationException('Insufficient permissions', self::NOT_APPROVED);
		}
		
		unset($user->password);
		return new \Nette\Security\Identity($user->id, $group->role, array('user' => $user->toArray(), 'group' => $group->toArray()));
	}
	
	public function calculateHash($password) {
		return strrev(sha1($password . $this->config['salt']));
	}
}