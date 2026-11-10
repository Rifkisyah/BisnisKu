@echo off
echo ===================================================
echo     BisnisKu - InfinityFree Deployment Builder
echo ===================================================
echo.

set BUILD_DIR=build_infinityfree
set CORE_DIR=%BUILD_DIR%\core
set HTDOCS_DIR=%BUILD_DIR%\htdocs

echo [1/6] Menyiapkan direktori build...
if exist "%BUILD_DIR%" rmdir /S /Q "%BUILD_DIR%"
mkdir "%BUILD_DIR%"
mkdir "%CORE_DIR%"
mkdir "%HTDOCS_DIR%"

echo [2/6] Mengkopi file aplikasi Laravel...
robocopy . "%CORE_DIR%" /E /XD .git .vscode node_modules build_infinityfree .phpunit.cache tests UML /XF exclude_build.txt build_for_infinityfree.bat .editorconfig .gitattributes .gitignore phpunit.xml phpunit.dusk.xml DESIGN.md /NFL /NDL /NJH /NJS

echo [3/6] Memindahkan isi public ke htdocs...
robocopy "%CORE_DIR%\public" "%HTDOCS_DIR%" /E /MOVE /NFL /NDL /NJH /NJS

echo [4/6] Menyesuaikan path di index.php...
powershell -Command "(Get-Content %HTDOCS_DIR%\index.php) -replace '__DIR__\s*\.\s*''/\.\./', '__DIR__.''/core/' | Set-Content %HTDOCS_DIR%\index.php"

echo [5/6] Mengamankan folder core dari akses publik...
echo Deny from all > "%CORE_DIR%\.htaccess"
echo Require all denied >> "%CORE_DIR%\.htaccess"

echo [6/6] Membuat file zip...
pushd "%CORE_DIR%"
tar -a -c -f "..\core.zip" * .env .htaccess
popd
pushd "%HTDOCS_DIR%"
tar -a -c -f "..\htdocs.zip" * .htaccess
popd

echo Selesai!
echo.
echo ===================================================
echo INSTRUKSI UPLOAD KE INFINITYFREE:
echo Karena batasan keamanan dari InfinityFree (Extract path must be within htdocs),
echo semua file HARUS berada di dalam htdocs. Kami telah menambahkan
echo sistem proteksi otomatis (.htaccess) agar folder core Anda tetap aman.
echo.
echo 1. Buka File Manager InfinityFree dan MASUK ke folder "htdocs".
echo 2. Hapus semua file bawaan (seperti index2.html) di dalam htdocs.
echo 3. Upload "core.zip" dan "htdocs.zip" ke DALAM folder "htdocs".
echo 4. Extract "core.zip" (otomatis akan membuat folder bernama "core" di dalam htdocs).
echo 5. Extract "htdocs.zip" (semua filenya akan langsung berada di htdocs).
echo 6. Hapus file zip setelah selesai.
echo ===================================================
