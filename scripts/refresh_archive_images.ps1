param(
    [string]$ProjectRoot = (Split-Path $PSScriptRoot -Parent),
    [string]$StartAt = '',
    [string]$Only = '',
    [switch]$ManifestOnly
)

$ErrorActionPreference = 'Stop'
Add-Type -AssemblyName System.Drawing
$headers = @{'User-Agent' = 'GeoArchive educational Laravel project (image attribution audit)'}
$usedSources = [System.Collections.Generic.HashSet[string]]::new([System.StringComparer]::OrdinalIgnoreCase)
$rejectedSources = [System.Collections.Generic.HashSet[string]]::new([System.StringComparer]::OrdinalIgnoreCase)

$records = @(
    # Categories
    @{Group='categories';File='archaeological-object.jpg';Query='Vani archaeology Georgia excavation artifacts'},
    @{Group='categories';File='architecture.jpg';Query='Georgian architecture monastery fortress'},
    @{Group='categories';File='church.jpg';FileTitle='File:Gelati interior (1).jpg';Query='Gelati Monastery church interior with Georgian Orthodox frescoes'},
    @{Group='categories';File='coin.png';FileTitle='File:Colchis-coins.jpg';Query='Colchian coin collection'},
    @{Group='categories';File='manuscript.jpg';Query='Georgian illuminated manuscript gospel'},
    @{Group='categories';File='painting.jpg';Query='Georgian medieval fresco painting'},
    @{Group='categories';File='weapon.jpg';Article='Khanjali';Query='Caucasian Georgian traditional weapon';Fit='contain'},

    # Artifacts and monuments
    @{Group='artifacts';File='vani-colchis-coin.png';FileTitle='File:Colchis coin.jpg';Query='Colchian silver coin'},
    @{Group='artifacts';File='gelati-gospel-manuscript.jpg';Query='Gelati Gospel Georgian manuscript'},
    @{Group='artifacts';File='svan-defensive-tower.png';Article='Svan towers';Query='Svan towers Mestia Georgia'},
    @{Group='artifacts';File='king-tamar-period-cross.jpg';Query='Georgian medieval processional cross Queen Tamar'},
    @{Group='artifacts';File='caucasian-shashka.jpg';FileTitle='File:Sabel, schaschka - Livrustkammaren - 1315 (cropped).tif';Query='Caucasian shashka sword';Fit='contain'},
    @{Group='artifacts';File='ananuri-church-icon.jpg';Query='Ananuri church icon fresco Georgia'},
    @{Group='artifacts';File='trialeti-gold-cup.jpg';Article='Trialeti-Vanadzor culture';Query='Trialeti gold goblet Georgia'},
    @{Group='artifacts';File='didgori-period-battle-axe.jpg';FileTitle='File:Decorated battleaxe.JPG';Query='decorated medieval battle axe museum';Fit='contain'},
    @{Group='artifacts';File='dmanisi-hominin-skull.jpg';Article='Dmanisi hominins';Query='Dmanisi hominin skull'},
    @{Group='artifacts';File='bolnisi-sioni-inscription.jpg';Query='Bolnisi Sioni inscription Georgian'},
    @{Group='artifacts';File='khakhuli-triptych.jpg';Article='Khakhuli triptych';Query='Khakhuli triptych enamel'},
    @{Group='artifacts';File='knight-panthers-skin-manuscript.jpg';Article="The Knight in the Panther's Skin";Query='Knight Panther Skin Georgian manuscript'},
    @{Group='artifacts';File='jvari-monastery.jpg';Article='Jvari Monastery';Query='Jvari monastery Mtskheta'},
    @{Group='artifacts';File='vani-bronze-torso.jpg';FileTitle='File:Archeological Artifact at Vani Archeological Museum.jpg';Query='Vani bronze figurine archaeology'},
    @{Group='artifacts';File='armazi-bilingual-stele.jpg';Query='Armazi bilingual inscription Serapitis'},
    @{Group='artifacts';File='kura-araxes-vessel.jpg';Query='Kura Araxes pottery vessel'},
    @{Group='artifacts';File='narikala-fortress.jpg';Article='Narikala';Query='Narikala fortress Tbilisi'},
    @{Group='artifacts';File='uplistsikhe-cave-city.jpg';Article='Uplistsikhe';Query='Uplistsikhe cave city Georgia'},
    @{Group='artifacts';File='dmanisi-medieval-citadel.jpg';Article='Dmanisi Sioni cathedral';Query='Dmanisi medieval citadel Sioni church'},
    @{Group='artifacts';File='svetitskhoveli-cathedral.jpg';Article='Svetitskhoveli Cathedral';Query='Svetitskhoveli cathedral exterior'},
    @{Group='artifacts';File='gelati-monastery.jpg';Article='Gelati Monastery';Query='Gelati monastery exterior Georgia'},
    @{Group='artifacts';File='alaverdi-cathedral.jpg';Article='Alaverdi Monastery';Query='Alaverdi cathedral Kakheti'},

    # Historical events
    @{Group='events';File='colchis-western-georgia.png';Article='Colchis';Query='ancient Colchis map Georgia'},
    @{Group='events';File='kingdom-iberia.png';FileTitle='File:Map of Kingdom of Iberia, III c. BC.svg';Query='Map of the Kingdom of Iberia (Kartli), 3rd century BC'},
    @{Group='events';File='christianization-iberia.jpg';Article='Christianization of the Kingdom of Iberia';Query='Saint Nino Christianization Georgia'},
    @{Group='events';File='vakhtang-gorgasali.jpg';Article='Vakhtang I';Query='Vakhtang Gorgasali portrait'},
    @{Group='events';File='lazic-war.png';Article='Petra, Lazica';Query='Lazic War Petra fortress Georgia'},
    @{Group='events';File='principality-iberia.png';Query='Principality of Iberia map Georgia 7th century'},
    @{Group='events';File='arab-invasions.png';FileTitle='File:Map of the Caucasus, 740 CE.svg';Query='Arab rule in Georgia Caucasus map 740'},
    @{Group='events';File='emirate-tbilisi.png';Article='Emirate of Tbilisi';Query='Emirate of Tbilisi map'},
    @{Group='events';File='kingdom-abkhazia.png';Article='Kingdom of Abkhazia';Query='Kingdom of Abkhazia map'},
    @{Group='events';File='ashot-tao-klarjeti.jpg';Article='Ashot I of Iberia';Query='Ashot I Tao Klarjeti relief'},
    @{Group='events';File='restoration-kingship.jpg';Article='Oshki';Query='Oshki monastery Tao Klarjeti Georgian kingship'},
    @{Group='events';File='unification-georgia.jpg';Article='Bagrat III of Georgia';Query='Bagrat III Georgia Bedia fresco'},
    @{Group='events';File='great-turkish-invasion.png';Article='Great Turkish Invasion';Query='Seljuk invasion Georgia map'},
    @{Group='events';File='david-accession.jpg';Article='David IV';Query='David IV Georgia icon'},
    @{Group='events';File='ruisi-urbnisi.jpg';Query='Ruisi church Georgia Urbnisi cathedral'},
    @{Group='events';File='battle-ertsukhi.jpg';Article='Kakheti';Query='Ertsukhi Kakheti landscape Georgia'},
    @{Group='events';File='battle-didgori.jpg';Query='Didgori memorial monument Georgia battle'},
    @{Group='events';File='liberation-tbilisi.jpg';Article='Tbilisi';Query='medieval Tbilisi Metekhi Narikala old illustration'},
    @{Group='events';File='david-legacy.jpg';Article='Georgian Chronicles';Query='David the Builder Georgian Chronicles manuscript'},
    @{Group='events';File='golden-age.jpg';Article='Vardzia';Query='Georgian Golden Age Vardzia monastery'},
    @{Group='events';File='reign-tamar.jpg';Article='Tamar I';Query='Queen Tamar Vardzia fresco'},
    @{Group='events';File='mongol-invasions.png';Article='Mongol invasions of Georgia';Query='Mongol invasion Georgia manuscript'},
    @{Group='events';File='george-v-restoration.jpg';Article='George V of Georgia';Query='George V Brilliant Georgia portrait'},
    @{Group='events';File='timurid-invasions.png';Article='Timurid invasions of Georgia';Query='Timur siege Georgian castle miniature'},
    @{Group='events';File='fragmentation-georgia.png';Article='Kingdom of Kartli';Query='Georgian kingdoms after fragmentation map'},
    @{Group='events';File='peace-amasya.jpg';Article='Peace of Amasya';Query='Peace of Amasya Ottoman Safavid map'},
    @{Group='events';File='treaty-georgievsk.jpg';Article='Heraclius II of Georgia';Query='Treaty Georgievsk Erekle II portrait';Focus='top'},
    @{Group='events';File='battle-krtsanisi.jpg';Article='Battle of Krtsanisi';Query='Battle Krtsanisi painting'},
    @{Group='events';File='russian-annexation.jpg';Article='George XII of Georgia';Query='Russian annexation Georgia 1801 Tiflis engraving'},
    @{Group='events';File='national-revival.jpg';Article='Ilia Chavchavadze';Query='Ilia Chavchavadze portrait'},
    @{Group='events';File='first-democratic-republic.png';Article='Democratic Republic of Georgia';Query='Democratic Republic Georgia 1918 government'},
    @{Group='events';File='soviet-invasion.png';Article='Soviet invasion of Georgia';Query='Red Army Tbilisi 1921'},
    @{Group='events';File='august-uprising.png';Article='Kakutsa Cholokashvili';Query='Kakutsa Cholokashvili August uprising Georgia 1924';Focus='top'},
    @{Group='events';File='april-9-tragedy.png';Article='Rustaveli Avenue';Query='April 9 tragedy memorial Rustaveli Avenue Tbilisi'},
    @{Group='events';File='independence-restoration.png';Article='1991 Georgian independence referendum';Query='Georgia independence declaration 1991 referendum'},
    @{Group='events';File='rose-revolution.jpg';Article='Rose Revolution';Query='Rose Revolution Tbilisi 2003'},
    @{Group='events';File='russo-georgian-war.jpg';Article='Russo-Georgian War';Query='Russo Georgian War Gori 2008'}
)

