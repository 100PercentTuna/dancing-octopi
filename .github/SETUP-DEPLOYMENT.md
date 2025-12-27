# GitHub Actions Deployment Setup

This repository uses GitHub Actions to automatically deploy the WordPress theme to your live site when you push to the `main` branch.

## Required GitHub Secrets

Go to your GitHub repository → Settings → Secrets and variables → Actions, and add:

### 1. FTP_SERVER
Your SFTP/FTP server hostname (e.g., `ftp.kunaalwadhwa.com` or IP address)

### 2. FTP_USERNAME  
Your FTP/SFTP username for WordPress hosting

### 3. FTP_PASSWORD
Your FTP/SFTP password

### 4. CACHE_CLEAR_URL (Optional)
If you want to automatically clear WordPress cache after deployment, add a cache-clearing webhook URL here.

## How It Works

1. **Trigger**: Runs automatically when you push to `main` branch AND files in `kunaal-theme/` changed
2. **Deploy**: Uploads only the `kunaal-theme/` folder to `/wp-content/themes/kunaal-theme/`  
3. **Excludes**: Automatically skips `.git`, `node_modules`, `vendor`, `.md` files, etc.
4. **Cache**: Optionally clears WordPress cache via webhook

## Workflow

```bash
# 1. Make changes locally
vim kunaal-theme/functions.php

# 2. Commit and push
git add kunaal-theme/
git commit -m "Update theme version to 4.20.8"
git push origin main

# 3. GitHub Actions deploys automatically (check Actions tab)
# 4. Theme is updated on WordPress within 1-2 minutes
```

## Alternative: Deployer for Git Plugin

If you prefer using the Deployer for Git WordPress plugin:

1. Install and activate [Deployer for Git](https://wordpress.org/plugins/deployer-for-git/) in WordPress admin
2. Go to plugin settings and connect your GitHub repository
3. Copy the webhook URL provided by the plugin
4. Add it as a GitHub webhook or use in GitHub Actions:

```yaml
- name: Trigger WordPress Deploy
  run: |
    curl -X GET "https://kunaalwadhwa.com/wp-admin/admin-ajax.php?action=dfg_deploy&repo_id=YOUR_REPO_ID&secret=YOUR_SECRET"
```

## Troubleshooting

### Deployment fails with "Permission denied"

**Common causes and solutions:**

1. **Incorrect credentials**
   - Double-check `FTP_USERNAME` and `FTP_PASSWORD` in GitHub Secrets
   - Ensure no extra spaces or special characters
   - Try logging in manually with an SFTP client (FileZilla, WinSCP) to verify credentials

2. **Wrong port**
   - Default SFTP port is 22, but some hosts use different ports (2222, 22222, etc.)
   - Add `FTP_PORT` secret if your server uses a non-standard port
   - Check your hosting provider's documentation

3. **SSH key authentication required**
   - Some servers disable password authentication and require SSH keys
   - If your server requires keys, you'll need to:
     - Generate an SSH key pair
     - Add the public key to your server's `~/.ssh/authorized_keys`
     - Add the private key as a GitHub secret `SSH_PRIVATE_KEY`
     - Update the workflow to use key-based auth (see alternative workflow below)

4. **Incorrect remote path**
   - Verify the path exists: `/html/wp-content/themes/` or `/wp-content/themes/`
   - Add `FTP_REMOTE_PATH` secret if your path is different
   - Ensure the FTP user has write permissions to that directory

5. **Server restrictions**
   - Some hosts block automated deployments
   - Check if your hosting provider allows SFTP from GitHub Actions IPs
   - Contact hosting support if needed

**Quick test:**
```bash
# Test SFTP connection manually
sftp -P 22 username@your-server.com
# Enter password when prompted
# If this works, credentials are correct
```

### Theme not updating on site
- Clear WordPress cache (WP Super Cache, W3 Total Cache, etc.)
- Clear browser cache
- Check GitHub Actions logs for errors

### Files not uploading
- Check `exclude` list in `.github/workflows/deploy-theme.yml`
- Verify `local-dir` and `server-dir` paths are correct

## Manual Deployment

If you need to deploy manually:

```bash
# Create ZIP
cd kunaal-theme/
zip -r ../kunaal-theme.zip . -x "*.git*" "*.md" "node_modules/*" "vendor/*"

# Upload via WordPress admin: Appearance → Themes → Add New → Upload Theme
```

## Security Notes

- **Never commit FTP credentials** to the repository
- Use SFTP (secure FTP) instead of plain FTP when possible  
- Rotate FTP password periodically
- Use a dedicated FTP user with minimal permissions (only `/wp-content/themes/`)

