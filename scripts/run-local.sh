#!/usr/bin/env bash
# Local dev runner for the Perch CKEditor AI buttons.
#
# Usage:
#   scripts/run-local.sh            # start XAMPP + open admin
#   scripts/run-local.sh stop       # stop XAMPP
#   scripts/run-local.sh smoke      # hit the Anthropic API directly to
#                                   # confirm the key in ai/config.php works
#
# Requires: XAMPP at /Applications/XAMPP, an Anthropic API key.

set -euo pipefail

XAMPP=/Applications/XAMPP/xamppfiles/xampp
PROJECT=/Applications/XAMPP/xamppfiles/htdocs/perchDev
ADMIN_URL="http://localhost/perchDev/perch/"
AI_DIR="$PROJECT/perch/addons/plugins/editors/ckeditor/perch/ai"
AI_CONFIG="$AI_DIR/config.php"
AI_SAMPLE="$AI_DIR/config.sample.php"

log()  { printf '\033[1;34m▸\033[0m %s\n' "$*"; }
warn() { printf '\033[1;33m!\033[0m %s\n' "$*"; }
die()  { printf '\033[1;31m✗\033[0m %s\n' "$*" >&2; exit 1; }

ensure_config() {
    if [[ -f "$AI_CONFIG" ]]; then
        return
    fi
    warn "ai/config.php not found."
    if [[ ! -f "$AI_SAMPLE" ]]; then
        die  "config.sample.php missing — project not set up correctly."
    fi
    cp "$AI_SAMPLE" "$AI_CONFIG"
    log "Created $AI_CONFIG from sample."
    log "Edit it and set PERCH_AI_API_KEY to your Anthropic key before testing."
    if [[ -n "${ANTHROPIC_API_KEY:-}" ]]; then
        /usr/bin/sed -i '' "s|define('PERCH_AI_API_KEY', '')|define('PERCH_AI_API_KEY', '${ANTHROPIC_API_KEY}')|" "$AI_CONFIG"
        log "ANTHROPIC_API_KEY env var detected — key injected into config.php."
    fi
}

mysql_already_listening() {
    # Returns 0 if something is already serving MySQL on localhost:3306.
    if command -v lsof >/dev/null 2>&1 && lsof -nP -iTCP:3306 -sTCP:LISTEN >/dev/null 2>&1; then
        return 0
    fi
    # Fallback: try a TCP connect.
    (exec 3<>/dev/tcp/127.0.0.1/3306) 2>/dev/null && return 0
    return 1
}

clean_stale_mysql_pids() {
    # XAMPP writes <hostname>.pid. If the machine's network hostname
    # changes, old PID files are left behind and the startup script's
    # `kill $(cat …)` fails with "No such process".
    local pid
    for f in /Applications/XAMPP/xamppfiles/var/mysql/*.pid; do
        [[ -e "$f" ]] || continue
        pid=$(cat "$f" 2>/dev/null || echo '')
        if [[ -z "$pid" ]] || ! ps -p "$pid" >/dev/null 2>&1; then
            log "Removing stale PID file $(basename "$f") (pid=$pid)"
            sudo rm -f "$f"
        fi
    done
}

start() {
    ensure_config
    log "Starting XAMPP (sudo required)…"
    sudo "$XAMPP" startapache

    if mysql_already_listening; then
        warn "Something is already listening on localhost:3306 — skipping XAMPP MySQL."
        warn "Perch will use the existing MySQL. If that isn't what you want, stop it first."
    else
        clean_stale_mysql_pids
        sudo "$XAMPP" startmysql || warn "MySQL failed to start — Perch may not load."
    fi

    log "Waiting for Apache to respond…"
    for i in {1..20}; do
        if curl -sf -o /dev/null "$ADMIN_URL"; then
            log "Apache is up."
            break
        fi
        sleep 0.5
    done
    log "Opening admin: $ADMIN_URL"
    open "$ADMIN_URL"
    cat <<EOF

Next steps:
  1. Log in to the Perch admin.
  2. Open any page/post with a CKEditor field.
  3. Look for the two new buttons in the toolbar:
       • Improve writing (AI)
       • Summarize (AI)
  4. Select some text (or leave nothing selected to act on the whole body)
     and click a button. The content will be replaced with the AI output.

To stop XAMPP:   scripts/run-local.sh stop
To smoke-test the API key:   scripts/run-local.sh smoke
EOF
}

stop() {
    log "Stopping XAMPP (sudo required)…"
    sudo "$XAMPP" stop
}

# Standalone smoke test: reads the API key straight out of config.php
# and calls the Anthropic Messages API. Bypasses Perch auth so you can
# verify the key/model/network before wiring up the browser.
smoke() {
    [[ -f "$AI_CONFIG" ]] || die "ai/config.php missing — run 'scripts/run-local.sh' first."
    local key model
    key=$(/Applications/XAMPP/xamppfiles/bin/php -r "require '$AI_CONFIG'; echo defined('PERCH_AI_API_KEY') ? PERCH_AI_API_KEY : '';" 2>/dev/null)
    model=$(/Applications/XAMPP/xamppfiles/bin/php -r "require '$AI_CONFIG'; echo defined('PERCH_AI_MODEL') ? PERCH_AI_MODEL : 'claude-opus-4-7';" 2>/dev/null)
    [[ -n "$key" ]] || die "PERCH_AI_API_KEY is empty in $AI_CONFIG."
    log "Calling Anthropic with model=$model…"
    curl -sS https://api.anthropic.com/v1/messages \
        -H "x-api-key: $key" \
        -H "anthropic-version: 2023-06-01" \
        -H "content-type: application/json" \
        -d "{\"model\":\"$model\",\"max_tokens\":64,\"messages\":[{\"role\":\"user\",\"content\":\"Reply with the single word: ok\"}]}" \
        | /Applications/XAMPP/xamppfiles/bin/php -r '$r=json_decode(stream_get_contents(STDIN),true); if(isset($r["content"][0]["text"])){echo "✓ API reply: ".$r["content"][0]["text"]."\n";}else{echo "✗ Unexpected response:\n"; print_r($r);}'
}

case "${1:-start}" in
    start) start ;;
    stop)  stop ;;
    smoke) smoke ;;
    *)     die "Unknown command: $1 (use start|stop|smoke)" ;;
esac
