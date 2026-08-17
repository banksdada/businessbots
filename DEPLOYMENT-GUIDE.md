# BusinessBots — Deployment Guide (Beginner Friendly)

This guide walks you through deploying BusinessBots to your server using **Coolify**.

---

## Prerequisites

Before you start, make sure you have:

- [ ] A **GitHub account** (you already have this)
- [ ] A **server** with Coolify installed (e.g., DigitalOcean, Vultr, Hetzner)
- [ ] A **domain name** pointed to your server's IP address
- [ ] A **Stripe account** (for payments)
- [ ] A **Meta Developer account** (for WhatsApp/Instagram)
- [ ] A **Google Cloud account** (for Business Profile)
- [ ] An **OpenAI API key** (for AI features)

---

## Step 1: Set Up Your Server with Coolify

### 1.1 Install Coolify

If you haven't already, install Coolify on your server:

```bash
# SSH into your server
ssh root@your-server-ip

# Install Coolify (one-liner)
curl -fsSL https://cdn.coollabs.io/coolify/install.sh | bash
```

After installation, Coolify will be available at:
```
http://your-server-ip:8000
```

### 1.2 Complete Coolify Setup

1. Open `http://your-server-ip:8000` in your browser
2. Create your admin account
3. Add your server (Coolify will guide you through this)

---

## Step 2: Connect GitHub to Coolify

### 2.1 Add GitHub Integration

1. In Coolify, go to **Keys & Tokens** → **GitHub**
2. Click **Add GitHub Integration**
3. Enter a name (e.g., "My GitHub")
4. You'll need a **GitHub Personal Access Token**

### 2.2 Create GitHub Token

