<?php
/**
 * BkdUser - Coolbkd Zend Framework 2 User Module
 * 
 * @link https://github.com/coolbkd/BkdUser for the canonical source repository
 * @copyright Copyright (c) 2005-2013 LightSoft 2005 Ltd. Bulgaria
 * @license https://github.com/coolbkd/BkdUser/blob/master/LICENSE BSDLicense
 * @author Stoyan Cheresharov <stoyan@coolbkd.com>
 * @author Svetoslav Chonkov <svetoslav.chonkov@gmail.com>
 * @author Nikola Vasilev <niko7vasilev@gmail.com>
 * @author Stoyan Revov <st.revov@gmail.com>
 * @author Martin Briglia <martin@mgscreativa.com>
 */

namespace BkdUser\Service\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthenticationFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $serviceLocator->get('doctrine.authenticationservice.orm_default');
    }
}