function Invoke-WikiJson([string]$uri) {
    for ($attempt = 1; $attempt -le 6; $attempt++) {
        try { return Invoke-RestMethod -Uri $uri -Headers $headers -TimeoutSec 30 }
        catch {
            if ($attempt -eq 6) { throw }
            Start-Sleep -Seconds ([Math]::Min(90, $attempt * 15))
        }
    }
}

$pageImageCache = @{}
$articleTitles = @($records | ForEach-Object { $_.Article } | Where-Object { $_ } | Select-Object -Unique)
if ($articleTitles.Count) {
    $batchUri = 'https://en.wikipedia.org/w/api.php?action=query&prop=pageimages&piprop=thumbnail|name&pithumbsize=1280&redirects=1&format=json&titles=' + [uri]::EscapeDataString(($articleTitles -join '|'))
    $batch = Invoke-WikiJson $batchUri
    foreach ($page in $batch.query.pages.PSObject.Properties.Value) { $pageImageCache[$page.title] = $page }
    $aliases = @{}
    foreach ($entry in @($batch.query.normalized)) { if ($entry.from) { $aliases[$entry.from] = $entry.to } }
    foreach ($entry in @($batch.query.redirects)) { if ($entry.from) { $aliases[$entry.from] = $entry.to } }
    foreach ($alias in $aliases.Keys) {
        $target = $aliases[$alias]
        while ($aliases.ContainsKey($target)) { $target = $aliases[$target] }
        if ($pageImageCache.ContainsKey($target)) { $pageImageCache[$alias] = $pageImageCache[$target] }
    }
}

