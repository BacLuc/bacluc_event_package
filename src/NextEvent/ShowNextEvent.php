<?php


namespace BaclucEventPackage\NextEvent;


use BaclucC5Crud\Controller\ActionProcessor;
use BaclucC5Crud\Controller\Renderer;
use BaclucC5Crud\Controller\VariableSetter;
use BaclucC5Crud\TableViewService;
use BaclucEventPackage\EventActionRegistryFactory;
use function BaclucC5Crud\Lib\collect as collect;

class ShowNextEvent implements ActionProcessor
{
    /**
     * @var TableViewService
     */
    private $tableViewService;
    /**
     * @var VariableSetter
     */
    private $variableSetter;
    /**
     * @var Renderer
     */
    private $renderer;

    /**
     * ShowFormActionProcessor constructor.
     * @param TableViewService $tableViewService
     * @param VariableSetter $variableSetter
     * @param Renderer $renderer
     */
    public function __construct(TableViewService $tableViewService, VariableSetter $variableSetter, Renderer $renderer)
    {
        $this->tableViewService = $tableViewService;
        $this->variableSetter = $variableSetter;
        $this->renderer = $renderer;
    }


    function getName(): string
    {
        return EventActionRegistryFactory::SHOW_NEXT_EVENT;
    }

    function process(array $get, array $post, ...$additionalParameters)
    {
        $tableView = $this->tableViewService->getTableView();

        if (sizeof($tableView->getRows()) >= 1) {
            $detailEntry = collect($tableView->getRows())->first();
            $eventfound = true;
        } else {
            $eventfound = false;
        }
        $this->variableSetter->set("eventfound", $eventfound);
        if ($eventfound) {
            foreach ($detailEntry as $key => $value) {
                $this->variableSetter->set($key, $value);
            }
        }
        $this->renderer->render("view/nextevent");
    }

}