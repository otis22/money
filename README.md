[![Build Status](https://travis-ci.com/elegant-bro/money.svg?branch=master)](https://travis-ci.com/elegant-bro/money)
[![Coverage Status](https://coveralls.io/repos/github/elegant-bro/money/badge.svg?branch=master)](https://coveralls.io/github/elegant-bro/money?branch=master)

Why we decided to make yet another money library? There are popular solutions:
* https://github.com/moneyphp/money
* https://github.com/brick/money
* https://github.com/akaunting/money

But

1. These libraries have no interfaces for money, 
we can't create custom class which would be the money.

1. Existed implementations are classes with hundreds LoC and dozens of methods
like `add`, `devide`, `compare`, etc. which is essentially a procedural.

1. We cannot add new functionality because we have to extend existed classes
which mostly declared as final.

1. Existed libraries hide manipulation with scale.

## Our solution

At the first our `Money` is an interface, nothing prevents you from implementing
your custom money object eg `UserBalance` which presents user's balance from a database,
ORM entity or even a file.

```php
<?php declare(strict_types=1);
 
use ElegantBro\Money\Ensure\IsoCurrency;
use ElegantBro\Money\Money;
use ElegantBro\Money\Currency;

final class UserBalance implements Money
{
    private $userId;

    private $connection;
    
    private $currency;

    
    public function __construct(int $userId, string $currency, PDO $connection)
    {
        $this->userId = $userId;
        $this->connection = $connection;
        $this->currency = new IsoCurrency($currency);
    }

    /**
     * @inheritDoc
     */
    public function amount(): string
    {
        $stmt =  $this->connection
            ->prepare(
                'SELECT SUM(debit - credit) 
                 FROM transactions 
                 WHERE user_id = ? AND currency = ?'
            );
        $stmt->execute([$this->userId, $this->currency->asString()]);
    
        return $stmt->fetchColumn();
    }

    /**
     * @inheritDoc
     */
    public function currency(): Currency
    {
        return $this->currency;
    }

    /**
     * @inheritDoc
     */
    public function scale(): int
    {
        return 4;
    }
}
```

or you can very simple implementations

```php
<?php declare(strict_types=1);
 
use ElegantBro\Money\Currencies\USD;
use ElegantBro\Money\Money;
use ElegantBro\Money\Currency;

final class FiveDollars implements Money
{   
    private $currency;

    public function __construct()
    {
        $this->currency = new USD();
    }

    /**
     * @inheritDoc
     */
    public function amount(): string
    {
        return '5';
    }

    /**
     * @inheritDoc
     */
    public function currency(): Currency
    {
        return $this->currency;
    }

    /**
     * @inheritDoc
     */
    public function scale(): int
    {
        return 2;
    }
}
```

Secondly, operations with money themselves are money. For example sum of money is object which implements Money.
I think you will not deny that sum of money is money too.

```php
<?php declare(strict_types=1);

use ElegantBro\Money\Operations\SumOf;
use ElegantBro\Money\Operations\Divided;

$s = new SumOf(
    new FiveDollars(),
    new FiveDollars()
);
// $s->amount() === '10.00'
// $s->currency->asString() === 'USD
// $s->scale() === 2


$d = new Divided(
    new FiveDollars(),
    '3', // denominator
    4    // scale
);
// $d->amount() === '1.6666'
// $d->currency->asString() === 'USD
// $d->scale() === 4
```

So you can easily add operation need for your application.



# Tests

#### Build container

`docker build --build-arg VERSION=7.4 --tag elegant-bro/money:7.4 ./docker/`

#### Install dependencies

`docker run --rm -ti -v $PWD:/app elegant-bro/money:7.4 install`

#### Run tests

`docker run --rm -ti -v $PWD:/app -w /app elegant-bro/money:7.4 vendor/bin/phpunit`

#### Test code style
`docker run --rm -ti -v $PWD:/app -w /app elegant-bro/money:7.4 vendor/bin/ecs --level psr12 check src`
