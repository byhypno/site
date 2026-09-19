<?php
class ControllerInformationEgeserCity extends Controller {
    public function index() {
        $cities = $this->getCities();
        $key = isset($this->request->get['city']) ? strtolower(trim((string)$this->request->get['city'])) : '';

        if (!isset($cities[$key])) {
            return $this->notFound();
        }

        $city = $cities[$key];
        $base = rtrim($this->config->get('config_url'), '/');
        $canonical = $base . '/' . $city['slug'];

        $this->document->setTitle($city['meta_title']);
        $this->document->setDescription($city['meta_description']);
        $this->document->addLink($canonical, 'canonical');

        $data['breadcrumbs'] = array(
            array('text' => 'Ana Sayfa', 'href' => $base . '/'),
            array('text' => 'Hizmet Bölgeleri', 'href' => $base . '/#hizmet-bolgeleri'),
            array('text' => $city['name'] . ' Prefabrik Ev', 'href' => $canonical)
        );

        $data['city'] = $city;
        $data['canonical'] = $canonical;
        $data['base'] = $base;
        $data['contact_url'] = $base . '/iletisim';
        $data['models_url'] = $base . '/prefabrik-yapilar';
        $data['telephone'] = trim((string)$this->config->get('config_telephone'));
        if ($data['telephone'] === '') {
            $data['telephone'] = '0531 886 60 90';
        }
        $phone_digits = preg_replace('/\D+/', '', (string)$data['telephone']);
        $data['phone_href'] = 'tel:+' . (substr($phone_digits, 0, 2) === '90' ? $phone_digits : '90' . ltrim($phone_digits, '0'));
        $data['whatsapp_url'] = 'https://wa.me/905318866090?text=' . rawurlencode('Merhaba, ' . $city['name'] . ' bölgesindeki prefabrik yapı projem için bilgi ve teklif almak istiyorum.');

        $data['other_cities'] = array();
        foreach ($cities as $other_key => $other) {
            if ($other_key !== $key) {
                $data['other_cities'][] = array(
                    'name' => $other['name'],
                    'href' => $base . '/' . $other['slug']
                );
            }
        }

        $data['schema'] = $this->schema($city, $canonical, $base);
        $data['breadcrumb_schema'] = $this->breadcrumbSchema($data['breadcrumbs']);

        $this->common($data);
        $this->response->setOutput($this->load->view('information/egeser_city', $data));
    }

