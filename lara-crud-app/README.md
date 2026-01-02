# **Rangkuman Transkrip - CRUD Laravel & Gmail SMTP**

Berikut adalah catatan komprehensif dari bagian transkrip yang membahas **CRUD Laravel** dan **Gmail SMTP**. Catatan ini dirancang langkah demi langkah dengan penekanan pada bagian penting, kesalahan umum, dan solusi.

---

## **A. INSTALASI & SETUP PROJEK LARAVEL 12**

### **1. Instalasi Laravel 12**
```bash
composer create-project laravel/laravel laravel-app
```
- **Folder proyek baru**: `laravel-app`
- **Buka di VS Code**: 
  ```bash
  cd laravel-app
  code .
  ```

### **2. Menjalankan Development Server**
```bash
php artisan serve
```
- Server berjalan di: `http://localhost:8000`
- **Error pertama yang mungkin muncul**: "Internal Server Error" karena database belum dikonfigurasi.

---

## **B. KONFIGURASI DATABASE**

### **1. Ganti Database dari SQLite ke MySQL**
- **File**: `.env`
- Ubah konfigurasi:
  ```env
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=nama_database
  DB_USERNAME=root
  DB_PASSWORD=
  ```
- **Pastikan**: Buat database terlebih dahulu di phpMyAdmin.

### **2. Jalankan Migrasi Default**
```bash
php artisan migrate
```
- **Tabel yang dibuat**: `users`, `password_reset_tokens`, `personal_access_tokens`, `failed_jobs`, dll.
- Setelah ini, halaman utama Laravel seharusnya sudah bisa diakses.

---

## **C. INSTALASI AUTHENTIKASI (BREEZE)**

### **1. Install Breeze via Composer**
```bash
composer require laravel/breeze --dev
```

### **2. Install Breeze Scaffolding**
```bash
php artisan breeze:install
```
- **Pilih stack**: `blade` (untuk Blade dengan Alpine)
- **Dark mode?**: Pilih sesuai preferensi
- **Testing framework**: `PHPUnit`
- **Proses ini akan mengubah file**:
  - `resources/css/app.css` (akan di-overwrite)
  - `routes/web.php` (ditambahkan rute Breeze)
  - **File layout dan view baru**

### **3. Generate CSS Build**
```bash
npm run build
```
- **Penting**: Build file CSS akan disimpan di `public/build/assets/`

### **4. Fitur Verifikasi Email**
- **File**: `app/Models/User.php`
- Uncomment line:
  ```php
  use Illuminate\Contracts\Auth\MustVerifyEmail;
  ```
- Ubah class menjadi:
  ```php
  class User extends Authenticatable implements MustVerifyEmail
  ```
- **Default mailer**: `.env` setting `MAIL_MAILER=log` (email verification link akan disimpan di `storage/logs/laravel.log`)

---

## **D. MEMBUAT APLIKASI BLOG (CRUD)**

### **1. Membuat Tabel `blogs`**
- **Buat migration**:
  ```bash
  php artisan make:migration create_blogs_table
  ```
- **Isi migration** (`database/migrations/xxxx_create_blogs_table.php`):
  ```php
  public function up()
  {
      Schema::create('blogs', function (Blueprint $table) {
          $table->id();
          $table->string('title', 50);
          $table->text('description')->nullable();
          $table->string('banner_image', 150)->nullable();
          $table->foreignId('user_id')->constrained('users');
          $table->timestamps();
      });
  }
  ```
- **Jalankan migrasi**:
  ```bash
  php artisan migrate
  ```

### **2. Membuat Model & Controller**
```bash
php artisan make:model Blog
php artisan make:controller BlogController --resource
```
- **File model**: `app/Models/Blog.php`
  ```php
  protected $fillable = ['title', 'description', 'banner_image', 'user_id'];
  ```

### **3. Membuat View (Blade Templates)**
- **Folder**: `resources/views/blog/`
- **File yang dibuat**:
  1. `index.blade.php` (untuk list blogs)
  2. `create.blade.php` (form tambah blog)
  3. `show.blade.php` (detail single blog)
  4. `edit.blade.php` (form edit blog)

