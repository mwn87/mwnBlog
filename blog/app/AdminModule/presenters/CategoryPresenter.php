<?php

namespace AdminModule;

class CategoryPresenter extends BasePresenter {
	protected $items;
	protected $item;
	
	public function actionDefault() {
		$this->items = $this->context->repository->category->findAll();
	}
	
	public function renderDefault() {
		$this->template->items = $this->items;
	}
	
	public function actionEdit($item) {
		$this->item = $this->context->repository->category->findOneBy(array("id" => $item));
		
		if(!$this->item) {
			$this->flashMessage('This category does not exist', 'error');
			$this->redirect('Category:');
		}
		
		$this['updateForm']['name']->setDefaultValue($this->item->name);
		$this['updateForm']['description']->setDefaultValue($this->item->description);
		$this['updateForm']['tags']->setDefaultValue($this->item->tags);
	}
	
	public function renderEdit() {
		$this->template->item = $this->item;
	}
	
	public function handleDelete($item) {
		$this->item = $this->context->repository->category->findOneBy(array("id" => $item));
		
		if(!$this->item) {
			$this->flashMessage('This category does not exist', 'error');
			$this->redirect('Category:');
		}
		
		if($this->item->delete()) {
			$this->flashMessage('The category was successfully deleted', 'info');
		} else {
			$this->flashMessage('The category could not be deleted', 'error');
		}
		
		$this->redirect('this');
	}
	
	protected function createComponentUpdateForm($name) {
		$form = new \Nette\Application\UI\Form($this, $name);
		
		$form->addText('name', 'Name')->setRequired('Please fill out the name field');
		$form->addTextArea('description', 'Description');
		$form->addTextArea('tags', 'Tags (one tag per line)');
		
		$form->addSubmit('submit', 'Save');
		$form->onSuccess[] = callback($this, 'updateFormSuccess');
	}
	
	public function updateFormSuccess(\Nette\Application\UI\Form $form) {
		$values = $form->getValues();
		
		$data['name'] = $values->name;
		$data['description'] = $values->description;
		$data['tags'] = $values->tags;
		$data['slug'] = $values->name == $this->item->name ? $this->item->slug : \RepositoryUtils::slug($this->context->repository->category, $values->name, $this->item ? $this->item->id : null);
		
		if($this->item) {
			$this->item->update($data);
		} else {
			$this->context->repository->category->insert($data);
		}
		
		$this->flashMessage('Category has been successfully saved');
		$this->redirect('Category:');
	}
}
