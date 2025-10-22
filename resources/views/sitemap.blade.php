<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    {{-- Página principal de listagem --}}
    <url>
        <loc>{{ url('/processos') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>

    {{-- Cada processo individual --}}
    @foreach ($processes as $process)
        <url>
            <loc>{{ url("/processos/{$process->id}") }}</loc>
            @if ($process->updated_at || $process->created_at)
                <lastmod>{{ ($process->updated_at ?? $process->created_at)->toAtomString() }}</lastmod>
            @endif
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach
</urlset>
