<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 26.07.16
 * Time: 08:21
 */

namespace Concrete\Package\BaclucEventPackage\Src;


use Concrete\Package\BasicTablePackage\Src\AssociationBaseEntity;
use Concrete\Package\BasicTablePackage\Src\DiscriminatorEntry\DiscriminatorEntry;
use Concrete\Package\BasicTablePackage\Src\EntityGetterSetter;
use Concrete\Package\BasicTablePackage\Src\Group;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\Table;

/*because of the hack with @DiscriminatorEntry Annotation, all Doctrine Annotations need to be
properly imported*/

/**
 * Class EventGroup
 * @IgnoreAnnotation("package")\n*  Concrete\Package\BaclucEventPackage\Src
 * @Entity
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="discr", type="string")
 *  * @DiscriminatorEntry(value="Concrete\Package\BaclucEventPackage\Src\EventGroup")
 * @Table(name="bacluc_event_group")
 */
class EventGroup extends AssociationBaseEntity
{
    use EntityGetterSetter;
    //dontchange
    public static $staticEntityfilterfunction; //that you have a filter that is only for this entity
    /**
     * @var int
     * @Id @Column(type="integer")
     * @GEneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     *
     * @var Event
     * @ManyToOne(targetEntity="Event", inversedBy="EventGroups")
     * @JoinColumn(name="event_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $Event;

    /**
     *
     * @var Group
     * @ManyToOne(targetEntity="Concrete\Package\BasicTablePackage\Src\Group")
     * @JoinColumn(name="group_id", referencedColumnName="gID", onDelete="CASCADE")
     */
    protected $Group;


    public function __construct(){
        parent::__construct();
    }




}