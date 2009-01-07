<?php
echo $form->input('Client.stateId', array('type' => 'select', 'label' => 'State'));
echo $ajax->observeField(
               "ClientStateId",
               array(
                  "update"=>"cityChooser",
                  "url"=>"/states/get_cities"
               )
          );
echo $ajax->observeField(
               "ClientStateId",
               array(
                  "update"=>"cityChooser",
                  "url"=>"/states/get_cities",
				  'indicator' => 'spinner'
               )
          );
$arrayKeys = array_keys($stateIds);
$firstStateId = $arrayKeys[0];
$runOnce = $ajax->remoteFunction(
               array(
                  "update"=>"cityChooser",
                  "url"=>"/states/get_cities/$firstStateId"
               )
          );
echo $javascript->codeBlock($runOnce);
?>