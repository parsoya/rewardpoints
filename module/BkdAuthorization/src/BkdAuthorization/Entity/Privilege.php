<?php
namespace BkdAuthorization\Entity;

use Doctrine\ORM\Mapping as ORM;

use Zend\Form\Annotation; 

/**
 * Privileges
 *
 * @ORM\Table(name="privilege")
 * @ORM\Entity
 * @Annotation\Name("privilege")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ClassMethods")
 */
class Privilege
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":30}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[a-zA-Z][a-zA-Z0-9_ -]{0,100}$/"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Privilege:"})
     */
    protected $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     */
    protected $id;
    
    /**
     * @var BkdAuthorization\Entity\Resource
     *
     * @ORM\ManyToOne(targetEntity="BkdAuthorization\Entity\Resource")
     * @ORM\JoinColumn(name="resource_id", referencedColumnName="id", nullable=true)
     * @Annotation\Type("DoctrineModule\Form\Element\ObjectSelect")
     * @Annotation\Options({
     * "label":"Resource:",
     * "empty_option": "Please, choose a resource",
     * "target_class":"BkdAuthorization\Entity\Resource",
     * "property": "name"})
     */
    protected $resource;
    
    /**
    * @var BkdUser\Entity\Role
    *
    * @ORM\ManyToOne(targetEntity="BkdUser\Entity\Role")
    * @ORM\JoinColumn(name="role_id", referencedColumnName="id", nullable=false)
    * @Annotation\Type("DoctrineModule\Form\Element\ObjectSelect")
    * @Annotation\Options({
    * "label":"Role:",
    * "empty_option": "Please, choose a role",
    * "target_class":"BkdUser\Entity\Role",
    * "property": "name"})
    */
    protected $role;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="permission_allow", type="boolean", nullable=false)
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Options({
     * "label":"Allow permission:"})
     */
    protected $permissionAllow = true;

    /**
     * Set name
     *
     * @param  string   $name
     * @return Privilege
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set resource
     *
     * @param  BkdAuthorization\Entity\Resource $resource
     * @return BkdAuthorization\Entity\Privilege
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Get resource
     *
     * @return BkdAuthorization\Entity\Resource
     */
    public function getResource()
    {
        return $this->resource;
    }
    
    /**
     * Set role
     *
     * @param  Role $role
     * @return BkdAuthorization\Entity\Privilege
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return Role
     */
    public function getRole()
    {
        return $this->role;
    }
    
    /**
     * Set permissionAllow
     *
     * @param  boolean $permissionAllow
     * @return BkdAuthorization\Entity\Privilege
     */
    public function setPermissionAllow($permissionAllow)
    {
        $this->permissionAllow = $permissionAllow;

        return $this;
    }
    
    /**
     * Get permissionAllow
     *
     * @return boolean
     */
    public function getPermissionAllow()
    {
        return $this->permissionAllow;
    }
    
}