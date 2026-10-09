# OAuth2-Server


## Server Status abfragen

 systemctl --user status oauth2-server


## Server starten und stoppen

 systemctl --user stop oauth2-server
 systemctl --user start oauth2-server


## systemd-Unit-Datei erstellen

Mit einem systemd-User-Service läuft der Server dauerhaft, startet automatisch 
nach Boot bzw. Reboot.

 mkdir -p ~/.config/systemd/user
 cat > ~/.config/systemd/user/oauth2-server.service <<'EOF'
 
[Unit]
Description=OAuth2 Authorization Server

[Service]
WorkingDirectory=/home/harald/public_html/app/oauth2-server
ExecStart=/usr/bin/php -S localhost:8010
Restart=always

[Install]
WantedBy=default.target
EOF

 systemctl --user daemon-reload
 systemctl --user enable --now oauth2-server.service
