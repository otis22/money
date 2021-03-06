<?php

declare(strict_types=1);
/**
 * @author Pavel Stepanets <pahhan.ne@gmail.com>
 * @author Artem Dekhtyar <m@artemd.ru>
 */

namespace ElegantBro\Money\Tests\Ensure;

use ElegantBro\Money\Ensure\NotZero;
use ElegantBro\Money\Operations\Minus;
use ElegantBro\Money\Tests\Stub\ThreeEuro;
use ElegantBro\Money\Tests\Stub\ZeroBelarusRuble;
use Exception;
use LogicException;
use PHPUnit\Framework\TestCase;

final class NotZeroTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testPositiveAmountCurrency(): void
    {
        self::assertEquals(
            'EUR',
            ($m = new NotZero(
                new ThreeEuro()
            ))->currency()->asString()
        );

        self::assertEquals(
            '3',
            $m->amount()
        );
    }

    /**
     * @throws Exception
     */
    public function testNegativeAmountCurrency(): void
    {
        self::assertEquals(
            'EUR',
            ($m = new NotZero(
                new Minus(
                    new ThreeEuro()
                )
            ))->currency()->asString()
        );

        self::assertEquals(
            '-3',
            $m->amount()
        );

        self::assertEquals(
            2,
            $m->scale()
        );
    }

    /**
     * @throws Exception
     */
    public function testZero(): void
    {
        $this->expectException(LogicException::class);
        (new NotZero(
            new ZeroBelarusRuble()
        ))->amount();
    }
}
