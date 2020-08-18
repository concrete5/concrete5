<?php

namespace Concrete\Core\Validator\String;

use ArrayAccess;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Validator\ValidatorForSubjectInterface;
use InvalidArgumentException;

/**
 * Validate the length of a string.
 */
class UniqueUserEmailValidator extends EmailValidator implements ValidatorForSubjectInterface
{
    /**
     * The email address is already in use.
     *
     * @var int
     */
    const E_EMAIL_IN_USE = 101;

    /**
     * The email address is already in use by another user.
     *
     * @var int
     */
    const E_EMAIL_USED_BY_ANOTHER_USER = 102;

    /**
     * The connection to the database.
     *
     * @var \Concrete\Core\Database\Connection\Connection
     */
    protected $connection;

    /**
     * UniqueUserEmailValidator constructor.
     *
     *
     * @param bool $testMXRecord Should we test the MX record to see if the domain is valid?
     * @param bool $strict Should email address warnings be considered as errors?
     * @param \Concrete\Core\Database\Connection\Connection $connection
     */
    public function __construct($testMXRecord = false, $strict = false, Connection $connection)
    {
        parent::__construct($testMXRecord, $strict);
        $this->connection = $connection;
        $this->setRequirementString(
            self::E_EMAIL_IN_USE,
            function (UniqueUserEmailValidator $validator, $code) {
                return t('An email address must be unique.');
            }
        );
        $this->setRequirementString(
            self::E_EMAIL_USED_BY_ANOTHER_USER,
            function (UniqueUserEmailValidator $validator, $code) {
                return t('An email address must be unique.');
            }
        );
        $this->setErrorString(
            self::E_EMAIL_IN_USE,
            function (UniqueUserEmailValidator $validator, $code, $mixed) {
                return t('The email address "%s" is already in use. Please contact the administrator of this site for more information.', $mixed);
            }
        );
        $this->setErrorString(
            self::E_EMAIL_USED_BY_ANOTHER_USER,
            function (UniqueUserEmailValidator $validator, $code, $mixed) {
                return t('The email address "%s" is already in use. Please contact the administrator of this site for more information.', $mixed);
            }
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Validator\ValidatorInterface::isValid()
     */
    public function isValid($mixed, ArrayAccess $error = null)
    {
        return $this->isValidFor($mixed, null, $error);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Validator\ValidatorForSubjectInterface::isValidFor()
     */
    public function isValidFor($mixed, $subject = null, ArrayAccess $error = null)
    {
        $result = parent::isValid($mixed, $error);
        if ($result === true) {
            $uID = 0;
            if ($subject) {
                if (is_object($subject)) {
                    if (!method_exists($subject, 'getUserID')) {
                        throw new InvalidArgumentException(t('Invalid subject type supplied to validator.'));
                    }
                    $uID = (int) $subject->getUserID();
                } else {
                    $uID = (int) $subject;
                }
            } else {
                $uID = 0;
            }
            $qb = $this->connection->createQueryBuilder();
            $qb
                ->select('u.uID')
                ->from('Users', 'u')
                ->where($qb->expr()->eq('u.uEmail', $qb->createNamedParameter($mixed)))
            ;
            if ($uID !== 0) {
                $qb->andWhere($qb->expr()->neq('u.uID', $qb->createNamedParameter($uID)));
            }
            if ($qb->execute()->fetchColumn() !== false) {
                $result = false;
                if ($error) {
                    $message = $this->getErrorString($uID === 0 ? self::E_EMAIL_IN_USE : self::E_EMAIL_USED_BY_ANOTHER_USER, $mixed);
                    if ($message) {
                        $error[] = $message;
                    }
                }
            }
        }

        return $result;
    }
}
