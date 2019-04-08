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

        larapush_rescue(function () use ($request) {
            Remote::runPreScripts($request->input('transaction'));
        }, function ($exception) {
            return response_payload(false, ['message'=> $exception->getMessage()]);
        });

        return response_payload(true);
    }
}
