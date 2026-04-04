const { addonBuilder, serveHTTP } = require('stremio-addon-sdk');
const fetch = require('node-fetch');

const BACKEND_URL = process.env.BACKEND_URL || 'https://entertainment-tracker-9okg.onrender.com';
const PORT = process.env.PORT || 7000;

const manifest = {
    id: 'com.entertainment-tracker.stremio',
    version: '1.0.0',
    name: 'Entertainment Tracker',
    description: 'Auto-track your watch progress to Entertainment Tracker',
    catalogs: [],
    resources: ['subtitles'],
    types: ['movie', 'series'],
    idPrefixes: ['tt'],
    behaviorHints: {
        configurable: true,
        configurationRequired: true,
    },
    config: [
        {
            key: 'apiToken',
            type: 'text',
            title: 'API Token (from Entertainment Tracker)',
        },
    ],
};

const builder = new addonBuilder(manifest);

// We use the subtitles handler as a hook — Stremio calls it when playback starts.
// We don't return actual subtitles, just use it to log the watch event.
builder.defineSubtitlesHandler(async ({ type, id, config }) => {
    if (!config || !config.apiToken) {
        return { subtitles: [] };
    }

    try {
        const imdbId = id.split(':')[0]; // tt1234567 or tt1234567:1:2 for series
        const season = id.split(':')[1] || null;
        const episode = id.split(':')[2] || null;

        await sendWatchEvent(config.apiToken, {
            imdb_id: imdbId,
            content_type: type === 'series' ? 'tv' : 'movie',
            season: season ? parseInt(season) : null,
            episode: episode ? parseInt(episode) : null,
        });
    } catch (err) {
        console.error('Failed to send watch event:', err.message);
    }

    return { subtitles: [] };
});

async function sendWatchEvent(token, data) {
    const res = await fetch(`${BACKEND_URL}/api/stremio/webhook`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
        },
        body: JSON.stringify(data),
    });

    if (!res.ok) {
        const text = await res.text();
        throw new Error(`Webhook failed (${res.status}): ${text}`);
    }

    return res.json();
}

serveHTTP(builder.getInterface(), { port: PORT });
console.log(`Stremio addon running on port ${PORT}`);
console.log(`Install URL: http://localhost:${PORT}/manifest.json`);
