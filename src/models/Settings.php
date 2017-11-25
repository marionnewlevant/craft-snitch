<?php

namespace marionnewlevant\snitch\models;

use Craft;
use craft\base\Model;
class Settings extends Model
{

    const SERVERPOLLINTERVAL = 2;
    const MESSAGE = 'May also be edited by: {user}.';
    const INPUTIDSELECTOR = 'form input[type="hidden"][name="entryId"]' // entry forms
        .', form input[type="hidden"][name="elementId"]' // modals entry forms
        .', form input[type="hidden"][name="setId"]' // global set
        .', form input[type="hidden"][name="categoryId"]' // category
        .', form input[type="hidden"][name="userId"]'; // user;
    /**
     * @var int
     */
    public $serverPollInterval = self::SERVERPOLLINTERVAL;

    /**
     * @var string
     */
    public $message = self::MESSAGE;

    /**
     * @var string
     */
    public $inputIdSelector = self::INPUTIDSELECTOR;

    public function rules()
    {
        return [
            ['serverPollInterval', 'integer', 'min' => 1, 'max' => 5],
            ['serverPollInterval', 'default', 'value' => self::SERVERPOLLINTERVAL],
            ['message', 'string'],
            ['message', 'default', 'value' => self::MESSAGE],
            ['inputIdSelector', 'string'],
            ['inputIdSelector', 'default', 'value' => self::INPUTIDSELECTOR],
        ];
    }
}