<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use \Firebase\JWT\JWT;

class OtorisasiController extends Controller
{

  function login(Request $request)
  {
    $validasi = Validator::make($request->all(), [
      'email' => 'required|email',
      'password' => 'required',
    ]);

    if($validasi->fails()) {
      return response()->json($validasi->messages());
    }

    // cek user dengan email sesuai request
    $check_user = DB::table('tb_user')->where('email_user', $request->email)->first();

    // jika ada, verifikasi passwordnya
    if ($check_user) {
      // verifikasi kesamaan password
      if (password_verify($request->password, $check_user->password_user)) {
        $secret_key = base64_encode('rahasia');
        $issuer_claim = 'Tugas Web Service';
        $audience_claim = 'Arbi Syarifudin';
        $issued_at_claim = time();
        $not_before_claim = $issued_at_claim + 10;
        $expire_claim = $issued_at_claim + 84600;
        
        $token = [
          'iss' => $issuer_claim,
          'aud' => $audience_claim,
          'iat' => $issued_at_claim,
          'nbf' => $not_before_claim,
          'exp' => $expire_claim,
          'data' => [
            'id' => $check_user->id_user,
            'name' => $check_user->nama_user,
            'email' => $check_user->email_user
          ]
        ];

        $jwt = JWT::encode($token, $secret_key);

        $result = [
          'took' => $_SERVER['REQUEST_TIME_FLOAT'],
          'code' => 200,
          'message' => 'Login success!',
          'token' => $jwt,
        ];

        return response()->json($result, 200);
      } else {

        $result = [
          'took' => $_SERVER['REQUEST_TIME_FLOAT'],
          'code' => 401,
          'message' => 'Invalid email or password!',
          'token' => null,
        ];

        return response()->json($result, 401);

      }
    } else {

      $result = [
        'took' => $_SERVER['REQUEST_TIME_FLOAT'],
        'code' => 401,
        'message' => 'Invalid email or password!',
        'token' => null,
      ];

      return response()->json($result, 401);

    }
  }

  function register(Request $request)
  {
    $validasi = Validator::make($request->all(), [
      'nama' => 'required|min:3',
      'email' => 'required|email',
      'password' => 'required',
    ]);

    if($validasi->fails()) {
      return response()->json($validasi->messages());
    }

    DB::table('tb_user')->insert([
      'nama_user' => $request->nama,
      'email_user' => $request->email,
      'password_user' => password_hash($request->password, PASSWORD_DEFAULT),
    ]);


    $result = [
      'took' => $_SERVER['REQUEST_TIME_FLOAT'],
      'code' => 201,
      'message' => 'User registered succesfully!',
    ];

    return response()->json($result, 201);

  }

}
