<?php

namespace App\Services\Database;

/**
 * QueryCreator
 */
class QueryCreator
{

    /**
     * TODO [dynamicInsert description]
     * @param  array $fields
     * @param  array $tokens array of param used to execute a prepared query thanks DatabasePDO
     * @return array
     */
    public function dynamicInsert($fields, $tokens)
    {
        $dynamicToken = array();

        if (!is_array($tokens)) {
            $tokens = array(0=>$tokens);
        }

        if (!is_array($fields)) {
            $fields = array(0=>$fields);
        }

        $dynamicQueryInsert = " (";
        $dynamicQueryValues = " VALUES (";

        foreach ($fields as $field=>$value) {
            if (isset($tokens[$value])) {
                $dynamicQueryInsert .= $field . ",";
                $dynamicQueryValues .= ":" . $value . ",";

                $dynamicToken[$value] = $tokens[$value];
            }
        }

        $dynamicQueryInsert = rtrim($dynamicQueryInsert, ","); $dynamicQueryInsert .= ")";
        $dynamicQueryValues = rtrim($dynamicQueryValues, ","); $dynamicQueryValues .= ")";
        $dynamicQuery       = $dynamicQueryInsert . $dynamicQueryValues;

        return array("dynamicQuery" => $dynamicQuery, "dynamicToken" =>$dynamicToken);
    }

}
