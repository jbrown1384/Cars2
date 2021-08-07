<p align="center">Dealer Inspire Insurance Coding Challenge</p>

## Github
https://github.com/jbrown1384/Cars2

## Application
<p>Using a given data set of insurance information. Produce an aggregate total of the column `tiv_2012` by `county` and by `line` and place into a file named output.json</p>

## Execution
```
php index.php
```

## CSV Input File
<p>The input file can be found in the <a href="https://github.com/jbrown1384/Cars2/tree/master/public">public directory</a></p>

## JSON Output File
<p>The JSON output file can be found in the <a href="https://github.com/jbrown1384/Cars2/tree/master/output">output directory</a></p>

<p><b>Example output format:</b></p>


```
"county": {
        "ALACHUA COUNTY": {
            "tiv_2012": 12345.6789
        },
        "BAKER COUNTY": {
            "tiv_2012": 12345.6789
        },
        "BAY COUNTY": {
            "tiv_2012": 12345.6789
        },
        etc...
    },
    "line": {
        "Commercial": {
            "tiv_2012": 12345.6789
        },
        etc...
    }
}
```