1. Go to [github.com/settings/tokens](https://github.com/settings/tokens)
2. Click **Generate new token (classic)**
3. Give it a name like "Coolify Deployment"
4. Select these permissions:
   - `repo` (Full control of private repositories)
   - `read:org` (Read organization membership)
5. Click **Generate token**
6. **Copy the token** (you'll only see it once!)

### 2.3 Add Token to Coolify

1. Paste the token into Coolify's GitHub integration
2. Click **Save**

---

## Step 3: Create Environment Variables

### 3.1 Generate Laravel App Key

On your local machine, run:

```bash
# Clone the repo (if you haven't already)
git clone https://github.com/banksdada/businessbots.git
cd businessbots

# Generate an app key
php artisan key:generate --show
```

Copy the key that looks like:
```
base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### 3.2 Create .env File

Copy the example file and fill in your values:

```bash
cp .env.example .env
```

Then edit `.env` and fill in these values:

```env
# App
APP_KEY=base64:your-generated-key-here
APP_URL=https://your-domain.com

# Database (Coolify will create these automatically)
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=businessbots
DB_USERNAME=your-db-username
DB_PASSWORD=your-secure-password

# Redis (Coolify will create this automatically)
REDIS_HOST=redis
REDIS_PASSWORD=your-secure-password

# Stripe (from stripe.com/dashboard)
STRIPE_KEY=pk_live_xxxxx
STRIPE_SECRET=sk_live_xxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxx
STRIPE_PRICE_STARTER=price_xxxxx
STRIPE_PRICE_PROFESSIONAL=price_xxxxx

# Meta (from developers.facebook.com)
META_APP_ID=your-app-id
META_APP_SECRET=your-app-secret
META_WHATSAPP_TOKEN=your-whatsapp-token
META_WHATSAPP_PHONE_NUMBER_ID=your-phone-number-id
META_WEBHOOK_VERIFY_TOKEN=your-custom-token

# LinkedIn (from linkedin.com/developers)
LINKEDIN_CLIENT_ID=your-client-id
LINKEDIN_CLIENT_SECRET=your-client-secret

# Google (from console.cloud.google.com)
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret

# OpenAI (from platform.openai.com)
OPENAI_API_KEY=sk-xxxxx
AI_API_KEY=sk-xxxxx

# Email (use Gmail SMTP for testing)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME=BusinessBots
```

---

## Step 4: Create the Deployment in Coolify

### 4.1 Add New Resource

1. In Coolify, click **+ Add New Resource**
2. Select **Docker Compose**
3. Choose your server
4. Click **Continue**

### 4.2 Connect to GitHub

1. Select your GitHub integration
2. Repository: `banksdada/businessbots`
3. Branch: `master`
4. Docker Compose Location: `./docker-compose.yml`
5. Click **Continue**

### 4.3 Configure Build Settings

1. **Name:** `businessbots` (or whatever you like)
2. **Domain:** Enter your domain (e.g., `businessbots.yourdomain.com`)
3. Click **Continue**

### 4.4 Add Environment Variables

1. In the deployment settings, go to **Environment Variables**
2. Copy ALL values from your `.env` file into Coolify
3. Each variable should be on its own line

**Important:** Make sure these match exactly:
```
DB_HOST=postgres
REDIS_HOST=redis
```

### 4.5 Deploy

1. Click **Deploy**
2. Wait for the build to complete (5-10 minutes)
3. Check the logs for any errors

---

## Step 5: Set Up the Database

### 5.1 Run Migrations

After deployment, Coolify will show your running services. You need to run database migrations:

1. In Coolify, go to your **app** service
2. Click **Terminal** (or SSH into your server)
3. Run:

```bash
php artisan migrate --force
php artisan db:seed
```

### 5.2 Create Storage Link

```bash
php artisan storage:link
```

---

## Step 6: Configure Stripe Webhooks

### 6.1 Add Webhook Endpoint

1. Go to [stripe.com/dashboard/webhooks](https://stripe.com/dashboard/webhooks)
2. Click **Add endpoint**
3. Enter: `https://your-domain.com/stripe/webhook`
4. Select events:
   - `checkout.session.completed`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
   - `invoice.payment_failed`
5. Click **Add endpoint**

### 6.2 Copy Webhook Secret

1. Click on the webhook you just created
2. Copy the **Signing secret** (starts with `whsec_`)
3. Add it to your `.env` in Coolify as `STRIPE_WEBHOOK_SECRET`

---

## Step 7: Configure Meta (WhatsApp/Instagram)

### 7.1 Create Meta App

1. Go to [developers.facebook.com](https://developers.facebook.com)
2. Click **My Apps** → **Create App**
3. Select **Business** type
4. Fill in app name and contact email
5. Click **Create App**

### 7.2 Set Up WhatsApp

1. In your app, click **Add Product** → **WhatsApp**
2. Follow the setup wizard
3. Copy your **Phone Number ID** and **Temporary Access Token**
4. Add these to your `.env` in Coolify

### 7.3 Configure Webhook

1. In WhatsApp settings, go to **Configuration**
2. Enter your webhook URL: `https://your-domain.com/webhooks/whatsapp`
3. Enter your **Verify Token** (the one you set in `.env`)
4. Subscribe to messages

---

## Step 8: Test Everything

### 8.1 Test the Website

1. Visit `https://your-domain.com`
2. You should see the homepage
3. Click **Sign up** and create an account
4. Complete the onboarding wizard

### 8.2 Test WhatsApp

1. Send a message to your WhatsApp Business number
2. Check the logs in Coolify for any errors
3. You should receive an AI-generated reply

### 8.3 Test Billing

1. Go to the pricing page
2. Click **Start Free Trial**
3. Complete the Stripe checkout
4. Verify the subscription is active

---

## Troubleshooting

### "Application is not booting"

Check the logs in Coolify:
1. Go to your app service
2. Click **Logs**
3. Look for PHP errors

Common fixes:
- Make sure `APP_KEY` is set correctly
- Make sure database credentials match
- Run `php artisan config:clear`

### "WhatsApp messages not received"

1. Check Meta webhook logs
2. Verify the verify token matches
3. Make sure your domain has SSL (HTTPS)

### "Posts not publishing"

1. Check if queue worker is running
2. Look at the logs for queue job errors
3. Verify social media tokens are valid

### "Payments not working"

1. Verify Stripe keys are correct (use test keys first!)
2. Check webhook endpoint is accessible
3. Look at Stripe webhook logs for errors

---

## Useful Commands

```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Check queue status
php artisan queue:work --status

# View logs
tail -f storage/logs/laravel.log

# Run tests
php artisan test
```

---

## Getting Help

If you're stuck:

1. Check the [logs in Coolify](#troubleshooting)
2. Search the error message on Google
3. Ask in the Laravel Discord: [https://laravel.com/discord](https://laravel.com/discord)
4. Open an issue on GitHub: [https://github.com/banksdada/businessbots/issues](https://github.com/banksdada/businessbots/issues)

---

## Next Steps

Once deployed, you can:

- [ ] Set up email notifications
- [ ] Add more social media channels
- [ ] Customize the AI prompts
- [ ] Set up monitoring (e.g., UptimeRobot)
- [ ] Create a backup strategy

---

**Last updated:** August 2026
