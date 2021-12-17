<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OtorisasiController extends Controller {


  public function register(Request $request)
  {
    
    DB::table('tb_user')->insert([
      'nama_user' => $request->nama,
      'email_user' => $request->email,
      'password_user' => password_hash($request->password, PASSWORD_DEFAULT)
    ]);

    $result = [
      'code' => 201,
      'message' => 'Registrasi user berhasil!',
    ];

    return response()->json($result, 201);


  }

  public function login(Request $request) {

    // cek apakah ada user dengan email tersebut
    $data_user = DB::table('tb_user')->where('email_user', $request->email)
    ->first();

    // jika ada user
    if($data_user) {

      // verifikasi password
      if(password_verify($request->password, $data_user->password_user)) {

        // kalau password cocok, buat TOKEN
        $secret_key = base64_encode("rahasia");
        $issuer_claim = "Tugas Web Service";
        $audience_claim = "Arbi Syarifudin - 12181630";
        $issued_at_claim = time();
        $not_before_claim = $issued_at_claim + 10;
        $expired_claim = $issued_at_claim + 86400;

        $token = [
          "iss" => $issuer_claim,
          "aud" => $audience_claim,
          "iat" => $issued_at_claim,
          "nbf" => $not_before_claim,
          "exp" => $expired_claim,
          "data" => [
            "id" => $data_user->id_user,
            "nama" => $data_user->nama_user,
            "email" => $data_user->email_user,
          ]
        ];

        // encode jwt token dari data diatas
        $jwt = JWT::encode($token, $secret_key);

        // kasih response sukses ke client
        return response()->json([
          'code' => 200,
          'message' => 'Login sukses!',
          'token' => $jwt
        ], 200);

      } else {

        // kalau password tidak cocok
        return response()->json([
          'code' => 401,
          'message' => 'Email / password salah!',
        ], 401);

      }

    } else {

      // jika user dgn email trsbt tidak ada
      return response()->json([
        'code' => 401,
        'message' => 'Email / password salah!',
      ], 401);

    }



  }

}