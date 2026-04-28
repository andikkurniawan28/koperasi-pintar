<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BalanceSheetController;
use App\Http\Controllers\CashFlowController;
use App\Http\Controllers\AutoJournalController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LoanTypeController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfitLossController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SavingController;
use App\Http\Controllers\SavingTransactionByMemberReportController;
use App\Http\Controllers\SavingTypeController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockLedgerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WithdrawController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'loginProcess'])->name('loginProcess');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/', HomeController::class)->name('home')->middleware(['auth']);

Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::middleware(['auth'])->group(function () {

    Route::get('user', [UserController::class, 'index'])->name('user.index')->middleware('role:Admin');
    Route::get('user/create', [UserController::class, 'create'])->name('user.create')->middleware('role:Admin');
    Route::post('user', [UserController::class, 'store'])->name('user.store')->middleware('role:Admin');
    Route::get('user/{user}/edit', [UserController::class, 'edit'])->name('user.edit')->middleware('role:Admin');
    Route::put('user/{user}', [UserController::class, 'update'])->name('user.update')->middleware('role:Admin');
    Route::delete('user/{user}', [UserController::class, 'destroy'])->name('user.destroy')->middleware('role:Admin');

    Route::get('customer', [CustomerController::class, 'index'])->name('customer.index')->middleware('role:Admin');
    Route::get('customer/create', [CustomerController::class, 'create'])->name('customer.create')->middleware('role:Admin');
    Route::post('customer', [CustomerController::class, 'store'])->name('customer.store')->middleware('role:Admin');
    Route::get('customer/{customer}/edit', [CustomerController::class, 'edit'])->name('customer.edit')->middleware('role:Admin');
    Route::put('customer/{customer}', [CustomerController::class, 'update'])->name('customer.update')->middleware('role:Admin');
    Route::delete('customer/{customer}', [CustomerController::class, 'destroy'])->name('customer.destroy')->middleware('role:Admin');

    Route::get('member', [MemberController::class, 'index'])->name('member.index')->middleware('role:Admin');
    Route::get('member/create', [MemberController::class, 'create'])->name('member.create')->middleware('role:Admin');
    Route::post('member', [MemberController::class, 'store'])->name('member.store')->middleware('role:Admin');
    Route::get('member/{member}/edit', [MemberController::class, 'edit'])->name('member.edit')->middleware('role:Admin');
    Route::put('member/{member}', [MemberController::class, 'update'])->name('member.update')->middleware('role:Admin');
    Route::delete('member/{member}', [MemberController::class, 'destroy'])->name('member.destroy')->middleware('role:Admin');

    Route::get('supplier', [SupplierController::class, 'index'])->name('supplier.index')->middleware('role:Admin');
    Route::get('supplier/create', [SupplierController::class, 'create'])->name('supplier.create')->middleware('role:Admin');
    Route::post('supplier', [SupplierController::class, 'store'])->name('supplier.store')->middleware('role:Admin');
    Route::get('supplier/{supplier}/edit', [SupplierController::class, 'edit'])->name('supplier.edit')->middleware('role:Admin');
    Route::put('supplier/{supplier}', [SupplierController::class, 'update'])->name('supplier.update')->middleware('role:Admin');
    Route::delete('supplier/{supplier}', [SupplierController::class, 'destroy'])->name('supplier.destroy')->middleware('role:Admin');

    Route::get('account', [AccountController::class, 'index'])->name('account.index')->middleware('role:Admin');
    Route::get('account/create', [AccountController::class, 'create'])->name('account.create')->middleware('role:Admin');
    Route::post('account', [AccountController::class, 'store'])->name('account.store')->middleware('role:Admin');
    Route::get('account/{account}/edit', [AccountController::class, 'edit'])->name('account.edit')->middleware('role:Admin');
    Route::put('account/{account}', [AccountController::class, 'update'])->name('account.update')->middleware('role:Admin');
    Route::delete('account/{account}', [AccountController::class, 'destroy'])->name('account.destroy')->middleware('role:Admin');

    Route::get('saving_type', [SavingTypeController::class, 'index'])->name('saving_type.index')->middleware('role:Admin');
    Route::get('saving_type/create', [SavingTypeController::class, 'create'])->name('saving_type.create')->middleware('role:Admin');
    Route::post('saving_type', [SavingTypeController::class, 'store'])->name('saving_type.store')->middleware('role:Admin');
    Route::get('saving_type/{saving_type}/edit', [SavingTypeController::class, 'edit'])->name('saving_type.edit')->middleware('role:Admin');
    Route::put('saving_type/{saving_type}', [SavingTypeController::class, 'update'])->name('saving_type.update')->middleware('role:Admin');
    Route::delete('saving_type/{saving_type}', [SavingTypeController::class, 'destroy'])->name('saving_type.destroy')->middleware('role:Admin');

    Route::get('loan_type', [LoanTypeController::class, 'index'])->name('loan_type.index')->middleware('role:Admin');
    Route::get('loan_type/create', [LoanTypeController::class, 'create'])->name('loan_type.create')->middleware('role:Admin');
    Route::post('loan_type', [LoanTypeController::class, 'store'])->name('loan_type.store')->middleware('role:Admin');
    Route::get('loan_type/{loan_type}/edit', [LoanTypeController::class, 'edit'])->name('loan_type.edit')->middleware('role:Admin');
    Route::put('loan_type/{loan_type}', [LoanTypeController::class, 'update'])->name('loan_type.update')->middleware('role:Admin');
    Route::delete('loan_type/{loan_type}', [LoanTypeController::class, 'destroy'])->name('loan_type.destroy')->middleware('role:Admin');

    Route::get('product', [ProductController::class, 'index'])->name('product.index')->middleware('role:Admin');
    Route::get('product/create', [ProductController::class, 'create'])->name('product.create')->middleware('role:Admin');
    Route::post('product', [ProductController::class, 'store'])->name('product.store')->middleware('role:Admin');
    Route::get('product/{product}/edit', [ProductController::class, 'edit'])->name('product.edit')->middleware('role:Admin');
    Route::put('product/{product}', [ProductController::class, 'update'])->name('product.update')->middleware('role:Admin');
    Route::delete('product/{product}', [ProductController::class, 'destroy'])->name('product.destroy')->middleware('role:Admin');

    Route::get('sales', [SalesController::class, 'index'])->name('sales.index')->middleware('role:Admin, Kasir');
    Route::get('sales/create', [SalesController::class, 'create'])->name('sales.create')->middleware('role:Admin, Kasir');
    Route::post('sales', [SalesController::class, 'store'])->name('sales.store')->middleware('role:Admin, Kasir');
    Route::get('sales/{sales}', [SalesController::class, 'show'])->name('sales.show')->middleware('role:Admin, Kasir');
    Route::delete('sales/{sales}', [SalesController::class, 'destroy'])->name('sales.destroy')->middleware('role:Admin');

    Route::get('purchase', [PurchaseController::class, 'index'])->name('purchase.index')->middleware('role:Admin, Kasir');
    Route::get('purchase/create', [PurchaseController::class, 'create'])->name('purchase.create')->middleware('role:Admin, Kasir');
    Route::post('purchase', [PurchaseController::class, 'store'])->name('purchase.store')->middleware('role:Admin, Kasir');
    Route::get('purchase/{purchase}', [PurchaseController::class, 'show'])->name('purchase.show')->middleware('role:Admin, Kasir');
    Route::delete('purchase/{purchase}', [PurchaseController::class, 'destroy'])->name('purchase.destroy')->middleware('role:Admin');

    Route::get('stock_adjustment', [StockAdjustmentController::class, 'index'])->name('stock_adjustment.index')->middleware('role:Admin, Kasir');
    Route::get('stock_adjustment/create', [StockAdjustmentController::class, 'create'])->name('stock_adjustment.create')->middleware('role:Admin, Kasir');
    Route::post('stock_adjustment', [StockAdjustmentController::class, 'store'])->name('stock_adjustment.store')->middleware('role:Admin, Kasir');
    Route::get('stock_adjustment/{stock_adjustment}', [StockAdjustmentController::class, 'show'])->name('stock_adjustment.show')->middleware('role:Admin, Kasir');
    Route::delete('stock_adjustment/{stock_adjustment}', [StockAdjustmentController::class, 'destroy'])->name('stock_adjustment.destroy')->middleware('role:Admin');

    Route::get('invoice', [InvoiceController::class, 'index'])->name('invoice.index')->middleware('role:Admin, Kasir');
    Route::get('invoice/create', [InvoiceController::class, 'create'])->name('invoice.create')->middleware('role:Admin, Kasir');
    Route::post('invoice', [InvoiceController::class, 'store'])->name('invoice.store')->middleware('role:Admin, Kasir');
    Route::get('invoice/{invoice}', [InvoiceController::class, 'show'])->name('invoice.show')->middleware('role:Admin, Kasir');
    Route::delete('invoice/{invoice}', [InvoiceController::class, 'destroy'])->name('invoice.destroy')->middleware('role:Admin');

    Route::get('payment', [PaymentController::class, 'index'])->name('payment.index')->middleware('role:Admin, Kasir');
    Route::get('payment/create', [PaymentController::class, 'create'])->name('payment.create')->middleware('role:Admin, Kasir');
    Route::post('payment', [PaymentController::class, 'store'])->name('payment.store')->middleware('role:Admin, Kasir');
    Route::get('payment/{payment}', [PaymentController::class, 'show'])->name('payment.show')->middleware('role:Admin, Kasir');
    Route::delete('payment/{payment}', [PaymentController::class, 'destroy'])->name('payment.destroy')->middleware('role:Admin');

    Route::get('saving', [SavingController::class, 'index'])->name('saving.index')->middleware('role:Admin, Kasir');
    Route::get('saving/create', [SavingController::class, 'create'])->name('saving.create')->middleware('role:Admin, Kasir');
    Route::post('saving', [SavingController::class, 'store'])->name('saving.store')->middleware('role:Admin, Kasir');
    Route::get('saving/{saving}', [SavingController::class, 'show'])->name('saving.show')->middleware('role:Admin, Kasir');
    Route::delete('saving/{saving}', [SavingController::class, 'destroy'])->name('saving.destroy')->middleware('role:Admin');

    Route::get('withdraw', [WithdrawController::class, 'index'])->name('withdraw.index')->middleware('role:Admin, Kasir');
    Route::get('withdraw/create', [WithdrawController::class, 'create'])->name('withdraw.create')->middleware('role:Admin, Kasir');
    Route::post('withdraw', [WithdrawController::class, 'store'])->name('withdraw.store')->middleware('role:Admin, Kasir');
    Route::get('withdraw/{withdraw}', [WithdrawController::class, 'show'])->name('withdraw.show')->middleware('role:Admin, Kasir');
    Route::delete('withdraw/{withdraw}', [WithdrawController::class, 'destroy'])->name('withdraw.destroy')->middleware('role:Admin');

    Route::get('loan', [LoanController::class, 'index'])->name('loan.index')->middleware('role:Admin, Kasir');
    Route::get('loan/create', [LoanController::class, 'create'])->name('loan.create')->middleware('role:Admin, Kasir');
    Route::post('loan', [LoanController::class, 'store'])->name('loan.store')->middleware('role:Admin, Kasir');
    Route::get('loan/{loan}', [LoanController::class, 'show'])->name('loan.show')->middleware('role:Admin, Kasir');
    Route::delete('loan/{loan}', [LoanController::class, 'destroy'])->name('loan.destroy')->middleware('role:Admin');

    Route::get('installment', [InstallmentController::class, 'index'])->name('installment.index')->middleware('role:Admin, Kasir');
    Route::get('installment/create', [InstallmentController::class, 'create'])->name('installment.create')->middleware('role:Admin, Kasir');
    Route::post('installment', [InstallmentController::class, 'store'])->name('installment.store')->middleware('role:Admin, Kasir');
    Route::get('installment/{installment}', [InstallmentController::class, 'show'])->name('installment.show')->middleware('role:Admin, Kasir');
    Route::delete('installment/{installment}', [InstallmentController::class, 'destroy'])->name('installment.destroy')->middleware('role:Admin');

    Route::get('journal', [JournalController::class, 'index'])->name('journal.index')->middleware('role:Admin, Kasir');
    Route::get('journal/create', [JournalController::class, 'create'])->name('journal.create')->middleware('role:Admin, Kasir');
    Route::post('journal', [JournalController::class, 'store'])->name('journal.store')->middleware('role:Admin, Kasir');
    Route::get('journal/{journal}', [JournalController::class, 'show'])->name('journal.show')->middleware('role:Admin, Kasir');
    Route::delete('journal/{journal}', [JournalController::class, 'destroy'])->name('journal.destroy')->middleware('role:Admin');

    Route::get('ledger', [LedgerController::class, 'index'])->name('ledger.index')->middleware('role:Admin');
    Route::get('stock_ledger', [StockLedgerController::class, 'index'])->name('stock_ledger.index')->middleware('role:Admin');
    Route::get('cash_flow', [CashFlowController::class, 'index'])->name('cash_flow.index')->middleware('role:Admin');
    Route::get('profit_loss', [ProfitLossController::class, 'index'])->name('profit_loss.index')->middleware('role:Admin');
    Route::get('balance_sheet', [BalanceSheetController::class, 'index'])->name('balance_sheet.index')->middleware('role:Admin');
    Route::get('saving_transaction_by_member', [SavingTransactionByMemberReportController::class, 'index'])->name('saving_transaction_by_member.index')->middleware('role:Admin');

    Route::get('auto_journal', [AutoJournalController::class, 'index'])->name('auto_journal.index')->middleware('role:Admin');
    Route::post('auto_journal', [AutoJournalController::class, 'process'])->name('auto_journal.process')->middleware('role:Admin');
    Route::get('activity_log', [ActivityLogController::class, 'index'])->name('activity_log.index')->middleware('role:Admin');
});

Route::post('ledger', [LedgerController::class, 'process'])->name('ledger.process');
Route::post('stock_ledger', [StockLedgerController::class, 'process'])->name('stock_ledger.process');
Route::post('cash_flow', [CashFlowController::class, 'process'])->name('cash_flow.process');
Route::post('profit_loss', [ProfitLossController::class, 'process'])->name('profit_loss.process');
Route::post('balance_sheet', [BalanceSheetController::class, 'process'])->name('balance_sheet.process');
Route::post('saving_transaction_by_member', [SavingTransactionByMemberReportController::class, 'process'])->name('saving_transaction_by_member.process');
