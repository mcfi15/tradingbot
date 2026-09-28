<?php

namespace App\Services;

class SolanaTransactionSigner
{
    /**
     * Encode an integer as compact-u16 variable length integer.
     *
     * @param int $value
     * @return string
     */
    public static function encodeCompactU16(int $value): string
    {
        $out = '';
        while (true) {
            $elem = $value & 0x7f;
            $value >>= 7;
            if ($value === 0) {
                $out .= chr($elem);
                break;
            } else {
                $out .= chr($elem | 0x80);
            }
        }
        return $out;
    }

    /**
     * Build and sign a native SOL transfer transaction.
     *
     * @param string $fromPrivateKeyB58 64-byte base58 encoded private key of user
     * @param string $toPublicKeyB58 base58 public address of recipient
     * @param int $amountLamports lamports to transfer
     * @param string $recentBlockhashB58 recent blockhash from RPC
     * @return string base64 encoded serialized transaction
     */
    public static function buildAndSignSolTransfer(
        string $fromPrivateKeyB58,
        string $toPublicKeyB58,
        int $amountLamports,
        string $recentBlockhashB58
    ): string {
        $fromPrivateKeyBytes = SolanaBase58::decode($fromPrivateKeyB58);
        $fromPublicKeyBytes = substr($fromPrivateKeyBytes, 32, 32);

        $toPublicKeyBytes = SolanaBase58::decode($toPublicKeyB58);
        $blockhashBytes = SolanaBase58::decode($recentBlockhashB58);

        // System Program ID: 11111111111111111111111111111111 (32 bytes of zeroes)
        $systemProgramBytes = str_repeat("\x00", 32);

        // Accounts list
        $accounts = [
            $fromPublicKeyBytes, // Index 0 (Signer, Writable, Fee Payer)
            $toPublicKeyBytes,   // Index 1 (Writable)
            $systemProgramBytes  // Index 2 (Read-only, Unsigned)
        ];

        // 1. Transaction Message Header
        // - numRequiredSignatures: 1
        // - numReadOnlySignedAccounts: 0
        // - numReadOnlyUnsignedAccounts: 1
        $header = pack('C3', 1, 0, 1);

        // 2. Account Keys list serialization
        $accountsSerialized = self::encodeCompactU16(count($accounts));
        foreach ($accounts as $account) {
            $accountsSerialized .= $account;
        }

        // 3. Instruction definition
        // - Program ID index: 2 (System Program)
        // - Account indexes count: 2 (source and destination)
        // - Account indexes: [0, 1]
        // - Data: Code 2 (Transfer, 4 bytes) + Amount (8 bytes) = 12 bytes
        $instData = pack('V', 2) . pack('P', $amountLamports);
        $instruction = pack('C', 2) // Program index
            . self::encodeCompactU16(2) // Account indexes count
            . pack('C2', 0, 1) // Account indexes
            . self::encodeCompactU16(strlen($instData)) // Data length
            . $instData;

        $instructionsSerialized = self::encodeCompactU16(1) . $instruction;

        // Compile Message
        $message = $header . $accountsSerialized . $blockhashBytes . $instructionsSerialized;

        // Sign message using standard Ed25519 signature
        if (!function_exists('sodium_crypto_sign_detached')) {
            throw new \RuntimeException('PHP Sodium extension is not installed or enabled.');
        }
        $signature = sodium_crypto_sign_detached($message, $fromPrivateKeyBytes);

        // Compile Transaction
        // - Signatures count: 1
        // - Signature (64 bytes)
        // - Message
        $transaction = self::encodeCompactU16(1) . $signature . $message;

        return base64_encode($transaction);
    }

