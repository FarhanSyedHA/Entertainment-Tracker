import type { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'com.syedfarhan.entertainmenttracker',
  appName: 'Entertainment Tracker',
  webDir: 'dist',
  // The APK is a thin WebView over the live Vercel frontend. Any frontend deploy
  // is picked up instantly — no APK rebuild needed. `dist/` still has to exist
  // (Capacitor copies it into android/app/src/main/assets as a fallback) but the
  // WebView navigates to server.url on launch.
  server: {
    url: 'https://entertainment-tracker-xi.vercel.app',
    cleartext: false,
  },
};

export default config;
