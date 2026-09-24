# UC Properties Limited — PHP/MySQL redesign

A complete, editable website for XAMPP with PHP 8.2+ and MySQL 8+ or MariaDB 10.4+. HTML is written directly in the PHP pages and shared includes. The design uses locally bundled Bootstrap 5.3.8, custom CSS, and vanilla JavaScript. No Node.js, build step, frontend server, or paid service is needed to run the delivered project.

## Start here on Windows / XAMPP

1. Extract the ZIP. Put the **uc-properties** folder inside `C:\xampp\htdocs\`.
2. Open XAMPP Control Panel. Start **Apache** and **MySQL**. If Apache uses port 8080, keep that port in every local URL below.
3. Open `http://localhost:8080/phpmyadmin/` and choose **Import**. Import `database/schema.sql`, then `database/content.sql`. The schema creates `uc_properties`. Do not re-import into an existing populated database.
4. Copy `config/config.example.php` to **config/config.php**. Edit the database host, port, username, and password for your installation. The example uses local XAMPP's common root/blank-password setup; it must not be used on a public server. MySQL's port is normally 3306, independent of Apache's 8080.
5. Keep `base_url` as `http://localhost:8080/uc-properties`, or change it to match your Apache port and folder name. Do not add a trailing slash. A live installation can use `https://your-domain.com` or `https://your-domain.com/subfolder`.
6. Generate a private random secret in the VS Code terminal:

   ```powershell
   C:\xampp\php\php.exe -r "echo bin2hex(random_bytes(32));"
   ```

   Paste the result into `app_secret` in your private config file. Never share that value.
7. In the terminal, switch to the project directory:

   ```powershell
   cd C:\xampp\htdocs\uc-properties
   C:\xampp\php\php.exe tools\check.php
   ```

8. Create your administrator using **your own email address**:

   ```powershell
   C:\xampp\php\php.exe tools\create-admin.php your-email@example.com
   ```

   Enter a unique password of **12–72 characters** twice. The script hides the password while you type. No administrator credentials are supplied in the ZIP. This command creates a staff account directly; you do not need to register on the public website.
9. Open `http://localhost:8080/uc-properties/`. Staff sign in is `http://localhost:8080/uc-properties/admin/`.

If Apache is on port 80, remove `:8080`. If MySQL uses another port, change only `db.port`. If the database has a different name, update the `CREATE DATABASE` / `USE` statements before import and `db.name` in the config.

## What is included

- Responsive homepage, estate listings and details, building designs and details, about, services, FAQ, updates/articles, contact, inspection, privacy, and terms pages.
- Location, budget, size, type and availability filters; completed-home bedroom filtering; sorting, pagination, and empty states.
- Estate → plot → approved building design → inspection journey, with selection validation on the server.
- Database-backed enquiries and inspections with references, consent time, staff statuses, and private notes.
- Protected administration for locations, estates, plot options and pricing, designs, compatibility approvals, image/document uploads, FAQs, updates, approved testimonials, and shared settings.
- The supplied transparent logo. `.logo-black` and `.logo-white` use CSS filters to preserve the exact artwork; omitting those classes displays the original gold image.
- Company-source imagery, labelled architectural renderings. Empty update and testimonial sections stay hidden on the homepage.
- Keyboard-friendly mobile menu and FAQs, reduced-motion support, input labels, output escaping, and responsive layouts.

## Folder guide

| Path | Purpose |
| --- | --- |
| `index.php`, other root `.php` pages | Visible HTML and page-level database reads |
| `includes/` | Bootstrap, shared visible header/footer/cards, forms, validation, uploads |
| `assets/css/style.css` | Public design and responsive rules |
| `assets/css/admin.css` | Staff dashboard styling |
| `assets/js/main.js` | Menu, gallery, FAQ motion support, reveal and selection behaviour |
| `assets/vendor/` | Local Bootstrap CSS and its licence |
| `assets/images/` | Original logo and company-source images |
| `config/config.example.php` | Example configuration; copy to private `config.php` |
| `admin/` | Protected staff pages and field definitions |
| `database/schema.sql` | Database structure, relationships, indexes and decimal money fields |
| `database/content.sql` | Published company-source content; no customer enquiries |
| `database/demo.sql` | Optional, explicitly labelled demonstration enquiry |
| `uploads/` | Validated user uploads, with Apache non-execution protections |
| `tools/` | CLI administrator setup and installation checks |
| `docs/` | Content source notes, staff guide, testing report and release checklist |

