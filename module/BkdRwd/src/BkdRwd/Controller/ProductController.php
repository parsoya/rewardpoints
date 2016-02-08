<?php

namespace BkdRwd\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Zend\Form\Annotation\AnnotationBuilder;

use Zend\Form\Element;

// hydration tests
use Zend\Stdlib\Hydrator;

// for Doctrine annotation
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder as DoctrineAnnotationBuilder;

//- use Doctrine\Common\Persistence\ObjectManager;

use BkdRwd\Entity\Product;

class ProductController extends AbstractActionController
{
    public function indexAction()
    {
        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $isAllowedProducts=true;
        $id = (int) $this->params()->fromRoute('id', 0);
        try {
            $repository = $entityManager->getRepository('BkdRwd\Entity\Program');
            $program = $repository->findOneBy(array('id' => $id));

        } catch (\Exception $ex) {
           return $this->redirect()->toRoute('bkd-rwd/default', array(
               'controller' => 'index',
                'action' => 'index'
            ));
        }
            if (!$id) return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'index', 'action' => 'index'));

            $dql = "SELECT c, u, a  FROM BkdRwd\Entity\Product c LEFT JOIN c.author u  LEFT JOIN c.program a WHERE a.id = ?1";
            $query = $entityManager->createQuery($dql);
            $query->setMaxResults(30);
            $query->setParameter(1, $id);
            // I will get a collection of Programs
            $products = $query->getResult();

            return new ViewModel(array(
                'id' => $id,
                'products' => $products
            ));
    }

    public function addAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $isAllowedProducts=true;
        if (!$id) return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'program', 'action' => 'index'));

        try {
            $repository = $entityManager->getRepository('BkdRwd\Entity\Program');
            $program = $repository->findOneBy(array('id' => $id));
            $isAllowedProducts = $program->getAllowProducts();

        } catch (\Exception $ex) {
           return $this->redirect()->toRoute('bkd-rwd/default', array(
               'controller' => 'index',
                'action' => 'index'
            ));
        }

        if ($isAllowedProducts) {
            $product = new Product;
            $product->setProgram($program);
            $builder = new DoctrineAnnotationBuilder($entityManager);
            $form = $builder->createForm( $product );

            $form->remove('created');
            $form->remove('author');
            $form->remove('program');

            //hide the id element(bug in doctrine maybe)
            //otherwise method to hide without bug;
            try {
                $form->get('id')->setAttribute('type','hidden');
            } catch (\Exception $ex) {

            }
            //$repository = $entityManager->getRepository('BkdUser\Entity\Language');
            //$language = $repository->findOneBy(array('abbreviation' => 'en'));
            //$product->setLanguage($language);

            foreach ($form->getElements() as $element) {
                if (method_exists($element, 'getProxy')) {
                    $proxy = $element->getProxy();
                    if (method_exists($proxy, 'setObjectManager')) {
                        $proxy->setObjectManager($entityManager);
                    }
                }
            }

            $form->setHydrator(new DoctrineHydrator($entityManager,'BkdRwd\Entity\Product'));

            $send = new Element('send');
            $send->setValue('Add'); // submit
            $send->setAttributes(array(
                'type'  => 'submit'
            ));
            $form->add($send);
            $form->bind($product);

            $request = $this->getRequest();
            if ($request->isPost()) {
                 $form->setData($request->getPost());
                  if ($form->isValid()) {
                    $this->prepareData($product);
                    $entityManager->persist($product);
                    $entityManager->flush();

                    return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'product', 'action' => 'index', 'id' => $id), true);
                  }
            }

            return new ViewModel(array(
                'isAllowedProducts' => $isAllowedProducts,
                'id' => $id,
                'form' => $form
            ));
        } else {
            return new ViewModel(array(
                'isAllowedProducts' => $isAllowedProducts,
                'id' => $id
            ));

        }
        /*

        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $product = new Product;
        //$program = new Program;
        $form = $this->getForm($product, $entityManager, 'Add');

        $form->bind($product);

        $request = $this->getRequest();
        if ($request->isPost()) {
                $post = $request->getPost();
                $form->setData($post);
                if ($form->isValid()) {
                    $this->prepareData($product);
                    $entityManager->persist($product);
                    $entityManager->flush();

                    return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'product', 'action' => 'index'));
                }
        }

        //return new ViewModel(array('form' => $form,'id'=>$id));
        /*
        /*/
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id2', 0);
        if (!$id) {
            return $this->redirect()->toRoute('bkd-rwd/default', array(
                'controller' => 'product',
                'action' => 'add'
            ), true);
        }
        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        try {
            $repository = $entityManager->getRepository('BkdRwd\Entity\Product');
            $product = $repository->getProductForEdit($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('bkd-rwd/default', array(
                'controller' => 'product',
                'action' => 'index'
            ), true);
        }

        $builder = new DoctrineAnnotationBuilder($entityManager);
        $form = $builder->createForm( $product );
        $form->remove('created');
        $form->remove('author');
        $form->remove('program');

        //hide the id element(bug in doctrine maybe)
        try {
            $form->get('id')->setAttribute('type','hidden');
        } catch (\Exception $ex) {

        }

        foreach ($form->getElements() as $element) {
            if (method_exists($element, 'getProxy')) {
                $proxy = $element->getProxy();
                if (method_exists($proxy, 'setObjectManager')) {
                    $proxy->setObjectManager($entityManager);
                }
            }
        }

        $form->setHydrator(new DoctrineHydrator($entityManager,'BkdRwd\Entity\Product'));
        $send = new Element('send');
        $send->setValue('Edit');
        $send->setAttributes(array(
            'type'  => 'submit'
        ));
        $form->add($send);

        $form->bind($product);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $entityManager->persist($product);
                $entityManager->flush();

                 return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'product', 'action' => 'index'), true);
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id2', 0);
        if (!$id) {
            return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'product', 'action' => 'index'), true);
        }

        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        try {
            $repository = $entityManager->getRepository('BkdRwd\Entity\Product');
            $product = $repository->find($id);
            $entityManager->remove($product);
            $entityManager->flush();
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('bkd-rwd/default', array(
                'controller' => 'product',
                'action' => 'index'
            ), true);
        }

        return $this->redirect()->toRoute('bkd-rwd/default', array(
                'controller' => 'product',
                'action' => 'index'
        ), true);
    }

    public function prepareData($product)
    {
        $product->setCreated(new \DateTime());
        $auth = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        if ($auth->hasIdentity()) {
            $user = $auth->getIdentity();
        }
        $product->setAuthor($user);
        $reward = new \BkdRwd\Entity\Reward();
        $product->setReward($reward);
    }

    public function getForm($product, $entityManager, $action)
    {

        $builder = new DoctrineAnnotationBuilder($entityManager);
        $form = $builder->createForm( $product );

        //!!!!!! Start !!!!! Added to make the association tables work with select
        foreach ($form->getElements() as $element) {
            if (method_exists($element, 'getProxy')) {
                $proxy = $element->getProxy();
                if (method_exists($proxy, 'setObjectManager')) {
                    $proxy->setObjectManager($entityManager);
                }
            }
        }

        $form->setHydrator(new DoctrineHydrator($entityManager,'BkdRwd\Entity\Product'));

        $send = new Element('send');
        $send->setValue($action); // submit
        $send->setAttributes(array(
                'type'  => 'submit'
        ));
        $form->add($send);

        return $form;
    }

    public function rewardAction() {
        $id2 = $this->params()->fromRoute('id2');
        $id = $this->params()->fromRoute('id');

        $request = $this->getRequest();
        if ($request->isPost()) {
                $post = $request->getPost();
                $date = $post->date;
            }
       
        if (!$id) {
            return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'index', 'action' => 'index'));
        }
        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

        $product = $entityManager->find('BkdRwd\Entity\Product', $id);

        $currentRequestedCount = 0;
        $product->getReward()->setCompletionDate($date);

        if ($id2 > 0) {
            $currentRequestedCount = $product->getReward()->getRequestedCount();
            $currentRequestedCount++;
            $product->getReward()->setRequestedCount($currentRequestedCount);
        } else {
            $currentRequestedCount = $product->getReward()->getApprovedCount();
            $currentRequestedCount++;
            $product->getReward()->setApprovedCount($currentRequestedCount);
        }

        $usersRewarded = $product->getReward()->getUsersRewarded();
        $usersRewarded[] = $this->identity();

        try {

            $entityManager->persist($product);
            $entityManager->flush();
        } catch (\Exception $ex) {

        }

          $result = new JsonModel(array(
              'id' => $id,
            'success'=>true,
        ));

        return $result;

       // return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'program', 'action' => 'view', 'id' => $id));
    }
}
