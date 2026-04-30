# Runway PWA - GitHub Pages Deployment

This is a Progressive Web App (PWA) version of Runway that can be installed on phones and works offline with cached data.

## What's Included

- **manifest.json** - PWA metadata for installation
- **sw.js** - Service worker for offline support and caching
- **PWA Meta Tags** - iOS and Android support in index.php
- **Installable** - "Add to Home Screen" on mobile devices

## Deployment Options

### Option 1: GitHub Pages + Existing Backend (Recommended)

If your PHP backend is already running somewhere (local machine, Render, Heroku, etc.):

1. **Create a new GitHub repository** (or use existing)
   ```bash
   git remote add github https://github.com/yourusername/runway.git
   git push github main
   ```

2. **Enable GitHub Pages**
   - Go to repository Settings → Pages
   - Select `main` branch as source
   - Your PWA will be available at `https://yourusername.github.io/runway`

3. **Update API Endpoint** (in `app.js`)
   - Change `api.php` calls to point to your backend server
   - Example: Replace `api.php?action=` with `https://your-backend.com/api.php?action=`

4. **Handle CORS**
   - Your backend needs to allow requests from your GitHub Pages domain
   - Update headers in `api.php`:
   ```php
   header('Access-Control-Allow-Origin: https://yourusername.github.io');
   header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
   header('Access-Control-Allow-Headers: Content-Type');
   ```

### Option 2: Netlify + Serverless Backend

1. Connect your GitHub repo to Netlify
2. Set build command: `echo "No build"` (it's static files)
3. Set publish directory: `PWA/`
4. Netlify handles serving your PWA with better performance

### Option 3: Full Self-Hosted (PHP + PWA)

If you want everything on one server:

1. Upload the entire `PWA/` folder to your hosting
2. PHP/MySQL backend runs on the same domain
3. No CORS configuration needed
4. App works with your existing database

## Local Testing

To test the PWA locally:

```bash
cd PWA
php -S localhost:8000
```

Then visit `http://localhost:8000/` and:
- Open DevTools (F12)
- Go to Application → Service Workers to see the service worker
- Go to Application → Manifest to see the PWA manifest
- Try offline mode (DevTools → Network → Offline) - cached pages still load

## Installing as PWA

### Desktop (Chrome/Edge)
1. Click the "Install" button in address bar
2. Or: Menu → "Install app"

### iPhone/iPad
1. Open in Safari
2. Share → "Add to Home Screen"
3. App appears on home screen

### Android (Chrome)
1. Menu → "Install app" or
2. Tap the install prompt when it appears

## Key Features

✅ **Offline Support** - Caches pages and API responses  
✅ **Installable** - Acts like a native app  
✅ **Works Everywhere** - GitHub Pages, any hosting  
✅ **Same Data** - Connects to your existing backend  

## Important Notes

- **Session Management**: Users must log in through `login.php` first
- **Backend Required**: This PWA version still needs your PHP backend running
- **CORS**: Make sure your backend allows requests from your PWA URL
- **Service Worker**: Caches may need manual clearing if you update code. Users can reload with Ctrl+Shift+R (or Cmd+Shift+R on Mac)

## Customization

- **Theme Color**: Edit `theme_color` in `manifest.json`
- **App Name**: Edit `name` and `short_name` in `manifest.json`
- **Icons**: Update icon data URLs in `manifest.json` and `index.php`
