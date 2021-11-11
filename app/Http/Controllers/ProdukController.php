<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdukController extends Controller
{

  public function tampil()
  {
    $produk = DB::table('tb_produk')
              ->join('tb_kategori', 'tb_kategori.id_kategori', '=', 'tb_produk.id_kategori')
              ->get();
            
    foreach ($produk as $key => $item) {
      $produk[$key] = $item;
      $produk[$key]->_links = [
        [
          'rel'   => 'detail produk',
          'href'  => '/api/produk/' . $item->id_produk,
          'type'  => 'GET'
        ],
        [
          'rel'   => 'kategori',
          'href'  => '/api/kategori/' . $item->id_kategori,
          'type'  => 'GET'
        ]
      ];
    }

    $data = [
      'took'    => $_SERVER['REQUEST_TIME'],
      'code'    => 200,
      'message' => 'Success',
      'data'    => $produk,
    ];

    return response()->json($data, 200);
  }

  public function detail($id)
  {
    // cek data
    $query = DB::table('tb_produk')->where('id_produk', $id);

    if ($query->count() < 1) {
      $data = [
        'took'    => $_SERVER['REQUEST_TIME'],
        'code'    => 404,
        'message' => 'Data not found!',
      ];
      return response()->json($data, 404);
    }

    $produk = $query->join('tb_kategori', 'tb_kategori.id_kategori', '=', 'tb_produk.id_kategori')
              ->first();

    $produk->_links = [
      [
        'href'  => '/api/kategori/' . $produk->id_kategori,
        'rel'   => 'kategori',
        'type'  => 'GET'
      ]
    ];

    $data = [
      'took'    => $_SERVER['REQUEST_TIME'],
      'code'    => 200,
      'message' => 'Success',
      'data'    => $produk,
    ];

    return response()->json($data, 200);
  }

  public function tambah(Request $request) {

    $this->validate($request, [
      'nama_produk' => 'required|unique:tb_produk,nama_produk',
      'harga_produk' => 'required',
      'stok_produk' => 'required',
      'id_kategori' => 'required|exists:tb_kategori,id_kategori',
    ], $this->_error_messages());

    $data_baru = [
      'nama_produk' => $request->nama_produk,
      'harga_produk' => $request->harga_produk,
      'stok_produk' => $request->stok_produk,
      'id_kategori' => $request->id_kategori,
    ]; 

    DB::table('tb_produk')->insert($data_baru);

    $data = [
      'took'    => $_SERVER['REQUEST_TIME'],
      'code'    => 201,
      'message' => 'Data created successfully!',
    ];

    return response()->json($data, 201);
  }

  public function ubah(Request $request, $id)
  {

    $this->validate($request, [
      'nama_produk' => 'required|unique:tb_produk,nama_produk,' . $id . ',id_produk',
      'harga_produk' => 'required',
      'stok_produk' => 'required',
      'id_kategori' => 'required|exists:tb_kategori,id_kategori',
    ], $this->_error_messages());

    $data_ubah = [
      'nama_produk' => $request->nama_produk,
      'harga_produk' => $request->harga_produk,
      'stok_produk' => $request->stok_produk,
      'id_kategori' => $request->id_kategori,
    ];

    DB::table('tb_produk')->where('id_produk', $id)->update($data_ubah);

    $data = [
      'took'    => $_SERVER['REQUEST_TIME'],
      'code'    => 200,
      'message' => 'Data updated successfully!',
    ];

    return response()->json($data, 200);
  }

  public function hapus($id)
  {

    // cek data
    $query = DB::table('tb_produk')->where('id_produk', $id);

    if ($query->count() < 1) {
      $data = [
        'took'    => $_SERVER['REQUEST_TIME'],
        'code'    => 404,
        'message' => 'Data not found!',
      ];
      return response()->json($data, 404);
    }

    $query->delete();

    $data = [
      'took'    => $_SERVER['REQUEST_TIME'],
      'code'    => 200,
      'message' => 'Data deleted successfully!',
    ];

    return response()->json($data, 200);
  }

  private function _error_messages() {
    return  [
      'nama_produk.required' => 'Nama produk diperlukan.',
      'nama_produk.unique' => 'Nama produk sudah ada.',
      'harga_produk.required' => 'Harga produk diperlukan.',
      'stok_produk.required' => 'Stok produk diperlukan.',
      'id_kategori.required' => 'ID Kategori diperlukan.',
      'id_kategori.exists' => 'ID Kategori tidak ada.',
    ];
  }
  
}