    /**
     * Build and sign a native SOL transfer transaction sponsored by the master wallet.
     *
     * @param string $userPrivateKeyB58 64-byte base58 encoded private key of user (source)
     * @param string $masterPrivateKeyB58 64-byte base58 encoded private key of master wallet (fee payer & destination)
     * @param int $amountLamports lamports to transfer
     * @param string $recentBlockhashB58 recent blockhash from RPC
     * @return string base64 encoded serialized transaction
     */
    public static function buildAndSignSolTransferSponsored(
        string $userPrivateKeyB58,
        string $masterPrivateKeyB58,
        int $amountLamports,
        string $recentBlockhashB58
    ): string {
        $userPrivateKeyBytes = SolanaBase58::decode($userPrivateKeyB58);
        $userPublicKeyBytes = substr($userPrivateKeyBytes, 32, 32);

        $masterPrivateKeyBytes = SolanaBase58::decode($masterPrivateKeyB58);
        $masterPublicKeyBytes = substr($masterPrivateKeyBytes, 32, 32);

        $blockhashBytes = SolanaBase58::decode($recentBlockhashB58);

        // System Program ID: 11111111111111111111111111111111 (32 bytes of zeroes)
        $systemProgramBytes = str_repeat("\x00", 32);

        // Accounts list
        // Index 0: Master Public Key (signer, writable, fee payer, destination)
        // Index 1: User Public Key (signer, writable, source)
        // Index 2: System Program ID (read-only, unsigned)
        $accounts = [
            $masterPublicKeyBytes, // Index 0
            $userPublicKeyBytes,   // Index 1
            $systemProgramBytes    // Index 2
        ];

        // 1. Transaction Message Header
        // - numRequiredSignatures: 2
        // - numReadOnlySignedAccounts: 0
        // - numReadOnlyUnsignedAccounts: 1
        $header = pack('C3', 2, 0, 1);

        // 2. Account Keys list serialization
        $accountsSerialized = self::encodeCompactU16(count($accounts));
        foreach ($accounts as $account) {
            $accountsSerialized .= $account;
        }

        // 3. Instruction definition
        // - Program ID index: 2 (System Program)
        // - Account indexes count: 2 (source and destination)
        // - Account indexes: [1, 0] (from User to Master)
        // - Data: Code 2 (Transfer, 4 bytes) + Amount (8 bytes) = 12 bytes
        $instData = pack('V', 2) . pack('P', $amountLamports);
        $instruction = pack('C', 2) // Program index
            . self::encodeCompactU16(2) // Account indexes count
            . pack('C2', 1, 0) // Account indexes (source = 1, destination = 0)
            . self::encodeCompactU16(strlen($instData)) // Data length
            . $instData;

        $instructionsSerialized = self::encodeCompactU16(1) . $instruction;

        // Compile Message
        $message = $header . $accountsSerialized . $blockhashBytes . $instructionsSerialized;

        if (!function_exists('sodium_crypto_sign_detached')) {
            throw new \RuntimeException('PHP Sodium extension is not installed or enabled.');
        }

        // Sign with fee payer (Master) first, then source (User)
        $sigMaster = sodium_crypto_sign_detached($message, $masterPrivateKeyBytes);
        $sigUser = sodium_crypto_sign_detached($message, $userPrivateKeyBytes);

        // Compile Transaction
        // - Signatures count: 2
        // - Master Signature (64 bytes)
        // - User Signature (64 bytes)
        // - Message
        $transaction = self::encodeCompactU16(2) . $sigMaster . $sigUser . $message;

        return base64_encode($transaction);
    }

