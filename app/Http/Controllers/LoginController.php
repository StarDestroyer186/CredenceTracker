<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        $validatedData = $request->validate([
            "iduser" => "required",
            "idpass" => "required",
            "idmap" => "required",
            "timezone" => "required"
        ]);

        $timezone = $validatedData['timezone'];
        $iduser = $validatedData['iduser'];
        $idpass = $validatedData['idpass'];
        $idmap = $validatedData['idmap'];

        $user = User::where(['LOGIN_NAME' => [$validatedData['iduser']], 'LOGIN_PASS' => [$validatedData['idpass']]])->first();
        $token = $user->createToken('auth_token')->accessToken;

        $data = DB::table('sys_user as u')
            ->leftJoin('web_default as w', 'u.user_id', '=', 'w.user_id')
            ->leftJoin('sys_user_role as ur', 'u.user_id', '=', 'ur.user_id')
            ->leftJoin('sys_role as r', 'ur.role_id', '=', 'r.role_id')
            ->select(
                'u.user_id as uid',
                'u.user_name as uname',
                'u.login_pass as pass',
                'u.email',
                DB::raw("CONVERT(VARCHAR(5), CONVERT(TIME, dbo.fn_to_client_time(DATEADD(MINUTE, ISNULL(u.mail_offset, 0), '19000101'), $timezone * 60), 20)) as rtime"),
                'u.mail_report as rmail',
                'u.mail_type as mtype',
                'u.valid',
                'r.role_name as rname',
                'w.def_lat as lat',
                'w.def_lng as lng',
                'w.def_zoom as zoom',
                'w.def_fit_bounds as fit',
                'w.def_collapsed_group as collapsed',
                DB::raw("ISNULL(w.def_asset_infos, '1,2,3,4,5,6,7,8,9,10,11') as assetInfos"),
                'w.def_page as page',
                'w.push_notification as puno',
                'w.def_show as show',
                'w.show_zone as zone',
                'w.show_marker as marker',
                'w.def_date_fmt as date_fmt',
                'w.def_time_fmt as time_fmt',
                'w.def_sound_alarm as sond_alarm',
                'w.def_popup_alarm as popup_alarm',
                'w.unit_distance as ud',
                'w.unit_fuel as uf',
                'w.unit_temperature as ut',
                'w.unit_speed as us',
                'w.unit_altitude as ua',
                'w.unit_tpms as up',
            )
            ->addSelect(DB::raw("(SELECT COUNT(*) FROM sys_user as uu WHERE uu.user_id = u.user_id) as pno"))
            ->where('u.login_name', $iduser)
            ->get();

        $objectCount = DB::table('sys_object_kind')->selectRaw('count(*) as okind')->get();

        if ($data && $objectCount) {
            $userData = $data[0];
            $objectCount = $objectCount[0];

            // dd($userData->pno,$userData->valid);

            if ($userData->pno > 0 && (int)$userData->valid != 1) {
                return response()->json([
                    'message' => 'Stopped',
                    'status' => 0
                ]);
            } elseif ($userData->pass != $idpass) {
                return response()->json([
                    'message' => 'Incorrect Password',
                    'status' => 0
                ]);
            } else {
                return response()->json([
                    'token' => $token,
                    'userdata' => $userData,
                    'message' => "Logged in successfully",
                    'status' => 1
                ]);
            }
        }
    }

    public function logout(Request $request)
{
    $request->user()->token()->revoke();
    return response()->json([
        'message' => 'Logged out successfully',
        'status' => 1,
    ]);
}

    public function findUser($id)
    {
        $user = User::where('USER_ID', $id)->first();
        if (is_null($user)) {
            return response()->json([
                'data' => null,
                'message' => 'data not found',
                'status' => 0
            ]);
        } else {
            return response()->json([
                'data' => $user,
                'message' => 'User Found',
                'status' => 1
            ]);
        }
    }
}
