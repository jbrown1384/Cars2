<?php

class InsuranceParser {

    private array $targetColumns = array('county', 'tiv_2012', 'line');
    private array $csvRowData = array();
    private array $outputData = array();
    private String $filename;
    private $fileHandler;

    /**
     * Sets the file name that was instantiated with the class and proceeds to retrieve and output the data. 
     *
     * @param String $filename
     */
    public function __construct(String $filename) {
        $this->filename = $filename;
        if($this->retrieveFileData()) {
            $this->outputDataFile();
        }
    }

    /**
     * Determines that the file is accessible and the data is in a format that we can parse. 
     *
     * @return Bool     True when output data has been retrieved, False when the csv could not be parsed or was empty
     */
    private function retrieveFileData(): Bool {

        if($this->formatCsvData()) {
            $this->outputData = $this->generateOutputResponse();
        }

        return (!empty($this->outputData));
    }

    /**
     * Parses the csv and associates each row of data to their corresponding column header
     *
     * @return Bool     True when output data has been grouped into column values, False when no data is found   
     */
    private function formatCsvData(): Bool {
        ini_set('auto_detect_line_endings', true);
        $headerKeysFound = false;
		$headerKeys = array();

        if (file_exists($this->filename) && ($fileHandler = fopen($this->filename, "rb")) !== false) {
            $this->fileHandler = $fileHandler;
        }

        if (!$this->fileHandler) {
            throw new Exception("Could not locate file specified");
        }

        
		while (($data = fgetcsv($this->fileHandler)) !== false) {
            if(!$headerKeysFound) {
				$headerKeys = $data;
				$headerKeysFound = true;
				
                continue;
			}

			$formattedData = array_combine($headerKeys, $data);

            $insuranceData = array();
			foreach($this->targetColumns as $column) {
				$insuranceData[$column] = $formattedData[$column];
			}

			$this->csvRowData[] = $insuranceData;
		}

        return (!empty($this->csvRowData));
    }

    /**
     * Function that will generate the entire output array for county and line
     *
     * @return Array    array containing the formatting and data calculations needed to output to our json file
     */
    private function generateOutputResponse(): Array {
        return array(
			"county" => $this->calculateTargetResults('county'),
			"line" => $this->calculateTargetResults('line'),
		);
    }

    /**
     * This function will group the data into distinct column values and sum the total of the tiv_2012
     * column associated to each of those distinct values.
     *
     * @param String $key   CSV column that we are aggrogating totals
     * @return Array        Contains a sum of all tiv_2012 values, grouped by distinct values from the column key past to the function
     */
    function calculateTargetResults(String $key): Array
	{
		$aggrogateData = array_reduce($this->csvRowData, function ($row, $index) use ($key) { 
			if(!array_key_exists($index[$key], $row)) {
				$row[$index[$key]] = array(
					'tiv_2012' => 0
				);
			}

			$row[$index[$key]]['tiv_2012'] += (isset($row[$index['tiv_2012']]) ? $row[$index['tiv_2012']] : 0) + $index['tiv_2012'];
			return $row; 
   		}, array());  

		ksort($aggrogateData);

		return $aggrogateData;
	} 

    /**
     * This function json encodes the output data and places it into an output.json file in the public directory
     *
     * @return Bool     True when the file can be closed, False when there was an issue generating the file
     */
    private function outputDataFile(): Bool {
        if(($file = fopen('output/output.json', 'w')) !== false) {
            fwrite($file, json_encode($this->outputData, JSON_PRETTY_PRINT));
        }

        return fclose($file);
    }


    /**
     * Destroy any existing file handler that may remain and reset auto detect line endings
     */
    public function __destruct() {
        ini_set('auto_detect_line_endings', false);

        if($this->fileHandler) {
            fclose($this->fileHandler);
        }
    }
}

?>