# 🛡️ TubeMod

> **One tool to manage toxic comments in your YouTube channels.** > Automatically analyze spam comments, online gambling, and hate speech.

🌐 **Live Website:** [tubemod.online](https://tubemod.online)

## 📖 Tentang Sistem

TubeMod adalah aplikasi berbasis web yang dirancang khusus untuk membantu kreator konten YouTube dalam menjaga kebersihan ekosistem kolom komentar mereka. Dibangun dengan arsitektur **PHP MVC**, sistem ini mampu mendeteksi, menganalisis, dan menyaring komentar-komentar negatif secara otomatis menggunakan parameter kata kunci khusus tanpa mengganggu alur kerja kreator.

## ✨ Fitur Utama
* **🔒 Google Authentication:** Login aman dan terintegrasi langsung dengan akun Google/YouTube pengguna.
* **🤖 Smart Auto-Analyze:** Memindai ratusan komentar dalam hitungan detik dari URL video YouTube yang dimasukkan.
* **🎯 Custom Keyword Filter:** Pengguna dapat menambahkan kata kunci spesifik (misal: istilah judi online, umpatan lokal, *spam* promosi) untuk dideteksi oleh sistem.
* **📊 Moderation History:** Lacak dan kelola riwayat video yang sudah pernah dianalisis sebelumnya.
* **⚡ Native & Fast:** Antarmuka dibangun menggunakan Native CSS dan Vanilla JS tanpa *dependency* berat, menjamin performa dan *loading* yang super cepat.

## 🛠️ Tech Stack
* **Backend:** PHP 8.2 (Custom MVC Architecture)
* **Frontend:** HTML5, Native CSS, Vanilla JavaScript
* **API Integration:** YouTube Data API v3, Google OAuth 2.0
* **Database:** MySQL

## 🗺️ Roadmap (Future Plans)
Sistem ini akan terus dikembangkan untuk memberikan pengalaman moderasi terbaik. Beberapa rencana ke depan meliputi:
- [ ] Implementasi AJAX untuk proses analisis agar halaman lebih responsif (tanpa *reload*).
- [ ] Fitur *Export* hasil analisis ke format CSV/Excel untuk kebutuhan pelaporan.
- [ ] **Mobile App Integration:** Membangun versi aplikasi *mobile* menggunakan **Flutter & Dart** untuk kemudahan moderasi komentar langsung dari *smartphone*.

## 📄 Lisensi
Didistribusikan di bawah Lisensi MIT. Lihat `LICENSE` untuk informasi lebih lanjut.