## Edit and publish content

Read `docs/ADMIN-GUIDE.md` for the staff workflow. Create locations before estates, and estates before plot options. Create designs, then approve explicit **Design compatibility** associations. An association must reference the exact estate's plot option; plot size alone is never used to infer suitability.

Seeded company estate names and plot sizes come from the original website. **All prices remain unconfirmed**, because the original did not clearly establish current inclusions/payment rules. Two published design associations are supplied as unapproved staff review records. They are not displayed as compatible until staff approve them. The initial public catalogue has three land estates, sixteen plot options, and two internally consistent proposed designs. Designs with conflicting source bedroom counts are documented but not published.

No approved customer testimonials or project updates were verified, so none are fabricated. No fake sales, guarantees, awards, or customer counts are shown. Review the draft privacy and terms in Company settings before public launch.

## Enquiry notifications

Requests are always saved to MySQL first. Email is **off by default**. To enable it:

1. Configure working mail transport for PHP. On Windows XAMPP this usually involves `php.ini` and a properly configured SMTP relay/sendmail helper; PHP `mail()` does not provide modern SMTP authentication by itself.
2. In private `config.php`, set `mail.enabled` to `true`, and set the approved staff recipient and sender.
3. Send a local test request and verify both the staff dashboard and actual inbox delivery.

Only the request reference and protected dashboard link are included in notifications. A successful PHP `mail()` result means the mail transport accepted the message, **not** that it reached an inbox. The dashboard shows disabled, accepted, or failed notification status. No messages are sent to customers automatically, and inspection requests are not automatically confirmed.

## Uploads

JPG, JPEG, PNG and WebP images: maximum 5 MB and 60 megapixels. PDF brochures/documents: maximum 10 MB. File extension, detected MIME type, and image/PDF signature are checked on the server; random file names are generated. SVG, HTML, PHP, and other scripts are not accepted. PHP's `upload_max_filesize` should be at least `10M` and `post_max_size` at least `12M`; restart Apache after changing `php.ini`.

Use **Images & documents** to add gallery items to one estate or one design. Add the main cover, main floor plan, and main brochure in the estate/design editor. Deleting records does not automatically remove old physical upload files; review those before deleting files on disk.

## Demonstration data

`content.sql` is company-source content, with uncertainty handled explicitly. `demo.sql` is optional and creates a clearly marked fake enquiry with an invalid example email address. Staff can switch the enquiry filter to **Demonstration only** to see it. Dashboard totals exclude demonstration enquiries. Never import the demo file into a live customer database.

## Account recovery and maintenance

To intentionally reset a staff password from the project terminal:

```powershell
C:\xampp\php\php.exe tools\create-admin.php your-email@example.com --reset
```

Automated deployment may use `--password-stdin` with a securely supplied input stream. Never place a password in a command argument or commit it to a script.

Staff sessions expire after 30 minutes of inactivity. Login attempts are limited to eight per connection per 15-minute window; public requests to ten per hour. This basic protection uses the direct connection address, so configure and review it if placing the site behind a shared proxy. Expired rate-limit records can be removed periodically with `tools/cleanup-limits.php`.

Back up the database, upload folder, and private config before changes. Keep private configuration, customer records, and backups out of public folders and source control.

## Before putting this online

This ZIP is an XAMPP/PHP deliverable, not a deployed website. Use PHP hosting with MySQL/MariaDB and HTTPS. Use a dedicated database user with only necessary privileges. Review the brief release checklist in `docs/CONTENT-REVIEW.md`, and verify that Apache honours the included `.htaccess` files. On a non-Apache server, implement equivalent denies for `config`, `includes`, `database`, `docs`, and `tools`, and prevent all script execution in uploads.

See `docs/TESTING.md` for what was actually checked and what remains to verify in your Windows and production environments.
#   u c - p r o p e r t i e s  
 