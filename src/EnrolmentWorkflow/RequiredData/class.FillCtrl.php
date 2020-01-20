<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\RequiredData;

use ilSrUserEnrolmentPlugin;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant\AssistantsRequestGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\AcceptRequestGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestStepGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use srag\RequiredData\SrUserEnrolment\Fill\AbstractFillCtrl;

/**
 * Class FillCtrl
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\RequiredData
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class FillCtrl extends AbstractFillCtrl
{

    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    const RETURN_REQUEST_STEP = 1;
    const RETURN_ACCEPT_STEP = 2;
    const RETURN_MULTIPLE_REQUESTS = 3;
    /**
     * @var int
     */
    protected $return;


    /**
     * @inheritDoc
     *
     * @param int $return
     */
    public function __construct(int $parent_context, int $parent_id, int $return)
    {
        parent::__construct($parent_context, $parent_id);

        $this->return = $return;
    }


    /**
     * @inheritDoc
     */
    protected function back()/* : void*/
    {
        switch ($this->return) {
            case self::RETURN_REQUEST_STEP:
                self::dic()->ctrl()->redirectByClass(RequestStepGUI::class, RequestStepGUI::CMD_REQUEST_STEP);
                break;

            case self::RETURN_ACCEPT_STEP:
                self::dic()->ctrl()->redirectByClass(AcceptRequestGUI::class, AcceptRequestGUI::CMD_ACCEPT_REQUEST);
                break;

            case self::RETURN_MULTIPLE_REQUESTS:
                self::dic()->ctrl()->redirectByClass(AssistantsRequestGUI::class, AssistantsRequestGUI::CMD_MULTIPLE_REQUESTS);
                break;

            default:
                break;
        }
    }


    /**
     * @inheritDoc
     */
    protected function cancel()/* : void*/
    {
        switch ($this->return) {
            case self::RETURN_REQUEST_STEP:
                self::dic()->ctrl()->redirectByClass(RequestStepGUI::class, RequestStepGUI::CMD_BACK);
                break;

            case self::RETURN_ACCEPT_STEP:
                self::dic()->ctrl()->redirectByClass(AcceptRequestGUI::class, AcceptRequestGUI::CMD_BACK);
                break;

            case self::RETURN_MULTIPLE_REQUESTS:
                self::dic()->ctrl()->redirectByClass(AssistantsRequestGUI::class, AssistantsRequestGUI::CMD_BACK);
                break;

            default:
                break;
        }
    }
}
