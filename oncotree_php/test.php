<?php
	$files = glob("C:/xampp_7/htdocs/oncotree_php/DX_txt/*.txt");
    $output = "result.txt";

    foreach($files as $file) {
        $content = file_get_contents($file);
        echo $content . "<br>";
    }
?>