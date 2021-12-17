<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Validator;

class KategoriController extends Controller
{

  public function tampil()
  {
  
    $kategori = DB::table('tb_kategori')->get();

    $tgl_diperbarui = $kategori[0]->tgl_diperbarui;

    $etag = hash('sha256', $tgl_diperbarui);

    Cache::put($etag, $kategori, 300);

    if (isset($_SERVER["HTTP_IF_NONE_MATCH"]) && $_SERVER["HTTP_IF_NONE_MATCH"] == $etag) {
      return response('', 304, ['ETag' => $etag, 'Cache-Control' => 'must-revalidate']);
    } else {

      foreach ($kategori as $key => $item) {
        $kategori[$key] = $item;
        $kategori[$key]->_links = [
          [
            'rel'   => 'Detail kategori',
            'href'  => '/api/kategori/' . $item->id_kategori,
            'type'  => 'GET'
          ]
        ];
      }
  
      $data = [
        'code'    => 200,
        'message' => 'Data semua kategori berhasil diambil!',
        'data'    => $kategori,
      ];
  
      return response()->json($data, 200, ['ETag' => $etag, 'Cache-Control' => 'must-revalidate']);
    }
            
   
  }

  public function detail($id)
  {
    // cek data
    $query = DB::table('tb_kategori')->where('id_kategori', $id);

    if ($query->count() < 1) {
      $data = [
        'code'    => 404,
        'message' => 'Data tidak ditemukan!',
      ];
      return response()->json($data, 404);
    }

    $kategori = $query->first();

    $data = [
      'code'    => 200,
      'message' => 'Detail kategori berhasil diambil!',
      'data'    => $kategori,
    ];

    return response()->json($data, 200);
  }

  public function tambah(Request $request) {

    $validation = Validator::make($request->all(), [
      'nama_kategori' => 'required|unique:tb_kategori,nama_kategori',
    ], $this->_error_messages());

    if ($validation->fails())
    {
      return response()->json([
        'code' => 422,
        'message' => 'Permintaan tidak valid!',
        'errors' => $validation->errors()
      ], 422);
    }

    $authHeader = $request->header('Authorization');
    // Bearer aksladdssdhd
    $arr = explode(" ", $authHeader);
    $jwt = isset($arr[1]) ? $arr[1] : "";
    $secretkey = base64_encode("rahasia");

    if ($jwt) {

      try {
        
        $decoded = JWT::decode($jwt, $secretkey, array("HS256"));
    
        DB::table('tb_kategori')->insert(['nama_kategori' => $request->nama_kategori]);
    
        $data = [
          'code'    => 201,
          'message' => 'Data kategori berhasil ditambah!',
        ];
    
        return response()->json($data, 201);



      } catch (\Exception $e) {
        
        return response()->json([
          "code" => 401,
          "message" => "Akses dilarang!",
        ], 401);

      }

    } else {
      return response()->json([
        "code" => 401,
        "message" => "Akses dilarang!",
      ], 401);
    }

  }

  public function ubah(Request $request, $id)
  {

    $validation = Validator::make($request->all(), [
      'nama_kategori' => 'required|unique:tb_kategori,nama_kategori,' . $id . ',id_kategori',
    ],
      $this->_error_messages()
    );

    if ($validation->fails()) {
      return response()->json([
        'code' => 422,
        'message' => 'Permintaan tidak valid!',
        'errors' => $validation->errors()
      ], 422);
    }

    DB::table('tb_kategori')->where('id_kategori', $id)
    ->update(['nama_kategori' => $request->nama_kategori]);

    $data = [
      'code'    => 200,
      'message' => 'Data kategori berhasil diperbarui!',
    ];

    return response()->json($data, 200);
  }

  public function hapus($id)
  {
    // cek data
    $query = DB::table('tb_kategori')->where('id_kategori', $id);

    if ($query->count() < 1) {
      $data = [
        'code'    => 404,
        'message' => 'Data kategori tidak ditemukan!',
      ];
      return response()->json($data, 404);
    }

    $query->delete();

    $data = [
      'code'    => 200,
      'message' => 'Data kategori berhasil dihapus!',
    ];

    return response()->json($data, 200);
  }

  private function _error_messages() {
    return  [
      'nama_kategori.required' => 'Nama kategori diperlukan.',
      'nama_kategori.unique' => 'Nama kategori sudah ada.',
    ];
  }
  
}
