<?php

namespace AdminModule;

class ArticlePresenter extends BasePresenter {
	protected $items;
	protected $item;
	
	public function actionDefault() {
		$this->items = $this->context->repository->article->findAll();
	}
	
	public function renderDefault() {
		$this->template->items = $this->items;
	}
	
	public function actionEdit($item) {
		$this->item = $this->context->repository->article->findOneBy(array("id" => $item));
		
		if(!$this->item) {
			$this->flashMessage('This article does not exist', 'error');
			$this->redirect('Article:');
		}
		
		$this['updateForm']['name']->setDefaultValue($this->item->name);
		$this['updateForm']['category']->setDefaultValue($this->item->categories_id);
		$this['updateForm']['published']->setDefaultValue($this->item->published);
		$this['updateForm']['perex']->setDefaultValue($this->item->perex);
		$this['updateForm']['tags']->setDefaultValue($this->item->tags);
		$this['updateForm']['text']->setDefaultValue($this->item->text);
	}
	
	public function renderEdit() {
		$this->template->item = $this->item;
	}
	
	public function handleDelete($item) {
		$this->item = $this->context->repository->article->findOneBy(array("id" => $item));
		
		if(!$this->item) {
			$this->flashMessage('This article does not exist', 'error');
			$this->redirect('Article:');
		}
		
		if($this->item->delete()) {
			$this->flashMessage('The article was successfully deleted', 'info');
		} else {
			$this->flashMessage('The article could not be deleted', 'error');
		}
		
		$this->redirect('this');
	}
	
	protected function createComponentUpdateForm($name) {
		$form = new \Nette\Application\UI\Form($this, $name);
		
		$form->addText('name', 'Article name');
		$form->addSelect('published', 'Published', array('0' => 'No', '1' => 'Yes'));
		$form->addSelect('category', 'Category')->setItems(array(0 => 'No category') + $this->context->repository->category->fetchPairs('id', 'name'));
		$form->addTextArea('tags', 'Tags (one tag per line)');
		$form->addTextArea('perex', 'Perex');
		$form->addTextArea('text', 'Text');
		
		$form->addSubmit('submit', 'Save Article');
		$form->onSuccess[] = callback($this, 'updateFormSuccess');
	}
	
	public function updateFormSuccess(\Nette\Application\UI\Form $form) {
		$values = $form->getValues();
		
		$data['categories_id'] = $values->category ? $values->category : null;
		$data['name'] = $values->name;
		$data['slug'] = ($this->item && $values->name == $this->item->name) ? $this->item->slug : \RepositoryUtils::slug($this->context->repository->article, $values->name, $this->item ? $this->item->id : null);
		$data['perex'] = $values->perex;
		$data['text'] = $values->text;
		$data['tags'] = $values->tags;
		$data['published'] = $values->published;
		
		if($this->item && $values->published && !$this->item->created) {
			$data['created'] = date('Y-m-d H:i:s');
		} elseif (!$this->item && $values->published) {
			$data['created'] = date('Y-m-d H:i:s');
		}
		
		if($this->item && $values->published && $this->item->created) {
			$data['edited'] = date('Y-m-d H:i:s');
		}
		
		if($this->item) {
			$this->item->update($data);
		} else {
			$this->context->repository->article->insert($data);
		}
		
		$this->flashMessage('Article has been successfully saved');
		$this->redirect('Article:');
	}
}