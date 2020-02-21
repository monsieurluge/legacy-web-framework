<?php

namespace App\Services\Response\JsonApi;

abstract class ErrorsEnum
{

    const CANNOT_SAVE_CSV_FILE  = '1';
    const CANNOT_SEND_CSV_FILE  = '2';
    const CANNOT_CREATE_ISSUE   = '3';
    const CANNOT_UPDATE_ISSUE   = '4';
    const CANNOT_ADD_ATTACHMENT = '5';
    const EMPTY_NOTE_CONTENT    = '6';
    const CANNOT_UPDATE_NOTE    = '7';
    const CANNOT_DELETE_NOTE    = '8';
    const CANNOT_GET_NOTES      = '9';
    const CANNOT_GET_EMAIL      = '10';
    const CANNOT_SEND_EMAIL     = '11';
    const CANNOT_GET_OFFICE     = '12';
    const CANNOT_GET_SSII       = '13';

}
