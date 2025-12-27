# Email Setup Guide for Contact Form

## Problem
The contact form is showing the error: "Sorry, there was an error sending your message. Please check your email configuration or try emailing directly."

This is because WordPress's default `wp_mail()` function often doesn't work reliably on shared hosting (like GoDaddy) without proper SMTP configuration.

## Solution: Use WP Mail SMTP Plugin with Free Email Service

### Recommended Free Email Services:

1. **Brevo (formerly Sendinblue)** - **RECOMMENDED**
   - Free tier: 300 emails/day
   - Very reliable and easy to set up
   - No credit card required
   - Sign up: https://www.brevo.com/

2. **SendGrid**
   - Free tier: 100 emails/day
   - Reliable, owned by Twilio
   - Sign up: https://sendgrid.com/

3. **Gmail SMTP** (if you have a Gmail account)
   - Free, but requires app password
   - Limited to 500 emails/day
   - Requires 2-factor authentication enabled

### Step-by-Step Setup with Brevo (Recommended):

#### 1. Install WP Mail SMTP Plugin
- Go to WordPress Admin → Plugins → Add New
- Search for "WP Mail SMTP"
- Install and activate "WP Mail SMTP by WPForms"

#### 2. Create Brevo Account
- Go to https://www.brevo.com/
- Sign up for free account (no credit card needed)
- Verify your email address

#### 3. Get SMTP Credentials from Brevo
- Log into Brevo dashboard
- Go to **Settings** → **SMTP & API**
- Click on **SMTP** tab
- You'll see:
  - **SMTP Server**: `smtp-relay.brevo.com`
  - **Port**: `587` (TLS) or `465` (SSL)
  - **Login**: Your Brevo email address
  - **Password**: Create an SMTP key (click "Generate" to create a new one)

#### 4. Configure WP Mail SMTP Plugin
- Go to WordPress Admin → **WP Mail SMTP** → **Settings**
- Select **Other SMTP** as your mailer
- Enter the following:
  - **SMTP Host**: `smtp-relay.brevo.com`
  - **Encryption**: `TLS` (or `SSL` if using port 465)
  - **SMTP Port**: `587` (or `465` for SSL)
  - **Authentication**: Enable
  - **SMTP Username**: Your Brevo email address
  - **SMTP Password**: The SMTP key you generated
  - **From Email**: Your site's email (e.g., `noreply@yourdomain.com` or your personal email)
  - **From Name**: Your name or site name
- Click **Save Settings**

#### 5. Test the Configuration
- In WP Mail SMTP settings, go to **Email Test** tab
- Enter your email address
- Click **Send Email**
- Check your inbox - you should receive a test email

#### 6. Test Contact Form
- Go to your Contact page
- Submit a test message
- Check if you receive the email

### Alternative: Gmail SMTP Setup

If you prefer to use Gmail:

1. **Enable 2-Factor Authentication** on your Google account
2. **Generate App Password**:
   - Go to Google Account → Security → 2-Step Verification → App passwords
   - Generate a password for "Mail"
3. **Configure WP Mail SMTP**:
   - Select **Gmail** as mailer
   - Enter your Gmail address
   - Use the app password (not your regular Gmail password)
   - From Email: Your Gmail address
   - From Name: Your name

### Troubleshooting

- **Still not working?**
  - Check WP Mail SMTP → Tools → Email Log for error messages
  - Verify SMTP credentials are correct
  - Try a different port (587 vs 465)
  - Check if your hosting provider blocks SMTP ports (some shared hosts do)

- **Emails going to spam?**
  - Use a proper "From Email" address (not a generic one)
  - Add SPF and DKIM records to your domain (if using custom domain)
  - Brevo/SendGrid handle this automatically

### Notes

- The contact form code in `functions.php` will automatically work once WP Mail SMTP is configured
- No code changes needed - the plugin intercepts `wp_mail()` calls
- Free tiers are usually sufficient for personal websites
- You can upgrade to paid plans if you need more emails

