<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 07.01.16
 * Time: 13:06
 */

namespace CoreBundle\Handler;

use CoreBundle\Entity\UserSetting;
use CoreBundle\Exception\Handler\User\PasswordNotCorrectException;
use CoreBundle\Exception\Handler\User\TokenNotCorrectException;
use CoreBundle\Exception\Handler\User\UserBannedException;
use CoreBundle\Exception\Handler\User\UserNotFoundException;
use CoreBundle\Exception\Handler\User\UserSettingNotFoundException;
use CoreBundle\Exception\Processor\ProcessorException;
use CoreBundle\Model\Event\User\UserAuthEvent;
use CoreBundle\Model\Event\User\UserEvent;
use CoreBundle\Model\Event\User\UserEvents;
use CoreBundle\Model\Request\Call\ErrorAwareTrait;
use CoreBundle\Model\Request\RequestErrorInterface;
use CoreBundle\Model\Request\SecurityRequestInterface;
use CoreBundle\Model\Request\User\UserGetListRequest;
use CoreBundle\Model\Request\User\UserGetProfileRequest;
use CoreBundle\Model\Request\User\UserPatchLagRequest;
use CoreBundle\Model\Request\User\UserPatchSettingRequest;
use CoreBundle\Model\Request\User\UserPostAuthRequest;
use CoreBundle\Model\Request\User\UserPostRegisterRequest;
use CoreBundle\Model\Response\ResponseStatusCode;
use CoreBundle\Entity\User;
use CoreBundle\Processor\UserProcessorInterface;
use CoreBundle\Repository\UserRepository;
use CoreBundle\Repository\UserSettingRepository;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class UserHandler
 * @package CoreBundle\Handler
 */
