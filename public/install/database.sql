-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 05, 2026 at 10:55 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `foyana_clean`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `lang` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blockchains`
--

CREATE TABLE `blockchains` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rpc_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'enabled',
  `priority` int DEFAULT NULL,
  `symbol` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `network` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `master_wallet_address` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `master_private_key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `instructions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `explorer_url_live` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `explorer_url_devnet` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blockchains`
--

INSERT INTO `blockchains` (`id`, `name`, `code`, `rpc_url`, `status`, `priority`, `symbol`, `network`, `logo`, `master_wallet_address`, `master_private_key`, `created_at`, `updated_at`, `instructions`, `explorer_url_live`, `explorer_url_devnet`) VALUES
(1, 'Solana', 'solana', 'https://api.mainnet-beta.solana.com', 'enabled', 10, 'SOL', 'SOL', 'assets/images/tokens/solana.png', NULL, NULL, '2026-07-27 19:22:01', '2026-08-17 13:22:16', 'To enable auto-sweeping of user deposits, make sure this Master Wallet address is funded with some SOL (for transaction fees). You must also send a tiny amount of USDC and USDT, and any other tokens you want to support to this master wallet address to initialize and activate their respective token accounts on the Solana network.', 'https://solscan.io/account/{address}', 'https://solscan.io/account/{address}?cluster=devnet'),
(2, 'Ethereum', 'ethereum', 'https://eth-mainnet.g.alchemy.com/public', 'enabled', 20, 'ETH', 'ERC20', 'assets/images/tokens/ethereum.png', NULL, NULL, '2026-07-29 00:03:31', '2026-08-17 13:22:16', 'To enable auto-sweeping of user deposits, make sure this Master Wallet address is funded with some ETH (for transaction fees). You must also send a tiny amount of USDC and USDT to this master wallet address to initialize and activate their respective token accounts on the Ethereum network.', 'https://etherscan.io/address/{address}', 'https://sepolia.etherscan.io/address/{address}'),
(3, 'Binance Smart Chain', 'bsc', 'https://bsc-rpc.publicnode.com', 'enabled', 30, 'BNB', 'BEP20', 'assets/images/tokens/bsc.png', NULL, NULL, '2026-07-29 12:53:32', '2026-08-17 13:22:16', 'To enable auto-sweeping of user deposits, make sure this Master Wallet address is funded with some BNB (for transaction fees). You must also send a tiny amount of USDC and USDT to this master wallet address to initialize and activate their respective token accounts on the BSC network.', 'https://bscscan.com/address/{address}', 'https://testnet.bscscan.com/address/{address}'),
(4, 'Base', 'base', 'https://mainnet.base.org', 'enabled', 90, 'BASE', 'BASE', 'assets/images/tokens/base.png', NULL, NULL, '2026-07-29 21:28:38', '2026-08-17 13:22:16', 'To enable auto-sweeping of user deposits, make sure this Master Wallet address is funded with some ETH on Base (for transaction fees). You must also send a tiny amount of USDC and USDT to this master wallet address to initialize and activate their respective token accounts on the Base network.', 'https://basescan.org/address/{address}', 'https://sepolia.etherscan.io/address/{address}'),
(5, 'Polygon', 'polygon', 'https://polygon-bor-rpc.publicnode.com', 'enabled', 60, 'MATIC', 'POL/MATIC', 'assets/images/tokens/polygon.png', NULL, NULL, '2026-07-29 22:58:26', '2026-08-17 13:22:16', 'To enable auto-sweeping of user deposits, make sure this Master Wallet address is funded with some POL (for transaction fees). You must also send a tiny amount of USDC and USDT to this master wallet address to initialize and activate their respective token accounts on the Polygon network.', 'https://polygonscan.com/address/{address}', 'https://amoy.polygonscan.com/address/{address}'),
(6, 'Arbitrum One', 'arbitrum', 'https://arb1.arbitrum.io/rpc', 'enabled', 70, 'ARB', 'ARB', 'assets/images/tokens/arbitrum.png', NULL, NULL, '2026-07-30 05:37:53', '2026-08-17 13:22:16', 'Custodial Layer 2 wallet for automated Arbitrum sweeps.', 'https://arbiscan.io/address/{address}', 'https://sepolia.arbiscan.io/address/{address}'),
(7, 'Optimism', 'optimism', 'https://mainnet.optimism.io', 'enabled', 80, 'OP', 'OP', 'assets/images/tokens/optimism.png', NULL, NULL, '2026-07-30 05:37:53', '2026-08-17 13:22:16', 'Custodial Layer 2 wallet for automated Optimism sweeps.', 'https://optimistic.etherscan.io/address/{address}', 'https://sepolia-optimism.etherscan.io/address/{address}'),
(8, 'Avalanche C-Chain', 'avalanche', 'https://api.avax.network/ext/bc/C/rpc', 'enabled', 100, 'AVAX', 'AVAX', 'assets/images/tokens/avalanche.png', NULL, NULL, '2026-07-30 05:37:53', '2026-08-17 13:22:16', 'Custodial Layer 1 wallet for automated Avalanche sweeps.', 'https://snowtrace.io/address/{address}', 'https://testnet.snowtrace.io/address/{address}'),
(9, 'Fantom Opera', 'fantom', 'https://rpc.ankr.com/fantom', 'enabled', 110, 'FTM', 'FTM', 'assets/images/tokens/fantom.png', NULL, NULL, '2026-07-30 05:37:53', '2026-08-17 13:22:16', 'Custodial Layer 1 wallet for automated Fantom sweeps.', 'https://ftmscan.com/address/{address}', 'https://testnet.ftmscan.com/address/{address}'),
(10, 'Cronos Chain', 'cronos', 'https://evm.cronos.org', 'enabled', 120, 'CRO', 'CRO', 'assets/images/tokens/cronos.png', NULL, NULL, '2026-07-30 05:37:53', '2026-08-17 13:22:16', 'Custodial Layer 1 wallet for automated Cronos sweeps.', 'https://cronoscan.com/address/{address}', 'https://cronos-testnet.crypto.org/explorer/address/{address}'),
(11, 'Linea', 'linea', 'https://rpc.linea.build', 'enabled', 130, 'LINEA', 'LINEA', 'assets/images/tokens/linea.png', NULL, NULL, '2026-07-30 05:37:53', '2026-08-17 13:22:16', 'Custodial Layer 2 wallet for automated Linea sweeps.', 'https://lineascan.build/address/{address}', 'https://sepolia.lineascan.build/address/{address}'),
(12, 'Scroll', 'scroll', 'https://rpc.scroll.io', 'enabled', 140, 'SCROLL', 'SCROLL', 'assets/images/tokens/scroll.png', NULL, NULL, '2026-07-30 05:37:53', '2026-08-17 13:22:17', 'Custodial Layer 2 wallet for automated Scroll sweeps.', 'https://scrollscan.com/address/{address}', 'https://sepolia.scrollscan.com/address/{address}'),
(13, 'zkSync Era', 'zksync', 'https://mainnet.era.zksync.io', 'enabled', 150, 'ZKSYNC', 'ZKSYNC', 'assets/images/tokens/zksync.png', NULL, NULL, '2026-07-30 05:37:53', '2026-08-17 13:22:17', 'Custodial Layer 2 wallet for automated zkSync sweeps.', 'https://era.zksync.network/address/{address}', 'https://sepolia.era.zksync.network/address/{address}'),
(14, 'Celo', 'celo', 'https://forno.celo.org', 'enabled', 160, 'CELO', 'CELO', 'assets/images/tokens/celo.png', NULL, NULL, '2026-07-30 05:37:53', '2026-08-17 13:22:17', 'Custodial Layer 1 wallet for automated Celo sweeps.', 'https://celoscan.io/address/{address}', 'https://alfajores.celoscan.io/address/{address}'),
(15, 'Mantle', 'mantle', 'https://rpc.mantle.xyz', 'enabled', 170, 'MNT', 'MNT', 'assets/images/tokens/mantle.png', NULL, NULL, '2026-07-30 05:37:53', '2026-08-17 13:22:17', 'Custodial Layer 2 wallet for automated Mantle sweeps.', 'https://mantlescan.info/address/{address}', 'https://sepolia.mantlescan.info/address/{address}'),
(16, 'Tron', 'tron', 'https://api.trongrid.io', 'enabled', 40, 'TRX', 'TRC20', 'assets/images/tokens/trx.png', NULL, NULL, '2026-07-30 18:57:52', '2026-08-17 13:22:16', 'Custodial Tron wallet for automated TRX and TRC20 deposit sweeps. Keep the master wallet funded with TRX for bandwidth, energy, and transaction fees.', 'https://tronscan.org/#/address/{address}', 'https://nile.tronscan.org/#/address/{address}'),
(17, 'Bitcoin', 'bitcoin', 'https://blockstream.info/api', 'enabled', 50, 'BTC', 'BTC', 'assets/images/tokens/btc.png', NULL, NULL, '2026-08-05 19:04:23', '2026-08-17 13:22:16', 'To enable auto-sweeping of user deposits, make sure this Master Wallet is configured. Transactions require a fee, which is deducted directly from the deposit when sweeping.', 'https://blockstream.info/address/{address}', 'https://blockstream.info/testnet/address/{address}');

-- --------------------------------------------------------

--
-- Table structure for table `blockchain_tokens`
--

CREATE TABLE `blockchain_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `blockchain_id` bigint UNSIGNED NOT NULL,
  `symbol` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mint_address` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `decimals` int NOT NULL DEFAULT '9',
  `status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'enabled',
  `priority` int NOT NULL DEFAULT '100' COMMENT 'Ordering priority; lower numbers appear first',
  `logo` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blockchain_tokens`
--

