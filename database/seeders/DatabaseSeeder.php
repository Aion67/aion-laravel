<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Medication;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->resetPharmacyData();

        $admin = $this->upsertUser('admin@example.com', 'Admin User', User::ROLE_ADMIN);
        $staffMembers = collect([
            ['email' => 'pharmacist@example.com', 'name' => 'Pharmacist User'],
            ['email' => 'cashier@example.com', 'name' => 'Cashier User'],
            ['email' => 'inventory@example.com', 'name' => 'Inventory User'],
            ['email' => 'support@example.com', 'name' => 'Support User'],
        ])->map(fn (array $profile): User => $this->upsertUser($profile['email'], $profile['name'], User::ROLE_PHARMACIST));

        $team = collect([$admin])->concat($staffMembers)->values();
        $medications = $this->seedMedications();
        $customers = $this->seedCustomers();
        $inventoryByMedication = $this->seedInventory($medications);

        $this->seedPrescriptions($customers, $team, $medications);
        $this->seedSales($customers, $team, $medications, $inventoryByMedication);
        $this->seedManualStockMovements($team, $medications, $inventoryByMedication);
        $this->seedCriticalLowStock($team, $medications, $inventoryByMedication);
    }

    private function resetPharmacyData(): void
    {
        SaleItem::query()->delete();
        Sale::query()->delete();
        PrescriptionItem::query()->delete();
        Prescription::query()->delete();
        StockMovement::query()->delete();
        Inventory::query()->delete();
        Medication::query()->delete();
        Customer::query()->delete();
    }

    private function upsertUser(string $email, string $name, string $role): User
    {
        return User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => $role,
            ]
        );
    }

    private function seedMedications(): Collection
    {
        $catalog = [
            ['sku' => 'MED-001', 'name' => 'Amoxicillin', 'unit_type' => 'capsule', 'dosage_form' => 'Capsule', 'strength' => '500 mg', 'unit_price' => 12.50, 'reorder_level' => 18, 'status' => 'active', 'image_path' => 'medication-images/amoxicillin.png'],
            ['sku' => 'MED-002', 'name' => 'Paracetamol', 'unit_type' => 'tablet', 'dosage_form' => 'Tablet', 'strength' => '500 mg', 'unit_price' => 4.20, 'reorder_level' => 30, 'status' => 'active', 'image_path' => 'medication-images/paracetamol.jpg'],
            ['sku' => 'MED-003', 'name' => 'Ibuprofen', 'unit_type' => 'tablet', 'dosage_form' => 'Tablet', 'strength' => '200 mg', 'unit_price' => 5.75, 'reorder_level' => 24, 'status' => 'active', 'image_path' => 'medication-images/ibuprofen.jpg'],
            ['sku' => 'MED-004', 'name' => 'Metformin', 'unit_type' => 'tablet', 'dosage_form' => 'Tablet', 'strength' => '850 mg', 'unit_price' => 9.90, 'reorder_level' => 20, 'status' => 'active', 'image_path' => 'medication-images/metformin.jpg'],
            ['sku' => 'MED-005', 'name' => 'Atorvastatin', 'unit_type' => 'tablet', 'dosage_form' => 'Tablet', 'strength' => '20 mg', 'unit_price' => 14.80, 'reorder_level' => 16, 'status' => 'active', 'image_path' => 'medication-images/atorvastatin.jpg'],
            ['sku' => 'MED-006', 'name' => 'Amlodipine', 'unit_type' => 'tablet', 'dosage_form' => 'Tablet', 'strength' => '5 mg', 'unit_price' => 10.30, 'reorder_level' => 16, 'status' => 'active', 'image_path' => 'medication-images/amlodipine.jpg'],
            ['sku' => 'MED-007', 'name' => 'Omeprazole', 'unit_type' => 'capsule', 'dosage_form' => 'Capsule', 'strength' => '20 mg', 'unit_price' => 8.40, 'reorder_level' => 18, 'status' => 'active', 'image_path' => 'medication-images/omeprazole.jpg'],
            ['sku' => 'MED-008', 'name' => 'Cetirizine', 'unit_type' => 'tablet', 'dosage_form' => 'Tablet', 'strength' => '10 mg', 'unit_price' => 6.10, 'reorder_level' => 15, 'status' => 'active', 'image_path' => 'medication-images/cetirizine.jpg'],
            ['sku' => 'MED-009', 'name' => 'Salbutamol Inhaler', 'unit_type' => 'inhaler', 'dosage_form' => 'Inhaler', 'strength' => '100 mcg', 'unit_price' => 19.75, 'reorder_level' => 12, 'status' => 'active', 'image_path' => 'medication-images/salbutamol-inhaler.jpg'],
            ['sku' => 'MED-010', 'name' => 'Vitamin C', 'unit_type' => 'tablet', 'dosage_form' => 'Tablet', 'strength' => '500 mg', 'unit_price' => 7.30, 'reorder_level' => 25, 'status' => 'active', 'image_path' => 'medication-images/vitamin-c.jpg'],
            ['sku' => 'MED-011', 'name' => 'Oral Rehydration Salts', 'unit_type' => 'sachet', 'dosage_form' => 'Powder', 'strength' => 'One sachet', 'unit_price' => 3.60, 'reorder_level' => 20, 'status' => 'active', 'image_path' => 'medication-images/oral-rehydration-salts.jpg'],
            ['sku' => 'MED-012', 'name' => 'Zinc Sulfate', 'unit_type' => 'tablet', 'dosage_form' => 'Tablet', 'strength' => '20 mg', 'unit_price' => 5.25, 'reorder_level' => 18, 'status' => 'active', 'image_path' => 'medication-images/zinc-sulfate.jpg'],
            ['sku' => 'MED-013', 'name' => 'Losartan', 'unit_type' => 'tablet', 'dosage_form' => 'Tablet', 'strength' => '50 mg', 'unit_price' => 11.40, 'reorder_level' => 14, 'status' => 'active', 'image_path' => 'medication-images/losartan.jpg'],
            ['sku' => 'MED-014', 'name' => 'Co-amoxiclav', 'unit_type' => 'tablet', 'dosage_form' => 'Tablet', 'strength' => '625 mg', 'unit_price' => 16.90, 'reorder_level' => 15, 'status' => 'active', 'image_path' => 'medication-images/co-amoxiclav.jpg'],
        ];

        return collect($catalog)->map(function (array $medication): Medication {
            return Medication::updateOrCreate(
                ['sku' => $medication['sku']],
                $medication
            );
        })->values();
    }

    private function seedCustomers(): Collection
    {
        $profiles = [
            ['first_name' => 'Amina', 'last_name' => 'Khan', 'sex' => 'female'],
            ['first_name' => 'Daniel', 'last_name' => 'Owusu', 'sex' => 'male'],
            ['first_name' => 'Grace', 'last_name' => 'Mensah', 'sex' => 'female'],
            ['first_name' => 'Samuel', 'last_name' => 'Adebayo', 'sex' => 'male'],
            ['first_name' => 'Nadia', 'last_name' => 'Kassim', 'sex' => 'female'],
            ['first_name' => 'Peter', 'last_name' => 'Mugisha', 'sex' => 'male'],
            ['first_name' => 'Joy', 'last_name' => 'Ncube', 'sex' => 'female'],
            ['first_name' => 'Michael', 'last_name' => 'Okello', 'sex' => 'male'],
            ['first_name' => 'Hawa', 'last_name' => 'Bello', 'sex' => 'female'],
            ['first_name' => 'Joseph', 'last_name' => 'Mensimah', 'sex' => 'male'],
            ['first_name' => 'Lydia', 'last_name' => 'Tetteh', 'sex' => 'female'],
            ['first_name' => 'Frank', 'last_name' => 'Asare', 'sex' => 'male'],
            ['first_name' => 'Patience', 'last_name' => 'Badu', 'sex' => 'female'],
            ['first_name' => 'Victor', 'last_name' => 'Mensah', 'sex' => 'male'],
            ['first_name' => 'Esi', 'last_name' => 'Adjei', 'sex' => 'female'],
            ['first_name' => 'Isaac', 'last_name' => 'Nartey', 'sex' => 'male'],
            ['first_name' => 'Ruth', 'last_name' => 'Kumi', 'sex' => 'female'],
            ['first_name' => 'Henry', 'last_name' => 'Boateng', 'sex' => 'male'],
        ];

        $medicalNotes = [
            'Seasonal allergies',
            'Hypertension',
            'Type 2 diabetes',
            'Asthma',
            'Peptic ulcer history',
            'Recurring migraine',
            'No chronic condition reported',
        ];

        $allergyNotes = [
            'None recorded',
            'Penicillin',
            'Shellfish',
            'Dust and pollen',
            'Aspirin',
        ];

        return collect($profiles)->map(function (array $profile) use ($medicalNotes, $allergyNotes): Customer {
            $dateOfBirth = now()->subYears(random_int(18, 68))->subDays(random_int(0, 300));
            $email = strtolower($profile['first_name'].'.'.$profile['last_name'].'@example.com');
            $createdAt = now()->subMonthsNoOverflow(random_int(0, 5))->subDays(random_int(0, 20))->setTime(random_int(8, 17), random_int(0, 59));

            $customer = Customer::updateOrCreate(
                ['email' => $email],
                [
                    'first_name' => $profile['first_name'],
                    'last_name' => $profile['last_name'],
                    'date_of_birth' => $dateOfBirth->toDateString(),
                    'sex' => $profile['sex'],
                    'phone' => '+256'.str_pad((string) random_int(700000000, 799999999), 9, '0', STR_PAD_LEFT),
                    'email' => $email,
                    'address' => $this->faker()->streetAddress().', '.$this->faker()->city(),
                    'medical_history' => collect($medicalNotes)->random(random_int(1, 2))->implode(', '),
                    'allergies' => collect($allergyNotes)->random(random_int(1, 2))->implode(', '),
                    'conditions' => collect($medicalNotes)->random(random_int(1, 2))->implode(', '),
                ]
            );

            DB::table('customers')->where('id', $customer->id)->update([
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            return $customer;
        })->values();
    }

    private function seedInventory(Collection $medications): Collection
    {
        return $medications->mapWithKeys(function (Medication $medication): array {
            $quantityOnHand = random_int(70, 220);

            return [
                $medication->id => Inventory::updateOrCreate(
                    ['medication_id' => $medication->id],
                    [
                        'quantity_on_hand' => $quantityOnHand,
                        'reserved_quantity' => random_int(0, 8),
                    ]
                ),
            ];
        });
    }

    private function seedPrescriptions(Collection $customers, Collection $team, Collection $medications): void
    {
        $statuses = ['draft', 'confirmed', 'dispensed', 'cancelled'];
        $months = $this->reportMonths();
        $counter = 1;

        foreach ($months as $monthIndex => $month) {
            for ($i = 0; $i < 4; $i++) {
                $prescribedAt = $this->dateInMonth($month, 1, 24);
                $prescription = Prescription::create([
                    'customer_id' => $customers->random()->id,
                    'user_id' => $team->random()->id,
                    'prescription_number' => sprintf('RX-SEED-%s-%03d', $prescribedAt->format('Ymd'), $counter++),
                    'status' => $statuses[($monthIndex + $i) % count($statuses)],
                    'notes' => $this->faker()->sentence(),
                    'prescribed_at' => $prescribedAt,
                ]);

                $items = $medications->shuffle()->take(random_int(1, 3));

                foreach ($items as $medication) {
                    PrescriptionItem::create([
                        'prescription_id' => $prescription->id,
                        'medication_id' => $medication->id,
                        'quantity' => random_int(1, 3),
                        'dosage_instructions' => $this->faker()->sentence(8),
                        'unit_price' => $medication->unit_price,
                    ]);
                }
            }
        }
    }
    private function faker(): object
    {
        static $faker = null;

        if ($faker === null) {
            if (class_exists(\Faker\Factory::class)) {
                $faker = \Faker\Factory::create();
            } else {
                $faker = new class {
                    public function streetAddress(): string
                    {
                        return 'Main Street 1';
                    }

                    public function city(): string
                    {
                        return 'Kampala';
                    }

                    public function sentence(int $words = 6): string
                    {
                        return 'Routine follow-up required.';
                    }
                };
            }
        }

        return $faker;
    }

    private function seedSales(Collection $customers, Collection $team, Collection $medications, Collection $inventoryByMedication): void
    {
        $paymentMethods = ['cash', 'card', 'mobile_money', 'bank_transfer'];
        $months = $this->reportMonths();
        $saleCounter = 1;

        foreach ($months as $monthIndex => $month) {
            for ($i = 0; $i < 8; $i++) {
                $soldAt = $this->dateInMonth($month, 2, 26);
                $sale = Sale::create([
                    'customer_id' => random_int(1, 100) <= 85 ? $customers->random()->id : null,
                    'user_id' => $team->random()->id,
                    'sale_number' => sprintf('SL-SEED-%s-%03d', $soldAt->format('Ymd'), $saleCounter++),
                    'subtotal' => 0,
                    'discount' => 0,
                    'tax' => 0,
                    'total' => 0,
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'status' => 'paid',
                    'sold_at' => $soldAt,
                ]);

                $subtotal = 0.0;
                $saleItems = $medications->shuffle()->take(random_int(1, 3));

                foreach ($saleItems as $medication) {
                    $inventory = $inventoryByMedication->get($medication->id);
                    $quantity = random_int(1, 4);

                    if ((int) $inventory->quantity_on_hand < $quantity + 12) {
                        $restockQuantity = max(24, $quantity * 4);
                        $inventory->quantity_on_hand += $restockQuantity;
                        $inventory->save();

                        StockMovement::create([
                            'medication_id' => $medication->id,
                            'user_id' => $team->random()->id,
                            'movement_type' => 'in',
                            'quantity' => $restockQuantity,
                            'reference_type' => 'replenishment',
                            'reference_id' => null,
                            'notes' => 'Scheduled restock for '.$medication->name,
                            'created_at' => $soldAt->copy()->subDay(),
                        ]);
                    }

                    $lineTotal = round(((float) $medication->unit_price) * $quantity, 2);
                    $subtotal += $lineTotal;

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'medication_id' => $medication->id,
                        'quantity' => $quantity,
                        'unit_price' => $medication->unit_price,
                        'line_total' => $lineTotal,
                    ]);

                    $inventory->quantity_on_hand -= $quantity;
                    $inventory->save();

                    StockMovement::create([
                        'medication_id' => $medication->id,
                        'user_id' => $team->random()->id,
                        'movement_type' => 'out',
                        'quantity' => $quantity,
                        'reference_type' => 'sale',
                        'reference_id' => $sale->id,
                        'notes' => 'Sale deduction for '.$sale->sale_number,
                        'created_at' => $soldAt,
                    ]);
                }

                $discount = round($subtotal * (random_int(0, 8) / 100), 2);
                $tax = round(($subtotal - $discount) * 0.05, 2);
                $total = max($subtotal - $discount + $tax, 0);

                $sale->update([
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'tax' => $tax,
                    'total' => $total,
                ]);
            }
        }
    }

    private function seedManualStockMovements(Collection $team, Collection $medications, Collection $inventoryByMedication): void
    {
        foreach ($medications->take(5) as $index => $medication) {
            $inventory = $inventoryByMedication->get($medication->id);
            $adjustmentDate = now()->subWeeks($index + 1)->startOfDay();
            $adjustment = random_int(8, 16);

            $inventory->quantity_on_hand += $adjustment;
            $inventory->save();

            StockMovement::create([
                'medication_id' => $medication->id,
                'user_id' => $team->random()->id,
                'movement_type' => 'in',
                'quantity' => $adjustment,
                'reference_type' => 'manual',
                'reference_id' => null,
                'notes' => 'Manual balancing stock adjustment',
                'created_at' => $adjustmentDate,
            ]);
        }
    }

    private function seedCriticalLowStock(Collection $team, Collection $medications, Collection $inventoryByMedication): void
    {
        foreach ($medications->whereIn('sku', ['MED-002', 'MED-003', 'MED-007', 'MED-010']) as $medication) {
            $inventory = $inventoryByMedication->get($medication->id);
            $inventory->quantity_on_hand = random_int(0, max(1, (int) floor(((int) $medication->reorder_level ?? 10) / 3)));
            $inventory->save();

            StockMovement::create([
                'medication_id' => $medication->id,
                'user_id' => $team->random()->id,
                'movement_type' => 'out',
                'quantity' => random_int(1, 3),
                'reference_type' => 'manual',
                'reference_id' => null,
                'notes' => 'Simulated low-stock condition for reporting and alerts',
                'created_at' => now()->subDays(random_int(1, 10)),
            ]);
        }
    }

    private function reportMonths(): Collection
    {
        return collect(range(0, 5))->map(fn (int $offset) => now()->subMonthsNoOverflow(5 - $offset)->startOfMonth());
    }

    private function dateInMonth(Carbon $month, int $minimumDay, int $maximumDay): Carbon
    {
        $day = random_int($minimumDay, min($maximumDay, $month->daysInMonth));

        return $month->copy()->addDays($day - 1)->setTime(random_int(8, 17), random_int(0, 59));
    }
}