class UserHandler implements UserProcessorInterface, EventSubscriberInterface
{
    use ContainerAwareTrait;
    use ErrorAwareTrait;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * UserHandler constructor.
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
        $this->repository = $this->manager->getRepository('CoreBundle:User');
    }

    /**
     * @return UserRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param UserPostRegisterRequest $registerRequest
     * @return User
     */
    public function processPostRegister(UserPostRegisterRequest $registerRequest) : User
    {
        try {
            if ($this->repository->findOneByEmail($registerRequest->getEmail())) {
                $this->getRequestError()->addError("email", 'This email was already registered');
            }
        } catch (UserNotFoundException $e) {
            // that's alright
        }

        try {
            if ($this->repository->findOneByLogin($registerRequest->getLogin())) {
                $this->getRequestError()->addError("login", 'This login was already registered');
            }
        } catch (UserNotFoundException $e) {
            // that's alright
        }

        if ($registerRequest->getPassword() !== $registerRequest->getPasswordRepeat()) {
            $this->getRequestError()->addError("password_repeat", 'The password repeat should be the same');
        }

        $this->container->get("core.service.error")->throwExceptionIfHasErrors($this->getRequestError(), ResponseStatusCode::FORBIDDEN);

        $user = new User();

        $user->setLogin($registerRequest->getLogin())
             ->setEmail($registerRequest->getEmail())
             ->setPassword($this->generatePasswordHash($registerRequest->getPassword()));

        $this->initUserSettings($user);

        $this->saveUser($user);

        $this->generateUserToken($user);

        return $user;
    }

    /**
     * @param UserPostAuthRequest $request
     * @return User
     */
    public function processPostAuth(UserPostAuthRequest $request) : User
    {
        if ($request->getToken()) {
            $user = $this->getSecureUser($request);

            $this->initUserSettings($user);
            $user->setGoodQuality(
                $user->getLag() < $this->container->getParameter("max_lag_for_record")
            )->addIp($request->getIp());

            $this->saveUser($user);

            return $user;
        }

        try {
            try {
                $user = $this->searchUserOnPlayzone($request);
            } catch (UserNotFoundException $e) {
                $this->container->get("event_dispatcher")->dispatch(
                    UserEvents::USER_AUTH,
                    $event = (new UserAuthEvent())
                                         ->setLogin($request->getLogin())
                                         ->setPassword($request->getPassword())
                );

                $user = $event->getUser();

                $this->saveUser($user);
            }
        } catch (UserNotFoundException $e) {
            $this->getRequestError()->addError("login", "The login is not found")
                 ->throwException(ResponseStatusCode::FORBIDDEN);
        } catch (PasswordNotCorrectException $e) {
            $this->getRequestError()->addError("password", "The password is not correct")
                 ->throwException(ResponseStatusCode::FORBIDDEN);
        }

        /** @var User $user */
        $this->generateUserToken($user);
        $this->initUserSettings($user);

        return $user;
    }

    /**
     * @param UserGetListRequest $listRequest
     * @return User[]
     */
    public function processGetList(UserGetListRequest $listRequest) : array 
    {
        $qb = $this->manager->createQueryBuilder();

        $users = $qb->select(array('u')) // string 'u' is converted to array internally
                    ->from('CoreBundle:User', 'u')
                    ->orderBy($listRequest->getOrderBy(), 'DESC')
                    ->setMaxResults($listRequest->getLimit())
                    ->andWhere('u.banned <> 1');

        if (!empty($listRequest->getFilter())) {
            $filter = json_decode($listRequest->getFilter(), true);
            foreach ($filter as $property => $value) {
                switch ($property) {
                    case 'games_count':
                        $users->andWhere('u.win + u.draw + u.lose > 0');
                        break;
                    case 'only_active':
                        $firstRatingDay = (new \DateTime())->setDate(2016, 8, 22);
                        $users->andWhere('u.lastPing > :minDateForActive')
                              ->andWhere('u.lastMove >= :firstRatingDay')
                              ->andWhere('u.rateGamesCount >= 5')
                              ->andWhere('u.engine <> 1')
                              ->setParameter('minDateForActive', new \DateTime('-30day'))
                              ->setParameter('firstRatingDay', $firstRatingDay);
                        break;
                    default:
                        $users->andWhere('u.'.$property.' = :'.$property)
                              ->setParameter($property, $value);
                }

            }
        }

        return $users->getQuery()->getResult();
    }

    /**
     * @param UserGetProfileRequest $request
     * @return User
     */
    public function processGetProfile(UserGetProfileRequest $request) : User
    {
        try {
            $user = $this->getRepository()->find($request->getId());
            $user->setPgnLink($this->getHttpPgnLink($user));
            return $user;
        } catch (UserNotFoundException $e) {
            $this->getRequestError()->addError("user_id", "User not found")
                                    ->throwException(ResponseStatusCode::NOT_FOUND);
        }
    }

    /**
     * @param UserPatchSettingRequest $settingRequest
     * @return UserSetting
     */
    public function processPatchSetting(UserPatchSettingRequest $settingRequest) : UserSetting
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($settingRequest, $this->getRequestError());
        
        try {
            $userSetting = $this->manager->getRepository('CoreBundle:UserSetting')
                ->find($settingRequest->getSettingId());
        } catch (UserSettingNotFoundException $e) {
            $this->getRequestError()
                 ->addError("setting_id", "Setting {$settingRequest->getSettingId()} is not found")
                 ->throwException(ResponseStatusCode::NOT_FOUND);
        }

        /** @var UserSetting $userSetting */
        $userSetting->setValue($settingRequest->getValue());
        $me->setSetting($userSetting);

        $this->saveUser($me);

        return $userSetting;
    }

    /**
     * @param UserPatchLagRequest $request
     * @return User
     */
    public function processPatchLag(UserPatchLagRequest $request) : User
    {
        $this->manager->flush(
            $me = $this->getSecureUser($request)
                       ->setLag($request->getLag())
                       ->setOnline(true)
                       ->setLastPing(new \DateTime())
        );

        return $me;
    }

    /**
     * @param User $user
     */
    private function saveUser(User $user)
    {
        $this->manager->persist($user);
        $this->manager->flush();
    }

    /**
     * @param string $login
     * @param string $token
     * @return User
     * @throws UserNotFoundException
     * @throws TokenNotCorrectException
     */
    public function getUserByLoginAndToken(string $login, string $token) : User
    {
        try {
            $user = $this->repository->findOneByLogin($login);
        } catch (UserNotFoundException $e) {
            $user = $this->repository->findOneByLogin(base64_decode($login));
        }

        $this->generateUserToken($user);

        if ($user->getToken() !== $token) {
            throw new TokenNotCorrectException;
        }

        return $user;
    }

    /**
     * @param string $password
     * @return string
     */
    public function generatePasswordHash($password) : string 
    {
        return md5($password);
    }

    /**
     * @param User $user
     */
    private function generateUserToken(User $user)
    {
        $user->setToken(md5($user->getLogin() . $user->getPassword()));
    }

    /**
     * @param UserPostAuthRequest $authRequest
     * @return User
     * @throws UserNotFoundException
     * @throws PasswordNotCorrectException
     */
    private function searchUserOnPlayzone(UserPostAuthRequest $authRequest) : User
    {
        try {
            $user = $this->repository->findOneByLogin($authRequest->getLogin());
        } catch (UserNotFoundException $e) {
            $user = $this->repository->findOneByEmail($authRequest->getLogin());
        }

        if ($user->getPassword() != $this->generatePasswordHash($authRequest->getPassword())) {
            throw new PasswordNotCorrectException;
        }

        return $user;
    }

    /**
     * @param User $user
     * @return void
     */
    private function initUserSettings(User $user)
    {
        $allSettings = $this->manager->getRepository("CoreBundle:UserSetting")
                            ->findBy([],['sort' => 'ASC']);

        foreach ($allSettings as $setting) {
            try {
                $user->getSetting($setting->getName())->setId($setting->getId());
            } catch (UserSettingNotFoundException $e) {
                $user->setSetting($setting);
            }
        }

        $settings = (array)$user->getSettings();
        uasort(
            $settings,
            function(UserSetting $a, UserSetting $b)
            {
                return $a->getSort() <=> $b->getSort();
            }
        );
        $this->container->get('logger')->info(
            'Settings: ' . json_encode(array_keys($settings))
        );
        $user->setSettings($settings);
    }

    /**
     * @param User $user
     * @return string
     */
    public function getHttpPgnLink(User $user) : string
    {
        $fs = new Filesystem();

        $pgnFileName = $this->getPgnFilePath($user);

        return $fs->exists($pgnFileName) ? "/pgn/" . basename($pgnFileName) : "";
    }

    /**
     * @param User $user
     * @return string
     */
    public function getPgnFilePath(User $user) : string
    {
        return $this->container->get("core.service.chess")->getPgnDir() . 
                    DIRECTORY_SEPARATOR . $user . ".pgn";
    }

    /**
     * @param SecurityRequestInterface $request
     * @return User
     */
    public function getSecureUser(SecurityRequestInterface $request)
    {
        return $this->container->get("core.service.security")->getUserIfCredentialsIsOk($request,
            $this->getRequestError());
    }

    /**
     * @return void
     */
    public function markUsersOfflineWhoJustGone()
    {
        /** @var User[] $users */
        $users = $this->repository->createQueryBuilder("u")
                      ->where("u.online = 1")
                      ->andWhere("u.lastPing < :agoPeriod")
                      ->setParameter("agoPeriod", new \DateTime("-1minute"))
                      ->getQuery()
                      ->getResult();
        
        foreach ($users as $user) {
            $this->manager->persist($user->setOnline(false));
        }
        
        $this->manager->flush();
    }

    /**
     * @return void
     */
    public function markAllUsersOffline()
    {
        /** @var User[] $users */
        $users = $this->repository->createQueryBuilder("u")
                      ->where("u.online = 1")
                      ->getQuery()
                      ->getResult();

        foreach ($users as $user) {
            $this->manager->persist($user->setOnline(false));
        }

        $this->manager->flush();
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            UserEvents::USER_IN => [
                ['onUserIn', 20]
            ],
            UserEvents::USER_OUT => [
                ['onUserOut', 20]
            ]
        ];
    }

    /**
     * @param UserEvent $event
     */
    public function onUserIn(UserEvent $event)
    {
        $this->saveUser(
            $event->getUser()->setOnline(true)
        );
    }

    /**
     * @param UserEvent $event
     */
    public function onUserOut(UserEvent $event)
    {
        $this->saveUser(
            $event->getUser()->setOnline(false)
        );
    }
}