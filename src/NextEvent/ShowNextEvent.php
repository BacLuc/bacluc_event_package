<?php


namespace BaclucEventPackage\NextEvent;


use BaclucC5Crud\Controller\ActionProcessor;
use BaclucC5Crud\Controller\Renderer;
use BaclucC5Crud\Controller\VariableSetter;
use BaclucC5Crud\TableViewService;
use function BaclucC5Crud\Lib\collect as collect;

class ShowNextEvent implements ActionProcessor
{
    const DETAIL_VIEW = "view/detail";
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
        return NextEventRegistryFactory::SHOW_NEXT_EVENT;
    }

    function process(array $get, array $post, ...$additionalParameters)
    {
        $tableView = $this->tableViewService->getTableView();

        if (sizeof($tableView->getRows()) >= 1) {
            $detailEntry = collect($tableView->getRows())->first();
            $headersAndValues = collect($tableView->getHeaders())->combine($detailEntry);
        } else {
            $headersAndValues = collect(array_flip($tableView->getHeaders()))
                ->map(function () {
                    return "";
                })->toArray();
        }
        $this->variableSetter->set("properties", $headersAndValues);
        $this->renderer->render(self::DETAIL_VIEW);
    }

}