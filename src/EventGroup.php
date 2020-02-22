<?php
namespace BaclucEventPackage;


use BaclucC5Crud\Lib\GetterTrait;
use BaclucC5Crud\Lib\SetterTrait;
use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

/**
 * Class EventGroup
 * @IgnoreAnnotation("package")\n*  Concrete\Package\BaclucEventPackage\Src
 * @Entity
 * @Table(name="bacluc_event_group")
 */
class EventGroup
{
    use SetterTrait, GetterTrait;
    /**
     * @var int
     * @Id @Column(type="integer")
     * @GEneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     *
     * @var Event
     * @ManyToOne(targetEntity="BaclucEventPackage\Event", inversedBy="eventGroups")
     * @JoinColumn(name="event_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $event;

    /**
     *
     * @var Group
     * @ManyToOne(targetEntity="BaclucEventPackage\Group")
     * @JoinColumn(name="group_id", referencedColumnName="gID", onDelete="CASCADE")
     */
    protected $group;

}