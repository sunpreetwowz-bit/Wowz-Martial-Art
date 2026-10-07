# Wowz Martial Art

Laravel academy website for **Wowz Martial Art** — a taekwondo & martial arts school with public marketing pages, admin CMS, and a student portal for belt tests, payments, and certificates.

**Branches**
- Kundan International School, Sector 46, Chandigarh  
- Ramgharia Bhawan, Sector 27, Chandigarh  
- Intensity Martial Art and Fitness, Zirakpur  

---

## Features

### Public website
- Home, About, Team, Programs (services), Events, Gallery, Achievements  
- **Our Black Belts** — students promoted to dan rank  
- Contact form with preferred branch + Google Maps for all 3 locations  
- Certificate verification  
- Page loader, scroll animations, academy-style UI (Oswald + Source Sans 3)

### Admin panel
- Website CMS: services, team, black belts, about, gallery, events, achievements, testimonials  
- Students & belts  
- Belt tests → applications → payments → results → certificates  
- Competition forms, contacts, notifications, audit logs  

### Student portal
- Profile, belt-test applications, payments (sandbox), certificates, competition forms  

---

## Tech stack

| Layer | Stack |
|--------|--------|
| Backend | PHP 8.2+, Laravel 11, MySQL |
| Auth | Laravel Breeze (Blade) |
| Frontend | Blade, Tailwind CSS, Alpine.js, Vite |
| PDF | DomPDF (certificates) |
| Timezone | Asia/Kolkata |

---

## Requirements

- PHP 8.2+ with extensions: `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `gd`  
- Composer  
- Node.js 18+ & npm  
- MySQL 8+ (or MariaDB)  

---

## Local setup (XAMPP)

```bash
# 1. Clone
git clone <your-repo-url> wowzmartialart
cd wowzmartialart

# 2. PHP dependencies
composer install

# 3. Environment
copy .env.example .env
php artisan key:generate

# 4. Edit .env — set database
# DB_DATABASE=wowzmartialart
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Create the MySQL database, then migrate + seed
php artisan migrate --seed

# 6. Frontend assets
npm install
npm run build

# 7. Media folder (images live in public/storage)
# Seeded media is downloaded by WebsiteContentSeeder into public/storage
# If needed, ensure public/storage exists and is writable

# 8. Run
php artisan serve
# → http://127.0.0.1:8000
```

### XAMPP note

If you open the site as `http://localhost/laravel/wowzmartialart/public/`, point the vhost/document root at `public/` when possible.  
A root `index.php` + `.htaccess` is included for hosts that require `index.php` in the project root (e.g. InfinityFree).

### Demo logins

After seeding, use the accounts created by `AdminUserSeeder` / `DemoDataSeeder` (check those seeders for email & password). Change them before any production use.

---

## Useful commands

```bash
php artisan migrate --seed          # fresh schema + demo content
php artisan db:seed --class=WebsiteContentSeeder
npm run dev                         # Vite HMR while developing
npm run build                       # production CSS/JS → public/build
php artisan test                    # PHPUnit
```

---

## Project structure

```
├── app/
│   ├── Http/Controllers/Web/      # Public site
│   ├── Http/Controllers/Admin/    # Admin CMS & operations
│   ├── Http/Controllers/Student/  # Student portal
│   ├── Models/
│   ├── Policies/
│   └── Support/                   # DemoMedia, ProgramDetails, AdminNavigation…
├── config/academy.php             # Branches, phone, email, hours
├── database/migrations/
├── database/seeders/
├── public/                        # Web root (index.php, build, storage)
├── resources/views/
│   ├── web/                       # Public Blade pages
│   ├── admin/
│   └── student/
├── routes/web.php
├── index.php                      # Shared-hosting entry → public/index.php
└── .htaccess                      # Rewrites to public/ on shared hosts
```

---

## Shared hosting / InfinityFree

1. Upload the full project so `index.php` sits in `htdocs/`.  
2. Upload `vendor/` (run `composer install --no-dev` locally first).  
3. Upload `public/build/` and **`public/storage/`** (real image files — not a symlink).  
4. Set `.env` on the server:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.infinityfreeapp.com
   DB_HOST=...
   DB_DATABASE=...
   DB_USERNAME=...
   DB_PASSWORD=...
   ```
5. Import migrations/seeded DB (or run migrate if CLI is available).  

Images are served from `/storage/...` → `public/storage/`.  
Do **not** block the public media path in `.htaccess` (private Laravel dirs `storage/app`, `storage/framework`, `storage/logs` remain forbidden).

---

## Configuration

Academy settings live in `config/academy.php` (overridable via `.env`):

- Name, phone, email, hours  
- Three branch addresses + map queries  
- Certificate / student code prefixes  
- Optional payment callback secret  

---

## License

This project is application code built on [Laravel](https://laravel.com), which is open-sourced under the [MIT license](https://opensource.org/licenses/MIT).  
Demo photos sourced via seeder from public stock (Pexels); replace with your own media for production.
