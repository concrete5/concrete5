<?php
namespace Concrete\Core\Page\Search\Field\Field;

use Concrete\Core\File\FileList;
use Concrete\Core\File\Type\Type;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\Field\FieldInterface;
use Concrete\Core\Search\ItemList\ItemList;

class ThemeField extends AbstractField
{

    protected $requestVariables = [
        'pThemeID'
    ];

    public function getKey()
    {
        return 'theme';
    }

    public function getDisplayName()
    {
        return t('Theme');
    }

    /**
     * @param FileList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $list->filter('pThemeID', $this->data['pThemeID']);
    }

    public function renderSearchField()
    {
        $html = '<select name="pThemeID" class="form-select">';
        $themes = Theme::getList();
        foreach ($themes as $pt) {
            $html .= '<option value="' . $pt->getThemeID() . '" ' . ($pt->getThemeID() == $this->data['pThemeID'] ? ' selected' : '') . '>' . $pt->getThemeName() . '</option>';
        }
        $html .= '</select>';
        return $html;
    }


}
