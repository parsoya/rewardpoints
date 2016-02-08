<?php

namespace BkdAuthorization\Acl;

use Zend\Permissions\Acl\Acl as ZendAcl,
    Zend\Permissions\Acl\Role\GenericRole as Role,
    Zend\Permissions\Acl\Resource\GenericResource as Resource;

class AclDb extends ZendAcl {
    /**
     * Default Role
     */
    const DEFAULT_ROLE = 'guest';

    /**
     * Constructor
     *
     * @param $entityManager Inject Doctrine's entity manager to load ACL from Database
     * @return void
     */
    public function __construct($entityManager)
    {
        $roles = $entityManager->getRepository('BkdUser\Entity\Role')->findAll();
        $resources = $entityManager->getRepository('BkdAuthorization\Entity\Resource')->findAll();
        $privileges = $entityManager->getRepository('BkdAuthorization\Entity\Privilege')->findAll();
        
        $this->_addRoles($roles)
             ->_addAclRules($resources, $privileges);
    }

    /**
     * Adds Roles to ACL
     *
     * @param array $roles
     * @return BkdAuthorization\Acl\AclDb
     */
    protected function _addRoles($roles)
    {
        foreach($roles as $role) {
            if (!$this->hasRole($role->getName())) {
                $parents = $role->getParents()->toArray();
                $parentNames = array();
                foreach($parents as $parent) {
                    $parentNames[] = $parent->getName();
                }
                $this->addRole(new Role($role->getName()), $parentNames);
            }
        }

        return $this;
    }

    /**
     * Adds Resources/privileges to ACL
     *
     * @param $resources
     * @param $privileges
     * @return User\Acl
     * @throws \Exception
     */
    protected function _addAclRules($resources, $privileges)
    {
        foreach ($resources as $resource) {
            if (!$this->hasResource($resource->getName())) {
                $this->addResource(new Resource($resource->getName()));
            }
        }
        
        foreach ($privileges as $privilege) {
            if($privilege->getPermissionAllow()) {
                $this->allow($privilege->getRole()->getName(), $privilege->getResource()->getName(), $privilege->getName());
            } else {
                $this->deny($privilege->getRole()->getName(), $privilege->getResource()->getName(), $privilege->getName());
            }
        }

        return $this;
    }
}
