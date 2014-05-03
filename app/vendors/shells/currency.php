<?php
class CurrencyShell extends Shell
{
    public $uses = array('Currency', 'CurrencyExchangeRate');

    public $api_url = 'http://download.finance.yahoo.com/d/quotes.csv?e=.csv';
    public $api_format = 'sl1d1t1';
    public $currencyCodeList;

    /**
     *
     */
    public function main()
    {
        $url = $this->prepare_api_url();

        $result = $this->fetch_api_data($url);

        $newRates = $this->update_exchange_rates($result);
        // email when no new entries made
        $dayOfWeek = date('w');
        if ($newRates == 0 && $dayOfWeek != 0 && $dayOfWeek != 6) {
            $this->sendEmailNoEntries();
        }
    }

    /**
     * @return string
     */
    public function prepare_api_url()
    {
        $currencyList = $this->Currency->find('list', array('fields' => 'currencyCode'));

        $this->currencyCodeList = $currencyList;

        $currencyString = '';
        foreach ($currencyList as $currencyCode) {
            if ($currencyCode == 'USD') {
                continue;
            }
            $currencyString .= $currencyCode . 'USD' . '=X+';
        }

        if (empty($currencyString)) {
            die('No currency codes found');
        }

        return $this->api_url . '&f=' . $this->api_format . '&' . 's=' . $currencyString;
    }

    /**
     * @param $url
     * @return array
     */
    public function fetch_api_data($url)
    {
        $handle = @fopen($url, 'r');

        do {
            $result[] = fgets($handle);
        } while (!feof($handle));

        fclose($handle);

        return $result;
    }

    /**
     * @param $result
     * @return int
     */
    public function update_exchange_rates($result)
    {

        $dayOfWeek = date('w');
        $sqlCountToday = "SELECT COUNT(*) as nbr FROM currencyExchangeRate WHERE DATE_FORMAT(created, '%Y-%m-%d') = '" . date('Y-m-d') . "'";
        $countToday = array_pop(array_pop(array_pop($this->CurrencyExchangeRate->query($sqlCountToday))));
        if ($countToday == 0 && $dayOfWeek != 0 && $dayOfWeek != 6) {
            $this->sendEmailNoneToday($sqlCountToday);
        }

        $recordCount = 0;

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

            $phpdate = strtotime(str_replace('"', '', $date) . ' ' . str_replace('"', '', $time));
            $mysqldate = date('Y-m-d H:i:s', $phpdate);


            $data = array('currencyId' => $currencyId,
                'dailyExchangeRateToDollar' => $exchangeRate,
                'asOfDateTime' => $mysqldate);

            $existingRate = $this->CurrencyExchangeRate->find('all', array('conditions' => $data));

            //only insert this rate if it's different from an existing one
            if (empty($existingRate) && floatval($exchangeRate) > 0) {
                $sqlAvgWeek = 'SELECT AVG(dailyExchangeRateToDollar) as avg FROM currencyExchangeRate WHERE currencyId = ' . $currencyId . ' AND asOfDateTime BETWEEN ("' . $mysqldate . '" - INTERVAL 1 WEEK) AND "' . $mysqldate . '"';
                $sqlAvgMonth = 'SELECT AVG(dailyExchangeRateToDollar) as avg FROM currencyExchangeRate WHERE currencyId = ' . $currencyId . ' AND asOfDateTime BETWEEN ("' . $mysqldate . '" - INTERVAL 1 MONTH) AND "' . $mysqldate . '"';
                $avgWeek = array_pop(array_pop(array_pop($this->CurrencyExchangeRate->query($sqlAvgWeek))));
                $avgMonth = array_pop(array_pop(array_pop($this->CurrencyExchangeRate->query($sqlAvgMonth))));

                $data['weeklyExchangeRateToDollar'] = $avgWeek;
                $data['monthlyExchangeRateToDollar'] = $avgMonth;
                if (floatval($avgWeek) > 0 && floatval($avgMonth) > 0) {
                    $currencyExchangeRateData[] = $data;
                }
            }
        }

        if (!empty($currencyExchangeRateData)) {
            $this->CurrencyExchangeRate->saveAll($currencyExchangeRateData);
            $recordCount++;
        }

        return $recordCount;
    }

    /**
     *
     */
    public function sendEmailNoEntries()
    {
        $emailTo = 'jwoods@luxurylink.com';
        $emailSubject = "Toolbox CurrencyShell did not insert any records.";
        $emailHeaders = "From: LuxuryLink.com DevMail<devmail@luxurylink.com>\r\n";
        $emailBody = 'no records inserted: ' . date('m/d/Y g:i:s a');
        @mail($emailTo, $emailSubject, $emailBody, $emailHeaders);
    }

    /**
     * @param $sql
     */
    public function sendEmailNoneToday($sql)
    {
        $emailTo = 'jwoods@luxurylink.com';
        $emailSubject = "Toolbox CurrencyShell running for " . date('Y-m-d');
        $emailHeaders = "From: LuxuryLink.com DevMail<devmail@luxurylink.com>\r\n";
        @mail($emailTo, $emailSubject, $sql, $emailHeaders);
    }
}
