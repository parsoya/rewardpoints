<?php

namespace BkdAuthorization\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class IsAllowed extends AbstractPlugin {

    protected $auth;
    protected $acl;

    public function __construct($auth, $acl) {
        $this->auth = $auth;
        $this->acl = $acl;
    }

    /**
     * Checks whether the current user has acces to a resource.
     * 
     * @param string $resource
     * @param string $privilege
     */
    public function __invoke($resource, $privilege = null) {
        if ($this->auth->hasIdentity()) {
            $user = $this->auth->getIdentity()->getRole()->getName();
            if (!$this->acl->hasResource($resource)) {
                throw new \Exception('Resource ' . $resource . ' not defined');
            }
            return $this->acl->isAllowed($user, $resource, $privilege);
        } else {
            return $this->acl->isAllowed(\BkdAuthorization\Acl\Acl::DEFAULT_ROLE, $resource, $privilege);
        }
    }

}
