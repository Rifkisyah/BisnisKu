<?php

$c = file_get_contents('tests/Browser/EP/CashierTest.php');
$c = str_replace(
    "\$csrfToken = \$browser->visit('/cashier')\n                                  \n                                  ->value('meta[name=\"csrf-token\"]', '');",
    "\$browser->visit('/cashier');",
    $c
);
file_put_contents('tests/Browser/EP/CashierTest.php', $c);
echo "Fixed CashierTest\n";
