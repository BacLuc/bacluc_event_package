<?php

namespace BaclucEventPackage;

use BaclucC5Crud\Controller\ActionProcessors\ShowErrorActionProcessor as CrudShowErrorActionProcessor;

class ShowErrorActionProcessor extends CrudShowErrorActionProcessor implements NoEditIdFallbackActionProcessor {}
