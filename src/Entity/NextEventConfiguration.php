<?php

namespace BaclucEventPackage\Entity;

use BaclucC5Crud\Entity\Identifiable;
use BaclucC5Crud\Lib\GetterTrait;
use BaclucC5Crud\Lib\SetterTrait;
use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\Table;

/**
 * @IgnoreAnnotation("package")
 *  Concrete\Package\BaclucC5Crud\Src
 *
 * @Entity
 *
 * @Table(name="NextEventConfiguration")
 */
class NextEventConfiguration implements Identifiable {
    use GetterTrait;
    use SetterTrait;

    /**
     * @var Group[]
     *
     * @ManyToMany(targetEntity="BaclucEventPackage\Entity\Group")
     *
     * @JoinTable(name="nexteventconfiguration_groups",
     *     joinColumns={@JoinColumn(name="nextEventConfigurationId", referencedColumnName="id")},
     *     inverseJoinColumns={@JoinColumn(name="groupId", referencedColumnName="gID")}
     * )
     */
    protected $showNextEventOfGroups;

    /**
     * Id of the block the configuration references.
     *
     * @var int
     *
     * @Id
     *
     * @Column(type="integer")
     */
    private $id;

    /**
     * NextEventConfiguration constructor.
     */
    public function __construct() {
        $this->showNextEventOfGroups = new ArrayCollection();
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
