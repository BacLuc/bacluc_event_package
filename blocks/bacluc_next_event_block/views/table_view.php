<?php

use Application\Block\BasicTableBlock\FieldTypes\FileField;
use Application\Block\BasicTableBlock\FieldTypes\SelfSaveInterface;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\Field;

if($event != null) {
	/**
	 * @var Field[] $fieldTypes
	 */
	$fieldTypes = $event->getFieldTypes();

	$title = $fieldTypes['title']->setSQLValue($event->get("title"))->getTableView();


	$date_from = $fieldTypes['date_from']->setSQLValue($event->get("date_from"))->getTableView();
	$time_from = $fieldTypes['time_from']->setSQLValue($event->get("time_from"))->getTableView();

	$date_to = $fieldTypes['date_to']->setSQLValue($event->get("date_to"))->getTableView();
	$time_to = $fieldTypes['time_to']->setSQLValue($event->get("time_to"))->getTableView();

	$description = $fieldTypes['description']->setSQLValue($event->get("description"))->getTableView();

	$registerfile = $fieldTypes['registerfile']->setSQLValue($event->get("registerfile"))->getTableView();

	$infofile = $fieldTypes['infofile']->setSQLValue($event->get("infofile"))->getTableView();

	?>
	<div class="bacluc_event bacluc_event_next_event_block">
		<div class="row">
			<div class="col-xs-12 title"><?php echo $title ?></div>
			<div class="col-xs-12 daterow">
				<div class="row">
					<div
						class="col-xs-12 col-sm-6"><?php echo " <span class='prefix from_prefix'>" . t("Von:") . "</span> " . $date_from . " " . $time_from; ?></div>
					<div
						class="col-xs-12 col-sm-6"><?php echo " <span class='prefix to_prefix'>" . t("Bis:") . "</span> " . $date_to . " " . $time_to; ?></div>
				</div>
			</div>
			<div class="col-xs-12 description"><?php echo $description ?></div>
			<?php if (strlen($registerfile) > 0) { ?>
				<div
					class="col-xs-12 filelink register"><?php echo " <span class='prefix register_prefix'>" . t("Anmeldung:") . "</span> " . $registerfile ?></div>
			<?php } ?>
			<?php if (strlen($infofile) > 0) { ?>
				<div
					class="col-xs-12 filelink info"><?php echo " <span class='prefix info_prefix'>" . t("Informationen:") . "</span> " . $infofile ?></div>
			<?php } ?>
		</div>
        <div class="row">
                <?php
            if($showAttend || $showApologize) {
                ?>
                <form action="<?php echo $this->action("changeAttendance") ?>" method="post">
                    <div class="col-xs-6">
                    <?php
                        if($showAttend){
                    ?>
                            <button class="btn btn-success btn-block"
                                    type="submit"
                                    name="newstate"
                                    value="<?php echo \Concrete\Package\BaclucEventPackage\Src\UserAttendsEvent::STATE_ATTENDING?>"
                                    aria-label="<?php echo t("enroll");?>" title="<?php echo t("enroll");?>'"
                            ><i class="fa fa-check"></i></button>
                    <?php
                        }
                    ?>
                    </div>
                    <div class="col-xs-6">
                    <?php
                        if($showApologize){
                    ?>
                            <button class="btn btn-danger btn-block"
                                    type="submit"
                                    name="newstate"
                                    value="<?php echo \Concrete\Package\BaclucEventPackage\Src\UserAttendsEvent::STATE_APOLOGIZED?>"
                                    aria-label="<?php echo t("cancel");?>" title="<?php echo t("cancel");?>'"
                            ><i class="fa fa-close"></i></button>
                    <?php
                        }
                    ?>
                    </div>
                </form>
                <?php
            }
                ?>

        </div>


	</div>
	<?php
}else{
	?>
	<div class="bacluc_event bacluc_event_next_event_block">
		<?php echo t("No Events found"); ?>


	</div>
<?php
}
	?>