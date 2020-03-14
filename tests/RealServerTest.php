<?php

namespace Spatie\EventServer\Tests;

use App\Domain\Account\AccountAggregateRoot;
use App\Domain\Account\Entities\Account;
use Exception;
use Spatie\EventServer\Tests\Fakes\TestAggregateRoot;

class RealServerTest extends ServerTestCase
{
    /** @test */
    public function real_server()
    {
        $server = $this->startServer();

        $uuid = uuid();

        $aggregate = new TestAggregateRoot($uuid);

        $aggregate->increase(10);

        $aggregate = TestAggregateRoot::find($uuid);

        $this->assertEquals(10, $aggregate->balance);

        $server->stop();

        $server = $this->startServer();

        $aggregate = TestAggregateRoot::find($uuid);

        $this->assertEquals(10, $aggregate->balance);

        $server->stop();
    }

    /** @test */
    public function app_test()
    {
        $this->startServer(true);

        $aggregateRoot = AccountAggregateRoot::new()->createAccount('Brent');

        $aggregateRoot->addMoney(100);
        $aggregateRoot->addMoney(100);
        $aggregateRoot->addMoney(100);

        $account = Account::find($aggregateRoot->uuid);

        $this->assertEquals(300, $account->balance);

        try {
            $aggregateRoot->subtractMoney(5000);
        } catch (Exception $exception) {
            // do nothing
        }

        try {
            $aggregateRoot->subtractMoney(5000);
        } catch (Exception $exception) {
            // do nothing
        }

        try {
            $aggregateRoot->subtractMoney(5000);
        } catch (Exception $exception) {
            // do nothing
        }
    }
}
