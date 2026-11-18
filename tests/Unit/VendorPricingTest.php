<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Vendor;
use PHPUnit\Framework\TestCase;

class VendorPricingTest extends TestCase
{
    public function test_custom_catering_price_is_proportional_to_guest_count(): void
    {
        $vendor = $this->vendor('Catering', 10000000, 200);

        $this->assertSame(2500000.0, $vendor->priceForGuests(50));
        $this->assertSame(10000000.0, $vendor->priceForGuests(200));
    }

    public function test_non_catering_price_is_not_adjusted_for_guest_count(): void
    {
        $vendor = $this->vendor('Photography', 3000000, 200);

        $this->assertSame(3000000.0, $vendor->priceForGuests(50));
    }

    public function test_katering_category_is_supported_and_unconfigured_capacity_keeps_base_price(): void
    {
        $vendor = $this->vendor('Katering & Kue', 8000000, null);

        $this->assertTrue($vendor->isCatering());
        $this->assertSame(8000000.0, $vendor->priceForGuests(50));
    }

    private function vendor(string $categoryName, float $price, ?int $capacity): Vendor
    {
        $vendor = new Vendor([
            'price' => $price,
            'guest_capacity' => $capacity,
        ]);
        $vendor->setRelation('category', new Category(['name' => $categoryName]));

        return $vendor;
    }
}