function Get-PageImage([string]$article) {
    if (-not $article) { return $null }
    $page = $pageImageCache[$article]
    if (-not $page) { return $null }
    if (-not $page.thumbnail.source -or -not $page.pageimage -or $usedSources.Contains($page.pageimage) -or $rejectedSources.Contains(('File:' + $page.pageimage))) { return $null }
    return @{Title=('File:' + $page.pageimage); Url=$page.thumbnail.source; Article=$page.title}
}

function Get-CommonsFile([string]$title) {
    if (-not $title -or $usedSources.Contains($title) -or $rejectedSources.Contains($title)) { return $null }
    $uri = 'https://commons.wikimedia.org/w/api.php?action=query&prop=imageinfo&iiprop=url&format=json&titles=' + [uri]::EscapeDataString($title)
    $data = Invoke-WikiJson $uri
    $page = @($data.query.pages.PSObject.Properties.Value)[0]
    $url = $page.imageinfo[0].url
    if (-not $url) { return $null }
    return @{Title=$page.title; Url=$url; Article=$null}
}

function Search-Commons([string]$query) {
    $words = @($query -split '\s+' | Where-Object { $_ })
    $queries = @($query)
    if ($words.Count -gt 3) { $queries += ($words[0..2] -join ' ') }
    if ($words.Count -gt 2) { $queries += ($words[0..1] -join ' ') }
    foreach ($search in ($queries | Select-Object -Unique)) {
        $uri = 'https://commons.wikimedia.org/w/api.php?action=query&generator=search&gsrnamespace=6&gsrlimit=10&gsrsearch=' + [uri]::EscapeDataString($search) + '&prop=imageinfo&iiprop=url&iiurlwidth=1280&format=json'
        $data = Invoke-WikiJson $uri
        Start-Sleep -Seconds 4
        $pages = @($data.query.pages.PSObject.Properties.Value | Sort-Object index)
        foreach ($page in $pages) {
            $url = $page.imageinfo[0].thumburl
            if (-not $url) { $url = $page.imageinfo[0].url }
            if ($url -and -not $usedSources.Contains($page.title) -and -not $rejectedSources.Contains($page.title) -and $page.title -notmatch '\.(pdf|djvu)$') {
                return @{Title=$page.title; Url=$url; Article=$null}
            }
        }
    }
    return $null
}

