# 9M2PJU LiteSOC - WordPress Security Plugin

[![LiteSOC](https://img.shields.io/badge/Security-LiteSOC-cyan.svg)](https://litesoc.io)
[![WordPress](https://img.shields.io/badge/WordPress-Plugin-blue.svg)](https://wordpress.org)
[![Version](https://img.shields.io/badge/Version-1.3.2-green.svg)](https://github.com/9M2PJU/9M2PJU-LiteSOC-WP-Plugin/releases/latest)
[![Donate](https://img.shields.io/badge/Donate-Buy%20Me%20a%20Coffee-yellow.svg)](https://buymeacoffee.com/9m2pju)

**9M2PJU LiteSOC** is a WordPress security plugin that integrates the power of [LiteSOC](https://litesoc.io) real-time threat detection, Behavioral AI, and Geo-IP Intelligence into your WordPress site.

![Settings Page](https://github.com/9M2PJU/9M2PJU-LiteSOC-WP-Plugin/blob/main/assets/settings-screenshot.png?raw=true)

### 🚨 LiteSOC In Action
![LiteSOC Dashboard](https://github.com/9M2PJU/9M2PJU-LiteSOC-WP-Plugin/blob/main/assets/dashboard-action.png?raw=true)

## 🚀 Key Features

- **Real-time Event Ingestion**: Automatically tracks authentication, user management, and admin activities.
- **Source & Environment Tracking**: (New) Tag every security event with its origin (e.g., "my-store") and environment (production/staging) for granular monitoring.
- **Compact & Premium UI**: (New) Sleek, space-saving admin interface with a modern aesthetic and real-time security logs.
- **Behavioral AI & Geo-IP Intelligence**: Identifies Geo-Anomalies, Impossible Travel, and Advanced Brute-force attacks using integrated AI models.
- **Hardened Security**: Includes IP validation (X-Forwarded-For support) and input sanitization.
- **Standardized Schema**: Uses the official LiteSOC event schema for maximum compatibility.

## 🏗 Architecture

```mermaid
graph TD
    subgraph WordPress ["WordPress Instance"]
        Hooks[WP Actions & Hooks]
        Tracker[LiteSOC Tracker]
        Admin[Admin Dashboard]
        API[LiteSOC API Wrapper]
    end

    subgraph LiteSOC_Cloud ["LiteSOC Cloud"]
        Ingest[Collect Endpoint]
        AI[Behavioral AI Engine]
        Dash[LiteSOC Dashboard]
    end

    Hooks -->|Triggers| Tracker
    Tracker -->|Normalizes| API
    API -->|HTTPS/JSON| Injest
    Injest -->|Processes| AI
    AI -->|Alerts/Logs| Dash
    Admin -->|Fetches| API
```

## 📊 Security Insights

The plugin provides granular insights into your site's security posture:

- **Auth Events**: Successful logins, failures, and logouts.
- **Admin Activity**: Plugin changes, settings updates, and privilege escalations.
- **User Activity**: New registrations and profile modifications.

## 🛠 Installation

### Via WordPress Dashboard (Recommended)
1. Go to **Plugins > Add New Plugin > Upload Plugin**.
2. Click **Choose File** and select the `9m2pju-litesoc.zip` package.
3. Click **Install Now** and then **Activate**.

### Via FTP
1. Upload the `9m2pju-litesoc` folder to your `/wp-content/plugins/` directory.
2. Activate the plugin through the **Plugins** menu in WordPress.

### Setup
Navigate to the **9M2PJU LiteSOC** menu and enter your API Key from the LiteSOC Dashboard.

## 📈 Statistics & Verification

Verified via brute-force simulation tests:
- **Throughput**: ~5 events/sec (unbatched).
- **Latency**: <200ms avg response from api.litesoc.io.
- **Success Rate**: 100% on valid payload delivery.
 
## ☕ Support
 
If you find this plugin useful, you can support its development by buying me a coffee:
 
[![Buy Me A Coffee](https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png)](https://buymeacoffee.com/9m2pju)
 
### 📄 License
 
This project is licensed under the **GPLv3** - see the [LICENSE](LICENSE) file for details.

