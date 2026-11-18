<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Tes lama memeriksa teks Indonesia; tes bahasa baru mengosongkan sesi sendiri.
        $this->withSession(['locale' => 'id']);
    }
}
