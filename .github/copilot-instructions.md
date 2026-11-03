# PROJECT_SPEC: Marketplace Hasil Bumi + Website Ekspor

Dokumen ini adalah acuan utama untuk AI copilot. Baca seluruhnya sebelum membuat kode. Jangan menambah fitur di luar dokumen ini tanpa konfirmasi.

## 1. Ringkasan

Satu project (monolith) berisi dua tampilan publik dan dua dashboard, memakai satu database dan satu sistem login:

1. Marketplace (customer lokal, bahasa Indonesia, Rupiah)
2. Website ekspor (buyer luar negeri, bahasa Inggris, USD, tanpa login)
3. Dashboard petani (penjual)
4. Panel master admin (1 akun)

Model: multi-vendor seperti Shopee. Admin menyediakan platform, petani membuka toko dan posting produk, customer membeli.

## 2. Tech stack (bisa diganti, putuskan di awal)

- Backend dan frontend: Laravel 11 (PHP 8.3), Blade + Tailwind CSS + Alpine.js
- Database: MySQL 8
- Auth: Laravel Breeze, dengan kolom `role` pada tabel users
- Upload file: Laravel Storage (lokal dulu, S3 nanti)
- Multibahasa: Laravel localization (`id`, `en`)

Catatan: jika tim lebih nyaman dengan stack lain (misalnya Next.js + Prisma + PostgreSQL), ganti bagian ini saja. Struktur peran, modul, dan database di bawah tetap berlaku.

## 3. Peran dan hak akses

| Role | Jumlah | Akses |
|---|---|---|
| `admin` | 1, dibuat lewat seeder, tidak ada form daftar | Semua modul di `/admin` |
| `farmer` | Tidak terbatas, daftar sendiri, perlu verifikasi admin | Dashboard di `/petani`, hanya data miliknya |
| `customer` | Tidak terbatas, daftar sendiri | Belanja dan "Akun saya" |
| Buyer ekspor | Tanpa akun | Hanya lihat katalog dan kirim RFQ |

Aturan:
- Satu akun boleh menjadi farmer sekaligus customer (tombol pindah mode).
- Gunakan middleware `role:admin`, `role:farmer`. Petani A tidak boleh melihat atau mengubah data petani B (gunakan Policy).
- Satu halaman login untuk semua. Setelah login, arahkan sesuai role: admin ke `/admin`, farmer ke `/petani`, customer ke marketplace.
- Akun admin wajib 2FA.

## 4. Struktur modul

Pisahkan kode per modul di dalam satu project:

```
app/
  Modules/
    Auth/
    Marketplace/     (katalog, keranjang, checkout, pra-order, akun customer)
    Farmer/          (dashboard petani, toko, produk, pesanan)
    Admin/           (panel master admin)
    Export/          (website ekspor, RFQ)
    Shared/          (model, service, helper yang dipakai bersama)
```

Model dan database dipakai bersama. Jangan duplikasi data produk antar modul.

## 5. Database (tabel inti)

- `users`: id, name, email, password, role (admin|farmer|customer), phone, customer_type (individual|business), is_active
- `farmer_profiles`: user_id, shop_name, slug, group_name (poktan), region, story, photo, verified_at
- `categories`: id, name, slug (sayur, buah, rempah, beras, dll)
- `products`: id, farmer_id, category_id, name, slug, description, unit (kg|ton), price_idr, stock, status (draft|pending|approved|rejected), show_in_marketplace (bool), show_in_export (bool)
- `product_images`: product_id, path, sort
- `harvest_schedules`: product_id, harvest_date, quota, status (open|closed) untuk pra-order
- `product_export_details`: product_id, name_en, description_en, hs_code, moisture_percent, size_spec, moq, monthly_capacity, incoterms, price_usd, loading_port
- `certificates`: id, name (Organic, Halal, HACCP, Phytosanitary, dll), file_path
- `product_certificate`: product_id, certificate_id
- `orders`: id, customer_id, status, payment_method, payment_term (cash|net_14|net_30), total, shipping_address, courier, tracking_number
- `order_items`: order_id, product_id, farmer_id, qty, price, is_preorder
- `payments`: order_id, method (transfer|ewallet|qris), status, paid_at, reference
- `disputes`: order_id, reason, status, admin_note
- `rfqs`: id, buyer_name, company, country, email, product_id, quantity, incoterms, message, status (new|replied|deal|cancelled), admin_note
- `market_prices`: commodity, price, unit, source, date
- `articles`: title, slug, body, cover, published_at