function Save-CoverImage([string]$title, [string]$url, [string]$destination, [string]$focus = 'center', [string]$fit = 'cover') {
    $temp = [System.IO.Path]::GetTempFileName()
    try {
        # Wikimedia may temporarily throttle bulk educational downloads. The
        # proxy follows Commons' canonical file redirect at a standard width.
        $fileName = $title -replace '^File:', ''
        $commonsRedirect = 'commons.wikimedia.org/wiki/Special:Redirect/file/' + $fileName
        $redirectProxy = 'https://images.weserv.nl/?url=' + [uri]::EscapeDataString($commonsRedirect) + '&w=1280&output=jpg'
        $sourceProxy = 'https://images.weserv.nl/?url=' + [uri]::EscapeDataString([uri]::UnescapeDataString($url)) + '&w=1280&output=jpg'
        for ($attempt = 1; $attempt -le 5; $attempt++) {
            $downloadUrl = if ($attempt % 2) { $redirectProxy } else { $sourceProxy }
            try { Invoke-WebRequest -Uri $downloadUrl -Headers $headers -OutFile $temp -TimeoutSec 60; break }
            catch { if ($attempt -eq 5) { throw }; Start-Sleep -Seconds ($attempt * 4) }
        }
        $source = [System.Drawing.Image]::FromFile($temp)
        try {
            $width = 1400; $height = 900
            $scale = if ($fit -eq 'contain') {
                [Math]::Min($width / $source.Width, $height / $source.Height)
            } else {
                [Math]::Max($width / $source.Width, $height / $source.Height)
            }
            $drawWidth = [int]($source.Width * $scale); $drawHeight = [int]($source.Height * $scale)
            $x = [int](($width - $drawWidth) / 2); $y = [int](($height - $drawHeight) / 2)
            if ($focus -eq 'top' -and $drawHeight -gt $height) { $y = 0 }
            $canvas = New-Object System.Drawing.Bitmap $width, $height
            $graphics = [System.Drawing.Graphics]::FromImage($canvas)
            try {
                $graphics.Clear([System.Drawing.Color]::FromArgb(244, 240, 232))
                $graphics.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
                $graphics.DrawImage($source, $x, $y, $drawWidth, $drawHeight)
                $canvas.Save($destination, [System.Drawing.Imaging.ImageFormat]::Jpeg)
            } finally { $graphics.Dispose(); $canvas.Dispose() }
        } finally { $source.Dispose() }
    } finally { Remove-Item -LiteralPath $temp -Force -ErrorAction SilentlyContinue }
}

