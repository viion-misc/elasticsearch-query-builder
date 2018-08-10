<?php

namespace App\Service\ElasticSearch;

/**
 * Elastic Search index maps, used for querying.
 */
class Mapping
{
    const STRING = [
        'type' => 'text',
        'analyzer' => 'standard',
        'fielddata' => true,
        'index' => true,
    ];
    
    const INTEGER = [
        'type' => 'integer',
        'index' => true,
    ];
    
    const BOOLEAN = [
        'type' => 'boolean',
        'index' => true,
    ];
    
    const TEXT = [
        'type' => 'text',
        'index' => true,
    ];
    
    /**
     * Detect the type of mapping the value should be
     */
    public static function detect($field, $value)
    {
        // fix objects
        if (is_object($value)) {
            print_r([
                'field' => $field
            ]);
            print_r($value);
            die("\nError: The above is an object, shouldnt be, fix it! Need an |ID in the field name\n\n");
        }
        
        /*if (is_bool($value)) {
            return self::BOOLEAN;
        }*/
        
        if (is_numeric($value)) {
            return self::INTEGER;
        }
        
        if (strlen($value) > 100) {
            return self::TEXT;
        }
        
        return self::STRING;
    }
}
