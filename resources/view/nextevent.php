<?php
$title = $title ?: "";
$date_from = $date_from ?: "";
$date_to = $date_to ?: "";
$description = $description ?: "";
if (isset($eventfound) && $eventfound) {
    ?>
    <div class="bacluc_event bacluc_event_next_event_block">
        <div class="row">
            <div class="col-xs-12 title"><?= $title ?></div>
            <div class="col-xs-12 daterow">
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <span class='prefix from_prefix'><?= t("From:") ?></span> <?= $date_from ?>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <span class='prefix to_prefix'><?= t("To:") ?></span> <?= $date_to ?>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 description"><?= $description ?></div>
        </div>
    </div>
    <?php
} else {
    ?>
    <div class="bacluc_event bacluc_event_next_event_block">
        <?php echo t("No Events found"); ?>
    </div>
    <?php
}
?>
