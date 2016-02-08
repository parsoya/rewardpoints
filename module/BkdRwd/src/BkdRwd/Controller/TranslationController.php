<?php

namespace BkdRwd\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Form\Annotation\AnnotationBuilder;

use Zend\Form\Element;

// hydration tests
use Zend\Stdlib\Hydrator;

// for Doctrine annotation
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder as DoctrineAnnotationBuilder;

use BkdRwd\Entity\Program;

class TranslationController extends AbstractActionController
{
    // R - retriev
    public function indexAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'program', 'action' => 'index'));

        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $dql = "SELECT a, u, l, c, h  FROM BkdRwd\Entity\Program a LEFT JOIN a.author u LEFT JOIN a.language l LEFT JOIN a.categories c LEFT JOIN a.children h WHERE a.id = ?1";
        $query = $entityManager->createQuery($dql);
        $query->setMaxResults(30);
        $query->setParameter(1, $id);
        $programs = $query->getResult();

        return new ViewModel(array('programs' => $programs, 'id' => $id));
    }

    // C - create
	public function addAction()
    {
		$id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'program', 'action' => 'index'));
		
        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $program = new Program;
		
		try {
            $repository = $entityManager->getRepository('BkdRwd\Entity\Program');
            $parent = $repository->findOneBy(array('id' => $id));
            $program->setParent($parent);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'program', 'action' => 'index'));
        }
		
        $form = $this->getForm($program, $entityManager, 'Add');

        $form->bind($program);

        $request = $this->getRequest();
        if ($request->isPost()) {
                $post = $request->getPost();
                $form->setData($post);
                if ($form->isValid()) {
                    $this->prepareData($program);
                    $entityManager->persist($program);
                    $entityManager->flush();

					return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'translation', 'action' => 'index', 'id' => $id), true);
                }
        }

        return new ViewModel(array('form' => $form));
    }

    // U - update
    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        if (!$id) return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'program', 'action' => 'index'));

        $id = (int) $this->params()->fromRoute('id2', 0);
        if (!$id) return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'translation', 'action' => 'add'), true);

        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

        try {
            $repository = $entityManager->getRepository('BkdRwd\Entity\Program');
            $program = $repository->find($id);
        } catch (\Exception $ex) {
            echo $ex->getMessage(); // this never will be seen fi you don't product the redirect

            return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'translation', 'action' => 'index'));
        }

        $form = $this->getForm($program, $entityManager, 'Update');

        $form->bind($program);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $form->setData($post);
             if ($form->isValid()) {
                $entityManager->persist($program);
                $entityManager->flush();

                return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'translation', 'action' => 'index'), true);
             }
        }

        return new ViewModel(array('form' => $form, 'id' => $id));
    }

    // D - delete
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id');
        if (!$id) return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'program', 'action' => 'index'));

        $id = (int) $this->params()->fromRoute('id2', 0);
        if (!$id) return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'translation', 'action' => 'index'), true);

        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

        try {
            $repository = $entityManager->getRepository('BkdRwd\Entity\Program');
            $program = $repository->find($id);
            $entityManager->remove($program);
            $entityManager->flush();
        } catch (\Exception $ex) {
            echo $ex->getMessage(); // this never will be seen fi you don't product the redirect

            return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'translation', 'action' => 'index'));
        }

        return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'translation', 'action' => 'index'), true);
    }

    public function viewAction()
    {
       // $id = $this->params()->fromRoute('id');
       // if (!$id) return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'translation', 'action' => 'index'));
       //
       // $id = (int) $this->params()->fromRoute('id2', 0);
       // if (!$id) return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'translation', 'action' => 'index'), true);
       //
       // $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
       //
       // try {
       //     $repository = $entityManager->getRepository('BkdRwd\Entity\Program');
       //     $program = $repository->find($id);
       //     if (!is_object($program)) return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'translation', 'action' => 'index'));
       // } catch (\Exception $ex) {
       //     echo $ex->getMessage(); // this never will be seen fi you don't product the redirect
       //
       //     return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'translation', 'action' => 'index'));
       // }
       //
       // $sm = $this->getServiceLocator();
       // $auth = $sm->get('Zend\Authentication\AuthenticationService');
       // $config = $sm->get('Config');
       // $acl = $sm->get('acl');
       // // everyone is guest untill it gets logged in
       // $role = \BkdAuthorization\Acl\Acl::DEFAULT_ROLE;
       // if ($auth->hasIdentity()) {
       //     $user = $auth->getIdentity();
       //     $role = $user->getRole()->getName();
       // }
       //
       // $resource = $program->getResource()->getName();
       // $privilege = 'view';
       // if (!$acl->hasResource($resource)) {
       //     throw new \Exception('Resource ' . $resource . ' not defined');
       // }
       //
       // if (!$acl->isAllowed($role, $resource, $privilege)) {
       //     return $this->redirect()->toRoute('home');
       // }
       //
       // return new ViewModel(array('program' => $program));
	   
	    $id = $this->params()->fromRoute('id');
        if (!$id) return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'translation', 'action' => 'index'));
       
        $id = (int) $this->params()->fromRoute('id2', 0);
        if (!$id) return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'translation', 'action' => 'index'), true);

        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

        try {
            $program = $entityManager->find('BkdRwd\Entity\Program', $id);
            if (!is_object($program)) {
                return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'translation', 'action' => 'index'));
            }
        } catch (\Exception $ex) {
            echo $ex->getMessage(); // this will never be seen if you don't product the redirect

            return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'index', 'action' => 'index'));
        }

        $counterViews = $program->getViewCount();
        $counterViews +=1;
        $program->setViewCount($counterViews);
        $entityManager->persist($program);
        $entityManager->flush();

        //--- Decide whether the user has access to this program ---------------
        $sm = $this->getServiceLocator();
        $auth = $sm->get('Zend\Authentication\AuthenticationService');
        $config = $sm->get('Config');
        $acl = $sm->get('acl');
        // everyone is guest until it gets logged in
        $role = \BkdAuthorization\Acl\Acl::DEFAULT_ROLE;
        if ($auth->hasIdentity()) {
            $user = $auth->getIdentity();
            $role = $user->getRole()->getName();
    }

        $resource = $program->getResource()->getName();
        $privilege = 'view';
        if (!$acl->hasResource($resource)) {
                throw new \Exception('Resource ' . $resource . ' not defined');
        }

        if (!$acl->isAllowed($role, $resource, $privilege)) {
                return $this->redirect()->toRoute('home');
        }
        //END --- Decide whether the user has access to this program -----------

        //--- Get all products -------------------------------------------------
        $dql = "SELECT c, a FROM BkdRwd\Entity\Product c LEFT JOIN c.program a WHERE a.id = ?1";
        $query = $entityManager->createQuery($dql);
        $query->setMaxResults(30);
        $query->setParameter(1, $id);
        $products = $query->getResult();
        //END --- Get all products ---------------------------------------------
		
		$hasUserVoted = $this->hasUserVoted($program);
		
        return new ViewModel(array('program' => $program, 'products' => $products, 'hasUserVoted' => $hasUserVoted));
    }

    public function getForm($program, $entityManager, $action)
    {
        $builder = new DoctrineAnnotationBuilder($entityManager);
        $form = $builder->createForm( $program );

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
        $form->setHydrator(new DoctrineHydrator($entityManager,'BkdRwd\Entity\Program'));
        $send = new Element('send');
        $send->setValue($action); // submit
        $send->setAttributes(array(
            'type'  => 'submit'
        ));
        $form->add($send);

        return $form;
    }
	
	public function prepareData($program)
    {
        $program->setCreated(new \DateTime());
        $auth = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        if ($auth->hasIdentity()) {
            $user = $auth->getIdentity();
        }
        $program->setAuthor($user);
		
		$vote = new \BkdRwd\Entity\Vote();
		$program->setVote($vote);
    }
	
	public function hasUserVoted($program)
	{
		$entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		
		$dql = "SELECT count(v.id) FROM BkdRwd\Entity\Vote v LEFT JOIN v.usersVoted u WHERE v.id = ?0 AND u.id =?1";
        $query = $entityManager->createQuery($dql);
		
		$programId = $program->getVote()->getId();

		$userId = $this->identity();
		$hasUserVoted = 'no';
		
		if($programId != null && $userId != null)
		{
			$userId = $this->identity()->getId();
			$query->setParameter(0, $programId);
			$query->setParameter(1, $userId);
			$hasUserVoted = $query->getSingleScalarResult();
		}
		
		return $hasUserVoted;
	}
}
