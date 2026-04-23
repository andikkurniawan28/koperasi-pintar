<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BalanceSheetController;
use App\Http\Controllers\CashFlowController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfitLossController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SavingTypeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
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
    Route::get('sales/{sales}/edit', [SalesController::class, 'edit'])->name('sales.edit')->middleware('role:Admin');
    Route::put('sales/{sales}', [SalesController::class, 'update'])->name('sales.update')->middleware('role:Admin');
    Route::delete('sales/{sales}', [SalesController::class, 'destroy'])->name('sales.destroy')->middleware('role:Admin');

    Route::get('ledger', [LedgerController::class, 'index'])->name('ledger.index')->middleware('role:Admin');
    Route::get('cash_flow', [CashFlowController::class, 'index'])->name('cash_flow.index')->middleware('role:Admin');
    Route::get('profit_loss', [ProfitLossController::class, 'index'])->name('profit_loss.index')->middleware('role:Admin');
    Route::get('balance_sheet', [BalanceSheetController::class, 'index'])->name('balance_sheet.index')->middleware('role:Admin');
});

Route::post('ledger', [LedgerController::class, 'process'])->name('ledger.process');
Route::post('cash_flow', [CashFlowController::class, 'process'])->name('cash_flow.process');
Route::post('profit_loss', [ProfitLossController::class, 'process'])->name('profit_loss.process');
Route::post('balance_sheet', [BalanceSheetController::class, 'process'])->name('balance_sheet.process');
