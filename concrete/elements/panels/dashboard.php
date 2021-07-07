<?php

use Concrete\Core\Application\UserInterface\Dashboard\Navigation\FavoritesNavigationFactory;
use Concrete\Core\Application\UserInterface\Dashboard\Navigation\NavigationFactory;
use Concrete\Core\User\User;

defined('C5_EXECUTE') or die("Access Denied.");
$u = app(User::class);
$ui = UserInfo::getByID($u->getUserID());
$dashboard = new \Concrete\Core\Application\Service\Dashboard();
$account = new \Concrete\Core\User\Account\AccountService();
$currentMode = 'dashboard';

$topLevelMenu = Element::get('dashboard/navigation/panel/top');
$favoritesMenu = Element::get('dashboard/navigation/panel/favorites');
$section = null;
if ($dashboard->inDashboard($c)) {
    $parents = array_reverse(app(\Concrete\Core\Html\Service\Navigation::class)->getTrailToCollection($c));
    if (count($parents) == 1) {
        $section = $c;
    } else {
        $section = $parents[1];
    }
} else if ($account->inMyAccount($c)) {
    $section = \Concrete\Core\Page\Page::getByPath('/dashboard/welcome');
}
if ($section) {
    $sectionMenu = Element::get('dashboard/navigation/panel/section', [$section, $c]);
    $currentMode = 'section';
}

?>
<section data-view="dashboard-panel" v-cloak>

    <div class="ccm-dashboard-panel-top" v-show="currentMode !== 'section'">
        <menu class="ccm-panel-dashboard-favorites-menu" v-show="currentMode != 'section'">
            <li :class="{active: currentMode === 'dashboard'}"><a @click="currentMode = 'dashboard'" href="#"
                                                                id="panel-dashboard-dashboard"><?= t('Dashboard') ?></a></li>
            <li :class="{active: currentMode === 'favorites'}"><a @click="currentMode = 'favorites'" href="#"
                                                                id="panel-dashboard-favorites"><?= t('Favorites') ?></a></li>
        </menu>

        <div v-show="currentMode === 'dashboard'">
            <?php
            $topLevelMenu->render();
            ?>
        </div>

        <div v-show="currentMode === 'favorites'">
            <?php
            $favoritesMenu->render();
            ?>
        </div>
    </div>

    <?php
    if (isset($section)) {
    ?>

        <div class="ccm-dashboard-panel-section">

            <div v-show="currentMode === 'section'">
                <header v-if="currentMode == 'section'">
                    <a href="javascript:void(0)" @click="revealTopNav" class="ccm-panel-back">
                        <svg><use xlink:href="#icon-arrow-left" /></svg>
                        <?= t('Dashboard') ?>
                    </a>

                    <h5><?=h(t($section->getCollectionName()))?></h5>
                </header>

                <?php
                $sectionMenu->render();
                ?>
            </div>
        </div>

    <?php } ?>

    <div class="ccm-panel-dashboard-footer">
        <p><?= t('Logged in as <a href="%s">%s</a>', URL::to('/account'), $ui->getUserDisplayName()); ?>. </p>
        <a href="<?= URL::to('/login', 'do_logout', Loader::helper('validation/token')->generate('do_logout')); ?>"><?= t('Sign Out.'); ?></a>
    </div>

</section>


<script type="text/javascript">

    $(function () {

        Concrete.Vue.activateContext('cms', function (Vue, config) {
            new Vue({
                el: 'section[data-view=dashboard-panel]',
                components: config.components,
                mounted() {
                    if (this.currentMode === 'section') {
                        // A bit of a hack. If the current view is section, that means we've loaded in from a dashboard
                        // page. So we move our top menu out as a hidden DIV so that we can reveal it using revealTopNav
                        // if a user needs to
                        const $topNav = $(this.$el).find('.ccm-dashboard-panel-top');
                        $topNav.insertBefore('#ccm-panel-dashboard div.ccm-panel-content')
                            .addClass('ccm-panel-content ccm-panel-slide-left')
                    }
                },
                methods: {
                    revealTopNav() {
                        var my = this
                        const $sectionNav = $(this.$el).closest('.ccm-panel-content-visible')
                        const $topNav = $sectionNav.prev()
                        $sectionNav
                            .queue(function() {
                                $(this).removeClass('ccm-panel-content-visible')
                                    .addClass('ccm-panel-slide-right')
                                my.currentMode = 'dashboard'
                                $sectionNav.dequeue()
                            })
                            .delay(1)
                            .queue(function() {
                                $topNav.removeClass('ccm-panel-slide-left').addClass('ccm-panel-content-visible')
                                $sectionNav.dequeue()
                            })
                    }
                },
                data: {
                    currentMode: '<?=$currentMode?>'
                }
            })
        })

    })

</script>
