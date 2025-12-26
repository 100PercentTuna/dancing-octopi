# GitHub Actions SFTP Deployment - Setup Guide

## Step 1: Add GitHub Secrets

Go to your GitHub repository and add the FTP credentials as secrets:

1. Navigate to: https://github.com/100PercentTuna/dancing-octopi/settings/secrets/actions
2. Click **"New repository secret"**
3. Add these three secrets:

### FTP_SERVER
- **Name**: `FTP_SERVER`
- **Secret**: Your hosting FTP/SFTP server address
  - For GoDaddy: Usually `ftp.kunaalwadhwa.com` or the server IP
  - Check your hosting control panel or email for this

### FTP_USERNAME
- **Name**: `FTP_USERNAME`
- **Secret**: Your FTP username
  - For GoDaddy: Usually your cPanel username or hosting username

### FTP_PASSWORD
- **Name**: `FTP_PASSWORD`
- **Secret**: Your FTP password
  - For GoDaddy: Your hosting password or FTP-specific password

## Step 2: Find Your FTP Credentials (GoDaddy)

If you don't have your FTP credentials:

1. Log into GoDaddy: https://sso.godaddy.com/
2. Go to "My Products" → "Managed WordPress"
3. Click "Manage" next to kunaalwadhwa.com
4. Look for "SFTP/SSH" or "File Manager" section
5. Create/view SFTP credentials there

**OR** check your email for hosting setup details from GoDaddy.

## Step 3: Verify Server Path

The workflow is configured to upload to:
```
/wp-content/themes/kunaal-theme/
```

If your WordPress installation is in a subdirectory, you may need to update the `server-dir` path in `.github/workflows/deploy-theme.yml`:

```yaml
server-dir: /public_html/wp-content/themes/kunaal-theme/  # If WP is in public_html
# or
server-dir: /html/wp-content/themes/kunaal-theme/         # If WP is in html
```

## Step 4: Push to GitHub

Once secrets are added, push the workflow:

```bash
git push origin main
```

If the push fails due to token permissions, push via GitHub web interface:
1. Go to https://github.com/100PercentTuna/dancing-octopi
2. Click "Add file" → "Upload files"
3. Upload `.github/workflows/deploy-theme.yml` and `.github/SETUP-DEPLOYMENT.md`
4. Commit changes

## Step 5: Test Deployment

After pushing, the workflow will run automatically. To verify:

1. Go to: https://github.com/100PercentTuna/dancing-octopi/actions
2. You should see a workflow run for your latest commit
3. Click on it to see the deployment log
4. If successful, check WordPress admin → Appearance → Themes to confirm version update

## Step 6: Verify It Works

Make a small test change:

```bash
# Edit version number
vim kunaal-theme/style.css  # Change Version: 4.20.7 to 4.20.8

# Commit and push
git add kunaal-theme/style.css
git commit -m "Test auto-deployment - bump to 4.20.8"
git push origin main

# Check GitHub Actions tab - deployment should run automatically
# Check WordPress admin - theme version should update to 4.20.8
```

## Troubleshooting

### "Permission denied" error
- Double-check FTP credentials are correct
- Verify FTP user has write access to `/wp-content/themes/`
- Try connecting with an FTP client (FileZilla) using same credentials

### "Connection refused"  
- Check if your host uses SFTP (port 22) vs FTPS (port 21)
- Update workflow protocol if needed:
  ```yaml
  protocol: sftp  # Instead of ftps
  port: 22        # Add this line
  ```

### Workflow doesn't trigger
- Ensure you pushed to `main` branch
- Check that changes are in `kunaal-theme/` directory
- View workflow logs in Actions tab

### Theme not updating in WordPress
- Clear WordPress cache
- Check GoDaddy's file manager to verify files actually uploaded
- Check file permissions (should be 644 for files, 755 for directories)

## Need Help?

Check the deployment logs:
```bash
# View recent commits
git log --oneline -5

# Check workflow file
cat .github/workflows/deploy-theme.yml
```

Or contact me with:
- Error message from GitHub Actions
- FTP connection test results
- Hosting provider details

