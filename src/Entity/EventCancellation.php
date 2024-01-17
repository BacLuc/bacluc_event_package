<?php

namespace BaclucEventPackage\Entity;

use BaclucC5Crud\Entity\Identifiable;
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
 * Class Event.
 *
 * @IgnoreAnnotation("package")\n*
 *
 * @Entity
 *
 * @Table(name="bacluc_event_cancellation")
 */
class EventCancellation implements Identifiable {
    use SetterTrait;
    use GetterTrait;

    /**
     * @var int
     *
     * @Id @Column(type="integer", nullable=false, options={"unsigned": true})
     *
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Column(type="string", length=100)
     */
    private $name;

    /**
     * @var Event
     *
     * @ManyToOne(targetEntity="BaclucEventPackage\Entity\Event")
     *
     * @JoinColumn(name="event_id", onDelete="CASCADE", nullable=false)
     */
    private $event;

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
