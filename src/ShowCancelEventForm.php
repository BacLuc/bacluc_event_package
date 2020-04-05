<?php


namespace BaclucEventPackage;


use BaclucC5Crud\Controller\ActionProcessor;
use BaclucC5Crud\Controller\ActionProcessors\ShowEditEntryForm;
use BaclucC5Crud\Controller\Renderer;
use BaclucC5Crud\Controller\VariableSetter;
use BaclucC5Crud\View\CancelFormViewAction;
use BaclucC5Crud\View\FormView\TextField;
use BaclucC5Crud\View\SubmitFormViewAction;

class ShowCancelEventForm implements ActionProcessor
{
    /**
     * @var VariableSetter
     */
    private $variableSetter;
    /**
     * @var Renderer
     */
    private $renderer;
    /**
     * @var SubmitFormViewAction
     */
    private $submitFormAction;
    /**
     * @var CancelFormViewAction
     */
    private $cancelFormAction;

    public function __construct(
        VariableSetter $variableSetter,
        Renderer $renderer,
        SubmitFormViewAction $submitFormAction,
        CancelFormViewAction $cancelFormAction
    ) {
        $this->variableSetter = $variableSetter;
        $this->renderer = $renderer;
        $this->submitFormAction = $submitFormAction;
        $this->cancelFormAction = $cancelFormAction;
    }


    function getName(): string
    {
        return EventActionRegistryFactory::SHOW_CANCEL_EVENT_FORM;
    }

    function process(array $get, array $post, ...$additionalParameters)
    {
        $textField = new TextField("Name", "name", "");
        $editId = null;
        if (count($additionalParameters) == 1) {
            $editId = $additionalParameters[0];
        }
        $this->variableSetter->set("fields", [$textField]);
        $this->variableSetter->set("editId", $editId);
        $this->variableSetter->set("addFormTags", true);
        $this->variableSetter->set("submitFormAction", $this->submitFormAction);
        $this->variableSetter->set("cancelFormAction", $this->cancelFormAction);
        $this->renderer->render(ShowEditEntryForm::FORM_VIEW);
    }

}