### **4. Setup Resource Routes & Layout**
- **File**: `routes/web.php`
  ```php
  Route::middleware(['auth', 'verified'])->group(function () {
      Route::resource('blog', BlogController::class);
  });
  ```
- **Update navigation menu** (`resources/views/layouts/navigation.blade.php`):
  ```blade
  <x-nav-link :href="route('blog.index')" :active="request()->routeIs('blog.index')">
      {{ __('Blog') }}
  </x-nav-link>
  ```

---

## **E. IMPLEMENTASI CRUD OPERATIONS**

### **1. CREATE (Store Method)**
- **File**: `app/Http/Controllers/BlogController.php`
- **Method `store`**:
  ```php
  public function store(Request $request)
  {
      // Validasi
      $data = $request->validate([
          'title' => 'required|string',
          'description' => 'required|string',
          'banner_image' => 'required|image'
      ]);
  
      // Upload gambar
      if ($request->hasFile('banner_image')) {
          $data['banner_image'] = $request->file('banner_image')
              ->store('blogs', 'public');
      }
  
      // Tambah user_id dari user yang login
      $data['user_id'] = Auth::id();
  
      // Simpan ke database
      Blog::create($data);
  
      return redirect()->route('blog.index')
          ->with('success', 'Blog created successfully');
  }
  ```

### **2. READ (Index & Show Methods)**
- **Index method**:
  ```php
  public function index()
  {
      $blogs = Blog::where('user_id', auth()->id())
          ->orderBy('id', 'desc')
          ->paginate(10);
      return view('blog.index', compact('blogs'));
  }
  ```
- **Show method**:
  ```php
  public function show(Blog $blog)
  {
      return view('blog.show', compact('blog'));
  }
  ```

### **3. UPDATE (Edit & Update Methods)**
- **Edit method**:
  ```php
  public function edit(Blog $blog)
  {
      return view('blog.edit', compact('blog'));
  }
  ```
- **Update method**:
  ```php
  public function update(Request $request, Blog $blog)
  {
      $data = $request->validate([
          'title' => 'required|string',
          'description' => 'required|string',
          'banner_image' => 'sometimes|image'
      ]);
  
      // Jika ada gambar baru
      if ($request->hasFile('banner_image')) {
          // Hapus gambar lama
          if ($blog->banner_image) {
              Storage::disk('public')->delete($blog->banner_image);
          }
          // Upload gambar baru
          $data['banner_image'] = $request->file('banner_image')
              ->store('blogs', 'public');
      }
  
      $blog->update($data);
  
      return redirect()->route('blog.show', $blog)
          ->with('success', 'Blog updated successfully');
  }
  ```

### **4. DELETE (Destroy Method)**
- **Destroy method**:
  ```php
  public function destroy(Blog $blog)
  {
      // Hapus gambar dari storage
      if ($blog->banner_image) {
          Storage::disk('public')->delete($blog->banner_image);
      }
      
      // Hapus dari database
      $blog->delete();
  
      return redirect()->route('blog.index')
          ->with('success', 'Blog deleted successfully');
  }
  ```

---

## **F. TRIK & BAGIAN PENTING**

### **1. Storage Link untuk Gambar**
- Setelah upload gambar ke storage, buat symbolic link:
  ```bash
  php artisan storage:link
  ```
- **Tampilkan gambar di Blade**:
  ```blade
  <img src="{{ asset('storage/' . $blog->banner_image) }}" alt="Banner">
  ```

### **2. Form Method Spoofing untuk PUT/PATCH/DELETE**
- **Untuk update form** (method PUT/PATCH):
  ```blade
  <form method="POST" action="{{ route('blog.update', $blog) }}">
      @csrf
      @method('PUT') <!-- atau @method('PATCH') -->
      <!-- form fields -->
  </form>
  ```
- **Untuk delete form**:
  ```blade
  <form method="POST" action="{{ route('blog.destroy', $blog) }}">
      @csrf
      @method('DELETE')
      <button onclick="return confirm('Are you sure?')">Delete</button>
  </form>
  ```

### **3. Menampilkan Pesan Sukses/Error**
- **Di layout utama** (`resources/views/layouts/app.blade.php`):
  ```blade
  @if(session('success'))
      <div class="success-message">
          {{ session('success') }}
      </div>
  @endif
  ```

