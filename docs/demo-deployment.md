# Demo deployment — Hetzner

Password-gated demo for outreach. Takes ~15 minutes on a fresh server.

---

## 1. Provision a Hetzner server

1. Go to [console.hetzner.cloud](https://console.hetzner.cloud) → New project → Add server
2. **Location:** Nuremberg or Falkenstein (Germany, DSGVO)
3. **Image:** Ubuntu 24.04
4. **Type:** CX22 (2 vCPU, 4 GB RAM) — ~€4/month
5. **SSH key:** add your public key
6. **Firewall:** allow ports 22, 80, 443 only
7. Note the server IP

Point a domain (or subdomain like `demo.yourdomain.help`) at the IP via an A record.
FrankenPHP will auto-provision a Let's Encrypt cert. Without a domain, use `:80` in `SERVER_NAME`.

---

## 2. Install Docker on the server

```bash
ssh root@<server-ip>

apt-get update && apt-get upgrade -y
apt-get install -y ca-certificates curl git make
install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] \
  https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo "$VERSION_CODENAME") stable" \
  > /etc/apt/sources.list.d/docker.list
apt-get update
apt-get install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin
```

---

## 3. Clone the repo

```bash
git clone https://github.com/tony-stark-eth/suicide-prevention-app.git
cd suicide-prevention-app
```

---

## 4. Create the env file

```bash
cp .env.demo.example .env.demo
```

Generate the password hash (run locally, not on the server):

```bash
make demo-hash p="your-chosen-demo-password"
```

Copy the output hash, then edit `.env.demo` on the server:

```bash
nano .env.demo
```

Fill in every variable — minimum required:

| Variable | What to set |
|---|---|
| `APP_SECRET` | `openssl rand -hex 32` |
| `POSTGRES_PASSWORD` | any strong password |
| `ANTHROPIC_API_KEY` | your key from console.anthropic.com |
| `SERVER_NAME` | `demo.yourdomain.help` (or `:80` for IP-only) |
| `APP_URL` | `https://demo.yourdomain.help` |
| `FOLLOWUP_FROM_EMAIL` | any address |
| `DEMO_USERNAME` | e.g. `demo` |
| `DEMO_PASSWORD_HASH` | the hash from `make demo-hash` |

---

## 5. Deploy

```bash
make demo-deploy
```

This runs in order:
1. `demo-build` — builds the production Docker image
2. `demo-up` — starts PHP + Postgres + Mailpit
3. `demo-init` — runs migrations, loads fixtures, downloads GeoIP DB, warms cache

Takes 3–5 minutes on first build (downloads PHP image + installs Composer deps).

---

## 6. Verify

```bash
curl -u demo:yourpassword https://demo.yourdomain.help/en
# Should return HTTP 200 with HTML
```

Visit `https://demo.yourdomain.help` in a browser — you'll get a login prompt.

---

## Useful commands after deployment

```bash
make demo-logs          # follow logs
make demo-sh            # shell into the container
make demo-down          # stop everything
make demo-up            # restart without rebuilding
```

---

## Updating the demo after a code change

```bash
git pull
make demo-build && make demo-up
```

---

## Notes

- Mailpit captures follow-up emails locally at `http://<server-ip>:8025` (localhost only — not exposed publicly)
- The GeoIP database should be refreshed monthly: `make demo-init` re-downloads it
- Caddy stores TLS certificates in the `caddy_data` Docker volume — they persist across restarts
