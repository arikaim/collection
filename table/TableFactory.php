<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Collection\Table;

use Arikaim\Core\Collection\Table\Table;

/**
 * Table factory class
 */
class TableFactory
{   
    /** */
    public static function loadCsv(string $fileName, string $separator = ','): ?object
    {
        $file = \fopen($fileName,'r');
        if ($file === false) {
            return null;
        }

        $header = \fgetcsv($file,null,$separator);
        if ($header === false) {
            return null;
        }
     
        $rows = [];
        while (($row = \fgetcsv($file,null,$separator)) !== false) {
            $rows[] = $row;
        }

        \fclose($file);

        return new Table($rows,$header,$separator);
    }
}
