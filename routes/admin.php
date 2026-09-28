<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepositController;
use App\Http\Controllers\Admin\FileManager\CodeEditorController;
use App\Http\Controllers\Admin\FileManager\FileController;
use App\Http\Controllers\Admin\KycController;
use App\Http\Controllers\Admin\Settings\ActivationController;
use App\Http\Controllers\Admin\Settings\BonusSystemController;
use App\Http\Controllers\Admin\Settings\CertificateController;
use App\Http\Controllers\Admin\Settings\BlockchainController;
use App\Http\Controllers\Admin\Settings\CoreController;
use App\Http\Controllers\Admin\Settings\LiveChatController;
use App\Http\Controllers\Admin\Settings\ModuleController;
use App\Http\Controllers\Admin\Settings\TeamController;
use App\Http\Controllers\Admin\Settings\ReviewController;
use App\Http\Controllers\Admin\Settings\EmailController;
use App\Http\Controllers\Admin\Settings\FinancialController;
use App\Http\Controllers\Admin\Settings\MenuController;
use App\Http\Controllers\Admin\Settings\OveriewController;
use App\Http\Controllers\Admin\Settings\SecurityController;
use App\Http\Controllers\Admin\Settings\SeoController;
use App\Http\Controllers\Admin\Settings\FaqController;
use App\Http\Controllers\Admin\Settings\UtilityController;
use App\Http\Controllers\Admin\Trading\TradingBotController;
use App\Http\Controllers\Admin\Trading\CopyTradingController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WithdrawalController;
use App\Http\Controllers\Admin\ReferralNetworkController;
use App\Http\Controllers\Admin\Update\PrecheckController;
use App\Http\Controllers\Admin\Update\UpdateController;
use Illuminate\Support\Facades\Route;