INSERT INTO `blockchain_tokens` (`id`, `blockchain_id`, `symbol`, `name`, `mint_address`, `decimals`, `status`, `priority`, `logo`, `created_at`, `updated_at`) VALUES
(1, 1, 'SOL', 'Solana', NULL, 9, 'enabled', 50, 'assets/images/tokens/sol.png', '2026-07-27 19:22:01', '2026-08-06 14:09:22'),
(2, 1, 'USDC', 'USD Coin', 'EPjFWdd5AufqSSqeM2qN1xzybapC8G4wEGGkZwyTDt1v', 6, 'enabled', 20, 'assets/images/tokens/usdc.png', '2026-07-27 19:22:01', '2026-08-06 14:09:22'),
(3, 1, 'USDT', 'Tether USD', 'Es9vMFrzaCERmJfrF4H2FYD4KCoNkY11McCe8BenwNYB', 6, 'enabled', 10, 'assets/images/tokens/usdt.png', '2026-07-27 19:22:01', '2026-08-06 14:09:22'),
(4, 2, 'ETH', 'Ethereum', NULL, 18, 'enabled', 40, 'assets/images/tokens/eth.png', '2026-07-29 00:03:31', '2026-08-06 14:09:22'),
(5, 2, 'USDC', 'USD Coin', '0xA0b86991c6218b36c1d19D4a2e9Eb0cE3606eB48', 6, 'enabled', 20, 'assets/images/tokens/usdc.png', '2026-07-29 00:03:31', '2026-08-06 14:09:22'),
(6, 2, 'USDT', 'Tether USD', '0xdAC17F958D2ee523a2206206994597C13D831ec7', 6, 'enabled', 10, 'assets/images/tokens/usdt.png', '2026-07-29 00:03:31', '2026-08-06 14:09:22'),
(7, 3, 'BNB', 'BNB', NULL, 18, 'enabled', 70, 'assets/images/tokens/bnb.png', '2026-07-29 12:53:32', '2026-08-06 14:09:22'),
(8, 3, 'USDC', 'USD Coin', '0x8AC76a51cc950d9822D68b83fE1Ad97B32CD580d', 18, 'enabled', 20, 'assets/images/tokens/usdc.png', '2026-07-29 12:53:32', '2026-08-06 14:09:22'),
(9, 3, 'USDT', 'Tether USD', '0x55d398326f99059fF775485246999027B3197955', 18, 'enabled', 10, 'assets/images/tokens/usdt.png', '2026-07-29 12:53:32', '2026-08-06 14:09:22'),
(19, 1, 'JUP', 'Jupiter', 'JUPyiwrYJGwH171bXTJNcFMWrXqV22N4cT741Jkhc7v', 6, 'enabled', 250, 'assets/images/tokens/jup.png', '2026-07-29 17:51:26', '2026-08-06 14:09:23'),
(20, 1, 'PYTH', 'Pyth Network', 'HZ128PyWt5D8gdZs9eVEPF9aYqhHs7xNsBCr8UCvdEEw', 6, 'enabled', 280, 'assets/images/tokens/pyth.png', '2026-07-29 17:51:26', '2026-08-06 14:09:23'),
(21, 1, 'BONK', 'Bonk', 'DezXAZ8z7PnrFcPy8Gssg3qW88xhZaYCm1tdBc25AH97', 5, 'enabled', 170, 'assets/images/tokens/bonk.png', '2026-07-29 17:51:26', '2026-08-06 14:09:22'),
(22, 2, 'WBTC', 'Wrapped BTC', '0x2260FAC5E5542a773Aa44fBCfeDf7C193bc2C599', 8, 'enabled', 330, 'assets/images/tokens/btc.png', '2026-07-29 17:51:26', '2026-08-06 14:30:06'),
(23, 2, 'LINK', 'Chainlink', '0x514910771AF9Ca656af840dff83E8264EcF986CA', 18, 'enabled', 260, 'assets/images/tokens/link.png', '2026-07-29 17:51:26', '2026-08-06 14:09:23'),
(24, 2, 'UNI', 'Uniswap', '0x1f9840a85d5aF5bf1D1762F925BDADdC4201F984', 18, 'enabled', 300, 'assets/images/tokens/uni.png', '2026-07-29 17:51:26', '2026-08-06 14:09:23'),
(25, 2, 'BUSD', 'Binance USD', '0x4Fabb145d64652a948d72533023f6E7A623C7C53', 18, 'enabled', 110, 'assets/images/tokens/busd.png', '2026-07-29 17:51:26', '2026-08-06 14:09:22'),
(26, 3, 'WBTC', 'Wrapped BTC (BNB Chain)', '0x7130d2A12B9BCbFAe4f2634d864A1Ee1Ce3Ead9c', 18, 'enabled', 330, 'assets/images/tokens/btc.png', '2026-07-29 17:51:26', '2026-08-06 14:30:06'),
(27, 3, 'LINK', 'Chainlink', '0xF8A0BF9cF54bb92F17374d9e9A321E6a111A51bd', 18, 'enabled', 260, 'assets/images/tokens/link.png', '2026-07-29 17:51:26', '2026-08-06 14:09:23'),
(28, 3, 'CAKE', 'PancakeSwap', '0x0E09FaBB73Bd3Ade0a17ECC321fD13a19e81cE82', 18, 'enabled', 200, 'assets/images/tokens/cake.png', '2026-07-29 17:51:26', '2026-08-06 14:09:22'),
(29, 3, 'FDUSD', 'First Digital USD', '0xc5f0f7b66764F6ec8C8Dff7baeB41Fe1E0D1C1d1', 18, 'enabled', 120, 'assets/images/tokens/fdusd.png', '2026-07-29 17:51:26', '2026-08-06 14:09:22'),
(30, 3, 'WBNB', 'Wrapped BNB', '0xbb4CdB9CBd36B01bD1cBaEBF2De08d9173bc095c', 18, 'enabled', 100, 'assets/images/tokens/wbnb.png', '2026-07-29 17:51:26', '2026-07-30 12:16:43'),
(31, 3, 'BUSD', 'Binance USD', '0xe9e7CEA3DedcA5984780Bafc599bD69ADd087D56', 18, 'enabled', 110, 'assets/images/tokens/busd.png', '2026-07-29 17:51:26', '2026-08-06 14:09:22'),
(32, 3, 'XRP', 'Binance-Peg XRP', '0x1D2F0da169ceB9fC7B3144628dB156f3F6c60dBE', 18, 'enabled', 320, 'assets/images/tokens/xrp.png', '2026-07-29 17:51:26', '2026-08-06 14:09:23'),
(33, 4, 'ETH', 'Ethereum', NULL, 18, 'enabled', 40, 'assets/images/tokens/eth.png', '2026-07-29 21:28:38', '2026-08-06 14:09:22'),
(34, 4, 'USDC', 'USD Coin', '0x833589fCD6eDb6E08f4c7C32D4f71b54bda02913', 6, 'enabled', 20, 'assets/images/tokens/usdc.png', '2026-07-29 21:28:38', '2026-08-06 14:09:22'),
(35, 4, 'USDT', 'Tether USD', '0xfde4C96c8593536E31F229EA8f37b2ADa2699bb2', 6, 'enabled', 10, 'assets/images/tokens/usdt.png', '2026-07-29 21:28:38', '2026-08-06 14:09:22'),
(36, 4, 'WETH', 'Wrapped Ether', '0x4200000000000006', 18, 'enabled', 100, 'assets/images/tokens/weth.png', '2026-07-29 22:50:45', '2026-07-30 12:37:09'),
(37, 4, 'DAI', 'Dai Stablecoin', '0x50c5771a7ee4b5724d2677b27acec9e54a61406e', 18, 'enabled', 100, 'assets/images/tokens/dai.png', '2026-07-29 22:50:45', '2026-08-06 14:09:22'),
(38, 4, 'LINK', 'Chainlink', '0xf869c8c9c6ec5960c6895c258d60920d3f0a441c2', 18, 'enabled', 260, 'assets/images/tokens/link.png', '2026-07-29 22:50:45', '2026-08-06 14:09:23'),
(39, 4, 'cbBTC', 'Coinbase Wrapped BTC', '0xcbB7C0000aB88B473b1be400030010303E8ecbe2', 8, 'enabled', 150, 'assets/images/tokens/cbbtc.png', '2026-07-29 22:50:45', '2026-08-06 14:09:22'),
(40, 4, 'AERO', 'Aerodrome', '0x940181a94a35a4569e4529a3cdfb74e38fd98631', 18, 'enabled', 160, 'assets/images/tokens/aero.png', '2026-07-29 22:50:45', '2026-08-06 14:09:22'),
(41, 4, 'BRETT', 'Brett', '0x532f27101965dd16d7a4b0e527d2c3df8398ff06', 18, 'enabled', 180, 'assets/images/tokens/brett.png', '2026-07-29 22:50:45', '2026-08-06 14:09:22'),
(42, 5, 'POL', 'POL', NULL, 18, 'enabled', 90, 'assets/images/tokens/pol.png', '2026-07-29 22:58:26', '2026-08-06 14:09:22'),
(43, 5, 'USDC', 'USD Coin', '0x3c499c542cEF5E3811e1192ce70d8cC03d5c3359', 6, 'enabled', 20, 'assets/images/tokens/usdc.png', '2026-07-29 22:58:26', '2026-08-06 14:09:22'),
(44, 5, 'USDC.e', 'USD Coin (Bridged)', '0x2791Bca1f2de4661ED88A30C99A7a9449Aa84174', 6, 'enabled', 140, 'assets/images/tokens/usdc.e.png', '2026-07-29 22:58:26', '2026-08-06 14:09:22'),
(45, 5, 'USDT', 'Tether USD', '0xc2132D05D31c914a87C6611C10748AEb04B58e8F', 6, 'enabled', 10, 'assets/images/tokens/usdt.png', '2026-07-29 22:58:26', '2026-08-06 14:09:22'),
(46, 6, 'ETH', 'Ethereum', NULL, 18, 'enabled', 40, 'assets/images/tokens/eth.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(47, 6, 'USDC', 'USD Coin', '0xaf88d065e77c8cC2239327C5EDb3A432268e5831', 6, 'enabled', 20, 'assets/images/tokens/usdc.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(48, 6, 'USDC.e', 'USD Coin (Bridged)', '0xFF970A61A04b6511af650kb0082f4007b8b4081c', 6, 'enabled', 140, 'assets/images/tokens/usdc.e.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(49, 6, 'USDT', 'Tether USD', '0xFd086bC7CD5C481DCC9C85ebE478A1C0b69FCbb9', 6, 'enabled', 10, 'assets/images/tokens/usdt.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(50, 7, 'ETH', 'Ethereum', NULL, 18, 'enabled', 40, 'assets/images/tokens/eth.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(51, 7, 'USDC', 'USD Coin', '0x0b2C639c533813f4Aa9d7837CAf62653d097Ff85', 6, 'enabled', 20, 'assets/images/tokens/usdc.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(52, 7, 'USDC.e', 'USD Coin (Bridged)', '0x7F5c764cBc14f9669B88837ca1490cCa17c31607', 6, 'enabled', 140, 'assets/images/tokens/usdc.e.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(53, 7, 'USDT', 'Tether USD', '0x94b008aA00579c1307B0EF2c489296F82574e4e8', 6, 'enabled', 10, 'assets/images/tokens/usdt.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(54, 8, 'AVAX', 'Avalanche', NULL, 18, 'enabled', 80, 'assets/images/tokens/avax.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(55, 8, 'USDC', 'USD Coin', '0xB97EF1ec3Efe14901427b265120DE1726571c6e6', 6, 'enabled', 20, 'assets/images/tokens/usdc.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(56, 8, 'USDC.e', 'USD Coin (Bridged)', '0xA7D8e908D230C6977813100a66CE9A4c47b51b75', 6, 'enabled', 140, 'assets/images/tokens/usdc.e.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(57, 8, 'USDT', 'Tether USD', '0x9702230A8Ea53601f5cD2dc00fDBc13d4Df4A8c7', 6, 'enabled', 10, 'assets/images/tokens/usdt.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(58, 9, 'FTM', 'Fantom', NULL, 18, 'enabled', 230, 'assets/images/tokens/ftm.png', '2026-07-30 05:37:53', '2026-08-06 14:09:23'),
(59, 9, 'USDC', 'USD Coin', '0x28a92dac19cca1b988734103b1e6b6ae7e012f28', 6, 'enabled', 20, 'assets/images/tokens/usdc.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(60, 9, 'USDT', 'Tether USD', '0x04068DA6C83AFCFA0e13ba15A6696662335D5B75', 6, 'enabled', 10, 'assets/images/tokens/usdt.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(61, 10, 'CRO', 'Cronos', NULL, 18, 'enabled', 220, 'assets/images/tokens/cro.png', '2026-07-30 05:37:53', '2026-08-06 14:09:23'),
(62, 10, 'USDC', 'USD Coin', '0xc21223249CAcdE18f4C7C32D4f71b54bda029130', 6, 'enabled', 20, 'assets/images/tokens/usdc.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(63, 10, 'USDT', 'Tether USD', '0x66e42df0808088b7e0808a00400466b0a72c44bc', 6, 'enabled', 10, 'assets/images/tokens/usdt.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(64, 11, 'ETH', 'Ethereum', NULL, 18, 'enabled', 40, 'assets/images/tokens/eth.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(65, 11, 'USDC', 'USD Coin', '0x1a0d538f4fbe279c15b47c840d0293ae96314482', 6, 'enabled', 20, 'assets/images/tokens/usdc.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(66, 11, 'USDT', 'Tether USD', '0xA21943e8DDF2aB1109000a294d07b0c78a06362d', 6, 'enabled', 10, 'assets/images/tokens/usdt.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(67, 12, 'ETH', 'Ethereum', NULL, 18, 'enabled', 40, 'assets/images/tokens/eth.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(68, 12, 'USDC', 'USD Coin', '0x06efb589c3c5f5a4df523b3ef3082a620d3f0a44', 6, 'enabled', 20, 'assets/images/tokens/usdc.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(69, 12, 'USDT', 'Tether USD', '0xf55bfdd40f0a58775c3303b10c40acbaf6e377c0', 6, 'enabled', 10, 'assets/images/tokens/usdt.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(70, 13, 'ETH', 'Ethereum', NULL, 18, 'enabled', 40, 'assets/images/tokens/eth.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(71, 13, 'USDC', 'USD Coin', '0x1d1711E89ae664158433375038025b4E2620CcCc', 6, 'enabled', 20, 'assets/images/tokens/usdc.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(72, 13, 'USDT', 'Tether USD', '0x4932F7F61A04B6511af650kB0082f4007B8b4081', 6, 'enabled', 10, 'assets/images/tokens/usdt.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(73, 14, 'CELO', 'Celo', NULL, 18, 'enabled', 210, 'assets/images/tokens/celo.png', '2026-07-30 05:37:53', '2026-08-06 14:09:23'),
(74, 14, 'USDC', 'USD Coin', '0x765DEc818c4e0f2491d75d6de31e34F582875e6d', 6, 'enabled', 20, 'assets/images/tokens/usdc.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(75, 14, 'USDT', 'Tether USD', '0x48068da6c83afcfa0e13ba15a6696662335d5b75', 6, 'enabled', 10, 'assets/images/tokens/usdt.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(76, 15, 'MNT', 'Mantle', NULL, 18, 'enabled', 270, 'assets/images/tokens/mnt.png', '2026-07-30 05:37:53', '2026-08-06 14:09:23'),
(77, 15, 'USDC', 'USD Coin', '0x09ecb58d6c451364b917284e3f45ed6c5777838c5', 6, 'enabled', 20, 'assets/images/tokens/usdc.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(78, 15, 'USDT', 'Tether USD', '0x201E8480d2c4e0d0293ae9631448D293a79afab1', 6, 'enabled', 10, 'assets/images/tokens/usdt.png', '2026-07-30 05:37:53', '2026-08-06 14:09:22'),
(87, 16, 'TRX', 'TRON', NULL, 6, 'enabled', 60, 'assets/images/tokens/trx.png', '2026-07-30 18:57:52', '2026-08-06 14:09:22'),
(88, 16, 'USDT', 'Tether USD', 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t', 6, 'enabled', 10, 'assets/images/tokens/usdt.png', '2026-07-30 18:57:52', '2026-08-06 14:09:22'),
(89, 16, 'USDC', 'USD Coin', 'TEkxiTehnzSmSe2XqrBj4w32RUN966rdz8', 6, 'enabled', 20, 'assets/images/tokens/usdc.png', '2026-07-30 18:57:52', '2026-08-06 14:09:22'),
(90, 16, 'USDD', 'Decentralized USD', 'TPYmHEhy5n8TCEfYGqW2rPxsghSfzghPDn', 18, 'enabled', 130, 'assets/images/tokens/usdd.png', '2026-07-30 18:57:52', '2026-08-06 14:09:22'),
(91, 16, 'JST', 'JUST', 'TCFLL5dx5ZJdKnWuesXxi1VPwjLVmWZZy9', 18, 'enabled', 240, 'assets/images/tokens/jst.png', '2026-07-30 18:57:52', '2026-08-06 14:09:23'),
(92, 16, 'SUN', 'SUN Token', 'TSSMHYeV2uE9qYH95DqyoCuNCzEL1NvU3S', 18, 'enabled', 290, 'assets/images/tokens/sun.png', '2026-07-30 18:57:52', '2026-08-06 14:09:23'),
(93, 16, 'WIN', 'WINkLink', 'TLa2f6VPqDgRE67v1736s7bJ8Ray5wYjU7', 6, 'enabled', 310, 'assets/images/tokens/win.png', '2026-07-30 18:57:52', '2026-08-06 14:09:23'),
(94, 16, 'BTT', 'BitTorrent', 'TAFjULxiVgT4qWk6UZwjqwZXTSaGaqnVp4', 18, 'enabled', 190, 'assets/images/tokens/btt.png', '2026-07-30 18:57:52', '2026-08-06 14:09:22'),
(97, 17, 'BTC', 'Bitcoin', NULL, 8, 'enabled', 30, 'assets/images/tokens/btc.png', '2026-08-05 19:04:23', '2026-08-06 14:22:35');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `client_reviews`
--

CREATE TABLE `client_reviews` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `review` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` int NOT NULL DEFAULT '5',
  `image` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `client_reviews`
--

INSERT INTO `client_reviews` (`id`, `name`, `role`, `review`, `rating`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Alexander Novak', 'Private Equity Analyst', 'The matching engine is significantly faster than any retail terminal I\'ve used. The execution speed during high-volatility events is truly institutional.', 5, 'reviewer_1.png', '2026-02-26 07:56:10', '2026-02-26 07:56:10'),
(2, 'Dr. Sarah Chen', 'Hedge Fund Manager', 'Integration of traditional assets with liquid crypto futures is seamless. Their compliance-first approach gives us the confidence to scale our strategies.', 5, 'reviewer_2.png', '2026-02-26 07:56:10', '2026-02-26 07:56:10'),
(3, 'Jameson Vane', 'Serial Tech Investor', 'The platform doesn\'t just offer a platform; they offer a competitive advantage. The ROI calculator is spot on, and the transparency is refreshing in this space.', 5, 'reviewer_3.png', '2026-02-26 07:56:10', '2026-02-26 07:56:10'),
(4, 'Michael Sterling', 'Compliance Officer', 'The regulatory framework and real-time audit logs are exceptional. It\'s rare to find a platform that prioritizes security without sacrificing performance.', 5, NULL, '2026-02-26 07:56:10', '2026-02-26 07:56:10'),
(5, 'Elena Rodriguez', 'Quantitative Trader', 'API documentation is clean and the latency is minimal. Integrating our algorithmic models was straightforward and the results have been consistent.', 5, NULL, '2026-02-26 07:56:11', '2026-02-26 07:56:11'),
(6, 'David Chang', 'Wealth Manager', 'Our clients appreciate the transparent fee structure and the diverse asset classes. The portfolio management tools are top-tier.', 5, NULL, '2026-02-26 07:56:11', '2026-02-26 07:56:11'),
(7, 'Sophia Loren', 'Venture Capitalist', 'The strategic vision of the leadership team is clearly reflected in the product. It’s a robust bridge between traditional and digital finance.', 5, NULL, '2026-02-26 07:56:11', '2026-02-26 07:56:11'),
(8, 'Marcus Thorne', 'Risk Strategist', 'Their risk mitigation protocols are the best I’ve seen. The platform handles extreme market conditions with impressive stability.', 5, NULL, '2026-02-26 07:56:11', '2026-02-26 07:56:11'),
(9, 'Isabella Gomez', 'Fintech Advisor', 'User experience is intuitive yet powerful. They’ve successfully demystified complex trading instruments for institutional-grade users.', 5, NULL, '2026-02-26 07:56:11', '2026-02-26 07:56:11'),
(10, 'Robert Miller', 'Asset Allocation Lead', 'The depth of liquidity across all pairs is remarkable. We’ve been able to execute large orders with minimal slippage consistently.', 5, NULL, '2026-02-26 07:56:11', '2026-02-26 07:56:11');

-- --------------------------------------------------------

--
-- Table structure for table `copy_tradings`
--

CREATE TABLE `copy_tradings` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pair` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `roi` decimal(18,2) NOT NULL,
  `amount_type` enum('manual','percentage') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `percentage` decimal(5,2) DEFAULT NULL,
  `expires_at` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `copy_trading_histories`
--

CREATE TABLE `copy_trading_histories` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `copy_trading_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(24,2) NOT NULL,
  `pair` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `copy_code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `roi` decimal(12,2) NOT NULL,
  `profit` decimal(24,2) DEFAULT NULL,
  `status` enum('active','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `completes_at` bigint UNSIGNED DEFAULT NULL,
  `activated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cron_jobs`
--

CREATE TABLE `cron_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `command` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `recommended` int NOT NULL,
  `last_run` bigint UNSIGNED NOT NULL,
  `module` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cron_jobs`
--

INSERT INTO `cron_jobs` (`id`, `name`, `description`, `command`, `recommended`, `last_run`, `module`, `created_at`, `updated_at`) VALUES
(1, 'Notification Cleaner', 'Cleans up old, already-read notification messages to keep the database fast.', 'notification_cleaner', 86400, 1785762539, NULL, '2026-02-26 05:28:12', '2026-09-04 19:46:47'),
(3, 'Background Task Queue', 'Sends system emails, security alerts, and processes pending jobs quickly.', 'queue_worker', 30, 1785773637, NULL, '2026-02-26 05:28:12', '2026-08-03 17:13:57'),
(4, 'Expired Deposit Cleaner', 'Cancels abandoned deposits that were not paid within the expiration window.', 'deposit_reconciler', 60, 1785773626, NULL, '2026-02-26 05:28:12', '2026-09-04 19:46:47'),
(9, 'System Log Cleaner', 'Trims large system log files to save server disk space and maintain performance.', 'log_cleaner', 3600, 1785771144, NULL, '2026-02-26 05:28:13', '2026-09-04 19:46:47'),
(10, 'Market Rates & Sitemap Updater', 'Updates market prices and keeps your website sitemap fresh for search engines.', 'resource_indexer', 1800, 1785773635, NULL, '2026-02-26 05:28:13', '2026-09-04 19:46:47'),
(11, 'Trading Bots Engine', 'Executes active trading bots and calculates daily profits for users.', 'trading_bots_engine', 60, 1784647124, 'trading_bot_module', '2026-07-27 16:18:44', '2026-09-04 19:46:47'),
(12, 'Copy Trading Synchronizer', 'Settles completed copy trades and distributes profits to investors.', 'copy_trading_sync', 60, 1785773622, 'copy_trading_module', '2026-07-27 16:18:46', '2026-09-04 19:46:47'),
(13, 'Crypto Deposit Monitor', 'Checks blockchains for incoming customer deposits and credits their accounts.', 'deposit_monitor', 30, 1786967453, NULL, '2026-08-17 12:50:53', '2026-09-04 19:46:47');

-- --------------------------------------------------------

--
-- Table structure for table `deposits`
--

CREATE TABLE `deposits` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `blockchain_id` bigint UNSIGNED DEFAULT NULL,
  `blockchain_token_id` bigint UNSIGNED DEFAULT NULL,
  `amount` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `converted_amount` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fee_percent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fee_amount` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_amount` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exchange_rate` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_proof` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expires_at` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `structured_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '{"crypto":{"transaction_hash":"b7f3e2a1c9d84f6e0a5b3c7d9e1f4a8b2c6d5e7f9a0b1c2d3e4f5a6b7c8","wallet_address":"TN1Z3YwRk9sQH2LJ8a6Xx4EwQb5F3M7P9C","currency":"USDT","network":"TRC20"},"bank_transfer":{"bank_name":"Chase Bank","account_holder":"John Doe","account_number":"1234567890","routing_number":"021000021 (nullable)","swift":"CHASUS33 (nullable)"},"digital_wallet":{"payment_id":"PAY-84739201","identity_id":"johndoe@email.com \\/ johndoe123"}}',
  `auto_res_dump` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Dump of response from third party payment provider',
  `status` enum('pending','completed','failed','partial_payment') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exchange_connections`
--

CREATE TABLE `exchange_connections` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `exchange` enum('binance','bybit','mexc') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'binance',
  `market_type` enum('spot','futures') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'spot',
  `api_key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `api_secret` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `api_key_hint` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `label` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_total_balance` decimal(24,8) NOT NULL DEFAULT '0.00000000',
  `last_synced_at` bigint DEFAULT NULL,
  `last_error` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `exchange_connections_user_exchange_market_unique` (`user_id`,`exchange`,`market_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` bigint UNSIGNED NOT NULL,
  `question` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'General',
  `sort_order` int NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`id`, `question`, `answer`, `category`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'How does automated AI quantitative trading work on :name?', ':name deploys high-frequency quantitative algorithms that continuously analyze market order books, tick data, and cross-exchange arbitrage liquidity spreads 24/7. When algorithmic entry conditions are met, trades are executed automatically with zero emotional bias.', 'AI Bots', 1, 1, '2026-08-16 15:22:39', '2026-08-16 15:22:39'),
(2, 'How does one-click Copy Trading operate?', 'With One-Click Copy Trading, you enter a Master Trader\'s verified copy code (e.g. SATO-QUANT-01) on :name. Every order entry, take-profit target, and stop-loss executed by the Master Trader is synchronized to your account in real-time with under 12ms latency.', 'Copy Trading', 2, 1, '2026-08-16 15:22:39', '2026-08-16 15:22:39'),
(3, 'Are my funds locked, and how do I request withdrawals?', 'Your trading capital is held in isolated sub-accounts during the strategy lockup term (e.g. 7, 15, or 30 days). Daily yield profits are credited continuously, and on-chain withdrawal requests to your Web3 wallet are processed instantly via direct blockchain smart contracts.', 'Capital & Withdrawals', 3, 1, '2026-08-16 15:22:39', '2026-08-16 15:22:39'),
(4, 'What risk management safeguards are built into :name?', ':name enforces strict quantitative risk controls including automated stop-loss thresholds, maximum drawdown circuit breakers, and 100% non-custodial capital settlement to ensure investor capital protection.', 'Security & Risk', 4, 1, '2026-08-16 15:22:39', '2026-08-16 15:22:39'),
(5, 'Which cryptocurrencies and blockchain networks are supported for settlement?', ':name supports direct on-chain deposits and profit withdrawals across Bitcoin (BTC), Ethereum (ETH), Solana (SOL), BNB Chain, Polygon, Arbitrum, Avalanche, Optimism, Base, Tether (USDT), and USD Coin (USDC).', 'On-Chain Infrastructure', 5, 1, '2026-08-16 15:22:39', '2026-08-16 15:22:39');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kycs`
--

CREATE TABLE `kycs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_front` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_back` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `selfie` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proof_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `rejection_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `management_teams`
--

CREATE TABLE `management_teams` (
  `id` bigint UNSIGNED NOT NULL,
  `role` enum('ceo','cto','coo','cmo','cfo','quant','others') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `management_teams`
--

INSERT INTO `management_teams` (`id`, `role`, `name`, `image`, `description`, `created_at`, `updated_at`) VALUES
(1, 'ceo', 'Alexander Vance', 'ceo.png', 'With over 18 years in quantitative finance and fintech leadership, Alexander guides :site_name\'s mission to make automated algorithmic trading accessible to all investors.', '2026-02-26 07:30:56', '2026-08-18 12:42:14'),
(2, 'cto', 'Maya Sterling', 'cto.png', 'Specializing in ultra-low latency order routing and blockchain infrastructure, Maya directs core architecture, multi-exchange API gateways, and cybersecurity systems.', '2026-02-26 07:30:57', '2026-08-18 12:42:14'),
(3, 'quant', 'Dr. Nathan Cole', 'asset_head.png', 'PhD in Computational Finance, Dr. Cole oversees algorithmic model development, predictive volatility forecasting, and institutional risk management protocols.', '2026-02-26 07:30:58', '2026-08-18 12:42:14'),
(4, 'coo', 'Claire Dupont', 'compliance_head.png', 'Expert in international financial compliance and digital asset regulation, Claire directs global governance, regulatory standards, and institutional operational security.', '2026-02-26 07:30:58', '2026-08-18 12:42:14');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` bigint UNSIGNED NOT NULL,
  `label` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `route_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `route_wildcard` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `params` json DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type` enum('user','admin','frontend') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'frontend',
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `label`, `route_name`, `route_wildcard`, `params`, `url`, `icon`, `type`, `parent_id`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Dashboard', 'user.dashboard', NULL, NULL, NULL, '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"20\" height=\"20\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><rect width=\"7\" height=\"9\" x=\"3\" y=\"3\" rx=\"1\" /><rect width=\"7\" height=\"5\" x=\"14\" y=\"3\" rx=\"1\" /><rect width=\"7\" height=\"9\" x=\"14\" y=\"12\" rx=\"1\" /><rect width=\"7\" height=\"5\" x=\"3\" y=\"16\" rx=\"1\" /></svg>', 'user', NULL, 1, 1, '2026-02-25 07:13:45', '2026-02-25 07:13:45'),
(2, 'KYC', 'user.kyc', NULL, NULL, NULL, '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"20\" height=\"20\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z\"/><path d=\"m9 12 2 2 4-4\"/></svg>', 'user', NULL, 2, 1, '2026-02-25 07:13:45', '2026-08-17 13:55:55'),
(3, 'Deposits', NULL, NULL, NULL, '#', '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"20\" height=\"20\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"M20 7h-9\"/><path d=\"M14 17H5\"/><circle cx=\"17\" cy=\"17\" r=\"3\"/><path d=\"M7 7h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h12\"/></svg>', 'user', NULL, 3, 1, '2026-02-25 07:13:45', '2026-02-25 07:13:45'),
(4, 'Deposit History', 'user.deposits.index', 'user.deposits.*', NULL, NULL, NULL, 'user', 3, 1, 1, '2026-02-25 07:13:45', '2026-02-25 07:13:45'),
(5, 'New Deposit', 'user.deposits.new', 'user.deposits.*', NULL, NULL, NULL, 'user', 3, 2, 1, '2026-02-25 07:13:45', '2026-02-25 07:13:45'),
(6, 'Pending Deposits', 'user.deposits.pending', 'user.deposits.*', NULL, NULL, NULL, 'user', 3, 3, 1, '2026-02-25 07:13:45', '2026-02-25 07:13:45'),
(7, 'Approved Deposits', 'user.deposits.approved', 'user.deposits.*', NULL, NULL, NULL, 'user', 3, 4, 1, '2026-02-25 07:13:45', '2026-02-25 07:13:45'),
(8, 'Failed Deposits', 'user.deposits.failed', 'user.deposits.*', NULL, NULL, NULL, 'user', 3, 5, 1, '2026-02-25 07:13:45', '2026-02-25 07:13:45'),
(33, 'Withdrawals', NULL, NULL, NULL, '#', '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"20\" height=\"20\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><rect width=\"20\" height=\"12\" x=\"2\" y=\"6\" rx=\"2\"/><circle cx=\"12\" cy=\"12\" r=\"2\"/><path d=\"M6 12h.01M18 12h.01\"/></svg>', 'user', NULL, 9, 1, '2026-02-25 07:13:46', '2026-02-25 07:13:46'),
(34, 'Withdrawal History', 'user.withdrawals.index', 'user.withdrawals.*', NULL, NULL, NULL, 'user', 33, 1, 1, '2026-02-25 07:13:46', '2026-02-25 07:13:46'),
(35, 'New Withdrawal', 'user.withdrawals.new', 'user.withdrawals.*', NULL, NULL, NULL, 'user', 33, 2, 1, '2026-02-25 07:13:46', '2026-02-25 07:13:46'),
(36, 'Pending Withdrawals', 'user.withdrawals.pending', 'user.withdrawals.*', NULL, NULL, NULL, 'user', 33, 3, 1, '2026-02-25 07:13:46', '2026-02-25 07:13:46'),
(37, 'Approved Withdrawals', 'user.withdrawals.approved', 'user.withdrawals.*', NULL, NULL, NULL, 'user', 33, 4, 1, '2026-02-25 07:13:46', '2026-02-25 07:13:46'),
(38, 'Failed Withdrawals', 'user.withdrawals.failed', 'user.withdrawals.*', NULL, NULL, NULL, 'user', 33, 5, 1, '2026-02-25 07:13:46', '2026-02-25 07:13:46'),
(39, 'Transactions', 'user.transactions', NULL, NULL, NULL, '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"20\" height=\"20\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"M8 3 4 7l4 4\"/><path d=\"M4 7h16\"/><path d=\"m16 21 4-4-4-4\"/><path d=\"M20 17H4\"/></svg>', 'user', NULL, 10, 1, '2026-02-25 07:13:46', '2026-02-25 07:13:46'),
(40, 'Referrals', 'user.referrals', NULL, NULL, NULL, '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"20\" height=\"20\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2\"/><circle cx=\"9\" cy=\"7\" r=\"4\"/><path d=\"M22 21v-2a4 4 0 0 0-3-3.87\"/><path d=\"M16 3.13a4 4 0 0 1 0 7.75\"/></svg>', 'user', NULL, 11, 1, '2026-02-25 07:13:46', '2026-02-25 07:13:46'),
(41, 'Dashboard', 'admin.dashboard', NULL, NULL, NULL, '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"20\" height=\"20\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><rect width=\"7\" height=\"9\" x=\"3\" y=\"3\" rx=\"1\" /><rect width=\"7\" height=\"5\" x=\"14\" y=\"3\" rx=\"1\" /><rect width=\"7\" height=\"9\" x=\"14\" y=\"12\" rx=\"1\" /><rect width=\"7\" height=\"5\" x=\"3\" y=\"16\" rx=\"1\" /></svg>', 'admin', NULL, 0, 1, '2026-02-25 07:13:46', '2026-02-25 11:07:57'),
(42, 'Users', NULL, NULL, NULL, '#', '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"20\" height=\"20\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2\"/><circle cx=\"9\" cy=\"7\" r=\"4\"/><path d=\"M22 21v-2a4 4 0 0 0-3-3.87\"/><path d=\"M16 3.13a4 4 0 0 1 0 7.75\"/></svg>', 'admin', NULL, 4, 1, '2026-02-25 07:13:46', '2026-02-25 11:07:57'),
(43, 'All Users', 'admin.users.index', 'admin.users.*', NULL, NULL, NULL, 'admin', 42, 1, 1, '2026-02-25 07:13:46', '2026-02-25 07:13:46'),
(44, 'KYC Verified', 'admin.users.index', 'admin.users.*', '{\"kyc_status\": \"approved\"}', NULL, NULL, 'admin', 42, 1, 1, '2026-02-25 07:13:46', '2026-08-17 13:55:55'),
(45, 'Email Verified', 'admin.users.index', 'admin.users.*', '{\"email_verified\": \"1\"}', NULL, NULL, 'admin', 42, 2, 1, '2026-02-25 07:13:46', '2026-02-25 07:13:46'),
(46, 'Active Users', 'admin.users.index', 'admin.users.*', '{\"status\": \"active\"}', NULL, NULL, 'admin', 42, 3, 1, '2026-02-25 07:13:46', '2026-02-25 07:13:46'),
(47, 'Banned Users', 'admin.users.index', 'admin.users.*', '{\"status\": \"banned\"}', NULL, NULL, 'admin', 42, 4, 1, '2026-02-25 07:13:46', '2026-02-25 07:13:46'),
(48, 'Bulk Email', 'admin.users.bulk-email', 'admin.users.*', NULL, NULL, NULL, 'admin', 42, 5, 1, '2026-02-25 07:13:46', '2026-02-25 07:13:46'),
(57, 'Deposits', NULL, NULL, NULL, '#', '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"20\" height=\"20\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\"><path d=\"M3 7h14a3 3 0 0 1 3 3v7a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V7Z\" /><path d=\"M17 7V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v1\" /><path d=\"M20 12h-4a2 2 0 0 0 0 4h4\" /><path d=\"M12 10v6\" /><path d=\"M9.5 13.5 12 16l2.5-2.5\" /></svg>', 'admin', NULL, 2, 1, '2026-02-25 07:13:46', '2026-02-25 11:07:57'),
(58, 'All Deposits', 'admin.deposits.index', 'admin.deposits.*', NULL, NULL, NULL, 'admin', 57, 1, 1, '2026-02-25 07:13:46', '2026-02-25 07:13:46'),
(59, 'Pending Deposits', 'admin.deposits.index', 'admin.deposits.*', '{\"status\": \"pending\"}', NULL, NULL, 'admin', 57, 2, 1, '2026-02-25 07:13:46', '2026-02-25 07:13:46'),
(60, 'Completed Deposits', 'admin.deposits.index', 'admin.deposits.*', '{\"status\": \"completed\"}', NULL, NULL, 'admin', 57, 3, 1, '2026-02-25 07:13:46', '2026-02-25 07:13:46'),
(61, 'Failed Deposits', 'admin.deposits.index', 'admin.deposits.*', '{\"status\": \"failed\"}', NULL, NULL, 'admin', 57, 4, 1, '2026-02-25 07:13:46', '2026-02-25 07:13:46'),
(62, 'Withdrawals', NULL, NULL, NULL, '#', '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"20\" height=\"20\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\"><path d=\"M3 7h14a3 3 0 0 1 3 3v7a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V7Z\" /><path d=\"M17 7V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v1\" /><path d=\"M20 12h-4a2 2 0 0 0 0 4h4\" /><path d=\"M12 16V10\" /><path d=\"M9.5 12.5 12 10l2.5 2.5\" /></svg>', 'admin', NULL, 3, 1, '2026-02-25 07:13:46', '2026-02-25 11:07:57'),
(63, 'All Withdrawals', 'admin.withdrawals.index', 'admin.withdrawals.*', NULL, NULL, NULL, 'admin', 62, 1, 1, '2026-02-25 07:13:47', '2026-02-25 07:13:47'),
(64, 'Pending Withdrawals', 'admin.withdrawals.index', 'admin.withdrawals.*', '{\"status\": \"pending\"}', NULL, NULL, 'admin', 62, 2, 1, '2026-02-25 07:13:47', '2026-02-25 07:13:47'),
(65, 'Completed Withdrawals', 'admin.withdrawals.index', 'admin.withdrawals.*', '{\"status\": \"completed\"}', NULL, NULL, 'admin', 62, 3, 1, '2026-02-25 07:13:47', '2026-02-25 07:13:47'),
(66, 'Failed Withdrawals', 'admin.withdrawals.index', 'admin.withdrawals.*', '{\"status\": \"failed\"}', NULL, NULL, 'admin', 62, 4, 1, '2026-02-25 07:13:47', '2026-02-25 07:13:47'),
(88, 'KYC Records', NULL, NULL, NULL, '#', '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\"><path d=\"M12 3l8 4v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V7l8-4Z\" /><path d=\"M9 12a3 3 0 1 0 6 0a3 3 0 0 0-6 0Z\" /><path d=\"M8.5 18c1.1-1.6 2.8-2.5 3.5-2.5s2.4.9 3.5 2.5\" /><path d=\"M15.8 10.8l1.2 1.2 2.4-2.4\" /></svg>', 'admin', NULL, 11, 1, '2026-02-25 07:13:47', '2026-08-17 13:55:55'),
(89, 'All KYC Records', 'admin.kyc.index', 'admin.kyc.*', NULL, NULL, NULL, 'admin', 88, 1, 1, '2026-02-25 07:13:47', '2026-08-17 13:55:55'),
(90, 'Pending', 'admin.kyc.index', 'admin.kyc.*', '{\"status\": \"pending\"}', NULL, NULL, 'admin', 88, 2, 1, '2026-02-25 07:13:47', '2026-02-25 07:13:47'),
(91, 'Approved', 'admin.kyc.index', 'admin.kyc.*', '{\"status\": \"approved\"}', NULL, NULL, 'admin', 88, 3, 1, '2026-02-25 07:13:47', '2026-02-25 07:13:47'),
(92, 'Rejected', 'admin.kyc.index', 'admin.kyc.*', '{\"status\": \"rejected\"}', NULL, NULL, 'admin', 88, 4, 1, '2026-02-25 07:13:47', '2026-02-25 07:13:47'),
(93, 'Transactions', 'admin.transactions.index', 'admin.transactions.*', NULL, NULL, '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\"><path d=\"M7 7h12\" /><path d=\"M15 3l4 4-4 4\" /><path d=\"M17 17H5\" /><path d=\"M9 21l-4-4 4-4\" /></svg>', 'admin', NULL, 12, 1, '2026-02-25 07:13:47', '2026-02-25 11:07:57'),
(94, 'Referrals', 'admin.referrals.index', 'admin.referrals.*', NULL, NULL, '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"> <circle cx=\"12\" cy=\"6\" r=\"2\"/> <circle cx=\"4\" cy=\"18\" r=\"2\"/> <circle cx=\"12\" cy=\"18\" r=\"2\"/> <circle cx=\"20\" cy=\"18\" r=\"2\"/> <path d=\"M12 8v4\"/> <path d=\"M12 12H4\"/> <path d=\"M12 12H20\"/> <path d=\"M12 12v4\"/> </svg>', 'admin', NULL, 13, 1, '2026-02-25 07:13:47', '2026-02-25 11:07:57'),
(95, 'Settings', 'admin.settings.index', 'admin.settings.*', NULL, NULL, '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"> <circle cx=\"12\" cy=\"12\" r=\"3\"/><path d=\"M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3h0 a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8 1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1Z\"/> </svg>', 'admin', NULL, 14, 1, '2026-02-25 07:13:47', '2026-02-25 11:07:57'),
(96, 'File Manager', 'admin.file-manager.index', 'admin.file-manager.*', NULL, NULL, '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"1.8\" stroke-linecap=\"round\" stroke-linejoin=\"round\"> <path d=\"M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v2H3z\"/> <rect x=\"3\" y=\"9\" width=\"18\" height=\"10\" rx=\"2\"/> <rect x=\"7\" y=\"12\" width=\"2\" height=\"2\" rx=\".3\"/> <rect x=\"11\" y=\"12\" width=\"2\" height=\"2\" rx=\".3\"/> <rect x=\"15\" y=\"12\" width=\"2\" height=\"2\" rx=\".3\"/> </svg>', 'admin', NULL, 15, 1, '2026-02-25 07:13:47', '2026-08-17 13:55:55'),
(97, 'Update', 'admin.update.index', 'admin.update.*', NULL, NULL, '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"1.8\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><polyline points=\"23 4 23 10 17 10\"></polyline><polyline points=\"1 20 1 14 7 14\"></polyline><path d=\"M3.5 9a9 9 0 0 1 14.1-3.4L23 10M1 14l5.4 4.4A9 9 0 0 0 20.5 15\"></path></svg>', 'admin', NULL, 30, 1, '2026-07-27 16:18:30', '2026-07-27 16:18:30'),
(98, 'Trading Bots', NULL, 'admin.trading-bots.*', NULL, '#', '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"20\" height=\"20\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"M12 8V4H8\"/><rect width=\"16\" height=\"12\" x=\"4\" y=\"8\" rx=\"2\"/><path d=\"M2 14h2\"/><path d=\"M20 14h2\"/><path d=\"M15 13v2\"/><path d=\"M9 13v2\"/></svg>', 'admin', NULL, 7, 1, '2026-07-27 16:18:41', '2026-08-17 13:55:54'),
(99, 'Bot Manager', 'admin.trading-bots.index', 'admin.trading-bots.index', NULL, NULL, NULL, 'admin', 98, 1, 1, '2026-07-27 16:18:41', '2026-07-27 16:18:41'),
(100, 'Bot Activations', 'admin.trading-bots.activations.index', 'admin.trading-bots.activations.*', NULL, NULL, NULL, 'admin', 98, 2, 1, '2026-07-27 16:18:41', '2026-07-27 16:18:41'),
(101, 'Trading Logs', 'admin.trading-bots.logs.index', 'admin.trading-bots.logs.*', NULL, NULL, NULL, 'admin', 98, 3, 1, '2026-07-27 16:18:41', '2026-07-27 16:18:41'),
(102, 'Trading Bots', NULL, 'user.trading-bots.*', NULL, '#', '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"20\" height=\"20\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"M12 8V4H8\"/><rect width=\"16\" height=\"12\" x=\"4\" y=\"8\" rx=\"2\"/><path d=\"M2 14h2\"/><path d=\"M20 14h2\"/><path d=\"M15 13v2\"/><path d=\"M9 13v2\"/></svg>', 'user', NULL, 8, 1, '2026-07-27 16:18:42', '2026-08-17 13:55:54'),
(103, 'Available Bots', 'user.trading-bots.index', 'user.trading-bots.*', NULL, NULL, NULL, 'user', 102, 1, 1, '2026-07-27 16:18:42', '2026-07-27 16:18:42'),
(104, 'My Activations', 'user.trading-bots.activations', 'user.trading-bots.*', NULL, NULL, NULL, 'user', 102, 2, 1, '2026-07-27 16:18:42', '2026-07-27 16:18:42'),
(105, 'Trading Logs', 'user.trading-bots.logs', 'user.trading-bots.*', NULL, NULL, NULL, 'user', 102, 3, 1, '2026-07-27 16:18:42', '2026-07-27 16:18:42'),
(106, 'Daily Summary', 'user.trading-bots.daily-summary', 'user.trading-bots.*', NULL, NULL, NULL, 'user', 102, 4, 1, '2026-07-27 16:18:42', '2026-07-27 16:18:42'),
(107, 'Copy Trading', NULL, 'admin.copy-trading.*', NULL, '#', '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"20\" height=\"20\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><rect width=\"14\" height=\"14\" x=\"8\" y=\"8\" rx=\"2\" ry=\"2\"/><path d=\"M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2\"/></svg>', 'admin', NULL, 7, 1, '2026-07-27 16:18:44', '2026-08-17 13:55:55'),
(108, 'New Copy Trading', 'admin.copy-trading.create', 'admin.copy-trading.create', NULL, NULL, NULL, 'admin', 107, 1, 1, '2026-07-27 16:18:44', '2026-08-17 13:55:55'),
(109, 'Trading Codes', 'admin.copy-trading.index', 'admin.copy-trading.index', NULL, NULL, NULL, 'admin', 107, 2, 1, '2026-07-27 16:18:44', '2026-07-27 16:18:44'),
(110, 'Trading History', 'admin.copy-trading.history', 'admin.copy-trading.history', NULL, NULL, NULL, 'admin', 107, 3, 1, '2026-07-27 16:18:44', '2026-07-27 16:18:44'),
(111, 'Copy Trading', NULL, 'user.copy-trading.*', NULL, '#', '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"20\" height=\"20\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><rect width=\"14\" height=\"14\" x=\"8\" y=\"8\" rx=\"2\" ry=\"2\"/><path d=\"M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2\"/></svg>', 'user', NULL, 8, 1, '2026-07-27 16:18:44', '2026-08-17 13:55:55'),
(112, 'Trade Now', 'user.copy-trading.index', 'user.copy-trading.index', NULL, NULL, NULL, 'user', 111, 1, 1, '2026-07-27 16:18:44', '2026-07-27 16:18:44'),
(113, 'Trading History', 'user.copy-trading.history', 'user.copy-trading.history', NULL, NULL, NULL, 'user', 111, 2, 1, '2026-07-27 16:18:44', '2026-07-27 16:18:44'),
(114, 'User Wallets', 'admin.deposits.wallets', 'admin.deposits*', NULL, NULL, NULL, 'admin', 57, 5, 1, '2026-07-28 14:47:54', '2026-07-28 14:47:54'),
(117, 'Master Wallets', 'admin.deposits.master-wallets', 'admin.deposits.*', NULL, NULL, NULL, 'admin', 57, 6, 1, '2026-07-28 15:22:15', '2026-07-28 15:22:15'),
(118, 'Automated Trading', NULL, 'user.trading.*', NULL, '#', '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"20\" height=\"20\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"M3 3v18h18\"/><path d=\"M7 15l4-5 3 3 5-7\"/></svg>', 'user', NULL, 7, 1, '2026-09-26 14:46:17', '2026-09-26 14:46:17'),
(119, 'Signals', 'user.trading.signals.index', 'user.trading.signals.*', NULL, NULL, NULL, 'user', 118, 1, 1, '2026-09-26 14:46:17', '2026-09-26 14:46:17'),
(120, 'Place Order', 'user.trading.orders.index', 'user.trading.orders.index', NULL, NULL, NULL, 'user', 118, 2, 1, '2026-09-26 14:46:17', '2026-09-26 14:46:17'),
(121, 'My Exchanges', 'user.trading.exchanges.index', 'user.trading.exchanges.*', NULL, NULL, NULL, 'user', 118, 3, 1, '2026-09-26 14:46:17', '2026-09-26 14:46:17'),
(122, 'Trade Logs', 'user.trading.orders.logs', 'user.trading.orders.logs', NULL, NULL, NULL, 'user', 118, 4, 1, '2026-09-26 14:46:17', '2026-09-26 14:46:17'),
(123, 'Risk Settings', 'user.trading.preferences.index', 'user.trading.preferences.*', NULL, NULL, NULL, 'user', 118, 5, 1, '2026-09-26 14:46:17', '2026-09-26 14:46:17');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_09_04_193222_initialization', 1),
(2, '2026_09_05_212000_add_name_and_description_to_cron_jobs_table', 2),
(3, '2026_09_26_130000_create_exchange_connections_table', 3),
(4, '2026_09_26_130100_create_user_trading_preferences_table', 3),
(5, '2026_09_26_130200_create_trading_signals_table', 3),
(6, '2026_09_26_130300_create_trade_orders_table', 3),
(7, '2026_09_26_130400_create_trade_logs_table', 3),
(8, '2026_09_26_130500_create_signal_follows_table', 3),
(9, '2026_09_26_150000_add_automated_trading_menu_items', 3);

-- --------------------------------------------------------

--
-- Table structure for table `notification_messages`
--

CREATE TABLE `notification_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unread',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `admin_seen` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `onboardings`
--

CREATE TABLE `onboardings` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `risk_profile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'conservative, balanced, growth',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('HHAOmSAR95R8JnICFUbW6fOXHeQXXzuH887f1nz2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidUdlMDIzcUJYTGRIVE5qTnN1T1d4OVlyRnhpUDJqQldBaDc4dEExbyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly9mb3lhbmEubG9jYWwvY29weS10cmFkaW5nIjtzOjU6InJvdXRlIjtzOjEyOiJjb3B5LXRyYWRpbmciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1788643416);

-- --------------------------------------------------------

--
-- Table structure for table `signal_follows`
--

CREATE TABLE `signal_follows` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `trading_signal_id` bigint UNSIGNED NOT NULL,
  `mode` enum('auto','manual') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'auto',
  `status` enum('pending','queued','executed','failed','skipped') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `trade_order_id` bigint UNSIGNED DEFAULT NULL,
  `risk_snapshot` json DEFAULT NULL,
  `error_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `signal_follows_user_signal_unique` (`user_id`,`trading_signal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'name', 'Foyana', '2026-02-24 06:19:02', '2026-08-05 20:19:51'),
(2, 'logo_square', 'logo-square.png?v=1787049104', '2026-02-24 06:19:02', '2026-08-18 11:31:44'),
(3, 'logo_rectangle', 'logo-rectangle.png?v=1787049104', '2026-02-24 06:19:02', '2026-08-18 11:31:44'),
(4, 'favicon', 'favicon.png?v=1787049104', '2026-02-24 06:19:02', '2026-08-18 11:31:44'),
(5, 'email', 'email@example.com', '2026-02-24 06:19:02', '2026-02-24 06:19:02'),
(6, 'timezone', 'Europe/London', '2026-02-24 06:19:02', '2026-02-24 06:19:02'),
(7, 'email_verification', 'disabled', '2026-02-24 06:19:02', '2026-02-24 06:19:02'),
(8, 'google_recaptcha', 'disabled', '2026-02-24 06:19:02', '2026-02-24 06:19:02'),
(9, 'require_strong_password', 'disabled', '2026-02-24 06:19:02', '2026-02-24 06:19:02'),
(10, 'login_otp', 'disabled', '2026-02-24 06:19:02', '2026-02-24 06:19:02'),
(11, 'welcome_bonus', '10', '2026-02-24 06:19:02', '2026-02-24 06:19:02'),
(12, 'referral_bonus', '[10,5,3,0,0,0]', '2026-02-24 06:19:03', '2026-02-24 06:19:03'),
(13, 'pagination', '10', '2026-02-24 06:19:03', '2026-02-26 12:44:24'),
(14, 'delete_notification_message', 'enabled', '2026-02-24 06:19:03', '2026-02-24 06:19:03'),
(15, 'currency', 'USD', '2026-02-24 06:19:03', '2026-07-28 16:19:32'),
(16, 'currency_symbol', '$', '2026-02-24 06:19:03', '2026-07-28 16:19:32'),
(17, 'currency_symbol_position', 'before', '2026-02-24 06:19:03', '2026-02-24 06:19:03'),
(18, 'decimal_places', '2', '2026-02-24 06:19:03', '2026-02-24 06:19:03'),
(19, 'min_deposit', '1', '2026-02-24 06:19:03', '2026-02-24 06:19:03'),
(20, 'max_deposit', '90000', '2026-02-24 06:19:03', '2026-02-24 09:27:21'),
(21, 'deposit_fee', '1.5', '2026-02-24 06:19:03', '2026-02-24 06:19:03'),
(22, 'deposit_expires_at', '10', '2026-02-24 06:19:04', '2026-02-24 06:19:04'),
(23, 'min_withdrawal', '1', '2026-02-24 06:19:04', '2026-07-28 22:11:01'),
(24, 'max_withdrawal', '50000', '2026-02-24 06:19:04', '2026-02-24 13:21:10'),
(25, 'withdrawal_fee', '1.2', '2026-02-24 06:19:04', '2026-02-24 06:19:04'),
(38, 'email_notification', '{\"append_date_to_subject\":\"enabled\",\"email_queue\":\"disabed\",\"notifications\":{\"deposit\":{\"status\":\"enabled\",\"tip\":\"If this is enabled, users will get email notification when they make deposits or when their deposit status changes.\",\"warning\":null},\"email_verification\":{\"status\":\"enabled\",\"tip\":\"If this is enabled, users will get email notification when they register.\",\"warning\":\"If you have enabled email verification in the security setting and disabled this notification, users won\'t be able to sign up as no verification link or code will be sent.\"},\"kyc\":{\"status\":\"enabled\",\"tip\":\"If this is enabled, users will get email notification when they carry out KYC verification or when their KYC status changes.\",\"warning\":null},\"otp_verification\":{\"status\":\"enabled\",\"tip\":\"If this is enabled, users will get email notification when they make attempt any action that requires an OTP code.\",\"warning\":\"If you have enabled OTP verification in the security setting and disabled this notification, users won\'t be able to login or carryout any action that requires otp verification as no OTP code will be sent.\"},\"referral\":{\"status\":\"enabled\",\"tip\":\"If this is enabled, users will get email notification when someone sign up with their referral link or code.\",\"warning\":null},\"transaction\":{\"status\":\"enabled\",\"tip\":\"If this is enabled, users will get email notification when any transaction occurs on their account.\",\"warning\":\"Sending too many emails can trigger email quota limit, blacklisting or spam. Consult your hosting provider.\"},\"welcome\":{\"status\":\"enabled\",\"tip\":\"If this is enabled, users will get email notification when their sign up is completed.\",\"warning\":null},\"withdrawal\":{\"status\":\"enabled\",\"tip\":\"If this is enabled, users will get email notification when they withdraw or when their withdrawal status changes.\",\"warning\":null},\"account_ban\":{\"status\":\"enabled\",\"tip\":\"If this is enabled, users will get email notification when their account is banned or unbanned.\",\"warning\":null}}}', '2026-02-24 06:19:05', '2026-02-24 06:19:05'),
(39, 'regulatory_compliance', '{\"regulators\":[\"FinCEN Registered Money Services Business (MSB)\",\"Virtual Asset Regulatory Authority (VARA Compliance)\",\"European MiCA (Markets in Crypto-Assets) Framework\",\"Financial Conduct Authority (FCA Standards)\"],\"pdf_certificates\":[{\"name\":\"Digital Asset Custody & Settlement Certificate\",\"file\":\"digital_asset_custody_certificate.pdf\"},{\"name\":\"Algorithmic Trading & Technology License\",\"file\":\"algorithmic_trading_license.pdf\"},{\"name\":\"MSB Financial Operations Certificate\",\"file\":\"msb_financial_operations_certificate.pdf\"},{\"name\":\"Anti-Money Laundering (AML) Audit Verification\",\"file\":\"aml_audit_verification.pdf\"}]}', '2026-02-24 06:19:05', '2026-08-18 13:24:41'),
(40, 'seo_description', 'Automate your crypto trading with algorithmic AI bots and mirror verified strategies in real time with copy trading on :site_name. Fast execution, total portfolio control.', '2026-02-24 06:19:05', '2026-08-17 14:07:03'),
(41, 'seo_keywords', 'copy trading, AI trading bots, automated crypto trading, algorithmic trading, crypto copy trading, automated trading strategies, quantitative trading, :site_name', '2026-02-24 06:19:05', '2026-08-17 14:07:03'),
(42, 'social_title', ':site_name | Automated AI Bot & Copy Trading Platform', '2026-02-24 06:19:05', '2026-08-17 14:07:03'),
(43, 'social_description', 'Deploy automated AI trading bots and copy top-performing crypto traders in real time on :site_name. Institutional-grade execution built for modern digital asset traders.', '2026-02-24 06:19:05', '2026-08-17 14:07:03'),
(44, 'seo_image', 'seo_banner_1787053883.png', '2026-02-24 06:19:05', '2026-08-18 12:51:23'),
(45, 'social_media', '{\"twitter\":\"https:\\/\\/twitter.com\\/user\",\"facebook\":\"https:\\/\\/facebook.com\\/user\",\"instagram\":\"https:\\/\\/instagram.com\\/user\",\"linkedin\":\"https:\\/\\/linkedin.com\\/company\\/user\",\"youtube\":\"https:\\/\\/youtube.com\\/@user\",\"telegram\":\"https:\\/\\/t.me\\/user\",\"whatsapp\":\"https:\\/\\/wa.me\\/user\",\"tiktok\":\"https:\\/\\/tiktok.com\\/@user\",\"x\":\"https:\\/\\/x.com\\/user\"}', '2026-02-24 06:19:05', '2026-08-17 14:09:55'),
(46, 'app_timezone', 'Europe/London', '2026-02-25 02:08:55', '2026-02-25 02:08:55'),
(47, 'offices', '[]', '2026-02-25 02:08:56', '2026-08-18 11:31:44'),
(52, 'modules', '{\"trading_bot_module\":{\"name\":\"Trading Bot\",\"status\":\"enabled\",\"menu_search\":[{\"term\":\"Trading Bots\",\"column\":\"label\"}],\"description\":\"Allows automated trading through administrator-managed or strategy-based bots. Users can deploy bots to execute trades continuously based on predefined market strategies.\"},\"file_manager_module\":{\"name\":\"File Manager\",\"status\":\"enabled\",\"menu_search\":[{\"term\":\"File Manager\",\"column\":\"label\"}],\"description\":\"Enables secure server-side file management for administrators, including file upload, code editing, archiving, extraction, and directory management within the platform environment.\"},\"kyc_module\":{\"name\":\"KYC\",\"status\":\"enabled\",\"menu_search\":[{\"term\":\"KYC\",\"column\":\"label\"}],\"description\":\"Provides identity verification workflows for user onboarding, document review, and compliance management.\"},\"copy_trading_module\":{\"name\":\"Copy Trading\",\"status\":\"enabled\",\"menu_search\":[{\"term\":\"Copy Trading\",\"column\":\"label\"}],\"description\":\"Enables copy trading for users.\"}}', '2026-02-25 10:02:51', '2026-08-17 13:55:55'),
(53, 'kyc', '[{\"name\":\"Deposits\",\"route_wildcard\":\"user.deposits.*\",\"description\":\"When enabled, users must complete KYC verification before making deposits into their account.\",\"status\":\"disabled\"},{\"name\":\"Withdrawal\",\"route_wildcard\":\"user.withdrawals.*\",\"description\":\"When enabled, users must complete KYC verification before requesting or processing withdrawals.\",\"status\":\"disabled\"}]', '2026-02-25 10:02:51', '2026-07-28 21:44:32'),
(54, 'livechat_scripts', '<script src=\"\"></script>', '2026-03-04 02:14:36', '2026-08-17 14:11:12'),
(55, 'header_scripts', NULL, '2026-03-04 02:14:37', '2026-03-04 02:14:37'),
(56, 'footer_scripts', NULL, '2026-03-04 02:14:38', '2026-03-04 02:14:38'),
(57, 'login_methods', '{\"google\":{\"status\":\"enabled\",\"name\":\"Google\",\"icon\":\"<svg class=\\\"w-5 h-5\\\" viewBox=\\\"0 0 24 24\\\"><path d=\\\"M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z\\\" fill=\\\"#4285F4\\\"\\/><path d=\\\"M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z\\\" fill=\\\"#34A853\\\"\\/><path d=\\\"M5.84 14.1c-.22-.66-.35-1.36-.35-2.1s.13-1.44.35-2.1V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l3.66-2.84z\\\" fill=\\\"#FBBC05\\\"\\/><path d=\\\"M12 5.38c1.62 0 3.06.56 4.21 1.63l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z\\\" fill=\\\"#EA4335\\\"\\/><\\/svg>\"},\"github\":{\"status\":\"enabled\",\"name\":\"GitHub\",\"icon\":\"<svg class=\\\"w-5 h-5\\\" fill=\\\"currentColor\\\" viewBox=\\\"0 0 24 24\\\"><path fill-rule=\\\"evenodd\\\" d=\\\"M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z\\\" clip-rule=\\\"evenodd\\\" \\/><\\/svg>\"},\"facebook\":{\"status\":\"enabled\",\"name\":\"Facebook\",\"icon\":\"<svg class=\\\"w-5 h-5\\\" fill=\\\"currentColor\\\" viewBox=\\\"0 0 24 24\\\"><path fill-rule=\\\"evenodd\\\" d=\\\"M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z\\\" clip-rule=\\\"evenodd\\\" \\/><\\/svg>\"},\"twitter\":{\"status\":\"enabled\",\"name\":\"Twitter (X)\",\"icon\":\"<svg class=\\\"w-5 h-5\\\" fill=\\\"currentColor\\\" viewBox=\\\"0 0 24 24\\\"><path d=\\\"M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932 6.064-6.932zm-1.292 19.49h2.039L6.486 3.24H4.298l13.311 17.403z\\\"\\/><\\/svg>\"},\"linkedin\":{\"status\":\"enabled\",\"name\":\"LinkedIn\",\"icon\":\"<svg class=\\\"w-5 h-5\\\" fill=\\\"currentColor\\\" viewBox=\\\"0 0 24 24\\\"><path d=\\\"M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z\\\"\\/><\\/svg>\"},\"gitlab\":{\"status\":\"enabled\",\"name\":\"GitLab\",\"icon\":\"<svg class=\\\"w-5 h-5\\\" fill=\\\"currentColor\\\" viewBox=\\\"0 0 24 24\\\"><path d=\\\"M23.955 13.587l-1.342-4.135-2.664-8.189c-.135-.417-.724-.417-.859 0L16.425 9.452H7.575L4.91 1.263c-.135-.417-.724-.417-.859 0L1.387 9.452.045 13.587c-.114.352.016.74.321.961l11.634 8.45 11.633-8.45c.306-.222.436-.609.322-.961z\\\"\\/><\\/svg>\"},\"bitbucket\":{\"status\":\"enabled\",\"name\":\"Bitbucket\",\"icon\":\"<svg class=\\\"w-5 h-5\\\" fill=\\\"currentColor\\\" viewBox=\\\"0 0 24 24\\\"><path d=\\\"M22.308 1.156c-.135-.338-.41-.58-.758-.65L12.066 0 2.457 1.112c-.347.07-.624.312-.758.65L0 12l2.457 10.888a.81.81 0 0 0 .758.65L12 24l8.785-1.462a.81.81 0 0 0 .758-.65L24 12l-1.692-10.844zM16.92 15.6h-9.84l-1.08-7.2h12l-1.08 7.2z\\\"\\/><\\/svg>\"},\"email\":{\"status\":\"enabled\",\"name\":\"Email\",\"icon\":\"<svg xmlns=\\\"http:\\/\\/www.w3.org\\/2000\\/svg\\\" class=\\\"w-5 h-5\\\" viewBox=\\\"0 0 24 24\\\" fill=\\\"none\\\" stroke=\\\"currentColor\\\" stroke-width=\\\"2\\\" stroke-linecap=\\\"round\\\" stroke-linejoin=\\\"round\\\"><path d=\\\"M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z\\\"\\/><polyline points=\\\"22,6 12,13 2,6\\\"\\/><\\/svg>\"}}', '2026-03-04 10:12:39', '2026-07-27 16:18:29'),
(58, 'preloader', 'enabled', '2026-07-27 16:18:31', '2026-07-27 16:18:31'),
(63, 'nocaptcha_sitekey', NULL, '2026-07-28 21:44:32', '2026-07-28 21:44:32'),
(64, 'nocaptcha_secret', NULL, '2026-07-28 21:44:32', '2026-07-28 21:44:32'),
(65, 'auto_approve_withdrawal', 'enabled', '2026-07-28 22:06:37', '2026-07-29 10:33:47'),
(68, 'ethereum_last_scanned_block', '25636254', '2026-07-29 03:08:31', '2026-07-29 11:31:16');

-- --------------------------------------------------------

--
-- Table structure for table `trade_logs`
--

CREATE TABLE `trade_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `trade_order_id` bigint UNSIGNED DEFAULT NULL,
  `trading_signal_id` bigint UNSIGNED DEFAULT NULL,
  `exchange` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pair` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `market_type` enum('spot','futures') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` enum('info','warning','error','critical') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  `action` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `context` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `trade_logs_user_created_index` (`user_id`,`created_at`),
  KEY `trade_logs_level_created_index` (`level`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trade_orders`
--

CREATE TABLE `trade_orders` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `exchange_connection_id` bigint UNSIGNED NOT NULL,
  `trading_signal_id` bigint UNSIGNED DEFAULT NULL,
  `client_order_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exchange_order_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exchange` enum('binance','bybit','mexc') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'binance',
  `market_type` enum('spot','futures') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'spot',
  `pair` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `side` enum('buy','sell') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'buy',
  `direction` enum('buy','sell','long','short') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_type` enum('market','limit') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'market',
  `leverage` int UNSIGNED DEFAULT NULL,
  `quantity` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `limit_price` decimal(18,8) DEFAULT NULL,
  `filled_price` decimal(18,8) DEFAULT NULL,
  `take_profit_price` decimal(18,8) DEFAULT NULL,
  `stop_loss_price` decimal(18,8) DEFAULT NULL,
  `notional` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `realised_pnl` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `fee` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `status` enum('pending','open','filled','closed','cancelled','rejected','expired') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `origin` enum('manual','auto') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `is_tp_hit` tinyint(1) NOT NULL DEFAULT '0',
  `is_sl_hit` tinyint(1) NOT NULL DEFAULT '0',
  `error_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `close_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `submitted_at` bigint DEFAULT NULL,
  `filled_at` bigint DEFAULT NULL,
  `closed_at` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `trade_orders_client_order_id_unique` (`client_order_id`),
  KEY `trade_orders_user_status_index` (`user_id`,`status`),
  KEY `trade_orders_user_created_index` (`user_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trading_bots`
--

CREATE TABLE `trading_bots` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('crypto','forex') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'crypto',
  `exchanges` json DEFAULT NULL,
  `traded_pairs` json NOT NULL,
  `min_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `max_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `daily_return_min` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `daily_return_max` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `duration` int UNSIGNED NOT NULL,
  `duration_type` enum('hour','day','week','month','year') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hour',
  `trading_days` json DEFAULT NULL,
  `is_capital_returned` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trading_bot_activations`
--

CREATE TABLE `trading_bot_activations` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `trading_bot_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(20,8) NOT NULL,
  `leverage` int UNSIGNED DEFAULT NULL,
  `today_roi` decimal(20,2) NOT NULL DEFAULT '0.00',
  `returned_profit` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `today_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `today_amount_roi` decimal(20,2) NOT NULL DEFAULT '0.00',
  `today_cycle_reset_at` bigint UNSIGNED DEFAULT NULL,
  `next_profit_date` bigint UNSIGNED DEFAULT NULL,
  `last_profit_date` bigint UNSIGNED DEFAULT NULL,
  `status` enum('active','suspended','completed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `start_date` bigint UNSIGNED NOT NULL,
  `end_date` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trading_bot_logs`
--

CREATE TABLE `trading_bot_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `trading_bot_activation_id` bigint UNSIGNED NOT NULL,
  `trading_pair` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exchange` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('forex','crypto') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `profit` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `profit_percentage` decimal(20,2) NOT NULL DEFAULT '0.00',
  `exit_time` bigint UNSIGNED DEFAULT NULL,
  `exit_price` decimal(18,8) DEFAULT NULL,
  `direction` enum('buy','sell','long','short') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `leverage` int UNSIGNED DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trading_signals`
--

CREATE TABLE `trading_signals` (
  `id` bigint UNSIGNED NOT NULL,
  `pair` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `base_asset` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quote_asset` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `market_type` enum('spot','futures') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'spot',
  `direction` enum('buy','sell','long','short') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'buy',
  `entry_price` decimal(18,8) NOT NULL,
  `target_1` decimal(18,8) DEFAULT NULL,
  `target_2` decimal(18,8) DEFAULT NULL,
  `target_3` decimal(18,8) DEFAULT NULL,
  `stop_loss` decimal(18,8) DEFAULT NULL,
  `confidence` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status` enum('active','closed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `source` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `signal_time` bigint NOT NULL,
  `expires_at` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `trading_signals_status_market_index` (`status`,`market_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `amount` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `converted_amount` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `converted_currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rate` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_balance` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tron_gas_sponsorships`
--

CREATE TABLE `tron_gas_sponsorships` (
  `id` bigint UNSIGNED NOT NULL,
  `blockchain_id` bigint UNSIGNED NOT NULL,
  `user_blockchain_wallet_id` bigint UNSIGNED NOT NULL,
  `funding_tx_hash` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sponsored_amount_sun` bigint UNSIGNED NOT NULL,
  `recovered_amount_sun` bigint UNSIGNED NOT NULL DEFAULT '0',
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `settled_at` timestamp NULL DEFAULT NULL,
  `meta` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `balance` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `status` enum('active','banned','suspended') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `username` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `solana_wallet_address` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `solana_private_key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `referral_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referrer_id` bigint UNSIGNED DEFAULT NULL,
  `lang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `photo` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_blockchain_wallets`
--

CREATE TABLE `user_blockchain_wallets` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `blockchain_id` bigint UNSIGNED NOT NULL,
  `address` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `private_key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_trading_preferences`
--

CREATE TABLE `user_trading_preferences` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `auto_trading_mode` tinyint(1) NOT NULL DEFAULT '0',
  `default_exchange` enum('binance','bybit','mexc') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_market_type` enum('spot','futures') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'spot',
  `max_trade_size` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `risk_percentage` decimal(5,2) NOT NULL DEFAULT '1.00',
  `auto_stop_loss` tinyint(1) NOT NULL DEFAULT '1',
  `stop_loss_percentage` decimal(5,2) NOT NULL DEFAULT '2.00',
  `take_profit_percentage` decimal(5,2) NOT NULL DEFAULT '4.00',
  `max_concurrent_trades` int UNSIGNED NOT NULL DEFAULT '3',
  `max_daily_loss_percentage` decimal(5,2) NOT NULL DEFAULT '10.00',
  `slippage_tolerance` decimal(5,2) NOT NULL DEFAULT '0.50',
  `daily_loss_reset_at` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `user_trading_preferences_user_id_unique` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--

CREATE TABLE `withdrawals` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `blockchain_id` bigint UNSIGNED DEFAULT NULL,
  `blockchain_token_id` bigint UNSIGNED DEFAULT NULL,
  `amount` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `converted_amount` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fee_percent` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fee_amount` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount_payable` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exchange_rate` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_reference` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_hash` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_proof` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `structured_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '{"crypto":{"transaction_hash":"b7f3e2a1c9d84f6e0a5b3c7d9e1f4a8b2c6d5e7f9a0b1c2d3e4f5a6b7c8","wallet_address":"TN1Z3YwRk9sQH2LJ8a6Xx4EwQb5F3M7P9C","currency":"USDT","network":"TRC20"},"bank_transfer":{"bank_name":"Chase Bank","account_holder":"John Doe","account_number":"1234567890","routing_number":"021000021 (nullable)","swift":"CHASUS33 (nullable)"},"digital_wallet":{"payment_id":"PAY-84739201","identity_id":"johndoe@email.com \\/ johndoe123"}}',
  `auto_res_dump` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Dump of response from third party payment provider',
  `status` enum('pending','completed','failed','partial_payment') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_username_unique` (`username`),
  ADD UNIQUE KEY `admins_email_unique` (`email`);

--
-- Indexes for table `blockchains`
--
ALTER TABLE `blockchains`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `blockchains_code_unique` (`code`);

--
-- Indexes for table `blockchain_tokens`
--
ALTER TABLE `blockchain_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `blockchain_tokens_blockchain_id_foreign` (`blockchain_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `client_reviews`
--
ALTER TABLE `client_reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `copy_tradings`
--
ALTER TABLE `copy_tradings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `copy_trading_histories`
--
ALTER TABLE `copy_trading_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `copy_trading_histories_user_id_foreign` (`user_id`),
  ADD KEY `copy_trading_histories_copy_trading_id_foreign` (`copy_trading_id`),
  ADD KEY `copy_trading_histories_status_index` (`status`);

--
-- Indexes for table `cron_jobs`
--
ALTER TABLE `cron_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cron_jobs_command_unique` (`command`);

--
-- Indexes for table `deposits`
--
ALTER TABLE `deposits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deposits_user_id_foreign` (`user_id`),
  ADD KEY `deposits_blockchain_id_foreign` (`blockchain_id`),
  ADD KEY `deposits_blockchain_token_id_foreign` (`blockchain_token_id`);

--
-- Indexes for table `exchange_connections`
--
ALTER TABLE `exchange_connections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kycs`
--
ALTER TABLE `kycs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kycs_user_id_foreign` (`user_id`);

--
-- Indexes for table `management_teams`
--
ALTER TABLE `management_teams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_items_parent_id_foreign` (`parent_id`),
  ADD KEY `menu_items_type_parent_id_sort_order_index` (`type`,`parent_id`,`sort_order`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification_messages`
--
ALTER TABLE `notification_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notification_messages_user_id_foreign` (`user_id`);

--
-- Indexes for table `onboardings`
--
ALTER TABLE `onboardings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `onboardings_user_id_foreign` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `signal_follows`
--
ALTER TABLE `signal_follows`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `trade_logs`
--
ALTER TABLE `trade_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trade_orders`
--
ALTER TABLE `trade_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trading_bots`
--
ALTER TABLE `trading_bots`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trading_bot_activations`
--
ALTER TABLE `trading_bot_activations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trading_bot_activations_user_id_foreign` (`user_id`),
  ADD KEY `trading_bot_activations_trading_bot_id_foreign` (`trading_bot_id`);

--
-- Indexes for table `trading_bot_logs`
--
ALTER TABLE `trading_bot_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trading_bot_logs_user_id_foreign` (`user_id`),
  ADD KEY `trading_bot_logs_trading_bot_activation_id_foreign` (`trading_bot_activation_id`);

--
-- Indexes for table `trading_signals`
--
ALTER TABLE `trading_signals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_user_id_foreign` (`user_id`);

--
-- Indexes for table `tron_gas_sponsorships`
--
ALTER TABLE `tron_gas_sponsorships`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tron_gas_sponsorships_user_blockchain_wallet_id_foreign` (`user_blockchain_wallet_id`),
  ADD KEY `tron_gas_sponsorship_lookup_idx` (`blockchain_id`,`user_blockchain_wallet_id`,`status`),
  ADD KEY `tron_gas_sponsorships_funding_tx_hash_index` (`funding_tx_hash`),
  ADD KEY `tron_gas_sponsorships_status_index` (`status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD KEY `users_referrer_id_foreign` (`referrer_id`);

--
-- Indexes for table `user_blockchain_wallets`
--
ALTER TABLE `user_blockchain_wallets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_blockchain_wallets_user_id_blockchain_id_unique` (`user_id`,`blockchain_id`),
  ADD KEY `user_blockchain_wallets_blockchain_id_foreign` (`blockchain_id`);

--
-- Indexes for table `user_trading_preferences`
--
ALTER TABLE `user_trading_preferences`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `withdrawals_user_id_foreign` (`user_id`),
  ADD KEY `withdrawals_blockchain_id_foreign` (`blockchain_id`),
  ADD KEY `withdrawals_blockchain_token_id_foreign` (`blockchain_token_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blockchains`
--
ALTER TABLE `blockchains`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `blockchain_tokens`
--
ALTER TABLE `blockchain_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `client_reviews`
--
ALTER TABLE `client_reviews`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `copy_tradings`
--
ALTER TABLE `copy_tradings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `copy_trading_histories`
--
ALTER TABLE `copy_trading_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cron_jobs`
--
ALTER TABLE `cron_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `deposits`
--
ALTER TABLE `deposits`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exchange_connections`
--
ALTER TABLE `exchange_connections`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `kycs`
--
ALTER TABLE `kycs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `management_teams`
--
ALTER TABLE `management_teams`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notification_messages`
--
ALTER TABLE `notification_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `onboardings`
--
ALTER TABLE `onboardings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `signal_follows`
--
ALTER TABLE `signal_follows`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `trade_logs`
--
ALTER TABLE `trade_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trade_orders`
--
ALTER TABLE `trade_orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trading_bots`
--
ALTER TABLE `trading_bots`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trading_bot_activations`
--
ALTER TABLE `trading_bot_activations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trading_bot_logs`
--
ALTER TABLE `trading_bot_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trading_signals`
--
ALTER TABLE `trading_signals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tron_gas_sponsorships`
--
ALTER TABLE `tron_gas_sponsorships`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_blockchain_wallets`
--
ALTER TABLE `user_blockchain_wallets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_trading_preferences`
--
ALTER TABLE `user_trading_preferences`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blockchain_tokens`
--
ALTER TABLE `blockchain_tokens`
  ADD CONSTRAINT `blockchain_tokens_blockchain_id_foreign` FOREIGN KEY (`blockchain_id`) REFERENCES `blockchains` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exchange_connections`
--
-- Only user_id is constrained. A connection must be able to be removed while
-- its trade history survives, so nothing here cascades from the credential row.
ALTER TABLE `exchange_connections`
  ADD CONSTRAINT `exchange_connections_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `copy_trading_histories`
--
ALTER TABLE `copy_trading_histories`
  ADD CONSTRAINT `copy_trading_histories_copy_trading_id_foreign` FOREIGN KEY (`copy_trading_id`) REFERENCES `copy_tradings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `copy_trading_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `deposits`
--
ALTER TABLE `deposits`
  ADD CONSTRAINT `deposits_blockchain_id_foreign` FOREIGN KEY (`blockchain_id`) REFERENCES `blockchains` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `deposits_blockchain_token_id_foreign` FOREIGN KEY (`blockchain_token_id`) REFERENCES `blockchain_tokens` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `deposits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kycs`
--
ALTER TABLE `kycs`
  ADD CONSTRAINT `kycs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `menu_items` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notification_messages`
--
ALTER TABLE `notification_messages`
  ADD CONSTRAINT `notification_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `onboardings`
--
ALTER TABLE `onboardings`
  ADD CONSTRAINT `onboardings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `signal_follows`
--
ALTER TABLE `signal_follows`
  ADD CONSTRAINT `signal_follows_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trade_logs`
--
-- trade_order_id and trading_signal_id are deliberately NOT constrained. A log
-- row is an audit record: it must survive the deletion of the order or signal it
-- describes, and an FK with CASCADE would silently destroy the history.
ALTER TABLE `trade_logs`
  ADD CONSTRAINT `trade_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trade_orders`
--
-- exchange_connection_id and trading_signal_id are soft references for the same
-- reason as above: disconnecting an exchange must not erase the orders it
-- placed, and an order must outlive the signal that produced it.
ALTER TABLE `trade_orders`
  ADD CONSTRAINT `trade_orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trading_bot_activations`
--
ALTER TABLE `trading_bot_activations`
  ADD CONSTRAINT `trading_bot_activations_trading_bot_id_foreign` FOREIGN KEY (`trading_bot_id`) REFERENCES `trading_bots` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trading_bot_activations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trading_bot_logs`
--
ALTER TABLE `trading_bot_logs`
  ADD CONSTRAINT `trading_bot_logs_trading_bot_activation_id_foreign` FOREIGN KEY (`trading_bot_activation_id`) REFERENCES `trading_bot_activations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trading_bot_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tron_gas_sponsorships`
--
ALTER TABLE `tron_gas_sponsorships`
  ADD CONSTRAINT `tron_gas_sponsorships_blockchain_id_foreign` FOREIGN KEY (`blockchain_id`) REFERENCES `blockchains` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tron_gas_sponsorships_user_blockchain_wallet_id_foreign` FOREIGN KEY (`user_blockchain_wallet_id`) REFERENCES `user_blockchain_wallets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_referrer_id_foreign` FOREIGN KEY (`referrer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_blockchain_wallets`
--
ALTER TABLE `user_blockchain_wallets`
  ADD CONSTRAINT `user_blockchain_wallets_blockchain_id_foreign` FOREIGN KEY (`blockchain_id`) REFERENCES `blockchains` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_blockchain_wallets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_trading_preferences`
--
ALTER TABLE `user_trading_preferences`
  ADD CONSTRAINT `user_trading_preferences_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD CONSTRAINT `withdrawals_blockchain_id_foreign` FOREIGN KEY (`blockchain_id`) REFERENCES `blockchains` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `withdrawals_blockchain_token_id_foreign` FOREIGN KEY (`blockchain_token_id`) REFERENCES `blockchain_tokens` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `withdrawals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
