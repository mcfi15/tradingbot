<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class EtfAndBondRouteRemovalTest extends TestCase
{
    public function test_etf_and_bond_routes_are_no_longer_registered(): void
    {
        $this->assertFalse(Route::has('capital-instruments.bonds'));
        $this->assertFalse(Route::has('capital-instruments.bonds.buy'));
        $this->assertFalse(Route::has('capital-instruments.bonds.buy-validate'));
        $this->assertFalse(Route::has('capital-instruments.bonds.sell'));
        $this->assertFalse(Route::has('capital-instruments.etfs'));
        $this->assertFalse(Route::has('capital-instruments.etfs.buy'));
        $this->assertFalse(Route::has('capital-instruments.etfs.buy-validate'));
        $this->assertFalse(Route::has('capital-instruments.etfs.sell'));
        $this->assertFalse(Route::has('user.capital-instruments.commercial-papers'));
        $this->assertFalse(Route::has('user.capital-instruments.mutual-funds'));
        $this->assertFalse(Route::has('user.capital-instruments.treasury-bills'));
        $this->assertFalse(Route::has('admin.bonds.index'));
        $this->assertFalse(Route::has('admin.etfs.index'));
        $this->assertFalse(Route::has('admin.forex-trading.accounts.index'));
        $this->assertFalse(Route::has('admin.futures-trading.accounts.index'));
        $this->assertFalse(Route::has('admin.margin-trading.accounts.index'));
    }
}
