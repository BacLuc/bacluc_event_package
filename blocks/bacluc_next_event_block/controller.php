<?php
namespace Concrete\Package\BaclucEventPackage\Block\BaclucNextEventBlock;

use Concrete\Core\Http\Request;
use Concrete\Core\User\Group\Group;
use Concrete\Package\BaclucEventPackage\Src\Event;
use Concrete\Package\BaclucEventPackage\Src\UserAttendsEvent;
use Concrete\Package\BasicTablePackage\Src\BlockOptions\CanEditOption;
use Concrete\Package\BasicTablePackage\Src\BlockOptions\GroupRefOption as GroupRefOption;
use Concrete\Package\BasicTablePackage\Src\ExampleBaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use User;

//test
class Controller extends \Concrete\Package\BasicTablePackage\Block\BasicTableBlockPackaged\Controller
{
    protected $btHandle = 'bacluc_next_event_block';
    /**
     * table title
     * @var string
     */
    protected $header = "BaclucNextEventBlock";

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
     * @var \Doctrine\ORM\EntityManager

    private $entityManager;
*/

    /**
     * set blocktypeset
     * @var string
     */
    protected $btDefaultSet = 'bacluc_event_set';

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


        //load the current options

        if ($obj instanceof Block) {
         $bt = $this->getEntityManager()->getRepository('\Concrete\Package\BasicTablePackage\Src\BasicTableInstance')->findOneBy(array('bID' => $obj->getBlockID()));

            $this->basicTableInstance = $bt;
        }

        $this->requiredOptions = array(
                new GroupRefOption()
        );

        $this->requiredOptions[0]->set('optionName', "groupoptions");


    }



    /**
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t("Display next Event of chosen Groups.");
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t("BaclucNextEventBlock");

    }

    public function getNextEvent(){
        //get the groups
        $blockOptions = $this->getBlockOptions();
        /**
         * @var ArrayCollection $Groups
         */
        $GroupAssociations = $blockOptions[0]->get('GroupAssociations');
        $GroupAssociations = $GroupAssociations->toArray();
        $groupids = array();
        foreach($GroupAssociations as $groupnum => $GroupAssociation){
            $groupids[] = $GroupAssociation->Group->gID;
        }
        $Event = $this->model->getNextEvent($groupids);
        //var_dump($Event);
        return $Event;
    }

    public function view(){
        $event = $this->getNextEvent();
        $this->set("event", $event);

        $showAttend = false;
        $showApologize = false;
        if($event != null){
            /**
             * @var \DateTime $startDate
             */
            $startDate = new \DateTime($event->get("date_from")->format("Y-m-d")." ".$event->get("time_from"));

            if($startDate> new \DateTime()) {
                if (\Concrete\Core\User\User::isLoggedIn()) {
                    $user = new User();
                    $eventgroups = $event->get("EventGroups");
                    $canAttend = false;
                    foreach ($eventgroups as $num => $eventgroup) {
                        if ($user->inGroup(Group::getByID($eventgroup->get("Group")->get("gID")))) {
                            $canAttend = true;
                            break;
                        }
                    }
                    if($canAttend){
                        $userAttends = UserAttendsEvent::getCurrentAttendance($event, $user->getUserID());
                        if ($userAttends == null) {
                            $showApologize = true;
                            $showAttend = true;
                        }elseif($userAttends->get("state") == UserAttendsEvent::STATE_APOLOGIZED){
                            $showApologize = false;
                            $showAttend = true;
                        }elseif($userAttends->get("state") == UserAttendsEvent::STATE_ATTENDING){
                            $showApologize = true;
                            $showAttend = false;
                        }
                    }

                }
            }

        $this->set("showApologize", $showApologize);
        $this->set("showAttend", $showAttend);
        }
    }

    public function action_changeAttendance(){
        $post = Request::post();
        if(strlen($post['newstate'])>0 && User::isLoggedIn()){
            $newstate = $post['newstate'];
            if($newstate == UserAttendsEvent::STATE_APOLOGIZED || $newstate == UserAttendsEvent::STATE_ATTENDING){
                $event = $this->getNextEvent();
                $user = new User();
                UserAttendsEvent::changeUserAttendanceState($event,$user->getUserID(),$newstate);
            }
        }
    }

}
