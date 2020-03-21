<?php


namespace BaclucEventPackage;

use BaclucC5Crud\Lib\GetterTrait;
use BaclucC5Crud\Lib\SetterTrait;
use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

/**
 * Class Event
 * @IgnoreAnnotation("package")\n*
 * @Entity
 * @Table(name="bacluc_event_cancellation")
 *
 */
class EventCancellation
{
    use SetterTrait, GetterTrait;
    /**
     * @var int
     * @Id @Column(type="integer", nullable=false, options={"unsigned":true})
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Column(type="string", length=100)
     */
    private $name;

    /**
     * @var Event
     * @ManyToOne(targetEntity="BaclucEventPackage\Event")
     * @JoinColumn(name="event_id", onDelete="CASCADE", nullable=false)
     *
     */
    private $event;

}