Semua tabel memakai migration dan seeder. Buat factory untuk data uji.

## 6. Rute utama

Marketplace (publik, login hanya saat checkout):
`/`, `/produk`, `/produk/{slug}`, `/petani/{slug}` (storytelling), `/harga-pasar`, `/edukasi`, `/keranjang`, `/checkout`, `/akun`, `/akun/pesanan`

Dashboard petani (`role:farmer`, prefix `/dashboard-petani`):
`/produk`, `/produk/create`, `/pesanan`, `/jadwal-panen`, `/profil-toko`

Panel admin (`role:admin`, prefix `/admin`):
`/dashboard`, `/petani` (verifikasi), `/produk` (moderasi + pilih kanal), `/pesanan`, `/sengketa`, `/rfq`, `/sertifikat`, `/harga-pasar`, `/artikel`, `/pengaturan`

Website ekspor (subdomain `export.` atau prefix `/export`, tanpa login):
`/`, `/products`, `/products/{slug}`, `/certifications`, `/logistics`, `/rfq`, `/contact`

## 7. Alur bisnis yang harus dipatuhi

1. Petani daftar, admin verifikasi, baru bisa posting.
2. Produk baru berstatus `pending`. Hanya produk `approved` yang tampil.
3. Admin memilih kanal per produk: `show_in_marketplace`, `show_in_export`, atau keduanya. Produk ekspor wajib punya `product_export_details`.
4. Customer checkout. Pembeli usaha (`customer_type = business`) boleh memilih bayar tempo (net_14, net_30), pembeli individu wajib bayar langsung.
5. Pra-order: produk dengan `harvest_schedules` terbuka bisa dipesan sebelum panen. Kurangi kuota, bukan stok.
6. Stok berkurang saat pesanan dibayar. Cegah overselling dengan database transaction.
7. RFQ: buyer isi form tanpa login, data masuk ke `rfqs`, admin menindaklanjuti dari `/admin/rfq`. Kirim email notifikasi ke admin dan konfirmasi ke buyer.

## 8. Aturan untuk AI copilot

- Bangun bertahap sesuai fase di bawah. Selesaikan dan uji satu fase sebelum lanjut.
- Gunakan Form Request untuk validasi, Policy untuk otorisasi, Service class untuk logika bisnis.
- Jangan hardcode teks. Gunakan file bahasa `id` dan `en`.
- Tampilan marketplace mobile-first. Website ekspor bergaya korporat profesional.
- Tulis test (Feature test) untuk: login per role, akses terlarang, moderasi produk, checkout, RFQ.
- Jangan membuat fitur yang ada di bagian 10 (di luar cakupan awal).
- Jika ada keputusan yang belum jelas, tanyakan dulu, jangan menebak.

## 9. Fase pengerjaan

**Fase 1: Fondasi.** Setup project, auth, role, seeder admin, layout dasar, middleware dan redirect per role.

**Fase 2: Produk dan dashboard petani.** Registrasi petani, profil toko, CRUD produk, upload foto, status moderasi.

**Fase 3: Marketplace.** Katalog, pencarian, kategori, halaman produk, halaman profil petani, keranjang, checkout, akun customer, bayar tempo untuk pembeli usaha.

**Fase 4: Panel admin.** Verifikasi petani, moderasi produk, pilih kanal, pesanan, sengketa.

**Fase 5: Konten dan pra-order.** Jadwal panen dan pra-order, harga pasar, artikel edukasi.

**Fase 6: Website ekspor.** Katalog dengan spesifikasi teknis, halaman sertifikasi, informasi logistik, form RFQ, menu RFQ di admin, bahasa Inggris dan tampilan USD.

**Fase 7: Integrasi.** Payment gateway (transfer, e-wallet, QRIS), API ongkir dan kurir, notifikasi email atau WhatsApp.

## 10. Di luar cakupan awal

- Aplikasi mobile native
- Akun login untuk buyer ekspor
- Multi role admin (staf keuangan, konten, dll)
- Escrow atau rekening bersama
- Mandarin, Arab, dan multi-mata uang selain USD
- Kalkulator logistik otomatis

## 11. Pertanyaan terbuka (konfirmasi ke atasan sebelum Fase 7)

- Uang pembeli ditampung dulu oleh platform lalu diteruskan ke petani, atau dibayar langsung ke petani?
- Apakah platform mengambil komisi dari tiap transaksi?
- Apakah produk ekspor boleh diajukan langsung oleh petani, atau selalu diinput admin?