    private function getCities() {
        return array(
            'izmir' => array(
                'name' => 'İzmir',
                'slug' => 'izmir-prefabrik-ev',
                'region' => 'MERKEZ HİZMET BÖLGESİ',
                'meta_title' => 'İzmir Prefabrik Ev Modelleri ve Proje Çözümleri | Egeser',
                'meta_description' => 'İzmir prefabrik ev ve kurumsal yapı projeleri için model, teknik kapsam, saha erişimi, sevkiyat ve montaj koşullarını Kemalpaşa merkezli değerlendirin.',
                'headline' => 'İzmir prefabrik ev projelerini sahaya göre planlıyoruz.',
                'lead' => 'Kemalpaşa’daki üretim merkezimizden İzmir’in farklı ilçelerine yönelik bireysel ve kurumsal prefabrik yapı ihtiyaçlarını; kullanım amacı, arazi erişimi ve teknik kapsamla birlikte değerlendiriyoruz.',
                'local_text' => 'İzmir’de kıyı ve iç kesimler arasında rüzgâr, nem, yaz sıcaklığı ve saha koşulları değişebilir. Bu nedenle tek bir standart çözüm yerine, projenin bulunduğu parsel ve kullanım biçimi üzerinden teknik kapsam oluşturulur.',
                'districts' => array('Kemalpaşa', 'Bornova', 'Torbalı', 'Menderes', 'Menemen', 'Seferihisar', 'Urla', 'Bergama'),
                'factors' => array(
                    array('title' => 'Yakın üretim merkezi', 'text' => 'Kemalpaşa merkezli planlama; proje görüşmesi, sevkiyat güzergâhı ve saha koordinasyonunun birlikte ele alınmasını sağlar.'),
                    array('title' => 'Kıyı ve iç kesim farkı', 'text' => 'Nem, rüzgâr ve sıcaklık etkileri ilçeye göre değiştiği için yalıtım ve dış kabuk tercihleri proje özelinde değerlendirilir.'),
                    array('title' => 'Saha erişimi', 'text' => 'Dar yol, eğim, vinç yaklaşımı ve montaj alanı gibi uygulama ayrıntıları teklif öncesinde netleştirilir.')
                ),
                'uses' => array('Tek katlı ve çift katlı prefabrik ev', 'Yazlık veya sürekli kullanım yapısı', 'Ofis, yemekhane ve sosyal tesis', 'Şantiye ve geçici kullanım birimleri'),
                'logistics' => 'Üretim merkezi ile proje sahası arasındaki rota, araç yaklaşımı, indirme alanı ve montaj ekibinin çalışma koşulları birlikte planlanır.',
                'faqs' => array(
                    array('q' => 'İzmir’in tüm ilçelerine hizmet veriyor musunuz?', 'a' => 'Projenin konumu, yol ve saha erişimi değerlendirildikten sonra İzmir genelindeki uygun projeler için sevkiyat ve montaj kapsamı oluşturulur.'),
                    array('q' => 'İzmir prefabrik ev fiyatı nasıl belirlenir?', 'a' => 'm², kat ve oda planı, yalıtım sistemi, iç donanım, sevkiyat mesafesi, saha erişimi ve montaj kapsamı toplam teklifi belirler.'),
                    array('q' => 'Arsa için izin ve ruhsat gerekiyor mu?', 'a' => 'İmar, ruhsat ve yerel uygulamalar parsel ile belediyeye göre değişebilir. Projeye başlamadan önce ilgili belediyeden güncel koşulların doğrulanması gerekir.'),
                    array('q' => 'Kemalpaşa’daki tesisi ziyaret edebilir miyim?', 'a' => 'Showroom ve üretim görüşmeleri için iletişim ekibimizden randevu oluşturabilirsiniz.')
                )
            ),
            'manisa' => array(
                'name' => 'Manisa', 'slug' => 'manisa-prefabrik-ev', 'region' => 'EGE BÖLGESİ',
                'meta_title' => 'Manisa Prefabrik Ev Modelleri ve Yapı Projeleri | Egeser',
                'meta_description' => 'Manisa prefabrik ev, ofis ve kurumsal yapı projelerinde saha, iklim, sevkiyat ve montaj koşullarını proje bazında değerlendirin.',
                'headline' => 'Manisa prefabrik yapı projelerinde erişim ve iklimi birlikte değerlendiriyoruz.',
                'lead' => 'Kemalpaşa üretim merkezine yakınlığıyla Manisa projelerinde saha keşfi, güzergâh ve montaj organizasyonu verimli biçimde planlanabilir. Yapı çözümü yine kullanım amacı ve proje kapsamına göre belirlenir.',
                'local_text' => 'Manisa merkez ile ilçeler arasında mesafe, yaz sıcaklığı, kış koşulları ve arazi yapısı farklılaşır. Özellikle kırsal sahalarda yol genişliği, eğim ve çalışma alanı teklif öncesi kontrol edilmelidir.',
                'districts' => array('Yunusemre', 'Şehzadeler', 'Turgutlu', 'Salihli', 'Akhisar', 'Saruhanlı', 'Gördes', 'Demirci'),
                'factors' => array(
                    array('title' => 'Güzergâh planı', 'text' => 'İlçe ve saha konumuna göre sevkiyat rotası, araç yaklaşımı ve indirme noktası değerlendirilir.'),
                    array('title' => 'Karasal iklim etkisi', 'text' => 'Sıcak yazlar ve ilçeye göre değişen kış koşulları yalıtım ve kullanım senaryosuyla birlikte ele alınır.'),
                    array('title' => 'Kırsal saha koşulları', 'text' => 'Eğim, zemin hazırlığı, enerji-su bağlantıları ve montaj alanı proje başlangıcında netleştirilir.')
                ),
                'uses' => array('Aile yaşamına uygun prefabrik ev', 'Bağ evi ve dönemsel kullanım yapısı', 'Tarımsal işletme ofisi ve sosyal alan', 'Şantiye, yatakhane ve yemekhane'),
                'logistics' => 'Kemalpaşa’dan Manisa’ya sevkiyat; yapının ölçüsü, saha konumu ve uygun araç güzergâhına göre ayrı olarak fiyatlandırılır ve programlanır.',
                'faqs' => array(
                    array('q' => 'Manisa’ya prefabrik ev sevkiyatı yapıyor musunuz?', 'a' => 'Evet. İlçe, yol ve saha erişimi uygunluğu incelenerek sevkiyat ve montaj kapsamı proje bazında planlanır.'),
                    array('q' => 'Manisa için hangi yalıtım tercih edilmeli?', 'a' => 'Kullanım süresi, ilçe, rakım ve saha koşulları bilinmeden tek bir sistem önermek doğru olmaz. Teknik kapsam bu verilerle belirlenir.'),
                    array('q' => 'Fiyat almak için hangi bilgiler gerekli?', 'a' => 'Yaklaşık m², oda ihtiyacı, kullanım amacı, ilçe ve mümkünse arsa fotoğrafları ilk değerlendirme için yeterlidir.'),
                    array('q' => 'Ruhsat işlemlerini kim doğrulamalı?', 'a' => 'Parselin bağlı olduğu belediyeden imar ve ruhsat koşulları güncel olarak doğrulanmalıdır.')
                )
            ),
            'aydin' => array(
                'name' => 'Aydın', 'slug' => 'aydin-prefabrik-ev', 'region' => 'EGE BÖLGESİ',
                'meta_title' => 'Aydın Prefabrik Ev ve Yapı Çözümleri | Egeser Prefabrik',
                'meta_description' => 'Aydın prefabrik ev ve kurumsal yapı projelerinde sıcak iklim, kıyı nemi, saha erişimi, sevkiyat ve montaj koşullarını birlikte değerlendirin.',
                'headline' => 'Aydın prefabrik ev projelerini sıcak iklim ve kullanım biçimine göre ele alıyoruz.',
                'lead' => 'Aydın’ın kıyı ilçeleri ile iç kesimleri farklı saha ve iklim koşulları sunar. Prefabrik yapı kapsamı; sürekli veya dönemsel kullanım, ilçe konumu ve uygulama alanına göre planlanır.',
                'local_text' => 'Yüksek yaz sıcaklığı, kıyı kesimlerde nem ve yoğun sezon trafiği; malzeme seçimi kadar sevkiyat zamanlamasını da etkileyebilir. Montaj alanına araç ve ekip erişimi önceden değerlendirilir.',
                'districts' => array('Efeler', 'Nazilli', 'Söke', 'Kuşadası', 'Didim', 'İncirliova', 'Germencik', 'Çine'),
                'factors' => array(
                    array('title' => 'Yaz konforu', 'text' => 'Sıcak dönemde kullanım yoğunluğu dikkate alınarak yalıtım, doğrama ve havalandırma ihtiyaçları değerlendirilir.'),
                    array('title' => 'Kıyı nemi', 'text' => 'Kuşadası ve Didim gibi kıyı bölgelerinde dış kabuk ve bakım koşulları yerel çevre etkileriyle birlikte ele alınır.'),
                    array('title' => 'Sezon ve erişim', 'text' => 'Yoğun sezon, dar site yolları ve saha çalışma alanı sevkiyat-montaj programına dahil edilir.')
                ),
                'uses' => array('Yazlık prefabrik ev', 'Sürekli yaşam için tek katlı ev', 'Turizm ve işletme destek birimi', 'Ofis, güvenlik ve personel yapıları'),
                'logistics' => 'Aydın ve ilçeleri için mesafe, araç güzergâhı, sezon yoğunluğu ve saha erişimi incelenerek sevkiyat takvimi oluşturulur.',
                'faqs' => array(
                    array('q' => 'Aydın’da yazlık prefabrik ev yapılabilir mi?', 'a' => 'Uygun parsel ve yasal koşullar sağlandığında, dönemsel kullanım ihtiyacına göre model ve teknik kapsam değerlendirilebilir.'),
                    array('q' => 'Kıyı bölgelerinde farklı malzeme gerekir mi?', 'a' => 'Nem, rüzgâr ve kullanım yoğunluğu malzeme ve bakım tercihlerini etkileyebilir; kesin sistem saha ve proje bilgisiyle belirlenir.'),
                    array('q' => 'Aydın sevkiyat ücreti sabit mi?', 'a' => 'Hayır. İlçe, rota, yapı ölçüsü, araç erişimi ve montaj kapsamına göre hesaplanır.'),
                    array('q' => 'Teklif için arsa keşfi zorunlu mu?', 'a' => 'İlk teklif için temel bilgiler ve görseller yeterli olabilir; kesin uygulama planında saha doğrulaması gerekebilir.')
                )
            ),
            'usak' => array(
                'name' => 'Uşak', 'slug' => 'usak-prefabrik-ev', 'region' => 'İÇ EGE',
                'meta_title' => 'Uşak Prefabrik Ev Modelleri ve Proje Çözümleri | Egeser',
                'meta_description' => 'Uşak prefabrik ev ve kurumsal yapı projelerinde karasal iklim, yalıtım, saha erişimi, sevkiyat ve montaj kapsamını değerlendirin.',
                'headline' => 'Uşak prefabrik yapı projelerinde yalıtım ve lojistiği baştan planlıyoruz.',
                'lead' => 'Uşak’ın iç Ege koşullarında kış sıcaklıkları, rüzgâr, rakım ve ilçe mesafeleri proje kararlarını etkiler. Yapı sistemi, kullanım süresi ve saha bilgileri üzerinden değerlendirilir.',
                'local_text' => 'Sürekli yaşam hedeflenen projelerde ısı konforu ve enerji kullanımı; dönemsel yapılarda ise kullanım takvimi öne çıkar. Yol ve montaj erişimi kırsal bölgelerde ayrıca incelenir.',
                'districts' => array('Merkez', 'Banaz', 'Eşme', 'Sivaslı', 'Ulubey', 'Karahallı'),
                'factors' => array(
                    array('title' => 'Isı yalıtımı', 'text' => 'Kış koşulları ve kullanım süresi, duvar-çatı sistemi ile doğrama tercihleri değerlendirilirken dikkate alınır.'),
                    array('title' => 'Rüzgâr ve rakım', 'text' => 'Saha konumu, açık arazi etkisi ve yerel koşullar teknik proje öncesinde paylaşılmalıdır.'),
                    array('title' => 'Uzun mesafe lojistiği', 'text' => 'Sevkiyat güzergâhı, araç yaklaşımı ve montaj programı üretim planıyla birlikte oluşturulur.')
                ),
                'uses' => array('Sürekli yaşam için prefabrik ev', 'Kırsal yaşam ve bağ evi', 'Üretim tesisi destek yapısı', 'Şantiye ofisi, yatakhane ve yemekhane'),
                'logistics' => 'Uşak sevkiyatlarında rota, yapı ölçüsü, saha yaklaşımı ve montaj ekibinin çalışma programı teklif kapsamına açıkça dahil edilir.',
                'faqs' => array(
                    array('q' => 'Uşak ikliminde prefabrik ev kullanılabilir mi?', 'a' => 'Uygun teknik kapsam ve doğru kullanım planıyla değerlendirilebilir. Yalıtım seçimi ilçe, rakım ve kullanım süresine göre yapılmalıdır.'),
                    array('q' => 'Kış şartları fiyatı etkiler mi?', 'a' => 'Yalıtım, doğrama ve çatı kapsamındaki tercihler toplam maliyeti etkileyebilir.'),
                    array('q' => 'Uşak’a montaj ekibi geliyor mu?', 'a' => 'Saha ve proje uygunluğu onaylandığında sevkiyat ve montaj organizasyonu teklif kapsamında planlanır.'),
                    array('q' => 'Arsa hazırlığını kim yapar?', 'a' => 'Zemin ve altyapı sorumlulukları proje sözleşmesinde netleştirilir; saha koşulları teklif öncesinde paylaşılmalıdır.')
                )
            ),
            'balikesir' => array(
                'name' => 'Balıkesir', 'slug' => 'balikesir-prefabrik-ev', 'region' => 'EGE / MARMARA GEÇİŞİ',
                'meta_title' => 'Balıkesir Prefabrik Ev ve Yapı Projeleri | Egeser Prefabrik',
                'meta_description' => 'Balıkesir prefabrik ev projelerinde kıyı ve iç kesim farklarını, uzun mesafe lojistiğini, saha erişimini ve montaj kapsamını değerlendirin.',
                'headline' => 'Balıkesir prefabrik ev projelerini bölgenin değişken koşullarına göre planlıyoruz.',
                'lead' => 'Geniş yüzölçümü ve kıyıdan iç kesimlere değişen iklimi nedeniyle Balıkesir projelerinde konum bilgisi kritik önemdedir. Model ve teknik kapsam, ilçe ile kullanım senaryosuna göre ele alınır.',
                'local_text' => 'Edremit Körfezi ve Ayvalık çevresindeki nem-rüzgâr etkileri ile iç ilçelerin karasal koşulları aynı değildir. Uzun mesafe, rota ve çalışma alanı da teslim kapsamını doğrudan etkiler.',
                'districts' => array('Karesi', 'Altıeylül', 'Edremit', 'Ayvalık', 'Burhaniye', 'Bandırma', 'Gönen', 'Susurluk'),
                'factors' => array(
                    array('title' => 'Kıyı–iç kesim farkı', 'text' => 'Nem, rüzgâr, sıcaklık ve kullanım yoğunluğu ilçe bazında değerlendirilir.'),
                    array('title' => 'Mesafe ve rota', 'text' => 'Balıkesir’in geniş coğrafyasında ilçe mesafesi, ana yol bağlantısı ve saha yaklaşımı lojistik planını belirler.'),
                    array('title' => 'Dönemsel kullanım', 'text' => 'Yazlık veya sürekli yaşam seçimi, yalıtım ve iç donanım kararlarının temel girdilerinden biridir.')
                ),
                'uses' => array('Kıyı bölgesinde yazlık prefabrik ev', 'İç kesimde sürekli yaşam evi', 'Tarım ve sanayi işletme yapıları', 'Ofis, yatakhane ve sosyal tesis'),
                'logistics' => 'Balıkesir projelerinde yalnız il adı değil, kesin ilçe ve saha konumu üzerinden mesafe, güzergâh, araç erişimi ve montaj süresi hesaplanır.',
                'faqs' => array(
                    array('q' => 'Balıkesir’in hangi ilçelerine hizmet veriyorsunuz?', 'a' => 'İlçe ve saha erişimi uygunluğuna göre Balıkesir genelindeki bireysel ve kurumsal projeler değerlendirilir.'),
                    array('q' => 'Ayvalık ve Edremit için özel değerlendirme gerekir mi?', 'a' => 'Kıyı nemi, rüzgâr, sezon yoğunluğu ve saha erişimi proje planında ayrıca dikkate alınır.'),
                    array('q' => 'Balıkesir sevkiyatı neye göre fiyatlanır?', 'a' => 'İlçe mesafesi, yapı ölçüsü, araç ve vinç erişimi ile montaj kapsamına göre fiyatlanır.'),
                    array('q' => 'Modeli görmeden teklif alabilir miyim?', 'a' => 'Yaklaşık m², oda planı ve saha bilgileriyle ön değerlendirme yapılabilir; model sayfaları seçim için yol gösterir.')
                )
            ),
            'mugla' => array(
                'name' => 'Muğla', 'slug' => 'mugla-prefabrik-ev', 'region' => 'GÜNEY EGE',
                'meta_title' => 'Muğla Prefabrik Ev ve Yapı Çözümleri | Egeser Prefabrik',
                'meta_description' => 'Muğla prefabrik ev projelerinde kıyı iklimi, eğimli ve dar saha erişimi, sevkiyat rotası ve montaj koşullarını birlikte değerlendirin.',
                'headline' => 'Muğla prefabrik ev projelerinde saha erişimini projenin merkezine alıyoruz.',
                'lead' => 'Muğla’da kıyı iklimi, eğimli araziler, dar yollar ve sezon yoğunluğu proje organizasyonunu etkileyebilir. Yapı kapsamı; kullanım amacı, ilçe ve gerçek saha koşullarına göre belirlenir.',
                'local_text' => 'Bodrum, Fethiye ve Marmaris gibi ilçelerde parsel erişimi ile araç çalışma alanı özellikle önemlidir. Menteşe ve iç kesimlerde ise rakım ve kış koşulları ayrıca değerlendirilmelidir.',
                'districts' => array('Menteşe', 'Bodrum', 'Milas', 'Fethiye', 'Marmaris', 'Dalaman', 'Ortaca', 'Ula'),
                'factors' => array(
                    array('title' => 'Dar ve eğimli erişim', 'text' => 'Yol genişliği, dönüş noktaları, eğim, vinç konumu ve indirme alanı saha görselleriyle önceden kontrol edilir.'),
                    array('title' => 'Kıyı koşulları', 'text' => 'Nem, rüzgâr ve yoğun güneş etkisi dış kabuk, doğrama ve bakım beklentileriyle birlikte değerlendirilir.'),
                    array('title' => 'Sezon planlaması', 'text' => 'Trafik ve çalışma kısıtları sevkiyat-montaj takvimini etkileyebileceği için proje programı erken netleştirilir.')
                ),
                'uses' => array('Yazlık veya sürekli yaşam evi', 'Turizm işletmesi destek yapısı', 'Bahçe, çiftlik ve kırsal yaşam yapısı', 'Ofis, personel ve sosyal alan'),
                'logistics' => 'Muğla projelerinde mesafeye ek olarak yol geometrisi, site veya parsel girişi, indirme alanı ve sezon takvimi teklif öncesinde değerlendirilir.',
                'faqs' => array(
                    array('q' => 'Muğla’nın kıyı ilçelerine sevkiyat yapıyor musunuz?', 'a' => 'Saha ve yol erişimi uygun olduğunda kıyı ilçelerindeki projeler için sevkiyat ve montaj planı hazırlanabilir.'),
                    array('q' => 'Dar yol varsa proje yapılamaz mı?', 'a' => 'Kesin karar ölçü ve saha incelemesiyle verilir. Yol genişliği, dönüşler, araç ve ekipman yaklaşımı önceden kontrol edilmelidir.'),
                    array('q' => 'Muğla’da nem için ne yapılır?', 'a' => 'Kıyı koşulları; dış kaplama, doğrama, havalandırma ve bakım beklentileriyle birlikte teknik kapsamda ele alınır.'),
                    array('q' => 'Yazlık kullanım ile sürekli kullanım arasında fark var mı?', 'a' => 'Kullanım süresi; yalıtım, iç donanım, iklimlendirme ve bütçe önceliklerini değiştirir.')
                )
            )
        );
    }

