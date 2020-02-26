<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\TotalRequests;

use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\AbstractRule;

/**
 * Class TotalRequests
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\TotalRequests
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class TotalRequests extends AbstractRule
{

    const TABLE_NAME_SUFFIX = "totareq";
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_is_notnull   true
     */
    protected $total_requests = 0;


    /**
     * @inheritDoc
     */
    public static function supportsParentContext(/*?*/ int $parent_context = null) : bool
    {
        switch ($parent_context) {
            case self::PARENT_CONTEXT_COURSE:
                return false;

            default:
                return true;
        }
    }


    /**
     * @inheritDoc
     */
    public function getRuleDescription() : string
    {
        return $this->getTotalRequests();
    }

    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            case "total_requests":
                return json_encode($field_value);

            default:
                return null;
        }
    }


    /**
     * @inheritDoc
     */
    public function wakeUp(/*string*/ $field_name, $field_value)
    {
        switch ($field_name) {
            case "total_requests":
                return json_decode($field_value);

            default:
                return null;
        }
    }


    /**
     * @return int
     */
    public function getTotalRequests() : int
    {
        return $this->total_requests;
    }


    /**
     * @param int $total_requests
     */
    public function setTotalRequests(int $total_requests)/* : void*/
    {
        $this->total_requests = $total_requests;
    }
}
