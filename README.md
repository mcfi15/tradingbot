# Foyana — Automated AI Trading Bots & Real-Time Copy Trading Platform

![Foyana Cover](https://foyana.com/assets/images/foyana-cover-image.png)

Foyana is an institutional-grade, self-hosted PHP and Laravel software platform designed exclusively for **Algorithmic AI Bot Trading** and **Verified Copy Trading**. Built on Laravel 12 with direct on-chain blockchain infrastructure, Foyana operates entirely with **self-managed blockchain wallets**—allowing administrators to maintain full ownership of their private keys and settlement workflows without relying on 3rd-party custodial payment gateways.

---

## 🚀 Key Modules & Core Features

### 1. Algorithmic Bot Trading Engine
* **Quantitative & AI Bot Strategies**: Deploy automated quantitative bots with pre-configured risk parameters, daily ROI targets, and dynamic execution cycles.
* **Real-Time Bot Performance Analytics**: Detailed tracking of active activations, historical trade logs, execution timestamps, and aggregate daily performance cards.
* **Automated Profit Distribution**: Background cron scheduler calculating and distributing bot profit yields directly to user balances.
* **Flexible Bot Portfolio**: Users can activate, monitor, scale, and deactivate trading bots seamlessly across various market pairs.

### 2. Real-Time Copy Trading System
* **Verified Master Trader Directory**: Showcase vetted master traders complete with verified track records, win rates, historical ROI, risk levels, and total follower counts.
* **Instant Strategy Mirroring**: Follow top-performing strategies with user-customized capital allocation and risk limits.
* **Automated Profit Sharing**: Transparent profit-sharing mechanisms between master traders and copiers.
* **Detailed Execution History**: Transparent logs of all executed copy trades, profit/loss records, and asset growth.

### 3. Direct Self-Managed Blockchain Infrastructure (Zero 3rd-Party Fees)
* **Native Self-Custodial Architecture**: Administrators maintain full custody and control over private keys, master wallets, and signing processes.
* **Multi-Chain Native Deposit Support**:
  * **EVM Networks**: Ethereum, BNB Smart Chain, Polygon, Arbitrum, Optimism, Base, Avalanche, Linea, Scroll, and more.
  * **Non-EVM Networks**: Solana (SOL & SPL tokens) and TRON (TRX & TRC20 tokens).
* **Automated HD Wallet Generation**: Unique on-chain deposit addresses generated per user/network with cryptographic key derivation.
* **Real-Time Blockchain Monitoring Daemon**: Background listeners scanning blocks and transactions in real-time for instant deposit confirmation and automatic wallet sweeping.
* **Direct On-Chain Withdrawals**: Secure on-chain transaction signing and broadcast directly via RPC nodes.

### 4. Enterprise Security & Regulatory Compliance
* **Regulatory Compliance Showcase**: Built-in compliance module featuring certificates and disclosures (FinCEN MSB, VARA, MiCA, FCA standards).
* **Multi-Tier KYC Verification**: Identity document upload, review, and status validation for compliance.
* **Account Security & 2FA**: Email verification, session management, and login OTP authentication.
* **Multi-Level Referral Engine**: Tiered affiliate reward structure with real-time tracking and instant referral bonus credits.

---

## 🛠 Tech Stack

* **Backend**: Laravel 12.x (PHP 8.3+)
* **Blockchain Core**: Direct RPC / JSON-RPC node integration (EVM, Solana, Tron) with local transaction signing
* **Frontend**: Tailwind CSS (York Theme), Alpine.js, Blade Components, Vite
* **Database**: MySQL 8.0+ / MariaDB (UTF8MB4)
* **Real-time & Queues**: Redis, Laravel Database Queues, Task Scheduler
* **PDF & Media Generation**: DomPDF, QR Code Generator

---

## ⚡ Web-Based Quick Installation

Foyana comes equipped with an automated, step-by-step **Web Installation Wizard** (`/install`) that handles requirement diagnostics, database seeding, license activation, and administrator account creation with zero command-line setup required.

### Server Requirements
* **PHP Version**: `PHP 8.3+` (PHP 8.3 or PHP 8.4 Recommended)
* **Required PHP Extensions**: `BCMath`, `Ctype`, `cURL`, `DOM`, `Fileinfo`, `JSON`, `Mbstring`, `OpenSSL`, `PDO`, `PDO_MySQL`, `Tokenizer`, `XML`, `Zip`, `GD`, `GMP`
* **Database**: MySQL `>= 8.0` or MariaDB `>= 10.4`
* **Web Server**: Apache (with `mod_rewrite` enabled), Nginx, or LiteSpeed / cPanel / hPanel

---

### Step-by-Step Installation Guide

1. **Upload & Extract Package Archive**:
   Download and extract the Foyana software release archive (`foyana-v*.zip`). Inside, you will find the `foyana/Files` directory containing the application and the `foyana/documentation` folder.

2. **Copy Files to Web Root (`public_html`)**:
   Copy or move all files and folders located **inside `foyana/Files`** directly into your domain's web root (e.g., `public_html` or `/var/www/foyana`).
   > **Important for cPanel users**: Make sure **"Show Hidden Files (dotfiles)"** is enabled in cPanel File Manager Settings before copying so that critical system files like `.env` and `.htaccess` are properly included.

3. **Set Web Server Document Root**:
   Ensure your web server properly directs web traffic to the **`public/`** folder:
   * **Apache / LiteSpeed / cPanel**: **Already pre-configured!** The root directory includes an optimized `.htaccess` file that automatically routes all incoming requests into the `public/` directory and enforces HTTPS.
   * **Nginx Server**: Configure your server block so that the `root` directive points directly to the `public/` folder (e.g., `root /var/www/foyana/public;` or `root /home/user/public_html/public;`).

4. **Launch Web Installer**:
   Open your browser and navigate to:
   ```
   https://your-domain.com/install
   ```

5. **Complete 8-Step Setup Wizard**:
   The installer will guide you through the automated setup:
   * **System Diagnostics**: Automated verification of PHP extensions, server settings, and folder permissions.
   * **License Activation**: Enter your purchase license key.
   * **Database Configuration**: Enter your MySQL credentials (the installer automatically imports and migrates the database schema).
   * **Administrator Setup**: Configure your primary super-admin account and platform defaults.

6. **Setup Background Cron Job**:
   To automate AI bot trading cycles, profit distribution, and blockchain monitoring, set up a 1-minute cron job on your server (cPanel, crontab, or external cron service):
   ```bash
   * * * * * curl -s -o /dev/null https://your-domain.com/utils/cronjob
   ```
   *(Or via CLI: `* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1`)*

---

## 📖 Live Demonstration & Documentation

* **Official Portal**: [https://foyana.com](https://foyana.com)
* **Documentation**: [https://foyana.com/docs](https://foyana.com/docs)
* **Demo Environment**: [https://foyana.com/demo](https://foyana.com/demo)

---

## 📄 License & Proprietary Information

Foyana is commercial software. All rights reserved. Redistribution, resale, or unauthorized sublicensing is strictly prohibited.
