import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import './index.css'
import App from './App.tsx'
import { AuthProvider } from './context/AuthContext'
import { WatchedProvider } from './context/WatchedContext'
import { ToastProvider } from './context/ToastContext'
import { Capacitor } from '@capacitor/core'
import { StatusBar, Style } from '@capacitor/status-bar'

// When running inside the Capacitor APK, tell Android not to overlay the WebView
// on top of the status bar. With overlay=false, Android draws the status bar in
// its own band and the WebView reports the real cutout/status-bar height through
// env(safe-area-inset-top). The CSS already reads that value; this just makes
// Android populate it instead of reporting 0. On the web (non-native), the
// import is a no-op — Capacitor.isNativePlatform() guards the call.
if (Capacitor.isNativePlatform()) {
  StatusBar.setOverlaysWebView({ overlay: false }).catch(() => {});
  StatusBar.setStyle({ style: Style.Dark }).catch(() => {});
  StatusBar.setBackgroundColor({ color: '#0f0f14' }).catch(() => {});
}

createRoot(document.getElementById('root')!).render(
  <StrictMode>
    <AuthProvider>
      <ToastProvider>
        <WatchedProvider>
          <App />
        </WatchedProvider>
      </ToastProvider>
    </AuthProvider>
  </StrictMode>,
)
