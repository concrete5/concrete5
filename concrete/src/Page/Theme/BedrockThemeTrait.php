<?php
namespace Concrete\Core\Page\Theme;

/**
 * This is a trait you can add to your theme's PageTheme class if it is built with the Concrete bedrock. That means
 * it includes the bedrock SCSS and JS files. If your theme's JS and CSS files include these starter assets, you'll
 * automatically need to require jQuery and Bootstrap4, and you'll automatically support the bootstrap4 grid system.
 */
trait BedrockThemeTrait
{

    public function registerAssets()
    {
        $this->requireAsset('font-awesome');
        $this->requireAsset('jquery');
        $this->requireAsset('vue');
        $this->requireAsset('bootstrap');
        $this->requireAsset('moment');
    }

    public function getThemeGridFrameworkHandle(): string
    {
        return 'bootstrap5';
    }

}
