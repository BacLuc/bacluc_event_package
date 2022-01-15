<?php

namespace BaclucEventPackage;

use BaclucC5Crud\Controller\ActionProcessor;
use BaclucC5Crud\Controller\ActionRegistryFactory;
use BaclucC5Crud\Controller\CurrentUrlSupplier;
use BaclucC5Crud\Controller\PaginationParser;
use BaclucC5Crud\Controller\Renderer;
use BaclucC5Crud\Controller\VariableSetter;
use BaclucC5Crud\TableViewService;
use BaclucC5Crud\View\FormView\IntegerField;
use BaclucC5Crud\View\TableView\TableViewFieldConfiguration;
use BaclucC5Crud\View\ViewActionRegistry;

class ShowCancellations implements ActionProcessor {
    public const TABLE_VIEW = 'view/table';
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
    /**
     * @var PaginationParser
     */
    private $paginationParser;

    private CurrentUrlSupplier $currentUrlSupplier;

    public function __construct(
        VariableSetter $variableSetter,
        Renderer $renderer,
        NoEditIdFallbackActionProcessor $noEditIdFallbackActionProcessor,
        TableViewFieldConfiguration $tableViewFieldConfiguration,
        CancellationsRepository $cancellationsRepository,
        ViewActionRegistry $viewActionRegistry,
        PaginationParser $paginationParser,
        CurrentUrlSupplier $currentUrlSupplier
    ) {
        $this->variableSetter = $variableSetter;
        $this->renderer = $renderer;
        $this->noEditIdFallbackActionProcessor = $noEditIdFallbackActionProcessor;
        $this->tableViewFieldConfiguration = $tableViewFieldConfiguration;
        $this->cancellationsRepository = $cancellationsRepository;
        $this->viewActionRegistry = $viewActionRegistry;
        $this->paginationParser = $paginationParser;
        $this->currentUrlSupplier = $currentUrlSupplier;
    }

    public function getName(): string {
        return EventActionRegistryFactory::SHOW_CANCELLATIONS;
    }

    public function process(array $get, array $post, ...$additionalParameters) {
        $editId = null;
        if (1 == count($additionalParameters) && null != $additionalParameters[0]) {
            $editId = $additionalParameters[0];
        }
        if (null == $editId) {
            return call_user_func_array([$this->noEditIdFallbackActionProcessor, 'process'], func_get_args());
        }

        $eventCancellationsTableEntrySupplier =
            new EventCancellationsTableEntrySupplier($editId, $this->cancellationsRepository);
        $tableViewService =
            new TableViewService($eventCancellationsTableEntrySupplier, $this->tableViewFieldConfiguration);

        $paginationConfiguration = $this->paginationParser->parse($get);
        $tableView = $tableViewService->getTableView($paginationConfiguration);
        $this->variableSetter->set('headers', $tableView->getHeaders());
        $this->variableSetter->set('rows', $tableView->getRows());
        $this->variableSetter->set(
            'actions',
            [$this->viewActionRegistry->getByName(ActionRegistryFactory::BACK_TO_MAIN)]
        );
        $this->variableSetter->set('rowactions', []);
        $this->variableSetter->set('count', $tableView->getCount());
        $this->variableSetter->set('currentPage', $paginationConfiguration->getCurrentPage());
        $this->variableSetter->set('pageSize', $paginationConfiguration->getPageSize());
        $pageSizeField = new IntegerField('Entries to display', 'pageSize', $paginationConfiguration->getPageSize());
        $this->variableSetter->set('pageSizeField', $pageSizeField);
        $this->variableSetter->set('currentURL', $this->currentUrlSupplier->getUrl());
        $this->renderer->render(self::TABLE_VIEW);
    }
}
