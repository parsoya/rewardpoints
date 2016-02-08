<?php
/**
 * Coolbkd Zend Framework 2 CMS Module
 * 
 * @link https://github.com/coolbkd/BkdRwd for the canonical source repository
 * @copyright Copyright (c) 2005-2013 LightSoft 2005 Ltd. Bulgaria
 * @license https://github.com/coolbkd/BkdRwd/blob/master/LICENSE BSDLicense
 * @author Stoyan Cheresharov <stoyan@coolbkd.com>
 * @author Stoyan Revov <st.revov@gmail.com>
 */

namespace BkdRwd\View\Helper;

use Zend\View\Helper\AbstractHelper;

class ProgramIntro extends AbstractHelper {
    protected $entityManager;

    public function __construct($entityManager) {
        $this->entityManager = $entityManager;
    }
    
    /**
     * Displays an program's introtext if the user has access to it,
     * otherwise returns an empty string.
     * 
     * @param int $id
     * @param int $indent Number of white spaces to prepend. Defaults to 0.
     * @throws Exception If an program with this id is not found.
     */
    public function __invoke($id, $indentSpaces = 0) {
        $program = $this->entityManager->find('BkdRwd\Entity\Program', $id);
        if(!$program) {
            throw new \Exception('Program with id=' . $id . ' not found.');
        }
        if($this->getView()->isAllowed($program->getResource()->getName(), 'view')) {
            $indent = \str_repeat(' ', $indentSpaces);
            
            $html  =           '<program>' . PHP_EOL;
            $html .= $indent . '    <h3>' . PHP_EOL;
            $html .= $indent . '        <a href="' . $this->getView()->url('bkd-rwd/default',
                            array('controller' => 'program', 'action'=>'view', 'id' => $program->getId())) . '">'
                            . $program->getTitle() . '</a>' . PHP_EOL;
            $html .= $indent . '    </h3>' . PHP_EOL;
            $html .= $indent . '    <p class="program-introtext">' . $program->getIntrotext() . '</p>' . PHP_EOL;
            $html .= $indent . '</program>' . PHP_EOL;
            
            return $html;
        } else {
            return "";
        }
    }
}