$credits = @()
$started = -not $StartAt
$onlyRecords = @($Only -split ',' | ForEach-Object { $_.Trim() } | Where-Object { $_ })
foreach ($record in $records) {
    $recordPath = "$($record.Group)/$($record.File)"
    if ($onlyRecords.Count -and $recordPath -notin $onlyRecords) { continue }
    if (-not $started) {
        $started = $recordPath -eq $StartAt
        if (-not $started) { continue }
    }
    Write-Output "Refreshing $($record.Group)/$($record.File)"
    $source = if ($record.SourceUrl) { @{Title=$record.SourceTitle; Url=$record.SourceUrl; Article=$null} } else { $null }
    if (-not $source) { $source = Get-CommonsFile $record.FileTitle }
    if (-not $source) { $source = Get-PageImage $record.Article }
    if (-not $source) { $source = Search-Commons $record.Query }
    if (-not $source) { throw "No image found for $($record.Group)/$($record.File)" }
    [void]$usedSources.Add($source.Title)
    $destination = Join-Path $ProjectRoot "storage/app/public/$($record.Group)/$($record.File)"
    if (-not $ManifestOnly) {
        try { Save-CoverImage $source.Title $source.Url $destination $record.Focus $record.Fit }
        catch {
            [void]$usedSources.Remove($source.Title)
            [void]$rejectedSources.Add($source.Title)
            $source = Search-Commons $record.Query
            if (-not $source) { throw }
            [void]$usedSources.Add($source.Title)
            Save-CoverImage $source.Title $source.Url $destination $record.Focus $record.Fit
        }
    }
    $credits += [pscustomobject]@{
        Record = "$($record.Group)/$($record.File)"
        Subject = $record.Query
        WikimediaFile = $source.Title
        SourceUrl = $source.Url
        Article = $source.Article
    }
    if (-not $ManifestOnly) { Start-Sleep -Milliseconds 900 }
}

$manifestPath = Join-Path $ProjectRoot 'IMAGE_SOURCES.json'
$allCredits = @($credits)
function Expand-CreditRecords($items) {
    foreach ($item in @($items)) {
        if ($item.Record) { $item }
        elseif ($item.value) { Expand-CreditRecords $item.value }
    }
}
if (($Only -or $StartAt) -and (Test-Path $manifestPath)) {
    $updatedRecords = @($credits | ForEach-Object { $_.Record })
    $parsedCredits = Get-Content $manifestPath -Raw | ConvertFrom-Json
    $existingCredits = @(Expand-CreditRecords $parsedCredits)
    $allCredits = @($existingCredits | Where-Object { $_.Record -notin $updatedRecords }) + @($credits)
}
$allCredits = @($allCredits | Sort-Object Record)
$manifestJson = $allCredits | ConvertTo-Json -Depth 4
[System.IO.File]::WriteAllText($manifestPath, $manifestJson, (New-Object System.Text.UTF8Encoding($false)))
$lines = @('# GeoArchive Image Sources', '', 'All seeded images are independent, topic-matched Wikimedia sources. They are locally stored and normalized to a consistent 1400×900 presentation crop.', '', '| Archive file | Historical subject | Wikimedia source |', '| --- | --- | --- |')
foreach ($credit in $allCredits) {
    $label = $credit.WikimediaFile -replace '^File:', '' -replace '\|', '-'
    $lines += "| ``$($credit.Record)`` | $($credit.Subject) | [$label]($($credit.SourceUrl)) |"
}
$lines | Set-Content -Encoding utf8 (Join-Path $ProjectRoot 'IMAGE_CREDITS.md')
Write-Output "Documented $($credits.Count) independent images."
