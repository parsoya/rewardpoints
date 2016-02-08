<?php

/**
 * Coolbkd Zend Framework 2 CMS Module
 * 
 * @link https://github.com/coolbkd/BkdRwd for the canonical source repository
 * @copyright Copyright (c) 2005-2013 LightSoft 2005 Ltd. Bulgaria
 * @license https://github.com/coolbkd/BkdRwd/blob/master/LICENSE BSDLicense
 * @author Stoyan Revov <st.revov@gmail.com>
 * @author Stoyan Cheresharov <stoyan@coolbkd.com>
 */

namespace BkdRwd\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Reward extends AbstractHelper {

    protected $entityManager;

    public function __construct($entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * Generates html with options to reward and display current viewCount
     * 
     * @param \BkdRwd\Entity\Reward $reward
     * @param string $rewardUrl
     * @param string $notrewardUrl
     * @return string
     */
    public function __invoke(\BkdRwd\Entity\Reward $reward, $rewardUrl, $notrewardUrl) {
        $hasUserRewardd = $this->hasUserRewardd($reward);

        $result = '<p>';

        switch ($hasUserRewardd) {
            case -1:
                $result .= $this->getView()->translate('Only registered users can Reward.');
                break;
            case 0:
                $result .= '<a href="' . $rewardUrl . '" id="test">' . $this->getView()->translate('Request Reward') . '</a>';
           // $result .= '<a href="#" id="test">' . $this->getView()->translate('Request Reward') . '</a>';
                break;
            case 1:
                $result .= $this->getView()->translate('Requested Reward!');
        }

        $result .= '</p>';

        $result .=
                '<p>' .
               // $this->getView()->translate('Rewarded') . ': ' . '<span>' . $reward->getLikesCount() . '</span>' . ' ' .
               // $this->getView()->translate('Pending Reward') . ': ' . '<span>' . $rewart->getDislikesCount() . '</span>' .
                '</p>';

        return $result;
    }

    /**
     * Checks if the current user has already rewardd for the entity
     * 
     * @param \BkdRwd\Entity\Reward $reward
     * @return integer 1 if rewardd, 0 if not, -1 if is not allowed to reward.
     */
    protected function hasUserRewardd($reward) {
        $dql = "SELECT count(v.id) FROM BkdRwd\Entity\Reward v LEFT JOIN v.usersRewarded u WHERE v.id = ?0 AND u.id =?1";
        $query = $this->entityManager->createQuery($dql);

        $rewardId = $reward->getId();

        $user = $this->getView()->identity();
        $hasUserRewardd = -1;

        if ($rewardId != null && $user != null) {
            $userId = $user->getId();
            $query->setParameter(0, $rewardId);
            $query->setParameter(1, $userId);
            $hasUserRewardd = $query->getSingleScalarResult();
        }

        return $hasUserRewardd;
    }

}