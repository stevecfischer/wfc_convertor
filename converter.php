<?php
    
if($file && $brand && $productType)
{
    //include configuration
    $config = 'config/'.$productType.'_'.$brand.'.php';
    include $config;

    //load data
    switch($fileType)
    {
        case 'xlsx': $fileName = loadXlsxData($file, $config, $productType, $brand); break;
    }
}

/**
 * Load data from XLS file format
 */
function loadXlsxData($file, $config, $productType, $brand)
{
    include 'lib/PHPExcel.php';
    include $config;
    
    $objReader = new PHPExcel_Reader_Excel2007();
    $objReader->setReadDataOnly(true);
    $objPHPExcel = $objReader->load($file);
    $products = array();
    
    $sheet = $objPHPExcel->getSheet(0);
    
    //create simple product line
    $result = createHeaders($config);
    
    $rowCount = 1;
    //loop row
    foreach($sheet->getRowIterator() as $row) 
    {
        //skip first rows
        if($rowCount < $startAtRow)
        {
            $rowCount++;
            continue;
        }
        
        $cellCount = 1;
        //loop col
        $data = array();
        foreach ($row->getCellIterator() as $cell)
        {
           $data[$cellCount] = $cell->getValue();
           $cellCount++;
        }
        
        //create simple product line
        $result .= createSimple($data, $config);
        
        //save simple for further configurable creation
        if($hasConfigurable)
        {
            $groupingBy = preg_replace_callback(
                '/\{([A-Z]*)\}/',
                function($matches) use ($data)
                {
                    $rowNumberToLetter = array('A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10, 'K' => 11, 'L' => 12, 'M' => 13, 'N' => 14, 'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 'S' => 19, 'T' => 20, 'U' => 21, 'V' => 22, 'W' => 23, 'X' => 24, 'Y' => 25, 'Z' => 26, 'AA' => 27, 'AB' => 28, 'AC' => 29, 'AD' => 30, 'AE' => 31, 'AF' => 32, 'AG' => 33, 'AH' => 34, 'AI' => 35, 'AJ' => 36, 'AK' => 37, 'AL' => 38, 'AM' => 39, 'AN' => 40, 'AO' => 41, 'AP' => 42, 'AQ' => 43, 'AR' => 44, 'AS' => 45, 'AT' => 46, 'AU' => 47, 'AV' => 48, 'AW' => 49, 'AX' => 50, 'AY' => 51, 'AZ' => 52);    
                    return str_replace(array("\n", '"', '’', '—'), array('', '""', "'", ' '), $data[$rowNumberToLetter[$matches[1]]]);
                },
                $configurableGroup
            );
            if(!is_array($products[$groupingBy]))
            {
                $products[$groupingBy] = array();
            }
            $products[$groupingBy][] = $data;
        }
        
        $rowCount++;
    }
    
    //create configurable
    if($hasConfigurable)
    {
        foreach($products as $groupingBy => $simplesArray)
        {
            //create configurable product line
            $result .= createConfigurable($groupingBy, $config, $simplesArray);
        }
    }
        
    //debug
    //echo str_replace("\n", "<br>", $result); die;
    
    //save CSV
    $fileName = 'out/'.$productType.'_'.$brand.'_'.date('Y-m-d H-i').'.csv';
    $file = fopen($fileName,"w+");
    fwrite($file, $result);
    fclose($file);
    
    return $fileName;
}

