<?php
namespace Concrete\Core\Page\Search\Field\Field;

use Concrete\Core\File\FileList;
use Concrete\Core\File\Type\Type;
use Concrete\Core\Page\PageList;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\Field\FieldInterface;
use Concrete\Core\Search\ItemList\ItemList;

class ParentPageField extends AbstractField
{

    protected $requestVariables = [
        'cParentIDSearchField', 'cParentAll'
    ];

    public function getKey()
    {
        return 'parent_page';
    }

    public function getDisplayName()
    {
        return t('Parent Page');
    }

    /**
     * @param PageList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        if ($this->data['cParentIDSearchField'] > 0) {
            $pc = \Page::getByID($this->data['cParentIDSearchField']);
            if ($pc && !$pc->isError()) {
                if ($pc->isSystemPage()) {
                    $list->includeSystemPages();
                    $list->includeInactivePages();
                }
                $siteObject = $pc->getSiteTreeObject();
                if (is_object($siteObject)) {
                    $list->setSiteTreeObject($siteObject);
                }
                if ($this->data['cParentAll'] == 1) {
                    $cPath = $pc->getCollectionPath();
                    $list->filterByPath($cPath);
                } else {
                    $list->filterByParentID($this->data['cParentIDSearchField']);
                }
            }
        }
    }

    public function renderSearchField()
    {
        $ps = \Core::make("helper/form/page_selector");
        $form = \Core::make("helper/form");
        $html = $ps->selectPage('cParentIDSearchField', $this->data['cParentIDSearchField'], ['askIncludeSystemPages' => true]);
        $html .= '<div class="form-group mt-3">';
        $html .= '<label class="control-label form-label">' . t('Search All Children?') . '</label>';
        $html .= '<div class="form-check">' . $form->radio('cParentAll', 0, $this->data['cParentAll']) . ' <label class="form-check-label" for="cParentAll1">' . t('No') . '</label></div>';
        $html .= '<div class="form-check">' . $form->radio('cParentAll', 1, $this->data['cParentAll']) . ' <label class="form-check-label" for="cParentAll2">' . t('Yes') . '</label></div>';
        $html .= '</div>';
        return $html;
    }


}
