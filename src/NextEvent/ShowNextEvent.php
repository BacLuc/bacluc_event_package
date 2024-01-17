<?php

namespace BaclucEventPackage\NextEvent;

use BaclucC5Crud\Controller\PaginationConfiguration;
use BaclucC5Crud\Controller\Renderer;
use BaclucC5Crud\Controller\VariableSetter;
use BaclucC5Crud\TableViewService;
use BaclucC5Crud\View\ViewActionRegistry;
use BaclucEventPackage\EventActionRegistryFactory;
use BaclucEventPackage\NoEditIdFallbackActionProcessor;

use function BaclucC5Crud\Lib\collect;

class ShowNextEvent implements NoEditIdFallbackActionProcessor {
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
     * @var ViewActionRegistry
     */
    private $viewActionRegistry;

    /**
     * ShowFormActionProcessor constructor.
     */
    public function __construct(
        TableViewService $tableViewService,
        VariableSetter $variableSetter,
        Renderer $renderer,
        ViewActionRegistry $viewActionRegistry
    ) {
        $this->tableViewService = $tableViewService;
        $this->variableSetter = $variableSetter;
        $this->renderer = $renderer;
        $this->viewActionRegistry = $viewActionRegistry;
    }

    public function getName(): string {
        return EventActionRegistryFactory::SHOW_NEXT_EVENT;
    }

    public function process(array $get, array $post, ...$additionalParameters) {
        $tableView = $this->tableViewService->getTableView(new PaginationConfiguration(0, null));

        $rows = $tableView->getRows();
        if (sizeof($rows) >= 1) {
            $detailEntry = collect($rows)->first();
            $eventfound = true;
        } else {
            $eventfound = false;
        }
        $this->variableSetter->set('eventfound', $eventfound);
        if ($eventfound) {
            foreach ($detailEntry as $key => $value) {
                $this->variableSetter->set($key, $value);
            }
            $this->variableSetter->set('eventId', array_keys($rows)[0]);
        }
        $this->variableSetter->set(
            'actions',
            [$this->viewActionRegistry->getByName(EventActionRegistryFactory::SHOW_CANCEL_EVENT_FORM)]
        );
        $this->renderer->render('view/nextevent');
    }
}
