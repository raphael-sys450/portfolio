<?php
// URL de ton profil Behance
$url = 'https://www.behance.net/raphaelarnold_';

// Récupération du HTML distant
$html = @file_get_contents($url);
if ($html === false) {
    die('Impossible de récupérer le contenu de la page Behance.');
}

// Chargement dans DOMDocument
libxml_use_internal_errors(true);
$dom = new DOMDocument();
$dom->loadHTML($html);
libxml_clear_errors();

// Récupération de toutes les balises <img>
$xpath = new DOMXPath($dom);
$imgs = $xpath->query('//img');

// Construction d’un tableau d’images uniques
$images = [];
foreach ($imgs as $img) {
    $src = $img->getAttribute('src');
    $alt = $img->getAttribute('alt');

    // On ignore les images vides
    if (!$src) {
        continue;
    }

    // Normalisation URL relative -> absolue (au cas où)
    if (strpos($src, '//') === 0) {
        $src = 'https:' . $src;
    } elseif (strpos($src, 'http') !== 0) {
        // Si ce n'est pas une URL absolue mais relative
        $src = 'https://www.behance.net' . $src;
    }

    // Évite les doublons
    if (!isset($images[$src])) {
        $images[$src] = [
            'src' => $src,
            'alt' => $alt
        ];
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Galerie – Images Behance de Raphaël Arnold</title>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            margin: 0;
            padding: 2rem;
            background: #0f0f10;
            color: #f5f5f5;
        }
        h1 {
            margin-bottom: 1rem;
        }
        .info {
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            color: #aaa;
        }
        .grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        }
        .item {
            background: #18181a;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #27272a;
        }
        .item a {
            color: inherit;
            text-decoration: none;
        }
        .thumb-wrapper {
            aspect-ratio: 4 / 3;
            background: #0b0b0c;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .thumb-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .caption {
            padding: 0.5rem 0.75rem;
            font-size: 0.8rem;
            color: #d4d4d8;
            border-top: 1px solid #27272a;
            word-break: break-word;
        }
        .caption small {
            display: block;
            color: #71717a;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <h1>Images Behance – Raphaël Arnold</h1>
    <p class="info">
        Cette page répertorie toutes les images trouvées sur
        <a href="https://www.behance.net/raphaelarnold_" target="_blank" style="color:#60a5fa;">
            ton profil Behance
        </a>.
        Les images sont récupérées dynamiquement côté serveur en PHP.
    </p>

    <?php if (empty($images)): ?>
        <p>Aucune image trouvée.</p>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($images as $image): ?>
                <div class="item">
                    <a href="<?php echo htmlspecialchars($image['src']); ?>" target="_blank">
                        <div class="thumb-wrapper">
                            <img src="<?php echo htmlspecialchars($image['src']); ?>"
                                 alt="<?php echo htmlspecialchars($image['alt'] ?: 'Image Behance'); ?>">
                        </div>
                        <div class="caption">
                            <?php echo htmlspecialchars($image['alt'] ?: 'Sans titre'); ?>
                            <small><?php echo htmlspecialchars($image['src']); ?></small>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</body>
</html>
