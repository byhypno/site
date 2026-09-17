EGESER LEAD SECURITY V1.0.1 — OPENCART 2.3
==========================================

Bu sürüm V1.0 logundaki NOT FOUND eşleşmelerine göre sadeleştirilmiştir.

Kurulum / Güncelleme
1. Eklentiler > Eklenti Yükle
2. egeser_lead_security_v1_0_1.ocmod.zip dosyasını yükleyin.
3. Aynı code kullanıldığı için V1.0 kaydını günceller.
4. Eklentiler > Modifikasyonlar > Yenile.
5. Egeser Lead Security Integration satırının Versiyon sütununda 1.0.1 görünmesini kontrol edin.
6. Eklentiler > Eklentiler > Modüller > Egeser Lead Security açık kalsın.
7. Ana sayfada Ctrl+F5.
8. Teklif formunu test edin.

V1.0.1 değişiklikleri
- OCMOD arama noktaları kısaltıldı ve trim=true kullanıldı.
- Eski session CSRF bloğu DB nonce doğrulamasına yönlendirildi.
- Lead kaydından hemen önce nonce tüketilir.
- Response içindeki csrf_token alanı DB nonce ile yenilenir.
- Form şablonuna DB nonce bootstrap JS eklenir.
- Core fiziksel dosyalar değiştirilmez.

Kontrol
Modifikasyon Günlük sekmesinde:
MOD: Egeser Lead Security Integration
altında NOT FOUND - OPERATION SKIPPED bulunmamalı.