    private function schema($city, $canonical, $base) {
        $faq = array();
        foreach ($city['faqs'] as $item) {
            $faq[] = array(
                '@type' => 'Question',
                'name' => $item['q'],
                'acceptedAnswer' => array('@type' => 'Answer', 'text' => $item['a'])
            );
        }

        return array(
            '@context' => 'https://schema.org',
            '@graph' => array(
                array(
                    '@type' => 'Service',
                    '@id' => $canonical . '#service',
                    'name' => $city['name'] . ' Prefabrik Ev ve Yapı Çözümleri',
                    'url' => $canonical,
                    'description' => $city['meta_description'],
                    'serviceType' => 'Prefabrik ev ve prefabrik yapı projelendirme, üretim, sevkiyat ve montaj hizmeti',
                    'provider' => array('@type' => 'Organization', 'name' => 'Egeser Prefabrik', 'url' => $base . '/'),
                    'areaServed' => array('@type' => 'City', 'name' => $city['name'])
                ),
                array('@type' => 'FAQPage', 'mainEntity' => $faq)
            )
        );
    }

    private function breadcrumbSchema($breadcrumbs) {
        $items = array();
        foreach ($breadcrumbs as $position => $item) {
            $items[] = array(
                '@type' => 'ListItem',
                'position' => $position + 1,
                'name' => trim(strip_tags($item['text'])),
                'item' => html_entity_decode($item['href'], ENT_QUOTES, 'UTF-8')
            );
        }
        return array('@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $items);
    }

    private function notFound() {
        $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');
        $this->document->setTitle('Sayfa Bulunamadı');
        $data['breadcrumbs'] = array(
            array('text' => 'Ana Sayfa', 'href' => rtrim($this->config->get('config_url'), '/') . '/')
        );
        $data['heading_title'] = 'Sayfa Bulunamadı';
        $data['text_error'] = 'Aradığınız hizmet bölgesi sayfası bulunamadı.';
        $data['button_continue'] = 'Ana Sayfaya Dön';
        $data['continue'] = rtrim($this->config->get('config_url'), '/') . '/';
        $this->common($data);
        $this->response->setOutput($this->load->view('error/not_found', $data));
    }

    private function common(&$data) {
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
    }
}
