<?php

namespace PhpJsBehat;

use RuntimeException;

class FakeServer
{
    /**
     * @param mixed $request
     * @return mixed
     */
    public function call($request)
    {
        if ($request === 'foo') {
            return 'bar';
        }

        throw new RuntimeException('Unexpected request');
    }
}
