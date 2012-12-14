<?php

namespace AdminModule;

class LoginPresenter extends BasePresenter {
	public function actionLogout() {
		$this->getUser()->logout(true);
		
		$this->flashMessage('You have been successfully logged out', 'info');
		$this->redirect('Login:');
	}
	
	
	protected function createComponentLoginForm($name) {
		$form = new \Nette\Application\UI\Form($this, $name);
		
		$form->addText('username', 'Username')->setRequired('Please fill out the username field');
		$form->addPassword('password', 'Password')->setRequired('Please fill out the password field');
		
		$form->addSubmit('loginFormSubmit', 'Přihlásit');
		$form->onSuccess[] = callback($this, 'loginFormSuccess');
	}
	
	public function loginFormSuccess(\Nette\Application\UI\Form $form) {
		$values = $form->getValues();
		
		try {
			$this->getUser()->login($values->username, $values->password);
			
			$this->flashMessage('Welcome ' . $values->username, 'info');
			$this->redirect('Dashboard:');
		} catch (\Nette\Security\AuthenticationException $e) {
			$this->flashMessage($e->getMessage(), 'error');
			$this->redirect('this');
		}
	}
}