<?php
namespace Concrete\Package\BaclucEventPackage\Block\BaclucEventParticipiantsBlock;

use Concrete\Core\User\Group\Group;
use Concrete\Package\BaclucEventPackage\Src\Event;
use Concrete\Package\BaclucEventPackage\Src\UserAttendsEvent;
use Concrete\Package\BasicTablePackage\Src\BaseEntityRepository;
use Concrete\Package\BasicTablePackage\Src\BlockOptions\CanEditOption;
use Concrete\Package\BasicTablePackage\Src\ExampleBaseEntity;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\Field as Field;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\IntegerField;

class Controller extends \Concrete\Package\BasicTablePackage\Block\BasicTableBlockPackaged\Controller
{
    protected $btHandle = 'bacluc_event_participiants_block';
    /**
     * table title
     * @var string
     */
    protected $header = "BaclucParticipiantsBlock";

    /**
     * Array of \Concrete\Package\BasicTablePackage\Src\BlockOptions\TableBlockOption
     * @var array
     */
    protected $requiredOptions = array();

    /**
     * @var \Concrete\Package\BasicTablePackage\Src\BaseEntity
     */
    protected $model;


    /**
     * set blocktypeset
     * @var string
     */
    protected $btDefaultSet = 'bacluc_event_set';


    protected $fieldTypes = array();

    /**
     *
     * Controller constructor.
     * @param null $obj
     */
    function __construct($obj = null)
    {
        //$this->model has to be instantiated before, that session handling works right

        $this->model = new Event();
        parent::__construct($obj);



        if ($obj instanceof Block) {
         $bt = $this->getEntityManager()->getRepository('\Concrete\Package\BasicTablePackage\Src\BasicTableInstance')->findOneBy(array('bID' => $obj->getBlockID()));

            $this->basicTableInstance = $bt;
        }


/*
 * add blockoptions here if you wish
        $this->requiredOptions = array(
            new TextBlockOption(),
            new DropdownBlockOption(),
            new CanEditOption()
        );

        $this->requiredOptions[0]->set('optionName', "Test");
        $this->requiredOptions[1]->set('optionName', "TestDropDown");
        $this->requiredOptions[1]->setPossibleValues(array(
            "test",
            "test2"
        ));

        $this->requiredOptions[2]->set('optionName', "testlink");
*/

        $this->fieldTypes = array(
            'event' => new Field("event", "event", "event")
            ,'total' => new IntegerField("total", "total", "total")
            ,UserAttendsEvent::STATE_APOLOGIZED => new IntegerField(UserAttendsEvent::STATE_APOLOGIZED ,UserAttendsEvent::STATE_APOLOGIZED ,UserAttendsEvent::STATE_APOLOGIZED )
            ,UserAttendsEvent::STATE_ATTENDING => new IntegerField(UserAttendsEvent::STATE_ATTENDING,UserAttendsEvent::STATE_ATTENDING,UserAttendsEvent::STATE_ATTENDING)
            ,UserAttendsEvent::STATE_ATTENDED => new IntegerField(UserAttendsEvent::STATE_ATTENDED,UserAttendsEvent::STATE_ATTENDED,UserAttendsEvent::STATE_ATTENDED)
        );

    }



    /**
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t("Show participiants of event");
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t("BaclucParticipiantsBlock");
    }

    public function getTableControlButtons($view){
        return '
        <div class="tablecontrols">
    
                        
                         <a href="' . $view->action('exportCSV') . '" >
                            <button type="button" value=""  class="btn inlinebtn actionbutton exportcsv" aria-label="' . t("export CSV") . '" title="' . t("export CSV") . '">
                                    <i class ="fa fa-download" aria-hidden="true"> </i>
                            </button>
                        </a>
                        
                        
            
           </div>  
        
        ';
    }

    function getActions($object, $row = array())
    {
        //".$object->action('edit_row_form')."
        $string = "
    	<td class='actioncell'>
    	<form method='post' action='" . $object->action('edit_row_form') . "'>
    		<input type='hidden' name='rowid' value='" . $row['id'] . "'/>
    		<input type='hidden' name='action' value='edit' id='action_" . $row['id'] . "'>";
            $string.=$this->getDetailActionIcon($row);


        $string .= "</form>
    	</td>";
        return $string;
    }

    function getDetailActionIcon($row)
    {
        return static::getActionButton($row,"detail", "btn inlinebtn actionbutton list", "list","fa fa-list");
    }

    function action_edit_row_form()
    {
        //empty because nothing should happen
    }

    function action_save_row($redirectOnSuccess = true)
    {
        //empty because nothing should happen
    }

    function action_add_new_row_form()
    {
        //empty because nothing should happen
    }

    /**
     * funciton to retrieve the table data
     * @return array
     */
    public function displayTable()
    {
        $query = BaseEntityRepository::getBuildQueryWithJoinedAssociations(UserAttendsEvent::class);

        $userAttendsEvents = $query->getQuery()->getResult();

        $events = BaseEntityRepository::getBuildQueryWithJoinedAssociations(Event::class)->getQuery()->getResult();
        $attendedMap = array();


        $qb = \Concrete\Package\BasicTablePackage\Controller::getEntityManagerStatic()->createQueryBuilder();
        $qb
            ->select( 'Event.id as eventid, g.gID as groupid')
            ->from('Concrete\Package\BaclucEventPackage\Src\Event', 'Event')
            ->join('Event.EventGroups','eg')
            ->join('eg.Group','g');

        $event_groups = $qb->getQuery()->getResult();

        $uniqueStringFunction = Event::getDefaultGetDisplayStringFunction();

        foreach ($events as $event){
            /**
             * @var Event $event
             */
            //first get Users which are allowed to attend event
            $attendedMap[$event->getId()]=array(
                'uniqueString' => $uniqueStringFunction($event)
                ,'users'=>array()
            );

        }

        $result = array();

        foreach($event_groups as $row){
            $group = Group::getByID($row['groupid']);
            $users = $group->getGroupMemberIDs();
            $result[$row['eventid']]=array(
                'event' => $attendedMap[$row['eventid']]['uniqueString']
                ,'total' => count($users)
            , UserAttendsEvent::STATE_APOLOGIZED => 0
            , UserAttendsEvent::STATE_ATTENDING => 0
            , UserAttendsEvent::STATE_ATTENDED => 0
            );
        }

        foreach ($userAttendsEvents as $userAttendsEvent){
            /**
             * @var UserAttendsEvent $userAttendsEvent
             */
            switch ($userAttendsEvent->get("state")){
                case UserAttendsEvent::STATE_ATTENDED:
                    $result[$userAttendsEvent->get("Event")->get("id")][UserAttendsEvent::STATE_ATTENDED]++;
                    break;
                case UserAttendsEvent::STATE_ATTENDING:
                    $result[$userAttendsEvent->get("Event")->get("id")][UserAttendsEvent::STATE_ATTENDING]++;
                    break;
                case UserAttendsEvent::STATE_APOLOGIZED:
                    $result[$userAttendsEvent->get("Event")->get("id")][UserAttendsEvent::STATE_APOLOGIZED]++;
                    break;
            }
        }

        return $result;

    }

    /**
     * @return array of Application\Block\BasicTableBlock\Field
     */
    public function getFields()
    {
        return $this->fieldTypes;
    }

}
