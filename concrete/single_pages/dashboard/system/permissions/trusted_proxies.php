<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Page\View\PageView $view
 * @var Concrete\Controller\SinglePage\Dashboard\System\Permissions\TrustedProxies $controller
 * @var string[] $trustedIPs
 * @var string[] $trustableHeaders
 * @var string[] $trustedHeaders
 * @var array[] $requestForwardedHeaders
 * @var Concrete\Core\Http\Request $request
 * @var IPLib\Address\AddressInterface|null $currentProxyIP
 */
?>
<form method="post" action="<?= $view->action('save') ?>">
    <?php $token->output('ccm_trusted_proxies_save') ?>

    <?php
    if (count($requestForwardedHeaders) === 0) {
        ?>
        <div class="alert alert-warning">
            <p><?= t('No forwarded header has been detected. This may mean that you are not using a proxy and that all the following options should be empty.') ?></p>
        </div>
        <?php
    }
    ?>

    <div class="form-group">
        <?= $form->label('trustedIPs', t('List of IP address/ranges of your proxy')) ?>
        <?= $form->textarea('trustedIPs', implode("\n", $trustedIPs), ['style' => 'resize:vertical', 'rows' => '10']) ?>
        <div class="text-muted">
            <?= t('Separate IP addresses with spaces or new lines.') ?><br />
            <?= t(
                'Accepted values are single addresses (IPv4 like %1$s, and IPv6 like %2$s) and ranges in subnet format (IPv4 like %3$s, and IPv6 like %4$s).',
                '<code>127.0.0.1</code>',
                '<code>::1</code>',
                '<code>127.0.0.1/24</code>',
                '<code>::1/8</code>'
            ) ?><br />
        </div>
    </div>

    <div class="form-group">
        <?= $form->label('trustedHeaders', t('List of headers that should be trusted')) ?>
        <?php
        foreach ($trustableHeaders as $trustableHeader) {
            ?>
            <div class="form-check">
                <label class="form-check-label">
                    <?= $form->checkbox('trustedHeaders[]', $trustableHeader, in_array($trustableHeader, $trustedHeaders, true)) ?>
                    <code><?= h($trustableHeader) ?></code>
                </label>
            </div>
            <?php
        }
        ?>
    </div>

    <div class="alert alert-info">
        <?php
        if (count($requestForwardedHeaders) > 0) {
            ?>
            <p><?= t('In the current request, the following headers are present (you may want to select them - and only them):')?></p>
            <ul>
                <?php
                foreach ($requestForwardedHeaders as $requestForwardedHeaderName => $requestForwardedHeaderValue) {
                    ?>
                    <li>
                        <?php
                        if (is_string($requestForwardedHeaderValue)) {
                            echo t('%s (value: %s)', '<code>' . h($requestForwardedHeaderName) . '</code>', '<code>' . h($requestForwardedHeaderValue) . '</code>');
                        } else {
                            echo '<code>' . h($requestForwardedHeaderName) . '</code>';
                        }
                        ?>
                    </li>
                    <?php
                }
                ?>
            </ul>
            <br />
            <?php
        }
        ?>
        <p><?= t('With the currently configured IPs and headers, PHP detected these values:') ?></p>
        <dl class="dl-horizontal">
            <dt><?= t('Protocol') ?></dt>
            <dd><?= h($request->getScheme()) ?></dd>
            <dt><?= t('Protocol version') ?></dt>
            <dd><?= h($request->getProtocolVersion()) ?></dd>
            <dt><?= t('Host') ?></dt>
            <dd><?= h($request->getHost()) ?></dd>
            <dt><?= t('Port') ?></dt>
            <dd><?= h($request->getPort()) ?></dd>
            <dt><?= t('Client IP') ?></dt>
            <dd><?= h($request->getClientIp()) ?></dd>
            <dt><?= t('Using Proxy') ?></dt>
            <dd><?= $currentProxyIP === null ? t('No') : t('Yes') ?></dd>
            <?php
            if ($currentProxyIP !== null) {
                ?>
                <dt><?= t('Proxy IP') ?></dt>
                <dd><?= h((string) $currentProxyIP) ?></dd>
                <?php
            }
            ?>
        </dl>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="float-end btn btn-primary" type="submit"><?=t('Save')?></button>
        </div>
    </div>

</form>
