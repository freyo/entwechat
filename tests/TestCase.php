<?php

namespace EntWeChat\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * class TestCase.
 */
class TestCase extends BaseTestCase
{
    /**
     * Tear down the test case.
     */
    public function tearDown()
    {
        $this->finish();
        parent::tearDown();
        if ($container = \Mockery::getContainer()) {
            $this->addToAssertionCount($container->Mockery_getExpectationCount());
        }
        \Mockery::close();
    }

    /**
     * Run extra tear down code.
     */
    protected function finish()
    {
        // call more tear down methods
    }
}
