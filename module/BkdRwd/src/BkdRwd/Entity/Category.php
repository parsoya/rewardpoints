<?php
/**
 * Coolbkd Zend Framework 2 CMS Module
 * 
 * @link https://github.com/coolbkd/BkdRwd for the canonical source repository
 * @copyright Copyright (c) 2005-2013 LightSoft 2005 Ltd. Bulgaria
 * @license https://github.com/coolbkd/BkdRwd/blob/master/LICENSE BSDLicense
 * @author Stoyan Cheresharov <stoyan@coolbkd.com>
 * @author Svetoslav Chonkov <svetoslav.chonkov@gmail.com>
 * @author Stoyan Revov <st.revov@gmail.com>
*/

namespace BkdRwd\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Zend\Form\Annotation;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity
 * @Annotation\Name("Category")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ClassMethods")
 */
class Category
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":30}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[a-zA-Z][a-zA-Z0-9_-]{0,24}$/"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Program Type:"})
     */
    protected $name;

    /**
     * Bidirectional - Not neccessary !!! many category to many Program (INVERSE SIDE)
     *
     * @ORM\ManyToMany(targetEntity="BkdRwd\Entity\Program", mappedBy="categories")
     * @Annotation\Exclude()
     */
    protected $programs;
    
    /**
     * Represents an User, who owns this category. Null if general category.
     *
     * @ORM\ManyToOne(targetEntity="BkdUser\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     * @Annotation\Exclude()
     */
    protected $user;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     */
    protected $id;

    public function __construct()
    {
        $this->programs = new ArrayCollection();
    }

    /**
     * Set Name
     *
     * @param  string   $name
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get programs
     *
     * @return array
     */
    public function getPrograms()
    {
        return $this->programs;
    }

    /**
     * Add program
     *
     * @return Collection
     */
    public function addProgram(\BkdRwd\Entity\Program $program)
    {
        return $this->programs[] = $program;
    }

    public function removeProgram(\BkdRwd\Entity\Program $program)
    {
        $this->programs->removeElement($program);
    }
    
    /**
     * Set user
     *
     * @param  BkdUser\Entity\User $user
     * @return Category
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return BkdUser\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * Get Id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
