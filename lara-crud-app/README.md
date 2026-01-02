# 📚 Catatan Belajar Laravel 12 - CRUD & Gmail SMTP

## 📋 Daftar Isi
- [Instalasi & Setup](#-instalasi--setup)
- [Autentikasi dengan Breeze](#-autentikasi-dengan-breeze)
- [CRUD Blog Management](#-crud-blog-management)
- [Gmail SMTP Configuration](#-gmail-smtp-configuration)
- [Troubleshooting & Common Errors](#️-troubleshooting--common-errors)
- [Best Practices](#-best-practices)

---

## 🚀 Instalasi & Setup

### **1. Install Laravel 12**
```bash
composer create-project laravel/laravel nama-project
cd nama-project
code .  # Buka di VS Code
```

### **2. Konfigurasi Database**
**File**: `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=root
DB_PASSWORD=
```

**Jalankan migrasi:**
```bash
php artisan migrate
```

### **3. Jalankan Development Server**
```bash
php artisan serve
```
👉 **Akses**: `http://localhost:8000`

---

## 🔐 Autentikasi dengan Breeze

### **1. Install Breeze Package**
```bash
composer require laravel/breeze --dev
php artisan breeze:install
```

**Pilihan saat instalasi:**
- **Stack**: `blade` (pilih Blade dengan Alpine)
- **Dark mode**: Yes/No
- **Testing framework**: `PHPUnit` (pilih 1)

### **2. Build Assets**
```bash
npm install
npm run build
```

### **3. Aktifkan Email Verification**
**File**: `app/Models/User.php`
```php
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    // Uncomment line ini:
    // protected $fillable = [...];
}
```

**File**: `.env` (default untuk testing)
```env
MAIL_MAILER=log  # Email disimpan di storage/logs/laravel.log
```

---

## 📝 CRUD Blog Management

### **1. Membuat Database Migration**
```bash
php artisan make:migration create_blogs_table
```

**File migration** (`database/migrations/xxxx_create_blogs_table.php`):
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

**Jalankan migrasi:**
```bash
php artisan migrate
```

### **2. Membuat Model & Controller**
```bash
php artisan make:model Blog
php artisan make:controller BlogController --resource
```

**File model** (`app/Models/Blog.php`):
```php
protected $fillable = ['title', 'description', 'banner_image', 'user_id'];
```

### **3. Setup Routes dengan Middleware**
**File**: `routes/web.php`
```php
use App\Http\Controllers\BlogController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('blog', BlogController::class);
});
```

### **4. Membuat Blade Views**
**Folder**: `resources/views/blog/`
- `index.blade.php` - List semua blog
- `create.blade.php` - Form tambah blog
- `show.blade.php` - Detail single blog
- `edit.blade.php` - Form edit blog

### **5. Update Navigation Menu**
**File**: `resources/views/layouts/navigation.blade.php`
```blade
<x-nav-link :href="route('blog.index')" :active="request()->routeIs('blog.index')">
    {{ __('Blog') }}
</x-nav-link>
```

---

## ✨ IMPLEMENTASI CRUD OPERATIONS

### **CREATE - Store Method**
**File**: `app/Http/Controllers/BlogController.php`
```php
public function store(Request $request)
{
    // Validasi input
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

### **READ - Index & Show Methods**
```php
// Index method - list semua blog user
public function index()
{
    $blogs = Blog::where('user_id', auth()->id())
        ->orderBy('id', 'desc')
        ->paginate(10);
    return view('blog.index', compact('blogs'));
}

// Show method - detail single blog
public function show(Blog $blog)
{
    return view('blog.show', compact('blog'));
}
```

### **UPDATE - Edit & Update Methods**
```php
// Edit method - form edit
public function edit(Blog $blog)
{
    return view('blog.edit', compact('blog'));
}

// Update method - proses update
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

### **DELETE - Destroy Method**
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

## 📧 Gmail SMTP Configuration

### **1. Setup di `.env`**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=email_anda@gmail.com
MAIL_PASSWORD=app_password_google
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=email_anda@gmail.com
MAIL_FROM_NAME="Nama Aplikasi Anda"
```

### **2. Mendapatkan App Password dari Google**
**Langkah-langkah:**
1. Buka: [Google Account Security](https://myaccount.google.com/security)
2. Aktifkan **2-Step Verification** (jika belum)
3. Cari **"App passwords"** 
4. Atau langsung ke: `myaccount.google.com/apppasswords`
5. Pilih app: `Mail`
6. Pilih device: `Other` (beri nama, misal "Laravel App")
7. Klik **"Generate"**
8. **Salin password 16 karakter** (tanpa spasi)

### **3. Customize Email Template**
```bash
php artisan vendor:publish --tag=laravel-mail
```

**File**: `resources/views/vendor/mail/html/message.blade.php`
- Ganti logo dengan URL logo Anda
- Customize tampilan sesuai kebutuhan

### **4. Testing Email Verification**
1. Register user baru
2. Check email di Gmail
3. Klik link verifikasi
4. User otomatis login setelah verifikasi

---

## ⚠️ Troubleshooting & Common Errors

### **1. Database Connection Error**
```
SQLSTATE[HY000] [2002] Connection refused
```
**Solusi:**
- Pastikan MySQL server running
- Cek credentials di `.env`
- Pastikan database sudah dibuat

### **2. "419 Page Expired" pada Form Submit**
**Solusi:**
- Pastikan ada `@csrf` di dalam form
- Clear cache:
```bash
php artisan cache:clear
php artisan config:clear
```

### **3. Gambar Tidak Muncul**
**Penyebab:** Belum buat storage link
**Solusi:**
```bash
php artisan storage:link
```

**Di Blade, gunakan:**
```blade
<!-- BENAR -->
<img src="{{ asset('storage/' . $blog->banner_image) }}">

<!-- SALAH -->
<img src="storage/{{ $blog->banner_image }}">
```

### **4. Gmail SMTP Tidak Mengirim Email**
**Cek:**
1. App Password sudah benar (16 karakter, tanpa spasi)
2. Port yang digunakan: `465` (SSL) atau `587` (TLS)
3. "Less secure app access" diaktifkan (jika tidak pakai App Password)

### **5. Error "Class 'Blog' not found"**
**Solusi:** Import model di controller
```php
use App\Models\Blog;
```

### **6. Method Spoofing untuk PUT/PATCH/DELETE**
**Form edit (method PUT):**
```blade
<form method="POST" action="{{ route('blog.update', $blog) }}">
    @csrf
    @method('PUT')
    <!-- form fields -->
</form>
```

**Form delete (method DELETE):**
```blade
<form method="POST" action="{{ route('blog.destroy', $blog) }}">
    @csrf
    @method('DELETE')
    <button onclick="return confirm('Yakin ingin menghapus?')">
        Delete
    </button>
</form>
```

---

## 💡 Tips & Best Practices

### **1. Storage Link Setup**
Selalu jalankan setelah upload gambar pertama:
```bash
php artisan storage:link
```

### **2. Pagination di Blade**
```blade
{{ $blogs->links() }}
```

### **3. Menampilkan Pesan Sukses/Error**
**Di layout utama** (`resources/views/layouts/app.blade.php`):
```blade
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
```

### **4. Limit Text di Table**
```blade
{{ Str::limit($blog->description, 50) }}
```

### **5. Format Tanggal**
```blade
{{ $blog->created_at->format('d M Y') }}
```

---

## 📁 Project Structure
```
laravel-project/
├── app/
│   ├── Http/Controllers/
│   │   └── BlogController.php
│   └── Models/
│       ├── User.php
│       └── Blog.php
├── database/
│   └── migrations/
│       └── xxxx_create_blogs_table.php
├── resources/
│   ├── views/
│   │   ├── blog/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   ├── show.blade.php
│   │   │   └── edit.blade.php
│   │   └── layouts/
│   └── css/
├── storage/
│   └── app/public/blogs/    # Gambar disimpan di sini
├── public/
│   └── storage/             # Symbolic link ke storage
├── routes/
│   ├── web.php
│   └── api.php
└── .env
```

---

## 🔗 Git Commands untuk Dokumentasi
```bash
# Buat branch khusus dokumentasi
git checkout -b docs/learning-notes

# Commit dengan pattern yang baik
git add README.md
git commit -m "docs: add comprehensive laravel crud & smtp notes"
git push origin docs/learning-notes

# Merge ke main branch
git checkout main
git merge docs/learning-notes
git push origin main
```

---

## 📚 Resources & References
- [Laravel Documentation](https://laravel.com/docs/12.x)
- [Breeze Documentation](https://laravel.com/docs/12.x/starter-kits#laravel-breeze)
- [Laravel Email Verification](https://laravel.com/docs/12.x/verification)
- [Google App Passwords](https://myaccount.google.com/apppasswords)

---

## ✅ Checklist Progress
- [ ] Laravel 12 installed
- [ ] Database configured
- [ ] Breeze authentication working
- [ ] Blog CRUD operations complete
- [ ] Image upload working with storage link
- [ ] Gmail SMTP configured
- [ ] Email verification working
- [ ] All forms have CSRF protection
- [ ] Error handling implemented
- [ ] Success messages displayed


---
*Last Updated: 2 Januari 2025*  
*Created with ❤️ untuk pembelajaran Laravel 12*
