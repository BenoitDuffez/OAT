<?php

header('Location: '.preg_replace("#^(.*)/screenshots.*\$#", "\$1/", $_SERVER['REQUEST_URI']));

