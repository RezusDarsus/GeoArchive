<?php

namespace Database\Seeders;

use App\Models\Artifact;
use App\Models\Category;
use App\Models\HistoricalEvent;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@geoarchive.test'],
            ['name' => 'GeoArchive Admin', 'password' => 'password', 'role' => 'admin'],
        );
        $admin->profile()->updateOrCreate(
            ['user_id' => $admin->id],
            ['bio' => 'Administrator and curator of the GeoArchive collection.'],
        );

        $user = User::updateOrCreate(
            ['email' => 'user@geoarchive.test'],
            ['name' => 'GeoArchive User', 'password' => 'password', 'role' => 'user'],
        );
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['bio' => 'A student of Georgian history.'],
        );

        $categories = [
            'Manuscript' => [
                'description' => 'Georgia has one of the oldest continuous literary traditions in the Caucasus. This collection includes Gospel books, royal charters, chronicles, hymnography, and copies of major works such as The Knight in the Panther’s Skin. Georgian manuscripts preserve the development of the Asomtavruli, Nuskhuri, and Mkhedruli scripts, while their bindings, calligraphy, miniatures, and marginal notes reveal the work of monastic schools and generations of copyists.',
                'image' => 'categories/manuscript.jpg',
            ],
            'Coin' => [
                'description' => 'Coins provide compact evidence for ancient trade, royal authority, religious symbols, and Georgia’s place between the Black Sea, Anatolia, Iran, and the wider Mediterranean. The archive begins with silver money associated with Colchis and continues through the coinage of Georgian kingdoms. Inscriptions, portraits, metal composition, and find locations help historians reconstruct economic connections that written chronicles do not always record.',
                'image' => 'categories/coin.png',
            ],
            'Church' => [
                'description' => 'Christianity became central to Georgian state and culture after the conversion of the Kingdom of Iberia in the fourth century. This category gathers icons, crosses, enamel work, liturgical objects, and other sacred heritage. These objects connect religious practice with royal patronage, long-distance artistic exchange, local workshops, and the monasteries that protected Georgian language and learning during periods of invasion and political division.',
                'image' => 'categories/church.jpg',
            ],
            'Weapon' => [
                'description' => 'Weapons document both the organized armies of Georgian kingdoms and the martial traditions of mountain communities. Swords, axes, shields, bows, firearms, and decorated fittings can show changes in military technology and contact with neighboring regions. They also appear in chronicles, epic literature, family memory, and ceremonial dress, where a weapon could represent status and responsibility as much as combat.',
                'image' => 'categories/weapon.jpg',
            ],
            'Archaeological Object' => [
                'description' => 'Archaeological finds extend Georgia’s history far beyond surviving written sources. From the early Homo fossils of Dmanisi to Bronze Age burials and urban centers such as Vani, excavated objects reveal settlement, food production, ritual, craft specialization, trade, and migration. Their meaning comes not only from appearance but also from the precise layer, building, grave, or landscape in which archaeologists discovered them.',
                'image' => 'categories/archaeological-object.jpg',
            ],
            'Architecture' => [
                'description' => 'Georgia’s built heritage reflects geography, faith, defense, and regional identity. The collection includes early basilicas, cross-domed churches, monasteries, fortresses, palaces, bridges, carved façades, and the defensive towers of Svaneti. Buildings such as Jvari, Svetitskhoveli, Gelati, and the Svan tower settlements demonstrate how Georgian builders adapted stone construction to mountain landscapes while developing forms that influenced the wider Caucasus.',
                'image' => 'categories/architecture.jpg',
            ],
            'Painting' => [
                'description' => 'Georgian painting ranges from monumental church frescoes and devotional icons to manuscript illumination and modern portraiture. Medieval painters combined Byzantine Christian subjects with local faces, clothing, inscriptions, and color traditions. Surviving murals also preserve rare images of rulers and patrons, making them valuable historical documents as well as works of art.',
                'image' => 'categories/painting.jpg',
            ],
        ];

        foreach ($categories as $name => $data) {
            Category::updateOrCreate(['name' => $name], $data);
        }

        $tagNames = [
            'Prehistoric', 'Ancient', 'Medieval', 'Colchis', 'Imereti', 'King Tamar',
            'Religion', 'War', 'Culture', 'Golden Age', 'Writing', 'Independence',
        ];
        foreach ($tagNames as $name) {
            Tag::firstOrCreate(compact('name'));
        }

        // Inserted chronologically so the public timeline can begin with Colchis
        // and move through the medieval unification and David the Builder.
        $events = [
            ['title' => 'Colchis in Western Georgia', 'description' => 'Colchis, known in Georgian tradition as Egrisi or Kolkheti, developed along the eastern Black Sea in present-day western Georgia. Its wealth, metalworking, agriculture, and Black Sea connections placed it among the earliest major political and cultural centers associated with Georgian history.', 'date_or_period' => 'Late Bronze Age–1st century BCE', 'location' => 'Western Georgia and the eastern Black Sea coast'],
            ['title' => 'Rise of the Kingdom of Iberia', 'description' => 'The eastern Georgian kingdom of Kartli, called Iberia by Greco-Roman writers, emerged as a major state in the South Caucasus. Georgian tradition associates its early monarchy with Pharnavaz I and the formation of institutions that connected the regions around Mtskheta.', 'date_or_period' => '3rd century BCE', 'location' => 'Mtskheta and eastern Georgia'],
            ['title' => 'Christianization of the Kingdom of Iberia', 'description' => 'In the early fourth century, the preaching of Saint Nino led King Mirian III of Iberia, the ancient Georgian kingdom of Kartli, to adopt Christianity. The event became a foundation of Georgia’s religious, literary, and architectural identity.', 'date_or_period' => 'Early 4th century', 'location' => 'Mtskheta, Kingdom of Iberia'],
            ['title' => 'Reign of Vakhtang Gorgasali', 'description' => 'Vakhtang I Gorgasali ruled Iberia during the late fifth and early sixth centuries, reorganized royal power, supported the Georgian Church, and resisted Sasanian dominance. Georgian tradition also closely connects his reign with the establishment of Tbilisi as a royal center.', 'date_or_period' => 'Late 5th–early 6th century', 'location' => 'Kartli and Tbilisi'],
            ['title' => 'Lazic War in Egrisi', 'description' => 'The Byzantine and Sasanian empires fought for control of Lazica, the western Georgian successor to ancient Colchis. The twenty-year conflict involved local Lazic rulers and ended with Lazica remaining within the Byzantine sphere.', 'date_or_period' => '541–562', 'location' => 'Egrisi/Lazica, western Georgia'],
            ['title' => 'Principality of Iberia', 'description' => 'After the Sasanian suppression of the Iberian monarchy around 580, leading Georgian nobles governed Kartli through a presiding prince. The principality survived between larger empires and preserved a framework for the later restoration of Georgian kingship.', 'date_or_period' => 'c. 580–888', 'location' => 'Kartli, eastern Georgia'],
            ['title' => 'First Arab Invasions of Georgia', 'description' => 'Arab armies entered Georgian lands in the mid-seventh century. Control varied by region, and Georgian nobles often retained local authority while paying tribute, but repeated campaigns reshaped politics and caused major destruction.', 'date_or_period' => 'From the 640s', 'location' => 'Kartli and other Georgian regions'],
            ['title' => 'Establishment of the Emirate of Tbilisi', 'description' => 'Tbilisi became the center of a Muslim emirate created during Arab rule. The emirate controlled parts of eastern Georgia and remained an important political and commercial power until the city returned to Georgian rule under David IV.', 'date_or_period' => '736–1122', 'location' => 'Tbilisi'],
            ['title' => 'Formation of the Kingdom of Abkhazia', 'description' => 'A powerful western Georgian kingdom emerged in the late eighth century. Its rulers expanded eastward, promoted Georgian ecclesiastical and literary culture, and later joined the dynastic process that produced the unified Kingdom of Georgia.', 'date_or_period' => 'From the 780s', 'location' => 'Western Georgia'],
            ['title' => 'Ashot I and the Revival of Tao-Klarjeti', 'description' => 'Ashot I of the Bagrationi dynasty established a strong base in Tao-Klarjeti in southwestern Georgian lands. Political recovery, monastery building, manuscript production, and the revival of Artanuji helped prepare the Bagrationi restoration of Georgian kingship.', 'date_or_period' => 'Early 9th century', 'location' => 'Tao-Klarjeti'],
            ['title' => 'Restoration of Georgian Kingship', 'description' => 'Adarnase IV of the Bagrationi dynasty assumed the royal title in 888, restoring Georgian kingship after centuries of foreign domination and princely rule. The Kingdom of the Iberians became a major step toward national unification.', 'date_or_period' => '888', 'location' => 'Tao-Klarjeti and Kartli'],
            ['title' => 'Unification of the Kingdom of Georgia', 'description' => 'Bagrat III united major Georgian realms through dynastic inheritance, diplomacy, and military power. In 1008 he became the first ruler of the unified Kingdom of Georgia, joining western and eastern Georgian political traditions.', 'date_or_period' => '1008', 'location' => 'Kingdom of Georgia'],
            ['title' => 'The Great Turkish Invasion', 'description' => 'Repeated Seljuk incursions devastated Georgian towns, villages, agriculture, and royal authority during the eleventh century. The crisis reached its height before the accession of David IV and shaped the reforms of his reign.', 'date_or_period' => '1060s–1080s', 'location' => 'Kingdom of Georgia'],
            ['title' => 'Accession of David IV the Builder', 'description' => 'David IV became king at a time of Seljuk pressure and internal weakness. He rebuilt royal authority, reorganized the army and administration, resettled damaged lands, and began the long reconquest that transformed medieval Georgia.', 'date_or_period' => '1089', 'location' => 'Kingdom of Georgia'],
            ['title' => 'Council of Ruisi-Urbnisi', 'description' => 'David IV convened a major church council to reform ecclesiastical leadership, discipline, and administration. The reforms strengthened cooperation between the Georgian monarchy and Church as part of the king’s broader state-building program.', 'date_or_period' => '1103', 'location' => 'Ruisi and Urbnisi'],
            ['title' => 'Battle of Ertsukhi', 'description' => 'David IV defeated a Seljuk-backed force after bringing Kakheti-Hereti under his authority. The victory secured eastern Georgian lands and became an important stage in the reunification of the kingdom.', 'date_or_period' => '1104', 'location' => 'Ertsukhi, eastern Georgia'],
            ['title' => 'Battle of Didgori', 'description' => 'King David IV led the Georgian army to a decisive victory over a much larger Seljuk coalition. The victory opened the way to the liberation of Tbilisi and became a defining moment of Georgian statehood.', 'date_or_period' => '12 August 1121', 'location' => 'Didgori, Georgia'],
            ['title' => 'Liberation of Tbilisi', 'description' => 'David IV captured Tbilisi from the emirate and made the city the capital of the unified Georgian kingdom. The event ended centuries of emirate rule and restored Tbilisi as the central political and commercial city of Georgia.', 'date_or_period' => '1122', 'location' => 'Tbilisi'],
            ['title' => 'Legacy of David the Builder', 'description' => 'By the end of David IV’s reign, Georgia had been transformed into a centralized regional power. His military, administrative, educational, and church reforms created the foundation for the Georgian Golden Age.', 'date_or_period' => '1089–1125', 'location' => 'Kingdom of Georgia'],
            ['title' => 'Golden Age of Georgia', 'description' => 'A period of political strength, cultural achievement, architecture, literature, and learning that began under David IV and reached its height under King Tamar.', 'date_or_period' => '11th–13th centuries', 'location' => 'Kingdom of Georgia'],
            ['title' => 'Reign of King Tamar', 'description' => 'Tamar ruled as king of Georgia during a period of expansion and cultural flourishing. Her reign remains one of the most celebrated chapters in Georgian history.', 'date_or_period' => '1184–1213', 'location' => 'Kingdom of Georgia'],
            ['title' => 'Mongol Invasions of Georgia', 'description' => 'Mongol armies entered the Caucasus in the thirteenth century and eventually imposed imperial rule and tribute on the Georgian kingdom.', 'date_or_period' => '1220s–1240s', 'location' => 'Kingdom of Georgia and the South Caucasus'],
            ['title' => 'Restoration under George V the Brilliant', 'description' => 'George V restored effective royal authority, ended direct Mongol domination, and reunited much of the Georgian kingdom.', 'date_or_period' => '1318–1346', 'location' => 'Kingdom of Georgia'],
            ['title' => 'Timurid Invasions of Georgia', 'description' => 'Timur led repeated campaigns that devastated Georgian towns, monasteries, agriculture, and royal resources.', 'date_or_period' => '1386–1403', 'location' => 'Kingdom of Georgia'],
            ['title' => 'Fragmentation of the Georgian Kingdom', 'description' => 'The united monarchy broke into rival kingdoms and principalities after prolonged war, dynastic competition, and regional pressure.', 'date_or_period' => '1490–1493', 'location' => 'Kartli, Kakheti, Imereti, and Georgian principalities'],
            ['title' => 'Peace of Amasya and the Division of Georgia', 'description' => 'The Ottoman and Safavid empires formalized spheres of influence that divided Georgian lands between competing imperial systems.', 'date_or_period' => '1555', 'location' => 'Western and eastern Georgian kingdoms'],
            ['title' => 'Treaty of Georgievsk', 'description' => 'The Kingdom of Kartli-Kakheti entered a treaty with the Russian Empire that placed the eastern Georgian kingdom under Russian protection while promising the continuation of its Bagrationi monarchy.', 'date_or_period' => '24 July 1783', 'location' => 'Georgievsk'],
            ['title' => 'Battle of Krtsanisi', 'description' => 'The army of Agha Mohammad Khan Qajar defeated Georgian forces and devastated Tbilisi after Russia failed to provide promised protection.', 'date_or_period' => '8–11 September 1795', 'location' => 'Krtsanisi and Tbilisi'],
            ['title' => 'Russian Annexation of Kartli-Kakheti', 'description' => 'The Russian Empire abolished the Georgian monarchy and annexed the eastern Georgian kingdom, beginning a new and difficult political era.', 'date_or_period' => '1801', 'location' => 'Kartli-Kakheti'],
            ['title' => 'Georgian National Revival and Ilia Chavchavadze', 'description' => 'Writers, educators, publishers, and reformers led a cultural and national revival under Russian imperial rule.', 'date_or_period' => '1860s–1907', 'location' => 'Tbilisi and Georgian cultural centers'],
            ['title' => 'First Democratic Republic of Georgia', 'description' => 'Georgia declared independence and established a democratic republic with a multiparty parliament and a progressive constitution.', 'date_or_period' => '26 May 1918–25 February 1921', 'location' => 'Tbilisi, Georgia'],
            ['title' => 'Soviet Invasion and Occupation of Georgia', 'description' => 'The Red Army invaded the Democratic Republic, captured Tbilisi, and installed a Bolshevik government.', 'date_or_period' => 'February–March 1921', 'location' => 'Georgia'],
            ['title' => 'August Uprising against Soviet Rule', 'description' => 'A coordinated anti-Soviet rebellion sought to restore Georgian independence but was defeated and followed by mass repression.', 'date_or_period' => 'August–September 1924', 'location' => 'Multiple regions of Georgia'],
            ['title' => 'April 9 Tragedy', 'description' => 'Soviet troops violently dispersed a peaceful pro-independence demonstration in Tbilisi, killing civilians and accelerating the independence movement.', 'date_or_period' => '9 April 1989', 'location' => 'Rustaveli Avenue, Tbilisi'],
            ['title' => 'Independence Referendum and Restoration', 'description' => 'On 31 March 1991, voters overwhelmingly supported restoring Georgia’s independence. The Supreme Council adopted the Act of Restoration of State Independence on 9 April 1991.', 'date_or_period' => '31 March–9 April 1991', 'location' => 'Georgia'],
            ['title' => 'Rose Revolution', 'description' => 'Large peaceful protests following disputed elections led to the resignation of President Eduard Shevardnadze and a transfer of power.', 'date_or_period' => 'November 2003', 'location' => 'Tbilisi and cities across Georgia'],
            ['title' => 'Russo-Georgian War', 'description' => 'War broke out in August 2008 between Georgia, Russia, and Russian-backed separatist forces, producing deaths, displacement, and continuing territorial consequences.', 'date_or_period' => 'August 2008', 'location' => 'South Ossetia, Abkhazia, and other areas of Georgia'],
        ];

        $eventImages = [
            'Colchis in Western Georgia' => 'events/colchis-western-georgia.png',
            'Rise of the Kingdom of Iberia' => 'events/kingdom-iberia.png',
            'Christianization of the Kingdom of Iberia' => 'events/christianization-iberia.jpg',
            'Reign of Vakhtang Gorgasali' => 'events/vakhtang-gorgasali.jpg',
            'Lazic War in Egrisi' => 'events/lazic-war.png',
            'Principality of Iberia' => 'events/principality-iberia.png',
            'First Arab Invasions of Georgia' => 'events/arab-invasions.png',
            'Establishment of the Emirate of Tbilisi' => 'events/emirate-tbilisi.png',
            'Formation of the Kingdom of Abkhazia' => 'events/kingdom-abkhazia.png',
            'Ashot I and the Revival of Tao-Klarjeti' => 'events/ashot-tao-klarjeti.jpg',
            'Restoration of Georgian Kingship' => 'events/restoration-kingship.jpg',
            'Unification of the Kingdom of Georgia' => 'events/unification-georgia.jpg',
            'The Great Turkish Invasion' => 'events/great-turkish-invasion.png',
            'Accession of David IV the Builder' => 'events/david-accession.jpg',
            'Council of Ruisi-Urbnisi' => 'events/ruisi-urbnisi.jpg',
            'Battle of Ertsukhi' => 'events/battle-ertsukhi.jpg',
            'Battle of Didgori' => 'events/battle-didgori.jpg',
            'Liberation of Tbilisi' => 'events/liberation-tbilisi.jpg',
            'Legacy of David the Builder' => 'events/david-legacy.jpg',
            'Golden Age of Georgia' => 'events/golden-age.jpg',
            'Reign of King Tamar' => 'events/reign-tamar.jpg',
            'Mongol Invasions of Georgia' => 'events/mongol-invasions.png',
            'Restoration under George V the Brilliant' => 'events/george-v-restoration.jpg',
            'Timurid Invasions of Georgia' => 'events/timurid-invasions.png',
            'Fragmentation of the Georgian Kingdom' => 'events/fragmentation-georgia.png',
            'Peace of Amasya and the Division of Georgia' => 'events/peace-amasya.jpg',
            'Treaty of Georgievsk' => 'events/treaty-georgievsk.jpg',
            'Battle of Krtsanisi' => 'events/battle-krtsanisi.jpg',
            'Russian Annexation of Kartli-Kakheti' => 'events/russian-annexation.jpg',
            'Georgian National Revival and Ilia Chavchavadze' => 'events/national-revival.jpg',
            'First Democratic Republic of Georgia' => 'events/first-democratic-republic.png',
            'Soviet Invasion and Occupation of Georgia' => 'events/soviet-invasion.png',
            'August Uprising against Soviet Rule' => 'events/august-uprising.png',
            'April 9 Tragedy' => 'events/april-9-tragedy.png',
            'Independence Referendum and Restoration' => 'events/independence-restoration.png',
            'Rose Revolution' => 'events/rose-revolution.jpg',
            'Russo-Georgian War' => 'events/russo-georgian-war.jpg',
        ];
        $eventSortYears = [
            'Colchis in Western Georgia' => -1200,
            'Rise of the Kingdom of Iberia' => -300,
            'Christianization of the Kingdom of Iberia' => 326,
            'Reign of Vakhtang Gorgasali' => 447,
            'Lazic War in Egrisi' => 541,
            'Principality of Iberia' => 580,
            'First Arab Invasions of Georgia' => 642,
            'Establishment of the Emirate of Tbilisi' => 736,
            'Formation of the Kingdom of Abkhazia' => 780,
            'Ashot I and the Revival of Tao-Klarjeti' => 813,
            'Restoration of Georgian Kingship' => 888,
            'Unification of the Kingdom of Georgia' => 1008,
            'The Great Turkish Invasion' => 1064,
            'Accession of David IV the Builder' => 1089,
            'Council of Ruisi-Urbnisi' => 1103,
            'Battle of Ertsukhi' => 1104,
            'Battle of Didgori' => 1121,
            'Liberation of Tbilisi' => 1122,
            'Legacy of David the Builder' => 1125,
            'Golden Age of Georgia' => 1126,
            'Reign of King Tamar' => 1184,
            'Mongol Invasions of Georgia' => 1220,
            'Restoration under George V the Brilliant' => 1318,
            'Timurid Invasions of Georgia' => 1386,
            'Fragmentation of the Georgian Kingdom' => 1490,
            'Peace of Amasya and the Division of Georgia' => 1555,
            'Treaty of Georgievsk' => 1783,
            'Battle of Krtsanisi' => 1795,
            'Russian Annexation of Kartli-Kakheti' => 1801,
            'Georgian National Revival and Ilia Chavchavadze' => 1860,
            'First Democratic Republic of Georgia' => 1918,
            'Soviet Invasion and Occupation of Georgia' => 1921,
            'August Uprising against Soviet Rule' => 1924,
            'April 9 Tragedy' => 1989,
            'Independence Referendum and Restoration' => 1991,
            'Rose Revolution' => 2003,
            'Russo-Georgian War' => 2008,
        ];
        $expandedEventDescriptions = require database_path('seeders/data/historical_event_descriptions.php');
        $buildLongFormDescription = require database_path('seeders/data/build_long_form_description.php');

        foreach ($events as $event) {
            $event['description'] = $buildLongFormDescription(
                $event['title'],
                $expandedEventDescriptions[$event['title']],
                'event',
                $event['date_or_period'],
                $event['location'],
            );
            $event['sort_year'] = $eventSortYears[$event['title']];
            $event['image'] = $eventImages[$event['title']];
            HistoricalEvent::updateOrCreate(['title' => $event['title']], $event);
        }

        $artifacts = [
            ['title' => 'Vani Colchis Coin', 'description' => 'A silver coin associated with the ancient kingdom of Colchis, reflecting the region’s trade networks and sophisticated metalwork.', 'period' => '5th–3rd century BCE', 'location' => 'Vani, Imereti', 'category' => 'Coin', 'tags' => ['Colchis', 'Imereti', 'Ancient']],
            ['title' => 'Gelati Gospel Manuscript', 'description' => 'A richly decorated manuscript representing the religious scholarship and book arts cultivated in medieval Georgian monasteries.', 'period' => '12th century', 'location' => 'Gelati Monastery', 'category' => 'Manuscript', 'tags' => ['Medieval', 'Religion', 'Golden Age', 'Writing']],
            ['title' => 'Svan Defensive Tower', 'description' => 'A traditional stone tower built to protect families and communities in the high mountain settlements of Svaneti.', 'period' => '9th–12th centuries', 'location' => 'Upper Svaneti', 'category' => 'Architecture', 'tags' => ['Medieval', 'Culture']],
            ['title' => 'King Tamar Period Cross', 'description' => 'A devotional cross illustrating the craftsmanship and Christian visual culture of Georgia during the reign of King Tamar.', 'period' => 'Late 12th century', 'location' => 'Mtskheta', 'category' => 'Church', 'tags' => ['King Tamar', 'Religion', 'Golden Age']],
            ['title' => 'Caucasian Shashka', 'description' => 'A single-edged Caucasian sabre (shashka) with a guardless hilt and decorated scabbard, representing the cavalry sidearm widely carried across Georgia and the wider Caucasus.', 'period' => '19th century', 'location' => 'Caucasus', 'category' => 'Weapon', 'tags' => ['War', 'Culture']],
            ['title' => 'Ananuri Church Icon', 'description' => 'A painted icon from the Ananuri complex that combines religious symbolism with the regional style of Georgian sacred art.', 'period' => '17th century', 'location' => 'Ananuri', 'category' => 'Painting', 'tags' => ['Religion', 'Culture']],
            ['title' => 'Trialeti Gold Cup', 'description' => 'A ceremonial gold vessel discovered in a burial mound, demonstrating the technical skill and ritual life of Bronze Age communities.', 'period' => 'Middle Bronze Age', 'location' => 'Trialeti', 'category' => 'Archaeological Object', 'tags' => ['Ancient', 'Culture']],
            ['title' => 'Didgori-period Battle Axe', 'description' => 'A representative medieval battle axe illustrating the military material culture of the period surrounding the Battle of Didgori.', 'period' => 'Early 12th century', 'location' => 'Georgia', 'category' => 'Weapon', 'tags' => ['Medieval', 'War', 'Golden Age']],
            ['title' => 'Dmanisi Hominin Skull', 'description' => 'One of the exceptionally preserved early Homo fossils recovered at Dmanisi. The site’s fossils and stone tools, dated to roughly 1.85–1.77 million years ago, are central to the study of early hominin movement beyond Africa.', 'period' => 'Early Pleistocene', 'location' => 'Dmanisi, Kvemo Kartli', 'category' => 'Archaeological Object', 'tags' => ['Prehistoric', 'Culture']],
            ['title' => 'Bolnisi Sioni Inscription', 'description' => 'An Old Georgian inscription written in the Asomtavruli script on Bolnisi Sioni Cathedral. Dated to 493/494, it is among the earliest securely dated monuments of Georgian writing.', 'period' => '493–494', 'location' => 'Bolnisi Sioni Cathedral', 'category' => 'Architecture', 'tags' => ['Ancient', 'Religion', 'Writing']],
            ['title' => 'Khakhuli Triptych', 'description' => 'A monumental medieval icon of the Theotokos assembled with more than one hundred Georgian and Byzantine cloisonné enamels dating from the eighth through twelfth centuries.', 'period' => '8th–12th centuries', 'location' => 'Art Museum of Georgia, Tbilisi', 'category' => 'Church', 'tags' => ['Medieval', 'Religion', 'Culture', 'Golden Age']],
            ['title' => 'The Knight in the Panther’s Skin Manuscript', 'description' => 'A manuscript tradition of Shota Rustaveli’s celebrated Georgian epic, composed in the twelfth or thirteenth century and regarded as a defining literary work of Georgia’s Golden Age.', 'period' => '12th–13th centuries', 'location' => 'Georgia', 'category' => 'Manuscript', 'tags' => ['Medieval', 'Writing', 'Culture', 'Golden Age']],
            ['title' => 'Jvari Monastery', 'description' => 'A sixth-century Georgian Orthodox monastery above Mtskheta. Its compact tetraconch design became highly influential in medieval Georgian and Armenian church architecture.', 'period' => '6th century', 'location' => 'Mtskheta', 'category' => 'Architecture', 'tags' => ['Medieval', 'Religion', 'Culture']],
            ['title' => 'Vani Bronze Figurine', 'description' => 'A finely cast bronze figurine from the ritual and urban landscape of ancient Vani, illustrating the skill of Colchian metalworkers and their connections across the Black Sea world.', 'period' => '3rd–2nd century BCE', 'location' => 'Vani, Imereti', 'category' => 'Archaeological Object', 'tags' => ['Ancient', 'Colchis', 'Imereti', 'Culture']],
            ['title' => 'Armazi Bilingual Stele', 'description' => 'A funerary inscription written in Greek and Armazic, the local form of Aramaic, preserving rare evidence for the multilingual royal society of ancient Iberia.', 'period' => '2nd century CE', 'location' => 'Armaziskhevi, Mtskheta', 'category' => 'Archaeological Object', 'tags' => ['Ancient', 'Writing', 'Culture']],
            ['title' => 'Kura-Araxes Ceramic Vessel', 'description' => 'A hand-built ceramic vessel representing the distinctive black-and-red pottery tradition associated with Early Bronze Age communities across Georgia and the South Caucasus.', 'period' => 'Early Bronze Age', 'location' => 'Eastern Georgia', 'category' => 'Archaeological Object', 'tags' => ['Prehistoric', 'Culture']],
            ['title' => 'Narikala Fortress', 'description' => 'The historic citadel overlooking Tbilisi preserves layers of construction connected with Iberian, Persian, Arab, Georgian, and later rulers of the city.', 'period' => '4th–18th centuries', 'location' => 'Tbilisi', 'category' => 'Architecture', 'tags' => ['Ancient', 'Medieval', 'War', 'Culture']],
            ['title' => 'Uplistsikhe Cave City', 'description' => 'A rock-cut settlement above the Mtkvari whose halls, streets, ritual spaces, dwellings, and medieval church document more than two thousand years of occupation.', 'period' => '1st millennium BCE–Middle Ages', 'location' => 'Shida Kartli', 'category' => 'Architecture', 'tags' => ['Ancient', 'Medieval', 'Culture']],
            ['title' => 'Dmanisi Medieval Citadel', 'description' => 'The fortified medieval town at Dmanisi controlled an important trade route and preserves walls, gates, churches, baths, workshops, and houses above the famous prehistoric deposits.', 'period' => '9th–14th centuries', 'location' => 'Dmanisi, Kvemo Kartli', 'category' => 'Architecture', 'tags' => ['Medieval', 'War', 'Culture']],
            ['title' => 'Svetitskhoveli Cathedral', 'description' => 'Georgia’s principal medieval cathedral stands at the spiritual center of Mtskheta and combines royal memory, sacred tradition, monumental stonework, and centuries of rebuilding.', 'period' => '11th century', 'location' => 'Mtskheta', 'category' => 'Church', 'tags' => ['Medieval', 'Religion', 'Culture']],
            ['title' => 'Gelati Monastery', 'description' => 'Founded by David the Builder, Gelati joined a royal monastery, academy, manuscript center, mosaics, frescoes, and dynastic burial place in one of medieval Georgia’s greatest ensembles.', 'period' => 'Founded 1106', 'location' => 'Near Kutaisi, Imereti', 'category' => 'Church', 'tags' => ['Medieval', 'Religion', 'Golden Age', 'Imereti']],
            ['title' => 'Alaverdi Cathedral', 'description' => 'The soaring cathedral of Alaverdi dominates the Alazani Valley and represents the architectural ambition and ecclesiastical importance of medieval Kakheti.', 'period' => 'Early 11th century', 'location' => 'Kakheti', 'category' => 'Church', 'tags' => ['Medieval', 'Religion', 'Culture']],
        ];

        $artifactImages = [
            'Vani Colchis Coin' => 'artifacts/vani-colchis-coin.png',
            'Gelati Gospel Manuscript' => 'artifacts/gelati-gospel-manuscript.jpg',
            'Svan Defensive Tower' => 'artifacts/svan-defensive-tower.png',
            'King Tamar Period Cross' => 'artifacts/king-tamar-period-cross.jpg',
            'Caucasian Shashka' => 'artifacts/caucasian-shashka.jpg',
            'Ananuri Church Icon' => 'artifacts/ananuri-church-icon.jpg',
            'Trialeti Gold Cup' => 'artifacts/trialeti-gold-cup.jpg',
            'Didgori-period Battle Axe' => 'artifacts/didgori-period-battle-axe.jpg',
            'Dmanisi Hominin Skull' => 'artifacts/dmanisi-hominin-skull.jpg',
            'Bolnisi Sioni Inscription' => 'artifacts/bolnisi-sioni-inscription.jpg',
            'Khakhuli Triptych' => 'artifacts/khakhuli-triptych.jpg',
            'The Knight in the Panther’s Skin Manuscript' => 'artifacts/knight-panthers-skin-manuscript.jpg',
            'Jvari Monastery' => 'artifacts/jvari-monastery.jpg',
            'Vani Bronze Figurine' => 'artifacts/vani-bronze-torso.jpg',
            'Armazi Bilingual Stele' => 'artifacts/armazi-bilingual-stele.jpg',
            'Kura-Araxes Ceramic Vessel' => 'artifacts/kura-araxes-vessel.jpg',
            'Narikala Fortress' => 'artifacts/narikala-fortress.jpg',
            'Uplistsikhe Cave City' => 'artifacts/uplistsikhe-cave-city.jpg',
            'Dmanisi Medieval Citadel' => 'artifacts/dmanisi-medieval-citadel.jpg',
            'Svetitskhoveli Cathedral' => 'artifacts/svetitskhoveli-cathedral.jpg',
            'Gelati Monastery' => 'artifacts/gelati-monastery.jpg',
            'Alaverdi Cathedral' => 'artifacts/alaverdi-cathedral.jpg',
        ];

        $expandedArtifactDescriptions = require database_path('seeders/data/artifact_descriptions.php');

        Artifact::where('title', 'Vani Bronze Torso')->update(['title' => 'Vani Bronze Figurine']);
        Artifact::where('title', 'Khevsurian Sword')->update(['title' => 'Caucasian Shashka']);
        $categories = Category::pluck('id', 'name');
        $tags = Tag::pluck('id', 'name');
        foreach ($artifacts as $index => $data) {
            $artifactTags = $data['tags'];
            $categoryName = $data['category'];
            unset($data['tags'], $data['category']);
            $data['description'] = $buildLongFormDescription(
                $data['title'],
                $expandedArtifactDescriptions[$data['title']],
                'artifact',
                $data['period'],
                $data['location'],
                $categoryName,
            );
            $data['category_id'] = $categories[$categoryName];
            $data['user_id'] = $index === 6 ? $user->id : $admin->id;
            $data['image'] = $artifactImages[$data['title']];
            $artifact = Artifact::updateOrCreate(['title' => $data['title']], $data);
            $artifact->tags()->sync(collect($artifactTags)->map(fn ($name) => $tags[$name])->all());
        }

        $connections = [
            'Vani Colchis Coin' => ['Colchis in Western Georgia'],
            'Vani Bronze Figurine' => ['Colchis in Western Georgia'],
            'Armazi Bilingual Stele' => ['Rise of the Kingdom of Iberia'],
            'Bolnisi Sioni Inscription' => ['Christianization of the Kingdom of Iberia'],
            'Jvari Monastery' => ['Christianization of the Kingdom of Iberia', 'Principality of Iberia'],
            'Narikala Fortress' => ['Reign of Vakhtang Gorgasali', 'Establishment of the Emirate of Tbilisi', 'Liberation of Tbilisi'],
            'Didgori-period Battle Axe' => ['Battle of Didgori'],
            'Gelati Monastery' => ['Accession of David IV the Builder', 'Legacy of David the Builder', 'Golden Age of Georgia'],
            'Gelati Gospel Manuscript' => ['Golden Age of Georgia'],
            'King Tamar Period Cross' => ['Reign of King Tamar'],
            'Khakhuli Triptych' => ['Golden Age of Georgia', 'Reign of King Tamar'],
            'The Knight in the Panther’s Skin Manuscript' => ['Golden Age of Georgia', 'Reign of King Tamar'],
        ];
        $eventIds = HistoricalEvent::pluck('id', 'title');
        foreach ($connections as $artifactTitle => $eventTitles) {
            Artifact::where('title', $artifactTitle)->firstOrFail()->historicalEvents()->sync(
                collect($eventTitles)->map(fn (string $title): int => $eventIds[$title])->all()
            );
        }
    }
}
