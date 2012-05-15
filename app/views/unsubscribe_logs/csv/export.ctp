<?php 
	
	// Loop through the data array 
	foreach ($data as $row){ 

		// Loop through every value in a row 
		$outArr=array();
		foreach ($row['UnsubscribeLog'] as &$value){ 
			// Apply opening and closing text delimiters to every value 
			$outArr[]= "\"".$value."\""; 
		} 
		if (isset($row[0])){
			foreach ($row[0] as &$value){ 
				// Apply opening and closing text delimiters to every value 
				$outArr[]= "\"".$value."\""; 
			} 
		}	

		// Echo all values in a row comma separated 
		echo implode(",",$outArr)."\n"; 

	} 

?> 
