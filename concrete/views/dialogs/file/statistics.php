<?php

/**
 * @var Concrete\Controller\Dialog\File\Statistics $controller
 * @var Concrete\Core\View\View $view
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface $resolver
 * @var Concrete\Core\Entity\File\File $file
 * @var array[] $records
 * @var bool $hasMoreRecords
 * @var int $totalRecords
 */
?>
<div id="ccm-file-statistics" v-cloak>
    <template v-if="records.length !== 0">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th><?= t('Date') ?></th>
                    <th><?= t('Version') ?></th>
                    <th><?= t('User') ?></th>
                    <th><?= t('Page') ?></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="record in records" v-bind:key="record.id">
                    <td>{{ record.dt }}</td>
                    <td>{{ record.v }}</td>
                    <td>
                        <i v-if="typeof record.u === 'number'"><?= t('Deleted user (ID: %s)', '{{ record.u }}') ?></i>
                        <template v-else-if="typeof record.u === 'string'">{{ record.u }}</template>
                        <i v-else><?= t('Guest') ?></i>
                    </td>
                    <td>{{ record.p }}</td>
                </tr>
            </tbody>
        </table>
        <div class="my-2">
            <?= t('Showing %1$s records out of %2$s', '{{ records.length }}', $totalRecords) ?>
            <a v-if="canLoadMore" href="#" class="float-end" v-on:click.prevent="loadMoreRecords"><?= t('Load more') ?></a>
        </div>
    </template>
</div>
<form id="ccm-file-statistics-download" target="_blank" method="POST" action="<?= $resolver->resolve(['/ccm/system/dialogs/file/statistics/download', $file->getFileID()]) ?>">
    <?php $token->output("ccm-file-statistics-download-{$file->getFileID()}") ?>
</form>
<div class="dialog-buttons">
    <input
        type="submit"
        form="ccm-file-statistics-download"
        class="btn btn-primary"
        <?= $records === [] ? ' disabled="disabled"' : '' ?>
        value="<?= t('Download') ?>"
    />
</div>

<script>
Concrete.Vue.activateContext('cms', function (Vue, config) {
    new Vue({
        el: '#ccm-file-statistics',
        data: () => {
            return {
                busy: false,
                records: <?= json_encode($records) ?>,
                hasMoreRecords: <?= json_encode($hasMoreRecords) ?>,
            };
        },
        computed: {
            canLoadMore: function() {
                return this.hasMoreRecords && !this.busy;
            },
        },
        methods: {
            loadMoreRecords: function() {
                var my = this;
                if (!my.canLoadMore) {
                    return;
                }
                my.busy = true;
                var lastRecord = my.records[my.records.length - 1];
                $.concreteAjax({
                    url: <?= json_encode((string) $resolver->resolve(['/ccm/system/dialogs/file/statistics/load_more', $file->getFileID()])) ?>,
                    data: {
                        <?= json_encode($token::DEFAULT_TOKEN_NAME) ?>: <?= json_encode($token->generate("ccm-file-statistics-more-{$file->getFileID()}")) ?>,
                        beforeID: lastRecord.id,
                    },
                    success: function(data) {
                        my.records.push.apply(my.records, data.records);
                        my.hasMoreRecords = data.hasMoreRecords;
                    },
                    complete: function() {
                        my.busy = false;
                    },
                });
            },
        },
    });
});
</script>
