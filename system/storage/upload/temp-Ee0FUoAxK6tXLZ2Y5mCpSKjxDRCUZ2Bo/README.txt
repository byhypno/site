EGESER TEKLİF TALEPLERİ MENÜ V1.0.4
=====================================

V1.0.3 controller içindeki $sale dizisine ekleme yapmasına rağmen menü görünmedi.
Bu sürüm controller menü dizisine müdahale etmez.

V1.0.4:
- admin/view/template/common/column_left.tpl içinde <ul id="menu"> satırını bulur.
- Sol menüye doğrudan "Teklif Talepleri" bağlantısı ekler.
- Teklif yönetimi verilerine ve form akışına dokunmaz.

Kurulum:
1. V1.0.2 ve V1.0.3 menü modifikasyonlarını kapatın veya kaldırın.
2. Eklentiler > Eklenti Yükle > bu paketi yükleyin.
3. Eklentiler > Modifikasyonlar > Yenile.
4. Admin panelinde Ctrl+F5.
5. Sol menünün üst kısmında "Teklif Talepleri" görünmelidir.

Not:
Bu paket yalnızca menü görünürlüğünü çözer. Teklif Yönetimi V1.0 modülü kurulu kalmalıdır.
