<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 01.02.16
 * Time: 23:08
 */

namespace BaclucEventPackage;

use BaclucC5Crud\Entity\WithUniqueStringRepresentation;
use BaclucC5Crud\Lib\GetterTrait;
use BaclucC5Crud\Lib\SetterTrait;
use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

/**
 * Class Group
 * package Concrete\Package\BasicTablePackage\Src
 * @Entity
 * @Table(name="Groups"
 * , indexes={
 * @Index(name="gName",
 * columns={"gName"}),
 * @Index(name="gBadgeFID",
 * columns={"gBadgeFID"}),
 * @Index(name="pkgID",
 * columns={"pkgID"})
 * }
 * )
 *
 */
class Group implements WithUniqueStringRepresentation
{
    use SetterTrait, GetterTrait;

    /**
     * @var int
     * @Id @Column(type="integer", nullable=false, options={"unsigned":true})
     * @GeneratedValue(strategy="AUTO")
     */
    private $gID;

    /**
     * @var string
     * @Column(type="string")
     */
    private $gName;

    /**
     * @var string
     * @Column(type="string", nullable=false)
     */
    private $gDescription;

    /**
     * @var boolean
     * @Column(type="boolean", options={"default"=0})
     */
    private $gUserExpirationIsEnabled;


    /**
     * @var string
     * @Column(type="string", length=12)
     */
    private $gUserExpirationMethod;

    /**
     * @var DateTime
     * @Column(type="datetime")
     */
    private $gUserExpirationSetDateTime;

    /**
     * @var int
     * @Column(type="integer", length=10, nullable=false, options={"unsigned":true, "default"=0})
     *
     */
    private $gUserExpirationInterval;

    /**
     * @var string
     * @Column(type="string", length=20)
     */
    private $gUserExpirationAction;

    /**
     * @var boolean
     * @Column(type="boolean", nullable=false, options={"default"=0})
     */
    private $gIsBadge;

    /**
     * @var int
     * @Column(type="integer", length=10, nullable=false, options={"unsigned":true, "default"=0})
     *
     */
    private $gBadgeFID;


    /**
     * @var string
     * @Column(type="string", length=255)
     */
    private $gBadgeDescription;

    /**
     * @var int
     * @Column(type="integer", length=11, nullable=false, options={"default"=0})
     */
    private $gBadgeCommunityPointValue;

    /**
     * @var boolean
     * @Column(type="boolean", nullable=false, options={"default"=0})
     */
    private $gIsAutomated;

    /**
     * @var boolean
     * @Column(type="boolean", nullable=false, options={"default"=0})
     */
    private $gCheckAutomationOnRegister;

    /**
     * @var boolean
     * @Column(type="boolean", nullable=false, options={"default"=0})
     */
    private $gCheckAutomationOnLogin;

    /**
     * @var boolean
     * @Column(type="boolean", nullable=false, options={"default"=0})
     */
    private $gCheckAutomationOnJobRun;

    /**
     * @var string
     * @Column(type="text", length=65535)
     */
    private $gPath;

    /**
     * @var int
     * @Column(type="integer", length=10, nullable=false, options={"unsigned":true, "default"=0})
     *
     */
    private $pkgID;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->gName;
    }

    public function createUniqueString(): string
    {
        return $this->gName;
    }
}