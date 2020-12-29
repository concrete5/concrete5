<?php

namespace Concrete\Core\User;

use Concrete\Core\Antispam\Service;
use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\Category\UserCategory;
use Concrete\Core\Attribute\Key\UserKey;
use Concrete\Core\Attribute\ObjectInterface as AttributeObjectInterface;
use Concrete\Core\Attribute\ObjectTrait;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Encryption\PasswordHasher;
use Concrete\Core\Entity\Attribute\Value\UserValue;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\File\DownloadStatistics;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Entity\User\User as UserEntity;
use Concrete\Core\Entity\User\UserSignup;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Export\Item\User as UserExporter;
use Concrete\Core\File\Image\BitmapFormat;
use Concrete\Core\File\Set\Set;
use Concrete\Core\File\StorageLocation\StorageLocationFactory;
use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Mail\Importer\MailImporter;
use Concrete\Core\Permission\ObjectInterface as PermissionObjectInterface;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\User\Avatar\AvatarInterface;
use Concrete\Core\User\Avatar\AvatarServiceInterface;
use Concrete\Core\User\Event\DeleteUser as DeleteUserEvent;
use Concrete\Core\User\Event\UserGroup as UserGroupEvent;
use Concrete\Core\User\Event\UserInfo as UserInfoEvent;
use Concrete\Core\User\Event\UserInfoWithAttributes as UserInfoWithAttributesEvent;
use Concrete\Core\User\Event\UserInfoWithPassword as UserInfoWithPasswordEvent;
use Concrete\Core\User\PrivateMessage\Limit;
use Concrete\Core\User\PrivateMessage\Mailbox as UserPrivateMessageMailbox;
use Concrete\Core\User\PrivateMessage\PrivateMessage;
use Concrete\Core\User\User as ConcreteUser;
use Concrete\Core\Utility\IPAddress;
use Concrete\Core\Utility\Service\Identifier;
use Concrete\Core\Workflow\Request\ActivateUserRequest as ActivateUserWorkflowRequest;
use Concrete\Core\Workflow\Request\DeleteUserRequest as DeleteUserWorkflowRequest;
use Core;
use Doctrine\ORM\EntityManagerInterface;
use Group;
use Imagine\Image\ImageInterface;
use League\Flysystem\AdapterInterface;
use League\URL\URLInterface;
use stdClass;
use Symfony\Component\EventDispatcher\EventDispatcher;

class UserInfo extends ConcreteObject implements AttributeObjectInterface, PermissionObjectInterface, ExportableInterface
{
    use ObjectTrait;

    /**
     * @var AvatarServiceInterface
     */
    protected $avatarService;

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var UserCategory
     */
    protected $attributeCategory;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var UserEntity
     */
    protected $entity;