    /**
     * Build and sign an SPL token transfer sponsored by the master wallet.
     *
     * @param string $userPrivateKeyB58 64-byte base58 encoded private key of user (authority)
     * @param string $masterPrivateKeyB58 64-byte base58 encoded private key of master wallet (fee payer)
     * @param string $sourceTokenAccountB58 base58 address of source ATA
     * @param string $destinationTokenAccountB58 base58 address of destination ATA
     * @param int $amountTokens raw token units to transfer (considering decimals)
     * @param string $recentBlockhashB58 recent blockhash from RPC
     * @return string base64 encoded serialized transaction
     */
    public static function buildAndSignSplTransfer(
        string $userPrivateKeyB58,
        string $masterPrivateKeyB58,
        string $sourceTokenAccountB58,
        string $destinationTokenAccountB58,
        int $amountTokens,
        string $recentBlockhashB58
    ): string {
        $userPrivateKeyBytes = SolanaBase58::decode($userPrivateKeyB58);
        $userPublicKeyBytes = substr($userPrivateKeyBytes, 32, 32);

        $masterPrivateKeyBytes = SolanaBase58::decode($masterPrivateKeyB58);
        $masterPublicKeyBytes = substr($masterPrivateKeyBytes, 32, 32);

        $sourceTokenBytes = SolanaBase58::decode($sourceTokenAccountB58);
        $destinationTokenBytes = SolanaBase58::decode($destinationTokenAccountB58);
        $blockhashBytes = SolanaBase58::decode($recentBlockhashB58);

        // Token Program ID: TokenkegQfeZyiNwAJbNbGKPFXCWuBvf9Ss623VQ5DA
        $tokenProgramBytes = SolanaBase58::decode('TokenkegQfeZyiNwAJbNbGKPFXCWuBvf9Ss623VQ5DA');

        // Accounts list:
        // Index 0: Master Public Key (signer, writable, fee payer)
        // Index 1: User Public Key (signer, writable, authority)
        // Index 2: Source Token Account (Writable)
        // Index 3: Destination Token Account (Writable)
        // Index 4: Token Program ID (Read-only, Unsigned)
        $accounts = [
            $masterPublicKeyBytes,       // Index 0
            $userPublicKeyBytes,         // Index 1
            $sourceTokenBytes,           // Index 2
            $destinationTokenBytes,      // Index 3
            $tokenProgramBytes           // Index 4
        ];

        // 1. Transaction Message Header
        // - numRequiredSignatures: 2
        // - numReadOnlySignedAccounts: 0
        // - numReadOnlyUnsignedAccounts: 1
        $header = pack('C3', 2, 0, 1);

        // 2. Account Keys list serialization
        $accountsSerialized = self::encodeCompactU16(count($accounts));
        foreach ($accounts as $account) {
            $accountsSerialized .= $account;
        }

        // 3. Instruction definition
        // - Program ID index: 4 (Token Program)
        // - Account indexes count: 3 (source, destination, owner)
        // - Account indexes: [2, 3, 1]
        // - Data: Instruction index 3 (Transfer, 1 byte) + Amount (8 bytes) = 9 bytes
        $instData = pack('C', 3) . pack('P', $amountTokens);
        $instruction = pack('C', 4) // Program index
            . self::encodeCompactU16(3) // Account indexes count
            . pack('C3', 2, 3, 1) // Account indexes
            . self::encodeCompactU16(strlen($instData)) // Data length
            . $instData;

        $instructionsSerialized = self::encodeCompactU16(1) . $instruction;

        // Compile Message
        $message = $header . $accountsSerialized . $blockhashBytes . $instructionsSerialized;

        if (!function_exists('sodium_crypto_sign_detached')) {
            throw new \RuntimeException('PHP Sodium extension is not installed or enabled.');
        }

        // Sign with fee payer (Master) first, then authority (User)
        $sigMaster = sodium_crypto_sign_detached($message, $masterPrivateKeyBytes);
        $sigUser = sodium_crypto_sign_detached($message, $userPrivateKeyBytes);

        // Compile Transaction
        // - Signatures count: 2
        // - Master Signature (64 bytes)
        // - User Signature (64 bytes)
        // - Message
        $transaction = self::encodeCompactU16(2) . $sigMaster . $sigUser . $message;

        return base64_encode($transaction);
    }

