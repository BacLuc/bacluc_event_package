<?php


namespace BaclucEventPackage;


use BaclucC5Crud\Controller\ActionProcessor;
use BaclucC5Crud\Controller\ActionRegistryFactory;
use BaclucC5Crud\Controller\Renderer;
use BaclucC5Crud\Controller\VariableSetter;
use BaclucC5Crud\TableViewService;
use BaclucC5Crud\View\TableView\TableViewFieldConfiguration;
use BaclucC5Crud\View\ViewActionRegistry;

class ShowCancellations implements ActionProcessor
{
    const TABLE_VIEW = "view/table";
    /**
     * @var VariableSetter
     */
    private $variableSetter;
    /**
     * @var Renderer
     */
    private $renderer;
    /**
     * @var NoEditIdFallbackActionProcessor
     */
    private $noEditIdFallbackActionProcessor;
    /**
     * @var TableViewFieldConfiguration
     */
    private $tableViewFieldConfiguration;
    /**
     * @var CancellationsRepository
     */
    private $cancellationsRepository;
    /**
     * @var ViewActionRegistry
     */
    private $viewActionRegistry;

    public function __construct(
        VariableSetter $variableSetter,
        Renderer $renderer,
        NoEditIdFallbackActionProcessor $noEditIdFallbackActionProcessor,
        TableViewFieldConfiguration $tableViewFieldConfiguration,
        CancellationsRepository $cancellationsRepository,
        ViewActionRegistry $viewActionRegistry
    ) {
        $this->variableSetter = $variableSetter;
        $this->renderer = $renderer;
        $this->noEditIdFallbackActionProcessor = $noEditIdFallbackActionProcessor;
        $this->tableViewFieldConfiguration = $tableViewFieldConfiguration;
        $this->cancellationsRepository = $cancellationsRepository;
        $this->viewActionRegistry = $viewActionRegistry;
    }

    function getName(): string
    {
        return EventActionRegistryFactory::SHOW_CANCELLATIONS;
    }


    function process(array $get, array $post, ...$additionalParameters)
    {
        $editId = null;
        if (count($additionalParameters) == 1 && $additionalParameters[0] != null) {
            $editId = $additionalParameters[0];
        }
        if ($editId == null) {
            return call_user_func_array([$this->noEditIdFallbackActionProcessor, "process"], func_get_args());
        }

        $eventCancellationsTableEntrySupplier =
            new EventCancellationsTableEntrySupplier($editId, $this->cancellationsRepository);
        $tableViewService =
            new TableViewService($eventCancellationsTableEntrySupplier, $this->tableViewFieldConfiguration);

        $tableView = $tableViewService->getTableView();
        $this->variableSetter->set("headers", $tableView->getHeaders());
        $this->variableSetter->set("rows", $tableView->getRows());
        $this->variableSetter->set("actions",
            [$this->viewActionRegistry->getByName(ActionRegistryFactory::BACK_TO_MAIN)]);
        $this->variableSetter->set("rowactions", []);
        $this->variableSetter->set("count", $tableView->getCount());
        $this->renderer->render(self::TABLE_VIEW);
    }

}