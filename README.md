# 🚀 Project Development Guide

Panduan menjalankan project dan workflow Git yang benar.

---

## 📥 1. Update Project (Sebelum Mulai Bekerja)

Pastikan selalu menarik perubahan terbaru dari branch `main` sebelum mulai development:

```bash
git pull origin main
```

---

## 📦 2. Install Dependencies

Setelah pull, jalankan perintah berikut:

### Install PHP Dependencies
```bash
composer install
```

### Install Node Dependencies
```bash
npm install
```

---

## 🗄 3. Setup Database

Jalankan migration fresh (akan reset database):

```bash
php artisan migrate:fresh
```

> ⚠️ **Perhatian:** Perintah ini akan menghapus seluruh data di database.

---

## ▶️ 4. Menjalankan Project

Untuk menjalankan project development server:

```bash
npm run serve
```

---

# 🔁 Workflow Sebelum Push

Sebelum melakukan `git push`, **WAJIB** pastikan branch kamu sudah update dengan `main`.

## ✅ Langkah yang Benar

### 1️⃣ Pull terbaru dari main
```bash
git pull origin main
```

### 2️⃣ Setelah tidak ada conflict, lakukan push
```bash
git push origin nama-branch-kamu
```

Contoh:
```bash
git push origin feature-login
```

---

# 📌 Ringkasan Workflow Development

```bash
git pull origin main
composer install
npm install
php artisan migrate:fresh
npm run serve
```

Sebelum push:

```bash
git pull origin main
git push origin nama-branch-kamu
```

---

# 🧠 Best Practice

- Selalu pull sebelum mulai kerja
- Selalu pull sebelum push
- Gunakan branch masing-masing (jangan langsung ke `main`)
- Resolve conflict sebelum melakukan push
