<?php

namespace BaclucEventPackage;

use BaclucC5Crud\Controller\ActionProcessor;
use BaclucC5Crud\Controller\Renderer;
use BaclucC5Crud\Controller\Validation\ValidationResultItem;
use BaclucC5Crud\Controller\Validation\Validator;
use BaclucC5Crud\Controller\ValuePersisters\FieldPersistor;
use BaclucC5Crud\Controller\ValuePersisters\PersistorConfiguration;
use BaclucC5Crud\Controller\VariableSetter;
use BaclucC5Crud\Entity\Repository;
use BaclucC5Crud\FormViewAfterValidationFailedService;
use BaclucC5Crud\View\CancelFormViewAction;
use BaclucC5Crud\View\SubmitFormViewAction;

use function BaclucC5Crud\Lib\collect;

class PostCancelEventForm implements ActionProcessor {
    public const FORM_VIEW = 'view/form';

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var FormViewAfterValidationFailedService
     */
    private $formViewAfterValidationFailedService;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var PersistorConfiguration
     */
    private $peristorConfiguration;

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
     * @var SubmitFormViewAction
     */
    private $submitFormAction;

    /**
     * @var CancelFormViewAction
     */
    private $cancelFormAction;

    /**
     * PostFormActionProcessor constructor.
     */
    public function __construct(
        Validator $validator,
        FormViewAfterValidationFailedService $formViewAfterValidationFailedService,
        Repository $repository,
        PersistorConfiguration $peristorConfiguration,
        VariableSetter $variableSetter,
        Renderer $renderer,
        NoEditIdFallbackActionProcessor $noEditIdFallbackActionProcessor,
        SubmitFormViewAction $submitFormAction,
        CancelFormViewAction $cancelFormAction
    ) {
        $this->validator = $validator;
        $this->formViewAfterValidationFailedService = $formViewAfterValidationFailedService;
        $this->repository = $repository;
        $this->peristorConfiguration = $peristorConfiguration;
        $this->variableSetter = $variableSetter;
        $this->renderer = $renderer;
        $this->noEditIdFallbackActionProcessor = $noEditIdFallbackActionProcessor;
        $this->submitFormAction = $submitFormAction;
        $this->cancelFormAction = $cancelFormAction;
    }

    public function getName(): string {
        return EventActionRegistryFactory::POST_CANCEL_EVENT_FORM;
    }

    public function process(array $get, array $post, ...$additionalParameters) {
        $editId = null;
        if (1 == count($additionalParameters) && null != $additionalParameters[0]) {
            $editId = $additionalParameters[0];
        }
        if (null == $editId) {
            return call_user_func_array([$this->noEditIdFallbackActionProcessor, 'process'], func_get_args());
        }

        $validationResult = $this->validator->validate($post);

        if (!$validationResult->isError()) {
            $postValues = collect($validationResult)
                ->keyBy(function (ValidationResultItem $validationResultItem) {
                    return $validationResultItem->getName();
                })
                ->map(function (ValidationResultItem $validationResultItem) {
                    return $validationResultItem->getPostValue();
                })
            ;

            $postValues['event'] = $editId;
            $entity = $this->repository->create();

            /**
             * @var FieldPersistor $persistor
             */
            foreach ($this->peristorConfiguration as $persistor) {
                $persistor->persist($postValues, $entity);
            }
            $this->repository->persist($entity);
        } else {
            $formView = $this->formViewAfterValidationFailedService->getFormView($validationResult);
            $this->variableSetter->set('fields', $formView->getFields());
            $this->variableSetter->set('editId', $editId);
            $validationErrors = collect($validationResult)
                ->keyBy(function (ValidationResultItem $resultItem) {
                    return $resultItem->getName();
                })->map(function (ValidationResultItem $resultItem) {
                    return $resultItem->getMessages();
                })
            ;
            $this->variableSetter->set('validationErrors', $validationErrors);
            $this->variableSetter->set('addFormTags', true);
            $this->variableSetter->set('submitFormAction', $this->submitFormAction);
            $this->variableSetter->set('cancelFormAction', $this->cancelFormAction);
            $this->renderer->render(self::FORM_VIEW);
        }
    }
}
