<?php
namespace Concrete\Core\User\PrivateMessage;

use Concrete\Core\File\File;
use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\User\PrivateMessage\Mailbox as UserPrivateMessageMailbox;
use Loader;
use UserInfo;
use Events;

class PrivateMessage extends ConcreteObject implements SubjectInterface
{
    protected $authorName = false;
    protected $mailbox;
    protected $attachments = [];

    public function getNotificationDate()
    {
        return \Core::make('date')->toDateTime($this->getMessageDateAdded());
    }

    public function getUsersToExcludeFromNotification()
    {
        return array();
    }

    public function getMessageDelimiter()
    {
        return t('-------------------- Original Message --------------------');
    }

    public static function getByID($msgID, $mailbox = false)
    {
        $db = Loader::db();
        $row = $db->GetRow('select uAuthorID, msgDateCreated, msgID, msgSubject, msgBody, uToID from UserPrivateMessages where msgID = ?', array($msgID));
        if (!isset($row['msgID'])) {
            return false;
        }

        $upm = new static();
        $upm->setPropertiesFromArray($row);

        if ($mailbox) {
            // we add in some mailbox-specific attributes
            $row = $db->GetRow('select msgID, msgIsNew, msgIsUnread, msgMailboxID, msgIsReplied, uID from UserPrivateMessagesTo where msgID = ? and uID = ?', array($msgID, $mailbox->getMailboxUserID()));
            if (isset($row['msgID'])) {
                $upm->setPropertiesFromArray($row);
            }
            $upm->mailbox = $mailbox;
        }

        // fetch the attachments from the given message
        foreach($db->getAll('select fID from UserPrivateMessagesAttachments where msgID = ?', [$msgID]) as $row) {
            $file = File::getByID($row["fID"]);
            if ($file instanceof \Concrete\Core\Entity\File\File) {
                $upm->attachments[] = $file;
            }
        }

        return $upm;
    }

    public function getMessageStatus()
    {
        if (is_object($this->mailbox)) {
            if (!$this->msgIsUnread) {
                return t('Read');
            }
            if ($this->mailbox->getMailboxID() == UserPrivateMessageMailbox::MBTYPE_SENT) {
                return t("Sent");
            }
        }

        if ($this->msgIsNew) {
            return t('New');
        }
        if ($this->msgIsUnread) {
            return t('Unread');
        }
        if ($this->msgIsReplied) {
            return t('Replied');
        }

        return t("Read");
    }

    public function markAsRead()
    {
        if (!$this->uID) {
            return false;
        }

        $db = Loader::db();
        if ($this->uID != $this->uAuthorID) {
            $ue = new Event($this);
            Events::dispatch('on_private_message_marked_as_read', $ue);

            $db->Execute('update UserPrivateMessagesTo set msgIsUnread = 0 where msgID = ? and msgMailboxID = ? and uID = ?', array($this->msgID, $this->msgMailboxID, $this->uID));
        }
    }

    public function getMessageAuthorID()
    {
        return $this->uAuthorID;
    }
    public function getMessageID()
    {
        return $this->msgID;
    }
    public function getMessageUserID()
    {
        return $this->uID;
    }
    public function getMessageAuthorObject()
    {
        return UserInfo::getByID($this->uAuthorID);
    }
    public function getMessageUserToID()
    {
        return $this->uToID;
    }
    public function getMessageUserToObject()
    {
        return UserInfo::getByID($this->uToID);
    }

    public function getMessageRelevantUserID()
    {
        if (is_object($this->mailbox)) {
            if ($this->mailbox->getMailboxID() == UserPrivateMessageMailbox::MBTYPE_SENT) {
                return $this->uToID;
            }
        }

        return $this->uAuthorID;
    }

    /**
     * Responsible for converting line breaks to br tags, perhaps running bbcode, as well as making the older replied-to messages gray.
     */
    public function getFormattedMessageBody()
    {
        $msgBody = $this->getMessageBody();
        $txt = Loader::helper('text');

        $repliedPos = strpos($msgBody, $this->getMessageDelimiter());
        if ($repliedPos > -1) {
            $repliedText = substr($msgBody, $repliedPos);
            $messageText = substr($msgBody, 0, $repliedPos);
            $msgBody = nl2br($txt->entities($messageText)) . '<div class="ccm-profile-message-replied">' . nl2br($txt->entities($repliedText)) . '</div>';
            $msgBody = str_replace($this->getMessageDelimiter(), '<hr />', $msgBody);
        } else {
            $msgBody = nl2br($txt->entities($msgBody));
        }

        return $msgBody;
    }

    public function isMessageUnread()
    {
        return $this->msgIsUnread;
    }

    public function isMessageReplied()
    {
        return $this->msgIsReplied;
    }

    public function isMessageNew()
    {
        return $this->msgIsNew;
    }

    public function delete()
    {
        $db = Loader::db();
        if (!$this->uID) {
            return false;
        }

        $ue = new Event($this);
        $ue = Events::dispatch('on_private_message_delete', $ue);
        if (!$ue) {
            return;
        }

        $db->Execute('delete from UserPrivateMessagesTo where uID = ? and msgID = ?', array($this->uID, $this->msgID));
    }

    public function getMessageRelevantUserObject()
    {
        $ui = UserInfo::getByID($this->getMessageRelevantUserID());

        return $ui;
    }

    public function getMessageRelevantUserName()
    {
        $ui = UserInfo::getByID($this->getMessageRelevantUserID());
        if (is_object($ui)) {
            return $ui->getUserName();
        }
    }

    public function getMessageAuthorName()
    {
        if ($this->authorName == false) {
            $author = $this->getMessageAuthorObject();
            if (is_object($author)) {
                $this->authorName = $author->getUserName();
            } else {
                $this->authorName = t('Unknown User');
            }
        }

        return $this->authorName;
    }

    public function getMessageDateAdded()
    {
        return $this->msgDateCreated;
    }

    public function getMessageSubject()
    {
        return $this->msgSubject;
    }
    public function getFormattedMessageSubject()
    {
        $txt = Loader::helper('text');

        return $txt->entities($this->msgSubject);
    }
    public function getMessageBody()
    {
        return $this->msgBody;
    }

    /**
     * @return \Concrete\Core\Entity\File\File[]
     */
    public function getAttachments() {
        return $this->attachments;
    }
}
