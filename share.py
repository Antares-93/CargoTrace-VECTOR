import subprocess
import re
import time
import sys
import os

print("=" * 70)
print("       CARGOTRACE - VECTOR GLOBAL SHIPMENT PLATFORM")
print("           SERVER PUBLIK ONLINE (CLOUDFLARE TUNNEL)")
print("=" * 70)
print("\n[!] Pastikan MySQL di XAMPP sudah AKTIF (Start)!\n")

# 1. Jalankan server lokal Laravel di port 8000 jika belum jalan
print("[1/2] Menyiapkan server lokal Laravel (port 8000)...")
php_server = None
try:
    import urllib.request
    urllib.request.urlopen("http://127.0.0.1:8000", timeout=1)
    print("      [OK] Server lokal port 8000 sudah aktif.")
except Exception:
    print("      [i] Menjalankan 'php artisan serve' di latar belakang...")
    php_server = subprocess.Popen(
        ["php", "artisan", "serve", "--port=8000"],
        stdout=subprocess.DEVNULL,
        stderr=subprocess.DEVNULL
    )
    time.sleep(2)

# 2. Jalankan Cloudflare Tunnel
print("[2/2] Membuka terowongan internet global Cloudflare...")
cf_binary = "cloudflared.exe" if os.path.exists("cloudflared.exe") else "cloudflared"

try:
    cf_proc = subprocess.Popen(
        [cf_binary, "tunnel", "--url", "http://127.0.0.1:8000"],
        stdout=subprocess.PIPE,
        stderr=subprocess.STDOUT,
        text=True,
        bufsize=1
    )
except FileNotFoundError:
    print("\n[!] ERROR: File cloudflared.exe tidak ditemukan di folder proyek!")
    sys.exit(1)

print("      Menghubungkan ke server Cloudflare (3-5 detik)...\n")

found_url = None
try:
    for line in cf_proc.stdout:
        match = re.search(r"https://[a-zA-Z0-9-]+\.trycloudflare\.com", line)
        if match and not found_url:
            found_url = match.group(0)
            
            # Copy URL otomatis ke clipboard Windows jika bisa
            try:
                subprocess.run("clip", input=found_url.strip().encode("utf-8"), check=False)
                copied_info = "  (Link sudah otomatis tersalin ke Clipboard! Tinggal CTRL + V)"
            except Exception:
                copied_info = "  (Salin link di atas dan bagikan)"

            print("=" * 70)
            print("  🎉 WEBSITE CARGOTRACE BERHASIL ONLINE DI INTERNET!")
            print("=" * 70)
            print(f"\n  👉 LINK RESMI:  {found_url}\n")
            print("=" * 70)
            print(copied_info)
            print("  ✓ Langsung terbuka mulus tanpa verifikasi IP atau halaman peringatan")
            print("  ✓ Aman untuk dikirimkan ke dosen atau dibuka di smartphone")
            print("  ✓ Tekan CTRL + C di terminal ini untuk berhenti jika sudah selesai")
            print("=" * 70 + "\n")
            sys.stdout.flush()

except KeyboardInterrupt:
    print("\n\n[i] Menghentikan server online...")
finally:
    try:
        cf_proc.terminate()
    except Exception:
        pass
    if php_server:
        try:
            php_server.terminate()
        except Exception:
            pass
    print("[OK] Server online telah dimatikan.")
