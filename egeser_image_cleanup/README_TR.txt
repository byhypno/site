EGESER GÜVENLİ GÖRSEL TEMİZLEME PAKETİ
======================================

AMAÇ
- 155 doğrulanmış temizlik adayını kontrol eder.
- Sadece yol + boyut + SHA-256 özeti birebir eşleşen dosyaları siler.
- Silmeden önce zorunlu yedek ZIP oluşturur.
- Eksik, değişmiş veya güvensiz dosyaları atlar.

KURULUM
1. Bu ZIP dosyasını cPanel > Dosya Yöneticisi üzerinden public_html içine yükleyin.
2. ZIP'i public_html içinde çıkartın.
3. Aşağıdaki adresi tarayıcıda açın:

   https://www.egeserprefabrik.com.tr/egeser_image_cleanup/?token=2d0c636f59e406a53426bdd7a821a9f448ec2461109db0366358292d53475f2a

4. Ön kontrol ekranında Eşleşen / Eksik / Değişmiş / Güvensiz sayılarını inceleyin.
5. Devam etmek için onay kutusuna şunu yazın:

   EGESER-155-DOSYA

6. "Yedek al ve eşleşen dosyaları sil" butonuna basın.
7. Siteyi, ürün sayfalarını ve blog görsellerini kontrol edin.
8. Her şey doğruysa public_html/egeser_image_cleanup klasörünü silin.

YEDEK VE GERİ YÜKLEME
- Yedek, system/storage/download veya system/storage/upload içine oluşturulur.
- Dosya adı: egeser-gorsel-temizlik-yedegi-TARIH-SAAT.zip
- Sorun olursa ZIP'i public_html içine açarak image klasörünü geri yükleyin.

ÖNEMLİ
- Araç yalnızca 155 aday dosyayı hedefler.
- Veritabanını değiştirmez.
- Klasörleri silmez.
- 329 inceleme dosyasına ve 121 kullanılan görsele dokunmaz.
- Güvenlik anahtarını üçüncü kişilerle paylaşmayın.
