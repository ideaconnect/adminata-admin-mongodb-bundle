<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IDCT\Adminata\DoctrineMongoDB\Tests\Filter;

use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\Query\Expr;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

abstract class FilterWithQueryBuilderTestCase extends TestCase
{
    protected const DEFAULT_FIELD_NAME = 'field';

    /**
     * Created on first use: a test that never touches the builder leaves no
     * mock without expectations behind (a PHPUnit notice).
     *
     * @var (Builder&MockObject)|null
     */
    private ?Builder $queryBuilder = null;

    /**
     * The builder for tests that set expectations on it.
     *
     * @return Builder&MockObject
     */
    protected function getQueryBuilder()
    {
        if (null === $this->queryBuilder) {
            $this->queryBuilder = $this->createMock(Builder::class);
            self::configureQueryBuilder($this->queryBuilder);
        }

        return $this->queryBuilder;
    }

    /**
     * The builder for tests that only hand it to the filter and set no
     * expectations on it.
     *
     * @return Builder&Stub
     */
    protected function getQueryBuilderStub()
    {
        $queryBuilder = static::createStub(Builder::class);
        self::configureQueryBuilder($queryBuilder);

        return $queryBuilder;
    }

    /**
     * @param Builder&Stub $queryBuilder
     */
    private static function configureQueryBuilder(Builder $queryBuilder): void
    {
        // PHPUnit 13 deprecates with() without expects(), and the number of
        // field() calls differs per test (none when the filter stays inactive),
        // so the field name is checked inside the callback instead.
        $queryBuilder
            ->method('field')
            ->willReturnCallback(static function (string $field) use ($queryBuilder): Builder {
                static::assertSame(self::DEFAULT_FIELD_NAME, $field);

                return $queryBuilder;
            });
        $expr = static::createStub(Expr::class);
        $expr
            ->method('field')
            ->willReturnSelf();
        $queryBuilder
            ->method('expr')
            ->willReturn($expr);
    }
}
