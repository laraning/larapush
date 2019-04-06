<?php

namespace Laraning\Larapush\Http\Controllers;

use Illuminate\Http\Request;
use Laraning\Larapush\Support\Remote;
use Illuminate\Support\Facades\Validator;
use Laraning\Larapush\Abstracts\RemoteBaseController;

final class PreScriptsController extends RemoteBaseController
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction' => 'required',
        ]);

        if ($validator->fails()) {
            return response_payload(false, ['message'=> $validator->errors()->first()], 201);
        }

        Remote::runPreScripts($request->input('transaction'));

        return response_payload(true);
    }
}
