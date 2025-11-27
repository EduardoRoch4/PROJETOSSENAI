# TechFit / SiteAcademia — Run instructions

If you're seeing "404 Not Found" at http://localhost:8080/ it's because the webserver's document root points to the repository root instead of the `SiteAcademia/` folder which contains the site's pages.

Quick fixes:

- Start PHP built-in server with the repo root as document root (this project contains an `index.php` redirect that opens `SiteAcademia/index.html`):

  ```powershell
  cd 'C:\Users\06141901182\Documents\PROJETOSSENAI'
  php -S localhost:8080 -t .
  ```

- Or configure your Apache/XAMPP/Nginx docroot to point to `.../PROJETOSSENAI/SiteAcademia` instead of the repository root.

After running the built-in server, open http://localhost:8080/ — it should redirect you to /SiteAcademia/index.html and load the project home.
