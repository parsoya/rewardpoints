<?php
/**
 * Coolbkd Zend Framework 2 CMS Module
 * 
 * @link https://github.com/coolbkd/BkdRwd for the canonical source repository
 * @copyright Copyright (c) 2005-2013 LightSoft 2005 Ltd. Bulgaria
 * @license https://github.com/coolbkd/BkdRwd/blob/master/LICENSE BSDLicense
 * @author Stoyan Cheresharov <stoyan@coolbkd.com>
 * @author Svetoslav Chonkov <svetoslav.chonkov@gmail.com>
*/


namespace BkdRwd\Entity;
use Doctrine\ORM\Mapping as ORM;

use Zend\Form\Annotation;
// children - are the transaltions
// parent - is the original program

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="BkdRwd\Entity\Repository\ProductRepository")
 * @Annotation\Name("Product")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ClassMethods")
 */
class Product
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
     * @var BkdRwd\Entity\Program
     *
     * @ORM\ManyToOne(targetEntity="BkdRwd\Entity\Program", inversedBy="products")
     * @ORM\JoinColumn(name="program_id", referencedColumnName="id")
     * @Annotation\Type("DoctrineModule\Form\Element\ObjectSelect")
     * @Annotation\Options({
     * "label":"Program:",
     * "empty_option": "Please, choose the Program",
     * "target_class":"BkdRwd\Entity\Program",
     * "property": "program"})
     */

    private $program;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=100, nullable=false)
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":100}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Title:"})
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="integer", nullable=true)
     * @Annotation\Attributes({"type":"textarea"})
     * @Annotation\Options({"label":"Text:"})
     */
    private $text;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     * @Annotation\Attributes({"type":"Zend\Form\Element\DateTime", "id": "created", "min":"2010-01-01T00:00:00Z", "max":"2020-01-01T00:00:00Z", "step":"1"})
     * @Annotation\Options({"label":"Date\Time:", "format":"Y-m-d\TH:iP"})
     */

    protected $created;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     */
    private $id;

    /**
     * @var Reward
     *
     * @ORM\OneToOne(targetEntity="BkdRwd\Entity\Reward", cascade={"persist"})
     * @ORM\JoinColumn(name="reward_id", referencedColumnName="id")
     * @Annotation\Exclude()
     */
    protected $reward = 0;

    public function __construct()
    {
    }
    public function __toString()
    {
        return $this->author . ' -> '.$this->text ;
    }


    /**
     * Set author
     *
     * @param  BkdUser\Entity\User   $author
     * @return BkdRwd\Entity\Product
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
     * Set program
     *
     * @param  BkdRwd\Entity\Program $program
     * @return BkdRwd\Entity\Product
     */
    public function setProgram($program)
    {
        $this->program = $program;

        return $this;
    }

    /**
     * Get program
     *
     * @return BkdRwd\Entity\Program
     */
    public function getProgram()
    {
        return $this->program;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
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
     * Get text
     *
     * @return integer
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set text
     *
     * @param  integer  $text
     * @return Product
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get created
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set created
     *
     * @param  DateTime $created
     * @return Product
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

      /**
     * Set reward
     *
     * @param  Reward $reward
     * @return Product
     */
    public function setReward($reward)
    {
        $this->reward = $reward;

        return $this;
    }
    
    /**
     * Get reward
     *
     * @return Reward
     */
    public function getReward()
    {
        return $this->reward;
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

}

/*
@var BkdRwd\Entity\Program

 @ORM\ManyToOne(targetEntity="BkdRwd\Entity\Program") - Unidirectional
*/
