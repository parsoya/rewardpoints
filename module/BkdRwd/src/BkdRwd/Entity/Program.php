<?php
/**
 * Coolbkd Zend Framework 2 CMS Module
 * 
 * @link https://github.com/coolbkd/BkdRwd for the canonical source repository
 * @copyright Copyright (c) 2005-2013 LightSoft 2005 Ltd. Bulgaria
 * @license https://github.com/coolbkd/BkdRwd/blob/master/LICENSE BSDLicense
 * @author Stoyan Cheresharov <stoyan@coolbkd.com>
 * @author Stoyan Revov <st.revov@gmail.com>
 * @author Svetoslav Chonkov <svetoslav.chonkov@gmail.com>
*/

namespace BkdRwd\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Zend\Form\Annotation;

// children - are the transaltions
// parent - is the original program

/**
 * Program
 *
 * @ORM\Table(name="program")
 * @ORM\Entity
 * @Annotation\Name("Program")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ClassMethods")
 */
class Program
{
    

    /**
     * @var BkdUser\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="BkdUser\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @Annotation\Type("DoctrineModule\Form\Element\ObjectSelect")
     * @Annotation\Options({
     * "label":"Author:",
     * "empty_option": "Please, choose the Author",
     * "target_class":"BkdUser\Entity\User",
     * "property": "username"})
     */
       protected $author;

    /**
     * @var BkdAuthorization\Entity\Resource
     *
     * @ORM\ManyToOne(targetEntity="BkdAuthorization\Entity\Resource")
     * @ORM\JoinColumn(name="resource_id", referencedColumnName="id")
     * @Annotation\Type("DoctrineModule\Form\Element\ObjectSelect")
     * @Annotation\Options({
     * "label":"Resource:",
     * "empty_option": "Please, choose the Resource",
     * "target_class":"BkdAuthorization\Entity\Resource",
     * "property": "name"})
     */
    protected $resource;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=100, nullable=false)
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":100}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[a-zA-Z][a-zA-Z0-9_ -]{0,100}$/"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Title:"})
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="full_text", type="text", nullable=true)
     * @Annotation\Attributes({"type":"textarea"})
     * @Annotation\Options({"label":"Full Text:"})
     */
    protected $fulltext;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     * @Annotation\Attributes({"type":"Zend\Form\Element\DateTime", "id": "created", "min":"2010-01-01T00:00:00Z", "max":"2020-01-01T00:00:00Z", "step":"1"})
     * @Annotation\Options({"label":"Date\Time:", "format":"Y-m-d\TH:iP"})
     */
    protected $created;

    /**
     * @var BkdRwd\Entity\Category
     *
     * @ORM\ManyToMany(targetEntity="BkdRwd\Entity\Category", inversedBy="programs")
     * @ORM\JoinTable(name="programs_categories",
     *      joinColumns={@ORM\JoinColumn(name="program_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id")}
     *      )
     * @Annotation\Type("DoctrineModule\Form\Element\ObjectSelect")
     * @Annotation\Attributes({"multiple":true})
     * @Annotation\Options({
     * "label":"Categories:",
     * "empty_option": "Please, choose the categories",
     * "target_class":"BkdRwd\Entity\Category",
     * "property": "name"})
     */
    protected $categories;

    /**
     * @var Product[]
     *
     * @ORM\OneToMany(targetEntity="BkdRwd\Entity\Product", mappedBy="program", cascade="remove")
     * @Annotation\Exclude()
     */
    protected $products;

    /**
     * @var boolean
     *
     * @ORM\Column(name="allow_products", type="boolean", nullable=true)
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Options({
     * "label":"Allow products:"})
     */
    protected $allowProducts = true;

    /**
     * @var integer
     *
     * @ORM\Column(name="view_count", type="integer", nullable=false)
     * @Annotation\Exclude()
     */
    protected $viewCount = 0;

    /**
     * @var Vote
     *
     * @ORM\OneToOne(targetEntity="BkdRwd\Entity\Vote", cascade={"persist"})
     * @ORM\JoinColumn(name="vote_id", referencedColumnName="id")
     * @Annotation\Exclude()
     */
    protected $vote = 0;

	
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
        $this->categories = new ArrayCollection;
        $this->products = new ArrayCollection;
        $this->created = new \DateTime();
    }

   
    /**
     * Set author
     *
     * @param  BkdUser\Entity\User   $author
     * @return BkdRwd\Entity\Program
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return ORM\ManyToMany\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set resource
     *
     * @param  BkdAuthorization\Entity\Resource $resource
     * @return BkdRwd\Entity\Program
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
     * Get Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set Title
     *
     * @param  string  $title
     * @return Program
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

   
    /**
     * Get Fulltext
     *
     * @return string
     */
    public function getFulltext()
    {
        return $this->fulltext;
    }

    /**
     * Set Fulltext
     *
     * @param  string  $fulltext
     * @return Program
     */
    public function setFulltext($fulltext)
    {
        $this->fulltext = $fulltext;

        return $this;
    }

    /**
     * Get Created
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set Created
     *
     * @param  DateTime $created
     * @return Program
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get categories
     *
     * @return Array
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set categories
     *
     * @param  array   $categories
     * @return Program
     */
    public function setCategories($categories)
    {
        $this->categories = $categories; // NOT neccessary

        return $this;
    }

    /**
     * Add Catgories
     *
     * @param  Collection $categories
     * @return Program
     */
    public function addCategories(Collection $categories)
    {
        foreach ($categories as $category) {
                $this->addCategory($category);
        }

        return $this;
    }

    /**
     * Add Catgory
     *
     * @param  BkdRwd\Entity\Category $category
     * @return Program
     */
    public function addCategory(\BkdRwd\Entity\Category $category)
    {
        $category->addProgram($this); // synchronously updating inverse side
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove Categories
     *
     * @param  Collection $categories
     * @return Program
     */
    public function removeCategories(Collection $categories)
    {
        foreach ($categories as $category) {
                $this->removeCategory($category);
        }

        return $this;
    }

    /**
     * Remove Category
     *
     * @param  BkdRwd\Entity\Category $category
     * @return Program
     */
    public function removeCategory(\BkdRwd\Entity\Category $category)
    {
        $this->categories->removeElement($category);
        $category->removeProgram($this); // update the other site

        return $this;
    }

   

    /**
     * Get products
     *
     * @return Product[]
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Get allowProducts
     *
     * @return boolean
     */
    public function getAllowProducts()
    {
        return $this->allowProducts;
    }

    /**
     * Set allowProducts
     *
     * @param  boolean $allowProducts
     * @return Program
     */
    public function setAllowProducts($allowProducts)
    {
        $this->allowProducts = $allowProducts;

        return $this;
    }

    /**
     * Get viewCount
     *
     * @return integer
     */
    public function getViewCount()
    {
        return $this->viewCount;
    }

    /**
     * Set viewCount
     *
     * @param  boolean $viewCount
     * @return Program
     */
    public function setViewCount($viewCount)
    {
        $this->viewCount = $viewCount;

        return $this;
    }

    /**
     * Set vote
     *
     * @param  Vote $vote
     * @return Program
     */
    public function setVote($vote)
    {
        $this->vote = $vote;

        return $this;
    }
	
   
    
    /**
     * Get vote
     *
     * @return Vote
     */
    public function getVote()
    {
        return $this->vote;
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