    /**
     * Build and sign a standard SPL token transfer direct from a wallet (e.g. Master Wallet).
     *
     * @param string $fromPrivateKeyB58 64-byte base58 encoded private key of source/fee payer/authority
     * @param string $sourceTokenAccountB58 base58 address of source ATA
     * @param string $destinationTokenAccountB58 base58 address of destination ATA
     * @param int $amountTokens raw token units to transfer (considering decimals)
     * @param string $recentBlockhashB58 recent blockhash from RPC
     * @return string base64 encoded serialized transaction
     */
    public static function buildAndSignSplTransferDirect(
        string $fromPrivateKeyB58,
        string $sourceTokenAccountB58,
        string $destinationTokenAccountB58,
        int $amountTokens,
        string $recentBlockhashB58
    ): string {
        $fromPrivateKeyBytes = SolanaBase58::decode($fromPrivateKeyB58);
        $fromPublicKeyBytes = substr($fromPrivateKeyBytes, 32, 32);

        $sourceTokenBytes = SolanaBase58::decode($sourceTokenAccountB58);
        $destinationTokenBytes = SolanaBase58::decode($destinationTokenAccountB58);
        $blockhashBytes = SolanaBase58::decode($recentBlockhashB58);

        // Token Program ID: TokenkegQfeZyiNwAJbNbGKPFXCWuBvf9Ss623VQ5DA
        $tokenProgramBytes = SolanaBase58::decode('TokenkegQfeZyiNwAJbNbGKPFXCWuBvf9Ss623VQ5DA');

        // Accounts list:
        // Index 0: From Public Key (signer, writable, fee payer, owner/authority)
        // Index 1: Source Token Account (Writable)
        // Index 2: Destination Token Account (Writable)
        // Index 3: Token Program ID (Read-only, Unsigned)
        $accounts = [
            $fromPublicKeyBytes,         // Index 0
            $sourceTokenBytes,           // Index 1
            $destinationTokenBytes,      // Index 2
            $tokenProgramBytes           // Index 3
        ];

        // 1. Transaction Message Header
        // - numRequiredSignatures: 1
        // - numReadOnlySignedAccounts: 0
        // - numReadOnlyUnsignedAccounts: 1
        $header = pack('C3', 1, 0, 1);

        // 2. Account Keys list serialization
        $accountsSerialized = self::encodeCompactU16(count($accounts));
        foreach ($accounts as $account) {
            $accountsSerialized .= $account;
        }

        // 3. Instruction definition
        // - Program ID index: 3 (Token Program)
        // - Account indexes count: 3 (source, destination, owner)
        // - Account indexes: [1, 2, 0]
        // - Data: Instruction index 3 (Transfer, 1 byte) + Amount (8 bytes) = 9 bytes
        $instData = pack('C', 3) . pack('P', $amountTokens);
        $instruction = pack('C', 3) // Program index
            . self::encodeCompactU16(3) // Account indexes count
            . pack('C3', 1, 2, 0) // Account indexes
            . self::encodeCompactU16(strlen($instData)) // Data length
            . $instData;

        $instructionsSerialized = self::encodeCompactU16(1) . $instruction;

        // Compile Message
        $message = $header . $accountsSerialized . $blockhashBytes . $instructionsSerialized;

        if (!function_exists('sodium_crypto_sign_detached')) {
            throw new \RuntimeException('PHP Sodium extension is not installed or enabled.');
        }

        $signature = sodium_crypto_sign_detached($message, $fromPrivateKeyBytes);

        // Compile Transaction
        // - Signatures count: 1
        // - Signature (64 bytes)
        // - Message
        $transaction = self::encodeCompactU16(1) . $signature . $message;

        return base64_encode($transaction);
    }

