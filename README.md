## UTS - WEB SERVICE
### Oleh : Arbi Syarifudin (12181630)

___

### Soal & Ketentuan : 
#### Membuat restful API dengan ketentuan sebagai berikut :
- Resource minimal 2 dan saling berelasi ada implementasi HATEOAS pada method GET
contoh (mobil dengan merek, buku dengan pengarang, dll )
- Tidak diperkenankan menggunakan database northwind, silahkan usaha sendiri :)
- Terdapat method GET seluruh data dan berdasarkan ID, method POST, PUT, dan DELETE
- Terdapat response kode error dan keterangannya


#### Cara menilaikan :
- Upload kode anda pada social repository seperti : github, gitlab, atau bitbucket. link repositorynya yang dikirim
- Demokan mulai dari GET,POST,PUT, dan DELETE dengan cara merekam dan mengunggah ke youtube. rekaman disertai screen wajah, pada deskripsi video berikan identitas nim dan nama Anda. Selanjutnya link youtube dan link repository kirim ulang ke sini.

### Skor Penilaian :
- Upload program : 50
- Mendemokan dengan video di youtube : 50
___


### Cuplikan 
![](screenshot1.png)
___


### Skema Database
#### tb_kategori
```
- id_kategori * Primary, INT, Auto Increment
- nama_kategori VARCHAR
```
#### tb_produk
```
- id_produk * Primary, INT, Auto Increment
- nama_produk VARCHAR
- harga_produk INT
- stok_produk INT
- id_kategori ** Index, INT
```
##### keterangan :
* \* Primary Key
* \*\* Foreign Key
___


### Cara menjalankan?
- Pastikan ```composer``` dan ```local webserver``` (xampp/wampp/dkk) sudah terinsal
- Lalu,
```
composer install
```
```
php -S localhost:8080 -t public
```
___


### Download [Postman Collection](https://www.postman.com/collections/bb8eb3d1b778c7a372a2).
