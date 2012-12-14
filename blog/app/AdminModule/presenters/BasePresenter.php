<?php

namespace AdminModule;

abstract class BasePresenter extends \Nette\Application\UI\Presenter {
	public function startup() {
		parent::startup();
		
		$this->getUser()->getStorage()->setNamespace('AdminModule');

		if(!$this->getUser()->isLoggedIn()) {
			$this->setLayout('unauthorized');
			if($this->getAction(true) != ':Admin:Login:default') {
				$this->redirect('Login:');
			}
		}
	}
}
