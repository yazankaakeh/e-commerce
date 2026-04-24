<?php

namespace Modules\CMS\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Modules\CMS\Models\Portfolio;

/**
 * Seeds the Codliy public portfolio.
 *
 * The three "featured" entries reflect real delivered products from the
 * studio's history — Doctor System (medical SaaS), Tagiy (NFC smart cards,
 * the studio's own pre-rebrand product) and the Turkish Marketer platform.
 * The remaining entries round out the grid so visitors don't land on a
 * three-tile portfolio that looks half-done.
 *
 * Placeholder hero images are reused from `public/assets/blog/` — swap
 * for dedicated shots in `public/assets/portfolio/` when you have them and
 * update the `cover` keys below.
 */
class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        // Covers live under `public/assets/blog/` for now. If/when a
        // dedicated `public/assets/portfolio/` folder lands, change this
        // one constant — the per-entry `cover` filenames stay the same.
        $coverDir = public_path('assets/blog');

        $entries = $this->portfolioEntries();
        $mediaAttached = 0;

        foreach ($entries as $index => $entry) {
            $coverFile = $entry['cover'] ?? null;
            unset($entry['cover']);

            // `order` lets the admin reorder manually later; seed it in
            // the array order so the featured ones land at the top.
            $entry['order'] = $index + 1;

            // The `show` view renders these list-shaped translatable fields
            // by splitting on newlines (see portfolio/show.blade.php around
            // `explode("\n", $portfolio->getTranslation('features', ...))`).
            // Source data in this seeder is written as string[] per locale
            // for readability — flatten to a single newline-joined string
            // per locale before persisting.
            foreach (['features', 'challenges', 'solutions', 'results'] as $listField) {
                // `isset()` already rejects null values, so one guard is enough —
                // PHPStan flagged the additional `=== null` check as dead code.
                if (! isset($entry[$listField])) {
                    continue;
                }

                $entry[$listField] = $this->flattenListTranslations($entry[$listField]);
            }

            $portfolio = Portfolio::create($entry);

            if (! $coverFile) {
                continue;
            }

            $fullPath = $coverDir . DIRECTORY_SEPARATOR . $coverFile;

            if (! File::exists($fullPath)) {
                $this->command->warn(
                    "Portfolio cover missing on disk: {$fullPath} " .
                    "(entry: {$entry['title']['en']})"
                );

                continue;
            }

            try {
                $portfolio->addMedia($fullPath)
                    ->preservingOriginal()
                    ->toMediaCollection('featured_image');

                $mediaAttached++;
            } catch (\Throwable $e) {
                $this->command->error(
                    "Could not attach featured_image for '{$entry['title']['en']}': " .
                    $e->getMessage()
                );
            }
        }

        $this->command->info(
            'Codliy portfolio seeded: ' . count($entries) . ' entries, ' .
            $mediaAttached . ' featured images attached.'
        );

        if ($mediaAttached < count($entries)) {
            $this->command->warn(
                'Some portfolio images did not attach. Check: ' .
                '(1) files exist under public/assets/blog/, ' .
                '(2) the "media" table exists (Spatie migration), ' .
                '(3) storage/app/public is writable, ' .
                '(4) `php artisan storage:link` was run so uploads are web-reachable.'
            );
        }
    }

    /**
     * Flatten a list-shaped translatable value into the string-per-locale
     * shape the `portfolio/show.blade.php` view expects.
     *
     * Input  — convenient for humans writing the seeder:
     *   [
     *     'en' => ['One', 'Two', 'Three'],
     *     'ar' => ['واحد', 'اثنان', 'ثلاثة'],
     *   ]
     *
     * Output — what the view and CMS form expect:
     *   [
     *     'en' => "One\nTwo\nThree",
     *     'ar' => "واحد\nاثنان\nثلاثة",
     *   ]
     *
     * Pass-through if the value is already a string-per-locale array, so
     * the function stays safe to call on unchanged data.
     *
     * @param  array<string, mixed>  $translations
     * @return array<string, string>
     */
    private function flattenListTranslations(array $translations): array
    {
        $out = [];

        foreach ($translations as $locale => $value) {
            if (is_array($value)) {
                $out[$locale] = implode("\n", array_map('strval', $value));
            } else {
                $out[$locale] = (string) $value;
            }
        }

        return $out;
    }

    /**
     * Source of truth for the portfolio content. Keeping it in a method
     * rather than inline makes `run()` readable as pure orchestration
     * and keeps this class testable later if we add a factory-backed test.
     *
     * @return array<int, array<string, mixed>>
     */
    private function portfolioEntries(): array
    {
        return [
            // ==========================================================
            // 1. Doctor System — medical practice SaaS
            // ==========================================================
            [
                'cover' => 'systems.png',
                'title' => [
                    'en' => 'Doctor System — multi-clinic practice platform',
                    'ar' => 'نظام الأطباء — منصة إدارة العيادات متعددة الفروع',
                    'tr' => 'Doctor System — çoklu klinik yönetim platformu',
                ],
                'short_description' => [
                    'en' => 'A bilingual SaaS that runs the full clinical workflow — patients, appointments, examinations, billing — for a network of specialist clinics.',
                    'ar' => 'منصة SaaS ثنائية اللغة تدير المسار السريري كاملاً — المرضى، المواعيد، الفحوصات، الفواتير — لشبكة من العيادات المتخصصة.',
                    'tr' => 'Uzman klinik ağı için tüm klinik akışı — hastalar, randevular, muayeneler, faturalama — çalıştıran iki dilli SaaS.',
                ],
                'description' => [
                    'en' => '<p class="lead">A production-grade practice management platform replacing a pile of spreadsheets, WhatsApp threads and paper forms with one system the front desk, the clinician and the billing team all trust.</p><h2>What we shipped</h2><ul><li>Patient records with timeline view, attachments and medical history.</li><li>Appointment scheduling with clinic/doctor filters, conflict detection and SMS reminders.</li><li>Structured examinations with templates per specialty (cardiology, dermatology, paediatrics).</li><li>Billing and insurance claims, exportable to the existing accounting software.</li><li>Full Arabic + English UI with RTL-aware layouts.</li></ul><h2>Why it matters</h2><p>The front desk went from 12 minutes per patient intake to under 3. The clinical team stopped keeping private spreadsheets on top of the system because the system finally fit their workflow.</p>',
                    'ar' => '<p class="lead">منصة إدارة عيادات إنتاجية تحل محل كم من جداول البيانات ورسائل واتساب والاستمارات الورقية بنظام واحد يثق به الاستقبال والطبيب وقسم المحاسبة.</p><h2>ما قمنا بتسليمه</h2><ul><li>ملفات المرضى مع عرض زمني ومرفقات وتاريخ طبي كامل.</li><li>جدولة المواعيد مع مرشحات عيادة/طبيب وكشف التعارضات وتذكيرات SMS.</li><li>فحوصات منظمة بقوالب لكل تخصص (قلب، جلدية، أطفال).</li><li>الفوترة والمطالبات التأمينية قابلة للتصدير لبرنامج المحاسبة.</li><li>واجهة بالعربية والإنجليزية بالكامل بتخطيط RTL سليم.</li></ul><h2>لماذا يهم</h2><p>انخفض زمن استقبال المريض من 12 دقيقة إلى أقل من 3. توقّف الفريق السريري عن الاحتفاظ بجداول بياناته الخاصة لأن النظام أخيرًا يناسب سير العمل.</p>',
                    'tr' => '<p class="lead">Resepsiyonun, hekimin ve faturalama ekibinin hepsinin güvendiği tek bir sistemle; elektronik tabloları, WhatsApp gruplarını ve kağıt formları yerinden eden üretim düzeyinde bir klinik yönetim platformu.</p><h2>Teslim ettiklerimiz</h2><ul><li>Zaman çizelgesi görünümü, ek dosyalar ve tıbbi geçmişle hasta kayıtları.</li><li>Klinik/doktor filtreleri, çakışma algılama ve SMS hatırlatmaları ile randevu planlama.</li><li>Uzmanlık başına şablonlu yapılandırılmış muayeneler (kardiyoloji, dermatoloji, pediatri).</li><li>Mevcut muhasebe yazılımına aktarılabilir faturalama ve sigorta talepleri.</li><li>RTL duyarlı tasarımlarla tam Arapça + İngilizce arayüz.</li></ul><h2>Neden önemli</h2><p>Resepsiyondaki hasta kaydı 12 dakikadan 3 dakikanın altına indi. Klinik ekibi sistem iş akışlarına nihayet oturdu ve kendi elektronik tablolarını tutmayı bıraktı.</p>',
                ],
                'category' => [
                    'en' => 'Healthcare SaaS',
                    'ar' => 'برمجيات الرعاية الصحية',
                    'tr' => 'Sağlık SaaS',
                ],
                'client_name' => 'Dr. Ahmed K.',
                'client_company' => 'Regional Specialist Clinics',
                'project_url' => null,
                'technologies' => ['Laravel', 'Vue 3', 'MySQL', 'Redis', 'Livewire', 'Tailwind CSS'],
                'start_date' => '2024-01-15',
                'completion_date' => '2024-07-10',
                'project_duration' => '6 months',
                'features' => [
                    'en' => [
                        'Multi-clinic, multi-doctor scheduling',
                        'Structured examinations with specialty templates',
                        'Patient timeline with attachments and history',
                        'Billing, insurance claims and accounting export',
                        'Arabic + English UI with full RTL support',
                    ],
                    'ar' => [
                        'جدولة لعدة عيادات وأطباء',
                        'فحوصات منظمة بقوالب تخصصية',
                        'عرض زمني لملف المريض مع المرفقات والتاريخ',
                        'فوترة ومطالبات تأمينية وتصدير محاسبي',
                        'واجهة عربية وإنجليزية بدعم RTL كامل',
                    ],
                    'tr' => [
                        'Çoklu klinik, çoklu doktor planlama',
                        'Uzmanlık şablonlarıyla yapılandırılmış muayeneler',
                        'Ek dosya ve geçmiş içeren hasta zaman çizelgesi',
                        'Faturalama, sigorta talepleri ve muhasebe dışa aktarımı',
                        'Tam RTL destekli Arapça + İngilizce arayüz',
                    ],
                ],
                'challenges' => [
                    'en' => [
                        'Legacy data spread across 4 spreadsheets and a retired PHP app.',
                        'Clinics on unreliable ADSL — offline-friendly fallbacks required.',
                        'Billing rules that differed per insurer and per clinic.',
                    ],
                    'ar' => [
                        'بيانات قديمة موزعة على 4 جداول وتطبيق PHP متقاعد.',
                        'عيادات على ADSL غير موثوق — احتياج لاحتياطات تعمل بدون اتصال.',
                        'قواعد فوترة تختلف لكل شركة تأمين وكل عيادة.',
                    ],
                    'tr' => [
                        '4 elektronik tablo ve emekli bir PHP uygulamasına yayılmış eski veriler.',
                        'Güvenilmez ADSL üzerinde klinikler — çevrimdışı dostu yedekler gerekli.',
                        'Sigortacıya ve kliniğe göre değişen faturalama kuralları.',
                    ],
                ],
                'solutions' => [
                    'en' => [
                        'A one-shot import pipeline with a dry-run mode and per-row error report.',
                        'Service Worker + IndexedDB for offline-first intake; syncs when online.',
                        'Billing rules lifted into a small DSL the office manager can edit.',
                    ],
                    'ar' => [
                        'خط استيراد لمرة واحدة بوضع تجريبي وتقرير أخطاء لكل صف.',
                        'Service Worker + IndexedDB لاستقبال يعمل بدون إنترنت ثم يتزامن.',
                        'قواعد الفوترة في DSL صغير يمكن لمسؤول المكتب تحريره.',
                    ],
                    'tr' => [
                        'Kuru çalıştırma modu ve satır başına hata raporu ile tek seferlik içe aktarma.',
                        'Çevrimdışı öncelikli kayıt için Service Worker + IndexedDB; bağlanınca senkronize.',
                        'Ofis yöneticisinin düzenleyebileceği küçük bir DSL\'e taşınmış faturalama kuralları.',
                    ],
                ],
                'results' => [
                    'en' => [
                        'Patient intake time cut from 12 minutes to under 3.',
                        'Zero unpaid claims lost to data-entry errors in Q3.',
                        '98% clinical staff adoption within two weeks.',
                    ],
                    'ar' => [
                        'انخفاض زمن استقبال المريض من 12 دقيقة إلى أقل من 3.',
                        'لا مطالبات غير مدفوعة ضاعت بأخطاء إدخال في الربع الثالث.',
                        'اعتماد 98% من الطاقم السريري خلال أسبوعين.',
                    ],
                    'tr' => [
                        'Hasta kayıt süresi 12 dakikadan 3 dakikanın altına.',
                        'Q3\'te veri girişi hatalarına kaybedilen ödenmemiş talep yok.',
                        'İki hafta içinde %98 klinik personeli adaptasyonu.',
                    ],
                ],
                'testimonial' => [
                    'en' => 'Codliy understood our workflow in a week — something the last team never did in a year. The platform feels built by people who have actually worked a clinic front desk.',
                    'ar' => 'فهم Codliy سير عملنا في أسبوع — شيء لم يفعله الفريق السابق في عام. المنصة تشعر أنها مبنية من أشخاص عملوا فعلاً في استقبال عيادة.',
                    'tr' => 'Codliy iş akışımızı bir haftada anladı — önceki ekibin bir yılda anlayamadığı şeyi. Platform gerçekten klinik resepsiyonunda çalışmış kişilerce yapılmış hissi veriyor.',
                ],
                'testimonial_author' => 'Dr. Ahmed K.',
                'testimonial_position' => 'Medical Director, Regional Specialist Clinics',
                'is_featured' => true,
                'is_active' => true,
            ],

            // ==========================================================
            // 2. Tagiy — NFC smart cards SaaS (the studio's own product,
            //    pre-rebrand to Codliy)
            // ==========================================================
            [
                'cover' => 'design.png',
                'title' => [
                    'en' => 'Tagiy — NFC smart business cards',
                    'ar' => 'Tagiy — بطاقات أعمال ذكية بتقنية NFC',
                    'tr' => 'Tagiy — NFC akıllı kartvizitler',
                ],
                'short_description' => [
                    'en' => 'The studio\'s own product before the Codliy rebrand: a SaaS that turns a physical NFC card into a living profile — tap to share contact, social, portfolio and payment links.',
                    'ar' => 'منتج الاستوديو الخاص قبل إعادة العلامة التجارية إلى Codliy: منصة تحول بطاقة NFC فعلية إلى ملف حي — لمسة لمشاركة جهات الاتصال والشبكات والأعمال والمدفوعات.',
                    'tr' => 'Codliy yeniden markalaşmadan önce stüdyonun kendi ürünü: fiziksel NFC kartını canlı bir profile dönüştüren — iletişim, sosyal, portföy ve ödeme bağlantılarını paylaşmak için dokun — bir SaaS.',
                ],
                'description' => [
                    'en' => '<p class="lead">Tagiy was a full SaaS product we built and ran end-to-end: hardware procurement, card programming, customer SaaS, and a payments layer. It ran for two years before we folded its technology into the current Codliy stack.</p><h2>The product</h2><p>Each customer got a set of NFC cards linked to a profile they fully controlled. Tapping the card opened the profile on the recipient\'s phone — no app install, no login. Profiles supported contact vCards, social links, a portfolio gallery, and a "pay me" button wired to local gateways.</p><h2>The interesting engineering</h2><ul><li>Card serialization tool that signed each card to a profile — replacements rotate the signature so lost cards can be revoked.</li><li>Per-card analytics (taps, regions, hours) surfaced in a small Vue dashboard.</li><li>Theme system with live preview — the core of what is now Codliy\'s Theme Settings.</li></ul><p>Much of what now ships as Codliy\'s theme engine, admin scaffolding and modular package structure was proven here first.</p>',
                    'ar' => '<p class="lead">كان Tagiy منتج SaaS كاملاً بنيناه وأدرناه من طرف إلى طرف: تجهيز الأجهزة، برمجة البطاقات، SaaS للعملاء، وطبقة مدفوعات. عمل لعامين قبل أن ندمج تقنيته في مكدس Codliy الحالي.</p><h2>المنتج</h2><p>كل عميل يحصل على مجموعة بطاقات NFC مرتبطة بملف شخصي يتحكم به بالكامل. لمسة البطاقة تفتح الملف على هاتف المستلم — دون تثبيت تطبيق أو تسجيل دخول. دعمت الملفات vCards وروابط الشبكات ومعرض أعمال وزر "ادفع لي" موصول ببوابات محلية.</p><h2>الهندسة المثيرة للاهتمام</h2><ul><li>أداة تسلسل بطاقات توقّع كل بطاقة لملف — البدائل تدور التوقيع لإلغاء البطاقات المفقودة.</li><li>تحليلات لكل بطاقة (لمسات، مناطق، ساعات) تظهر في لوحة Vue صغيرة.</li><li>نظام ثيم بمعاينة حية — نواة ما أصبح Theme Settings في Codliy.</li></ul>',
                    'tr' => '<p class="lead">Tagiy, uçtan uca yaptığımız ve işlettiğimiz tam bir SaaS ürünüydü: donanım tedariği, kart programlama, müşteri SaaS\'i ve ödeme katmanı. Teknolojisini mevcut Codliy yığınına katmadan önce iki yıl çalıştı.</p><h2>Ürün</h2><p>Her müşteri, tamamen kontrol ettiği bir profile bağlı bir dizi NFC kart aldı. Karta dokunmak profili alıcının telefonunda açtı — uygulama kurulumu veya giriş yok. Profiller vCard, sosyal bağlantılar, portföy galerisi ve yerel ağ geçitlerine bağlı bir "bana öde" düğmesini destekledi.</p><h2>İlginç mühendislik</h2><ul><li>Her kartı bir profile imzalayan kart serileştirme aracı — yedekler imzayı döndürür, böylece kayıp kartlar iptal edilebilir.</li><li>Küçük bir Vue panosunda yüzeye çıkan kart başına analizler (dokunuşlar, bölgeler, saatler).</li><li>Canlı önizlemeli tema sistemi — şu an Codliy Tema Ayarları olan şeyin çekirdeği.</li></ul>',
                ],
                'category' => [
                    'en' => 'Product (SaaS)',
                    'ar' => 'منتج (SaaS)',
                    'tr' => 'Ürün (SaaS)',
                ],
                'client_name' => 'Codliy Studio (in-house)',
                'client_company' => 'Codliy',
                'project_url' => null,
                'technologies' => ['Laravel', 'Vue 3', 'Tailwind CSS', 'MySQL', 'Stripe', 'Iyzico', 'NFC'],
                'start_date' => '2022-09-01',
                'completion_date' => '2024-09-30',
                'project_duration' => '2 years (in-house)',
                'features' => [
                    'en' => [
                        'Programmable NFC card linked to a live profile',
                        'Per-card analytics dashboard (taps, regions, hours)',
                        'Profile themes with live preview',
                        'Payments via Stripe + Iyzico (local + international)',
                        'Card revocation and rotation for lost cards',
                    ],
                    'ar' => [
                        'بطاقة NFC قابلة للبرمجة ومرتبطة بملف حي',
                        'تحليلات لكل بطاقة (لمسات، مناطق، ساعات)',
                        'ثيمات ملف مع معاينة حية',
                        'مدفوعات عبر Stripe و Iyzico (محلي ودولي)',
                        'إلغاء البطاقات المفقودة وتدوير التوقيع',
                    ],
                    'tr' => [
                        'Canlı profile bağlı programlanabilir NFC kart',
                        'Kart başına analiz panosu (dokunuşlar, bölgeler, saatler)',
                        'Canlı önizlemeli profil temaları',
                        'Stripe + Iyzico ile ödemeler (yerel + uluslararası)',
                        'Kayıp kartlar için iptal ve imza döndürme',
                    ],
                ],
                'challenges' => [
                    'en' => [
                        'Unknown-to-us hardware vendor quirks on the NFC chips.',
                        'Two payment gateways with very different webhook shapes.',
                        'Every customer wanted a different theme by day two.',
                    ],
                    'ar' => [
                        'خصوصيات مورد أجهزة غير معلومة على شرائح NFC.',
                        'بوابتا دفع بأشكال webhooks مختلفة تمامًا.',
                        'كل عميل أراد ثيمًا مختلفًا في اليوم الثاني.',
                    ],
                    'tr' => [
                        'NFC çiplerinde bize bilinmeyen donanım satıcısı tuhaflıkları.',
                        'Çok farklı webhook şekillerine sahip iki ödeme ağ geçidi.',
                        'Her müşteri ikinci güne gelmeden farklı bir tema istedi.',
                    ],
                ],
                'solutions' => [
                    'en' => [
                        'Serialization tool that normalized vendor quirks behind a clean API.',
                        'A gateway adapter layer — one interface, per-gateway drivers.',
                        'Theme engine with CSS variables and a live preview (the same engine now powers Codliy Theme Settings).',
                    ],
                    'ar' => [
                        'أداة تسلسل تطبيع خصوصيات المورد خلف API نظيفة.',
                        'طبقة محوّلات بوابات — واجهة واحدة ومشغلات لكل بوابة.',
                        'محرك ثيم بمتغيرات CSS ومعاينة حية (المحرك نفسه يشغّل إعدادات ثيم Codliy الآن).',
                    ],
                    'tr' => [
                        'Temiz bir API\'nin arkasında satıcı tuhaflıklarını normalleştiren serileştirme aracı.',
                        'Bir ağ geçidi adaptör katmanı — tek arayüz, ağ geçidi başına sürücüler.',
                        'CSS değişkenleri ve canlı önizleme ile tema motoru (şu an Codliy Tema Ayarları\'nı çalıştıran aynı motor).',
                    ],
                ],
                'results' => [
                    'en' => [
                        '3,400+ cards shipped across 9 countries.',
                        'Theme engine and admin scaffolding promoted into the Codliy starter.',
                        'Two gateway adapters still in use by other Codliy clients today.',
                    ],
                    'ar' => [
                        'أكثر من 3400 بطاقة شُحنت في 9 دول.',
                        'محرك الثيم وبنية الإدارة رُقّيا إلى قاعدة بداية Codliy.',
                        'محوّلا بوابتي دفع ما زالا مستخدمين اليوم لدى عملاء Codliy آخرين.',
                    ],
                    'tr' => [
                        '9 ülkede 3.400+ kart sevk edildi.',
                        'Tema motoru ve yönetim iskelesi Codliy başlangıç paketine yükseltildi.',
                        'İki ağ geçidi adaptörü bugün de diğer Codliy müşterileri tarafından kullanılıyor.',
                    ],
                ],
                'testimonial' => null,
                'testimonial_author' => null,
                'testimonial_position' => null,
                'is_featured' => true,
                'is_active' => true,
            ],

            // ==========================================================
            // 3. Turkish Marketer — campaign + CRM platform for SMEs
            // ==========================================================
            [
                'cover' => 'case.png',
                'title' => [
                    'en' => 'Turkish Marketer — SME campaign and CRM platform',
                    'ar' => 'Turkish Marketer — منصة حملات و CRM للشركات الصغيرة والمتوسطة',
                    'tr' => 'Turkish Marketer — KOBİ kampanya ve CRM platformu',
                ],
                'short_description' => [
                    'en' => 'A Turkish-language marketing + CRM suite for small businesses: WhatsApp campaigns, SMS, customer segmentation, and a lightweight sales pipeline — all under one login.',
                    'ar' => 'مجموعة تسويق + CRM باللغة التركية للشركات الصغيرة: حملات واتساب، SMS، تقسيم العملاء، وخط مبيعات خفيف — كله تحت تسجيل دخول واحد.',
                    'tr' => 'KOBİ\'ler için Türkçe pazarlama + CRM paketi: WhatsApp kampanyaları, SMS, müşteri segmentasyonu ve hafif bir satış hattı — hepsi tek oturum altında.',
                ],
                'description' => [
                    'en' => '<p class="lead">A local-first marketing platform for Turkish SMEs who need WhatsApp, SMS and email in one tool, priced and localized for the Turkish market.</p><h2>What we shipped</h2><ul><li>WhatsApp Business API integration with approved templates and delivery reports.</li><li>SMS campaigns via local Turkish providers (Iletimerkezi, NetGSM).</li><li>Lightweight CRM — contacts, notes, tags, pipeline stages.</li><li>Segmentation with saved filters and campaign suppression lists.</li><li>Iyzico-powered subscription billing with Turkish VAT handling.</li></ul><h2>Why it mattered</h2><p>Most international CRMs treat Turkey as an afterthought — wrong date formats, no local SMS providers, pricing in USD. This platform was built for it: TRY pricing, KVKK-compliant data handling, and support that actually speaks Turkish.</p>',
                    'ar' => '<p class="lead">منصة تسويق بتفضيل محلي لشركات صغيرة تركية تحتاج WhatsApp و SMS والبريد في أداة واحدة، مُسعّرة ومحلّية للسوق التركي.</p><h2>ما سلّمناه</h2><ul><li>تكامل WhatsApp Business API مع قوالب معتمدة وتقارير التسليم.</li><li>حملات SMS عبر مزودين محليين (Iletimerkezi، NetGSM).</li><li>CRM خفيف — جهات اتصال، ملاحظات، وسوم، مراحل مبيعات.</li><li>تقسيم مع مرشحات محفوظة وقوائم إيقاف الحملات.</li><li>فوترة اشتراكات بـ Iyzico مع معالجة ضريبة القيمة المضافة التركية.</li></ul>',
                    'tr' => '<p class="lead">WhatsApp, SMS ve e-postayı tek araçta ihtiyaç duyan Türk KOBİ\'leri için Türkiye pazarına göre fiyatlandırılmış ve yerelleştirilmiş yerel öncelikli bir pazarlama platformu.</p><h2>Teslim ettiklerimiz</h2><ul><li>Onaylı şablonlar ve teslim raporları ile WhatsApp Business API entegrasyonu.</li><li>Yerel sağlayıcılar (Iletimerkezi, NetGSM) üzerinden SMS kampanyaları.</li><li>Hafif CRM — kişiler, notlar, etiketler, boru hattı aşamaları.</li><li>Kaydedilmiş filtreler ve kampanya bastırma listeleri ile segmentasyon.</li><li>Türk KDV işleme ile Iyzico tabanlı abonelik faturalaması.</li></ul><h2>Neden önemliydi</h2><p>Çoğu uluslararası CRM Türkiye\'yi sonradan akla gelen bir şey gibi ele alır — yanlış tarih formatları, yerel SMS sağlayıcı yok, USD fiyatlandırma. Bu platform bunun için inşa edildi: TRY fiyatlandırma, KVKK uyumlu veri işleme ve gerçekten Türkçe konuşan destek.</p>',
                ],
                'category' => [
                    'en' => 'Marketing Platform',
                    'ar' => 'منصة تسويق',
                    'tr' => 'Pazarlama Platformu',
                ],
                'client_name' => 'Mehmet Y.',
                'client_company' => 'Turkish Marketer',
                'project_url' => null,
                'technologies' => ['Laravel', 'Vue 3', 'MySQL', 'Redis', 'Horizon', 'WhatsApp Business API', 'Iyzico'],
                'start_date' => '2023-03-01',
                'completion_date' => '2023-11-20',
                'project_duration' => '8 months',
                'features' => [
                    'en' => [
                        'WhatsApp Business API with templated campaigns',
                        'Local SMS providers: Iletimerkezi, NetGSM',
                        'Lightweight CRM with pipeline stages',
                        'Saved-filter segmentation + suppression lists',
                        'Iyzico subscriptions with Turkish VAT',
                    ],
                    'ar' => [
                        'WhatsApp Business API مع حملات قوالب',
                        'مزودو SMS محليون: Iletimerkezi، NetGSM',
                        'CRM خفيف مع مراحل مبيعات',
                        'تقسيم بمرشحات محفوظة وقوائم إيقاف',
                        'اشتراكات Iyzico مع ضريبة القيمة المضافة التركية',
                    ],
                    'tr' => [
                        'Şablonlu kampanyalarla WhatsApp Business API',
                        'Yerel SMS sağlayıcıları: Iletimerkezi, NetGSM',
                        'Boru hattı aşamalı hafif CRM',
                        'Kaydedilmiş filtre segmentasyonu + bastırma listeleri',
                        'Türk KDV ile Iyzico abonelikleri',
                    ],
                ],
                'challenges' => [
                    'en' => [
                        'WhatsApp template approval pipeline is slow and opaque.',
                        'Local SMS providers have very different APIs and quirks.',
                        'Turkish VAT rules on recurring subscriptions are subtle.',
                    ],
                    'ar' => [
                        'خط اعتماد قوالب WhatsApp بطيء وغير شفاف.',
                        'مزودو SMS محليون لديهم واجهات برمجية وخصوصيات مختلفة جدًا.',
                        'قواعد ضريبة القيمة المضافة التركية على الاشتراكات المتكررة دقيقة.',
                    ],
                    'tr' => [
                        'WhatsApp şablon onay süreci yavaş ve opak.',
                        'Yerel SMS sağlayıcılarının çok farklı API\'leri ve tuhaflıkları var.',
                        'Türk KDV kuralları tekrarlayan aboneliklerde inceliklidir.',
                    ],
                ],
                'solutions' => [
                    'en' => [
                        'Template library with pre-approved variants for common SME flows.',
                        'SMS adapter layer behind a single driver interface.',
                        'VAT + invoice module reviewed by a Turkish accountant.',
                    ],
                    'ar' => [
                        'مكتبة قوالب بمتغيرات معتمدة مسبقًا لتدفقات SME الشائعة.',
                        'طبقة محوّلات SMS خلف واجهة مشغل موحدة.',
                        'وحدة ضريبة القيمة المضافة والفواتير راجعها محاسب تركي.',
                    ],
                    'tr' => [
                        'Yaygın KOBİ akışları için önceden onaylanmış varyantlarla şablon kütüphanesi.',
                        'Tek bir sürücü arayüzü arkasında SMS adaptör katmanı.',
                        'Türk muhasebeci tarafından incelenen KDV + fatura modülü.',
                    ],
                ],
                'results' => [
                    'en' => [
                        '600+ active Turkish SMEs on the platform at handover.',
                        '3× reply rate vs. the client\'s previous email-only stack.',
                        'Support volume down 40% after onboarding checklist was added.',
                    ],
                    'ar' => [
                        'أكثر من 600 شركة تركية صغيرة نشطة على المنصة عند التسليم.',
                        'ضعف نسبة الرد 3× مقارنة بمكدس العميل السابق المقتصر على البريد.',
                        'انخفاض حجم الدعم 40% بعد إضافة قائمة تهيئة للمستخدمين.',
                    ],
                    'tr' => [
                        'Teslim sırasında platformda 600+ aktif Türk KOBİ.',
                        'Müşterinin önceki yalnızca e-posta yığınına göre 3× yanıt oranı.',
                        'Onboarding kontrol listesi eklendikten sonra destek hacmi %40 düştü.',
                    ],
                ],
                'testimonial' => [
                    'en' => 'The first platform our sales team actually opens every morning. Built by people who understood that "Türkiye\'de çalışmalı" is not the same as "add a language".',
                    'ar' => 'المنصة الأولى التي يفتحها فريق مبيعاتنا فعلاً كل صباح. مبنية من أشخاص فهموا أن "يجب أن تعمل في تركيا" ليست نفس "أضف لغة".',
                    'tr' => 'Satış ekibimizin her sabah gerçekten açtığı ilk platform. "Türkiye\'de çalışmalı" ile "dil ekle" nin aynı şey olmadığını anlayan kişiler tarafından yapıldı.',
                ],
                'testimonial_author' => 'Mehmet Y.',
                'testimonial_position' => 'Founder, Turkish Marketer',
                'is_featured' => true,
                'is_active' => true,
            ],

            // ==========================================================
            // 4. Logistics tracker — driver + dispatch mobile app
            // ==========================================================
            [
                'cover' => 'devops.png',
                'title' => [
                    'en' => 'FleetPing — driver + dispatch ops',
                    'ar' => 'FleetPing — عمليات السائقين والإرسال',
                    'tr' => 'FleetPing — sürücü + sevkiyat operasyonları',
                ],
                'short_description' => [
                    'en' => 'A driver mobile app and dispatch dashboard for a regional logistics operator — live tracking, proof of delivery, and route optimization.',
                    'ar' => 'تطبيق جوال للسائقين ولوحة إرسال لمشغل خدمات لوجستية إقليمي — تتبع مباشر، إثبات تسليم، وتحسين مسارات.',
                    'tr' => 'Bölgesel bir lojistik operatörü için sürücü mobil uygulaması ve sevkiyat panosu — canlı takip, teslimat kanıtı ve rota optimizasyonu.',
                ],
                'description' => [
                    'en' => '<p class="lead">Drivers used to radio in; dispatch used to pin markers on a whiteboard. We replaced both with a tight mobile-first system that a dispatcher and 40 drivers trust.</p><p>The driver app runs offline-first, syncs in the background, and never blocks the driver for more than a second. The dispatch dashboard shows live positions, delays, and suggested re-routes when traffic spikes.</p>',
                    'ar' => '<p class="lead">اعتاد السائقون الاتصال عبر اللاسلكي؛ واعتاد الإرسال وضع علامات على لوحة. استبدلنا الاثنين بنظام محمول أولاً محكم يثق به مرسل و 40 سائقًا.</p>',
                    'tr' => '<p class="lead">Sürücüler telsizle arardı; sevkiyat beyaz tahtaya pin iğnesi iliştirirdi. İkisini de bir sevkiyatçı ve 40 sürücünün güvendiği sıkı bir mobil öncelikli sistemle değiştirdik.</p>',
                ],
                'category' => [
                    'en' => 'Mobile + Dispatch',
                    'ar' => 'جوال + إرسال',
                    'tr' => 'Mobil + Sevkiyat',
                ],
                'client_name' => null,
                'client_company' => 'Regional Logistics Co.',
                'project_url' => null,
                'technologies' => ['Laravel', 'Flutter', 'PostgreSQL', 'Redis', 'Mapbox', 'Pusher'],
                'start_date' => '2023-08-01',
                'completion_date' => '2024-02-15',
                'project_duration' => '6 months',
                'features' => null,
                'challenges' => null,
                'solutions' => null,
                'results' => null,
                'testimonial' => null,
                'testimonial_author' => null,
                'testimonial_position' => null,
                'is_featured' => false,
                'is_active' => true,
            ],

            // ==========================================================
            // 5. Editorial CMS — multi-author publication
            // ==========================================================
            [
                'cover' => 'software.png',
                'title' => [
                    'en' => 'Inkstone — editorial CMS for a regional publication',
                    'ar' => 'Inkstone — نظام إدارة محتوى تحريري لإصدار إقليمي',
                    'tr' => 'Inkstone — bölgesel bir yayın için editoryal CMS',
                ],
                'short_description' => [
                    'en' => 'A multi-author editorial CMS with workflow states, scheduled publishing, and a newsroom-friendly editing experience.',
                    'ar' => 'نظام CMS تحريري متعدد المؤلفين بحالات سير عمل ونشر مجدول وتجربة تحرير مناسبة لغرفة الأخبار.',
                    'tr' => 'İş akışı durumları, zamanlanmış yayınlama ve haber odasına uygun bir düzenleme deneyimi olan çok yazarlı editoryal CMS.',
                ],
                'description' => [
                    'en' => '<p class="lead">A purpose-built editorial CMS that moves an article cleanly through draft → review → copy-edit → publish, with role-appropriate UIs at every stage.</p>',
                    'ar' => '<p class="lead">نظام CMS تحريري مصمم لغرض محدد ينقل المقال بنظافة خلال مسودة ← مراجعة ← تحرير نسخي ← نشر، بواجهات مناسبة للدور في كل مرحلة.</p>',
                    'tr' => '<p class="lead">Bir makaleyi taslak → inceleme → metin düzenleme → yayın arasında her aşamada role uygun arayüzlerle temiz bir şekilde taşıyan, özel yapım bir editoryal CMS.</p>',
                ],
                'category' => [
                    'en' => 'Publishing',
                    'ar' => 'نشر',
                    'tr' => 'Yayıncılık',
                ],
                'client_name' => null,
                'client_company' => 'Regional Publication',
                'project_url' => null,
                'technologies' => ['Laravel', 'Livewire', 'Tailwind CSS', 'MySQL', 'Meilisearch'],
                'start_date' => '2024-04-01',
                'completion_date' => '2024-09-30',
                'project_duration' => '6 months',
                'features' => null,
                'challenges' => null,
                'solutions' => null,
                'results' => null,
                'testimonial' => null,
                'testimonial_author' => null,
                'testimonial_position' => null,
                'is_featured' => false,
                'is_active' => true,
            ],

            // ==========================================================
            // 6. AI document assistant — internal knowledge base
            // ==========================================================
            [
                'cover' => 'agile.png',
                'title' => [
                    'en' => 'Atlas — AI-assisted internal knowledge base',
                    'ar' => 'Atlas — قاعدة معرفة داخلية بمساعدة الذكاء الاصطناعي',
                    'tr' => 'Atlas — AI destekli dahili bilgi tabanı',
                ],
                'short_description' => [
                    'en' => 'A private RAG-backed knowledge base for an enterprise ops team — answer questions over a decade of runbooks and postmortems, with citations.',
                    'ar' => 'قاعدة معرفة خاصة مدعومة بـ RAG لفريق عمليات مؤسسي — إجابات على عقد من كتيبات التشغيل ومراجعات الحوادث، مع استشهادات.',
                    'tr' => 'Bir kurumsal operasyon ekibi için özel RAG destekli bilgi tabanı — on yıllık runbook ve postmortem üzerinde, atıflarla sorulara yanıt verir.',
                ],
                'description' => [
                    'en' => '<p class="lead">A retrieval-augmented assistant built against the client\'s own document store — hybrid search, strict source citations, and an eval set that blocks regressions before deploy.</p>',
                    'ar' => '<p class="lead">مساعد مدعوم بالاسترجاع مبني على مستودع مستندات العميل — بحث هجين، استشهادات صارمة، ومجموعة تقييم تمنع التراجعات قبل النشر.</p>',
                    'tr' => '<p class="lead">Müşterinin kendi belge deposuna karşı oluşturulmuş geri çağırma destekli bir asistan — hibrit arama, kesin kaynak atıfları ve dağıtımdan önce gerilemeleri engelleyen bir değerlendirme seti.</p>',
                ],
                'category' => [
                    'en' => 'AI / RAG',
                    'ar' => 'ذكاء اصطناعي / RAG',
                    'tr' => 'AI / RAG',
                ],
                'client_name' => null,
                'client_company' => 'Enterprise Ops Team',
                'project_url' => null,
                'technologies' => ['Laravel', 'Python', 'OpenAI', 'pgvector', 'Meilisearch', 'Redis'],
                'start_date' => '2024-10-01',
                'completion_date' => '2025-02-28',
                'project_duration' => '5 months',
                'features' => null,
                'challenges' => null,
                'solutions' => null,
                'results' => null,
                'testimonial' => null,
                'testimonial_author' => null,
                'testimonial_position' => null,
                'is_featured' => false,
                'is_active' => true,
            ],
        ];
    }
}
