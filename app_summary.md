# 📘 Unuha Showcase - KONSEP SISTEM LENGKAP
## Platform Showcase Project Mahasiswa Informatika & PTI UNUHA

---

## 🎯 KONSEP UTAMA SISTEM

### **Pendekatan Arsitektur**
- **Single Dashboard URL** (`/dashboard`) yang adaptif berdasarkan role
- **Policy-Based Authorization** - bukan pemisahan controller per role
- **Modern Monolith** - Laravel + Inertia (No API Routes)
- **Shared Components** - komponen Vue reusable untuk semua role

### **Prinsip Desain**
1. **DRY (Don't Repeat Yourself)** - Satu endpoint, banyak fungsi
2. **Role-Based Content** - Konten dashboard disesuaikan, bukan route-nya
3. **Progressive Enhancement** - Fitur bertambah sesuai role, bukan terpisah
4. **Centralized Logic** - Business logic di Service/Repository, bukan di Controller

---

## 📊 STRUKTUR DATABASE LENGKAP

### **1. TABEL USERS & AUTHENTICATION**

#### **users**
**Fungsi:** Data utama semua pengguna sistem

| Kolom | Tipe | Deskripsi | Index |
|-------|------|-----------|-------|
| id | bigint | Primary Key | ✓ |
| name | varchar(255) | Nama lengkap | - |
| email | varchar(255) | Email login | ✓ UNIQUE |
| username | varchar(100) | Username untuk profile public | ✓ UNIQUE |
| password | varchar(255) | Hashed password | - |
| role | enum | superadmin, dosen, mahasiswa | ✓ |
| avatar | varchar(255) | Path foto profil | - |
| bio | text | Deskripsi singkat | - |
| is_active | boolean | Status aktif user | ✓ |
| email_verified_at | timestamp | Waktu verifikasi email | - |
| registration_completed | boolean | Apakah sudah lengkapi profile | ✓ |
| remember_token | varchar(100) | Token remember me | - |
| created_at | timestamp | - | - |
| updated_at | timestamp | - | - |
| deleted_at | timestamp | Soft delete | - |

**Relationships:**
- Has One: `profile_mahasiswa` atau `profile_dosen`
- Has Many: `projects`, `challenges`, `comments`, `notifications`

---

#### **profile_mahasiswas**
**Fungsi:** Data khusus mahasiswa

| Kolom | Tipe | Deskripsi | Index |
|-------|------|-----------|-------|
| id | bigint | Primary Key | ✓ |
| user_id | bigint | FK ke users | ✓ UNIQUE |
| nim | varchar(20) | Nomor Induk Mahasiswa | ✓ UNIQUE |
| prodi_id | bigint | FK ke prodis | ✓ |
| angkatan | year | Tahun masuk | ✓ |
| semester | tinyint | Semester saat ini | - |
| github_url | varchar(255) | Link GitHub | - |
| linkedin_url | varchar(255) | Link LinkedIn | - |
| portfolio_url | varchar(255) | Link portfolio pribadi | - |
| created_at | timestamp | - | - |
| updated_at | timestamp | - | - |

**Relationships:**
- Belongs To: `user`, `prodi`

---

#### **profile_dosens**
**Fungsi:** Data khusus dosen

| Kolom | Tipe | Deskripsi | Index |
|-------|------|-----------|-------|
| id | bigint | Primary Key | ✓ |
| user_id | bigint | FK ke users | ✓ UNIQUE |
| nidn | varchar(20) | Nomor Induk Dosen Nasional | ✓ UNIQUE |
| prodi_id | bigint | FK ke prodis | ✓ |
| jabatan | varchar(100) | Jabatan fungsional | - |
| bidang_keahlian | varchar(255) | Spesialisasi | - |
| scholar_url | varchar(255) | Google Scholar | - |
| scopus_url | varchar(255) | Scopus Profile | - |
| created_at | timestamp | - | - |
| updated_at | timestamp | - | - |

**Relationships:**
- Belongs To: `user`, `prodi`

---

#### **prodis**
**Fungsi:** Master data program studi

| Kolom | Tipe | Deskripsi | Index |
|-------|------|-----------|-------|
| id | bigint | Primary Key | ✓ |
| nama | varchar(100) | Nama Prodi | ✓ |
| kode | varchar(10) | Kode Prodi | ✓ UNIQUE |
| deskripsi | text | Deskripsi prodi | - |
| is_active | boolean | Status aktif | ✓ |
| created_at | timestamp | - | - |
| updated_at | timestamp | - | - |

**Data Awal:**
- Informatika (IF)
- Pendidikan Teknologi Informasi (PTI)

---

### **2. TABEL PROJECT MANAGEMENT**

#### **projects**
**Fungsi:** Data project yang diupload mahasiswa/dosen

| Kolom | Tipe | Deskripsi | Index |
|-------|------|-----------|-------|
| id | bigint | Primary Key | ✓ |
| user_id | bigint | FK ke users (owner) | ✓ |
| kategori_id | bigint | FK ke kategoris | ✓ |
| title | varchar(255) | Judul project | - |
| slug | varchar(255) | URL-friendly title | ✓ UNIQUE |
| description | text | Deskripsi singkat | - |
| content | longtext | Konten lengkap (Rich Text) | - |
| thumbnail | varchar(255) | Gambar cover | - |
| banner_image | varchar(255) | Banner untuk detail page | - |
| repository_url | varchar(255) | Link GitHub/GitLab | - |
| demo_url | varchar(255) | Link live demo | - |
| video_url | varchar(255) | Link video demo (YouTube) | - |
| status | enum | draft, published, archived | ✓ |
| is_featured | boolean | Pilihan admin untuk highlight | ✓ |
| view_count | bigint | Jumlah views | ✓ |
| like_count | bigint | Cache jumlah likes | - |
| save_count | bigint | Cache jumlah saves | - |
| share_count | bigint | Cache jumlah shares | - |
| comment_count | bigint | Cache jumlah comments | - |
| published_at | timestamp | Waktu publish | ✓ |
| created_at | timestamp | - | - |
| updated_at | timestamp | - | - |
| deleted_at | timestamp | Soft delete | - |

**Composite Index:**
- (status, published_at) - untuk query project published
- (user_id, status) - untuk my projects
- (is_featured, published_at) - untuk featured section

**Relationships:**
- Belongs To: `user`, `kategori`
- Belongs To Many: `tools` (teknologi yang digunakan)
- Has Many: `project_images`, `collaborators`, `likes`, `saves`, `comments`

---

#### **project_images**
**Fungsi:** Gallery gambar tambahan project

| Kolom | Tipe | Deskripsi | Index |
|-------|------|-----------|-------|
| id | bigint | Primary Key | ✓ |
| project_id | bigint | FK ke projects | ✓ |
| image_path | varchar(255) | Path gambar | - |
| caption | varchar(255) | Keterangan gambar | - |
| order | int | Urutan tampilan | ✓ |
| created_at | timestamp | - | - |
| updated_at | timestamp | - | - |

**Composite Index:**
- (project_id, order)

---

#### **project_collaborators**
**Fungsi:** Kolaborator project

| Kolom | Tipe | Deskripsi | Index |
|-------|------|-----------|-------|
| id | bigint | Primary Key | ✓ |
| project_id | bigint | FK ke projects | ✓ |
| user_id | bigint | FK ke users | ✓ |
| role | enum | owner, contributor | - |
| status | enum | pending, accepted, rejected | ✓ |
| invited_at | timestamp | Waktu undangan dikirim | - |
| accepted_at | timestamp | Waktu terima undangan | - |
| created_at | timestamp | - | - |
| updated_at | timestamp | - | - |

**UNIQUE Constraint:**
- (project_id, user_id)

---

### **3. TABEL MASTER DATA**

#### **kategoris**
**Fungsi:** Kategori project (Skripsi, PKM, Tugas Kuliah, dll)

| Kolom | Tipe | Deskripsi | Index |
|-------|------|-----------|-------|
| id | bigint | Primary Key | ✓ |
| nama | varchar(100) | Nama kategori | ✓ |
| slug | varchar(100) | URL slug | ✓ UNIQUE |
| deskripsi | text | Deskripsi kategori | - |
| icon | varchar(255) | Path icon atau SVG | - |
| color | varchar(7) | Hex color (#3B82F6) | - |
| is_active | boolean | Status aktif | ✓ |
| order | int | Urutan tampilan | ✓ |
| created_at | timestamp | - | - |
| updated_at | timestamp | - | - |

**Data Awal:**
- Skripsi/Tugas Akhir
- PKM (Program Kreativitas Mahasiswa)
- Tugas Kuliah
- Project Pribadi
- Magang/Internship
- Freelance

---

#### **tools**
**Fungsi:** Master teknologi/tools (Laravel, Vue, MySQL, dll)

| Kolom | Tipe | Deskripsi | Index |
|-------|------|-----------|-------|
| id | bigint | Primary Key | ✓ |
| nama | varchar(100) | Nama tool | ✓ |
| slug | varchar(100) | URL slug | ✓ UNIQUE |
| icon | text | SVG atau path icon | - |
| color | varchar(7) | Brand color | - |
| kategori_tool | enum | language, framework, library, database, platform | ✓ |
| is_active | boolean | Status aktif | ✓ |
| created_at | timestamp | - | - |
| updated_at | timestamp | - | - |

**Data Awal:** PHP, JavaScript, Python, Laravel, Vue.js, React, MySQL, PostgreSQL, Tailwind CSS, Bootstrap, Git, Docker, dll.

---

#### **project_tool** (Pivot Table)
**Fungsi:** Relasi many-to-many antara projects dan tools

| Kolom | Tipe | Deskripsi | Index |
|-------|------|-----------|-------|
| id | bigint | Primary Key | ✓ |
| project_id | bigint | FK ke projects | ✓ |
| tool_id | bigint | FK ke tools | ✓ |
| created_at | timestamp | - | - |
| updated_at | timestamp | - | - |

**UNIQUE Constraint:**
- (project_id, tool_id)

---

### **4. TABEL CHALLENGE SYSTEM**

#### **challenges**
**Fungsi:** Kompetisi/challenge yang dibuat dosen

| Kolom | Tipe | Deskripsi | Index |
|-------|------|-----------|-------|
| id | bigint | Primary Key | ✓ |
| creator_id | bigint | FK ke users (dosen) | ✓ |
| kategori_id | bigint | FK ke kategoris | - |
| title | varchar(255) | Judul challenge | - |
| slug | varchar(255) | URL slug | ✓ UNIQUE |
| description | longtext | Deskripsi lengkap | - |
| requirements | text | Persyaratan peserta | - |
| banner_image | varchar(255) | Banner challenge | - |
| status | enum | draft, open, review, completed | ✓ |
| start_date | datetime | Tanggal mulai | ✓ |
| deadline | datetime | Batas submit | ✓ |
| announcement_date | datetime | Tanggal pengumuman | - |
| max_participants | int | Maks peserta (null = unlimited) | - |
| prize_1 | varchar(255) | Hadiah juara 1 | - |
| prize_2 | varchar(255) | Hadiah juara 2 | - |
| prize_3 | varchar(255) | Hadiah juara 3 | - |
| view_count | bigint | Jumlah views | - |
| created_at | timestamp | - | - |
| updated_at | timestamp | - | - |
| deleted_at | timestamp | Soft delete | - |

**Composite Index:**
- (status, start_date, deadline)

---

#### **challenge_submissions**
**Fungsi:** Project yang disubmit ke challenge

| Kolom | Tipe | Deskripsi | Index |
|-------|------|-----------|-------|
| id | bigint | Primary Key | ✓ |
| challenge_id | bigint | FK ke challenges | ✓ |
| project_id | bigint | FK ke projects | ✓ |
| user_id | bigint | FK ke users (submitter) | ✓ |
| notes | text | Catatan dari peserta | - |
| status | enum | pending, accepted, rejected, winner | ✓ |
| ranking | tinyint | 1, 2, 3 untuk pemenang | ✓ |
| score | decimal(5,2) | Nilai total | - |
| feedback | text | Feedback dari dosen | - |
| submitted_at | timestamp | Waktu submit | - |
| reviewed_at | timestamp | Waktu review | - |
| created_at | timestamp | - | - |
| updated_at | timestamp | - | - |

**UNIQUE Constraint:**
- (challenge_id, project_id)

**Composite Index:**
- (challenge_id, status)

---

#### **challenge_criteria**
**Fungsi:** Kriteria penilaian challenge

| Kolom | Tipe | Deskripsi | Index |
|-------|------|-----------|-------|
| id | bigint | Primary Key | ✓ |
| challenge_id | bigint | FK ke challenges | ✓ |
| name | varchar(100) | Nama kriteria | - |
| weight | tinyint | Bobot (0-100) | - |
| description | text | Deskripsi kriteria | - |
| order | int | Urutan tampilan | ✓ |
| created_at | timestamp | - | - |
| updated_at | timestamp | - | - |

**Composite Index:**
- (challenge_id, order)

**Contoh Data:**
- Kreativitas (30%)
- Fungsionalitas (25%)
- UI/UX Design (20%)
- Dokumentasi (15%)
- Kode Quality (10%)

---

### **5. TABEL ENGAGEMENT & SOCIAL**

#### **interactions**
**Fungsi:** Menyimpan Like & Save dalam satu tabel

| Kolom | Tipe | Deskripsi | Index |
|-------|------|-----------|-------|
| id | bigint | Primary Key | ✓ |
| user_id | bigint | FK ke users | ✓ |
| project_id | bigint | FK ke projects | ✓ |
| type | enum | like, save | ✓ |
| collection_name | varchar(100) | Nama collection (untuk save) | - |
| created_at | timestamp | - | - |
| updated_at | timestamp | - | - |

**UNIQUE Constraint:**
- (user_id, project_id, type)

**Composite Index:**
- (project_id, type)
- (user_id, type)

**Keuntungan Merge:**
- Hemat tabel (2 jadi 1)
- Query lebih simple
- Lebih mudah tracking engagement

---

#### **project_views**
**Fungsi:** Tracking views project

| Kolom | Tipe | Deskripsi | Index |
|-------|------|-----------|-------|
| id | bigint | Primary Key | ✓ |
| project_id | bigint | FK ke projects | ✓ |
| user_id | bigint | FK ke users (nullable) | - |
| ip_address | varchar(45) | IP viewer | - |
| user_agent | text | Browser info | - |
| created_at | timestamp | - | - |

**Composite Index:**
- (project_id, created_at)

**Note:** View dihitung unique per IP per hari

---

#### **project_shares**
**Fungsi:** Tracking share project

| Kolom | Tipe | Deskripsi | Index |
|-------|------|-----------|-------|
| id | bigint | Primary Key | ✓ |
| project_id | bigint | FK ke projects | ✓ |
| user_id | bigint | FK ke users (nullable) | - |
| platform | enum | whatsapp, facebook, twitter, linkedin, copy_link | ✓ |
| created_at | timestamp | - | - |

**Composite Index:**
- (project_id, platform)

---

#### **comments**
**Fungsi:** Komentar di project

| Kolom | Tipe | Deskripsi | Index |
|-------|------|-----------|-------|
| id | bigint | Primary Key | ✓ |
| project_id | bigint | FK ke projects | ✓ |
| user_id | bigint | FK ke users | ✓ |
| parent_id | bigint | FK ke comments (for replies) | - |
| content | text | Isi komentar | - |
| like_count | int | Cache jumlah likes | - |
| is_edited | boolean | Apakah sudah diedit | - |
| created_at | timestamp | - | - |
| updated_at | timestamp | - | - |
| deleted_at | timestamp | Soft delete | - |

**Composite Index:**
- (project_id, parent_id)

---

#### **comment_likes**
**Fungsi:** Like pada komentar

| Kolom | Tipe | Deskripsi | Index |
|-------|------|-----------|-------|
| id | bigint | Primary Key | ✓ |
| comment_id | bigint | FK ke comments | ✓ |
| user_id | bigint | FK ke users | ✓ |
| created_at | timestamp | - | - |

**UNIQUE Constraint:**
- (comment_id, user_id)

---

### **6. TABEL NOTIFIKASI & ACTIVITY**

#### **notifications**
**Fungsi:** Notifikasi real-time untuk user

| Kolom | Tipe | Deskripsi | Index |
|-------|------|-----------|-------|
| id | bigint | Primary Key | ✓ |
| user_id | bigint | FK ke users | ✓ |
| type | enum | comment, like, collaboration_invite, challenge_new, challenge_result, mention, project_featured | ✓ |
| data | json | Data tambahan (project_id, message, dll) | - |
| related_type | varchar(255) | Polymorphic type | - |
| related_id | bigint | Polymorphic ID | - |
| is_read | boolean | Status baca | ✓ |
| read_at | timestamp | Waktu dibaca | - |
| created_at | timestamp | - | - |
| updated_at | timestamp | - | - |

**Composite Index:**
- (user_id, is_read, created_at)

**Jenis Notifikasi:**
1. **Comment:** "X mengomentari project Anda"
2. **Like:** "X menyukai project Anda"
3. **Collaboration Invite:** "X mengundang Anda berkolaborasi"
4. **Challenge New:** "Challenge baru dibuka: [Title]"
5. **Challenge Result:** "Hasil challenge [Title] telah diumumkan"
6. **Mention:** "X menyebut Anda di komentar"
7. **Project Featured:** "Project Anda ditampilkan di Featured"

---

#### **activity_logs**
**Fungsi:** Log aktivitas sistem (untuk admin)

| Kolom | Tipe | Deskripsi | Index |
|-------|------|-----------|-------|
| id | bigint | Primary Key | ✓ |
| user_id | bigint | FK ke users (nullable) | ✓ |
| action | varchar(100) | create_project, update_user, dll | ✓ |
| description | text | Deskripsi lengkap | - |
| related_type | varchar(255) | Polymorphic type | - |
| related_id | bigint | Polymorphic ID | - |
| ip_address | varchar(45) | IP address | - |
| created_at | timestamp | - | - |

**Composite Index:**
- (user_id, created_at)
- (action, created_at)

---

### **7. TABEL SETTINGS & CONFIGURATION**

#### **site_settings**
**Fungsi:** Pengaturan website global

| Kolom | Tipe | Deskripsi | Index |
|-------|------|-----------|-------|
| id | bigint | Primary Key | ✓ |
| key | varchar(100) | Key setting | ✓ UNIQUE |
| value | text | Value setting | - |
| group | varchar(50) | general, social_media, seo, email | ✓ |
| type | enum | text, textarea, image, boolean, number, json | - |
| created_at | timestamp | - | - |
| updated_at | timestamp | - | - |

**Data Awal:**
```
general:
- site_name: "Unuha Showcase UNUHA"
- site_tagline: "Showcase Karya Mahasiswa"
- site_logo
- site_favicon

social_media:
- facebook_url
- instagram_url
- twitter_url
- youtube_url

seo:
- meta_description
- meta_keywords
- google_analytics_id

email:
- contact_email
- support_email
```

---

#### **announcements**
**Fungsi:** Pengumuman di dashboard/landing

| Kolom | Tipe | Deskripsi | Index |
|-------|------|-----------|-------|
| id | bigint | Primary Key | ✓ |
| title | varchar(255) | Judul pengumuman | - |
| content | text | Isi pengumuman | - |
| type | enum | info, success, warning, danger | ✓ |
| is_active | boolean | Status tampil | ✓ |
| start_date | datetime | Mulai tampil | - |
| end_date | datetime | Berhenti tampil | - |
| created_at | timestamp | - | - |
| updated_at | timestamp | - | - |

**Composite Index:**
- (is_active, start_date, end_date)

---

## 🛣️ KONSEP ROUTING SISTEM

### **Public Routes (Guest)**
```
/                           → Landing Page
/projects                   → Gallery semua project
/projects/{slug}            → Detail project
/challenges                 → Daftar challenge
/challenges/{slug}          → Detail challenge
/leaderboard                → Top creators/projects
/profile/{username}         → Profile public user
/search                     → Pencarian global
/about                      → Tentang platform
```

### **Auth Routes**
```
/register                   → Form registrasi
/login                      → Form login
/email/verify               → Verifikasi email
/complete-profile           → Multi-step lengkapi profile
/forgot-password            → Reset password
```

### **Protected Routes (Authenticated)**

#### **Single Dashboard** ⭐
```
/dashboard                  → Dashboard adaptif per role
    ↓
    Role = superadmin       → StatCard + User Management
    Role = dosen            → My Challenges + Submissions
    Role = mahasiswa        → My Projects + Leaderboard
```

#### **Project Management**
```
/projects                   → Index (my projects)
/projects/create            → Form upload project
/projects/{id}              → Detail & edit
/projects/{id}/edit         → Edit page
/projects/{id}/delete       → Delete (soft)
```

#### **Challenge System**
```
/challenges                 → Browse/My Challenges
/challenges/create          → Buat challenge (dosen only)
/challenges/{id}            → Detail challenge
/challenges/{id}/edit       → Edit challenge
/challenges/{id}/submit     → Submit project
/challenges/{id}/submissions → Daftar submission (dosen)
/challenges/{id}/grade      → Penilaian (dosen)
```

#### **User Management (Admin Only)**
```
/users                      → Daftar semua user
/admin/dosen                → CRUD data dosen
/admin/mahasiswa            → CRUD data mahasiswa
/admin/prodis               → CRUD program studi
/admin/kategoris            → CRUD kategori project
/admin/tools                → CRUD teknologi/tools
```

#### **Additional Role-Specific Routes**
```
/penilaian                  → Penilaian challenge oleh dosen
/profile/dosen              → Edit profil akademis dosen
/profile/mahasiswa          → Edit profil mahasiswa
/kolaborasi                 → Kelola kolaborasi project mahasiswa
```

#### **Master Data (Admin Only)**
```
/kategoris                  → CRUD kategori
/tools                      → CRUD tools/teknologi
/prodis                     → CRUD prodi
/settings                   → Site settings
/announcements              → CRUD pengumuman
```

#### **Profile & Settings**
```
/profile                    → Edit profile
/profile/{username}         → View public profile
/settings                   → User settings
/saved                      → Project tersimpan (mahasiswa)
/notifications              → Daftar notifikasi
```

---

## 🔐 KONSEP AUTHENTICATION & AUTHORIZATION

### **Implementasi Role-Based Access Control**

#### **1. Role Management**
Sistem menyediakan tiga level role utama dengan hak akses berbeda:
- **superadmin**: Akses penuh ke semua fitur sistem
- **dosen**: Akses ke fitur challenge, penilaian, dan manajemen project pribadi
- **mahasiswa**: Akses ke manajemen project, partisipasi challenge, dan kolaborasi

#### **2. Menu Dinamis Berdasarkan Role**
Menu sidebar diimplementasikan secara dinamis berdasarkan role pengguna yang login:

**Superadmin Menu:**
- Dasbor
- Manajemen Pengguna (CRUD pengguna, reset password)
- Manajemen Dosen (CRUD data dosen)
- Manajemen Mahasiswa (CRUD data mahasiswa)
- Manajemen Program Studi (CRUD prodi)
- Manajemen Kategori (CRUD kategori project)
- Manajemen Teknologi (CRUD teknologi/tools)
- Semua Project (Moderasi semua project)
- Semua Challenge (Monitor semua challenge)

**Dosen Menu:**
- Dasbor
- Project Saya (Upload dan kelola portfolio penelitian/pengabdian)
- Manajemen Challenge (Buat dan kelola kompetisi)
- Penilaian Challenge (Nilai submission mahasiswa)
- Profil Dosen (Update data akademis)

**Mahasiswa Menu:**
- Dasbor
- Project Saya (Upload dan kelola portfolio karya)
- Ikuti Challenge (Daftar dan kirim project ke challenge)
- Kolaborasi (Kelola tim proyek)
- Profil Mahasiswa (Kelola biodata dan skill)

#### **3. Pengimplementasian Teknis**
Implementasi dilakukan di file `AppSidebar.vue` dengan menggunakan logika komputasi berdasarkan role pengguna:

```typescript
const mainNavItems = computed<NavItem[]>(() => {
    // Semua role mendapatkan Dasbor
    const items: NavItem[] = [
        {
            title: 'Dasbor',
            href: dashboard().url,
            icon: LayoutGrid,
        },
    ];

    if (!user.value) return items;

    // Superadmin menu
    if (user.value.role === 'superadmin') {
        items.push(
            {
                title: 'Manajemen Pengguna',
                href: '/admin/users',
                icon: Users,
                description: 'Kelola akun dosen dan mahasiswa',
            },
            // ... menu manajemen lainnya
        );
    }
    // Dosen menu
    else if (user.value.role === 'dosen') {
        items.push(
            {
                title: 'Project Saya',
                href: '/projects',
                icon: BookOpen,
                description: 'Portfolio project penelitian/pengabdian',
            },
            // ... menu dosen lainnya
        );
    }
    // Mahasiswa menu
    else if (user.value.role === 'mahasiswa') {
        items.push(
            {
                title: 'Project Saya',
                href: '/projects',
                icon: BookOpen,
                description: 'Portfolio karya mahasiswa',
            },
            // ... menu mahasiswa lainnya
        );
    }

    return items;
});
```

#### **4. Route Protection**
Semua route yang memerlukan otentikasi dilindungi menggunakan middleware `auth` dan untuk akses tertentu menggunakan role-based authorization di controller:

- Middleware `auth` untuk semua route yang memerlukan login
- Custom middleware/function untuk memastikan role yang sesuai
- Laravel Policies untuk granular authorization

---

## 🎨 KONSEP FRONTEND STRUKTUR

### **Layout Strategy**

#### **1. PublicLayout**
- Header: Logo + Menu (Projects, Challenges, Leaderboard)
- Footer: Social Media + Copyright
- Digunakan untuk: Landing, Project Gallery, Detail Project (guest)

#### **2. AppLayout**
- Sidebar: Menu berdasarkan role
- Topbar: Search, Notifications, Profile Dropdown
- Main Content Area
- Digunakan untuk: Dashboard, CRUD Pages (authenticated)

#### **3. AuthLayout**
- Centered Card Design
- Logo + Ilustrasi
- Digunakan untuk: Login, Register, Verify Email

---

### **Component Strategy**

#### **Shared Components (Reusable)**
```
Components/UI/
├── Button.vue
├── Input.vue
├── Card.vue
├── Modal.vue
├── Dropdown.vue
├── Badge.vue
├── Alert.vue
└── LoadingSpinner.vue
```

#### **Domain-Specific Components**

**Project Components**
```
Components/Project/
├── ProjectCard.vue          → Card di gallery
├── ProjectGrid.vue          → Grid layout untuk list
├── ProjectFilter.vue        → Filter by kategori/tools
├── ProjectStats.vue         → Views, Likes, Shares
├── TechStackBadge.vue       → Badge teknologi
├── CollaboratorList.vue     → List kolaborator
└── ShareButton.vue          → Button share sosmed
```

**Challenge Components**
```
Components/Challenge/
├── ChallengeCard.vue        → Card di list
├── CountdownTimer.vue       → Timer deadline
├── ParticipantList.vue      → List peserta
├── SubmissionCard.vue       → Card submission
├── CriteriaList.vue         → List kriteria penilaian
├── WinnerPodium.vue         → Display pemenang
└── StatusBadge.vue          → Badge status (open/review/completed)
```

**Dashboard Components**
```
Components/Dashboard/
├── StatCard.vue             → Card statistik (projects, users, dll)
├── AdminStats.vue           → Khusus admin
├── DosenOverview.vue        → Khusus dosen
├── MahasiswaProjects.vue    → Khusus mahasiswa
├── ChartAnalytics.vue       → Chart views/likes
└── RecentActivity.vue       → Timeline aktivitas
```

**Comment Components**
```
Components/Comment/
├── CommentSection.vue       → Container semua comment
├── CommentItem.vue          → Single comment
├── CommentForm.vue          → Form add/edit
└── CommentReply.vue         → Nested reply
```

**Profile Components**
```
Components/Profile/
├── ProfileHeader.vue        → Avatar + Bio + Stats
├── ProfileTabs.vue          → Projects / Challenges / About
├── AchievementBadge.vue     → Badge pencapaian
└── SocialLinks.vue          → Link GitHub/LinkedIn
```

---

### **Page Organization**

```
Pages/
├── Auth/
│   ├── Login.vue
│   ├── Register.vue          (Step 1 only: Name, Email, Password)
│   ├── CompleteProfile.vue   (Multi-step: Role → Data Diri → Selesai)
│   └── VerifyEmail.vue
│
├── Dashboard.vue             ⭐ Single Entry Point
│
├── Projects/
│   ├── Index.vue            (My Projects untuk authenticated / Gallery untuk public)
│   ├── Show.vue             (Detail project)
│   ├── Create.vue           (Form upload)
│   └── Edit.vue             (Form edit)
│
├── Challenges/
│   ├── Index.vue            (Browse challenges)
│   ├── Show.vue             (Detail challenge)
│   ├── Create.vue           (Dosen only)
│   ├── Edit.vue             (Dosen only)
│   ├── Submissions.vue      (Dosen: review submissions)
│   └── MySubmissions.vue    (Mahasiswa: track submission status)
│
├── Users/                   (Admin only)
│   ├── Index.vue
│   ├── Dosen/
│   │   ├── Index.vue
│   │   ├── Create.vue
│   │   └── Edit.vue
│   └── Mahasiswa/
│       ├── Index.vue
│       ├── Create.vue
│       └── Edit.vue
│
├── Master/                  (Admin only)
│   ├── Kategoris.vue
│   ├── Tools.vue
│   ├── Prodis.vue
│   └── Settings.vue
│
├── Admin/                   (Admin only)
│   ├── Dosen/
│   │   ├── Index.vue       (CRUD Dosen)
│   │   ├── Create.vue
│   │   └── Edit.vue
│   ├── Mahasiswa/
│   │   ├── Index.vue       (CRUD Mahasiswa)
│   │   ├── Create.vue
│   │   └── Edit.vue
│   └── Prodis/
│       ├── Index.vue       (CRUD Program Studi)
│       ├── Create.vue
│       └── Edit.vue
│
├── Evaluations/             (Dosen only)
│   └── Index.vue           (Penilaian submission challenge)
│
├── Profiles/                (Role-based profile management)
│   ├── Dosen/
│   │   └── Edit.vue       (Edit profil akademis dosen)
│   └── Mahasiswa/
│       └── Edit.vue       (Edit profil mahasiswa)
│
├── Collaborations/          (Mahasiswa only)
│   └── Index.vue          (Kelola kolaborasi tim project)
│
├── Profile/
│   ├── Show.vue            (Public profile)
│   └── Edit.vue            (Edit own profile)
│
├── Leaderboard.vue
├── Search.vue
└── Welcome.vue             (Landing page)
```

---

## 🧠 KONSEP BUSINESS LOGIC

### **Service Layer Pattern**

**Tujuan:** Memisahkan business logic dari Controller agar:
- Controller hanya handle request/response
- Logic kompleks di Service
- Mudah testing
- Reusable

**Contoh Service:**

#### **ProjectService**
```
Fungsi:
- createProject(data) → Validasi, Save, Upload Image, Notify
- updateProject(project, data) → Update, Sync Tools, Log Activity
- publishProject(project) → Change status, Notify Followers
- deleteProject(project) → Soft Delete, Notify Collaborators
- featureProject(project) → Set Featured, Notify Owner
- trackView(project, user, ip) → Save View, Update Counter
```

#### **AdminController**
```
Fungsi:
- DosenController@index → Menampilkan daftar dosen (Superadmin only)
- MahasiswaController@index → Menampilkan daftar mahasiswa (Superadmin only)
- ProdiController@index → Menampilkan daftar program studi (Superadmin only)
- KategoriController@index → Menampilkan daftar kategori project (Superadmin only)
- ToolController@index → Menampilkan daftar teknologi/tools (Superadmin only)
```

#### **EvaluationService**
```
Fungsi:
- evaluationController@index → Menampilkan submission untuk dinilai (Dosen only)
- gradeSubmission(submission, score, feedback) → Memberikan nilai dan feedback
```

#### **ProfileService**
```
Fungsi:
- profileDosenController@edit → Menampilkan form edit profil dosen (Dosen only)
- profileMahasiswaController@edit → Menampilkan form edit profil mahasiswa (Mahasiswa only)
- updateAcademicProfile(data) → Update profil akademis dosen
- updateStudentProfile(data) → Update profil mahasiswa
```

#### **CollaborationService**
```
Fungsi:
- collaborationController@index → Menampilkan daftar kolaborasi aktif (Mahasiswa only)
- inviteCollaborator(project, user) → Mengundang pengguna ke kolaborasi project
- acceptCollaboration(invitation) → Menerima undangan kolaborasi
- rejectCollaboration(invitation) → Menolak undangan kolaborasi
```

#### **ChallengeService**
```
Fungsi:
- createChallenge(data) → Save, Notify Users
- submitToChallenge(challenge, project) → Validate, Save Submission
- gradeSubmission(submission, score, feedback) → Update Score, Notify User
- announceWinners(challenge, rankings) → Update Winners, Send Notifications
- checkDeadline() → Cronjob untuk auto-close challenge
```

#### **NotificationService**
```
Fungsi:
- sendCommentNotification(comment) → Notify project owner
- sendLikeNotification(project, user) → Notify setiap 10 likes
- sendCollaborationInvite(project, user) → Notify invitee
- markAsRead(notification) → Update status
- clearAll(user) → Bulk mark read
```

---

### **Policy Pattern**

**Tujuan:** Authorization logic untuk akses kontrol.

**Contoh Policy:**

#### **ProjectPolicy**
```
view(user, project):
    → published = true untuk semua
    → draft = hanya owner atau admin

update(user, project):
    → owner atau collaborator dengan role='contributor'
    → admin bisa edit semua

delete(user, project):
    → hanya owner
    → admin bisa delete semua

feature(user, project):
    → hanya superadmin
```

#### **ChallengePolicy**
```
create(user):
    → role = dosen atau superadmin

update(user, challenge):
    → creator atau superadmin

gradeSubmission(user, challenge):
    → creator challenge
    → superadmin bisa override

delete(user, challenge):
    → creator (jika belum ada submission)
    → superadmin (paksa delete)
```

---

### **Role-Based Authorization Implementation**

#### **Contoh Controller Protection:**

**AdminController (Superadmin Only):**
```php
public function __construct()
{
    $this->middleware('auth');
    $this->middleware(function ($request, $next) {
        abort_if(!auth()->user()->isSuperAdmin(), 403);
        return $next($request);
    });
}
```

**DosenController (Dosen Only):**
```php
public function __construct()
{
    $this->middleware('auth');
    $this->middleware(function ($request, $next) {
        abort_if(!auth()->user()->isDosen(), 403);
        return $next($request);
    });
}
```

**MahasiswaController (Mahasiswa Only):**
```php
public function __construct()
{
    $this->middleware('auth');
    $this->middleware(function ($request, $next) {
        abort_if(!auth()->user()->isMahasiswa(), 403);
        return $next($request);
    });
}
```

---

### **Observer Pattern**

**Tujuan:** Otomasi action saat model event terjadi.

**Contoh Observer:**

#### **ProjectObserver**
```
creating:
    → Generate slug dari title
    → Set default status = 'draft'

created:
    → Log activity "User X create project Y"
    → Notify followers (jika user punya followers)

updating:
    → Set is_edited = true
    → Update updated_at

published (custom event):
    → Update published_at
    → Notify followers
    → Tweet to social media (optional)

deleting:
    → Delete related images
    → Notify collaborators
```

#### **CommentObserver**
```
created:
    → Increment project.comment_count
    → Notify project owner
    → Notify parent comment owner (jika reply)
    → Check mention (@username) → Notify mentioned user

deleted:
    → Decrement project.comment_count
    → Delete child comments (cascade)
```

---

## 🔄 ALUR KERJA SISTEM

### **1. Alur Registrasi & Onboarding**

```
Step 1: Registrasi Awal
┌─────────────────────────┐
│  Form Register          │
│  - Nama                 │
│  - Email                │
│  - Password             │
│  - Confirm Password     │
└──────────┬──────────────┘
           │
           ↓
    [Submit] → Create User (role='mahasiswa', registration_completed=false)
           │
           ↓
    Send Verification Email
           │
           ↓
┌──────────────────────────┐
│  Page: Verify Email      │
│  "Cek inbox Anda..."     │
└──────────┬───────────────┘
           │
           ↓
    User Click Link di Email
           │
           ↓
    Mark email_verified_at
           │
           ↓
Step 2: Lengkapi Profile (Multi-Step Form)
┌──────────────────────────┐
│  Progress: ○━━━━○━━━━○   │
│           1    2    3     │
└──────────┬───────────────┘
           │
    Sub-Step 1: Pilih Role
    ┌────────────────┐
    │  [ ] Mahasiswa  │
    │  [ ] Dosen      │
    └────────┬───────┘
             │
             ↓
    Sub-Step 2: Data Diri
    
    IF role = 'mahasiswa':
        → NIM, Prodi, Angkatan, GitHub
    
    IF role = 'dosen':
        → NIDN, Prodi, Jabatan, Scholar
    
             │
             ↓
    Sub-Step 3: Konfirmasi & Selesai
    ┌──────────────────────────┐
    │  Review data             │
    │  [Checkbox] Data benar   │
    │  [Submit]                │
    └──────────┬───────────────┘
               │
               ↓
    Update: registration_completed = true
               │
               ↓
    Redirect: /dashboard
```

---

### **2. Alur Upload Project**

```
Mahasiswa/Dosen Login
        │
        ↓
    Navigate: /projects/create
        │
        ↓
┌───────────────────────────────┐
│  Form Upload Project          │
│  - Title                      │
│  - Kategori (Dropdown)        │
│  - Description (Rich Text)    │
│  - Tools/Stack (Multi Select) │
│  - Thumbnail (Image Upload)   │
│  - Repository URL             │
│  - Demo URL                   │
│  - Gallery Images (Multiple)  │
│  - Status: [ ] Draft          │
│            [ ] Publish        │
└───────────────┬───────────────┘
                │
                ↓
    [Save] → Validate
                │
                ↓
    ProjectService::createProject()
        │
        ├─→ Save Project
        ├─→ Upload Images
        ├─→ Sync Tools (Many-to-Many)
        ├─→ Log Activity
        └─→ IF status='published': Notify Followers
                │
                ↓
    Redirect: /projects/{id} (Detail Page)
```

---

### **3. Alur Challenge (Dosen → Mahasiswa)**

#### **A. Dosen Buat Challenge**

```
Dosen Login
    │
    ↓
Navigate: /challenges/create
    │
    ↓
┌──────────────────────────────┐
│  Form Create Challenge        │
│  - Title                      │
│  - Description                │
│  - Requirements               │
│  - Category                   │
│  - Start Date                 │
│  - Deadline                   │
│  - Max Participants           │
│  - Prizes (1st, 2nd, 3rd)     │
│  - Criteria (Add Multiple):   │
│    ├─ Kreativitas (30%)       │
│    ├─ Fungsionalitas (25%)    │
│    └─ UI/UX (20%)             │
│  - Status: [ ] Draft          │
│            [ ] Open           │
└───────────────┬───────────────┘
                │
                ↓
    [Save] → ChallengeService::createChallenge()
                │
                ├─→ Save Challenge
                ├─→ Save Criteria
                └─→ IF status='open': Notify All Users
                │
                ↓
    Redirect: /challenges/{id}
```

#### **B. Mahasiswa Submit Project**

```
Mahasiswa Login
    │
    ↓
Browse: /challenges (Filter: status='open')
    │
    ↓
Click: Challenge Card
    │
    ↓
┌──────────────────────────────┐
│  Detail Challenge             │
│  - Deskripsi lengkap          │
│  - Deadline (Countdown Timer) │
│  - Criteria & Bobot           │
│  - Prizes                     │
│  [Button] Submit Project      │
└───────────────┬───────────────┘
                │
                ↓
    Modal: Select Project
    ┌──────────────────────────┐
    │  Pilih dari My Projects:  │
    │  [ ] Project A            │
    │  [ ] Project B            │
    │  [New Project] Upload     │
    │  Notes: (Optional)        │
    │  [Submit]                 │
    └───────────┬───────────────┘
                │
                ↓
    ChallengeService::submitToChallenge()
        │
        ├─→ Validate (Deadline belum lewat, belum submit sebelumnya)
        ├─→ Save Submission (status='pending')
        └─→ Notify Challenge Creator
                │
                ↓
    Success: "Submission berhasil! Tunggu hasil review."
```

#### **C. Dosen Review & Grading**

```
Dosen Login
    │
    ↓
Navigate: /challenges/{id}/submissions
    │
    ↓
┌─────────────────────────────────────┐
│  Daftar Submissions                  │
│  ┌─────────────────────────────────┐│
│  │ [Project Title]                 ││
│  │ by: Nama Mahasiswa              ││
│  │ Submitted: 2 hari lalu          ││
│  │ [Button] Review                 ││
│  └─────────────────────────────────┘│
└───────────────┬─────────────────────┘
                │
                ↓
    Click: [Review]
                │
                ↓
┌─────────────────────────────────────┐
│  Grading Form                        │
│  - Preview Project                   │
│  - Scoring per Criteria:             │
│    ├─ Kreativitas: [80] /100        │
│    ├─ Fungsionalitas: [85] /100     │
│    └─ UI/UX: [75] /100               │
│  - Total Score: 80.5 (auto calc)    │
│  - Feedback: [Text Area]             │
│  [Submit Grade]                      │
└───────────────┬─────────────────────┘
                │
                ↓
    ChallengeService::gradeSubmission()
        │
        ├─→ Calculate Total Score (weighted)
        ├─→ Save Score & Feedback
        └─→ Notify Mahasiswa
                │
                ↓
    Success: "Grading saved!"
```

#### **D. Pengumuman Pemenang**

```
All Submissions Reviewed
    │
    ↓
Dosen: Navigate to Challenge Detail
    │
    ↓
[Button] Announce Winners
    │
    ↓
┌─────────────────────────────────────┐
│  Select Winners                      │
│  - Juara 1: [Dropdown submission]   │
│  - Juara 2: [Dropdown submission]   │
│  - Juara 3: [Dropdown submission]   │
│  [Confirm]                           │
└───────────────┬─────────────────────┘
                │
                ↓
    ChallengeService::announceWinners()
        │
        ├─→ Update submission.ranking
        ├─→ Update submission.status = 'winner'
        ├─→ Update challenge.status = 'completed'
        ├─→ Notify All Participants
        └─→ Create Activity Log
                │
                ↓
    Public Display: Podium Winners di Challenge Page
```

---

### **4. Alur Social Interaction (Like, Comment, Save)**

#### **Like Project**

```
User View Project Detail
    │
    ↓
Click: [♡ Like Button]
    │
    ↓
AJAX Request: POST /projects/{id}/like
    │
    ↓
IF already liked:
    → Remove like (DELETE record)
    → Decrement project.like_count
ELSE:
    → Create like (INSERT record)
    → Increment project.like_count
    → IF like_count % 10 == 0: Notify Owner (milestone)
    │
    ↓
Return: { liked: true/false, like_count: X }
    │
    ↓
Update UI: [♥ Liked] + Counter
```

#### **Comment on Project**

```
User View Project Detail
    │
    ↓
Scroll to Comment Section
    │
    ↓
Type Comment + Click [Send]
    │
    ↓
POST /comments
    │
    ↓
CommentService::createComment()
    │
    ├─→ Save Comment
    ├─→ Increment project.comment_count
    ├─→ Notify Project Owner
    ├─→ Check @mention → Notify mentioned users
    └─→ IF reply: Notify parent comment owner
    │
    ↓
Return: Comment Data (with user info)
    │
    ↓
Append Comment to List (No Page Reload)
```

#### **Save Project**

```
User Browse Projects
    │
    ↓
Click: [⏷ Save Button] di Project Card
    │
    ↓
AJAX: POST /projects/{id}/save
    │
    ↓
IF already saved:
    → Remove save
ELSE:
    → Create save
    → Increment project.save_count
    │
    ↓
Update UI: [✓ Saved]
    │
    ↓
Access Saved Projects: /saved
```

---

### **5. Alur Dashboard Adaptif**

```
User Login
    │
    ↓
Redirect: /dashboard
    │
    ↓
DashboardController::index()
    │
    ├─→ Get user.role
    │
    ├─→ IF role = 'superadmin':
    │       ├─ Fetch: total_users, total_projects, pending_reviews
    │       ├─ Fetch: recent_activities (10 latest)
    │       └─ Render: Dashboard.vue with AdminStats Component
    │
    ├─→ IF role = 'dosen':
    │       ├─ Fetch: my_challenges_count, submissions_need_grading
    │       ├─ Fetch: active_challenges (with submissions count)
    │       └─ Render: Dashboard.vue with DosenOverview Component
    │
    └─→ IF role = 'mahasiswa':
            ├─ Fetch: my_projects_count, total_likes, challenges_joined
            ├─ Fetch: my_projects (5 latest)
            ├─ Fetch: leaderboard_position (optional)
            └─ Render: Dashboard.vue with MahasiswaProjects Component
    │
    ↓
Single Dashboard.vue dengan Conditional Rendering
```

**Implementasi Conditional di Vue:**

```
Template Dashboard.vue:

<div v-if="role === 'superadmin'">
    <AdminStats :stats="stats" />
    <RecentActivity :activities="recent_activities" />
</div>

<div v-else-if="role === 'dosen'">
    <DosenOverview :stats="stats" :challenges="active_challenges" />
</div>

<div v-else>
    <MahasiswaProjects :stats="stats" :projects="my_projects_list" />
    <LeaderboardWidget />
</div>
```

---

## 🔒 KONSEP AUTHORIZATION

### **Middleware Strategy**

#### **1. CheckRole Middleware**
```
Fungsi: Cek role user untuk akses route tertentu
Contoh:
- Route::middleware(['role:superadmin']) → Admin only
- Route::middleware(['role:dosen,superadmin']) → Dosen atau Admin
```

#### **2. CheckRegistrationCompleted Middleware**
```
Fungsi: Redirect ke /complete-profile jika belum lengkap
Attach ke: semua route protected kecuali /complete-profile
```

#### **3. TrackProjectView Middleware**
```
Fungsi: Auto increment view_count saat user akses project detail
Logic:
- Track unique view per IP per 24 jam
- Save ke project_views table
- Update project.view_count (cache)
```

---

### **Policy Authorization**

Setiap Model punya Policy file untuk define aturan akses.

**Kapan dipakai:**
- Sebelum CRUD operation
- Di Controller: `$this->authorize('update', $project);`
- Di Blade/Vue: `@can('update', $project)` atau `can('update', project)`

**Keuntungan:**
- Centralized authorization logic
- Mudah testing
- Reusable di berbagai controller

---

## 📈 KONSEP ANALYTICS & REPORTING

### **Data yang Ditrack**

#### **1. Project Analytics**
```
Per Project:
- Total Views (unique & non-unique)
- Total Likes
- Total Saves
- Total Shares (per platform)
- Total Comments
- View Source (public gallery, profile, challenge)
- Peak View Time (kapan paling banyak dilihat)
```

#### **2. User Analytics**
```
Per User:
- Total Projects
- Total Likes Received
- Total Comments Received
- Profile Views
- Engagement Rate: (likes + comments) / views * 100
- Follower Count (future feature)
```

#### **3. Challenge Analytics**
```
Per Challenge:
- Total Participants
- Submission Rate: submitted / registered * 100
- Average Score
- Submission Timeline (chart)
- Winner Projects (highlight)
```

#### **4. System Analytics (Admin)**
```
Global:
- User Growth (chart per bulan)
- Project Growth (chart per bulan)
- Most Used Tools/Stack
- Most Popular Categories
- Top Creators (leaderboard)
- Engagement Metrics (total likes, comments)
```

---

### **Leaderboard System**

#### **Kategori Leaderboard**

**1. Top Creators (Based on Engagement)**
```
Query:
users.projects_count + 
users.total_likes_received + 
users.total_comments_received + 
users.challenge_wins_count

Sort: DESC
Limit: 10
```

**2. Most Liked Projects**
```
Query: projects.like_count
Period: All Time / This Month / This Week
Sort: DESC
Limit: 10
```

**3. Most Viewed Projects**
```
Query: projects.view_count
Period: All Time / This Month
Sort: DESC
Limit: 10
```

**4. Challenge Winners Hall of Fame**
```
Query: challenge_submissions WHERE ranking IS NOT NULL
Display: Challenge Name, Winner Name, Project Title, Score
```

---

## 🎨 KONSEP UI/UX GUIDELINES

### **Design System**

#### **Color Palette**
```
Primary: #3B82F6 (Blue)        → Buttons, Links, Primary Actions
Secondary: #8B5CF6 (Purple)     → Secondary Actions, Badges
Success: #10B981 (Green)        → Success Messages, Publish Status
Warning: #F59E0B (Amber)        → Warning, Pending Status
Danger: #EF4444 (Red)           → Delete, Error Messages
Gray: #6B7280 (Neutral)         → Text, Borders, Backgrounds

Background:
- Light Mode: #F9FAFB (Gray-50)
-## 🎨 KONSEP UI/UX GUIDELINES (Lanjutan)

### **Design System (Lanjutan)**

#### **Color Palette (Lanjutan)**
```
Background:
- Light Mode: #F9FAFB (Gray-50)
- Dark Mode: #111827 (Gray-900)

Text:
- Primary: #111827 (Gray-900)
- Secondary: #6B7280 (Gray-500)
- Disabled: #D1D5DB (Gray-300)

Surfaces:
- Card/Modal: #FFFFFF (White)
- Hover: #F3F4F6 (Gray-100)
- Border: #E5E7EB (Gray-200)
```

#### **Typography**
```
Font Family: 'Inter', sans-serif

Heading:
- H1: 36px / 2.25rem (font-bold)
- H2: 30px / 1.875rem (font-bold)
- H3: 24px / 1.5rem (font-semibold)
- H4: 20px / 1.25rem (font-semibold)
- H5: 18px / 1.125rem (font-medium)

Body:
- Large: 18px / 1.125rem (font-normal)
- Regular: 16px / 1rem (font-normal)
- Small: 14px / 0.875rem (font-normal)
- Tiny: 12px / 0.75rem (font-normal)

Line Height:
- Heading: 1.2
- Body: 1.5
- Compact: 1.3
```

#### **Spacing System (Tailwind Based)**
```
xs: 0.25rem (4px)
sm: 0.5rem (8px)
md: 1rem (16px)
lg: 1.5rem (24px)
xl: 2rem (32px)
2xl: 3rem (48px)
3xl: 4rem (64px)
```

#### **Border Radius**
```
sm: 0.25rem (4px)   → Input, Small Buttons
md: 0.375rem (6px)  → Cards, Buttons
lg: 0.5rem (8px)    → Modals, Large Cards
xl: 0.75rem (12px)  → Feature Cards
full: 9999px        → Avatar, Pills, Badges
```

#### **Shadows**
```
sm: 0 1px 2px rgba(0,0,0,0.05)           → Subtle elevation
md: 0 4px 6px rgba(0,0,0,0.1)            → Cards
lg: 0 10px 15px rgba(0,0,0,0.1)          → Modals, Dropdowns
xl: 0 20px 25px rgba(0,0,0,0.1)          → Hero sections
inner: inset 0 2px 4px rgba(0,0,0,0.06)  → Input focus
```

---

### **Component Design Patterns**

#### **1. Project Card**
```
Struktur:
┌──────────────────────────┐
│  [Thumbnail Image]        │ → Aspect Ratio 16:9
│  ┌─────────────────────┐ │
│  │ Featured Badge      │ │ → Conditional (is_featured)
│  └─────────────────────┘ │
├──────────────────────────┤
│  [Category Badge]         │ → Color-coded by kategori
│  Project Title (2 lines) │ → Truncate overflow
│  Description (3 lines)   │ → Truncate overflow
├──────────────────────────┤
│  [Avatar] Author Name    │
│  [Stack Icons] (max 5)   │ → Teknologi yang dipakai
├──────────────────────────┤
│  👁 Views  ♥ Likes  💾   │ → Engagement stats
│  [Button Actions]        │ → Like, Save, Share
└──────────────────────────┘

Interactions:
- Hover: Scale 1.02 + Shadow increase
- Click Card: Navigate to detail
- Click Avatar: Navigate to profile
- Click Actions: Toggle state tanpa reload
```

#### **2. Challenge Card**
```
Struktur:
┌──────────────────────────┐
│  [Status Badge]           │ → Open/Review/Completed
│  Challenge Title          │
│  by: Dosen Name           │
├──────────────────────────┤
│  📅 Deadline: X hari lagi │ → Countdown timer
│  👥 X Peserta             │
│  🏆 Hadiah: Rp XXX        │
├──────────────────────────┤
│  [View Details Button]    │
│  [Submit Project Button]  │ → Conditional (if open)
└──────────────────────────┘

Visual States:
- Open: Green border + Pulsing badge
- Review: Yellow border + "Sedang dinilai"
- Completed: Gray border + Winner badge
```

#### **3. Dashboard Stat Card**
```
Struktur:
┌──────────────────────────┐
│  [Icon]  Label            │
│  [Angka Besar]            │ → 48px Bold
│  ↑ +X% dari bulan lalu    │ → Growth indicator
└──────────────────────────┘

Variants:
- Primary: Blue gradient background
- Success: Green gradient
- Warning: Yellow gradient
- Info: Purple gradient

Icon Position: Top-left atau left-center
Size: 64x64px card, 32x32px icon
```

#### **4. Comment Item**
```
Struktur:
┌──────────────────────────────────┐
│ [Avatar] Username • 2 jam lalu   │
│            [Badge] Pemilik Project│ → Conditional
│                                   │
│ Isi komentar bisa multi-line...  │
│ Supports @mention dan **bold**   │
│                                   │
│ ♥ 5 Likes  💬 Reply  ⚙ Edit/Del  │
│                                   │
│   ┌─────────────────────────────┐│
│   │ [Nested Reply 1]            ││ → Max depth: 2 level
│   │ [Nested Reply 2]            ││
│   └─────────────────────────────┘│
└──────────────────────────────────┘

Interactions:
- Click Reply: Show reply form inline
- Click Like: Toggle (heart animation)
- Hover Edit: Show dropdown (Edit/Delete)
- Real-time: New comment muncul tanpa reload
```

#### **5. Filter Panel**
```
Struktur:
┌──────────────────────────┐
│  🔍 Search               │ → Input pencarian
├──────────────────────────┤
│  📁 Kategori             │
│  [ ] Skripsi (45)        │ → Checkbox + count
│  [ ] PKM (23)            │
│  [ ] Tugas Kuliah (78)   │
├──────────────────────────┤
│  🛠 Teknologi            │
│  [ ] Laravel (34)        │
│  [ ] Vue.js (29)         │
│  [+] Tampilkan semua     │ → Expandable
├──────────────────────────┤
│  👤 Author               │
│  ( ) Semua               │ → Radio button
│  ( ) Mahasiswa           │
│  ( ) Dosen               │
├──────────────────────────┤
│  [Reset Filter] [Apply]  │
└──────────────────────────┘

Behavior:
- Apply filter: AJAX update tanpa reload
- Filter count: Update real-time
- Reset: Clear all + reload default
- Persistent: Save di URL query params
```

---

### **Page Layout Patterns**

#### **1. Landing Page (Public)**
```
Section Structure:

[Hero Section]
├─ Heading: "Showcase Karya Terbaik Mahasiswa UNUHA"
├─ Subheading: "Platform untuk berbagi..."
├─ CTA Buttons: [Explore Projects] [Mulai Berkarya]
└─ Hero Image/Animation

[Featured Projects Carousel]
├─ Title: "Project Unggulan"
├─ Carousel (5 featured projects)
└─ [View All]

[Statistics Section]
├─ [Stat Card] 500+ Projects
├─ [Stat Card] 200+ Creators
├─ [Stat Card] 50+ Challenges
└─ [Stat Card] 100+ Winners

[Active Challenges]
├─ Title: "Challenge Aktif"
├─ Grid (3 challenge cards)
└─ [Lihat Semua Challenge]

[Top Creators]
├─ Title: "Creator Terbaik Bulan Ini"
├─ Podium Display (Top 3)
└─ Leaderboard Table (Top 10)

[Recent Projects]
├─ Title: "Project Terbaru"
├─ Grid (8 project cards)
└─ [Lihat Semua Project]

[CTA Section]
├─ "Siap Showcase Karya Anda?"
└─ [Daftar Sekarang]

[Footer]
├─ About UNUHA
├─ Quick Links
├─ Social Media
└─ Copyright
```

#### **2. Project Gallery (Public/Auth)**
```
Layout:

[Breadcrumbs]
Home > Projects

[Page Header]
├─ Title: "Semua Project"
├─ Subtitle: "Jelajahi karya mahasiswa dan dosen"
└─ [Upload Project Button] → Conditional (auth)

[Filter Bar]
├─ [Search Input]
├─ [Dropdown] Kategori
├─ [Dropdown] Teknologi
├─ [Dropdown] Sort By (Terbaru, Terpopuler, Terbanyak Like)
└─ [Toggle] Grid/List View

[Filter Panel] (Sidebar/Drawer)
← Full filter options

[Project Grid/List]
├─ Masonry Grid (responsive)
├─ Infinite Scroll atau Pagination
└─ Empty State (jika tidak ada hasil)

Responsive:
- Desktop: Sidebar + Grid 3 columns
- Tablet: Drawer + Grid 2 columns
- Mobile: Bottom Sheet Filter + Grid 1 column
```

#### **3. Project Detail Page**
```
Layout:

[Breadcrumbs]
Home > Projects > [Category] > [Project Title]

[Hero Section]
├─ Banner Image (Full width)
├─ Title + Featured Badge
├─ Author Card (Avatar + Name + Follow Button)
├─ Engagement Stats (Views, Likes, Saves, Shares)
└─ Action Buttons (Like, Save, Share)

[Project Meta]
├─ Category Badge
├─ Tech Stack Badges (All used)
├─ Published Date
└─ Last Updated

[Project Content] (Rich Text)
├─ Description (Markdown/HTML)
├─ Features List
├─ Screenshots Gallery (Lightbox)
├─ Video Demo (Embedded YouTube)
└─ Links (Repository, Live Demo)

[Collaborators Section]
├─ Title: "Tim Pengembang"
└─ Avatar Grid (Max 6 shown)

[Related Projects]
├─ Title: "Project Serupa"
└─ Carousel (Same category/tech)

[Comment Section]
├─ Comment Count
├─ Sort (Terbaru, Terpopuler)
├─ Comment Form (Auth only)
└─ Comment List (Nested replies)

Sticky Elements:
- Action Bar (Like, Save, Share) → Sticky bottom (mobile)
- Table of Contents → Sticky sidebar (desktop)
```

#### **4. Dashboard Page (Adaptive)**
```
Layout:

[Page Header]
├─ Greeting: "Halo, [Name]! 👋"
├─ Current Date
└─ Quick Actions (conditional by role)

[Stats Grid] (Conditional Content)
├─ IF superadmin:
│   ├─ Total Users
│   ├─ Total Projects
│   ├─ Pending Reviews
│   └─ System Health
│
├─ IF dosen:
│   ├─ My Challenges
│   ├─ Submissions to Grade
│   ├─ Total Projects
│   └─ Engagement Rate
│
└─ IF mahasiswa:
    ├─ My Projects
    ├─ Total Likes
    ├─ Challenges Joined
    └─ Leaderboard Position

[Main Content Area] (Conditional)
├─ IF superadmin:
│   ├─ Recent Activities Timeline
│   ├─ User Growth Chart
│   └─ Quick Manage Links
│
├─ IF dosen:
│   ├─ Active Challenges List
│   ├─ Recent Submissions
│   └─ Performance Chart
│
└─ IF mahasiswa:
    ├─ My Projects List
    ├─ Available Challenges
    └─ Achievement Badges

[Sidebar Widgets] (Conditional)
├─ Announcements (All roles)
├─ Upcoming Deadlines (Mahasiswa, Dosen)
├─ Quick Tips (Mahasiswa)
└─ System Logs (Superadmin)

Responsive:
- Desktop: Sidebar + Main 2/3 width
- Tablet: Stack sidebar below
- Mobile: Single column, collapsible widgets
```

#### **5. Challenge Detail Page**
```
Layout:

[Header Section]
├─ Status Badge (Open/Review/Completed)
├─ Challenge Title
├─ Creator Info (Dosen name + avatar)
└─ Challenge Meta (Category, Dates, Participants)

[Countdown Section] (If status = open)
├─ Large countdown timer
└─ "Deadline: DD/MM/YYYY HH:MM"

[About Challenge] (Tabs)
├─ Tab: Deskripsi
│   └─ Rich text content
├─ Tab: Persyaratan
│   └─ Requirements list
├─ Tab: Kriteria Penilaian
│   ├─ Criteria 1 (30%)
│   ├─ Criteria 2 (25%)
│   └─ ... (dengan deskripsi)
└─ Tab: Hadiah
    ├─ 🥇 Juara 1: [Prize]
    ├─ 🥈 Juara 2: [Prize]
    └─ 🥉 Juara 3: [Prize]

[Participants Section]
├─ Total: X peserta
├─ Avatar Grid (show 12)
└─ [View All]

[Submissions] (Conditional by status)
├─ IF status = 'open':
│   └─ [Submit Project Button] (Mahasiswa only)
│
├─ IF status = 'review':
│   └─ "Sedang dalam tahap penilaian..."
│
└─ IF status = 'completed':
    ├─ [Winner Podium Display]
    ├─ All Submissions Grid
    └─ Leaderboard Table

[Submission Gallery] (Public, if completed)
├─ Filter: [All / Winners Only]
├─ Sort: [By Score / By Date]
└─ Submission Cards (with score badge)

Actions:
- Mahasiswa (if open): [Submit Project]
- Dosen (creator): [Edit] [View Submissions] [Announce Winners]
- Admin: [Edit] [Delete] [Feature]
```

---

### **Form Design Patterns**

#### **1. Upload Project Form (Multi-Section)**
```
Section 1: Informasi Dasar
┌────────────────────────────────┐
│ Judul Project *                │ → Text Input
│ [________________________]     │
│                                │
│ Kategori *                     │ → Dropdown/Select
│ [Pilih Kategori ▼]            │
│                                │
│ Deskripsi Singkat *            │ → Textarea (max 200 chars)
│ [________________________]     │
│ [________________________]     │
│ 0/200 karakter                 │
└────────────────────────────────┘

Section 2: Konten Project
┌────────────────────────────────┐
│ Konten Lengkap *               │ → Rich Text Editor
│ ┌──────────────────────────┐  │   (TipTap/Quill)
│ │ [B] [I] [U] [Link] [Img]│  │
│ ├──────────────────────────┤  │
│ │                          │  │
│ │  Editor area...          │  │
│ │                          │  │
│ └──────────────────────────┘  │
│                                │
│ Thumbnail *                    │ → Image Upload
│ [Drag & Drop atau Browse]     │
│ ┌────────────────────┐        │
│ │   [Preview Image]   │        │
│ └────────────────────┘        │
│ Max 2MB, Format: JPG, PNG     │
│                                │
│ Gallery Images (Optional)      │ → Multiple Upload
│ [+ Add Image] (Max 10)        │
│ ┌──┐ ┌──┐ ┌──┐               │
│ │1 │ │2 │ │3 │               │ → Draggable reorder
│ └──┘ └──┘ └──┘               │
└────────────────────────────────┘

Section 3: Teknologi & Links
┌────────────────────────────────┐
│ Teknologi/Tools *              │ → Multi-select Dropdown
│ [Laravel ×] [Vue.js ×] [+]    │   (Tag input style)
│                                │
│ Repository URL                 │ → Text Input
│ [https://github.com/...]      │
│                                │
│ Demo URL                       │ → Text Input
│ [https://demo.com/...]        │
│                                │
│ Video Demo (YouTube)           │ → Text Input
│ [https://youtube.com/watch...] │
└────────────────────────────────┘

Section 4: Kolaborator (Optional)
┌────────────────────────────────┐
│ Invite Collaborators           │
│ [Search user...] [Invite]     │
│                                │
│ Invited:                       │
│ ┌──────────────────────────┐  │
│ │ [Avatar] Name (Pending)  │  │
│ │          [Cancel Invite] │  │
│ └──────────────────────────┘  │
└────────────────────────────────┘

Section 5: Publikasi
┌────────────────────────────────┐
│ Status                         │
│ ( ) Save as Draft              │ → Radio button
│ ( ) Publish Now                │
│                                │
│ [Save Draft] [Publish Project]│ → Action buttons
└────────────────────────────────┘

Validasi:
- Real-time validation (on blur)
- Error message di bawah field
- Disable submit jika ada error
- Auto-save draft setiap 30 detik
- Konfirmasi sebelum leave page (unsaved changes)
```

#### **2. Create Challenge Form (Dosen)**
```
Step-by-Step Form:

Step 1: Informasi Challenge
┌────────────────────────────────┐
│ Judul Challenge *              │
│ [________________________]     │
│                                │
│ Kategori                       │
│ [Pilih Kategori ▼]            │
│                                │
│ Deskripsi *                    │ → Rich Text Editor
│ [Editor area...]               │
│                                │
│ Persyaratan Peserta            │ → Rich Text Editor
│ [Editor area...]               │
│                                │
│ Banner Image                   │ → Image Upload
│ [Drag & Drop atau Browse]     │
│                                │
│ [Lanjut ke Step 2 →]          │
└────────────────────────────────┘

Step 2: Jadwal & Batas
┌────────────────────────────────┐
│ Tanggal Mulai *                │ → Date Time Picker
│ [DD/MM/YYYY] [HH:MM]          │
│                                │
│ Deadline Submit *              │ → Date Time Picker
│ [DD/MM/YYYY] [HH:MM]          │
│                                │
│ Tanggal Pengumuman *           │ → Date Time Picker
│ [DD/MM/YYYY] [HH:MM]          │
│                                │
│ Max Peserta (Optional)         │ → Number Input
│ [____] (Kosongkan = unlimited)│
│                                │
│ [← Kembali] [Lanjut ke Step 3]│
└────────────────────────────────┘

Step 3: Kriteria Penilaian
┌────────────────────────────────┐
│ Tambah Kriteria                │
│ [+ Add Criteria]               │
│                                │
│ ┌──────────────────────────┐  │
│ │ Kriteria 1                │  │
│ │ Nama: Kreativitas         │  │
│ │ Bobot: [30] %             │  │
│ │ Deskripsi: [...]          │  │
│ │ [Move ↑↓] [Delete]        │  │
│ └──────────────────────────┘  │
│                                │
│ ┌──────────────────────────┐  │
│ │ Kriteria 2                │  │
│ │ Nama: Fungsionalitas      │  │
│ │ Bobot: [25] %             │  │
│ │ Deskripsi: [...]          │  │
│ │ [Move ↑↓] [Delete]        │  │
│ └──────────────────────────┘  │
│                                │
│ Total Bobot: 55/100%          │ → Must = 100%
│                                │
│ [← Kembali] [Lanjut ke Step 4]│
└────────────────────────────────┘

Step 4: Hadiah & Publikasi
┌────────────────────────────────┐
│ Hadiah (Optional)              │
│                                │
│ Juara 1: [________________]   │ → Text Input
│ Juara 2: [________________]   │
│ Juara 3: [________________]   │
│                                │
│ Status Publikasi               │
│ ( ) Save as Draft              │
│ ( ) Open Registration          │
│                                │
│ ┌──────────────────────────┐  │
│ │ Preview Challenge         │  │ → Summary card
│ └──────────────────────────┘  │
│                                │
│ [← Kembali] [Buat Challenge]  │
└────────────────────────────────┘

Features:
- Progress bar di top (1/4, 2/4, 3/4, 4/4)
- Save progress di localStorage (draft)
- Preview mode sebelum publish
- Validasi per step
```

#### **3. Grading Submission Form**
```
Layout:

[Project Preview Panel] (Left/Top)
┌────────────────────────────────┐
│ [Project Thumbnail]             │
│ Project Title                   │
│ by: Mahasiswa Name              │
│ ┌──────────────────────────┐  │
│ │ [View Project Detail]     │  │
│ │ [View Repository]         │  │
│ │ [View Demo]               │  │
│ └──────────────────────────┘  │
│                                │
│ Submission Notes:              │
│ "Lorem ipsum dolor sit amet..."│
└────────────────────────────────┘

[Grading Form] (Right/Bottom)
┌────────────────────────────────┐
│ Kriteria Penilaian              │
│                                │
│ 1. Kreativitas (30%)           │
│    Score: [____] / 100         │ → Number Input
│    ━━━━━━━━━━━━━━━━━━━━━━    │ → Visual slider
│                                │
│ 2. Fungsionalitas (25%)        │
│    Score: [____] / 100         │
│    ━━━━━━━━━━━━━━━━━━━━━━    │
│                                │
│ 3. UI/UX Design (20%)          │
│    Score: [____] / 100         │
│    ━━━━━━━━━━━━━━━━━━━━━━    │
│                                │
│ ... (kriteria lainnya)         │
│                                │
├────────────────────────────────┤
│ Total Score (Weighted):        │
│ [85.5] / 100                   │ → Auto calculated
│                                │
│ Grade: A                       │ → Auto assigned
│ ━━━━━━━━━━━━━━━━━━━━━━━━━   │ → Visual bar
├────────────────────────────────┤
│ Feedback untuk Peserta *       │
│ ┌──────────────────────────┐  │
│ │ [Rich text editor...]     │  │ → Textarea
│ │                           │  │
│ └──────────────────────────┘  │
│                                │
│ [Save Grade] [Submit & Next]  │
└────────────────────────────────┘

Features:
- Auto-save draft score setiap input
- Visual feedback (color-coded score)
- Keyboard shortcuts (Next submission: Ctrl+→)
- Bulk grading mode (optional)
```

---

### **Interaction & Animation**

#### **Micro-interactions**
```
1. Like Button:
   - Click: Heart scale up → Color fill → Particle burst
   - Unlike: Heart scale down → Color drain
   - Duration: 300ms

2. Save Button:
   - Click: Bookmark slide down → Fill color
   - Unsave: Bookmark slide up → Outline only
   - Duration: 250ms

3. Loading States:
   - Skeleton Screen untuk cards (shimmer effect)
   - Spinner untuk buttons (with disabled state)
   - Progress bar untuk uploads

4. Toast Notifications:
   - Slide in dari top-right
   - Auto dismiss after 5s
   - Swipe to dismiss
   - Stack multiple (max 3)

5. Modal/Dialog:
   - Fade in backdrop (opacity 0 → 0.5)
   - Scale up content (0.95 → 1)
   - Focus trap inside modal
   - ESC to close

6. Form Validation:
   - Shake animation untuk error field
   - Smooth color transition (red border)
   - Icon feedback (✓ success, ✗ error)

7. Dropdown Menu:
   - Fade + slide down (10px)
   - Highlight hover item
   - Close on outside click

8. Image Gallery:
   - Lightbox fade in
   - Swipe/Arrow navigation
   - Thumbnail preview strip
   - Zoom in/out gesture

9. Infinite Scroll:
   - Load spinner at bottom
   - Smooth append items
   - "Back to top" FAB (after 2 screens)

10. Filter Apply:
    - Ripple effect on button
    - Fade out old results
    - Fade in new results
    - URL update (history push)
```

#### **Page Transitions**
```
Route Changes (Inertia.js):
- Fade (default): opacity 0 → 1 (200ms)
- Slide: translateX(-20px) → 0 (300ms)
- No transition untuk same page update

Loading Bar:
- Top of page (2px height)
- Color: Primary brand color
- Indeterminate animation
- Hide on complete
```

#### **Responsive Behavior**
```
Breakpoints (Tailwind):
- sm: 640px
- md: 768px
- lg: 1024px
- xl: 1280px
- 2xl: 1536px

Mobile-First Approach:
- Base styles untuk mobile
- Progressive enhancement untuk tablet/desktop

Touch Interactions (Mobile):
- Larger tap targets (min 44x44px)
- Swipe gestures (dismiss notification, navigate carousel)
- Pull to refresh (pada list pages)
- Bottom sheet (untuk filter/menu)

Desktop Enhancements:
- Hover effects
- Keyboard shortcuts
- Tooltips
- Context menus
```

---

## 🔔 KONSEP NOTIFIKASI SYSTEM

### **Jenis Notifikasi**

#### **1. Real-Time Notifications (In-App)**
```
Triggers:
- New comment on my project
- Someone likes my project (every 10 likes milestone)
- Reply to my comment
- @mention in comment
- Collaboration invite received
- Collaboration invite accepted/rejected
- New challenge opened (all users)
- Challenge deadline reminder (24h before)
- Challenge result announced
- Project featured by admin
- Grade received on submission

Display:
- Bell icon (topbar) dengan badge count
- Dropdown list (max 5 latest, "View All")
- Real-time update (via polling atau websocket)
- Mark as read on view
- Click → Navigate to related page
```

#### **2. Email Notifications**
```
Send Email For:
- Email verification (on register)
- Password reset
- Collaboration invite
- Challenge result announced (winners only)
- Important system announcements

Template Structure:
- Header: Logo + Platform name
- Content: Subject + Body + CTA Button
- Footer: Unsubscribe link + Social media

Email Settings (User Preference):
[ ] Receive collaboration invites
[ ] Receive challenge updates
[ ] Receive weekly digest
[ ] Receive promotional emails
```

#### **3. Push Notifications (Future)**
```
For:
- Challenge deadline (1 day before)
- New comment while offline
- Mentioned while offline

Require:
- Service Worker setup
- Browser permission
- Web Push API integration
```

---

### **Notification UI**

#### **Notification Dropdown**
```
┌──────────────────────────────────────┐
│ Notifikasi                [Mark all] │
├──────────────────────────────────────┤
│ ● [Avatar] Username mengomentari     │
│   project "Project Title"            │
│   2 jam yang lalu                    │
├──────────────────────────────────────┤
│ ○ [Icon] Project Anda mencapai       │
│   100 likes!                         │
│   1 hari yang lalu                   │
├──────────────────────────────────────┤
│ ○ [Avatar] Dosen Name mengundang     │
│   Anda berkolaborasi                 │
│   3 hari yang lalu                   │
│   [Accept] [Reject]                  │
├──────────────────────────────────────┤
│            [Lihat Semua]             │
└──────────────────────────────────────┘

Legend:
● = Unread (bold text, colored bg)
○ = Read (normal text, white bg)
```

#### **Notification Page (/notifications)**
```
Layout:

[Header]
Notifikasi (Badge: X unread)
[Mark all as read] [Filter ▼]

[Filter Tabs]
[Semua] [Belum Dibaca] [Project] [Challenge] [Collaboration]

## 🔔 KONSEP NOTIFIKASI SYSTEM (Lanjutan)

### **Notification Page Layout (Lanjutan)**

```
[Notification List]
┌──────────────────────────────────────────────┐
│ [Date Divider] Hari Ini                      │
├──────────────────────────────────────────────┤
│ ● [Avatar] John Doe mengomentari project     │
│   "Website Portfolio"                        │
│   2 jam yang lalu                            │
│   [View Comment] [Mark as read]              │
├──────────────────────────────────────────────┤
│ ● [Icon 🏆] Challenge "Web Dev Competition"  │
│   deadline dalam 24 jam!                     │
│   5 jam yang lalu                            │
│   [View Challenge]                           │
├──────────────────────────────────────────────┤
│ [Date Divider] Kemarin                       │
├──────────────────────────────────────────────┤
│ ○ [Avatar] Jane Smith menyukai project Anda  │
│   1 hari yang lalu                           │
├──────────────────────────────────────────────┤
│ ○ [Icon ⭐] Project Anda "E-Learning App"    │
│   ditampilkan di Featured!                   │
│   1 hari yang lalu                           │
│   [View Project]                             │
├──────────────────────────────────────────────┤
│ [Date Divider] Minggu Ini                    │
├──────────────────────────────────────────────┤
│ ○ [Avatar] Dosen Ahmad mengundang Anda       │
│   berkolaborasi di "Research Project"        │
│   3 hari yang lalu                           │
│   [Accept] [Reject]                          │
├──────────────────────────────────────────────┤
│                  [Load More]                 │
└──────────────────────────────────────────────┘

Empty State (No Notifications):
┌──────────────────────────────┐
│     [Icon 🔔]                 │
│   Belum ada notifikasi       │
│   Anda akan menerima update  │
│   aktivitas di sini          │
└──────────────────────────────┘

Features:
- Infinite scroll
- Bulk actions (Select multiple → Mark as read/Delete)
- Filter by type
- Search notifications
- Auto-refresh every 30s
```

---

## 📱 KONSEP RESPONSIVE DESIGN

### **Breakpoint Strategy**

#### **Mobile First (< 640px)**
```
Layout Changes:
- Single column layout
- Sidebar → Bottom navigation atau Drawer
- Cards: Full width dengan padding
- Filter: Bottom sheet
- Tables: Horizontal scroll atau Card view

Navigation:
- Hamburger menu (top-left)
- Bottom tab bar (Home, Projects, Challenges, Profile)
- Floating Action Button untuk primary action

Forms:
- Full width inputs
- Stack label di atas input
- Large touch targets (min 48px)
- Native mobile pickers (date, select)

Project Cards:
- Full width
- Larger thumbnail (aspect 16:9)
- Stack elements vertically

Dashboard:
- Stack stat cards (1 column)
- Collapsible sections
- Priority content first
```

#### **Tablet (640px - 1024px)**
```
Layout Changes:
- 2 column grid untuk cards
- Sidebar dapat di-toggle
- Modal width: 80% viewport
- Table: Simplified columns

Navigation:
- Persistent sidebar (collapsible)
- Breadcrumbs visible
- Top navigation bar

Forms:
- 2 column layout (bila perlu)
- Side-by-side labels (optional)

Dashboard:
- 2 column stat cards
- Chart responsif
- Sidebar widgets visible
```

#### **Desktop (> 1024px)**
```
Layout Changes:
- 3-4 column grid untuk cards
- Persistent sidebar (always visible)
- Modal width: 600-800px max
- Full table columns
- Hover effects enabled

Navigation:
- Expanded sidebar dengan labels
- Multi-level breadcrumbs
- Search bar di topbar

Forms:
- Multi column layout
- Inline validation
- Keyboard shortcuts enabled

Dashboard:
- 3-4 column stat cards
- Advanced charts
- Multiple sidebars (left + right)
- Richer interactions (drag-drop, etc)

Extra Features:
- Tooltips
- Context menus (right-click)
- Keyboard navigation
- Advanced filters (always visible)
```

---

### **Touch vs Mouse Interactions**

```
Mobile/Touch:
- Tap: Primary action
- Long press: Secondary action (context menu)
- Swipe left/right: Navigate, dismiss
- Pull down: Refresh
- Pinch: Zoom (images)
- No hover states

Desktop/Mouse:
- Click: Primary action
- Right click: Context menu
- Hover: Show tooltips, highlight
- Drag: Reorder items
- Keyboard shortcuts
- Scroll: Infinite load
```

---

## 🔍 KONSEP SEARCH & FILTERING

### **Global Search**

#### **Search Bar (Topbar)**
```
Component:
┌────────────────────────────────────────┐
│ [🔍] Search projects, users, challenges│
└────────────────────────────────────────┘

On Focus:
┌────────────────────────────────────────┐
│ [🔍] Search...               [Shortcut]│
├────────────────────────────────────────┤
│ Recent Searches:                       │
│ - Web Development                      │
│ - Laravel Project                      │
│ [Clear History]                        │
├────────────────────────────────────────┤
│ Suggestions:                           │
│ 🔥 Most Searched                       │
│ - Machine Learning                     │
│ - Mobile App                           │
└────────────────────────────────────────┘

On Type (Debounce 300ms):
┌────────────────────────────────────────┐
│ [🔍] laravel                    [Clear]│
├────────────────────────────────────────┤
│ Projects (12)                          │
│ ├─ [Thumb] Laravel E-Commerce          │
│ ├─ [Thumb] Laravel Blog CMS            │
│ └─ View all projects →                 │
├────────────────────────────────────────┤
│ Users (3)                              │
│ ├─ [Avatar] John Laravel Dev           │
│ └─ View all users →                    │
├────────────────────────────────────────┤
│ Challenges (1)                         │
│ └─ [Icon] Laravel Speed Coding         │
└────────────────────────────────────────┘

Full Search Results Page (/search?q=laravel):
- Tabs: [All] [Projects] [Users] [Challenges]
- Filters: Category, Tech Stack, Date Range
- Sort: Relevance, Date, Popularity
- Results: List dengan highlight query
```

### **Advanced Filtering**

#### **Project Filter (Sidebar/Drawer)**
```
┌──────────────────────────────────┐
│ Filter Projects                  │
├──────────────────────────────────┤
│ 🔍 Search                        │
│ [____________]                   │
├──────────────────────────────────┤
│ 📁 Kategori                      │
│ ☐ Semua (150)                    │
│ ☐ Skripsi (45)                   │
│ ☐ PKM (23)                       │
│ ☐ Tugas Kuliah (67)              │
│ ☐ Project Pribadi (15)           │
├──────────────────────────────────┤
│ 🛠 Teknologi                     │
│ ☐ Laravel (34) [Remove]         │
│ ☐ Vue.js (29)                    │
│ ☐ React (18)                     │
│ ☐ MySQL (42)                     │
│ [+ Show More]                    │
├──────────────────────────────────┤
│ 👤 Author Type                   │
│ ◉ Semua                          │
│ ○ Mahasiswa Only                 │
│ ○ Dosen Only                     │
├──────────────────────────────────┤
│ 📅 Periode                       │
│ ○ Semua Waktu                    │
│ ○ Bulan Ini                      │
│ ○ 6 Bulan Terakhir               │
│ ○ Custom Range                   │
│   [From] - [To]                  │
├──────────────────────────────────┤
│ ⭐ Status                        │
│ ☐ Featured Only                  │
├──────────────────────────────────┤
│ [Reset] [Apply Filters]          │
└──────────────────────────────────┘

URL Query String:
/projects?
  q=laravel
  &category=skripsi,pkm
  &tech=laravel,vuejs
  &author=mahasiswa
  &period=6months
  &featured=1
  &sort=popular

Behavior:
- Apply filters → Update URL (history.pushState)
- Share link → Filter terapply otomatis
- Filter count update real-time
- Active filters badge di topbar
```

#### **Sort Options**
```
Dropdown:
┌──────────────────────────┐
│ Sort By:                 │
├──────────────────────────┤
│ ◉ Terbaru (Default)      │
│ ○ Terpopuler             │
│ ○ Terbanyak Like         │
│ ○ Terbanyak View         │
│ ○ A-Z (Judul)            │
│ ○ Z-A (Judul)            │
└──────────────────────────┘

Order:
- Terbaru: published_at DESC
- Terpopuler: (views + likes + comments) DESC
- Terbanyak Like: like_count DESC
- Terbanyak View: view_count DESC
- A-Z/Z-A: title ASC/DESC
```

---

## 🎯 KONSEP GAMIFICATION & ENGAGEMENT

### **Achievement System (Future Feature)**

```
Badges:
┌──────────────────────────────────┐
│ 🏆 First Project                 │
│    Upload project pertama        │
│    [Unlocked] 15 Nov 2024        │
├──────────────────────────────────┤
│ ⭐ 100 Likes                     │
│    Dapatkan 100 total likes      │
│    [Locked] 45/100               │
├──────────────────────────────────┤
│ 🔥 10 Projects                   │
│    Upload 10 projects            │
│    [Unlocked] 20 Des 2024        │
├──────────────────────────────────┤
│ 👑 Challenge Winner              │
│    Juara 1 di challenge          │
│    [Unlocked] 5 Jan 2025         │
├──────────────────────────────────┤
│ 💬 Active Commenter              │
│    50+ komentar diberikan        │
│    [Locked] 23/50                │
└──────────────────────────────────┘

Badge Rarity:
- Common (Bronze): Easy to get
- Uncommon (Silver): Medium difficulty
- Rare (Gold): Hard to get
- Legendary (Rainbow): Very rare

Display:
- Profile page showcase (top 6)
- Badge collection page (/profile/badges)
- Badge notification on unlock
- Share badge on social media
```

### **Leaderboard Mechanics**

#### **Scoring System**
```
User Score Calculation:

Base Points:
+ 10 points per project published
+ 1 point per project view
+ 5 points per project like
+ 3 points per project save
+ 2 points per comment received
+ 50 points per challenge win (1st place)
+ 30 points per challenge win (2nd place)
+ 20 points per challenge win (3rd place)
+ 15 points per collaboration

Bonus Multipliers:
× 1.5 if project is featured
× 1.2 if verified account (future)
× 2.0 if challenge winner

Total Score = Sum(Base Points) × Multipliers

Recalculation:
- Daily cron job (midnight)
- Manual trigger on significant events
- Cached for performance
```

#### **Leaderboard Types**

```
1. Overall Leaderboard
   - Rank all users by total score
   - Filter by: All Time, This Year, This Month
   - Show: Top 100

2. Category Leaderboard
   - Rank by category (Skripsi, PKM, etc)
   - Separate leaderboard per category
   - Show: Top 50

3. Technology Leaderboard
   - Rank by tech stack (Laravel, Vue, etc)
   - "Top Laravel Developers"
   - Show: Top 30

4. Monthly Rising Stars
   - New users with fastest growth
   - Reset every month
   - Show: Top 20

5. Prodi Leaderboard
   - Rank within same prodi
   - "Top Informatika Students"
   - Show: Top 50
```

#### **Leaderboard Display**

```
┌────────────────────────────────────────┐
│ 🏆 Leaderboard - Bulan Ini             │
│ [All Time ▼] [Filter: Semua Prodi ▼]  │
├────────────────────────────────────────┤
│                 PODIUM                 │
│        ┌─────────────────┐            │
│        │       #2        │            │
│   ┌────┤   [Avatar]      │────┐       │
│   │ #1 │  Jane Smith     │ #3 │       │
│   │[Av]│  1,250 pts      │[Av]│       │
│   │John│                 │ Bob│       │
│   │1500│                 │1000│       │
│   └────┴─────────────────┴────┘       │
├────────────────────────────────────────┤
│ #4  [Avatar] Alice Brown    850 pts   │
│ #5  [Avatar] Charlie Lee    820 pts   │
│ #6  [Avatar] David Kim      800 pts   │
│ ...                                    │
│ #25 [Avatar] You (Kamu)     450 pts   │ ← Highlight
│ ...                                    │
│                [Load More]             │
└────────────────────────────────────────┘

Features:
- Animated rank changes (↑↓)
- Profile link on click
- "Compare with me" button
- Export leaderboard (CSV) - admin only
```

### **Streak System**

```
Daily Streak:
- Login consecutive days
- Post/comment/like activity

Display:
┌──────────────────────────────┐
│ 🔥 Your Streak: 7 Days       │
│ ━━━━━━━━━━━━━━━━━━━━━━━━  │ Progress to 30
│ Keep it up! 23 more days     │
│ to reach 30-day badge        │
└──────────────────────────────┘

Rewards:
- 7 days: +10 bonus points
- 30 days: Badge + 50 points
- 100 days: Special badge + profile badge
- Streak freeze (1 per month, paid feature?)
```

---

## 📊 KONSEP ANALYTICS & REPORTING

### **Admin Analytics Dashboard**

#### **Overview Metrics (Cards)**
```
┌─────────────────────────────────────────────┐
│ Key Metrics (Last 30 Days)                  │
├───────────┬───────────┬──────────┬──────────┤
│ Total     │ Active    │ Projects │ Engage-  │
│ Users     │ Users     │ Posted   │ ment Rate│
│ 1,250     │ 456       │ 234      │ 68%      │
│ ↑ +15%    │ ↑ +8%     │ ↑ +23%   │ ↓ -2%    │
└───────────┴───────────┴──────────┴──────────┘

┌───────────┬───────────┬──────────┬──────────┤
│ Challenges│ Submis-   │ Comments │ Total    │
│ Active    │ sions     │ Posted   │ Likes    │
│ 8         │ 145       │ 1,890    │ 5,432    │
│ ↑ +2      │ ↑ +45%    │ ↑ +12%   │ ↑ +18%   │
└───────────┴───────────┴──────────┴──────────┘
```

#### **User Growth Chart**
```
┌─────────────────────────────────────────┐
│ User Registration Trend                 │
│                                         │
│ [Line Chart]                            │
│ Y-axis: User Count                      │
│ X-axis: Months (Jan - Dec)              │
│                                         │
│ Lines:                                  │
│ - Total Users (Blue)                    │
│ - Mahasiswa (Green)                     │
│ - Dosen (Orange)                        │
│                                         │
│ [Export CSV] [Filter Date Range]        │
└─────────────────────────────────────────┘
```

#### **Project Activity Heatmap**
```
┌─────────────────────────────────────────┐
│ Project Upload Activity (Last 12 Weeks) │
│                                         │
│     M  T  W  T  F  S  S                 │
│ W1  ▢  ▢  ▪  ▪  ▢  ▫  ▫               │
│ W2  ▢  ▪  ▪  ▢  ▫  ▫  ▢               │
│ W3  ▪  ▪  ▢  ▪  ▪  ▫  ▢               │
│ ...                                     │
│                                         │
│ Legend: ▫ 0-2  ▢ 3-5  ▪ 6+            │
└─────────────────────────────────────────┘
```

#### **Top Categories (Pie Chart)**
```
┌─────────────────────────────────────────┐
│ Project Distribution by Category        │
│                                         │
│ [Pie Chart]                             │
│ - Skripsi: 35% (120 projects)          │
│ - Tugas Kuliah: 28% (96 projects)      │
│ - PKM: 20% (68 projects)                │
│ - Project Pribadi: 12% (41 projects)   │
│ - Lainnya: 5% (17 projects)             │
└─────────────────────────────────────────┘
```

#### **Most Used Technologies (Bar Chart)**
```
┌─────────────────────────────────────────┐
│ Top 10 Technologies                     │
│                                         │
│ Laravel    ████████████████ 234        │
│ Vue.js     ████████████ 189            │
│ MySQL      ██████████ 156              │
│ React      ████████ 123                │
│ Bootstrap  ██████ 98                   │
│ Tailwind   █████ 87                    │
│ Node.js    ████ 76                     │
│ MongoDB    ███ 54                      │
│ Flutter    ██ 43                       │
│ Python     ██ 39                       │
└─────────────────────────────────────────┘
```

### **User/Creator Analytics**

#### **Project Performance (Dosen/Mahasiswa)**
```
My Projects Analytics (Last 30 Days)

┌───────────┬───────────┬──────────┬──────────┐
│ Total     │ Total     │ Total    │ Engage-  │
│ Views     │ Likes     │ Comments │ ment %   │
│ 2,450     │ 345       │ 89       │ 17.7%    │
│ ↑ +234    │ ↑ +45     │ ↑ +12    │ ↑ +2.3%  │
└───────────┴───────────┴──────────┴──────────┘

Project Performance Table:
┌─────────────────┬───────┬───────┬──────────┐
│ Project         │ Views │ Likes │ Comments │
├─────────────────┼───────┼───────┼──────────┤
│ E-Commerce Web  │ 1,234 │ 156   │ 34       │
│ Mobile App      │ 876   │ 123   │ 28       │
│ Portfolio Site  │ 340   │ 66    │ 27       │
│ ...             │       │       │          │
└─────────────────┴───────┴───────┴──────────┘

Views Over Time (Line Chart):
- 7 Days
- 30 Days
- 3 Months
- 1 Year

Traffic Sources:
- Direct: 45%
- Search: 30%
- Profile: 15%
- Challenge: 10%
```

---

## 🔐 KONSEP SECURITY & PERMISSIONS

### **Role-Based Access Control (RBAC)**

#### **Permission Matrix**

```
Feature                    | Superadmin | Dosen | Mahasiswa | Guest
---------------------------|------------|-------|-----------|-------
View Public Projects       |     ✓      |   ✓   |     ✓     |   ✓
View Project Detail        |     ✓      |   ✓   |     ✓     |   ✓
Create Project             |     ✓      |   ✓   |     ✓     |   ✗
Edit Own Project           |     ✓      |   ✓   |     ✓     |   ✗
Edit Any Project           |     ✓      |   ✗   |     ✗     |   ✗
Delete Own Project         |     ✓      |   ✓   |     ✓     |   ✗
Delete Any Project         |     ✓      |   ✗   |     ✗     |   ✗
Feature Project            |     ✓      |   ✗   |     ✗     |   ✗
Like/Save Project          |     ✓      |   ✓   |     ✓     |   ✗
Comment on Project         |     ✓      |   ✓   |     ✓     |   ✗
Create Challenge           |     ✓      |   ✓   |     ✗     |   ✗
Edit Own Challenge         |     ✓      |   ✓   |     ✗     |   ✗
Grade Submission           |     ✓      |   ✓*  |     ✗     |   ✗
Submit to Challenge        |     ✓      |   ✓   |     ✓     |   ✗
View Users List            |     ✓      |   ✗   |     ✗     |   ✗
Create/Edit Users          |     ✓      |   ✗   |     ✗     |   ✗
Delete Users               |     ✓      |   ✗   |     ✗     |   ✗
Manage Master Data         |     ✓      |   ✗   |     ✗     |   ✗
View Analytics (Global)    |     ✓      |   ✗   |     ✗     |   ✗
View Analytics (Own)       |     ✓      |   ✓   |     ✓     |   ✗
Manage Site Settings       |     ✓      |   ✗   |     ✗     |   ✗
View Activity Logs         |     ✓      |   ✗   |     ✗     |   ✗

* Hanya untuk challenge yang dia buat
```

### **Data Validation & Sanitization**

```
Input Validation Rules:

User Registration:
- name: required, string, max:255
- email: required, email, unique:users
- password: required, min:8, confirmed

Project Creation:
- title: required, string, max:255
- category_id: required, exists:kategoris,id
- description: required, string, max:500
- content: required, string
- thumbnail: nullable, image, max:2048 (2MB)
- repository_url: nullable, url
- demo_url: nullable, url
- tools: required, array, min:1
- tools.*: exists:tools,id

Challenge Creation:
- title: required, string, max:255
- description: required, string
- start_date: required, date, after:now
- deadline: required, date, after:start_date
- criteria: required, array, min:1
- criteria.*.name: required, string
- criteria.*.weight: required, numeric, min:0, max:100
- total_weight: sum must equal 100

Comment:
- content: required, string, max:1000
- parent_id: nullable, exists:comments,id

XSS Prevention:
- Sanitize HTML input (strip dangerous tags)
- Use htmlspecialchars() untuk output
- CSP (Content Security Policy) headers

SQL Injection Prevention:
- Always use Eloquent ORM atau Query Builder
- Never use raw SQL dengan user input
- Use parameter binding

CSRF Protection:
- Laravel CSRF token di semua forms
- Verify token di setiap POST request
- Refresh token on login
```

### **File Upload Security**

```
Allowed File Types:
Images:
- MIME: image/jpeg, image/png, image/gif, image/webp
- Extensions: .jpg, .jpeg, .png, .gif, .webp
- Max Size: 2MB (thumbnail), 5MB (banner/gallery)

Documents (future):
- MIME: application/pdf
- Extensions: .pdf
- Max Size: 10MB

Validation Process:
1. Check file extension
2. Verify MIME type (real, not spoofed)
3. Check file size
4. Scan for malware (ClamAV integration - optional)
5. Rename file (UUID-based)
6. Store in secure location (storage/app/public)
7. Generate thumbnails (intervention/image)

Storage Structure:
storage/app/public/
├── avatars/
│   └── [user_id]/
│       └── [uuid].jpg
├── projects/
│   ├── thumbnails/
│   │   └── [project_id]/
│   │       └── [uuid].jpg
│   ├── banners/
│   └── gallery/
└── challenges/
    └── banners/
```

### **Rate Limiting**

```
API Rate Limits:

Guest:
- 60 requests per minute
- 1000 requests per day

Authenticated:
- 120 requests per minute
- 5000 requests per day

Specific Endpoints:
- Login: 5 attempts per minute per IP
- Register: 3 attempts per 5 minutes per IP
- Comment: 10 per minute per user
- Like/Save: 60 per minute per user
- Upload Project: 5 per hour per user
- Create Challenge: 3 per hour per user

Implementation:
- Laravel throttle middleware
- Redis untuk counter storage
- Custom 429 error page
```

---

## 🚀 KONSEP DEPLOYMENT & PERFORMANCE

### **Server Requirements**

```
Minimum:
- PHP 8.1+
- MySQL 8.0+ atau PostgreSQL 13+
- Nginx atau Apache
- Node.js 18+ (untuk build assets)
- Composer 2.x
- Redis (untuk cache & queues)

Recommended:
- PHP 8.2+
- MySQL 8.0+
- Nginx (lebih performant untuk static files)
- Redis 7.0+
- PHP OPcache enabled
- SSL Certificate (Let's Encrypt)

Server Specs (for 1000 concurrent users):
- CPU: 4 cores
- RAM: 8GB
- Storage: 100GB SSD
- Bandwidth: 100Mbps
```

### **Performance Optimization**

#### **Database Optimization**
```
Indexing Strategy:
- users: email, username, role
- projects: slug, status, user_id, category_id, published_at
- challenges: status, creator_id, deadline
- comments: project_id, user_id
- interactions: (user_id, project_id, type)

Query Optimization:
- Eager loading (with, load) untuk relasi
- Select only needed columns
- Use chunk() untuk large datasets
- Index foreign keys
- Use EXPLAIN untuk analyze queries

Caching Strategy:
- Cache featured projects (1 hour)
- Cache leaderboard (30 minutes)
- Cache user stats (15 minutes)
- Cache category counts (1 hour)
- Use cache tags untuk group invalidation
```

#### **Asset Optimization**
```
Images:
- Compress on upload (80% quality JPEG)
- Generate multiple sizes (thumb, medium, large)
- Use WebP format (dengan fallback)
- Lazy loading untuk images
- CDN untuk static assets

CSS/JS:
- Vite build (minify & bundle)
- Code splitting (dynamic imports)
- Tree shaking (remove unused code)
- Gzip compression
- Browser caching headers

Fonts:
- Use system fonts atau self-host
- Font display: swap
- Subset fonts (hanya karakter yang dipakai)
```

#### **Frontend Performance**
```
Inertia.js Optimization:
- Partial reloads (only: ['posts'])
- Prefetch links on hover
- Defer non-critical data
- Use shared data wisely

Vue.js Optimization:
- Use v-show untuk frequent toggles
- Use v-if untuk conditional rendering
- Lazy load components
- Virtual scrolling untuk long lists
- Memoization (computed properties)

Loading Strategy:
- Skeleton screens (initial load)
- Progressive loading (scroll)
- Debounce search input (300ms)
- Throttle scroll events
```

### **Monitoring & Logging**

```
Application Monitoring:
- Laravel Telescope (development)
- Sentry (error tracking)
- New Relic atau DataDog (APM)

Log Levels:
- ERROR: Critical errors (500)
- WARNING: Non-critical issues
- INFO: Important events (login, upload)
- DEBUG: Development only

Log Rotation:
- Daily rotation
- Keep 14 days
- Compress old logs
- Separate log files (error.log, access.log)

Metrics to Track:
- Response time (avg, p95, p99)
- Error rate
- Database query time
- Cache hit rate
- Queue processing time
- Disk usage
- Memory usage
```

---

## 📝 KONSEP DOKUMENTASI

### **User Documentation**

```
Help Center Pages:

1. Getting Started
   - Cara membuat akun
   - Lengkapi profile
   - Navigasi dashboard

2. Upload Project
   - Step-by-step guide
   - Best practices
   - Tips optimasi thumbnail
   - Cara invite collaborators

3. Challenges
   - Cara join challenge
   - Submit project
   - Kriteria penilaian

4. Engagement
   - Like, save, share
   - Komentar & replies
   - Notifikasi

5. Profile
   - Edit profile

   dll


PERHATIAN!! YANG TAMPIL DI WEB HARUS BERBAHASA INDONESIA
INI PAKAI LARAVEL 12 + VUE STATER KIT RESMI DARI LARAVEL PAKAI INERTIA

---

## ✅ IMPLEMENTASI MENU DAN CRUD BERDASARKAN ROLE

### **Ringkasan Implementasi**

Sistem CRUD lengkap dengan menu berdasarkan role telah berhasil diimplementasikan dengan fitur filtering, pagination, bulk delete, dan form management.

#### **🔴 SUPERADMIN (Administrator Pusat)**
**Manajemen User (CRUD Lengkap):**
- **Create (Buat):** Dapat mendaftarkan akun baru untuk Dosen atau Mahasiswa secara manual
  - Form: `/admin/users/create` dengan validasi email & username unik
  - Fitur: Set role (superadmin/dosen/mahasiswa) dan status aktif
  
- **Read (Lihat):** Dapat melihat detail profil lengkap seluruh pengguna
  - List: `/admin/users` dengan pagination (15 per halaman)
  - Detail: `/admin/users/{id}` dengan info akun & profile spesifik
  - Filter: By role (superadmin/dosen/mahasiswa), status (aktif/tidak aktif), registration status
  - Search: By name, email, username
  
- **Update (Ubah):** Dapat mengedit data profil pengguna
  - Form: `/admin/users/{id}/edit`
  - Fitur: Edit name, email, username, role, status aktif
  - Bonus: Reset password, toggle status akun
  
- **Delete (Hapus):** Dapat menghapus akun dari sistem
  - Single delete: Via delete button di detail page
  - Bulk delete: Select multiple items di list, hapus sekaligus
  - Soft delete: Data tersimpan di database dengan deleted_at timestamp

**Manajemen Dosen:**
- Full CRUD untuk data dosen dengan profile khusus
- Form create/edit: `/admin/dosen/create`, `/admin/dosen/{id}/edit`
- Field: name, email, username, password, NIDN, Program Studi, Jabatan, Bidang Keahlian
- List dengan filter: By Prodi, Status
- Pagination: 15 per halaman

**Manajemen Mahasiswa:**
- Full CRUD untuk data mahasiswa dengan profile khusus
- Form create/edit: `/admin/mahasiswa/create`, `/admin/mahasiswa/{id}/edit`
- Field: name, email, username, password, NIM, Program Studi, Angkatan, Semester
- List dengan filter: By Prodi, Angkatan, Status
- Pagination: 15 per halaman

**Manajemen Master Data:**
- Program Studi (Prodi): Create, Read, Update, Delete dengan bulk delete
- Kategori Project: Create, Read, Update, Delete dengan bulk delete
- Tools/Teknologi: Create, Read, Update, Delete dengan bulk delete
- All dengan filtering, searching, pagination

**Fitur Admin Umum:**
- ✅ Pagination: Semua list menampilkan 15 data per halaman dengan navigasi
- ✅ Bulk Select: Checkbox di header untuk select semua, dengan tombol "Hapus Pilihan"
- ✅ Filtering: Multiple filter options per halaman (dropdown & text input)
- ✅ Search: Real-time search by multiple fields
- ✅ Breadcrumbs: Navigation path di setiap halaman
- ✅ Actions: View, Edit, Delete buttons per item
- ✅ Error Handling: Validasi form dengan error messages
- ✅ Responsive: Design mobile-friendly dengan grid layout

#### **🔵 DOSEN**
**Manajemen Challenge (Kompetisi):**
- Membuat dan mengelola kompetisi (Create, Edit, Delete challenge sendiri)
- Menentukan kriteria penilaian
- Menu: Project Saya → Manajemen Challenge

**Penilaian (Juri):**
- Memeriksa submission mahasiswa
- Memberikan nilai (scoring) dan feedback
- Menentukan pemenang
- Menu: Penilaian Challenge

**Manajemen Project Pribadi:**
- Mengupload dan mengelola portfolio penelitian atau pengabdian dosen sendiri
- Menu: Project Saya

**Profil Dosen:**
- Mengupdate data akademis pribadi (NIDN, Jabatan, Link Scholar/Scopus)
- Menu: Profil Dosen

**Kolaborasi:**
- Menerima atau menolak ajakan kolaborasi project

#### **🟢 MAHASISWA**
**Manajemen Project (Portfolio):**
- Mengupload karya (Create), mengedit detail (Update), mengatur status publish/draft
- Menu: Project Saya

**Partisipasi Challenge:**
- Mendaftar dan mengirimkan (Submit) project ke dalam challenge
- Menu: Ikuti Challenge

**Kolaborasi Tim:**
- Mengundang teman untuk bergabung dalam satu project
- Menerima undangan kolaborasi
- Menu: Kolaborasi

**Interaksi Sosial:**
- Memberikan like, komentar, menyimpan (bookmark) project karya orang lain

**Profil Mahasiswa:**
- Mengelola biodata diri, skill, tautan media sosial (LinkedIn/GitHub)
- Menu: Profil Mahasiswa

### **Struktur Implementasi**

1. **Frontend (Vue.js + TypeScript):**
   - Komponen reusable: `AdminDataTable.vue`, `AdminFilterBar.vue`
   - Pages: `resources/js/Pages/Admin/{Users,Dosen,Mahasiswa,Prodis,Kategoris,Tools}/{Index,Create,Edit,Show}.vue`
   - Form handling: Inertia Form dengan validation errors
   - Fitur: Pagination, bulk select, filtering, searching

2. **Backend (Laravel):**
   - Routes: `routes/web.php` dengan resource routes & custom routes
   - Controllers: Middleware `admin.superadmin` untuk protection
   - Methods: index, create, store, show, edit, update, destroy, bulkDelete
   - Validasi: Unique fields (email, username, NIM, NIDN), confirmed passwords
   - Relations: Eager loading untuk performa (profileMahasiswa, profileDosen, prodi)

3. **Database:**
   - Users table dengan role enum (superadmin, dosen, mahasiswa)
   - profile_mahasiswas dengan NIM, Prodi, Angkatan, Semester
   - profile_dosens dengan NIDN, Prodi, Jabatan, Bidang Keahlian
   - Soft deletes untuk semua model

4. **Full Bahasa Indonesia:**
   - Semua menu, label, placeholder, error messages, breadcrumbs dalam bahasa Indonesia
   - Toast notifications dalam bahasa Indonesia
   - Pagination text: "Menampilkan data", "dipilih", "Hapus Pilihan"