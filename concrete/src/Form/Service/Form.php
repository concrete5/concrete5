<?php
namespace Concrete\Core\Form\Service;

use Concrete\Core\Application\Application;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Localization\Service\CountryList;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\Utility\Service\Arrays as ArraysService;
use Concrete\Core\Utility\Service\Text as TextService;

/**
 * Helpful functions for working with forms. Includes HTML input tags and the like.
 *
 * \@package Helpers
 *
 * @category Concrete
 *
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
class Form
{
    /**
     * Internal counter used to generate unique IDs for radio inputs with the same name.
     *
     * @var int
     */
    protected $radioIndex = 1;

    /**
     * Internal counter used to generate unique IDs for select inputs with the same name.
     *
     * @var int
     */
    protected $selectIndex = 1;

    /**
     * The Application instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * The text service instance.
     *
     * @var TextService
     */
    protected $th;

    /**
     * Arrays helper instance.
     *
     * @var ArraysService
     */
    protected $ah;

    /**
     * The Request instance.
     *
     * @var \Concrete\Core\Http\Request|null
     */
    private $request;

    /**
     * Initialize the instance.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->th = $this->app->make(TextService::class);
        $this->ah = $this->app->make(ArraysService::class);
    }

    /**
     * Set the request instance.
     *
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return \Concrete\Core\Http\Request
     */
    protected function getRequest()
    {
        if ($this->request == null) {
            $this->request = $this->app->make(Request::class);
        }

        return $this->request;
    }

    /**
     * Returns an action suitable for including in a form action property.
     *
     * @param string $action
     * @param string $task
     *
     * @return \League\URL\URLInterface
     */
    public function action($action, $task = null)
    {
        return $this->app->make(ResolverManagerInterface::class)->resolve(func_get_args());
    }

    /**
     * Creates a submit button.
     *
     * @param string $key the name/id of the element
     * @param string $value The value of the element
     * @param array $miscFields additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     * @param string $additionalClasses list of additional space-separated CSS class names
     *
     * @return string
     */
    public function submit($key, $value, $miscFields = [], $additionalClasses = '')
    {
        $nameAndID = $this->buildNameAndID($key, $miscFields);
        return '<input type="submit"' . $this->serializeMiscFields('btn ccm-input-submit ' . $additionalClasses, $miscFields) . $nameAndID . ' value="' . $value . '" />';
    }

    /**
     * Creates a button.
     *
     * @param string $key the name/id of the element
     * @param string $value The value of the element
     * @param array $miscFields additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     * @param string $additionalClasses list of additional space-separated CSS class names
     *
     * @return string
     */
    public function button($key, $value, $miscFields = [], $additionalClasses = '')
    {
        $nameAndID = $this->buildNameAndID($key, $miscFields);
        return '<input type="button"' . $this->serializeMiscFields('btn ccm-input-button ' . $additionalClasses, $miscFields) . $nameAndID . ' value="' . $value . '" />';
    }

    /**
     * Creates a label tag.
     *
     * @param string $forFieldID the id of the associated element
     * @param string $innerHTML the inner html of the label
     * @param array $miscFields additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     *
     * @return string
     */
    public function label($forFieldID, $innerHTML, $miscFields = [])
    {
        $result = '<label';
        if ((string) $forFieldID !== '') {
            $result .= ' for="' . $forFieldID . '"';
        }

        return $result . $this->serializeMiscFields('form-label', $miscFields, []) . '>' . $innerHTML . '</label>';
    }

    /**
     * Creates a file input element.
     *
     * @param string $key the name/id of the element
     * @param array $miscFields additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     *
     * @return string
     */
    public function file($key, $miscFields = [])
    {
        $nameAndID = $this->buildNameAndID($key, $miscFields);
        return '<input type="file"' . $nameAndID . ' value=""' . $this->serializeMiscFields('form-control', $miscFields) . ' />';
    }

    /**
     * Creates a hidden form field.
     *
     * @param string $key the name/id of the element
     * @param string $value the value of the element (overriden if we received some data in POST or GET)
     * @param array $miscFields additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     *
     * @return string
     */
    public function hidden($key, $value = null, $miscFields = [])
    {
        $name = (string) ($miscFields['name'] ?? $key);
        if ($name !== '') {
            $requestValue = $this->getRequestValue($name);
            if ($requestValue !== false && !is_array($requestValue)) {
                $value = $requestValue;
            }
        }
        $nameAndID = $this->buildNameAndID($key, $miscFields);

        return '<input type="hidden"' . $nameAndID . $this->serializeMiscFields('', $miscFields) . ' value="' . $value . '" />';
    }

    /**
     * Generates a checkbox.
     *
     * @param string $key The name/id of the element. It should end with '[]' if it's to return an array on submit.
     * @param string $value String value sent to server, if checkbox is checked, on submit
     * @param string $isChecked "Checked" value (subject to be overridden by $_REQUEST). Checkbox is checked if value is true (string). Note that 'false' (string) evaluates to true (boolean)!
     * @param array $miscFields additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     *
     * @return string
     */
    public function checkbox($key, $value, $isChecked = false, $miscFields = [])
    {
        $name = (string) ($miscFields['name'] ?? $key);
        $_field = $name;
        if (substr($_field, -2) == '[]') {
            $_field = substr($_field, 0, -2);
        }
        if (!array_key_exists('id', $miscFields) && substr($key, -2) === '[]') {
            $miscFields['id'] = substr($key, 0, -2) . '_' . $value;
        }
        $nameAndID = $this->buildNameAndID($key, $miscFields);
        $checked = false;
        if ($isChecked && $this->getRequest()->get($_field) === null && $this->getRequest()->getMethod() !== 'POST') {
            $checked = true;
        } else {
            $requestValue = $this->getRequestValue($name);
            if ($requestValue !== false) {
                if (is_array($requestValue)) {
                    if (in_array($value, $requestValue)) {
                        $checked = true;
                    }
                } elseif ($requestValue == $value) {
                    $checked = true;
                }
            }
        }
        $checked = $checked ? ' checked="checked"' : '';

        return '<input type="checkbox"' . $nameAndID . $this->serializeMiscFields('form-check-input', $miscFields) . ' value="' . $value . '"' . $checked . ' />';
    }

    /**
     * Creates a textarea field.
     *
     * @param string $key the name/id of the element
     * @param string|array $valueOrMiscFields the value of the element or an array with additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     * @param array $miscFields (used if $valueOrMiscFields is not an array) Additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     *
     * @return string
     */
    public function textarea($key, $valueOrMiscFields = '', $miscFields = [])
    {
        if (is_array($valueOrMiscFields)) {
            $value = '';
            $miscFields = $valueOrMiscFields;
        } else {
            $value = $valueOrMiscFields;
        }
        $requestValue = $this->getRequestValue($miscFields['name'] ?? $key);
        if (is_string($requestValue)) {
            $value = $requestValue;
        }
        $nameAndID = $this->buildNameAndID($key, $miscFields);

        return '<textarea' . $nameAndID . $this->serializeMiscFields('form-control', $miscFields) . '>' . $value . '</textarea>';
    }

    /**
     * Generates a radio button.
     *
     * @param string $key the name of the element (its id will start with $key but will have a progressive unique number added)
     * @param string $value the value of the radio button
     * @param string|array $checkedValueOrMiscFields the value of the element (if it should be initially checked) or an array with additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     * @param array $miscFields (used if $checkedValueOrMiscFields is not an array) Additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     *
     * @return string
     */
    public function radio($key, $value, $checkedValueOrMiscFields = '', $miscFields = [])
    {
        if (is_array($checkedValueOrMiscFields)) {
            $checkedValue = '';
            $miscFields = $checkedValueOrMiscFields;
        } else {
            $checkedValue = $checkedValueOrMiscFields;
        }
        $checked = false;

        $requestValue = $this->getRequestValue($miscFields['name'] ?? $key);

        if ($requestValue !== false) {
            if ($requestValue == $value) {
                $checked = true;
            }
        } else {
            if ($checkedValue == $value) {
                $checked = true;
            }
        }
        if (!array_key_exists('id', $miscFields)) {
            $miscFields['id'] = $key . $this->radioIndex;
            ++$this->radioIndex;
        }
        $nameAndID = $this->buildNameAndID($key, $miscFields);

        $str = '<input type="radio"' . $nameAndID . ' value="' . $value . '"' . $this->serializeMiscFields('form-check-input', $miscFields);
        if ($checked) {
            $str .= ' checked="checked"';
        }
        $str .= ' />';

        return $str;
    }

    /**
     * Checks the request (first POST then GET) based on the key passed.
     * If $key denotes an array (eg akID[34]['value']) we'll turn the key into arrays if the key has text versions of [ and ] in it
     * If the result is a string, it'll be escaped (with htmlspecialchars).
     *
     * @param string $key the name of the field to be checked
     * @param string $type 'post' to check in POST data, other values to check in GET data
     *
     * @return false|array|string returns an array if $key denotes an array and we received that data, a string if $key is the name of a received data, false if $key is not found in the received data
     */
    public function getRequestValue($key)
    {
        $result = $this->processRequestValue($key, 'post');
        if ($result === false) {
            $result = $this->processRequestValue($key, 'get');
        }

        return $result;
    }

    /**
     * Renders a text input field.
     *
     * @param string $key the name/id of the element
     * @param string|array $valueOrMiscFields the value of the element or an array with additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     * @param array $miscFields (used if $valueOrMiscFields is not an array) Additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     *
     * @return string
     */
    public function text($key, $valueOrMiscFields = '', $miscFields = [])
    {
        return $this->inputType($key, 'text', $valueOrMiscFields, $miscFields);
    }

    /**
     * Renders a number input field.
     *
     * @param string $key the name/id of the element
     * @param string|array $valueOrMiscFields the value of the element or an array with additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     * @param array $miscFields (used if $valueOrMiscFields is not an array) Additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     *
     * @return string
     */
    public function number($key, $valueOrMiscFields = '', $miscFields = [])
    {
        return $this->inputType($key, 'number', $valueOrMiscFields, $miscFields);
    }

    /**
     * Renders an email input field.
     *
     * @param string $key the name/id of the element
     * @param string|array $valueOrMiscFields the value of the element or an array with additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     * @param array $miscFields (used if $valueOrMiscFields is not an array) Additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     *
     * @return string
     */
    public function email($key, $valueOrMiscFields = '', $miscFields = [])
    {
        return $this->inputType($key, 'email', $valueOrMiscFields, $miscFields);
    }

    /**
     * Renders a telephone input field.
     *
     * @param string $key the name/id of the element
     * @param string|array $valueOrMiscFields the value of the element or an array with additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     * @param array $miscFields (used if $valueOrMiscFields is not an array) Additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     *
     * @return string
     */
    public function telephone($key, $valueOrMiscFields = '', $miscFields = [])
    {
        return $this->inputType($key, 'tel', $valueOrMiscFields, $miscFields);
    }

    /**
     * Renders a URL input field.
     *
     * @param string $key the name/id of the element
     * @param string|array $valueOrMiscFields the value of the element or an array with additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     * @param array $miscFields (used if $valueOrMiscFields is not an array) Additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     *
     * @return string
     */
    public function url($key, $valueOrMiscFields = '', $miscFields = [])
    {
        return $this->inputType($key, 'url', $valueOrMiscFields, $miscFields);
    }

    /**
     * Renders a search input field.
     *
     * @param string $key the name/id of the element
     * @param string|array $valueOrMiscFields the value of the element or an array with additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     * @param array $miscFields (used if $valueOrMiscFields is not an array) Additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     *
     * @return string
     */
    public function search($key, $valueOrMiscFields = '', $miscFields = [])
    {
        return $this->inputType($key, 'search', $valueOrMiscFields, $miscFields);
    }

     /**
     * Renders any previously unspecified input field type. Allows for adaptive update to any new HTML input types
     * that are not covered by explicit methods. Browsers will either handle the specific input type or fallback
     * to a text input.
     *
     * @param string $key the name/id of the element
     * @param string|array $valueOrMiscFields the value of the element or an array with additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     * @param array $miscFields (used if $valueOrMiscFields is not an array) Additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     *
     * @return string
     */
    public function __call($name, $args)
    {
        $key = $args[0];
        $valueOrMiscFields = $args[1];
        $miscFields = array_slice($args,2);
        return $this->inputType($key, $name, $valueOrMiscFields, $miscFields);
    }
    
    
    /**
     * Renders a select field.
     *
     * @param string $key The name of the element. If $key denotes an array, the ID will start with $key but will have a progressive unique number added; if $key does not denotes an array, the ID attribute will be $key.
     * @param array $optionValues an associative array of key => display
     * @param string|array $valueOrMiscFields the value of the field to be selected or an array with additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     * @param array $miscFields (used if $valueOrMiscFields is not an array) Additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     *
     * @return $html
     */
    public function select($key, $optionValues, $valueOrMiscFields = '', $miscFields = [])
    {
        if (!is_array($optionValues)) {
            $optionValues = [];
        }
        if (is_array($valueOrMiscFields)) {
            $selectedValue = '';
            $miscFields = $valueOrMiscFields;
        } else {
            $selectedValue = (string) $valueOrMiscFields;
        }
        if ($selectedValue !== '') {
            $miscFields['ccm-passed-value'] = h($selectedValue);
        }
        $name = (string) ($miscFields['name'] ?? $key);
        if ($name !== '') {
            $requestValue = $this->getRequestValue($name);
            if (is_array($requestValue) && isset($requestValue[0]) && is_string($requestValue[0])) {
                $selectedValue = (string) $requestValue[0];
            } elseif ($requestValue !== false) {
                if (!is_array($requestValue)) {
                    $selectedValue = (string) $requestValue;
                } else {
                    $selectedValue = '';
                }
            }
        }
        if (!array_key_exists('id', $miscFields) && substr($key, -2) === '[]') {
            $miscFields['id'] = substr($key, 0, -2) . $this->selectIndex;
            $this->selectIndex++;
        }
        $nameAndID = $this->buildNameAndID($key, $miscFields);
        $str = '<select' . $nameAndID . $this->serializeMiscFields('form-select', $miscFields) . '>';
        foreach ($optionValues as $k => $text) {
            if (is_array($text)) {
                $str .= '<optgroup label="' . h($k) . '">';
                foreach ($text as $optionValue => $optionText) {
                    $str .= '<option value="' . h($optionValue) . '"';
                    if ((string) $optionValue === (string) $selectedValue) {
                        $str .= ' selected="selected"';
                    }
                    $str .= '>' . h($optionText) . '</option>';
                }
                $str .= '</optgroup>';
            } else {
                $str .= '<option value="' . h($k) . '"';
                if ((string) $k === (string) $selectedValue) {
                    $str .= ' selected="selected"';
                }
                $str .= '>' . h($text) . '</option>';
            }
        }
        $str .= '</select>';

        return $str;
    }

    /**
     * Renders a select menu to choose a Country.
     *
     * @param string $key The name of the element. If $key denotes an array, the ID will start with $key but will have a progressive unique number added; if $key does not denotes an array, the ID attribute will be $key.
     * @param string $selectedCountryCode the code of the Country to be initially selected
     * @param array $configuration Configuration options. Supported keys are:
     * - 'noCountryText': the text to be displayed when no country is selected
     * - 'required': do users must choose a Country?
     * - 'allowedCountries': an array containing a list of acceptable Country codes. If not set, all the countries will be selectable.
     * - 'linkStateProvinceField': set to true to look for text fields that have a "data-countryfield" attribute with the same value as this Country field name (updating the Country select will automatically update the State/Province list).
     * @param array $miscFields Additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     */
    public function selectCountry($key, $selectedCountryCode = '', array $configuration = [], array $miscFields = [])
    {
        $configuration += [
            'noCountryText' => '',
            'required' => false,
            'allowedCountries' => null,
            'linkStateProvinceField' => false,
            'hideUnusedStateProvinceField' => false,
            'clearStateProvinceOnChange' => false,
        ];
        $allCountries = $this->app->make(CountryList::class)->getCountries();
        if (is_array($configuration['allowedCountries'])) {
            $allCountries = array_intersect_key($allCountries, array_flip($configuration['allowedCountries']));
        }
        // Fix the selected Country code specified in the code
        if ($configuration['required'] && count($allCountries) === 1) {
            $selectedCountryCode = key($allCountries);
        } else {
            $selectedCountryCode = (string) $selectedCountryCode;
            if ($selectedCountryCode !== '' && !isset($allCountries[$selectedCountryCode])) {
                $selectedCountryCode = '';
            }
        }
        // Fix the Country code received via the current request
        $name = (string) ($miscFields['name'] ?? $key);
        if ($name !== '') {
            $requestValue = $this->getRequestValue($name);
            if (is_array($requestValue)) {
                $requestValue = (string) $requestValue[0];
            } elseif ($requestValue !== false && !is_string($requestValue)) {
                $requestValue = '';
            }
            if ($requestValue !== false && !isset($allCountries[$requestValue])) {
                $requestValue = '';
            }
        } else {
            $requestValue = false;
        }

        if ($requestValue !== false) {
            $selectedOption = $requestValue;
        } else {
            $selectedOption = $selectedCountryCode;
        }
        if (!array_key_exists('id', $miscFields) && substr($key, -2) === '[]') {
            $miscFields['id'] = substr($key, 0, -2) . $this->selectIndex;
            ++$this->selectIndex;
        }
        if ($selectedCountryCode === '' || !$configuration['required']) {
            $optionValues = ['' => (string) $configuration['noCountryText']];
        } else {
            $optionValues = [];
        }
        $optionValues += $allCountries;
        if ($selectedCountryCode !== '') {
            $miscFields['ccm-passed-value'] = $selectedCountryCode;
        }
        $nameAndID = $this->buildNameAndID($key, $miscFields);
        $str = '<select' . $nameAndID . $this->serializeMiscFields('form-control', $miscFields) . '>';
        foreach ($optionValues as $k => $text) {
            $str .= '<option value="' . h($k) . '"';
            if ((string) $k === (string) $selectedOption) {
                $str .= ' selected="selected"';
            }
            $str .= '>' . h($text) . '</option>';
        }
        $str .= '</select>';
        if ($configuration['linkStateProvinceField']) {
            $escapedID = preg_replace('/[!"#$%&\'()*+,.\\/:;<=>?@\\[\\]^`{|}~\\\\]/', '\\\\$0', $miscFields['id'] ?? $key);
            $config = [
                'hideUnusedStateProvinceField' => (bool) $configuration['hideUnusedStateProvinceField'],
                'clearStateProvinceOnChange' => (bool) $configuration['clearStateProvinceOnChange'],
            ];

            $str .= '<script>$(document).ready(function() {';
            $str .= 'ConcreteCountryDataLink.withCountryField(';
            $str .= '$(' . json_encode('#' . $escapedID) . ')';
            $str .= ', ' . json_encode($config);
            $str .= ');';
            $str .= '});</script>';
        }

        return $str;
    }

    /**
     * Renders a multiple select box.
     *
     * @param string $key The ID of the element. The name attribute will be $key followed by '[].
     * @param array $optionValues Hash array with name/value as the select's option value/text
     * @param array|string $defaultValues Default value(s) which match with the option values; overridden by $_REQUEST
     * @param array $miscFields additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     *
     * @return $html
     */
    public function selectMultiple($key, $optionValues, $defaultValues = false, $miscFields = [])
    {
        if (array_key_exists('name', $miscFields)) {
            $name = (string) $miscFields['name'];
            if ($name !== '' && substr($name, -2) === '[]') {
                $name = substr($name, 0, -2);
            }
        } else {
            $name = $key;
            $miscFields['name'] = $name . '[]';
        }
        $selectedValues = $defaultValues;
        if ($name !== '') {
            $requestValue = $this->getRequestValue($name . '[]');
            if ($requestValue !== false) {
                $selectedValues = $requestValue;
            }
        }
        if (!is_array($selectedValues)) {
            if ($selectedValues !== null && $selectedValues !== false) {
                $selectedValues = (array) $selectedValues;
            } else {
                $selectedValues = [];
            }
        }
        if (!is_array($optionValues)) {
            $optionValues = [];
        }
        $nameAndID = $this->buildNameAndID($key, $miscFields);
        $str = "<select{$nameAndID} multiple=\"multiple\"" . $this->serializeMiscFields('form-control', $miscFields) . '>';
        foreach ($optionValues as $k => $text) {
            if (is_array($text)) {
                if (count($text) > 0) {
                    $str .= '<optgroup label="' . h($k) . '">';
                    foreach ($text as $k1 => $text1) {
                        $str .= '<option value="' . h($k1) . '"';
                        if (in_array($k1, $selectedValues)) {
                            $str .= ' selected="selected"';
                        }
                        $str .= '>' . h($text1) . '</option>';
                    }
                    $str .= '</optgroup>';
                }
            } else {
                $str .= '<option value="' . h($k) . '"';
                if (in_array($k, $selectedValues)) {
                    $str .= ' selected="selected"';
                }
                $str .= '>' . h($text) . '</option>';
            }
        }
        $str .= '</select>';

        return $str;
    }

    /**
     * Renders a password input field.
     *
     * @param string $key the name/id of the element
     * @param string|array $valueOrMiscFields the value of the element or an array with additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     * @param array $miscFields (used if $valueOrMiscFields is not an array) Additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     *
     * @return string
     */
    public function password($key, $valueOrMiscFields = '', $miscFields = [])
    {
        return $this->inputType($key, 'password', $valueOrMiscFields, array_merge(["autocomplete" => "off"], $miscFields));
    }

    /**
     * Generates HTML code that can be added at the beginning of a form to disable username/password autocompletion.
     *
     * @return string
     */
    public function getAutocompletionDisabler()
    {
        $id = str_replace('.', '_', uniqid('ccm_form_autocompletiondisabler_', true));
        $result = <<<EOT
<div id="{$id}" style="position: absolute; top: -1000px; opacity: 0">
    <input type="text" id="{$id}_username" tabindex="-1" />
    <input type="password" id="{$id}_password" autocomplete="off" tabindex="-1" />
    <script>
    (function() {
        function removeFake() {
            setTimeout(
                function() {
                    var div = document.getElementById('{$id}');
                    div.parentNode.removeChild(div);
                },
                500
            );
        }
        if (window.addEventListener) {
            window.addEventListener('load', removeFake, false);
        } else if (window.attachEvent) {
            window.attachEvent('onload', removeFake);
        }
    })();
    </script>
</div>
EOT;

        return $result;
    }

    /**
     * Checks the request based on the key passed.
     * If $key denotes an array (eg akID[34]['value']) we'll turn the key into arrays if the key has text versions of [ and ] in it
     * If the result is a string, it'll be escaped (with htmlspecialchars).
     *
     * @param string $key the name of the field to be checked
     * @param string $type 'post' to check in POST data, other values to check in GET data
     *
     * @return false|array|string returns an array if $key denotes an array and we received that data, a string if $key is the name of a received data, false if $key is not found in the received data
     */
    protected function processRequestValue($key, $type = 'post')
    {
        $bag = $type == 'post' ? $this->getRequest()->request : $this->getRequest()->query;
        if (strpos($key, '[') !== false) {
            $key = str_replace(']', '', $key);
            $key = explode('[', trim($key, '['));
            $v2 = $this->ah->get($bag->all(), $key);
            if ($v2 !== null) {
                if (is_string($v2)) {
                    return $this->th->specialchars($v2);
                } else {
                    return $v2;
                }
            }
        } elseif ($bag->has($key) && is_string($s = $bag->get($key))) {
            return $this->th->specialchars($s);
        }

        return false;
    }

    /**
     * Internal function that creates an <input> element of type $type. Handles the messiness of evaluating $valueOrMiscFields. Assigns a default class of ccm-input-$type.
     *
     * @param string $key the name/id of the element
     * @param string $type Accepted value for HTML attribute "type"
     * @param string|array $valueOrMiscFields the value of the element or an array with additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     * @param array $miscFields (used if $valueOrMiscFields is not an array) Additional fields appended to the element (a hash array of attributes name => value), possibly including 'class', 'id', and 'name'
     *
     * @return string
     */
    protected function inputType($key, $type, $valueOrMiscFields, $miscFields)
    {
        if (is_array($valueOrMiscFields)) {
            $value = '';
            $miscFields = $valueOrMiscFields;
        } else {
            $value = $valueOrMiscFields;
        }
        $name = (string) ($miscFields['name'] ?? $key);
        $requestValue = $this->getRequestValue($name);
        if (is_string($requestValue)) {
            $value = $requestValue;
        }
        $value = h($value);
        $nameAndID = $this->buildNameAndID($key, $miscFields);

        return "<input type=\"$type\"{$nameAndID} value=\"$value\"" . $this->serializeMiscFields("form-control ccm-input-$type", $miscFields) . ' />';
    }

    /**
     * @deprecated Use serializeMiscFields
     *
     * @param string $defaultClass 
     * @param array $attributes 
     *
     * @return string
     */
    protected function parseMiscFields($defaultClass, $attributes)
    {
        return $this->serializeMiscFields((string) $defaultClass, (array) $attributes, []);
    }
    
    /**
     * @param string $defaultClass Default CSS class name
     * @param array $attributes a key/value array of attributes (name => value), possibly including 'class'
     * @param array $skipFields names of fields not to be serialized
     *
     * @return string
     */
    protected function serializeMiscFields($defaultClass, $attributes, array $skipFields = ['name', 'id']): string
    {
        $attributes = (array) $attributes;
        $defaultClass = trim((string) $defaultClass);
        if ($defaultClass !== '') {
            $attributes['class'] = trim(($attributes['class'] ?? '') . ' ' . $defaultClass);
        }
        $attr = '';
        foreach ($attributes as $k => $v) {
            if (!in_array($k, $skipFields, true)) {
                $attr .= " $k=\"$v\"";
            }
        }
        
        return $attr;
        
    }

    /**
     * Extract the value of the name and the id attributes.
     * @param string $key to be used if $miscFields does not contain the 'name' or the 'id' attribute. 
     * @param array $miscFields an array with the element attributes (key/value pair).
     *
     * @return array first element is the value of the name attributes, second element is the value of the id attribute.
     */
    protected function extractNameAndID(string $key, array $miscFields): array
    {
        return [
            array_key_exists('name', $miscFields) ? (string) $miscFields['name'] : $key,
            array_key_exists('id', $miscFields) ? (string) $miscFields['id'] : $key,
        ];
    }

    /**
     * Generate the HTML code containing the name and the id attributes (if any).
     *
     * @param string $key to be used if $miscFields does not contain the 'id' or the 'name' attribute.
     * @param array $miscFields an array with the element attributes (key/value pair).
     *
     * @return string If not empty, the result will start with a space
     */
    protected function buildNameAndID(string $key, array $miscFields): string
    {
        $chunks = [];
        [$name, $id] = $this->extractNameAndID($key, $miscFields);
        if ($id !== '') {
            $chunks[] = 'id="' . h($id) . '"';
        }
        if ($name !== '') {
            $chunks[] = 'name="' . h($name) . '"';
        }
        if ($chunks === []) {
            return '';
        }
        
        return ' ' . implode(' ', $chunks);
    }
}
