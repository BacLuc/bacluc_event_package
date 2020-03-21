<?php


namespace BaclucEventPackage;


use BaclucC5Crud\Controller\ActionProcessor;
use BaclucC5Crud\Controller\ActionProcessors\ShowEditEntryForm;
use BaclucC5Crud\Controller\Renderer;
use BaclucC5Crud\Controller\VariableSetter;
use BaclucC5Crud\View\FormView\TextField;

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

    public function __construct(VariableSetter $variableSetter, Renderer $renderer)
    {
        $this->variableSetter = $variableSetter;
        $this->renderer = $renderer;
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
        $this->renderer->render(ShowEditEntryForm::FORM_VIEW);
    }

}