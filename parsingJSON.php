#!/usr/bin/php

<!-- Dennis Stephens -->
<HTML>
	<HEAD>
		<TITLE>
			Xelkocle
		</TITLE>
	</HEAD>
	<BODY>
		<FORM ACTION="Stephens_p4.php" method="get"> 
			
			
			<SELECT name="category">

			<?php 

				if (file_exists('./DataSources.json')) {
					
					$fileString = file_get_contents('./DataSources.json');

					$json = json_decode($fileString, true);

					foreach($json["categories"] as $key => $val) {

						echo $key;
						echo "<option value='$key'>$key</option>";
					}
				}
				else
					echo "<option>Data Source Not Found.</option>";
				
				
			?>

			</SELECT><br>

			<SELECT name="whichfield">

			<?php	
				if (isset($json)) {
					
					foreach($json["searchterms"] as $key => $val) {
					
						echo "<option value=$key>$key</option>";
					}
				}
				else
					echo "<option>Data Source Not Found.</option>";
			?>
			</SELECT><br>

			<INPUT type="text" name="findme">
			<INPUT type="submit">
		</FORM>

		<?php

				
			if (isset($_GET["category"]) && isset($_GET["whichfield"]) && isset($_GET["findme"])){

				$jsonFile = $json["categories"][$_GET["category"]];
				$whichField = $json["searchterms"][$_GET["whichfield"]];
				$searchTerm = $_GET["findme"];

				
				if(file_exists($jsonFile))

					fileSearch($jsonFile, $whichField, $searchTerm);
				else
					echo "That category does not exist.";	
			}
			else 
				echo "Missing data, please try again!";

			function fileSearch($jsonFile, $whichField, $searchTerm) {

				$fileString = file_get_contents($jsonFile);

				$jsonArray = json_decode($fileString, true);


				$result = recursiveMatch($jsonArray, $whichField, $searchTerm);

				$display = recursivePrint($result);
				echo implode("<br>", $display);
			}
				
			//   https://www.tutorialrepublic.com/php-tutorial/php-json-parsing.php
			function recursivePrint($arr){

				global $values;

				foreach($arr as $key =>$value){

					if(is_array($value)){
						recursivePrint($value);
					}
					else
						$values[] = $value;

				}
				return $values;

			}
			function recursiveMatch($arr, $whichField, $searchTerm) {
   
				return array_reduce(
       					array_keys($arr),
					function ($acc, $key) use ($arr, $whichField, $searchTerm) {
						$value = $arr[$key];
						if(is_array($value)){
							$foo = recursiveMatch($value, $whichField, $searchTerm);
							if(empty($foo)){
								return $acc;
							}
							else {

								$acc[$key] = $foo;
								return $acc;
							}
						}
							
						if ($key === $whichField) {
							if (strpos($value, $searchTerm) !== -1) {
								$acc[$key] = $value;
							
							}
						}
						return $acc;
       					},
       					[]
				);
			}
		?>
	</BODY>
</HTML>
