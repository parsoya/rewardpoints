<?php
namespace BkdRwd\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $dql = "SELECT a FROM BkdRwd\Entity\Program a WHERE a.id !='' ORDER BY a.created DESC";
        $query = $entityManager->createQuery($dql);
        $query->setMaxResults(30);
        $programs = $query->getResult();

        //Top 5 producted programs;

        $dql = "SELECT count(a.id) as total, a.id FROM BkdRwd\Entity\Program a, BkdRwd\Entity\Product c where a.id = c.program GROUP BY a.id ORDER BY total DESC";
        $query = $entityManager->createQuery($dql);
        $result = $query->getResult();

        //second way;
        //http://stackoverflow.com/questions/11137395/doctrine-2-does-not-recognize-select-on-the-from-clause

        //$qb = $entityManager->createQueryBuilder();
        //$qb->select(array('count(a.id) as total, a.id'))
        //->from('BkdRwd\Entity\Program a, BkdRwd\Entity\Product c')
        //->where('a.id = c.program')
        //->groupBy('a.id')
        //->orderBy('total','DESC'); //missing the object.. follow bottom steps;

        //$result = $qb->getQuery()->getResult();

        $dql1 = '';
        $mostC = '';
		$mostProductedPrograms = Array();
		$countOfProducts = Array();
        foreach ($result as $resul) {
            $dql1 = "SELECT a FROM BkdRwd\Entity\Program a where a.id = ". $resul['id'];
            //echo $dql1;
            $query = $entityManager->createQuery($dql1);
            $query->setMaxResults(5);
            $result1 = $query->getResult();
            $mostProductedPrograms[] = $result1[0]; //List.Add($result[0]);
            $countOfProducts[] = $resul['total'];
        }

        $dql = "SELECT a FROM BkdRwd\Entity\Program a WHERE a.id!='' ORDER BY a.viewCount DESC";
        $query = $entityManager->createQuery($dql);
        $query->setMaxResults(5);
        $mostPreviewedPrograms = $query->getResult();

        return new ViewModel(array('programs' => $programs, 'mostProductedPrograms' => $mostProductedPrograms, 'countOfProducts' => $countOfProducts,
        'mostPreviewedPrograms' => $mostPreviewedPrograms));
    }

}
