<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

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
          'rel'   => 'Detail Produk',
          'href'  => '/api/produk/' . $item->id_produk,
          'type'  => 'GET'
        ],
        [
          'rel'   => 'Detail Kategori',
          'href'  => '/api/kategori/' . $item->id_kategori,
          'type'  => 'GET'
        ]
      ];
    }

    $data = [
      'code'    => 200,
      'message' => 'Data semua Produk berhasil diambil!',
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
        'code'    => 404,
        'message' => 'Data tidak ditemukan!',
      ];
      return response()->json($data, 404);
    }

    $produk = $query->join('tb_kategori', 'tb_kategori.id_kategori', '=', 'tb_produk.id_kategori')
              ->first();

    $produk->_links = [
      [
        'href'  => '/api/kategori/' . $produk->id_kategori,
        'rel'   => 'Detail Kategori',
        'type'  => 'GET'
      ]
    ];

    $data = [
      'code'    => 200,
      'message' => 'Detail Produk berhasil diambil!',
      'data'    => $produk,
    ];

    return response()->json($data, 200);
  }

  public function tambah(Request $request) {

    $validation = Validator::make($request->all(), [
      'nama_produk' => 'required|unique:tb_produk,nama_produk',
      'harga_produk' => 'required',
      'stok_produk' => 'required',
      'id_kategori' => 'required|exists:tb_kategori,id_kategori',
    ], $this->_error_messages());

    if ($validation->fails())
    {
      return response()->json([
        'code' => 422,
        'message' => 'Permintaan tidak valid!',
        'errors' => $validation->errors()
      ], 422);
    }

    $data_baru = [
      'nama_produk' => $request->nama_produk,
      'harga_produk' => $request->harga_produk,
      'stok_produk' => $request->stok_produk ? $request->stok_produk : 0,
      'id_kategori' => $request->id_kategori,
    ]; 

    DB::table('tb_produk')->insert($data_baru);

    $data = [
      'code'    => 201,
      'message' => 'Data produk berhasil ditambah!',
    ];

    return response()->json($data, 201);
  }

  public function ubah(Request $request, $id)
  {

    $validation = Validator::make($request->all(), [
      'nama_produk' => 'required|unique:tb_produk,nama_produk,' . $id . ',id_produk',
      'harga_produk' => 'required',
      'stok_produk' => 'required',
      'id_kategori' => 'required|exists:tb_kategori,id_kategori',
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

    $data_ubah = [
      'nama_produk' => $request->nama_produk,
      'harga_produk' => $request->harga_produk,
      'stok_produk' => $request->stok_produk,
      'id_kategori' => $request->id_kategori,
    ];

    DB::table('tb_produk')->where('id_produk', $id)->update($data_ubah);

    $data = [
      'code'    => 200,
      'message' => 'Data produk berhasil diperbarui!',
    ];

    return response()->json($data, 200);
  }

  public function hapus($id)
  {

    // cek data
    $query = DB::table('tb_produk')->where('id_produk', $id);

    if ($query->count() < 1) {
      $data = [
        'code'    => 404,
        'message' => 'Data produk tidak ditemukan!',
      ];
      return response()->json($data, 404);
    }

    $query->delete();

    $data = [
      'code'    => 200,
      'message' => 'Data produk berhasil dihapus!',
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
