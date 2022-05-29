<?php

namespace App\Http\Controllers;

use App\Jobs\DropboxSyncJob;
use App\Jobs\GoogleDriveSyncJob;
use App\Jobs\OneDriveSyncJob;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * Creates record in module by users mapping
     * @bodyParam token string required users jwt token
     * @bodyParam id integer required
     * @response {
     *  "Status": 0,
     *  "Msg": "convert queued",
     * }
     *
     * @response 400 {
     *  "Status":1,
     *  "Msg":"Missed require fields",
     *  "missed_fields":["id"],
     * }
     */
    public function sync(Request $request)
    {
        $user = $request->auth;

        dispatch(new DropboxSyncJob(['data' => $request->except('token')], $user));

        return response()->json(['status' => true, 'message' => 'record create queued'], 200);
    }

    public function syncGoogleDrive(Request $request)
    {
        $this->validate($request, [
            'module' => 'required',
            'id' => 'required',
            'token' => 'required',
            'directory' => 'required',
            'baseroot' => 'required',
        ]);

        dispatch(new GoogleDriveSyncJob($request->toArray(), $request->auth));

        return response()->json(array(
            'status' => true,
            'message' => 'job for moving attachment from Zoho to Google Drive is queued'
        ));
    }

    public function syncOneDrive(Request $request)
    {
        $this->validate($request, [
            'module' => 'required',
            'id' => 'required',
            'token' => 'required',
            'directory' => 'required',
        ]);

        dispatch(new OneDriveSyncJob($request->toArray(), $request->auth));

        return response()->json(array(
            'status' => 'success',
            'message' => 'Job to move attachments from Zoho to OneDrive was queued'
        ));
    }
}