    /**
     * Build and sign a transaction that first creates the recipient's Associated Token Account
     * and then transfers the SPL tokens to it.
     */
    public static function buildAndSignSplTransferWithAtaCreation(
        string $fromPrivateKeyB58,
        string $sourceTokenAccountB58,
        string $recipientOwnerB58,
        string $destinationTokenAccountB58,
        string $tokenMintB58,
        int $amountTokens,
        string $recentBlockhashB58
    ): string {
        $fromPrivateKeyBytes = SolanaBase58::decode($fromPrivateKeyB58);
        $fromPublicKeyBytes = substr($fromPrivateKeyBytes, 32, 32);

        $sourceTokenBytes = SolanaBase58::decode($sourceTokenAccountB58);
        $recipientOwnerBytes = SolanaBase58::decode($recipientOwnerB58);
        $destinationTokenBytes = SolanaBase58::decode($destinationTokenAccountB58);
        $mintBytes = SolanaBase58::decode($tokenMintB58);
        $blockhashBytes = SolanaBase58::decode($recentBlockhashB58);

        // System Program: 11111111111111111111111111111111
        $systemProgramBytes = str_repeat("\x00", 32);
        // Token Program: TokenkegQfeZyiNwAJbNbGKPFXCWuBvf9Ss623VQ5DA
        $tokenProgramBytes = SolanaBase58::decode('TokenkegQfeZyiNwAJbNbGKPFXCWuBvf9Ss623VQ5DA');
        // Associated Token Program: ATokenGPvbdGVxr1b2hvZbsiqW5xWH25efTNsLJA8knL
        $associatedTokenProgramBytes = SolanaBase58::decode('ATokenGPvbdGVxr1b2hvZbsiqW5xWH25efTNsLJA8knL');
        // Rent Sysvar: SysvarRent111111111111111111111111111111111
        $rentSysvarBytes = SolanaBase58::decode('SysvarRent111111111111111111111111111111111');

        // Accounts list (ordered):
        // Index 0: Fee Payer / Source Owner (Writable, Signer)
        // Index 1: Source Token Account (Writable, Unsigned)
        // Index 2: Destination Token Account (Writable, Unsigned)
        // Index 3: Recipient Owner Wallet (Read-only, Unsigned)
        // Index 4: Token Mint (Read-only, Unsigned)
        // Index 5: System Program ID (Read-only, Unsigned)
        // Index 6: Token Program ID (Read-only, Unsigned)
        // Index 7: Associated Token Program ID (Read-only, Unsigned)
        // Index 8: Rent Sysvar (Read-only, Unsigned)
        $accounts = [
            $fromPublicKeyBytes,           // Index 0
            $sourceTokenBytes,             // Index 1
            $destinationTokenBytes,        // Index 2
            $recipientOwnerBytes,          // Index 3
            $mintBytes,                    // Index 4
            $systemProgramBytes,           // Index 5
            $tokenProgramBytes,            // Index 6
            $associatedTokenProgramBytes,  // Index 7
            $rentSysvarBytes               // Index 8
        ];

        // 1. Transaction Message Header
        // - numRequiredSignatures: 1
        // - numReadOnlySignedAccounts: 0
        // - numReadOnlyUnsignedAccounts: 6
        $header = pack('C3', 1, 0, 6);

        // 2. Account Keys list serialization
        $accountsSerialized = self::encodeCompactU16(count($accounts));
        foreach ($accounts as $account) {
            $accountsSerialized .= $account;
        }

        // 3. Instruction 1: Create ATA
        // - Program ID index: 7 (Associated Token Program ID)
        // - Account indexes count: 7
        // - Account indexes: [0, 2, 3, 4, 5, 6, 8]
        // - Data: Empty
        $inst1 = pack('C', 7)
            . self::encodeCompactU16(7)
            . pack('C7', 0, 2, 3, 4, 5, 6, 8)
            . self::encodeCompactU16(0);

        // 4. Instruction 2: SPL Token Transfer
        // - Program ID index: 6 (Token Program ID)
        // - Account indexes count: 3
        // - Account indexes: [1, 2, 0]
        // - Data: opcode 3 (1 byte) + amount (8 bytes)
        $inst2Data = pack('C', 3) . pack('P', $amountTokens);
        $inst2 = pack('C', 6)
            . self::encodeCompactU16(3)
            . pack('C3', 1, 2, 0)
            . self::encodeCompactU16(strlen($inst2Data))
            . $inst2Data;

        // Compile all instructions
        $instructionsSerialized = self::encodeCompactU16(2) . $inst1 . $inst2;

        // Compile Message
        $message = $header . $accountsSerialized . $blockhashBytes . $instructionsSerialized;

        if (!function_exists('sodium_crypto_sign_detached')) {
            throw new \RuntimeException('PHP Sodium extension is not installed or enabled.');
        }

        $signature = sodium_crypto_sign_detached($message, $fromPrivateKeyBytes);

        // Compile Transaction
        // - Signatures count: 1
        // - Signature (64 bytes)
        // - Message
        $transaction = self::encodeCompactU16(1) . $signature . $message;

        return base64_encode($transaction);
    }
}
