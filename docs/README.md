# DOKUMENTASI SISTEM INFORMASI KABAR PRIANGAN (SIKAPRI)
**Pusat Dokumentasi Rekayasa Perangkat Lunak, Spesifikasi Produk & Keamanan Sistem Pers Terpadu**

Selamat datang di direktori dokumentasi resmi **SiKAPRI (Sistem Informasi Kabar Priangan)** — Harian Umum Kabar Priangan (PT Berkah Pikiran Rakyat). Direktori ini berisi seluruh dokumen acuan analisis kebutuhan, spesifikasi produk (*PRD*), arsitektur keamanan, standar desain tampilan, dan rencana strategis sistem.

---

## 📚 DAFTAR DOKUMEN & PANDUAN

| Nama Dokumen | Tautan Berkas | Deskripsi & Cakupan |
|---|---|---|
| **Product Requirements Document (PRD)** | [prd.md](file:///d:/SI%20KP/docs/prd.md) | **Dokumen Inti & Induk**: Analisis mendalam hasil sesi pengarahan pembimbing, prinsip CIA Triad, 5 Area Keamanan Utama (Pencadangan Data, Proteksi Login 3x Gagal & Lockout 15 Menit, RBAC & Siklus Akun Active/Review/Disable, Manajemen Kata Sandi Bcrypt/Argon2id, dan SEO Blocking), topologi hosting cloud vs database lokal kantor, serta skema migrasi database. |
| **Standar Desain Tampilan (UI/UX)** | [tampilan.md](file:///d:/SI%20KP/docs/tampilan.md) | **Pedoman Visual & Antarmuka**: Aturan baku warna teks hitam pekat (*Pure Black #000000*), standardisasi tata letak kwitansi pembayaran dan bukti order di semua divisi, komponen form login dengan tombol intip mata, pencegahan elemen bertumpuk (*anti-stacking*), dan panduan stylesheet cetak (*print media*). |
| **Rencana Strategis & Roadmap** | [plan.md](file:///d:/SI%20KP/docs/plan.md) | **Rencana Pengembangan**: Status aktual proyek, cakupan 5 divisi & 6 peran, jadwal observasi dan wawancara lanjutan. |
| **Spesifikasi Divisi Iklan** | [divisi-iklan.md](file:///d:/SI%20KP/docs/divisi-iklan.md) | **Modul Operasional Iklan**: Detail alur transaksi iklan koran harian, iklan Priangan TV, iklan digital portal online, tarif milimeter kolom, kop nota, kop kota, dan cutoff harian jam 17:00. |
| **Spesifikasi Divisi Sirkulasi** | [sirkulasi.md](file:///d:/SI%20KP/docs/sirkulasi.md) | **Modul Operasional Distribusi**: Alur distribusi koran fisik, data agen, surat jalan, retur koran, dan kartu piutang keagenan. |

---

## 🔐 RINGKASAN KEBIJAKAN UTAMA PEMBIMBING (QUICK REFERENCE)

1. **Keamanan Bukan Sekadar Pentest**: Keamanan harus terintegrasi (*secure by design*) pada basis data dan arsitektur web.
2. **5 Pilar Keamanan**:
   - **Area 1**: Skema pencadangan data rutin (harian, mingguan, bulanan, 3 bulanan, 6 bulanan, tahunan) di lokasi terpisah + SOP restore testing.
   - **Area 2**: Proteksi login 3 kali gagal langsung lockout 15 menit + deteksi activity log.
   - **Area 3**: Kontrol admin dengan 3 status (Active, Review, Disable) dan larangan hard delete untuk user bertransaksi. Jatah akun: Sirkulasi (1), Keuangan (1), Kasir (1), Billing (1), Iklan (1), Admin (1).
   - **Area 4**: Sandi rumit kombinasi huruf, angka, simbol (contoh pola: `1r12skabar17riangan`), dilarang tombol publik reset password.
   - **Area 5**: Web internal tertutup, blokir crawler dengan `robots.txt Disallow: /` dan meta tag `noindex, nofollow`.
3. **Arsitektur Jaringan**: Web app di cloud hosting, database MySQL lebih aman di server lokal kantor via private tunnel/VPN.
