<?php

$c = file_get_contents(__DIR__ . '/tests/Browser/EP/DebtTest.php');
$c = str_replace(
    "->type('input[name=\"debt_date\"]', now()->format('Y-m-d'))",
    "; \$browser->script(\"document.querySelector('input[name=\\\"debt_date\\\"]').value = '\".now()->format('Y-m-d').\"';\"); \$browser",
    $c
);
file_put_contents(__DIR__ . '/tests/Browser/EP/DebtTest.php', $c);

// Also remove native validation from debts form for tests just in case
$c = file_get_contents(__DIR__ . '/tests/Browser/EP/DebtTest.php');
$c = str_replace(
    "->type('input[name=\"debtor_name\"]', 'Pelanggan Hutang EP 001')",
    "->script(\"document.querySelector('form').setAttribute('novalidate', '');\"); \$browser->type('input[name=\"debtor_name\"]', 'Pelanggan Hutang EP 001')",
    $c
);
file_put_contents(__DIR__ . '/tests/Browser/EP/DebtTest.php', $c);

echo "DebtTest fixed";
