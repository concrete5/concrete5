<?php
namespace Concrete\Core\Page\Search\Field\Field;

use Concrete\Core\File\FileList;
use Concrete\Core\File\Type\Type;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\Field\FieldInterface;
use Concrete\Core\Search\ItemList\ItemList;

class NumberOfChildrenField extends AbstractField
{

    protected $requestVariables = [
        'cChildrenSelect', 'cChildren'
    ];

    public function getKey()
    {
        return 'number_of_children';
    }

    public function getDisplayName()
    {
        return t('# of Children');
    }

    /**
     * @param FileList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $symbol = '=';
        if ($this->data['cChildrenSelect'] == 'gt') {
            $symbol = '>';
        } elseif ($this->data['cChildrenSelect'] == 'lt') {
            $symbol = '<';
        }
        $list->filterByNumberOfChildren($this->data['cChildren'], $symbol);
    }

    public function renderSearchField()
    {
        $html = '<div class="row row-cols-auto g-0 align-items-center"><div class="col-auto"><select name="cChildrenSelect" class="form-select">';
        $html .= '<option value="gt"' . ($this->data['cChildrenSelect'] == 'gt' ? ' selected' : '') . '>' . t('More Than') . '</option>';
        $html .= '<option value="eq"' . ($this->data['cChildrenSelect'] == 'eq' ? ' selected' : '') . '>' . t('Equal To') . '</option>';
        $html .= '<option value="lt"' . ($this->data['cChildrenSelect'] == 'lt' ? ' selected' : '') . '>' . t('Fewer Than') . '</option>';
        $html .= '</select></div>';
        $html .= ' <div class="col-auto"><input type="number" name="cChildren" class="form-control" min="0" value="' . $this->data['cChildren'] . '" /></div></div>';
        return $html;
    }


}
