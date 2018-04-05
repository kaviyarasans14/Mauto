<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\UserBundle\Model;

use Mautic\CoreBundle\Model\FormModel;
use Mautic\EmailBundle\Helper\MailHelper;
use Mautic\UserBundle\Entity\User;
use Mautic\UserBundle\Event\StatusChangeEvent;
use Mautic\UserBundle\Event\UserEvent;
use Mautic\UserBundle\UserEvents;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

/**
 * Class UserModel.
 */
class UserModel extends FormModel
{
    /**
     * @var MailHelper
     */
    protected $mailHelper;

    public function __construct(MailHelper $mailHelper)
    {
        $this->mailHelper = $mailHelper;
    }

    /**
     * Define statuses that are supported.
     *
     * @var array
     */
    private $supportedOnlineStatuses = [
        'online',
        'idle',
        'away',
        'manualaway',
        'dnd',
        'offline',
    ];

    /**
     * {@inheritdoc}
     */
    public function getRepository()
    {
        return $this->em->getRepository(User::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissionBase()
    {
        return 'user:users';
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     */
    public function saveEntity($entity, $unlock = true)
    {
        if (!$entity instanceof User) {
            throw new MethodNotAllowedHttpException(['User'], 'Entity must be of class User()');
        }

        parent::saveEntity($entity, $unlock);
    }

    /**
     * Get a list of users for an autocomplete input.
     *
     * @param string $search
     * @param int    $limit
     * @param int    $start
     * @param array  $permissionLimiter
     *
     * @return array
     */
    public function getUserList($search = '', $limit = 10, $start = 0, $permissionLimiter = [])
    {
        $currentuser=$this->userHelper->getUser();
        $this->getRepository()->setCurrentUser($currentuser);

        return $this->getRepository()->getUserList($search, $limit, $start, $permissionLimiter);
    }

    /**
     * Checks for a new password and rehashes if necessary.
     *
     * @param User                     $entity
     * @param PasswordEncoderInterface $encoder
     * @param string                   $submittedPassword
     * @param bool|false               $validate
     *
     * @return string
     */
    public function checkNewPassword(User $entity, PasswordEncoderInterface $encoder, $submittedPassword, $validate = false)
    {
        if ($validate) {
            if (strlen($submittedPassword) < 6) {
                throw new \InvalidArgumentException($this->translator->trans('mautic.user.user.password.minlength', 'validators'));
            }
        }

        if (!empty($submittedPassword)) {
            //hash the clear password submitted via the form
            return $encoder->encodePassword($submittedPassword, $entity->getSalt());
        }

        return $entity->getPassword();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function createForm($entity, $formFactory, $action = null, $options = [])
    {
        if (!$entity instanceof User) {
            throw new MethodNotAllowedHttpException(['User'], 'Entity must be of class User()');
        }
        if (!empty($action)) {
            $options['action'] = $action;
        }

        return $formFactory->create('user', $entity, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity($id = null)
    {
        if ($id === null) {
            return new User();
        }

        $entity = parent::getEntity($id);

        if ($entity) {
            //add user's permissions
            $entity->setActivePermissions(
                $this->em->getRepository('MauticUserBundle:Permission')->getPermissionsByRole($entity->getRole())
            );
        }

        return $entity;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     */
    protected function dispatchEvent($action, &$entity, $isNew = false, Event $event = null)
    {
        if (!$entity instanceof User) {
            throw new MethodNotAllowedHttpException(['User'], 'Entity must be of class User()');
        }

        switch ($action) {
            case 'pre_save':
                $name = UserEvents::USER_PRE_SAVE;
                break;
            case 'post_save':
                $name = UserEvents::USER_POST_SAVE;
                break;
            case 'pre_delete':
                $name = UserEvents::USER_PRE_DELETE;
                break;
            case 'post_delete':
                $name = UserEvents::USER_POST_DELETE;
                break;
            default:
                return null;
        }

        if ($this->dispatcher->hasListeners($name)) {
            if (empty($event)) {
                $event = new UserEvent($entity, $isNew);
                $event->setEntityManager($this->em);
            }
            $this->dispatcher->dispatch($name, $event);

            return $event;
        }

        return null;
    }

    /**
     * Get list of entities for autopopulate fields.
     *
     * @param string $type
     * @param string $filter
     * @param int    $limit
     *
     * @return array
     */
    public function getLookupResults($type, $filter = '', $limit = 10)
    {
        $results = [];
        switch ($type) {
            case 'role':
                $results = $this->em->getRepository('MauticUserBundle:Role')->getRoleList($filter, $limit);
                break;
            case 'user':
                $currentuser=$this->userHelper->getUser();
                $this->em->getRepository('MauticUserBundle:User')->setCurrentUser($currentuser);
                $results = $this->em->getRepository('MauticUserBundle:User')->getUserList($filter, $limit, 0, []);
                break;
            case 'position':
                $results = $this->em->getRepository('MauticUserBundle:User')->getPositionList($filter, $limit);
                break;
        }

        return $results;
    }

    /**
     * Resets the user password and emails it.
     *
     * @param User                     $user
     * @param PasswordEncoderInterface $encoder
     * @param string                   $newPassword
     */
    public function resetPassword(User $user, PasswordEncoderInterface $encoder, $newPassword)
    {
        $encodedPassword = $this->checkNewPassword($user, $encoder, $newPassword);

        $user->setPassword($encodedPassword);
        $this->saveEntity($user);
    }

    /**
     * @param User $user
     *
     * @return string
     */
    protected function getResetToken(User $user)
    {
        /** @var \DateTime $lastLogin */
        $lastLogin = $user->getLastLogin();

        $dateTime = ($lastLogin instanceof \DateTime) ? $lastLogin->format('Y-m-d H:i:s') : null;

        return hash('sha256', $user->getUsername().$user->getEmail().$dateTime);
    }

    /**
     * @param User   $user
     * @param string $token
     *
     * @return bool
     */
    public function confirmResetToken(User $user, $token)
    {
        $resetToken = $this->getResetToken($user);

        return hash_equals($token, $resetToken);
    }

    /**
     * @param User $user
     */
    public function sendResetEmail(User $user)
    {
        $mailer = $this->mailHelper->getMailer();

        $resetToken = $this->getResetToken($user);
        $resetLink  = $this->router->generate('mautic_user_passwordresetconfirm', ['token' => $resetToken], true);
        $mailer->setFrom(['support@leadsengage.com' => 'LeadsEngage']);
        $mailer->setTo([$user->getEmail() => $user->getName()]);
        $mailer->setFrom(['support@leadsengage.com' => 'LeadsEngage']);
        $mailer->setSubject($this->translator->trans('mautic.user.user.passwordreset.subject'));
        /*$text = $this->translator->trans(
            'mautic.user.user.passwordreset.email.body',
            ['%name%' => $user->getFirstName(), '%resetlink%' => '<a href="'.$resetLink.'">'.$resetLink.'</a>']
        );
        $text = str_replace('\\n', "\n", $text);*/
        $name = $user->getFirstName();
        $text = "<!DOCTYPE html>
<html>
<meta name='viewport' content='width=device-width, initial-scale=1.0'>

	<head>
		<title></title>
		<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css'>
	</head>
	<body aria-disabled='false' style='min-height: 300px;margin:0px;'>
		<div style='background-color:#eff2f7'>
			<div style='padding-top: 55px;'>
				<div class='marle' style='margin: 0% 11.5%;background-color:#fff;padding: 50px 50px 50px 50px;border-bottom:5px solid #0071ff;'>

					<p style='text-align:center;'><img src='https://s3.amazonaws.com/leadsroll.com/home/leadsengage_logo-black.png' class='fr-fic fr-dii' height='40'></p>
					<br>
					<div style='text-align:center;width:100%;'>
						<div style='display:inline-block;width: 80%;'>

							<p style='text-align:left;font-size:14px;font-family: Montserrat,sans-serif;'>Hi $name,</p>

							<p style='text-align:left;font-size:14px;line-height: 30px;font-family: Montserrat,sans-serif;'>This email is in your inbox because you requested to reset your password. Here is the link to do just that</p><a href=\"$resetLink\" class='butle' style='text-align:center;text-decoration:none;font-family: Montserrat,sans-serif;transition: all .1s ease;color: #fff;font-weight: 400;font-size: 18px;margin-top: 10px;font-family: Montserrat,sans-serif;display: inline-block;letter-spacing: .6px;padding: 15px 30px;box-shadow: 0 1px 2px rgba(0,0,0,.36);white-space: nowrap;border-radius: 35px;background-color: #0071ff;border: #0071ff;'>Reset Your Password</a>
							<br>

							<p style='text-align:left;font-size:14px;line-height: 30px;font-family: Montserrat,sans-serif;'>If you did not request a password reset please reply to this email and let us know. We can investigate if the request was unauthorized.</p>
							<br>

							<p style='text-align:left;font-size:14px;font-family: Montserrat,sans-serif;'>Thanks,
								<br>LeadsEngage Support</p>
						</div>
					</div>
				</div>
				<br>
				<br>
				<br>
			</div>
		</div>
		
	</body>
</html>";
        //$html = nl2br($text);

        $mailer->setBody($text);
        //$mailer->setPlainText(strip_tags($text));

        $mailer->send();
    }

    /**
     * Set user preference.
     *
     * @param      $key
     * @param null $value
     * @param User $user
     */
    public function setPreference($key, $value = null, User $user = null)
    {
        if ($user == null) {
            $user = $this->userHelper->getUser();
        }

        $preferences       = $user->getPreferences();
        $preferences[$key] = $value;

        $user->setPreferences($preferences);

        $this->getRepository()->saveEntity($user);
    }

    /**
     * Get user preference.
     *
     * @param      $key
     * @param null $default
     * @param User $user
     */
    public function getPreference($key, $default = null, User $user = null)
    {
        if ($user == null) {
            $user = $this->userHelper->getUser();
        }
        $preferences = $user->getPreferences();

        return (isset($preferences[$key])) ? $preferences[$key] : $default;
    }

    /**
     * @param $status
     */
    public function setOnlineStatus($status)
    {
        $status = strtolower($status);

        if (in_array($status, $this->supportedOnlineStatuses)) {
            if ($this->userHelper->getUser()->getId()) {
                $this->userHelper->getUser()->setOnlineStatus($status);
                $this->getRepository()->saveEntity($this->userHelper->getUser());

                if ($this->dispatcher->hasListeners(UserEvents::STATUS_CHANGE)) {
                    $event = new StatusChangeEvent($this->userHelper->getUser());
                    $this->dispatcher->dispatch(UserEvents::STATUS_CHANGE, $event);
                }
            }
        }
    }

    /**
     * Return list of Users for formType Choice.
     *
     * @return array
     */
    public function getOwnerListChoices()
    {
        return $this->getRepository()->getOwnerListChoices($this);
    }

    public function getCurrentUserEntity()
    {
        return $this->userHelper->getUser();
    }
}
