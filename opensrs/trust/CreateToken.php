<?php

namespace OpenSRS\trust;

use OpenSRS\Base;
use OpenSRS\Exception;

/*
 *  Required object values:
 *  - none -
 *
 *  Optional Data:
 *  data - owner_email, admin_email, billing_email, tech_email, del_from, del_to, exp_from, exp_to, page, limit
 */

class CreateToken extends Base 
{
    private $_dataObject;
    private $_formatHolder = '';
    public $resultFullRaw;
    public $resultRaw;
    public $resultFullFormatted;
    public $resultFormatted;

    public function __construct($formatString, $dataObject)
    {
        parent::__construct();
        $this->_dataObject = $dataObject;
        $this->_formatHolder = $formatString;
        $this->_validateObject();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    // Validate the object
    private function _validateObject()
    {
        $allPassed = true;

        if (!isset($this->_dataObject->data->order_id) and !isset($this->_dataObject->data->product_id)) {
            throw new Exception('oSRS Error - order_id or product_id is not defined.');
            $allPassed = false;
        }

        // Run the command
        if ($allPassed) {
            // Execute the command
            $this->_processRequest();
        } else {
            throw new Exception('oSRS Error - Incorrect call.');
        }
    }

    // Post validation functions
    private function _processRequest()
    {
        $cmd = array(
            'protocol' => 'XCP',
            'action' => 'create_token',
            'object' => 'trust_service',
        );

        if (isset($this->_dataObject->data->product_id) && $this->_dataObject->data->product_id != '') {
            $cmd['attributes']['product_id'] = $this->_dataObject->data->product_id;
        }
        if (isset($this->_dataObject->data->order_id) && $this->_dataObject->data->order_id != '') {
            $cmd['attributes']['order_id'] = $this->_dataObject->data->order_id;
        }

        $xmlCMD = $this->_opsHandler->encode($cmd);                    // Flip Array to XML
        $XMLresult = $this->send_cmd($xmlCMD);                        // Send XML
        $arrayResult = $this->_opsHandler->decode($XMLresult);        // Flip XML to Array

        // Results
        $this->resultFullRaw = $arrayResult;
        $this->resultRaw = $arrayResult;
        $this->resultFullFormatted = $this->convertArray2Formatted($this->_formatHolder, $this->resultFullRaw);
        $this->resultFormatted = $this->convertArray2Formatted($this->_formatHolder, $this->resultRaw);
    }
}