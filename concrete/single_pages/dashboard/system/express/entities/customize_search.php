<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Controller\Element\Dashboard\Express\Menu;
use Concrete\Controller\Element\Search\Express\CustomizeResults;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;

/** @var View $view */
/** @var Entity $entity */
/** @var CustomizeResults $customizeElement */

$app = Application::getFacadeApplication();
/** @var Token $token */
$token = $app->make(Token::class);

?>

<div class="ccm-dashboard-header-buttons">

    <?php
    $manage = new Menu($entity);
    /** @noinspection PhpDeprecationInspection */
    $manage->render();
    ?>

</div>

<div class="row">
    <?php /** @noinspection PhpUnhandledExceptionInspection */
    View::element('dashboard/express/detail_navigation', ['entity' => $entity]) ?>

    <div class="col-md-8">
        <form method="post" action="<?php echo $view->action('save', $entity->getID()) ?>">
            <?php echo $token->output('save') ?>
            <?php /** @noinspection PhpDeprecationInspection */
            print $customizeElement->render();  ?>

            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <button class="float-end btn btn-primary" type="submit">
                        <?php echo t('Save') ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
