<?php

namespace Modules\Blog\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Modules\Blog\Enums\PostTypeEnum;
use Modules\Blog\Models\BlogCategory;
use Modules\Blog\Models\BlogPost;
use Modules\Blog\Models\BlogPostTags;
use Modules\Core\App\Enums\ActiveEnum;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        // -----------------------------------------------------------------
        // Categories — aligned with Codliy's services.
        // -----------------------------------------------------------------
        $categoriesData = [
            [
                'title' => ['en' => 'Engineering',   'ar' => 'الهندسة',      'tr' => 'Mühendislik'],
                'description' => [
                    'en' => 'Architecture, testing, observability and how we build software that stays maintainable.',
                    'ar' => 'العمارة البرمجية والاختبارات والمراقبة وكيف نبني برمجيات تظل قابلة للصيانة.',
                    'tr' => 'Mimari, testler, gözlemlenebilirlik ve sürdürülebilir yazılım üretme biçimimiz.',
                ],
                'is_active' => ActiveEnum::ACTIVE,
            ],
            [
                'title' => ['en' => 'AI & ML',        'ar' => 'الذكاء الاصطناعي', 'tr' => 'Yapay Zekâ'],
                'description' => [
                    'en' => 'LLMs, RAG, agents and the boring engineering that makes them production-safe.',
                    'ar' => 'نماذج اللغة الكبيرة والـ RAG والوكلاء الذكيون والهندسة التي تجعلهم آمنين في الإنتاج.',
                    'tr' => 'LLM, RAG, ajanlar ve onları üretime hazır kılan mühendislik.',
                ],
                'is_active' => ActiveEnum::ACTIVE,
            ],
            [
                'title' => ['en' => 'Cloud & DevOps', 'ar' => 'السحابة و DevOps', 'tr' => 'Bulut ve DevOps'],
                'description' => [
                    'en' => 'Infrastructure, CI/CD, incident response and keeping systems calm at 3am.',
                    'ar' => 'البنية التحتية و CI/CD والاستجابة للحوادث والحفاظ على هدوء الأنظمة في الثالثة فجرًا.',
                    'tr' => 'Altyapı, CI/CD, olay müdahalesi ve sistemlerin gecenin üçünde de sakin kalması.',
                ],
                'is_active' => ActiveEnum::ACTIVE,
            ],
            [
                'title' => ['en' => 'Product & Design', 'ar' => 'المنتج والتصميم', 'tr' => 'Ürün ve Tasarım'],
                'description' => [
                    'en' => 'Research-led UX, product strategy and the hand-off between design and code.',
                    'ar' => 'تجربة مستخدم مبنية على البحث واستراتيجية المنتج والتسليم بين التصميم والكود.',
                    'tr' => 'Araştırma odaklı UX, ürün stratejisi ve tasarım ile kod arasındaki devir teslim.',
                ],
                'is_active' => ActiveEnum::ACTIVE,
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $data) {
            $categories[] = BlogCategory::create($data);
        }

        // -----------------------------------------------------------------
        // Tags
        // -----------------------------------------------------------------
        $tagsData = [
            ['name' => ['en' => 'Laravel',      'ar' => 'لارافل',        'tr' => 'Laravel']],
            ['name' => ['en' => 'PHP',          'ar' => 'PHP',           'tr' => 'PHP']],
            ['name' => ['en' => 'Vue',          'ar' => 'Vue',           'tr' => 'Vue']],
            ['name' => ['en' => 'Next.js',      'ar' => 'Next.js',       'tr' => 'Next.js']],
            ['name' => ['en' => 'RAG',          'ar' => 'RAG',           'tr' => 'RAG']],
            ['name' => ['en' => 'LLM',          'ar' => 'LLM',           'tr' => 'LLM']],
            ['name' => ['en' => 'AWS',          'ar' => 'AWS',           'tr' => 'AWS']],
            ['name' => ['en' => 'CI/CD',        'ar' => 'CI/CD',         'tr' => 'CI/CD']],
            ['name' => ['en' => 'Observability', 'ar' => 'المراقبة',      'tr' => 'Gözlemlenebilirlik']],
            ['name' => ['en' => 'Security',     'ar' => 'الأمان',        'tr' => 'Güvenlik']],
            ['name' => ['en' => 'Architecture', 'ar' => 'المعمارية',     'tr' => 'Mimari']],
            ['name' => ['en' => 'Design',       'ar' => 'التصميم',       'tr' => 'Tasarım']],
        ];

        $tags = [];
        foreach ($tagsData as $data) {
            $tags[] = BlogPostTags::create($data);
        }

        // Indices: 0 Laravel, 1 PHP, 2 Vue, 3 Next, 4 RAG, 5 LLM,
        //         6 AWS, 7 CI/CD, 8 Observability, 9 Security, 10 Architecture, 11 Design.

        // -----------------------------------------------------------------
        // Posts
        // -----------------------------------------------------------------
        // Cover images live under `public/assets/blog/`. If we ever move
        // them again, update this constant and the `cover` filenames below.
        $blogCoverDir = public_path('assets/blog');

        // Track how many covers actually attached so the final output
        // surfaces silent drops (broken file paths, Spatie disk issues).
        $mediaAttached = 0;

        $posts = [
            [
                'category' => 0, // Engineering
                'cover' => 'software.png',
                'title' => [
                    'en' => 'Laravel at scale: lessons from shipping a six-figure-user SaaS',
                    'ar' => 'لارافل على نطاق واسع: دروس من إطلاق SaaS بمئات الآلاف من المستخدمين',
                    'tr' => 'Ölçekte Laravel: altı haneli kullanıcılı bir SaaS üretirken öğrendiklerimiz',
                ],
                'description' => [
                    'en' => '<p class="lead">Laravel can absolutely run at scale — but only if you treat the framework as a starting point, not a finish line. Here is what we changed when traffic crossed a million requests a day.</p><h2>Queue hygiene</h2><p>Every non-trivial interaction became a queued job. We split queues by latency budget: <code>realtime</code> (&lt; 1s), <code>batch</code> (retries, emails), and <code>heavy</code> (reports, exports). Horizon dashboards live next to the on-call runbook.</p><h2>Caching as a first-class concern</h2><ul><li>Read-through caches around every repository method with a consistent TTL policy.</li><li>Tag-based invalidation keyed on the aggregate root.</li><li>A small "cache budget" per request — exceeding it logs a warning that shows up in code review.</li></ul><h2>Database discipline</h2><p>No N+1 ships. Our code review checklist includes <code>with()</code> verification and an explicit note on each query that runs inside a loop. Telescope is useful in staging; in production we use Blackfire and slow-query logs.</p><blockquote>The framework does not make you fast. Predictability does.</blockquote><h2>Observability</h2><p>Structured logs, request IDs propagated through queues, p95 latency on every public endpoint, and a weekly review of the top-10 slowest routes. Nothing exotic — just applied rigorously.</p>',
                    'ar' => '<p class="lead">يمكن لـ Laravel بالتأكيد أن يعمل على نطاق واسع — لكن فقط إذا عاملتَ الإطار كنقطة انطلاق لا كخط نهاية. إليك ما غيّرناه عندما تجاوزت حركة المرور مليون طلب يوميًا.</p><h2>نظافة الطوابير</h2><p>أصبحت كل عملية غير بسيطة مَهمّةً في طابور. قسّمنا الطوابير حسب ميزانية الزمن: <code>realtime</code> و<code>batch</code> و<code>heavy</code>.</p><h2>التخزين المؤقت كمواطن من الدرجة الأولى</h2><p>Read-through caches حول كل دالة مستودع، وإبطال قائم على الوسوم، وميزانية تخزين مؤقت لكل طلب.</p><h2>انضباط قاعدة البيانات</h2><p>لا يمرّ N+1 أبدًا. نتحقق من <code>with()</code> في كل مراجعة كود.</p><h2>المراقبة</h2><p>سجلات منظمة، ومعرفات طلبات تنتقل عبر الطوابير، ومراجعة أسبوعية لأبطأ 10 مسارات.</p>',
                    'tr' => '<p class="lead">Laravel kesinlikle ölçekte çalışabilir — ama bunun için çerçeveyi bir başlangıç noktası olarak görmeniz gerekir. İşte günlük bir milyon isteği geçtiğimizde değiştirdiklerimiz.</p><h2>Kuyruk hijyeni</h2><p>Her önemsiz etkileşim kuyruğa alındı. Gecikme bütçesine göre ayrılmış kuyruklar: <code>realtime</code>, <code>batch</code>, <code>heavy</code>.</p><h2>Birinci sınıf bir önbellekleme</h2><p>Her depo metodu etrafında read-through cache, agregat köküne bağlı etiket tabanlı geçersizleştirme, istek başına cache bütçesi.</p><h2>Veritabanı disiplini</h2><p>N+1 yayına çıkmaz. <code>with()</code> her kod incelemesinde doğrulanır.</p><h2>Gözlemlenebilirlik</h2><p>Yapılandırılmış loglar, kuyruklar arasında taşınan istek kimlikleri ve en yavaş 10 rotanın haftalık incelemesi.</p>',
                ],
                'tags' => [0, 1, 8, 10], // Laravel, PHP, Observability, Architecture
                'clapping' => 128,
            ],
            [
                'category' => 1, // AI & ML
                'cover' => 'systems.png',
                'title' => [
                    'en' => 'RAG without hype: a production checklist we actually use',
                    'ar' => 'RAG بدون ضجيج: قائمة فحص إنتاجية نستخدمها فعلًا',
                    'tr' => 'Abartısız RAG: gerçekten kullandığımız üretim kontrol listesi',
                ],
                'description' => [
                    'en' => '<p class="lead">Most retrieval-augmented generation demos collapse the moment real users show up. These are the boring things we check before a RAG system is allowed anywhere near production.</p><h2>Evaluation first</h2><p>No eval set, no merge. We build a golden set of 200–500 Q&A pairs reviewed by a subject-matter expert before writing a single embedding. Every change is graded against it.</p><h2>Retrieval quality</h2><ul><li>Hybrid search — dense + BM25 — with a fallback to keyword-only for exact product codes.</li><li>Chunking informed by the document type (code, policy, transcript), not a fixed 512 tokens.</li><li>Re-ranking with a small cross-encoder when latency budget allows.</li></ul><h2>Guardrails that matter</h2><p>Input filtering, output filtering, and cost ceilings. Every call is observable: prompt, retrieved chunks, model response, cost, latency. We replay problem queries in CI.</p><h2>The quiet part</h2><blockquote>80% of RAG quality comes from the data pipeline, not the model.</blockquote><p>Get the ingestion, normalization and eval loop right first. The rest is tuning.</p>',
                    'ar' => '<p class="lead">معظم عروض RAG تنهار فور ظهور مستخدمين حقيقيين. هذه هي الأمور المملة التي نفحصها قبل السماح لنظام RAG بالاقتراب من الإنتاج.</p><h2>التقييم أولًا</h2><p>لا eval set، لا merge. نبني مجموعة ذهبية من 200–500 زوج سؤال/جواب يراجعها خبير قبل كتابة أي تضمين.</p><h2>جودة الاسترجاع</h2><p>بحث هجين (dense + BM25)، وتقسيم موجّه بنوع المستند، وإعادة ترتيب بـ cross-encoder صغير عند السماح بميزانية الزمن.</p><h2>ضوابط تهمّ فعلًا</h2><p>تصفية المدخلات والمخرجات وحدّ أقصى للتكلفة. كل استدعاء يمكن مراقبته.</p><blockquote>80% من جودة RAG تأتي من خط أنابيب البيانات، لا من النموذج.</blockquote>',
                    'tr' => '<p class="lead">RAG demolarının çoğu gerçek kullanıcı görünce çöker. Bir RAG sisteminin üretime yaklaşmasına izin vermeden önce kontrol ettiğimiz sıkıcı şeyler.</p><h2>Önce değerlendirme</h2><p>Eval seti yoksa merge yok. Tek bir embedding yazmadan önce alan uzmanı tarafından incelenmiş 200–500 soru-cevaptan oluşan altın küme oluştururuz.</p><h2>Geri çağırma kalitesi</h2><p>Hibrit arama (dense + BM25), belge türüne göre chunking, bütçe izin verirse cross-encoder ile yeniden sıralama.</p><h2>Önemli koruma bantları</h2><p>Giriş/çıkış filtreleme ve maliyet tavanı. Her çağrı izlenebilir.</p><blockquote>RAG kalitesinin %80\'i veri boru hattından gelir, modelden değil.</blockquote>',
                ],
                'tags' => [4, 5, 8, 9], // RAG, LLM, Observability, Security
                'clapping' => 214,
            ],
            [
                'category' => 2, // Cloud & DevOps
                'cover' => 'devops.png',
                'title' => [
                    'en' => 'Zero-downtime deploys on a budget: Laravel + AWS in practice',
                    'ar' => 'إطلاق بدون توقف بميزانية محدودة: Laravel + AWS في الممارسة',
                    'tr' => 'Bütçeyle sıfır kesintili dağıtım: pratikte Laravel + AWS',
                ],
                'description' => [
                    'en' => '<p class="lead">You do not need a platform team to deploy Laravel with zero downtime. You need a handful of primitives and the discipline to wire them up once.</p><h2>The shape of a deploy</h2><ol><li>Build an immutable artifact (Docker image or a zipped release).</li><li>Run migrations with <code>--isolated</code> during a small maintenance window — or use expand/contract for hot tables.</li><li>Shift traffic via weighted target groups or a blue/green ALB.</li><li>Warm the new stack before taking traffic — queue workers, cache primers, health checks.</li></ol><h2>Rollback is a feature</h2><p>Every deploy has a one-command rollback. Every migration has a reverse plan. If your only recovery strategy is "restore from backup", you do not have one.</p><h2>Keeping the bill sane</h2><ul><li>Reserved instances for baseline workloads.</li><li>Spot for queue workers with graceful shutdown.</li><li>S3 lifecycle rules for logs and backups.</li></ul>',
                    'ar' => '<p class="lead">لستَ بحاجة إلى فريق منصات لإطلاق Laravel بدون توقف. تحتاج إلى عدد قليل من الأساسيات وانضباط لتوصيلها مرة واحدة.</p><h2>شكل الإطلاق</h2><p>أنشئ artifact غير قابل للتغيير، شغّل المهاجرات بـ <code>--isolated</code>، حوّل الحركة عبر target groups موزونة، وأحمِ المكدّس الجديد قبل استقبال الحركة.</p><h2>التراجع ميزة</h2><p>كل إطلاق لديه تراجع بأمر واحد. كل migration لديه خطة عكسية.</p><h2>إبقاء الفاتورة معقولة</h2><p>Reserved instances للأحمال الأساسية، Spot لعمال الطوابير، قواعد lifecycle لـ S3.</p>',
                    'tr' => '<p class="lead">Laravel\'ı sıfır kesintiyle dağıtmak için platform ekibine ihtiyacınız yok. Birkaç temel yapı taşına ve bunları bir kez doğru bağlama disiplinine ihtiyacınız var.</p><h2>Bir dağıtımın şekli</h2><p>Değişmez bir artifact oluştur, <code>--isolated</code> ile migration çalıştır, ağırlıklı target group ile trafiği kaydır, yeni stack\'i önceden ısıt.</p><h2>Geri alma bir özelliktir</h2><p>Her dağıtımın tek komutla geri alma yolu var. Her migration\'ın ters planı var.</p><h2>Faturayı makul tutmak</h2><p>Temel yükler için reserved instance, kuyruk işçileri için spot, S3 için lifecycle kuralları.</p>',
                ],
                'tags' => [6, 7, 0], // AWS, CI/CD, Laravel
                'clapping' => 97,
            ],
            [
                'category' => 3, // Product & Design
                'cover' => 'design.png',
                'title' => [
                    'en' => 'The design-to-code handover that actually works',
                    'ar' => 'التسليم من التصميم إلى الكود الذي يعمل حقًا',
                    'tr' => 'Gerçekten işe yarayan tasarım-koda devir teslim',
                ],
                'description' => [
                    'en' => '<p class="lead">Most "handovers" are just Figma links tossed over a wall. Here is what we do instead — every project, every time.</p><h2>Shared language</h2><p>Design tokens live in one place, exported to both Figma styles and Tailwind/SCSS variables. No component ships without the token it consumes being named in the design file.</p><h2>The interaction log</h2><p>For each screen, designers write a short list of interactions: empty state, loading, error, success, keyboard shortcut. Engineers refuse to implement screens without this list. It is five minutes of writing that saves five hours of back-and-forth.</p><h2>Accessibility from the start</h2><ul><li>Color contrast checked at the token level.</li><li>Keyboard flows drawn on the same canvas as the layouts.</li><li>Screen reader copy written by the designer, not retro-fitted.</li></ul><blockquote>If a screen cannot be used with a keyboard, it is not done.</blockquote>',
                    'ar' => '<p class="lead">معظم "عمليات التسليم" هي مجرد روابط Figma تُرمى فوق الجدار. إليك ما نفعله بدلًا من ذلك — في كل مشروع، في كل مرة.</p><h2>لغة مشتركة</h2><p>Design tokens في مكان واحد، تُصدَّر إلى أنماط Figma ومتغيرات Tailwind/SCSS.</p><h2>سجل التفاعلات</h2><p>لكل شاشة، يكتب المصممون قائمة قصيرة بالتفاعلات: الحالة الفارغة، التحميل، الخطأ، النجاح، اختصارات لوحة المفاتيح.</p><h2>إمكانية الوصول من البداية</h2><p>تباين الألوان يُفحص على مستوى الـ token، وتدفقات لوحة المفاتيح تُرسم على نفس الكانفس.</p>',
                    'tr' => '<p class="lead">"Devir teslim"lerin çoğu duvarın üstünden atılan Figma linkleridir. Bizim yerine yaptığımız — her projede, her seferinde.</p><h2>Ortak dil</h2><p>Design token\'lar tek bir yerde, hem Figma stillerine hem Tailwind/SCSS değişkenlerine dışa aktarılır.</p><h2>Etkileşim günlüğü</h2><p>Her ekran için tasarımcılar kısa bir etkileşim listesi yazar: boş durum, yükleme, hata, başarı, klavye kısayolu.</p><h2>Baştan erişilebilirlik</h2><p>Kontrast token seviyesinde kontrol edilir, klavye akışları layout ile aynı kanvasta çizilir.</p>',
                ],
                'tags' => [11, 2, 3], // Design, Vue, Next
                'clapping' => 76,
            ],
            [
                'category' => 0, // Engineering
                'cover' => 'agile.png',
                'title' => [
                    'en' => 'Testing strategy for small teams: 80/20 pragmatism',
                    'ar' => 'استراتيجية اختبار للفرق الصغيرة: براغماتية 80/20',
                    'tr' => 'Küçük ekipler için test stratejisi: 80/20 pragmatizmi',
                ],
                'description' => [
                    'en' => '<p class="lead">You do not need 100% coverage. You need the tests that would have caught the last five bugs you shipped.</p><h2>The pyramid, honestly</h2><ul><li><strong>Unit:</strong> pure logic, domain rules, calculations. Cheap, fast, reliable.</li><li><strong>Feature:</strong> route → controller → database, asserting the observable behavior. This is where most of our value lives.</li><li><strong>Browser:</strong> only for flows that cannot be expressed at the HTTP layer — drag and drop, complex state machines, webhooks with signatures.</li></ul><h2>What we stopped doing</h2><p>We stopped writing tests for getters and setters. We stopped mocking every collaborator. We stopped aiming for an arbitrary coverage number and started aiming for a short, confident list of scenarios per feature.</p><h2>The CI loop</h2><p>Under 10 minutes or developers route around it. Parallel Pest, cached dependencies, a single source of truth for seeded data.</p>',
                    'ar' => '<p class="lead">لستَ بحاجة إلى تغطية 100%. أنت بحاجة إلى الاختبارات التي كانت ستلتقط آخر خمس علل أطلقتَها.</p><h2>الهرم بصدق</h2><p>Unit: منطق صرف. Feature: route → controller → قاعدة البيانات مع التحقق من السلوك. Browser: فقط للتدفقات التي لا يمكن التعبير عنها عبر HTTP.</p><h2>ما توقفنا عن فعله</h2><p>توقفنا عن كتابة اختبارات للـ getters والـ setters، وعن محاكاة كل متعاون.</p><h2>دورة CI</h2><p>أقل من 10 دقائق أو سيتحايل عليها المطورون.</p>',
                    'tr' => '<p class="lead">%100 kapsama ihtiyacınız yok. Yayınladığınız son beş hatayı yakalayacak testlere ihtiyacınız var.</p><h2>Piramit, dürüstçe</h2><p>Unit: saf mantık. Feature: route → controller → veritabanı, gözlemlenebilir davranışı doğrulama. Browser: sadece HTTP katmanında ifade edilemeyen akışlar.</p><h2>Bırakmış olduklarımız</h2><p>Getter ve setter için test yazmayı bıraktık. Her işbirlikçiyi mocklamayı bıraktık.</p><h2>CI döngüsü</h2><p>10 dakikanın altında değilse geliştiriciler onu atlatır.</p>',
                ],
                'tags' => [0, 1, 7], // Laravel, PHP, CI/CD
                'clapping' => 54,
            ],
            [
                'category' => 2, // Cloud & DevOps
                'cover' => 'case.png',
                'title' => [
                    'en' => 'Security by default: the OWASP items we refuse to skip',
                    'ar' => 'الأمان افتراضيًا: بنود OWASP التي نرفض تجاوزها',
                    'tr' => 'Varsayılan güvenlik: atlamayı reddettiğimiz OWASP maddeleri',
                ],
                'description' => [
                    'en' => '<p class="lead">Security is not a sprint at the end — it is a handful of defaults baked into every project on day one.</p><h2>The non-negotiables</h2><ul><li>Dependency review in CI, with weekly SCA reports reaching the engineering lead.</li><li>Explicit RBAC from the first migration. No "admin flag on the user model" shortcuts.</li><li>Signed URLs for every file the user should not enumerate.</li><li>Rate limiting on every write endpoint — measured, not guessed.</li><li>Audit log at the aggregate root, append-only, tamper-evident.</li></ul><h2>Secrets</h2><p>Secrets live in a real secrets manager (AWS SSM, HashiCorp Vault). Not in <code>.env</code> files committed to a private repo, not in CI variables that nobody reviews.</p><h2>The review that catches the most</h2><blockquote>Threat-model every user-supplied string. What if it is 10MB? What if it is binary? What if it is signed by a different key?</blockquote>',
                    'ar' => '<p class="lead">الأمان ليس ركضة في النهاية — بل مجموعة من الإعدادات الافتراضية المخبوزة في كل مشروع منذ اليوم الأول.</p><h2>غير قابلة للتفاوض</h2><p>مراجعة الاعتمادات في CI، RBAC صريح من أول migration، روابط موقعة، rate limiting على كل endpoint كتابة، سجل تدقيق append-only.</p><h2>الأسرار</h2><p>الأسرار في مدير أسرار حقيقي، لا في ملفات <code>.env</code>.</p><blockquote>نمذِج التهديدات لكل سلسلة مزوّدة من المستخدم.</blockquote>',
                    'tr' => '<p class="lead">Güvenlik sondaki bir sprint değildir — birinci gün her projeye yerleştirilen birkaç varsayılandır.</p><h2>Taviz verilmeyenler</h2><p>CI\'da bağımlılık incelemesi, ilk migration\'dan itibaren açık RBAC, imzalı URL\'ler, yazma uç noktalarında rate limiting, append-only denetim günlüğü.</p><h2>Sırlar</h2><p>Sırlar gerçek bir secrets manager\'da yaşar.</p><blockquote>Kullanıcı tarafından sağlanan her string için tehdit modellemesi yapın.</blockquote>',
                ],
                'tags' => [9, 6, 10], // Security, AWS, Architecture
                'clapping' => 112,
            ],
        ];

        foreach ($posts as $postData) {
            $categoryIndex = $postData['category'];
            $coverFile = $postData['cover'];
            $tagIndices = $postData['tags'];

            unset($postData['category'], $postData['cover'], $postData['tags']);

            $postData['category_id'] = $categories[$categoryIndex]->id;
            $postData['type'] = PostTypeEnum::PUBLISHED;
            $postData['is_active'] = ActiveEnum::ACTIVE;

            $post = BlogPost::create($postData);

            // Attach tags
            $mappedTagIds = array_map(fn ($i) => $tags[$i]->id, $tagIndices);
            $post->tags()->attach($mappedTagIds);

            // Attach cover image from public/assets/blog/. Every post in
            // the seed data has a cover (see `cover` keys above), so we
            // don't guard against null here — PHPStan flags it as dead
            // code, and more importantly, a null slipping through is a
            // seed-data bug we want to hear about loudly.
            $fullPath = $blogCoverDir . DIRECTORY_SEPARATOR . $coverFile;

            if (! File::exists($fullPath)) {
                $this->command->warn(
                    "Cover file missing on disk: {$fullPath} (post: {$postData['title']['en']})"
                );

                continue;
            }

            try {
                $post->addMedia($fullPath)
                    ->preservingOriginal()
                    ->toMediaCollection('img');

                $mediaAttached++;
            } catch (\Throwable $e) {
                // Surface the error loudly so misconfigured disks,
                // missing storage symlinks, or Spatie migration issues
                // don't get swallowed.
                $this->command->error(
                    "Could not attach cover for '{$postData['title']['en']}': " .
                    $e->getMessage()
                );
            }
        }

        $this->command->info(
            'Codliy blog seeded: ' . count($categories) . ' categories, ' .
            count($tags) . ' tags, ' . count($posts) . ' posts, ' .
            $mediaAttached . ' cover images attached.'
        );

        if ($mediaAttached < count($posts)) {
            $this->command->warn(
                'Some cover images did not attach. Check: ' .
                '(1) files exist under public/assets/blog/, ' .
                '(2) the "media" table exists (run Spatie\'s migration), ' .
                '(3) storage/app/public is writable, ' .
                '(4) `php artisan storage:link` was run so uploads are web-reachable.'
            );
        }
    }
}
