# Google OAuth Setup Guide

## Quick Setup Instructions

To fix the "Missing required parameter: client_id" error, you need to:

### Step 1: Create Google OAuth Credentials

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable **Google+ API** (or Google Identity API)
4. Go to **Credentials** → **Create Credentials** → **OAuth client ID**
5. Choose **Web application** as the application type
6. Add **Authorized redirect URIs**:
   - `http://127.0.0.1:8000/auth/google/callback`
   - `http://localhost:8000/auth/google/callback` (if using localhost)

### Step 2: Add Credentials to .env File

Add these lines to your `.env` file:

```env
GOOGLE_CLIENT_ID=your_client_id_here.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your_client_secret_here
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```

### Step 3: Clear Configuration Cache

Run these commands:

```bash
php artisan config:clear
php artisan cache:clear
```

### Step 4: Test Google Authentication

1. Visit `/login` or `/register`
2. Click "Continue with Google"
3. You should be redirected to Google login
4. After login, you'll be redirected back to your site

## Troubleshooting

If you still get errors:

1. **Check .env file** - Make sure GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET are set
2. **Verify redirect URI** - Must match exactly in Google Console
3. **Check API is enabled** - Google+ API must be enabled
4. **Clear cache** - Run `php artisan config:clear`

## For Production

When deploying to production:

1. Update the redirect URI in Google Console to your production URL
2. Update `.env` file with production URL:
   ```
   GOOGLE_REDIRECT_URI=https://yourdomain.com/auth/google/callback
   ```

## Note

If Google OAuth is not configured, users can still use email/password login. The Google button will show an error message if clicked without proper configuration.