function createSimple($data, $config)
{
    include $config;
    
    $result = '';
    foreach($row as $key => $oneRow)
    {
        //test if mapping is necessary
        if(is_array($oneRow))
        {
            $val = preg_replace_callback(
                '/\{([A-Z]*)\}/',
                function($matches) use ($data)
                {
                        $rowNumberToLetter = array('A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10, 'K' => 11, 'L' => 12, 'M' => 13, 'N' => 14, 'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 'S' => 19, 'T' => 20, 'U' => 21, 'V' => 22, 'W' => 23, 'X' => 24, 'Y' => 25, 'Z' => 26, 'AA' => 27, 'AB' => 28, 'AC' => 29, 'AD' => 30, 'AE' => 31, 'AF' => 32, 'AG' => 33, 'AH' => 34, 'AI' => 35, 'AJ' => 36, 'AK' => 37, 'AL' => 38, 'AM' => 39, 'AN' => 40, 'AO' => 41, 'AP' => 42, 'AQ' => 43, 'AR' => 44, 'AS' => 45, 'AT' => 46, 'AU' => 47, 'AV' => 48, 'AW' => 49, 'AX' => 50, 'AY' => 51, 'AZ' => 52);    
                        return str_replace(array("\n", '"', '’', '—'), array('', '""', "'", ' '), $data[$rowNumberToLetter[$matches[1]]]);
                },
                $oneRow['VALUE']
            );
                
            $val = str_replace('""', '"', $val);
            $val = str_replace('X', 'x', $val);
            $val = $oneRow[$val];
            $val = str_replace('"', '""', $val);
        }
        else
        {
            $val = preg_replace_callback(
                '/\{([A-Z]*)\}/',
                function($matches) use ($data)
                {
                        $rowNumberToLetter = array('A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10, 'K' => 11, 'L' => 12, 'M' => 13, 'N' => 14, 'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 'S' => 19, 'T' => 20, 'U' => 21, 'V' => 22, 'W' => 23, 'X' => 24, 'Y' => 25, 'Z' => 26, 'AA' => 27, 'AB' => 28, 'AC' => 29, 'AD' => 30, 'AE' => 31, 'AF' => 32, 'AG' => 33, 'AH' => 34, 'AI' => 35, 'AJ' => 36, 'AK' => 37, 'AL' => 38, 'AM' => 39, 'AN' => 40, 'AO' => 41, 'AP' => 42, 'AQ' => 43, 'AR' => 44, 'AS' => 45, 'AT' => 46, 'AU' => 47, 'AV' => 48, 'AW' => 49, 'AX' => 50, 'AY' => 51, 'AZ' => 52);    
                        return str_replace(array("\n", '"', '’', '—'), array('', '""', "'", ' '), $data[$rowNumberToLetter[$matches[1]]]);
                },
                $oneRow
            );
            if($key == 'price')
            {
                $val = $val*$priceMultiplier;
            }
			elseif($key == 'sku')
			{
				$val = str_replace(' ', '', str_replace(' X ', 'x', $val));
			}
        }
		
		
        $result .= '"'.$val.'",';
    }
    $result = substr($result, 0, strlen($result)-1)."\n";
    
    return $result;
}

