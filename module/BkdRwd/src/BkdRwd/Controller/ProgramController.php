<?php

namespace BkdRwd\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Element;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder as DoctrineAnnotationBuilder;
use BkdRwd\Entity\Program;
// Pagination
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;

class ProgramController extends AbstractActionController {

    public function __construct() {
        //new Paginator();
    }

    // R - retrieve
    public function indexAction() {
        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

        $dql = "SELECT a, u, c FROM BkdRwd\Entity\Program a LEFT JOIN a.author u  LEFT JOIN a.categories c WHERE a.id !=''";
        $query = $entityManager->createQuery($dql);
        $query->setMaxResults(30);
        $programs = $query->getResult();

        $repository = $entityManager->getRepository('BkdRwd\Entity\Program');
        $adapter = new DoctrineAdapter(new ORMPaginator($repository->createQueryBuilder('program')));

        // Create the paginator itself
        $paginator = new Paginator($adapter);
        $page = 1;
        if ($this->params()->fromRoute('page'))
            $page = $this->params()->fromRoute('page');
        $paginator->setCurrentPageNumber((int) $page)
                ->setItemCountPerPage(5);


        return new ViewModel(array('programs' => $programs, 'paginator' => $paginator));
    }

    // C - create
    public function addAction() {
        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $program = new Program;
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

                return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'program', 'action' => 'index'));
            }
        }

        return new ViewModel(array('form' => $form));
    }

    // U - update
    public function editAction() {
        $id = $this->params()->fromRoute('id');
        if (!$id) {
            return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'program', 'action' => 'index'));
        }

        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

        try {
            $program = $entityManager->find('BkdRwd\Entity\Program', $id);
        } catch (\Exception $ex) {
            echo $ex->getMessage(); // this will never be seen if you don't product the redirect

            return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'program', 'action' => 'index'));
        }

        $form = $this->getForm($program, $entityManager, 'Update');
        $form->bind($program);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $form->setData($post);
            if ($form->isValid()) {
                //$this->prepareData($program);
                $entityManager->persist($program);
                $entityManager->flush();

                return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'program', 'action' => 'index'));
            }
        }

        return new ViewModel(array('form' => $form, 'id' => $id));
    }

    // D - delete
    public function deleteAction() {
        $id = $this->params()->fromRoute('id');
        if (!$id) {
            return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'program', 'action' => 'index'));
        }

        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

        $repository = $entityManager->getRepository('BkdRwd\Entity\Program');
        $program = $repository->find($id);

        if ($program) {
            //Delete all products first
            $repository = $entityManager->getRepository('BkdRwd\Entity\Product');
            $products = $repository->find($id);

            if ($products) {
                foreach ($products as $product) {
                    $entityManager()->remove($product);
                }
            }

            //Delete votes
            $voteId = $program->getVote();
            $entityManager->remove($voteId);
            //Delete the program
            $entityManager->remove($program);
            $entityManager->flush();
            return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'program', 'action' => 'index'));
        }

        return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'program', 'action' => 'index'));
    }

    public function viewAction() {
        $id = $this->params()->fromRoute('id');
        if (!$id) {
            return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'index', 'action' => 'index'));
        }

        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

        try {
            $program = $entityManager->find('BkdRwd\Entity\Program', $id);
            if (!is_object($program)) {
                return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'index', 'action' => 'index'));
            }
        } catch (\Exception $ex) {
            echo $ex->getMessage(); // this will never be seen if you don't product the redirect

            return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'index', 'action' => 'index'));
        }

        //--- Decide whether the user has access to this program ---------------
      /*  $resource = $program->getResource()->getName();
        $privilege = 'view';
        if (!$this->isAllowed($resource, $privilege)) {
            return $this->redirect()->toRoute('home');
        } */
        //END --- Decide whether the user has access to this program -----------

        //--- Increase the View Count ------------------------------------------
        $counterViews = $program->getViewCount();
        $counterViews +=1;
        $program->setViewCount($counterViews);
        $entityManager->persist($program);
        $entityManager->flush();
        //END --- Increase the View Count --------------------------------------
        
        //--- Get all products -------------------------------------------------
        $dql = "SELECT c, a FROM BkdRwd\Entity\Product c LEFT JOIN c.program a WHERE a.id = ?1";
        $query = $entityManager->createQuery($dql);
        $query->setMaxResults(30);
        $query->setParameter(1, $id);
        $products = $query->getResult();
        //END --- Get all products ---------------------------------------------

        //if ($program->getLayout()) {$this->layout($program->getLayout());}

        return new ViewModel(array('program' => $program, 'products' => $products));
    }

    public function voteAction() {
        $id2 = $this->params()->fromRoute('id2');
        $id = $this->params()->fromRoute('id');
        if (!$id) {
            return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'index', 'action' => 'index'));
        }
        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

        $program = $entityManager->find('BkdRwd\Entity\Program', $id);

        $currentVoteCount = 0;

        if ($id2 > 0) {
            $currentVoteCount = $program->getVote()->getLikesCount();
            $currentVoteCount++;
            $program->getVote()->setLikesCount($currentVoteCount);
        } else {
            $currentVoteCount = $program->getVote()->getDislikesCount();
            $currentVoteCount++;
            $program->getVote()->setDislikesCount($currentVoteCount);
        }

        $usersVoted = $program->getVote()->getUsersVoted();
        $usersVoted[] = $this->identity();

        try {

            $entityManager->persist($program);
            $entityManager->flush();
        } catch (\Exception $ex) {

        }



        return $this->redirect()->toRoute('bkd-rwd/default', array('controller' => 'program', 'action' => 'view', 'id' => $id));
    }

    public function getForm($program, $entityManager, $action) {
        $builder = new DoctrineAnnotationBuilder($entityManager);
        $form = $builder->createForm($program);

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
        $form->remove('author');
        $form->setHydrator(new DoctrineHydrator($entityManager, 'BkdRwd\Entity\Program'));
        $send = new Element('send');
        $send->setValue($action); // submit
        $send->setAttributes(array(
            'type' => 'submit'
        ));
        $form->add($send);

        return $form;
    }

    public function prepareData($program) {
        $program->setCreated(new \DateTime());
        $auth = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        if ($auth->hasIdentity()) {
            $user = $auth->getIdentity();
        }
        $program->setAuthor($user);

        $vote = new \BkdRwd\Entity\Vote();
        $program->setVote($vote);
    }
    
}
