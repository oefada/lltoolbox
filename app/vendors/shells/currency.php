<?php
class CurrencyShell extends Shell {
    var $uses = array('Currency', 'CurrencyExchangeRate');
    
    var $api_url = 'http://download.finance.yahoo.com/d/quotes.csv?e=.csv';
    var $api_format = 'sl1d1t1';
    var $currencyCodeList;
    
    function main() {
        $url = $this->prepare_api_url();
        
        $result = $this->fetch_api_data($url);
        
        $this->update_exchange_rates($result);
    }
    
    function prepare_api_url() {
        $currencyList = $this->Currency->find('list', array('fields' => 'currencyCode'));

        $this->currencyCodeList = $currencyList;
        
        $currencyString = '';
        foreach ($currencyList as $currencyCode) {
            if ($currencyCode == 'USD') { continue; }
            $currencyString .= $currencyCode.'USD'.'=X+';
        }
        
        if (empty($currencyString)) {
            die('No currency codes found');
        }
        
        return $this->api_url.'&f='.$this->api_format.'&'.'s='.$currencyString;
    }
    
    function fetch_api_data($url) {
        $handle = @fopen($url, 'r');

        do {
            $result[] = fgets($handle);
        } while (!feof($handle));

        fclose($handle);
        
        return $result;
    }
    
    function update_exchange_rates($result) {
        foreach ($result as $line) {
            if (empty($line)) {
                continue;
            }
            
            
            $parts = explode(',', $line);

            $currencyCode = substr(str_replace('"', '', $parts[0]), 0, 3);
            $currencyId = array_pop(array_keys($this->currencyCodeList, $currencyCode));

            $exchangeRate = $parts[1];
            $date = $parts[2];
            $time = $parts[3];
            
            $phpdate = strtotime( str_replace('"', '', $date).' '.str_replace('"', '', $time) );
            $mysqldate = date( 'Y-m-d H:i:s', $phpdate);
            
            $data = array('currencyId' => $currencyId,
                                                'dailyExchangeRateToDollar' => $exchangeRate,
                                                'asOfDateTime' => $mysqldate);
            
            $existingRate = $this->CurrencyExchangeRate->find('all', array('conditions' => $data));

            //only insert this rate if it's different from an existing one
            if (empty($existingRate)) {
                $sqlAvgWeek = 'SELECT AVG(dailyExchangeRateToDollar) as avg FROM currencyExchangeRate WHERE currencyId = '.$currencyId.' AND asOfDateTime BETWEEN ("'.$mysqldate.'" - INTERVAL 1 WEEK) AND "'.$mysqldate.'"';
                $sqlAvgMonth = 'SELECT AVG(dailyExchangeRateToDollar) as avg FROM currencyExchangeRate WHERE currencyId = '.$currencyId.' AND asOfDateTime BETWEEN ("'.$mysqldate.'" - INTERVAL 1 MONTH) AND "'.$mysqldate.'"';
                $avgWeek = array_pop(array_pop(array_pop($this->CurrencyExchangeRate->query($sqlAvgWeek))));
                $avgMonth = array_pop(array_pop(array_pop($this->CurrencyExchangeRate->query($sqlAvgMonth))));

                $data['weeklyExchangeRateToDollar'] = $avgWeek;
                $data['monthlyExchangeRateToDollar'] = $avgMonth;
                $currencyExchangeRateData[] = $data;
            }
        }
        
        if (!empty($currencyExchangeRateData)) {
            $this->CurrencyExchangeRate->saveAll($currencyExchangeRateData);
        }
    }
}
?>