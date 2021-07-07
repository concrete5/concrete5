<?php

namespace Concrete\Tests\Form\Service;

use Concrete\Core\Http\Request;
use Core;
use Concrete\Tests\TestCase;

class FormTest extends TestCase
{
    /**
     * @var \Concrete\Core\Form\Service\Form
     */
    protected static $formHelper;

    /**
     * @var \Concrete\Core\Http\Request
     */
    protected static $request;

    public static function setUpBeforeClass()
    {
        self::$request = new Request();
        self::$formHelper = Core::make('helper/form', ['request' => self::$request]);
        self::$formHelper->setRequest(self::$request);
    }

    public function providerTestCreateElements()
    {
        return [
            // submit
            [
                'submit',
                ['Key', 'Value'],
                '<input type="submit" class="btn ccm-input-submit" id="Key" name="Key" value="Value" />',
            ],
            [
                'submit',
                ['Key[]', 'Value'],
                '<input type="submit" class="btn ccm-input-submit" id="Key[]" name="Key[]" value="Value" />',
            ],
            [
                'submit',
                ['Key', 'Value', ['class' => 'MY-CLASS']],
                '<input type="submit" class="MY-CLASS btn ccm-input-submit" id="Key" name="Key" value="Value" />',
            ],
            [
                'submit',
                ['Key', 'Value', [], 'MY-CLASS'],
                '<input type="submit" class="btn ccm-input-submit MY-CLASS" id="Key" name="Key" value="Value" />',
            ],
            [
                'submit',
                ['Key', 'Value', ['id' => ''], 'MY-CLASS'],
                '<input type="submit" class="btn ccm-input-submit MY-CLASS" name="Key" value="Value" />',
            ],
            [
                'submit',
                ['Key', 'Value', ['name' => ''], 'MY-CLASS'],
                '<input type="submit" class="btn ccm-input-submit MY-CLASS" id="Key" value="Value" />',
            ],
            [
                'submit',
                ['Key', 'Value', ['id' => 'override'], 'MY-CLASS'],
                '<input type="submit" class="btn ccm-input-submit MY-CLASS" id="override" name="Key" value="Value" />',
            ],
            [
                'submit',
                ['Key', 'Value', ['id' => 'override1', 'name' => 'override2'], 'MY-CLASS'],
                '<input type="submit" class="btn ccm-input-submit MY-CLASS" id="override1" name="override2" value="Value" />',
            ],
            // button
            [
                'button',
                ['Key', 'Value'],
                '<input type="button" class="btn ccm-input-button" id="Key" name="Key" value="Value" />',
            ],
            [
                'button',
                ['Key[]', 'Value'],
                '<input type="button" class="btn ccm-input-button" id="Key[]" name="Key[]" value="Value" />',
            ],
            [
                'button',
                ['Key', 'Value', ['class' => 'MY-CLASS']],
                '<input type="button" class="MY-CLASS btn ccm-input-button" id="Key" name="Key" value="Value" />',
            ],
            [
                'button',
                ['Key', 'Value', [], 'MY-CLASS'],
                '<input type="button" class="btn ccm-input-button MY-CLASS" id="Key" name="Key" value="Value" />',
            ],
            [
                'button',
                ['Key', 'Value', ['id' => ''], 'MY-CLASS'],
                '<input type="button" class="btn ccm-input-button MY-CLASS" name="Key" value="Value" />',
            ],
            [
                'button',
                ['Key', 'Value', ['id' => 'override1', 'name' => 'override2'], 'MY-CLASS'],
                '<input type="button" class="btn ccm-input-button MY-CLASS" id="override1" name="override2" value="Value" />',
            ],
            // label
            [
                'label',
                ['ForKey', '<b>label</b>'],
                '<label for="ForKey"><b>label</b></label>',
            ],
            [
                'label',
                ['ForKey[]', 'text'],
                '<label for="ForKey[]">text</label>',
            ],
            [
                'label',
                ['ForKey', 'text', []],
                '<label for="ForKey">text</label>',
            ],
            [
                'label',
                ['ForKey', 'text', ['class' => 'MY-CLASS']],
                '<label for="ForKey" class="MY-CLASS">text</label>',
            ],
            [
                'label',
                ['ForKey', 'text', ['class' => 'MY-CLASS', 'id' => 'my-id']],
                '<label for="ForKey" class="MY-CLASS" id="my-id">text</label>',
            ],
            // file
            [
                'file',
                ['Key'],
                '<input type="file" id="Key" name="Key" value="" class="form-control" />',
            ],
            [
                'file',
                ['Key[]'],
                '<input type="file" id="Key[]" name="Key[]" value="" class="form-control" />',
            ],
            [
                'file',
                ['Key', []],
                '<input type="file" id="Key" name="Key" value="" class="form-control" />',
            ],
            [
                'file',
                ['Key', ['class' => 'MY-CLASS']],
                '<input type="file" id="Key" name="Key" value="" class="MY-CLASS form-control" />',
            ],
            [
                'file',
                ['Key[]', ['id' => '']],
                '<input type="file" name="Key[]" value="" class="form-control" />',
            ],
            [
                'file',
                ['Key[]', ['id' => 'override']],
                '<input type="file" id="override" name="Key[]" value="" class="form-control" />',
            ],
            // hidden
            [
                'hidden',
                ['Key'],
                '<input type="hidden" id="Key" name="Key" value="" />',
            ],
            [
                'hidden',
                ['Key', null],
                '<input type="hidden" id="Key" name="Key" value="" />',
            ],
            [
                'hidden',
                ['Key', null, ['class' => 'MY-CLASS']],
                '<input type="hidden" id="Key" name="Key" class="MY-CLASS" value="" />',
            ],
            [
                'hidden',
                ['Key', ''],
                '<input type="hidden" id="Key" name="Key" value="" />',
            ],
            [
                'hidden',
                ['Key', '', ['class' => 'MY-CLASS']],
                '<input type="hidden" id="Key" name="Key" class="MY-CLASS" value="" />',
            ],
            [
                'hidden',
                ['Key', 'Field value'],
                '<input type="hidden" id="Key" name="Key" value="Field value" />',
            ],
            [
                'hidden',
                ['Key', 'Field value', ['class' => 'MY-CLASS', 'data-my-data' => 'My value']],
                '<input type="hidden" id="Key" name="Key" class="MY-CLASS" data-my-data="My value" value="Field value" />',
            ],
            [
                'hidden',
                ['Key', false],
                '<input type="hidden" id="Key" name="Key" value="" />',
            ],
            [
                'hidden',
                ['Key', 1],
                '<input type="hidden" id="Key" name="Key" value="1" />',
            ],
            [
                'hidden',
                ['Key', 'Original value'],
                '<input type="hidden" id="Key" name="Key" value="Original value" />',
            ],
            [
                'hidden',
                ['Key', 'Original value'],
                '<input type="hidden" id="Key" name="Key" value="" />',
                ['Key' => ''],
            ],
            [
                'hidden',
                ['Key[]'],
                '<input type="hidden" id="Key[]" name="Key[]" value="" />',
            ],
            [
                'hidden',
                ['Key', 'Original value'],
                '<input type="hidden" id="Key" name="Key" value="Received value" />',
                ['Key' => 'Received value'],
            ],
            [
                'hidden',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<input type="hidden" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Original value" />',
                ['Key' => 'Received value'],
            ],
            [
                'hidden',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<input type="hidden" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Original value" />',
                ['Key' => ['subkey1' => 'Received value']],
            ],
            [
                'hidden',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<input type="hidden" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Received value" />',
                ['Key' => ['subkey1' => ['subkey2' => 'Received value']]],
            ],
            [
                'hidden',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<input type="hidden" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Original value" />',
                ['Key' => ['subkey1' => ['subkey2' => ['subkey3' => 'Received value']]]],
            ],
            [
                'hidden',
                ['Key[subkey1][subkey2]', 'Original value', ['id' => '']],
                '<input type="hidden" name="Key[subkey1][subkey2]" value="Received value" />',
                ['Key' => ['subkey1' => ['subkey2' => 'Received value']]],
            ],
            [
                'hidden',
                ['Key[subkey1][subkey2]', 'Original value', ['name' => 'override']],
                '<input type="hidden" id="Key[subkey1][subkey2]" name="override" value="Original value" />',
                ['Key' => ['subkey1' => ['subkey2' => 'Received value']]],
            ],
            [
                'hidden',
                ['ID', 'Original value', ['name' => 'Key[subkey1][subkey2]']],
                '<input type="hidden" id="ID" name="Key[subkey1][subkey2]" value="Received value" />',
                ['Key' => ['subkey1' => ['subkey2' => 'Received value']]],
            ],
            // checkbox
            [
                'checkbox',
                ['Key', 'Value'],
                '<input type="checkbox" id="Key" name="Key" class="form-check-input" value="Value" />',
            ],
            [
                'checkbox',
                ['Key', 'Value', false],
                '<input type="checkbox" id="Key" name="Key" class="form-check-input" value="Value" />',
            ],
            [
                'checkbox',
                ['Key', 'Value', null],
                '<input type="checkbox" id="Key" name="Key" class="form-check-input" value="Value" />',
            ],
            [
                'checkbox',
                ['Key', 'Value', 0],
                '<input type="checkbox" id="Key" name="Key" class="form-check-input" value="Value" />',
            ],
            [
                'checkbox',
                ['Key', 'Value', '0'],
                '<input type="checkbox" id="Key" name="Key" class="form-check-input" value="Value" />',
            ],
            [
                'checkbox',
                ['Key', 'Value', true],
                '<input type="checkbox" id="Key" name="Key" class="form-check-input" value="Value" checked="checked" />',
            ],
            [
                'checkbox',
                ['Key', 'Value', '1'],
                '<input type="checkbox" id="Key" name="Key" class="form-check-input" value="Value" checked="checked" />',
            ],
            [
                'checkbox',
                ['Key', 'Value', true],
                '<input type="checkbox" id="Key" name="Key" class="form-check-input" value="Value" />',
                ['OtherField' => 'Other value'],
            ],
            [
                'checkbox',
                ['Key', 'Value', false],
                '<input type="checkbox" id="Key" name="Key" class="form-check-input" value="Value" checked="checked" />',
                ['Key' => 'Value'],
            ],
            [
                'checkbox',
                ['Key', 'Value', false],
                '<input type="checkbox" id="Key" name="Key" class="form-check-input" value="Value" />',
                ['Key' => ''],
            ],
            [
                'checkbox',
                ['Key', 'Value', false],
                '<input type="checkbox" id="Key" name="Key" class="form-check-input" value="Value" />',
                ['Key' => 'Wrong value'],
            ],
            [
                'checkbox',
                ['Key[]', 'Value'],
                '<input type="checkbox" id="Key_Value" name="Key[]" class="form-check-input" value="Value" />',
            ],
            [
                'checkbox',
                ['Key[]', 'Value', true],
                '<input type="checkbox" id="Key_Value" name="Key[]" class="form-check-input" value="Value" checked="checked" />',
            ],
            [
                'checkbox',
                ['Key[]', 'Value', true],
                '<input type="checkbox" id="Key_Value" name="Key[]" class="form-check-input" value="Value" />',
                ['OtherField' => 'Other value'],
            ],
            [
                'checkbox',
                ['Key[]', 'Value', false],
                '<input type="checkbox" id="Key_Value" name="Key[]" class="form-check-input" value="Value" checked="checked" />',
                ['Key' => 'Value'],
            ],
            [
                'checkbox',
                ['Key[]', 'Value', false],
                '<input type="checkbox" id="Key_Value" name="Key[]" class="form-check-input" value="Value" />',
                ['Key' => 'Other value'],
            ],
            [
                'checkbox',
                ['Key[]', 'Value', false],
                '<input type="checkbox" id="Key_Value" name="Key[]" class="form-check-input" value="Value" checked="checked" />',
                ['Key' => ['Look', 'for', 'Value', 'in', 'this', 'array']],
            ],
            [
                'checkbox',
                ['Key[subkey1][subkey2]', 'Value', false],
                '<input type="checkbox" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" class="form-check-input" value="Value" />',
                ['Key' => 'Value'],
            ],
            [
                'checkbox',
                ['Key[subkey1][subkey2]', 'Value', false],
                '<input type="checkbox" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" class="form-check-input" value="Value" />',
                ['Key' => ['subkey1' => 'Value']],
            ],
            [
                'checkbox',
                ['Key[subkey1][subkey2]', 'Value', false],
                '<input type="checkbox" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" class="form-check-input" value="Value" checked="checked" />',
                ['Key' => ['subkey1' => ['subkey2' => 'Value']]],
            ],
            [
                'checkbox',
                ['Key[subkey1][subkey2]', 'Value', false],
                '<input type="checkbox" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" class="form-check-input" value="Value" checked="checked" />',
                ['Key' => ['subkey1' => ['subkey2' => ['subkey3' => 'Value']]]],
            ],
            [
                'checkbox',
                ['Key[subkey1][subkey2]', 'Value', false],
                '<input type="checkbox" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" class="form-check-input" value="Value" />',
                ['Key' => ['subkey1' => ['subkey2' => ['subkey3' => 'Other value']]]],
            ],
            [
                'checkbox',
                ['Key[]', 'Value', false, ['id' => 'custom-id']],
                '<input type="checkbox" id="custom-id" name="Key[]" class="form-check-input" value="Value" checked="checked" />',
                ['Key' => ['Look', 'for', 'Value', 'in', 'this', 'array']],
            ],
            [
                'checkbox',
                ['Key[]', 'Value', false, ['name' => 'custom-name[]']],
                '<input type="checkbox" id="Key_Value" name="custom-name[]" class="form-check-input" value="Value" />',
                ['Key' => ['Look', 'for', 'Value', 'in', 'this', 'array']],
            ],
            [
                'checkbox',
                ['Key[]', 'Value', false, ['name' => 'custom-name[]']],
                '<input type="checkbox" id="Key_Value" name="custom-name[]" class="form-check-input" value="Value" checked="checked" />',
                ['custom-name' => ['Look', 'for', 'Value', 'in', 'this', 'array']],
            ],
            // textarea
            [
                'textarea',
                ['Key'],
                '<textarea id="Key" name="Key" class="form-control"></textarea>',
            ],
            [
                'textarea',
                ['Key', 'Value'],
                '<textarea id="Key" name="Key" class="form-control">Value</textarea>',
            ],
            [
                'textarea',
                ['Key', ['class' => 'MY-CLASS']],
                '<textarea id="Key" name="Key" class="MY-CLASS form-control"></textarea>',
            ],
            [
                'textarea',
                ['Key', '', ['class' => 'MY-CLASS']],
                '<textarea id="Key" name="Key" class="MY-CLASS form-control"></textarea>',
            ],
            [
                'textarea',
                ['Key', 'Value', ['class' => 'MY-CLASS']],
                '<textarea id="Key" name="Key" class="MY-CLASS form-control">Value</textarea>',
            ],
            [
                'textarea',
                ['Key', 'Original value'],
                '<textarea id="Key" name="Key" class="form-control"></textarea>',
                ['Key' => ''],
            ],
            [
                'textarea',
                ['Key', 'Original value'],
                '<textarea id="Key" name="Key" class="form-control">Received value</textarea>',
                ['Key' => 'Received value'],
            ],
            [
                'textarea',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<textarea id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" class="form-control">Original value</textarea>',
                ['Key' => 'Received value'],
            ],
            [
                'textarea',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<textarea id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" class="form-control">Original value</textarea>',
                ['Key' => ['subkey1' => 'Received value']],
            ],
            [
                'textarea',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<textarea id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" class="form-control">Received value</textarea>',
                ['Key' => ['subkey1' => ['subkey2' => 'Received value']]],
            ],
            [
                'textarea',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<textarea id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" class="form-control">Original value</textarea>',
                ['Key' => ['subkey1' => ['subkey2' => ['subkey3' => 'Received value']]]],
            ],
            [
                'textarea',
                ['Key[subkey1][subkey2]', 'Original value', ['id' => '']],
                '<textarea name="Key[subkey1][subkey2]" class="form-control">Received value</textarea>',
                ['Key' => ['subkey1' => ['subkey2' => 'Received value']]],
            ],
            [
                'textarea',
                ['Key[subkey1][subkey2]', 'Original value', ['name' => 'foo']],
                '<textarea id="Key[subkey1][subkey2]" name="foo" class="form-control">Original value</textarea>',
                ['Key' => ['subkey1' => ['subkey2' => 'Received value']]],
            ],
            [
                'textarea',
                ['Key[subkey1][subkey2]', 'Original value', ['name' => 'Key[subkey1][subkey2override]']],
                '<textarea id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2override]" class="form-control">Received value</textarea>',
                ['Key' => ['subkey1' => ['subkey2override' => 'Received value']]],
            ],
            // radio
            [
                'radio',
                ['Key', 'Value'],
                '<input type="radio" id="Key**UNIQUENUMBER**" name="Key" value="Value" class="form-check-input" />',
            ],
            [
                'radio',
                ['Key', 'Value', 'Incorrect value'],
                '<input type="radio" id="Key**UNIQUENUMBER**" name="Key" value="Value" class="form-check-input" />',
            ],
            [
                'radio',
                ['Key', 'Value', 'Value'],
                '<input type="radio" id="Key**UNIQUENUMBER**" name="Key" value="Value" class="form-check-input" checked="checked" />',
            ],
            [
                'radio',
                ['Key', 'Value', ['class' => 'MY-CLASS', 'data-custom' => 'My custom data']],
                '<input type="radio" id="Key**UNIQUENUMBER**" name="Key" value="Value" class="MY-CLASS form-check-input" data-custom="My custom data" />',
            ],
            [
                'radio',
                ['Key', 'Value', 'Value', ['class' => 'MY-CLASS']],
                '<input type="radio" id="Key**UNIQUENUMBER**" name="Key" value="Value" class="MY-CLASS form-check-input" checked="checked" />',
            ],
            [
                'radio',
                ['Key', 'Value'],
                '<input type="radio" id="Key**UNIQUENUMBER**" name="Key" value="Value" class="form-check-input" />',
                ['Key' => ''],
            ],
            [
                'radio',
                ['Key', 'Value'],
                '<input type="radio" id="Key**UNIQUENUMBER**" name="Key" value="Value" class="form-check-input" />',
                ['Key' => 'Invalid value'],
            ],
            [
                'radio',
                ['Key', 'Value'],
                '<input type="radio" id="Key**UNIQUENUMBER**" name="Key" value="Value" class="form-check-input" checked="checked" />',
                ['Key' => 'Value'],
            ],
            [
                'radio',
                ['Key', 'Value'],
                '<input type="radio" id="Key**UNIQUENUMBER**" name="Key" value="Value" class="form-check-input" />',
                ['OtherKey' => 'OtherValue'],
            ],
            [
                'radio',
                ['Key[subkey1][subkey2]', 'Value'],
                '<input type="radio" id="Key[subkey1][subkey2]**UNIQUENUMBER**" name="Key[subkey1][subkey2]" value="Value" class="form-check-input" checked="checked" />',
                ['Key' => ['subkey1' => ['subkey2' => 'Value']]],
            ],
            [
                'radio',
                ['Key[subkey1][subkey2]', 'Value', ['id' => '']],
                '<input type="radio" name="Key[subkey1][subkey2]" value="Value" class="form-check-input" checked="checked" />',
                ['Key' => ['subkey1' => ['subkey2' => 'Value']]],
            ],
            [
                'radio',
                ['Key[subkey1][subkey2]', 'Value', ['name' => 'Key[subkey1][subkey2override]']],
                '<input type="radio" id="Key[subkey1][subkey2]**UNIQUENUMBER**" name="Key[subkey1][subkey2override]" value="Value" class="form-check-input" />',
                ['Key' => ['subkey1' => ['subkey2' => 'Value']]],
            ],
            [
                'radio',
                ['Key[subkey1][subkey2]', 'Value', ['name' => 'Key[subkey1][subkey2override]']],
                '<input type="radio" id="Key[subkey1][subkey2]**UNIQUENUMBER**" name="Key[subkey1][subkey2override]" value="Value" class="form-check-input" checked="checked" />',
                ['Key' => ['subkey1' => ['subkey2override' => 'Value']]],
            ],
            // inputType (text, number, email, telephone, url, search, password)
            [
                'text',
                ['Key'],
                '<input type="text" id="Key" name="Key" value="" class="form-control ccm-input-text" />',
            ],
            [
                'number',
                ['Key'],
                '<input type="number" id="Key" name="Key" value="" class="form-control ccm-input-number" />',
            ],
            [
                'email',
                ['Key'],
                '<input type="email" id="Key" name="Key" value="" class="form-control ccm-input-email" />',
            ],
            [
                'telephone',
                ['Key'],
                '<input type="tel" id="Key" name="Key" value="" class="form-control ccm-input-tel" />',
            ],
            [
                'url',
                ['Key'],
                '<input type="url" id="Key" name="Key" value="" class="form-control ccm-input-url" />',
            ],
            [
                'search',
                ['Key'],
                '<input type="search" id="Key" name="Key" value="" class="form-control ccm-input-search" />',
            ],
            [
                'password',
                ['Key'],
                '<input type="password" id="Key" name="Key" value="" autocomplete="off" class="form-control ccm-input-password" />',
            ],
            [
                'text',
                ['Key', null],
                '<input type="text" id="Key" name="Key" value="" class="form-control ccm-input-text" />',
            ],
            [
                'text',
                ['Key', false],
                '<input type="text" id="Key" name="Key" value="" class="form-control ccm-input-text" />',
            ],
            [
                'text',
                ['Key', 'Value'],
                '<input type="text" id="Key" name="Key" value="Value" class="form-control ccm-input-text" />',
            ],
            [
                'text',
                ['Key', ['data-mine' => 'MY DATA', 'class' => 'MY-CLASS']],
                '<input type="text" id="Key" name="Key" value="" data-mine="MY DATA" class="MY-CLASS form-control ccm-input-text" />',
            ],
            [
                'text',
                ['Key', 'Value', ['class' => 'MY-CLASS']],
                '<input type="text" id="Key" name="Key" value="Value" class="MY-CLASS form-control ccm-input-text" />',
            ],
            [
                'text',
                ['Key', 'Value'],
                '<input type="text" id="Key" name="Key" value="Received value" class="form-control ccm-input-text" />',
                ['Key' => 'Received value'],
            ],
            [
                'text',
                ['Key', 'Value'],
                '<input type="text" id="Key" name="Key" value="Value" class="form-control ccm-input-text" />',
                ['OtherKey' => 'Other value'],
            ],
            [
                'text',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<input type="text" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Original value" class="form-control ccm-input-text" />',
            ],
            [
                'text',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<input type="text" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Original value" class="form-control ccm-input-text" />',
                ['OtherKey' => 'Other value'],
            ],
            [
                'text',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<input type="text" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Original value" class="form-control ccm-input-text" />',
                ['Key' => 'Received value'],
            ],
            [
                'text',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<input type="text" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Original value" class="form-control ccm-input-text" />',
                ['Key' => ['subkey1' => 'Received value']],
            ],
            [
                'text',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<input type="text" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Received value" class="form-control ccm-input-text" />',
                ['Key' => ['subkey1' => ['subkey2' => 'Received value']]],
            ],
            [
                'text',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<input type="text" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Original value" class="form-control ccm-input-text" />',
                ['Key' => ['subkey1' => ['subkey2' => ['subkey3' => 'Received value']]]],
            ],
            [
                'text',
                ['Key', 'Value', ['id' => '']],
                '<input type="text" name="Key" value="Value" class="form-control ccm-input-text" />',
            ],
            [
                'text',
                ['Key', 'Value', ['name' => '']],
                '<input type="text" id="Key" value="Value" class="form-control ccm-input-text" />',
            ],
            // select
            [
                'select',
                ['Key', null],
                '<select id="Key" name="Key" class="form-select"></select>',
            ],
            [
                'select',
                ['Key', false],
                '<select id="Key" name="Key" class="form-select"></select>',
            ],
            [
                'select',
                ['Key', ''],
                '<select id="Key" name="Key" class="form-select"></select>',
            ],
            [
                'select',
                ['Key', []],
                '<select id="Key" name="Key" class="form-select"></select>',
            ],
            [
                'select',
                ['Key', ['One' => 'First', 'Two' => 'Second']],
                '<select id="Key" name="Key" class="form-select"><option value="One">First</option><option value="Two">Second</option></select>',
            ],
            [
                'select',
                ['Key', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key" name="Key" ccm-passed-value="Two" class="form-select"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
            ],
            [
                'select',
                ['Key', ['One' => 'First', 'Two' => 'Second'], ['class' => 'MY-CLASS', 'data-mine' => 'MY DATA']],
                '<select id="Key" name="Key" class="MY-CLASS form-select" data-mine="MY DATA"><option value="One">First</option><option value="Two">Second</option></select>',
            ],
            [
                'select',
                ['Key', ['One' => 'First', 'Two' => 'Second'], 'Two', ['class' => 'MY-CLASS', 'data-mine' => 'MY DATA']],
                '<select id="Key" name="Key" class="MY-CLASS form-select" data-mine="MY DATA" ccm-passed-value="Two"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
            ],
            [
                'select',
                ['Key', ['One' => 'First', 'Two' => 'Second'], 'Invalid'],
                '<select id="Key" name="Key" ccm-passed-value="Invalid" class="form-select"><option value="One">First</option><option value="Two">Second</option></select>',
            ],
            [
                'select',
                ['Key', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key" name="Key" ccm-passed-value="Two" class="form-select"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
                ['OtherField' => 'OtherValue'],
            ],
            [
                'select',
                ['Key', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key" name="Key" ccm-passed-value="Two" class="form-select"><option value="One">First</option><option value="Two">Second</option></select>',
                ['Key' => ''],
            ],
            [
                'select',
                ['Key', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key" name="Key" ccm-passed-value="Two" class="form-select"><option value="One" selected="selected">First</option><option value="Two">Second</option></select>',
                ['Key' => 'One'],
            ],
            [
                'select',
                ['Key', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key" name="Key" ccm-passed-value="Two" class="form-select"><option value="One">First</option><option value="Two">Second</option></select>',
                ['Key' => 'Invalid'],
            ],
            [
                'select',
                ['Key[subkey1][subkey2]', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" ccm-passed-value="Two" class="form-select"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
                ['Key' => 'One'],
            ],
            [
                'select',
                ['Key[subkey1][subkey2]', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" ccm-passed-value="Two" class="form-select"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
                ['Key' => ['subkey1' => 'One']],
            ],
            [
                'select',
                ['Key[subkey1][subkey2]', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" ccm-passed-value="Two" class="form-select"><option value="One" selected="selected">First</option><option value="Two">Second</option></select>',
                ['Key' => ['subkey1' => ['subkey2' => 'One']]],
            ],
            [
                'select',
                ['Key[subkey1][subkey2]', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" ccm-passed-value="Two" class="form-select"><option value="One">First</option><option value="Two">Second</option></select>',
                ['Key' => ['subkey1' => ['subkey2' => ['subkey3' => 'One']]]],
            ],
            [
                'select',
                ['Key[subkey1][subkey2]', ['One' => 'First', 'Two' => 'Second'], 'Two', ['id' => '']],
                '<select name="Key[subkey1][subkey2]" ccm-passed-value="Two" class="form-select"><option value="One" selected="selected">First</option><option value="Two">Second</option></select>',
                ['Key' => ['subkey1' => ['subkey2' => 'One']]],
            ],
            [
                'select',
                ['Key[subkey1][subkey2]', ['One' => 'First', 'Two' => 'Second'], 'Two', ['name' => 'Key[subkey1override][subkey2]']],
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1override][subkey2]" ccm-passed-value="Two" class="form-select"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
                ['Key' => ['subkey1' => ['subkey2' => 'One']]],
            ],
            [
                'select',
                ['Key[subkey1][subkey2]', ['One' => 'First', 'Two' => 'Second'], 'Two', ['name' => 'Key[subkey1override][subkey2]']],
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1override][subkey2]" ccm-passed-value="Two" class="form-select"><option value="One" selected="selected">First</option><option value="Two">Second</option></select>',
                ['Key' => ['subkey1override' => ['subkey2' => 'One']]],
            ],
            // selectMultiple
            [
                'selectMultiple',
                ['Key', null],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-select"></select>',
            ],
            [
                'selectMultiple',
                ['Key', []],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-select"></select>',
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second']],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-select"><option value="One">First</option><option value="Two">Second</option></select>',
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second'], false],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-select"><option value="One">First</option><option value="Two">Second</option></select>',
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second'], null],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-select"><option value="One">First</option><option value="Two">Second</option></select>',
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second'], []],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-select"><option value="One">First</option><option value="Two">Second</option></select>',
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second'], false, ['data-mine' => 'MY DATA', 'class' => 'MY-CLASS']],
                '<select id="Key" name="Key[]" multiple="multiple" data-mine="MY DATA" class="MY-CLASS form-select"><option value="One">First</option><option value="Two">Second</option></select>',
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-select"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second'], ['Two']],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-select"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second'], ['Invalid']],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-select"><option value="One">First</option><option value="Two">Second</option></select>',
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second'], ['Two', 'One']],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-select"><option value="One" selected="selected">First</option><option value="Two" selected="selected">Second</option></select>',
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second']],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-select"><option value="One" selected="selected">First</option><option value="Two">Second</option></select>',
                ['Key' => 'One'],
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second']],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-select"><option value="One">First</option><option value="Two">Second</option></select>',
                ['Key' => []],
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second']],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-select"><option value="One" selected="selected">First</option><option value="Two">Second</option></select>',
                ['Key' => ['One']],
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-select"><option value="One" selected="selected">First</option><option value="Two">Second</option></select>',
                ['Key' => ['One']],
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second']],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-select"><option value="One" selected="selected">First</option><option value="Two" selected="selected">Second</option></select>',
                ['Key' => ['One', 'Two']],
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-select"><option value="One" selected="selected">First</option><option value="Two" selected="selected">Second</option></select>',
                ['Key' => ['One', 'Two']],
            ],
            [
                'selectMultiple',
                ['Key[subkey1][subkey2]', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2][]" multiple="multiple" class="form-select"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
                ['Key' => ['One']],
            ],
            [
                'selectMultiple',
                ['Key[subkey1][subkey2]', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2][]" multiple="multiple" class="form-select"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
                ['Key' => ['subkey1' => ['One']]],
            ],
            [
                'selectMultiple',
                ['Key[subkey1][subkey2]', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2][]" multiple="multiple" class="form-select"><option value="One" selected="selected">First</option><option value="Two">Second</option></select>',
                ['Key' => ['subkey1' => ['subkey2' => ['One']]]],
            ],
            [
                'selectMultiple',
                ['Key[subkey1][subkey2]', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2][]" multiple="multiple" class="form-select"><option value="One" selected="selected">First</option><option value="Two">Second</option></select>',
                ['Key' => ['subkey1' => ['subkey2' => ['subkey3' => 'One']]]],
            ],
            [
                'selectMultiple',
                ['Key[subkey1][subkey2]', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2][]" multiple="multiple" class="form-select"><option value="One">First</option><option value="Two">Second</option></select>',
                ['Key' => ['subkey1' => ['subkey2' => ['subkey3' => ['One']]]]],
            ],
            [
                'select',
                ['Key', ['One' => 'First', 'Two' => 'Second'], '<Two\'">'],
                '<select id="Key" name="Key" ccm-passed-value="&lt;Two&#039;&quot;&gt;" class="form-select"><option value="One">First</option><option value="Two">Second</option></select>',
                ['OtherField' => 'OtherValue'],
            ],
            [
                'selectMultiple',
                ['Key[subkey1][subkey2]', ['One' => 'First', 'Two' => 'Second'], 'Two', ['id' => '']],
                '<select name="Key[subkey1][subkey2][]" multiple="multiple" class="form-select"><option value="One" selected="selected">First</option><option value="Two">Second</option></select>',
                ['Key' => ['subkey1' => ['subkey2' => ['subkey3' => 'One']]]],
            ],
            [
                'selectMultiple',
                ['Key[subkey1][subkey2]', ['One' => 'First', 'Two' => 'Second'], 'Two', ['name' => 'KeyOverride[subkey1][subkey2][]']],
                '<select id="Key[subkey1][subkey2]" name="KeyOverride[subkey1][subkey2][]" multiple="multiple" class="form-select"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
                ['Key' => ['subkey1' => ['subkey2' => ['subkey3' => 'One']]]],
            ],
            [
                'selectMultiple',
                ['Key[subkey1][subkey2]', ['One' => 'First', 'Two' => 'Second'], 'Two', ['name' => 'KeyOverride[subkey1][subkey2][]']],
                '<select id="Key[subkey1][subkey2]" name="KeyOverride[subkey1][subkey2][]" multiple="multiple" class="form-select"><option value="One" selected="selected">First</option><option value="Two">Second</option></select>',
                ['KeyOverride' => ['subkey1' => ['subkey2' => ['subkey3' => 'One']]]],
            ],
        ];
    }

    /**
     * @dataProvider providerTestCreateElements
     *
     * @param mixed $method
     * @param array $args
     * @param mixed $expected
     * @param array $post
     */
    public function testCreateElements($method, array $args, $expected, array $post = [])
    {
        if (empty($post)) {
            self::$request->initialize();
        } else {
            self::$request->initialize([], $post, [], [], [], ['REQUEST_METHOD' => 'POST']);
        }
        $calculated = call_user_func_array([static::$formHelper, $method], $args);
        if (strpos($expected, '**UNIQUENUMBER**') === false) {
            $this->assertSame($expected, $calculated);
        } else {
            $chunks = explode('**UNIQUENUMBER**', $expected);
            array_walk($chunks, function (&$chunk, $index) {
                $chunk = preg_quote($chunk, '/');
            });
            $rx = '/^' . implode('\d+', $chunks) . '$/';
            $this->assertRegExp($rx, $calculated);
        }
    }
}