// Guests only — logged-in admins cannot access these
Route::middleware(['guest:admin', 'sandbox'])->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'loginValidate'])->name('login.validate')->withoutMiddleware('sandbox');
    Route::get('/login/{provider}', [LoginController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('/login/{provider}/callback', [LoginController::class, 'handleGoogleCallback'])->name('login.google.callback');
    Route::get('/forgot-password', [LoginController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('/forgot-password', [LoginController::class, 'sendResetCode'])->name('forgot-password.send');
    Route::get('/forgot-password/otp', [LoginController::class, 'resetOtp'])->name('forgot-password.otp');
    Route::post('/forgot-password/otp', [LoginController::class, 'validateResetOtp'])->name('forgot-password.otp.validate');
    Route::get('/reset-password', [LoginController::class, 'resetPasswordForm'])->name('reset-password');
    Route::post('/reset-password', [LoginController::class, 'updatePassword'])->name('reset-password.update');
});


// authenticated but not otp verified
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth:admin');
Route::middleware(['auth:admin'])->prefix('login')->name('login.')->group(function () {
    Route::get('/verify/otp', [LoginController::class, 'otp'])->name('otp');
    Route::post('/verify/otp', [LoginController::class, 'validateOtp'])->name('otp.validate');
    Route::post('/verify/resend-otp', [LoginController::class, 'resendOtp'])->name('otp.resend');
});

Route::middleware(['auth:admin', 'admin.otp.verified', 'sandbox'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/notifications/seen', [DashboardController::class, 'markNotificationSeen'])->name('notifications.seen');

    // Solana Master Wallet routes
    Route::prefix('solana-master-wallet')->name('solana-master-wallet.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SolanaMasterWalletController::class, 'index'])->name('index');
        Route::post('/generate', [\App\Http\Controllers\Admin\SolanaMasterWalletController::class, 'generate'])->name('generate');
        Route::post('/reveal', [\App\Http\Controllers\Admin\SolanaMasterWalletController::class, 'reveal'])->name('reveal');
        Route::get('/balance', [\App\Http\Controllers\Admin\SolanaMasterWalletController::class, 'getBalance'])->name('balance');
        Route::get('/history', [\App\Http\Controllers\Admin\SolanaMasterWalletController::class, 'history'])->name('history');
    });

    // Tron Master Wallet routes
    Route::prefix('tron-master-wallet')->name('tron-master-wallet.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\TronMasterWalletController::class, 'index'])->name('index');
        Route::post('/generate', [\App\Http\Controllers\Admin\TronMasterWalletController::class, 'generate'])->name('generate');
        Route::post('/reveal', [\App\Http\Controllers\Admin\TronMasterWalletController::class, 'reveal'])->name('reveal');
        Route::get('/history', [\App\Http\Controllers\Admin\TronMasterWalletController::class, 'history'])->name('history');
    });

    // Bitcoin Master Wallet routes
    Route::prefix('bitcoin-master-wallet')->name('bitcoin-master-wallet.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\BitcoinMasterWalletController::class, 'index'])->name('index');
        Route::post('/generate', [\App\Http\Controllers\Admin\BitcoinMasterWalletController::class, 'generate'])->name('generate');
        Route::post('/reveal', [\App\Http\Controllers\Admin\BitcoinMasterWalletController::class, 'reveal'])->name('reveal');
        Route::get('/history', [\App\Http\Controllers\Admin\BitcoinMasterWalletController::class, 'history'])->name('history');
    });

    Route::post('deposits/master-wallets/sync', [\App\Http\Controllers\Admin\EvmMasterWalletController::class, 'sync'])->name('evm-master-wallet.sync');

    // Unified EVM Master Wallet routes (Handles Ethereum, BSC, Base, Polygon, Arbitrum, Optimism, etc.)
    Route::prefix('{blockchain}-master-wallet')->name('evm-master-wallet.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\EvmMasterWalletController::class, 'index'])->name('index');
        Route::post('/generate', [\App\Http\Controllers\Admin\EvmMasterWalletController::class, 'generate'])->name('generate');
        Route::post('/reveal', [\App\Http\Controllers\Admin\EvmMasterWalletController::class, 'reveal'])->name('reveal');
        Route::get('/balance', [\App\Http\Controllers\Admin\EvmMasterWalletController::class, 'getBalance'])->name('balance');
        Route::get('/history', [\App\Http\Controllers\Admin\EvmMasterWalletController::class, 'history'])->name('history');
    });

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/bulk-action', [UserController::class, 'bulkAction'])->name('bulk-action');
        Route::post('/bulk', [UserController::class, 'bulkAction'])->name('bulk');
        Route::get('/view/{id}', [UserController::class, 'detail'])->name('detail');
        Route::put('/update/{id}', [UserController::class, 'update'])->name('update');
        Route::post('/status/{id}', [UserController::class, 'updateStatus'])->name('status');
        Route::post('/password/{id}', [UserController::class, 'updatePassword'])->name('password');
        Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('delete');
        Route::post('/credit-debit/{id}', [UserController::class, 'creditDebit'])->name('credit-debit')->withoutMiddleware('sandbox');
        Route::post('/login-as/{id}', [UserController::class, 'loginAs'])->name('login-as')->withoutMiddleware('sandbox');
        Route::post('/send-email/{id}', [UserController::class, 'sendEmail'])->name('send-email');
        Route::post('/email/{id}', [UserController::class, 'sendEmail'])->name('email');
        Route::get('/bulk-email', [UserController::class, 'bulkEmail'])->name('bulk-email');
        Route::post('/send-bulk-email', [UserController::class, 'sendBulkEmail'])->name('send-bulk-email');
    });


    // Global Trading Bots
    Route::prefix('trading-bots')->name('trading-bots.')->group(function () {
        Route::get('/', [TradingBotController::class, 'index'])->name('index');
        Route::get('create', [TradingBotController::class, 'create'])->name('create');
        Route::post('store', [TradingBotController::class, 'store'])->name('store');
        Route::get('edit/{id}', [TradingBotController::class, 'edit'])->name('edit');
        Route::post('update/{id}', [TradingBotController::class, 'update'])->name('update');
        Route::post('delete/{id}', [TradingBotController::class, 'destroy'])->name('delete');

        // Activations
        Route::get('activations', [TradingBotController::class, 'activations'])->name('activations.index');
        Route::post('activations/status/{id}', [TradingBotController::class, 'updateActivationStatus'])->name('activations.status');
        Route::post('activations/delete/{id}', [TradingBotController::class, 'deleteActivation'])->name('activations.delete');

        // Logs
        Route::get('logs', [TradingBotController::class, 'logs'])->name('logs.index');

        // AJAX Chart Data
        Route::get('chart-data', [TradingBotController::class, 'chartData'])->name('chart-data');

        // Import Default Bots
        Route::post('import-defaults', [TradingBotController::class, 'importDefaults'])->name('import-defaults');
    });

    // Copy Trading
    Route::prefix('copy-trading')->name('copy-trading.')->group(function () {
        Route::get('/', [CopyTradingController::class, 'index'])->name('index');
        Route::get('create', [CopyTradingController::class, 'create'])->name('create');
        Route::post('store', [CopyTradingController::class, 'store'])->name('store');
        Route::get('edit/{id}', [CopyTradingController::class, 'edit'])->name('edit');
        Route::post('update/{id}', [CopyTradingController::class, 'update'])->name('update');
        Route::post('delete/{id}', [CopyTradingController::class, 'destroy'])->name('delete');
        Route::get('history', [CopyTradingController::class, 'history'])->name('history');
        Route::get('get-signal-messages/{id}', [CopyTradingController::class, 'getSignalMessages'])->name('get-signal-messages');
        Route::get('signals/{id}', [CopyTradingController::class, 'getSignalMessages'])->name('signals');
        Route::get('chart-data', [CopyTradingController::class, 'chartData'])->name('chart-data');
        Route::post('history/delete/{id}', [CopyTradingController::class, 'destroyHistory'])->name('history.delete');
    });

    // Deposits
    Route::prefix('deposits')->name('deposits.')->group(function () {
        Route::get('/', [DepositController::class, 'index'])->name('index');
        Route::get('view/{id}', [DepositController::class, 'viewDeposit'])->name('view');
        Route::post('edit/{id}', [DepositController::class, 'update'])->name('update')->withoutMiddleware('sandbox');
        Route::post('delete/{id}', [DepositController::class, 'delete'])->name('delete');
        Route::get('wallets', [DepositController::class, 'userWallets'])->name('wallets');
        Route::post('wallets/{id}/reveal', [DepositController::class, 'revealUserWalletKey'])->name('wallets.reveal');
        Route::get('master-wallets', [DepositController::class, 'masterWallets'])->name('master-wallets');
        Route::post('master-wallets/reset', [DepositController::class, 'resetMasterWallets'])->name('master-wallets.reset');
    });

    // Withdrawals
    Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
        Route::get('/', [WithdrawalController::class, 'index'])->name('index');
        Route::get('view/{id}', [WithdrawalController::class, 'viewWithdrawal'])->name('view');
        Route::post('edit/{id}', [WithdrawalController::class, 'update'])->name('update')->withoutMiddleware('sandbox');
        Route::post('delete/{id}', [WithdrawalController::class, 'delete'])->name('delete');
    });

    // Kyc
    Route::prefix('kyc')->name('kyc.')->group(function () {
        Route::get('/', [KycController::class, 'index'])->name('index');
        Route::get('view/{id}', [KycController::class, 'viewKyc'])->name('view');
        Route::post('edit/{id}', [KycController::class, 'update'])->name('update');
        Route::post('delete/{id}', [KycController::class, 'delete'])->name('delete');
    });

    // Transactions
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::post('/delete/{id}', [TransactionController::class, 'delete'])->name('delete');
        Route::post('/bulk-delete', [TransactionController::class, 'bulkDelete'])->name('bulk-delete');
    });

    // Referral Network
    Route::prefix('referrals')->name('referrals.')->group(function () {
        Route::get('/', [ReferralNetworkController::class, 'index'])->name('index');
    });




    // settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [OveriewController::class, 'index'])->name('index');
        Route::get('legal', [OveriewController::class, 'legal'])->name('legal');
        // activation
        Route::get('activation', [ActivationController::class, 'index'])->name('activation');
        Route::post('activation', [ActivationController::class, 'update'])->name('activation.update');
        // system
        Route::get('system', [OveriewController::class, 'system'])->name('system');
        Route::post('system/clear-cache', [OveriewController::class, 'clearCache'])->name('system.clear-cache');
        Route::post('system/update-env', [OveriewController::class, 'updateEnvSetting'])->name('system.update-env');
        //Audit
        Route::get('audit', [OveriewController::class, 'audit'])->name('audit');
        Route::get('audit/pdf', [OveriewController::class, 'auditPdf'])->name('audit.pdf');
        //core
        Route::get('core', [CoreController::class, 'index'])->name('core');
        Route::post('core', [CoreController::class, 'update'])->name('core.update');
        //email
        Route::get('email', [EmailController::class, 'index'])->name('email');
        Route::post('email', [EmailController::class, 'update'])->name('email.update');
        Route::post('email/test', [EmailController::class, 'test'])->name('email.test');
        // cronjob
        Route::get('cronjob', [OveriewController::class, 'cronJob'])->name('cronjob');

        // financial
        Route::get('financial', [FinancialController::class, 'index'])->name('financial');
        Route::post('financial', [FinancialController::class, 'update'])->name('financial.update');

        // security
        Route::get('security', [SecurityController::class, 'index'])->name('security');
        Route::post('security', [SecurityController::class, 'update'])->name('security.update');

        // bonus system
        Route::get('bonus-system', [BonusSystemController::class, 'index'])->name('bonus-system');
        Route::post('bonus-system', [BonusSystemController::class, 'update'])->name('bonus-system.update');

        // certificate
        Route::get('certificate', [CertificateController::class, 'index'])->name('certificate');
        Route::post('certificate', [CertificateController::class, 'update'])->name('certificate.update');

        // seo
        Route::get('seo', [SeoController::class, 'index'])->name('seo');
        Route::post('seo', [SeoController::class, 'update'])->name('seo.update');

        // utility
        Route::get('utility', [UtilityController::class, 'index'])->name('utility');
        Route::post('utility', [UtilityController::class, 'update'])->name('utility.update');

        // livechat
        Route::get('livechat', [LiveChatController::class, 'index'])->name('livechat');
        Route::post('livechat', [LiveChatController::class, 'update'])->name('livechat.update');

        // login methods
        Route::get('login-method', [\App\Http\Controllers\Admin\Settings\LoginMethodController::class, 'index'])->name('login-method');
        Route::post('login-method', [\App\Http\Controllers\Admin\Settings\LoginMethodController::class, 'update'])->name('login-method.update');

        // menu
        Route::get('menu', [MenuController::class, 'index'])->name('menu');
        Route::post('menu', [MenuController::class, 'update'])->name('menu.update');
        Route::post('menu/create', [MenuController::class, 'create'])->name('menu.create');
        Route::post('menu/reorder', [MenuController::class, 'reorder'])->name('menu.reorder');

        // blockchain
        Route::prefix('blockchain')->name('blockchain.')->group(function () {
            Route::get('/', [BlockchainController::class, 'index'])->name('index');
            Route::post('blockchains/store', [BlockchainController::class, 'blockchainStore'])->name('blockchains.store');
            Route::post('blockchains/reorder', [BlockchainController::class, 'reorder'])->name('blockchains.reorder');
            Route::post('blockchains/update/{id}', [BlockchainController::class, 'blockchainUpdate'])->name('blockchains.update');
            Route::post('blockchains/toggle-status/{id}', [BlockchainController::class, 'blockchainToggleStatus'])->name('blockchains.toggle-status');
            Route::post('tokens/store', [BlockchainController::class, 'tokenStore'])->name('tokens.store');
            Route::post('tokens/update/{id}', [BlockchainController::class, 'tokenUpdate'])->name('tokens.update');
            Route::post('tokens/toggle-status/{id}', [BlockchainController::class, 'tokenToggleStatus'])->name('tokens.toggle-status');
            Route::post('tokens/delete/{id}', [BlockchainController::class, 'tokenDelete'])->name('tokens.delete');
        });


        // Modules
        Route::prefix('modules')->name('modules.')->group(function () {
            Route::get('/', [ModuleController::class, 'index'])->name('index');
            Route::post('update', [ModuleController::class, 'update'])->name('update');
        });

        //management team
        Route::prefix('management-team')->name('management-team.')->group(function () {
            Route::get('/', [TeamController::class, 'index'])->name('index');
            Route::post('update/{id}', [TeamController::class, 'update'])->name('update');
            Route::post('delete/{id}', [TeamController::class, 'delete'])->name('delete');
            Route::post('create', [TeamController::class, 'create'])->name('create');
        });

        // FAQ Management
        Route::prefix('faq')->name('faq.')->group(function () {
            Route::get('/', [FaqController::class, 'index'])->name('index');
            Route::post('store', [FaqController::class, 'store'])->name('store');
            Route::post('update/{id}', [FaqController::class, 'update'])->name('update');
            Route::post('delete/{id}', [FaqController::class, 'destroy'])->name('delete');
        });

        //client reviews
        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::get('/', [ReviewController::class, 'index'])->name('index');
            Route::post('update/{id}', [ReviewController::class, 'update'])->name('update');
            Route::post('delete/{id}', [ReviewController::class, 'delete'])->name('delete');
            Route::post('create', [ReviewController::class, 'create'])->name('create');
        });


    });

    // Account Settings
    Route::prefix('account')->name('account.')->middleware('sandbox')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::get('profile', [AccountController::class, 'profile'])->name('profile');
        Route::post('profile', [AccountController::class, 'profileUpdate'])->name('profile.update');
        Route::get('security', [AccountController::class, 'security'])->name('security');
        Route::post('password', [AccountController::class, 'passwordUpdate'])->name('password.update');
        Route::post('sessions/logout-other', [AccountController::class, 'logoutOtherDevices'])->name('sessions.logout-other');
    });

    // code editor
    Route::prefix('file-manager')->name('file-manager.')->middleware('sandbox')->group(function () {
        Route::get('/', [FileController::class, 'index'])->name('index');
        Route::get('download', [FileController::class, 'download'])->name('download');
        Route::get('view', [FileController::class, 'view'])->name('view');
        Route::post('delete', [FileController::class, 'delete'])->name('delete');
        Route::post('upload', [FileController::class, 'upload'])->name('upload');
        Route::post('create', [FileController::class, 'create'])->name('create');
        Route::post('rename', [FileController::class, 'rename'])->name('rename');
        Route::post('move', [FileController::class, 'move'])->name('move');
        Route::post('copy', [FileController::class, 'copy'])->name('copy');
        Route::post('permission', [FileController::class, 'permission'])->name('permission');
        Route::post('bulk-delete', [FileController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('bulk-move', [FileController::class, 'bulkMove'])->name('bulk-move');
        Route::post('bulk-copy', [FileController::class, 'bulkCopy'])->name('bulk-copy');
        Route::post('bulk-zip', [FileController::class, 'bulkZip'])->name('bulk-zip');
        Route::get('/code-editor', [CodeEditorController::class, 'index'])->name('code-editor');
        Route::post('/code-editor', [CodeEditorController::class, 'update'])->name('code-editor.update');
    });


    // update
    Route::prefix('update')->name('update.')->middleware('sandbox')->group(function () {
        Route::get('/', [PrecheckController::class, 'index'])->name('index');
        Route::post('/verify-requirements', [PrecheckController::class, 'verifyRequirements'])->name('verify-requirements');
        Route::post('/download-updater', [PrecheckController::class, 'updateUpdater'])->name('download-updater');

        Route::prefix('process')->name('process.')->group(function () {
            Route::get('/', [UpdateController::class, 'index'])->name('index');
            Route::post('/init-cleanup', [UpdateController::class, 'initCleanup'])->name('init-cleanup');
            Route::post('/download', [UpdateController::class, 'download'])->name('download');
            Route::post('/extract', [UpdateController::class, 'extract'])->name('extract');
            Route::post('/sanitize', [UpdateController::class, 'sanitize'])->name('sanitize');
            Route::post('/replace', [UpdateController::class, 'replace'])->name('replace');
            Route::post('/cleanup', [UpdateController::class, 'cleanup'])->name('cleanup');
        });
    });

});