### **4. Pagination di Blade**
```blade
{{ $blogs->links() }}
```

---

## **G. KONFIGURASI GMAIL SMTP**

### **1. Setup di `.env`**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=email_anda@gmail.com
MAIL_PASSWORD=app_password_dari_google
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=email_anda@gmail.com
MAIL_FROM_NAME="Your App Name"
```

### **2. Mendapatkan App Password dari Google**
1. Buka: [Google Account Security](https://myaccount.google.com/security)
2. Aktifkan **2-Step Verification** (jika belum)
3. Cari "App passwords" atau buka: `myaccount.google.com/apppasswords`
4. Pilih app: `Mail`
5. Pilih device: `Other` (nama custom, misal "Laravel App")
6. Klik "Generate"
7. **Salin password 16 karakter** (tanpa spasi)

### **3. Customize Email Verification Template**
```bash
php artisan vendor:publish --tag=laravel-mail
```
- **File template**: `resources/views/vendor/mail/html/message.blade.php`
- **Ubah logo**: Cari `<img src=...>` dan ganti dengan URL logo Anda.

### **4. Testing Email Verification**
1. Register user baru
2. Check email di Gmail
3. Klik link verifikasi
4. User akan langsung login setelah verifikasi

---

## **H. ERROR UMUM & SOLUSI**

### **1. "No application encryption key has been specified"**
```bash
php artisan key:generate
```

### **2. "SQLSTATE[HY000] [2002] Connection refused"**
- Pastikan MySQL server running
- Cek credentials di `.env`
- Pastikan database sudah dibuat

### **3. Gambar Tidak Muncul Setelah `storage:link`**
- Periksa path di Blade:
  ```blade
  <!-- BENAR: -->
  <img src="{{ asset('storage/blogs/filename.jpg') }}">
  
  <!-- SALAH: -->
  <img src="storage/blogs/filename.jpg">
  ```

### **4. "419 Page Expired" pada Form Submit**
- Pastikan ada `@csrf` di dalam form
- Clear cache:
  ```bash
  php artisan cache:clear
  php artisan config:clear
  ```

### **5. Gmail SMTP Tidak Mengirim Email**
- Pastikan "Less secure app access" diaktifkan (jika tidak menggunakan App Password)
- Atau gunakan App Password (direkomendasikan)
- Port yang benar: `465` (SSL) atau `587` (TLS)

### **6. Error "Class 'Blog' not found"**
- Pastikan model sudah di-import di controller:
  ```php
  use App\Models\Blog;
  ```

---

## **I. CHECKLIST FINAL**

### **Setup Awal**
- [ ] Laravel 12 terinstall
- [ ] Database connected
- [ ] Breeze installed & built
- [ ] Bisa register/login user

### **CRUD Blog**
- [ ] Migration `blogs` table
- [ ] Model `Blog` dengan `$fillable`
- [ ] Resource controller `BlogController`
- [ ] Blade templates: index, create, show, edit
- [ ] Resource routes dengan middleware `auth` & `verified`
- [ ] Form validation
- [ ] Upload gambar ke storage
- [ ] `storage:link` untuk akses gambar
- [ ] Pagination working
- [ ] Delete dengan konfirmasi

### **Email Verification**
- [ ] User model implements `MustVerifyEmail`
- [ ] Gmail SMTP configured di `.env`
- [ ] App Password dari Google
- [ ] Email template customized
- [ ] Verifikasi link bekerja

---

## **J. TIPS PRODUKSI**

1. **Jangan gunakan `php artisan serve` di production**
   - Gunakan web server seperti Nginx/Apache
   - Atau deploy ke Laravel Forge, Vapor, shared hosting

2. **Simpan App Password dengan aman**
   - Jangan commit `.env` ke GitHub
   - Gunakan environment variables di server

3. **Optimasi upload gambar**
   - Validasi ukuran file
   - Resize gambar dengan Intervention Image
   - Gunakan CDN untuk production

4. **Backup database secara berkala**

5. **Gunakan queue untuk pengiriman email**
   ```env
   QUEUE_CONNECTION=database
   ```
   Lalu:
   ```bash
   php artisan queue:work
   ```

---
