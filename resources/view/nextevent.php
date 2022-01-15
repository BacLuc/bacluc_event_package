<?php
$title = $title ?? '';
$date_from = $date_from ?? '';
$date_to = $date_to ?? '';
$description = $description ?? '';
$actions = $actions ?? [];
$eventId = $eventId ?? [];
$address = $address ?? '';
if (isset($eventfound) && $eventfound) {
    ?>
    <div class="bacluc_event bacluc_event_next_event_block">
        <div class="row">
            <div class="col-xs-12 title"><?php echo $title; ?></div>
            <div class="col-xs-12 addressrow">
                <div class="row">
                    <div class="col-xs-12 col-sm-12">
                        <span class='prefix address_prefix'><?php echo t('Address:'); ?></span> <?php echo $address; ?>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 daterow">
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <span class='prefix from_prefix'><?php echo t('From:'); ?></span> <?php echo $date_from; ?>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <span class='prefix to_prefix'><?php echo t('To:'); ?></span> <?php echo $date_to; ?>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 description"><?php echo $description; ?></div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <?php foreach ($actions as $action) { ?>
                    <?php /** @var \BaclucC5Crud\View\ViewActionDefinition $action */ ?>
                    <a href="<?php echo $this->action($action->getAction())."/{$eventId}"; ?>">
                        <button type="submit" class="btn inlinebtn actionbutton btn-light <?php echo $action->getButtonClass(); ?>"
                                aria-label="<?php echo t($action->getAriaLabel()); ?>"
                                title="<?php echo t($action->getTitle()); ?>">
                            <i class="fa <?php echo $action->getIconClass(); ?>" aria-hidden="true"> </i>
                            <span><?php echo t('Cancel'); ?></span>
                        </button>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php
} else {
        ?>
    <div class="bacluc_event bacluc_event_next_event_block">
        <?php echo t('No Events found'); ?>
    </div>
    <?php
    }
?>
