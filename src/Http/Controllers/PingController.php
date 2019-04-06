<?php

namespace Laraning\Larapush\Http\Controllers;

use Laraning\Larapush\Abstracts\RemoteBaseController;

final class PingController extends RemoteBaseController
{
    public function __invoke()
    {
        return response_payload(true);
    }
}
