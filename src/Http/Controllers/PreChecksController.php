<?php

namespace Laraning\Larapush\Http\Controllers;

use Laraning\Larapush\Support\Remote;
use Laraning\Larapush\Abstracts\RemoteBaseController;

final class PreChecksController extends RemoteBaseController
{
    public function __invoke()
    {
        Remote::preChecks();

        return response_payload(true);
    }
}
