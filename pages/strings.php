<?php

$str = new StringsDbAdapter($pdo);

echo "Languages:<br />";
var_dump($str->getLangs());

echo "Strings:<br />";

