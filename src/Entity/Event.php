<?php

namespace BaclucEventPackage\Entity;

use BaclucC5Crud\Entity\Identifiable;
use BaclucC5Crud\Lib\GetterTrait;
use BaclucC5Crud\Lib\SetterTrait;
use DateTime;
use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\Table;

/**
 * Class Event.
 *
 * @IgnoreAnnotation("package")\n*
 * @Entity
 * @Table(name="bacluc_event")
 */
class Event implements Identifiable {
    use SetterTrait;
    use GetterTrait;

    /**
     * @var int
     * @Id @Column(type="integer", nullable=false, options={"unsigned": true})
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @var DateTime
     * @Column(type="datetime")
     */
    protected $date_from;
    /**
     * @var DateTime
     * @Column(type="datetime")
     */
    protected $date_to;

    /**
     * @var string
     * @Column(type="string", length=1000)
     */
    protected $title;
    /**
     * @var string
     * @Column(type="text")
     */
    protected $description;

    /**
     * @var Group[]
     * @ManyToMany(targetEntity="BaclucEventPackage\Entity\Group")
     * @JoinTable(name="events_groups",
     *     joinColumns={@JoinColumn(name="eventId", referencedColumnName="id")},
     *     inverseJoinColumns={@JoinColumn(name="groupId", referencedColumnName="gID")}
     * )
     */
    protected $eventGroups;

    /**
     * @var string
     * @Column(type="string")
     */
    protected $address;

    public function __construct() {
        $this->eventGroups = new ArrayCollection();
    }

    public function getId() {
        return $this->id;
    }

    public function setId(int $id) {
        $this->id = $id;
    }

    public static function getIdFieldName(): string {
        return 'id';
    }
}
