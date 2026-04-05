import { useState, useEffect } from 'react';
import { api } from '../api/client';

export default function Settings({ onBack }) {
  const [tokens, setTokens] = useState([]);
  const [newToken, setNewToken] = useState(null);
  const [tokenName, setTokenName] = useState('Stremio');
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    loadTokens();
  }, []);

  const loadTokens = async () => {
    try {
      const data = await api.getTokens();
      setTokens(data.tokens || []);
    } catch {
      setTokens([]);
    }
  };

  const handleCreate = async () => {
    setLoading(true);
    try {
      const data = await api.createToken(tokenName);
      setNewToken(data.token);
      setTokenName('Stremio');
      loadTokens();
    } catch (err) {
      console.error('Failed to create token', err);
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (id) => {
    try {
      await api.deleteToken(id);
      loadTokens();
    } catch (err) {
      console.error('Failed to delete token', err);
    }
  };

  const handleCopy = () => {
    navigator.clipboard.writeText(newToken);
  };

  return (
    <div className="settings-page">
      <div className="settings-header">
        <button className="back-btn" onClick={onBack}>&larr; Back</button>
        <h2>Settings</h2>
      </div>

      <div className="settings-section">
        <h3>API Tokens</h3>
        <p className="settings-desc">
          Generate tokens to connect Stremio addon or other apps.
        </p>

        <div className="token-create">
          <input
            type="text"
            placeholder="Token name"
            value={tokenName}
            onChange={(e) => setTokenName(e.target.value)}
          />
          <button onClick={handleCreate} disabled={loading || !tokenName.trim()}>
            Generate Token
          </button>
        </div>

        {newToken && (
          <div className="token-reveal">
            <p>Your new token (copy it now — it won't be shown again):</p>
            <div className="token-value">
              <code>{newToken}</code>
              <button onClick={handleCopy}>Copy</button>
            </div>
          </div>
        )}

        <div className="token-list">
          {tokens.map((t) => (
            <div key={t.id} className="token-item">
              <div className="token-info">
                <span className="token-name">{t.name}</span>
                <span className="token-preview">{t.token_preview}</span>
                <span className="token-date">
                  {t.last_used_at ? `Last used: ${new Date(t.last_used_at).toLocaleDateString()}` : 'Never used'}
                </span>
              </div>
              <button className="remove-btn" onClick={() => handleDelete(t.id)}>Delete</button>
            </div>
          ))}
          {tokens.length === 0 && <p className="empty-text">No tokens yet.</p>}
        </div>
      </div>

      <div className="settings-section">
        <h3>Stremio Setup</h3>
        <ol className="setup-steps">
          <li>Generate an API token above</li>
          <li>Install the Stremio addon from the addon URL</li>
          <li>Paste your token in the addon configuration</li>
          <li>Start watching — progress auto-syncs!</li>
        </ol>
      </div>
    </div>
  );
}
