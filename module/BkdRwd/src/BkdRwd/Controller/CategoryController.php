<?php

namespace BkdRwd\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Form\Element;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder as DoctrineAnnotationBuilder;

use BkdRwd\Entity\Category;

class CategoryController extends AbstractActionController
{
    // R - retrieve
    public function indexAction()
    {
        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $dql = "SELECT c FROM BkdRwd\Entity\Category c ";
        $query = $entityManager->createQuery($dql);
        $query->setMaxResults(30);
        $categories = $query->getResult();

        return new ViewModel(array('categories' => $categories));
    }

    // C - create
    public function addAction()
    {
        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $category = new Category;
        $form = $this->getForm($category, $entityManager, 'Add');

        //hide the id element(bug in doctrine maybe)
        try {
            $form->get('id')->setAttribute('type','hidden');
        } catch (\Exception $ex) {

        }

        $form->bind($category);

        $request = $this->getRequest();
        if ($request->isPost()) {
                $post = $request->getPost();
                $form->setData($post);
                if ($form->isValid()) {
                    $entityManager->persist($category);
                    $entityManager->flush();

                    return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'category', 'action' => 'index'));
                }
        }

        return new ViewModel(array('form' => $form));
    }

    // U - update
    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        if (!$id) {
            return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'category', 'action' => 'index'));
        }

        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

        try {
            $category = $entityManager->find('BkdRwd\Entity\Category', $id);
        } catch (\Exception $ex) {
            echo $ex->getMessage(); // this will never be seen if you don't product the redirect

            return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'category', 'action' => 'index'));
        }

        $form = $this->getForm($category, $entityManager, 'Update');

        //hide the id element(bug in doctrine maybe)
        //$form->get('id')->setAttribute('type','hidden');

        try {
            $form->get('id')->setAttribute('type','hidden');
        } catch (\Exception $ex) {

        }
        $form->bind($category);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $form->setData($post);
            if ($form->isValid()) {
                //$this->prepareData($category);
                $entityManager->persist($category);
                $entityManager->flush();

            return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'category', 'action' => 'index'));
            }
        }

        return new ViewModel(array('form' => $form, 'id' => $id));
    }

    // D - delete
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id');
        if (!$id) {
            return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'category', 'action' => 'index'));
        }

        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

        try {
            $category = $entityManager->find('BkdRwd\Entity\Category', $id);
            $entityManager->remove($category);
            $entityManager->flush();
        } catch (\Exception $ex) {
            echo $ex->getMessage(); // this will never be seen if you don't product the redirect

            return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'category', 'action' => 'index'));
        }

        return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'category', 'action' => 'index'));
    }
	
	public function viewAction()
    {
		$id = $this->params()->fromRoute('id');
        if (!$id) {
            return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'category', 'action' => 'index'));
        }
		
		$entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

		$category = $entityManager->find('BkdRwd\Entity\Category', $id);

        return new ViewModel(array('category' => $category));
		
	}

    public function getForm($category, $entityManager, $action)
    {
        $builder = new DoctrineAnnotationBuilder($entityManager);
        $form = $builder->createForm( $category );

        //!!!!!! Start !!!!! Added to make the association tables work with select
        foreach ($form->getElements() as $element) {
            if (method_exists($element, 'getProxy')) {
                $proxy = $element->getProxy();
                if (method_exists($proxy, 'setObjectManager')) {
                    $proxy->setObjectManager($entityManager);
                }
            }
        }

        $form->remove('created');
        $form->remove('parent');
        $form->remove('author');
        $form->setHydrator(new DoctrineHydrator($entityManager,'BkdRwd\Entity\Category'));
        $send = new Element('send');
        $send->setValue($action); // submit
        $send->setAttributes(array(
                'type'  => 'submit'
        ));
        $form->add($send);

        return $form;
    }
}