    /**
     * @var EventDispatcher|null
     */
    protected $director = null;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserCategory $attributeCategory
     * @param Application $application
     * @param AvatarServiceInterface $avatarService
     */
    public function __construct(EntityManagerInterface $entityManager, UserCategory $attributeCategory, Application $application, AvatarServiceInterface $avatarService)
    {
        $this->avatarService = $avatarService;
        $this->application = $application;
        $this->entityManager = $entityManager;
        $this->attributeCategory = $attributeCategory;
        $this->connection = $entityManager->getConnection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'UserInfo: ' . $this->getUserID();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionObjectIdentifier()
     */
    public function getPermissionObjectIdentifier()
    {
        return $this->entity->getUserID();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionResponseClassName()
     */
    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\UserInfoResponse';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionAssignmentClassName()
     */
    public function getPermissionAssignmentClassName()
    {
        return '\\Concrete\\Core\\Permission\\Assignment\\UserAssignment';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionObjectKeyCategoryHandle()
     */
    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'user';
    }

    /**
     * @param UserEntity $entity
     */
    public function setEntityObject(UserEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return UserEntity
     */
    public function getEntityObject()
    {
        return $this->entity;
    }

    public function getExporter()
    {
        return new UserExporter();
    }

    /**
     * @return Group[]
     */
    public function getUserBadges()
    {
        $groups = [];
        $r = $this->connection->executeQuery('select g.gID from ' . $this->connection->getDatabasePlatform()->quoteSingleIdentifier('Groups') . ' g inner join UserGroups ug on g.gID = ug.gID where g.gIsBadge = 1 and ug.uID = ? order by ugEntered desc', [$this->getUserID()]);
        while ($row = $r->fetch()) {
            $groups[] = Group::getByID($row['gID']);
        }
        $r->closeCursor();

        return $groups;
    }

    /**
     * @param User $requester
     *
     * @return bool
     */
    public function triggerDelete($requester)
    {
        $v = [$this->getUserID()];
        $pkr = new DeleteUserWorkflowRequest();
        $pkr->setRequestedUserID($this->getUserID());
        $pkr->setRequesterUserID($requester->getUserID());
        $pkr->trigger();

        return $this->connection->fetchColumn('select uID from Users where uID = ? limit 1', $v) ? false : true;
    }

    /**
     * Deletes a user.
     *
     * @return bool
     */
    public function delete()
    {
        // we will NOT let you delete the admin user
        if ($this->getUserID() == USER_SUPER_ID) {
            return false;
        }

        // Dispatch an on_user_delete event, that every subscriber can cancel.
        $ue = new DeleteUserEvent($this);
        $ue = $this->getDirector()->dispatch('on_user_delete', $ue);
        if (!$ue->proceed()) {
            return false;
        }

        // Dispatch an on_user_deleted event: subscribers can't cancel this event.
        // This event could be at the end of this method, but let's keep it here so that subscribers
        // can get all the details of the user being deleted.
        $this->getDirector()->dispatch('on_user_deleted', new UserInfoEvent($this));

        $attributes = $this->attributeCategory->getAttributeValues($this->getEntityObject());
        foreach ($attributes as $attribute) {
            $this->attributeCategory->deleteValue($attribute);
        }

        $this->connection->executeQuery('DELETE FROM OauthUserMap WHERE user_id = ?', [(int)$this->getUserID()]);
        $this->connection->executeQuery('DELETE FROM Logs WHERE uID = ?', [(int)$this->getUserID()]);
        $this->connection->executeQuery('DELETE FROM UserSearchIndexAttributes WHERE uID = ?', [(int)$this->getUserID()]);
        $this->connection->executeQuery('DELETE FROM UserGroups WHERE uID = ?', [(int)$this->getUserID()]);
        $this->connection->executeQuery('DELETE FROM UserValidationHashes WHERE uID = ?', [(int)$this->getUserID()]);
        $this->connection->executeQuery('DELETE FROM Piles WHERE uID = ?', [(int)$this->getUserID()]);
        $this->connection->executeQuery('DELETE FROM ConfigStore WHERE uID = ?', [(int)$this->getUserID()]);
        $this->connection->executeQuery('DELETE FROM ConversationSubscriptions WHERE uID = ?', [(int)$this->getUserID()]);
        $this->connection->executeQuery('DELETE FROM PermissionAccessEntityUsers WHERE uID = ?', [(int)$this->getUserID()]);
        $this->connection->executeQuery('DELETE FROM authTypeConcreteCookieMap WHERE uID = ?', [(int)$this->getUserID()]);

        // Conversation messages and ratings should be detached from the user
        $this->connection->executeQuery('UPDATE ConversationMessages SET uID = 0, cnvMessageAuthorName = NULL, cnvMessageAuthorEmail = NULL, cnvMessageAuthorWebsite = NULL, cnvMessageSubmitIP = NULL, cnvMessageSubmitUserAgent = NULL WHERE uID = ?', [(int)$this->getUserID()]);
        $this->connection->executeQuery('UPDATE ConversationMessageRatings SET cnvMessageRatingIP = NULL, uID = 0 WHERE uID = ?', [(int)$this->getUserID()]);

        // Public file sets should be detached from the user
        $this->connection->executeQuery('UPDATE FileSets SET uID = 0 WHERE uID = ? AND fsType = ?', [
            (int)$this->getUserID(),
            Set::TYPE_PUBLIC,
        ]);

        // Delete private file sets from this user
        foreach (Set::getOwnedSets($this) as $set) {
            $set->delete();
        }

        $this->connection->executeQuery('UPDATE Blocks set uID = ? WHERE uID = ?', [(int)USER_SUPER_ID, (int)$this->getUserID()]);
        $this->connection->executeQuery('UPDATE Pages set uID = ? WHERE uID = ?', [(int)USER_SUPER_ID, (int)$this->getUserID()]);
        $this->entityManager->createQueryBuilder()
            ->update(DownloadStatistics::class, 'ds')
            ->set('ds.downloaderID', ':null')
            ->where($this->entityManager->getExpressionBuilder()->eq('ds.downloaderID', ':uID'))
            ->getQuery()
            ->execute(['null' => null, 'uID' => $this->getUserID()]);

        // We need to clear out the doctrine proxies for userSignups or we will get a Doctrine Error
        /** @var UserSignup[] $userSignups */
        $userSignups = $this->entityManager->getRepository(UserSignup::class)->findBy(['createdBy' => (int)$this->getUserID()]);
        $superAdminEntity = $this->entityManager->getRepository(UserEntity::class)->find((int)USER_SUPER_ID);

        foreach ($userSignups as $userSignup) {
            // If there is no SuperAdmin Just remove the relatedUserSignups
            if (is_object($superAdminEntity)) {
                $userSignup->setCreatedBy($superAdminEntity);
                $this->entityManager->persist($userSignup);
            } else {
                $this->entityManager->remove($userSignup);
            }
        }

        $expressEntities = $this->entityManager->getRepository(Entry::class)->findBy(['author' => (int)$this->getUserID()]);
        /** @var Entry $expressEntity */
        foreach ($expressEntities as $expressEntity) {
            // If there is no SuperAdmin Just remove the Express Entry
            if (is_object($superAdminEntity)) {
                $expressEntity->setAuthor($superAdminEntity);
                $this->entityManager->persist($expressEntity);
            } else {
                $this->entityManager->remove($expressEntity);
            }
        }

        $this->entityManager->remove($this->entity);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param PrivateMessage $msg
     *
     * @return bool
     */
    public function canReadPrivateMessage($msg)
    {
        return $msg->getMessageUserID() == $this->getUserID();
    }

    /**
     * @param ImageInterface $image
     */
    public function updateUserAvatar(ImageInterface $image)
    {
        $fsl = $this->application->make(StorageLocationFactory::class)->fetchDefault()->getFileSystemObject();
        $bitmapFormat = $this->application->make(BitmapFormat::class);
        $config = $this->application->make('config');
        $image = $image->get(BitmapFormat::FORMAT_JPEG, $bitmapFormat->getFormatImagineSaveOptions(BitmapFormat::FORMAT_JPEG));
        $file = REL_DIR_FILES_AVATARS . '/' . $this->getUserID() . '.jpg';
        if ($fsl->has($file)) {
            $fsl->delete($file);
        }

        $fsl->write(
            $file,
            $image,
            [
                'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
                'mimetype' => 'image/jpeg',
            ]
        );

        $this->connection->executeQuery('update Users set uHasAvatar = 1 where uID = ? limit 1', [$this->getUserID()]);

        // run any internal event we have for user update
        $ui = $this->application->make(UserInfoRepository::class)->getByID($this->getUserID());
        $ue = new UserInfoEvent($ui);
        $this->getDirector()->dispatch('on_user_update', $ue);
    }

    /**
     * Marks the current user as having had a password reset from the system.
     */
    public function markAsPasswordReset()
    {
        $this->connection->executeQuery('UPDATE Users SET uIsPasswordReset = 1 WHERE uID = ? limit 1', [$this->getUserID()]);
    }

    /**
     * Sent a private message.
     *
     * @param UserInfo $recipient
     * @param string $subject
     * @param string $text
     * @param PrivateMessage $inReplyTo
     * @param File[] $attachments
     *
     * @return ErrorList|false|null Returns:
     * - an error if the send limit has been reached
     * - false if the message is detected as spam
     * - null if no errors occurred
     */
    public function sendPrivateMessage($recipient, $subject, $text, $inReplyTo = null, $attachments = [])
    {
        if (Limit::isOverLimit($this->getUserID())) {
            return Limit::getErrorObject();
        }
        $antispam = $this->application->make('helper/validation/antispam');
        /* @var Service $antispam */
        $messageText = t('Subject: %s', $subject);
        $messageText .= "\n";
        $messageText .= t('Message: %s', $text);

        $additionalArgs = ['user' => $this];
        if (!$antispam->check($messageText, 'private_message', $additionalArgs)) {
            return false;
        }

        if (!$subject) {
            $subject = t('(No Subject)');
        }
        $dt = $this->application->make('date');
        $msgDateCreated = $dt->getOverridableNow();
        $v = [$this->getUserID(), $msgDateCreated, $subject, $text, $recipient->getUserID()];
        $this->connection->executeQuery('insert into UserPrivateMessages (uAuthorID, msgDateCreated, msgSubject, msgBody, uToID) values (?, ?, ?, ?, ?)', $v);

        $msgID = $this->connection->lastInsertId();

        if ($msgID) {
            // we add the private message to the sent box of the sender, and the inbox of the recipient
            $this->connection->executeQuery(
                'insert into UserPrivateMessagesTo (msgID, uID, uAuthorID, msgMailboxID, msgIsNew, msgIsUnread) values (?, ?, ?, ?, ?, ?)',
                [$msgID, $this->getUserID(), $this->getUserID(), UserPrivateMessageMailbox::MBTYPE_SENT, 0, 1]
            );
            $this->connection->executeQuery(
                'insert into UserPrivateMessagesTo (msgID, uID, uAuthorID, msgMailboxID, msgIsNew, msgIsUnread) values (?, ?, ?, ?, ?, ?)',
                [$msgID, $recipient->getUserID(), $this->getUserID(), UserPrivateMessageMailbox::MBTYPE_INBOX, 1, 1]
            );
            // add file attachments
            foreach ($attachments as $attachment) {
                $this->connection->executeQuery('insert into UserPrivateMessagesAttachments (msgID, fID) values (?, ?)', [$msgID, $attachment->getFileID()]);
            }
        }

        // If the message is in reply to another message, we make a note of that here
        if (is_object($inReplyTo)) {
            $this->connection->executeQuery(
                'update UserPrivateMessagesTo set msgIsReplied = 1 where uID = ? and msgID = ?',
                [$this->getUserID(), $inReplyTo->getMessageID()]
            );
        }

        // send the email notification
        if ($recipient->getAttribute('profile_private_messages_notification_enabled')) {
            $site = $this->application->make('site')->getSite();
            $siteConfig = $site->getConfigRepository();
            $mh = $this->application->make('mail');
            $mh->addParameter('siteName', tc('SiteName', $site->getSiteName()));
            $mh->addParameter('msgSubject', $subject);
            $mh->addParameter('msgBody', $text);
            $mh->addParameter('msgAuthor', $this->getUserName());
            $mh->addParameter('msgDateCreated', $msgDateCreated);
            $urlManager = $this->application->make('url/manager');
            $mh->addParameter('profilePreferencesURL', $urlManager->resolve(['/account/edit_profile']));
            $mh->addParameter('myPrivateMessagesURL', $urlManager->resolve(['/account/messages']));
            if ($siteConfig->get('user.profiles_enabled')) {
                $mh->addParameter('profileURL', $this->getUserPublicProfileUrl());
                if ($this->getAttribute('profile_private_messages_enabled')) {
                    $mh->addParameter('replyToMessageURL', $urlManager->resolve(['/account/messages', 'reply', 'inbox', $msgID]));
                }
            }
            $mh->to($recipient->getUserEmail());

            $mi = MailImporter::getByHandle('private_message');
            if ($mi && $mi->isMailImporterEnabled()) {
                $mh->load('private_message_response_enabled');
                // we store information ABOUT the message here. The mail handler has to know how to handle this.
                $data = new stdClass();
                $data->msgID = $msgID;
                $data->toUID = $recipient->getUserID();
                $data->fromUID = $this->getUserID();
                $mh->enableMailResponseProcessing($mi, $data);
            } else {
                $mh->load('private_message');
            }
            $mh->sendMail();
        }

        $msg = PrivateMessage::getByID($msgID);
        $type = $this->application->make('manager/notification/types')->driver('new_private_message');
        $notifier = $type->getNotifier();
        $subscription = $type->getSubscription($msg);
        $notified = $notifier->getUsersToNotify($subscription, $msg);
        $notification = $type->createNotification($msg);
        $notifier->notify($notified, $notification);
    }

    /**
     * Gets the User object of the current UserInfo object ($this).
     *
     * @return \Concrete\Core\User\User
     */
    public function getUserObject()
    {
        // returns a full user object - groups and everything - for this userinfo object
        $nu = User::getByUserID($this->getUserID());

        return $nu;
    }

    /**
     * @param array $data
     *
     * @return bool|null returns false if the record has not been saved, null if the password confirmation failed, true otherwise
     */
    public function update($data)
    {
        $uID = (int)$this->getUserID();
        if ($uID === 0) {
            $result = false;
        } else {
            $result = true;
            $emailChanged = false;
            $passwordChangedOn = null;
            $fields = [];
            $values = [];
            $nullFields = [];
            if (isset($data['uName'])) {
                $fields[] = 'uName = ?';
                $values[] = $data['uName'];
            }
            if (isset($data['uEmail']) && $data['uEmail'] !== $this->getUserEmail()) {
                $emailChanged = true;
                $fields[] = 'uEmail = ?';
                $values[] = $data['uEmail'];
            }
            if (isset($data['uHasAvatar'])) {
                $fields[] = 'uHasAvatar = ?';
                $values[] = $data['uHasAvatar'] ? 1 : 0;
            }
            if (isset($data['uTimezone'])) {
                $fields[] = 'uTimezone = ?';
                $values[] = $data['uTimezone'];
            }
            if (isset($data['uDefaultLanguage'])) {
                $fields[] = 'uDefaultLanguage = ?';
                $values[] = $data['uDefaultLanguage'];
            }
            if (isset($data['uHomeFileManagerFolderID'])) {
                if ($data['uHomeFileManagerFolderID'] == '') {
                    $nullFields[] = 'uHomeFileManagerFolderID';
                } else {
                    $fields[] = 'uHomeFileManagerFolderID = ?';
                    $values[] = $data['uHomeFileManagerFolderID'];
                }
            }
            if (isset($data['uPassword']) && (string)$data['uPassword'] !== '') {
                if (isset($data['uPasswordConfirm']) && $data['uPassword'] === $data['uPasswordConfirm']) {
                    $passwordChangedOn = $this->application->make('date')->getOverridableNow();
                    $fields[] = 'uPassword = ?';
                    $values[] = $this->application->make(PasswordHasher::class)->hashPassword($data['uPassword']);
                    $fields[] = 'uLastPasswordChange = ?';
                    $values[] = $passwordChangedOn;
                    if (isset($data['uIsPasswordReset'])) {
                        $fields[] = 'uIsPasswordReset = ?';
                        $values[] = $data['uIsPasswordReset'] ? 1 : 0;
                    }
                } else {
                    $result = null;
                }
            }
            if ($result === true && !empty($fields)) {
                $this->connection->executeQuery(
                    'update Users set  ' . implode(', ', $fields) . 'where uID = ? limit 1',
                    array_merge($values, [$uID])
                );
                if (!empty($nullFields)) {
                    $nullFieldsStr = '';
                    foreach ($nullFields as $nullField) {
                        $nullFieldsStr .= (strlen($nullFieldsStr) > 0 ? ", " : "") . $nullField . " = NULL";
                    }
                    $nullQuery = sprintf('update Users set %s where uID = ? limit 1', $nullFieldsStr);
                    $this->connection->executeQuery($nullQuery, [$uID]);
                }
                if ($emailChanged) {
                    $this->connection->executeQuery('DELETE FROM UserValidationHashes WHERE uID = ?', [$uID]);
                    $h = $this->application->make('helper/validation/identifier');
                    $h->deleteKey('UserValidationHashes', 'uID', $uID);
                }
                // now we check to see if the user is updated his or her own logged in record
                $session = $this->application->make('session');
                if ($session->has('uID') && $uID === (int)$session->get('uID')) {
                    if (isset($data['uName'])) {
                        $session->set('uName', $data['uName']);
                    }
                    if (isset($data['uTimezone'])) {
                        $session->set('uTimezone', $data['uTimezone']);
                    }
                    if (isset($data['uDefaultLanguage'])) {
                        $session->set('uDefaultLanguage', $data['uDefaultLanguage']);
                    }
                    if (isset($data['uHomeFileManagerFolderID'])) {
                        $session->set('uHomeFileManagerFolderID', $data['uHomeFileManagerFolderID']);
                    }
                    if ($passwordChangedOn !== null) {
                        $session->set('uLastPasswordChange', $passwordChangedOn);
                    }
                }
                // run any internal event we have for user update
                $ue = new UserInfoEvent($this);
                $this->getDirector()->dispatch('on_user_update', $ue);
                if ($passwordChangedOn !== null) {
                    $ue = new UserInfoWithPasswordEvent($this);
                    $ue->setUserPassword($data['uPassword']);
                    $this->getDirector()->dispatch('on_user_change_password', $ue);
                }
            }
        }

        return $result;
    }

    /**
     * @param int[] $groupArray
     */
    public function updateGroups($groupArray)
    {
        $existingGIDArray = [];
        $r = $this->connection->executeQuery('select gID from UserGroups where uID = ?', [$this->getUserID()]);
        while ($row = $r->fetch()) {
            $existingGIDArray[] = $row['gID'];
        }
        $r->closeCursor();

        if (is_array($groupArray)) {
            foreach ($groupArray as $gID) {
                $key = array_search($gID, $existingGIDArray);
                if ($key !== false) {
                    // we remove this item from the existing GID array
                    unset($existingGIDArray[$key]);
                } else {
                    // this item is new, so we add it.
                    $_ux = $this->getUserObject();
                    $g = Group::getByID($gID);
                    $_ux->enterGroup($g);
                }
            }
        }

        // now we go through the existing GID Array, and remove everything, since whatever is left is not wanted.
        if (!empty($existingGIDArray)) {
            // Fire on_user_exit_group event for each group exited
            $groupObjects = [];
            foreach ($existingGIDArray as $gID) {
                $group = Group::getByID($gID);
                if ($group) {
                    $groupObjects[$gID] = $group;
                }
            }
            if (!empty($groupObjects)) {
                $inStr = implode(',', array_keys($groupObjects));
                $this->connection->executeQuery(
                    "delete from UserGroups where uID = ? and gID in ($inStr)",
                    [$this->getUserID()]
                );
                $userObject = $this->getUserObject();
                $app = Facade::getFacadeApplication();
                $logger = $this->application->make(LoggerFactory::class)->createLogger(Channels::CHANNEL_USERS);
                foreach ($groupObjects as $group) {
                    $ue = new UserGroupEvent($userObject);
                    $ue->setGroupObject($group);
                    $this->getDirector()->dispatch('on_user_exit_group', $ue);
                }
            }
        }
    }

    /**
     * @return string
     */
    public function setupValidation()
    {
        $result = $this->connection->fetchColumn('select uHash from UserValidationHashes where uID = ? order by uDateGenerated desc', [$this->getUserID()]);
        if (!$result) {
            $h = $this->application->make('helper/validation/identifier');
            $result = $h->generate('UserValidationHashes', 'uHash');
            $this->connection->executeQuery(
                'insert into UserValidationHashes (uID, uHash, uDateGenerated) values (?, ?, ?)',
                [$this->getUserID(), $result, time()]
            );
        }

        return $result;
    }

    /**
     * @return true
     */
    public function markValidated()
    {
        $v = [$this->getUserID()];
        $this->connection->executeQuery('update Users set uIsValidated = 1, uIsFullRecord = 1 where uID = ? limit 1', $v);
        $this->connection->executeQuery('update UserValidationHashes set uDateRedeemed = ' . time() . ' where uID = ?', $v);

        $this->uIsValidated = 1;
        $ue = new UserInfoEvent($this);
        $this->getDirector()->dispatch('on_user_validate', $ue);

        return true;
    }

    /**
     * @param string $newPassword
     *
     * @return bool
     */
    public function changePassword($newPassword)
    {
        return $this->update([
            'uPassword' => $newPassword,
            'uPasswordConfirm' => $newPassword,
            'uIsPasswordReset' => false,
        ]);
    }

    /**
     * @param string|null $action
     * @param int|null $requesterUID Use null for the current user
     *
     * @return bool
     */
    public function triggerActivate($action = null, $requesterUID = null)
    {
        if ($requesterUID === null) {
            $u = $this->application->make(User::class);
            $requesterUID = $u->getUserID();
        }

        $pkr = new ActivateUserWorkflowRequest();
        // default activate action of workflow is set after workflow request is created
        if ($action !== null) {
            $pkr->setRequestAction($action);
        }
        $pkr->setRequestedUserID($this->getUserID());
        $pkr->setRequesterUserID($requesterUID);
        $pkr->trigger();

        // Figure out whether the user was marked active during the workflow.
        // Usually happens if no workflows are attached (empty workflow).
        if ($this->connection->fetchColumn('SELECT uIsActive FROM Users WHERE uID = ? limit 1', [$this->getUserID()])) {
            $isActive = true;
        } else {
            $isActive = false;
        }
        $this->entity->setUserIsActive($isActive);

        return $isActive;
    }

    public function activate()
    {
        $this->connection->executeQuery(
            'update Users set uIsActive = 1 where uID = ? limit 1',
            [$this->getUserID()]
        );
        $ue = new UserInfoEvent($this);
        $this->getDirector()->dispatch('on_user_activate', $ue);
    }

    /**
     * @param int|null $requesterUID Use null for the current user
     *
     * @return bool
     */
    public function triggerDeactivate($requesterUID = null)
    {
        if ($requesterUID === null) {
            $u = $this->application->make(User::class);
            $requesterUID = $u->getUserID();
        }

        $pkr = new ActivateUserWorkflowRequest();
        $pkr->setRequestAction('deactivate');
        $pkr->setRequestedUserID($this->getUserID());
        $pkr->setRequesterUserID($requesterUID);
        $pkr->trigger();

        if ($this->connection->fetchColumn('select uIsActive from Users where uID = ? limit 1', [$this->getUserID()])) {
            $isActive = true;
        } else {
            $isActive = false;
        }
        $this->entity->setUserIsActive($isActive);

        return $isActive === false;
    }

    public function deactivate()
    {
        $this->connection->executeQuery(
            'update Users set uIsActive = 0 where uID = ? limit 1',
            [$this->getUserID()]
        );
        $ue = new UserInfoEvent($this);
        $this->getDirector()->dispatch('on_user_deactivate', $ue);
    }

    /**
     * @param int $length
     *
     * @return string|null
     */
    public function resetUserPassword($length = 256)
    {
        // resets user's password, and returns the value of the reset password
        if ($this->getUserID() > 0) {
            $id = $this->application->make(Identifier::class);
            $newPassword = $id->getString($length);
            $this->changePassword($newPassword);

            $ue = new UserInfoEvent($this);
            $this->getDirector()->dispatch('on_user_reset_password', $ue);

            return $newPassword;
        }
    }

    /**
     * @return AvatarInterface
     */
    public function getUserAvatar()
    {
        return $this->avatarService->getAvatar($this);
    }

    /**
     * @return URLInterface|null
     */
    public function getUserPublicProfileUrl()
    {
        $site = $this->application->make('site')->getSite();
        $config = $site->getConfigRepository();

        if (!$config->get('user.profiles_enabled')) {
            return;
        }
        $url = $this->application->make('url/manager');

        return $url->resolve([
            '/members/profile',
            'view',
            $this->getUserID(),
        ]);
    }

    /**
     * @return bool
     */
    public function hasAvatar()
    {
        return $this->avatarService->userHasAvatar($this);
    }

    /**
     * @see \Concrete\Core\Entity\User\User::getUserLastLogin()
     */
    public function getLastLogin()
    {
        return $this->entity->getUserLastLogin();
    }

    /**
     * @return string|null
     */
    public function getLastIPAddress()
    {
        $ip = new IPAddress($this->entity->getUserLastIP(), true);

        return $ip->getIp($ip::FORMAT_IP_STRING);
    }

    /**
     * @see \Concrete\Core\Entity\User\User::getUserPreviousLogin()
     */
    public function getPreviousLogin()
    {
        return $this->entity->getUserPreviousLogin();
    }

    /**
     * @see \Concrete\Core\Entity\User\User::isUserActive()
     */
    public function isActive()
    {
        return $this->entity->isUserActive();
    }

    /**
     * @see \Concrete\Core\Entity\User\User::isUserValidated()
     */
    public function isValidated()
    {
        return $this->entity->isUserValidated();
    }

    /**
     * @see \Concrete\Core\Entity\User\User::isUserFullRecord()
     */
    public function isFullRecord()
    {
        return $this->entity->isUserFullRecord();
    }

    /**
     * @see \Concrete\Core\Entity\User\User::getUserTotalLogins()
     */
    public function getNumLogins()
    {
        return $this->entity->getUserTotalLogins();
    }

    /**
     * @see \Concrete\Core\Entity\User\User::getUserID()
     */
    public function getUserID()
    {
        return $this->entity->getUserID();
    }

    /**
     * @see \Concrete\Core\Entity\User\User::getUserName()
     */
    public function getUserName()
    {
        return $this->entity->getUserName();
    }

    /**
     * @see \Concrete\Core\Entity\User\User::getUserName()
     */
    public function getUserDisplayName()
    {
        return $this->entity->getUserName();
    }

    /**
     * @see \Concrete\Core\Entity\User\User::getUserPassword()
     */
    public function getUserPassword()
    {
        return $this->entity->getUserPassword();
    }

    /**
     * @see \Concrete\Core\Entity\User\User::getUserEmail()
     */
    public function getUserEmail()
    {
        return $this->entity->getUserEmail();
    }

    /**
     * @see \Concrete\Core\Entity\User\User::getHomeFileManagerFolderID()
     */
    public function getUserHomeFolderId()
    {
        return $this->entity->getHomeFileManagerFolderID();
    }

    /**
     * @see \Concrete\Core\Entity\User\User::getUserTimezone()
     */
    public function getUserTimezone()
    {
        return $this->entity->getUserTimezone();
    }

    /**
     * @see \Concrete\Core\Entity\User\User::getUserDefaultLanguage()
     */
    public function getUserDefaultLanguage()
    {
        return $this->entity->getUserDefaultLanguage();
    }

    /**
     * @see \Concrete\Core\Entity\User\User::getUserDateAdded()
     */
    public function getUserDateAdded()
    {
        return $this->entity->getUserDateAdded();
    }

    /**
     * @see \Concrete\Core\Entity\User\User::getUserLastOnline()
     */
    public function getLastOnline()
    {
        return $this->entity->getUserLastOnline();
    }

    /**
     * @param UserAttributeKey[] $attributes
     */
    public function saveUserAttributesForm($attributes)
    {
        foreach ($attributes as $uak) {
            $controller = $uak->getController();
            $value = $controller->createAttributeValueFromRequest();
            $this->setAttribute($uak, $value);
        }

        $ue = new UserInfoWithAttributesEvent($this);
        $ue->setAttributes($attributes);
        $this->getDirector()->dispatch('on_user_attributes_saved', $ue);
    }

    /**
     * @param \Concrete\Core\Entity\Attribute\Key\UserKey[] $attributes
     */
    public function saveUserAttributesDefault(array $attributes)
    {
        foreach ($attributes as $uak) {
            $controller = $uak->getController();
            if (method_exists($controller, 'createDefaultAttributeValue')) {
                $value = $controller->createDefaultAttributeValue();
                if ($value !== null) {
                    $this->setAttribute($uak, $value);
                }
            }
        }
        $ue = new UserInfoWithAttributesEvent($this);
        $ue->setAttributes($attributes);
        $this->getDirector()->dispatch('on_user_attributes_saved', $ue);
    }

    /**
     * {@inheritdoc}
     *
     * @return UserCategory
     *
     * @see \Concrete\Core\Attribute\ObjectInterface::getObjectAttributeCategory()
     */
    public function getObjectAttributeCategory()
    {
        return $this->application->make(UserCategory::class);
    }

    /**
     * @param string|\Concrete\Core\Entity\Attribute\Key\UserKey $ak
     * @param bool $createIfNotExists
     *
     * @return UserValue|null
     */
    public function getAttributeValueObject($ak, $createIfNotExists = false)
    {
        if (!is_object($ak)) {
            $ak = UserKey::getByHandle($ak);
        }
        if (is_object($ak)) {
            $value = $this->getObjectAttributeCategory()->getAttributeValue($ak, $this->entity);
        } else {
            $value = null;
        }

        if ($value === null && $createIfNotExists) {
            $value = new UserValue();
            $value->setUser($this->entity);
            $value->setAttributeKey($ak);
        }

        return $value;
    }

    /**
     * Magic method for user attributes. This is db expensive but pretty damn cool
     * so if the attrib handle is "my_attribute", then get the attribute with $ui->getUserMyAttribute(), or "uFirstName" become $ui->getUserUfirstname();.
     *
     * @return mixed|null
     */
    public function __call($nm, $a)
    {
        if (substr($nm, 0, 7) == 'getUser') {
            $nm = preg_replace('/(?!^)[[:upper:]]/', '_\0', $nm);
            $nm = strtolower($nm);
            $nm = str_replace('get_user_', '', $nm);

            return $this->getAttribute($nm);
        }
    }

    /**
     * @return EventDispatcher
     */
    protected function getDirector()
    {
        if ($this->director === null) {
            $this->director = $this->application->make('director');
        }

        return $this->director;
    }

    /**
     * @deprecated Use \Core::make('user/registration')->create()
     */
    public static function add($data)
    {
        return Core::make('user/registration')->create($data);
    }

    /**
     * @deprecated Use \Core::make('user/registration')->createSuperUser()
     */
    public static function addSuperUser($uPasswordEncrypted, $uEmail)
    {
        return Core::make('user/registration')->createSuperUser($uPasswordEncrypted, $uEmail);
    }

    /**
     * @deprecated Use \Core::make('user/registration')->createFromPublicRegistration()
     */
    public static function register($data)
    {
        return Core::make('user/registration')->createFromPublicRegistration($data);
    }

    /**
     * @deprecated use \Core::make('Concrete\Core\User\UserInfoRepository')->getByID()
     */
    public static function getByID($uID)
    {
        return Core::make(UserInfoRepository::class)->getByID($uID);
    }

    /**
     * @deprecated use \Core::make('Concrete\Core\User\UserInfoRepository')->getByName()
     */
    public static function getByUserName($uName)
    {
        return Core::make(UserInfoRepository::class)->getByName($uName);
    }

    /**
     * @deprecated use \Core::make('Concrete\Core\User\UserInfoRepository')->getByEmail()
     */
    public static function getByEmail($uEmail)
    {
        return Core::make(UserInfoRepository::class)->getByEmail($uEmail);
    }

    /**
     * @deprecated use \Core::make('Concrete\Core\User\UserInfoRepository')->getByValidationHash()
     */
    public static function getByValidationHash($uHash, $unredeemedHashesOnly = true)
    {
        return Core::make(UserInfoRepository::class)->getByValidationHash($uHash, $unredeemedHashesOnly);
    }
}
