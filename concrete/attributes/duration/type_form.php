<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;

/** @var array $unitTypes */
/** @var string $unitType */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);

?>

<fieldset>
    <legend>
        <?php echo t('Duration Options') ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label("unitType", t("Unit Type")); ?>
        <?php echo $form->select("unitType", $unitTypes, $unitType); ?>
    </div>
</fieldset>