function createConfigurable($groupingBy, $config, $simplesArray)
{
    include $config;
    
    $result = '';
    $dataFirstSimple = $simplesArray[0];
    
    foreach($row as $key => $oneRow)
    {
        //specific check for Price
        if($key == 'price' && $configurablePrice == 'lowest_simple')
        {
            $val = 1000000000;
            foreach($simplesArray as $simple)
            {
                $currentPrice = preg_replace_callback(
                    '/\{([A-Z]*)\}/',
                    function($matches) use ($simple)
                    {
                        $rowNumberToLetter = array('A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10, 'K' => 11, 'L' => 12, 'M' => 13, 'N' => 14, 'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 'S' => 19, 'T' => 20, 'U' => 21, 'V' => 22, 'W' => 23, 'X' => 24, 'Y' => 25, 'Z' => 26, 'AA' => 27, 'AB' => 28, 'AC' => 29, 'AD' => 30, 'AE' => 31, 'AF' => 32, 'AG' => 33, 'AH' => 34, 'AI' => 35, 'AJ' => 36, 'AK' => 37, 'AL' => 38, 'AM' => 39, 'AN' => 40, 'AO' => 41, 'AP' => 42, 'AQ' => 43, 'AR' => 44, 'AS' => 45, 'AT' => 46, 'AU' => 47, 'AV' => 48, 'AW' => 49, 'AX' => 50, 'AY' => 51, 'AZ' => 52);    
                        return str_replace(array("\n", '"', '’', '—'), array('', '""', "'", ' '), $simple[$rowNumberToLetter[$matches[1]]]);
                    },
                    $row['price']
                );
                $currentPrice = $currentPrice*$priceMultiplier;
                    
                $val = min($val, $currentPrice);
            }
        }
        //specific check for special price
        elseif($key == 'special_price' && $configurableSpecialPrice == 'lowest_simple')
        {
            $val = 1000000000;
            foreach($simplesArray as $simple)
            {
                $currentPrice = preg_replace_callback(
                    '/\{([A-Z]*)\}/',
                    function($matches) use ($simple)
                    {
                        $rowNumberToLetter = array('A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10, 'K' => 11, 'L' => 12, 'M' => 13, 'N' => 14, 'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 'S' => 19, 'T' => 20, 'U' => 21, 'V' => 22, 'W' => 23, 'X' => 24, 'Y' => 25, 'Z' => 26, 'AA' => 27, 'AB' => 28, 'AC' => 29, 'AD' => 30, 'AE' => 31, 'AF' => 32, 'AG' => 33, 'AH' => 34, 'AI' => 35, 'AJ' => 36, 'AK' => 37, 'AL' => 38, 'AM' => 39, 'AN' => 40, 'AO' => 41, 'AP' => 42, 'AQ' => 43, 'AR' => 44, 'AS' => 45, 'AT' => 46, 'AU' => 47, 'AV' => 48, 'AW' => 49, 'AX' => 50, 'AY' => 51, 'AZ' => 52);    
                        return str_replace(array("\n", '"', '’', '—'), array('', '""', "'", ' '), $simple[$rowNumberToLetter[$matches[1]]]);
                    },
                    $row['special_price']
                );
                $val = min($val, $currentPrice);
            }
        }
        //specific check for weight
        elseif($key == 'weight' && $configurableWeight == 'lowest_simple')
        {
            $val = 1000000000;
            foreach($simplesArray as $simple)
            {
                $currentWeight = preg_replace_callback(
                    '/\{([A-Z]*)\}/',
                    function($matches) use ($simple)
                    {
                        $rowNumberToLetter = array('A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10, 'K' => 11, 'L' => 12, 'M' => 13, 'N' => 14, 'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 'S' => 19, 'T' => 20, 'U' => 21, 'V' => 22, 'W' => 23, 'X' => 24, 'Y' => 25, 'Z' => 26, 'AA' => 27, 'AB' => 28, 'AC' => 29, 'AD' => 30, 'AE' => 31, 'AF' => 32, 'AG' => 33, 'AH' => 34, 'AI' => 35, 'AJ' => 36, 'AK' => 37, 'AL' => 38, 'AM' => 39, 'AN' => 40, 'AO' => 41, 'AP' => 42, 'AQ' => 43, 'AR' => 44, 'AS' => 45, 'AT' => 46, 'AU' => 47, 'AV' => 48, 'AW' => 49, 'AX' => 50, 'AY' => 51, 'AZ' => 52);    
                        return str_replace(array("\n", '"', '’', '—'), array('', '""', "'", ' '), $simple[$rowNumberToLetter[$matches[1]]]);
                    },
                    $row['weight']
                );
                $val = min($val, $currentWeight);
            }
        }
        //specific check for associated
        elseif($key == 'associated')
        {
            $val = array();
            foreach($simplesArray as $simple)
            {
                $currentSku = preg_replace_callback(
                    '/\{([A-Z]*)\}/',
                    function($matches) use ($simple)
                    {
                        $rowNumberToLetter = array('A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10, 'K' => 11, 'L' => 12, 'M' => 13, 'N' => 14, 'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 'S' => 19, 'T' => 20, 'U' => 21, 'V' => 22, 'W' => 23, 'X' => 24, 'Y' => 25, 'Z' => 26, 'AA' => 27, 'AB' => 28, 'AC' => 29, 'AD' => 30, 'AE' => 31, 'AF' => 32, 'AG' => 33, 'AH' => 34, 'AI' => 35, 'AJ' => 36, 'AK' => 37, 'AL' => 38, 'AM' => 39, 'AN' => 40, 'AO' => 41, 'AP' => 42, 'AQ' => 43, 'AR' => 44, 'AS' => 45, 'AT' => 46, 'AU' => 47, 'AV' => 48, 'AW' => 49, 'AX' => 50, 'AY' => 51, 'AZ' => 52);    
                        return str_replace(array("\n", '"', '’', '—'), array('', '""', "'", ' '), $simple[$rowNumberToLetter[$matches[1]]]);
                    },
                    $row['sku']
                );
                $val[] = $currentSku;
            }
            $val = implode(',', $val);
        }
        //test if configurable override something
        elseif(isset($configurableRow[$key]))
        {
            $val = preg_replace_callback(
                '/\{([A-Z]*)\}/',
                function($matches) use ($dataFirstSimple)
                {
                    $rowNumberToLetter = array('A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10, 'K' => 11, 'L' => 12, 'M' => 13, 'N' => 14, 'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 'S' => 19, 'T' => 20, 'U' => 21, 'V' => 22, 'W' => 23, 'X' => 24, 'Y' => 25, 'Z' => 26, 'AA' => 27, 'AB' => 28, 'AC' => 29, 'AD' => 30, 'AE' => 31, 'AF' => 32, 'AG' => 33, 'AH' => 34, 'AI' => 35, 'AJ' => 36, 'AK' => 37, 'AL' => 38, 'AM' => 39, 'AN' => 40, 'AO' => 41, 'AP' => 42, 'AQ' => 43, 'AR' => 44, 'AS' => 45, 'AT' => 46, 'AU' => 47, 'AV' => 48, 'AW' => 49, 'AX' => 50, 'AY' => 51, 'AZ' => 52);    
                    return str_replace(array("\n", '"', '’', '—'), array('', '""', "'", ' '), $dataFirstSimple[$rowNumberToLetter[$matches[1]]]);
                },
                $configurableRow[$key]
            );
        }
        //else use default or simple values
        else
        {
            if(is_array($oneRow))
            {
                $val = preg_replace_callback(
                    '/\{([A-Z]*)\}/',
                    function($matches) use ($dataFirstSimple)
                    {
                            $rowNumberToLetter = array('A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10, 'K' => 11, 'L' => 12, 'M' => 13, 'N' => 14, 'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 'S' => 19, 'T' => 20, 'U' => 21, 'V' => 22, 'W' => 23, 'X' => 24, 'Y' => 25, 'Z' => 26, 'AA' => 27, 'AB' => 28, 'AC' => 29, 'AD' => 30, 'AE' => 31, 'AF' => 32, 'AG' => 33, 'AH' => 34, 'AI' => 35, 'AJ' => 36, 'AK' => 37, 'AL' => 38, 'AM' => 39, 'AN' => 40, 'AO' => 41, 'AP' => 42, 'AQ' => 43, 'AR' => 44, 'AS' => 45, 'AT' => 46, 'AU' => 47, 'AV' => 48, 'AW' => 49, 'AX' => 50, 'AY' => 51, 'AZ' => 52);    
                            return str_replace(array("\n", '"', '’', '—'), array('', '""', "'", ' '), $dataFirstSimple[$rowNumberToLetter[$matches[1]]]);
                    },
                    $oneRow['VALUE']
                );
                    
                $val = str_replace('""', '"', $val);
                $val = str_replace('X', 'x', $val);
                $val = $oneRow[$val];
                $val = str_replace('"', '""', $val);
            }
            else
            {
                $val = preg_replace_callback(
                    '/\{([A-Z]*)\}/',
                    function($matches) use ($dataFirstSimple)
                    {
                        $rowNumberToLetter = array('A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10, 'K' => 11, 'L' => 12, 'M' => 13, 'N' => 14, 'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 'S' => 19, 'T' => 20, 'U' => 21, 'V' => 22, 'W' => 23, 'X' => 24, 'Y' => 25, 'Z' => 26, 'AA' => 27, 'AB' => 28, 'AC' => 29, 'AD' => 30, 'AE' => 31, 'AF' => 32, 'AG' => 33, 'AH' => 34, 'AI' => 35, 'AJ' => 36, 'AK' => 37, 'AL' => 38, 'AM' => 39, 'AN' => 40, 'AO' => 41, 'AP' => 42, 'AQ' => 43, 'AR' => 44, 'AS' => 45, 'AT' => 46, 'AU' => 47, 'AV' => 48, 'AW' => 49, 'AX' => 50, 'AY' => 51, 'AZ' => 52);    
                        return str_replace(array("\n", '"', '’', '—'), array('', '""', "'", ' '), $dataFirstSimple[$rowNumberToLetter[$matches[1]]]);
                    },
                    $oneRow
                );
            }
        }
			
		if($key == 'sku' || $key == 'associated')
		{
			$val = str_replace(' ', '', str_replace(' X ', 'x', $val));
		}
        $result .= '"'.$val.'",';
    }
    $result = substr($result, 0, strlen($result)-1)."\n";
    
    return $result;
}

function createHeaders($config)
{
    include $config;
    
    $result = '';
    foreach($row as $key => $oneRow)
    {
        $result .= '"'.$key.'",';
    }
    $result = substr($result, 0, strlen($result)-1)."\n";
    
    return $result;
}

