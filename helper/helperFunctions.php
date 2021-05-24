<?php
function IsVariableIsSetOrEmpty($variableToCheck)
{
    if (!isset($variableToCheck) || empty($variableToCheck)) {
        return true;
    }
    return false;
}