<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 01.02.16
 * Time: 23:08
 */
namespace Concrete\Package\BaclucEventPackage\Src;
use Concrete\Core\Form\Service\Widget\DateTime;
use Concrete\Package\BaclucPersonPackage\Src\PostalAddress;
use Concrete\Package\BasicTablePackage\Src\BaseEntity;
use Concrete\Package\BasicTablePackage\Src\DiscriminatorEntry\DiscriminatorEntry;
use Concrete\Package\BasicTablePackage\Src\EntityGetterSetter;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\DateField as DateField;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\DirectEditAssociatedEntityField;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\DropdownLinkField;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\FileField as FileField;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\WysiwygField as WysiwygField;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\InheritanceType;


/*because of the hack with @DiscriminatorEntry Annotation, all Doctrine Annotations need to be
properly imported*/

/**
 * Class Event
 * @IgnoreAnnotation("package")\n*  Concrete\Package\BaclucEventPackage\Src
 *  @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorEntry(value="Concrete\Package\BaclucEventPackage\Src\Event")
 * @Entity
@Table(name="bacluc_event"
)
 *
 */
class Event extends BaseEntity
{
    use EntityGetterSetter;
    //dontchange
    public static $staticEntityfilterfunction; //that you have a filter that is only for this entity
    /**
     * @var int
     * @Id @Column(type="integer", nullable=false, options={"unsigned":true})
     * @GEneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @var DateTime
     * @Column(type="date")
     */
    protected $date_from;
    /**
     * @var DateTime
     * @Column(type="date")
     */
    protected $date_to;
    /**
     * @var DateTime
     * @Column(type="text", length=12)
     */
    protected $time_from;
    /**
     * @var DateTime
     * @Column(type="text", length=12)
     */
    protected $time_to;
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
     * @var int
     * @Column(type="integer")
     */
    protected $infofile;
    /**
     * @var int
     * @Column(type="integer")
     */
    protected $registerfile;
    /**
     * @var EventGroup[]
     * @OneToMany(targetEntity="Concrete\Package\BaclucEventPackage\Src\EventGroup", mappedBy="Event")

     */
    protected $EventGroups;


    /**
     * @var PostalAddress
     * @ManyToOne(targetEntity="Concrete\Package\BaclucPersonPackage\Src\PostalAddress")
     *
     */
    protected $PostalAddress;
    public function __construct(){
        parent::__construct();
        if($this->EventGroups == null){
            $this->EventGroups = new ArrayCollection();
        }
        $this->setDefaultFieldTypes();
    }
    public function setDefaultFieldTypes(){
        parent::setDefaultFieldTypes();
        $this->fieldTypes['title']->setNullable(false);
        /**
         * because Entity::setDefaultFieldTypes does not detect special field types like Wysiwyg field or filefield, they have to be set here manually.
         * The only Fields Entity::setDefaultFieldTypes detects are date, DropdownLinkField (m:1 relation), DropdownMulitLInkField (n:m relation)
         */
        $this->fieldTypes['description']=new WysiwygField("description",t("Description"),"description");
        $this->fieldTypes['infofile']= new FileField("infofile", t('Info'), "info");
        $this->fieldTypes['registerfile']= new FileField("registerfile", t('Anmeldung'), "register");

        $address = $this->fieldTypes['PostalAddress'];
        $directEditField = new DirectEditAssociatedEntityField($address->getSQLFieldName(), "Address", $address->getPostName());
        DropdownLinkField::copyLinkInfo($address,$directEditField);
        $this->fieldTypes['PostalAddress']=$directEditField;
    }

    public function getId(){
        return $this->id;
    }
    public function getIdFieldName()
    {
        return 'id';
    }
    /**
     * Returns the function, which generates the String for LInk Fields to identify the instance. Has to be unique to prevent errors
     * @return \Closure
     */
    public static function getDefaultGetDisplayStringFunction(){
        $function = function(Event $item){
            $dateField = new DateField("test", "test", "test");
            $returnString =$item->title;
            $dateField->setSQLValue($item->date_from);
            $returnString.= " ".$dateField->getTableView();
            if($item->date_to != null){
                $returnString =$item->title;
                $dateField->setSQLValue($item->date_to);
                $returnString.= " ".$dateField->getTableView();
            }
            return $returnString;
        };
        return $function;
    }
    public function getNextEvent(array  $groupids){
        $qb = \Concrete\Package\BasicTablePackage\Controller::getEntityManagerStatic()->createQueryBuilder();
        $qb
            ->select( 'Event')
            ->from('Concrete\Package\BaclucEventPackage\Src\Event', 'Event')
            ->join('Event.EventGroups','eg')
            ->join('eg.Group','g')
            ->where($qb->expr()->in('g.gID', ':Groups'))
            ->andWhere($qb->expr()->gte('Event.date_to', ":date"))
            ->orderBy('Event.date_from')
            ->setMaxResults(1)
            ->setParameter('Groups',$groupids)
            ->setParameter('date', new \DateTime())
        ;
        $query = $qb->getQuery();
        $result = $query->getResult();
        if($result != null){
            if(count($result)==1){
                return $result[0];
            }
        }
        return null;
    }
}