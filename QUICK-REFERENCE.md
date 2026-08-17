# Quick Reference Card

Print this out or keep it handy!

---

## Your Credentials

| Service | Where to Find It | What You Need |
|---------|------------------|---------------|
| **Stripe** | stripe.com/dashboard | API Keys, Webhook Secret, Price IDs |
| **Meta** | developers.facebook.com | App ID, App Secret, WhatsApp Token |
| **LinkedIn** | linkedin.com/developers | Client ID, Client Secret |
| **Google** | console.cloud.google.com | Client ID, Client Secret |
| **OpenAI** | platform.openai.com | API Key (sk-...) |

---

## Coolify Dashboard

| Task | Where to Click |
|------|----------------|
| View logs | Your app → Logs |
| Open terminal | Your app → Terminal |
| Change env vars | Your app → Environment Variables |
| Redeploy | Your app → Deploy |
| View databases | Your app → PostgreSQL |

---

## Common Commands

```bash
# Login to your server
ssh root@your-server-ip

# Access Coolify
http://your-server-ip:8000

# Clear Laravel cache (fixes many issues)
php artisan config:clear && php artisan cache:clear

# Check if queue is working
php artisan queue:work --status

# View live logs
tail -f storage/logs/laravel.log

# Restart queue worker
php artisan queue:restart
```

---

## Emergency Fixes

**Site shows white screen:**
```bash
php artisan config:clear
php artisan cache:clear
```

**Database connection error:**
```bash
# Check if PostgreSQL is running
docker ps | grep postgres

# Test database connection
php artisan tinker --execute="DB::connection()->getPdo();"
```

**Queue jobs stuck:**
```bash
php artisan queue:restart
php artisan queue:work --tries=3
```

**WhatsApp not responding:**
1. Check Meta webhook logs
2. Verify token in .env matches Meta dashboard
3. Check domain has SSL certificate

---

## Your URLs

| Service | URL |
|---------|-----|
| Website | https://your-domain.com |
| Admin Panel | https://your-domain.com/admin |
| Coolify | http://your-server-ip:8000 |
| Stripe Dashboard | https://dashboard.stripe.com |
| Meta Dashboard | https://developers.facebook.com |

---

## Support Contacts

- **Stripe Support:** https://support.stripe.com
- **Meta Support:** https://developers.facebook.com/support
- **Coolify Discord:** https://coollabs.io/discord
- **Laravel Discord:** https://laravel.com/discord
