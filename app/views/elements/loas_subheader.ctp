<?php
$this->pageTitle = $loa['Client']['name'];
$this->pageTitle.=$html2->c($loa['Client']['clientId'], 'Client Id:');
$this->pageTitle.='<br />';
$this->pageTitle.=$html2->c('manager: '.$client['Client']['managerUsername']);
$this->pageTitle.='<span class="inline-counter"> # of LOAs - ';
$this->pageTitle.=isset($client['Loa'])?count($client['Loa']):'0';
$this->pageTitle.='</span>';
