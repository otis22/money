<?php

declare(strict_types=1);
/**
 * @author Pavel Stepanets <pahhan.ne@gmail.com>
 * @author Artem Dekhtyar <m@artemd.ru>
 */

namespace ElegantBro\Money;

use ElegantBro\Money\Ensure\IsoCurrency;

function money(string $amount, string $currency, int $scale): Money
{
    return new JustMoney(
        $amount,
        new IsoCurrency($currency),
        $scale
    );
}
