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

namespace BkdRwd\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Vote can be used to vote for other entities.
 * 
 * A vote object contains information about the current vote (it can be likes or rating, etc) and the users who participated in it. Users who have already rewarded should not be allowed to vote again for tha same entity.
 *
 * @ORM\Table(name="Reward")
 * @ORM\Entity
 */
class Reward
{
    /**
     * @var integer
     *
     * @ORM\Column(name="total_requested", type="integer", nullable=false)
     */
    protected $total_requested = 0;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="total_approved", type="integer", nullable=false)
     */
    protected $total_approved = 0;

    /**
     * Holds a Collection of the users who rewarded for the entity, this object is attached to.
     *
     * @ORM\ManyToMany(targetEntity="BkdUser\Entity\User")
     * @ORM\JoinTable(name="rewards_training",
     *      joinColumns={@ORM\JoinColumn(name="reward_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     *      )
     */
    protected $usersRewarded;

    /**
     * @var DateTime
     * @ORM\ManyToMany(targetEntity="BkdUser\Entity\User")
     * @ORM\JoinTable(name="rewards_training",
     *      joinColumns={@ORM\JoinColumn(name="reward_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     *      )
     *  @ORM\Column(name="completion_date", type="date")
    */

    protected $completion_date;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    public function __construct()
    {
        $this->usersRewarded = new ArrayCollection();
    }

    /**
     * Set total_requested
     *
     * @param int $total_requested
     * @return Reward
     */
    public function setRequestedCount($total_requested)
    {
        $this->total_requested = $total_requested;

        return $this;
    }

    /**
     * Get total_requested
     *
     * @return int
     */
    public function getRequestedCount()
    {
        return $this->total_requested;
    }
    
    /**
     * Set total_approved
     *
     * @param int $total_approved
     * @return Reward
     */
    public function setApprovedCount($total_approved)
    {
        $this->total_approved = $total_approved;

        return $this;
    }

    /**
     * Get total_approved
     *
     * @return int
     */
    public function getApprovedCount()
    {
        return $this->total_approved;
    }
    
    /**
     * Get users who already rewarded
     *
     * @return array
     */
    public function getUsersRewarded()
    {
        return $this->usersRewarded;
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

    /**
     * Get Created
     *
     * @return DateTime
     */
    public function getCompletionDate()
    {
        return $this->completion_date;
    }

    /**
     * Set Created
     *
     * @param  DateTime $completion_date
     * @return Reward
     */
    public function setCompletionDate($completion_date)
    {
        $this->completion_date = $completion_date;

        return $this;
    }
}