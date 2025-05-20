<?php

namespace App\Constants;

class GlobalMessages
{
    // GLobal messaged
    const INVALID_DATA = 'Invalid data.';
    const DUPLICATE_DATA = "Data already exists. Cannot create or update with duplicate code or name.";
    const DUPLICATE_CODE = 'Duplicate code. Cannot create or update with duplicate code.';
    const DUPLICATE_NAME = 'Duplicate name. Cannot create or update with duplicate name.';
    const DATA_SAVED = 'Data was saved.';
    const DATA_UPDATED = 'Data was updated.';
    const DATA_DELETED = 'Data was deleted.';
    const DATA_RESTORED = 'Data was restored.';
    const DATA_EXISTS = 'Data already exists';

    // API Global Messages
    const ERROR_1 = '[E1] Invalid Parameters';
    const ERROR_2 = '[E2] Version is not available';
    const ERROR_3 = '[E3] Invalid Signature';
    const ERROR_4 = '[E4] App is not available';
    const ERROR_5 = '[E5] Json Param is invalid.';
    const ERROR_6 = '[E6] The version code has expired, Please update your App';
    const ERROR_7 = '[E7] SDK Key is not eligible for this App';
    const ERROR_8 = '[E8] Username is not available / already exists.';
    const ERROR_9 = '[E9] Email is not available / already exists.';
    const ERROR_10 = '[E10] Device ID not found.';
    const ERROR_11 = '[E11] Parameter {PARAM} is required';
    const ERROR_12 = '[E12] Parameter {PARAM} is invalid format';
    const ERROR_13 = '[E13] Parameter {PARAM} is int';	
    const ERROR_14 = '[E14] Game is not available';	
    
}