<?php

namespace Database\Seeders;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\ShipmentItem;
use App\Models\TrackingEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with realistic logistics dataset matching design briefs.
     */
    public function run(): void
    {
        // 1. Seed Demo Users for all 3 Roles
        $admin = User::firstOrCreate(
            ['email' => 'admin@cargotrace.com'],
            [
                'name' => 'Apex Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '+62 811-9876-5432',
            ]
        );

        $operator = User::firstOrCreate(
            ['email' => 'operator@cargotrace.com'],
            [
                'name' => 'Capt. Hendrik Visser',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'phone' => '+31 10 555 0192',
            ]
        );

        $customer1 = User::firstOrCreate(
            ['email' => 'customer@cargotrace.com'],
            [
                'name' => 'PT Samudera Logistik Mitra',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '+62 21 430 1199',
            ]
        );

        $customer2 = User::firstOrCreate(
            ['email' => 'transasia@cargotrace.com'],
            [
                'name' => 'Trans-Asia Freight Corp',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '+65 6789 0123',
            ]
        );

        // 2. Seed Partner Carriers (4 Major Lines)
        $carrierMaersk = Carrier::create([
            'code' => 'MSK',
            'name' => 'Maersk Line',
            'contact_email' => 'dispatch@maersk.com',
            'contact_phone' => '+45 33 63 33 63',
            'is_active' => true,
        ]);

        $carrierEvergreen = Carrier::create([
            'code' => 'EVG',
            'name' => 'Evergreen Marine',
            'contact_email' => 'operations@evergreen-marine.com',
            'contact_phone' => '+886 2 2501 1211',
            'is_active' => true,
        ]);

        $carrierQatar = Carrier::create([
            'code' => 'QTR',
            'name' => 'Qatar Airways Cargo',
            'contact_email' => 'cargo-ops@qatarairways.com.qa',
            'contact_phone' => '+974 4496 0000',
            'is_active' => true,
        ]);

        $carrierMsc = Carrier::create([
            'code' => 'MSC',
            'name' => 'Mediterranean Shipping Company (MSC)',
            'contact_email' => 'tracking@msc.com',
            'contact_phone' => '+41 22 703 8888',
            'is_active' => true,
        ]);

        // 3. Seed 6 Global Strategic Hubs & Terminals
        $locTanjungPriok = Location::create([
            'code' => 'IDTPP',
            'name' => 'Tanjung Priok Container Terminal',
            'type' => 'SEAPORT',
            'country' => 'Indonesia',
            'latitude' => -6.1017,
            'longitude' => 106.8833,
        ]);

        $locSingapore = Location::create([
            'code' => 'SGSIN',
            'name' => 'Port of Singapore (PSA Pasir Panjang)',
            'type' => 'SEAPORT',
            'country' => 'Singapore',
            'latitude' => 1.290270,
            'longitude' => 103.851959,
        ]);

        $locSuez = Location::create([
            'code' => 'EGSCA',
            'name' => 'Suez Canal Convoy Anchorage',
            'type' => 'CANAL',
            'country' => 'Egypt',
            'latitude' => 29.9737,
            'longitude' => 32.5599,
        ]);

        $locRotterdam = Location::create([
            'code' => 'NLRTM',
            'name' => 'Port of Rotterdam (Maasvlakte 2)',
            'type' => 'SEAPORT',
            'country' => 'Netherlands',
            'latitude' => 51.9547,
            'longitude' => 4.0531,
        ]);

        $locFrankfurt = Location::create([
            'code' => 'DEFRA',
            'name' => 'Frankfurt Airport CargoCity South',
            'type' => 'AIRPORT',
            'country' => 'Germany',
            'latitude' => 50.0379,
            'longitude' => 8.5622,
        ]);

        $locLosAngeles = Location::create([
            'code' => 'USLAX',
            'name' => 'Port of Los Angeles (Pier 400)',
            'type' => 'SEAPORT',
            'country' => 'United States',
            'latitude' => 33.7423,
            'longitude' => -118.2612,
        ]);

        // 4. Seed Consignments matching visual layout

        // Consignment 1: AT-2026-B8910 (Ocean Maersk, Delayed 28h, Stage 3 Suez Canal)
        $shipment1 = Shipment::create([
            'shipment_number' => 'AT-2026-B8910',
            'customer_id' => $customer1->id,
            'carrier_id' => $carrierMaersk->id,
            'origin_id' => $locTanjungPriok->id,
            'destination_id' => $locRotterdam->id,
            'status' => Shipment::STATUS_IN_TRANSIT,
            'transport_mode' => 'OCEAN',
            'expected_departure' => Carbon::now()->subDays(12)->setTime(8, 0),
            'actual_departure' => Carbon::now()->subDays(12)->setTime(8, 30),
            'expected_arrival' => Carbon::now()->addDays(2)->setTime(14, 0),
            'estimated_arrival' => Carbon::now()->addDays(2)->addHours(28)->setTime(18, 0),
            'actual_arrival' => null,
            'delay_status' => 'HIGH',
            'delay_hours' => 28,
            'consignee_name' => 'PT Samudera Logistik Mitra',
            'vessel_or_flight' => 'Maersk Mc-Kinney Moller',
            'berth_number' => 'Berth 402',
            'current_stage' => 3,
            'total_stages' => 4,
            'temperature_celsius' => 4.2,
            'humidity_pct' => 62.0,
            'air_speed_kts' => null,
            'air_altitude_ft' => null,
            'notes' => 'Multimodal corridor ocean lead. Cold-chain reefer container monitored continuously.',
        ]);

        ShipmentItem::create([
            'shipment_id' => $shipment1->id,
            'item_name' => 'Cold-Chain Pharma & Exotic Seafood (Reefer)',
            'quantity' => 2,
            'weight_kg' => 31900.50,
            'volume_cbm' => 76.40,
        ]);

        // Tracking timeline for AT-2026-B8910
        TrackingEvent::create([
            'shipment_id' => $shipment1->id,
            'location_id' => $locTanjungPriok->id,
            'created_by' => $operator->id,
            'status' => Shipment::STATUS_PICKED_UP,
            'event_time' => Carbon::now()->subDays(12)->setTime(6, 0),
            'latitude' => -6.1017,
            'longitude' => 106.8833,
            'description' => 'Container loaded aboard vessel Maersk Mc-Kinney Moller at Tanjung Priok',
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment1->id,
            'location_id' => $locTanjungPriok->id,
            'created_by' => $operator->id,
            'status' => Shipment::STATUS_IN_TRANSIT,
            'event_time' => Carbon::now()->subDays(12)->setTime(8, 30),
            'latitude' => -6.0850,
            'longitude' => 106.8700,
            'description' => 'Stage 1 Complete: Departed Jakarta corridor heading Northwest',
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment1->id,
            'location_id' => $locSingapore->id,
            'created_by' => $operator->id,
            'status' => Shipment::STATUS_IN_TRANSIT,
            'event_time' => Carbon::now()->subDays(9)->setTime(14, 15),
            'latitude' => 1.2902,
            'longitude' => 103.8519,
            'description' => 'Stage 2 Complete: Port of Singapore cleared bunkering & customs inspection',
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment1->id,
            'location_id' => $locSuez->id,
            'created_by' => $operator->id,
            'status' => Shipment::STATUS_IN_TRANSIT,
            'event_time' => Carbon::now()->subHours(9),
            'latitude' => 29.9737,
            'longitude' => 32.5599,
            'description' => 'Stage 3 Active: Anchored at Suez Canal Southern Convoy Waiting Area',
        ]);

        // Exception for AT-2026-B8910
        ShipmentException::create([
            'shipment_id' => $shipment1->id,
            'reported_by' => $operator->id,
            'exception_type' => ShipmentException::TYPE_PORT_CONGESTION,
            'severity' => ShipmentException::SEVERITY_HIGH,
            'status' => ShipmentException::STATUS_OPEN,
            'detected_at' => Carbon::now()->subHours(9),
            'resolved_at' => null,
            'resolution_notes' => 'Queue congestion at Suez entrance due to high seasonal northbound transit volume. Scheduled for next convoy window.',
        ]);

        // Consignment 2: AWB-772-91024 (Air Express, Qatar Airways, On Schedule)
        $shipment2 = Shipment::create([
            'shipment_number' => 'AWB-772-91024',
            'customer_id' => $customer1->id,
            'carrier_id' => $carrierQatar->id,
            'origin_id' => $locSingapore->id,
            'destination_id' => $locFrankfurt->id,
            'status' => Shipment::STATUS_IN_FLIGHT,
            'transport_mode' => 'AIR',
            'expected_departure' => Carbon::now()->subHours(7),
            'actual_departure' => Carbon::now()->subHours(7),
            'expected_arrival' => Carbon::now()->addHours(5)->setTime(21, 30),
            'estimated_arrival' => Carbon::now()->addHours(5)->setTime(21, 30),
            'actual_arrival' => null,
            'delay_status' => 'ON_TIME',
            'delay_hours' => 0,
            'consignee_name' => 'Apex Global Express Hub',
            'vessel_or_flight' => 'Boeing 777-200F / QR-803F',
            'berth_number' => 'Bay S-14',
            'current_stage' => 2,
            'total_stages' => 3,
            'temperature_celsius' => 18.0,
            'humidity_pct' => 45.0,
            'air_speed_kts' => 485,
            'air_altitude_ft' => 36000,
            'notes' => 'High-value semiconductor payload with dedicated temperature logger.',
        ]);

        ShipmentItem::create([
            'shipment_id' => $shipment2->id,
            'item_name' => 'High-Precision Semiconductor Chips',
            'quantity' => 12,
            'weight_kg' => 4850.00,
            'volume_cbm' => 18.20,
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment2->id,
            'location_id' => $locSingapore->id,
            'created_by' => $operator->id,
            'status' => Shipment::STATUS_BOOKED,
            'event_time' => Carbon::now()->subHours(10),
            'latitude' => 1.3644,
            'longitude' => 103.9915,
            'description' => 'Air cargo palletized and weighed at Singapore Changi Cargo Hub',
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment2->id,
            'location_id' => $locSingapore->id,
            'created_by' => $operator->id,
            'status' => Shipment::STATUS_IN_FLIGHT,
            'event_time' => Carbon::now()->subHours(7),
            'latitude' => 18.5204,
            'longitude' => 73.8567,
            'description' => 'Cruising at 36,000 ft over Arabian Sea corridor (Speed: 485 Kts)',
        ]);

        // Consignment 3: AT-2026-90412 (Ocean Evergreen, Berthed at Singapore)
        $shipment3 = Shipment::create([
            'shipment_number' => 'AT-2026-90412',
            'customer_id' => $customer2->id,
            'carrier_id' => $carrierEvergreen->id,
            'origin_id' => $locTanjungPriok->id,
            'destination_id' => $locSingapore->id,
            'status' => Shipment::STATUS_BERTHED,
            'transport_mode' => 'OCEAN',
            'expected_departure' => Carbon::now()->subDays(2),
            'actual_departure' => Carbon::now()->subDays(2),
            'expected_arrival' => Carbon::now()->subHours(4),
            'estimated_arrival' => Carbon::now()->subHours(4),
            'actual_arrival' => Carbon::now()->subHours(4),
            'delay_status' => 'ON_TIME',
            'delay_hours' => 0,
            'consignee_name' => 'Trans-Asia Freight Corp',
            'vessel_or_flight' => 'Ever Given',
            'berth_number' => 'Berth 12',
            'current_stage' => 3,
            'total_stages' => 3,
            'temperature_celsius' => 26.5,
            'humidity_pct' => 70.0,
            'notes' => 'Textiles and consumer electronic goods.',
        ]);

        ShipmentItem::create([
            'shipment_id' => $shipment3->id,
            'item_name' => 'Textile Rolls & Consumer Electronics',
            'quantity' => 4,
            'weight_kg' => 42000.00,
            'volume_cbm' => 120.00,
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment3->id,
            'location_id' => $locSingapore->id,
            'created_by' => $operator->id,
            'status' => Shipment::STATUS_BERTHED,
            'event_time' => Carbon::now()->subHours(4),
            'latitude' => 1.2902,
            'longitude' => 103.8519,
            'description' => 'Vessel moored safely at Singapore Berth 12 for container gantry offloading',
        ]);

        // Consignments 4 - 12 (to complete a rich 12-consignment portfolio with realistic routes)
        $additionalData = [
            [
                'number' => 'AT-2026-77102',
                'mode' => 'OCEAN',
                'customer' => $customer1,
                'carrier' => $carrierMsc,
                'origin' => $locSingapore,
                'destination' => $locLosAngeles,
                'status' => Shipment::STATUS_IN_TRANSIT,
                'delay_status' => 'ON_TIME',
                'delay_hours' => 0,
                'consignee' => 'Pacific Rim Importers LLC',
                'vessel' => 'MSC Gülsün',
                'berth' => 'Pier 400 - West',
                'weight' => 52000.00,
            ],
            [
                'number' => 'AWB-883-44190',
                'mode' => 'AIR',
                'customer' => $customer2,
                'carrier' => $carrierQatar,
                'origin' => $locFrankfurt,
                'destination' => $locSingapore,
                'status' => Shipment::STATUS_IN_TRANSIT,
                'delay_status' => 'LOW',
                'delay_hours' => 4,
                'consignee' => 'EuroAsia Automotive Components',
                'vessel' => 'Airbus A350-900F',
                'berth' => 'Gate C-22',
                'weight' => 7800.00,
            ],
            [
                'number' => 'AT-2026-33910',
                'mode' => 'OCEAN',
                'customer' => $customer1,
                'carrier' => $carrierMaersk,
                'origin' => $locRotterdam,
                'destination' => $locLosAngeles,
                'status' => Shipment::STATUS_DELIVERED,
                'delay_status' => 'ON_TIME',
                'delay_hours' => 0,
                'consignee' => 'Trans-Atlantic Distribution Corp',
                'vessel' => 'Maersk Madrid',
                'berth' => 'Terminal 3',
                'weight' => 64000.00,
            ],
            [
                'number' => 'AT-2026-55120',
                'mode' => 'OCEAN',
                'customer' => $customer2,
                'carrier' => $carrierEvergreen,
                'origin' => $locTanjungPriok,
                'destination' => $locRotterdam,
                'status' => Shipment::STATUS_BOOKED,
                'delay_status' => 'ON_TIME',
                'delay_hours' => 0,
                'consignee' => 'Nordic BioFuels Ltd',
                'vessel' => 'Ever Ace',
                'berth' => 'Berth 101',
                'weight' => 28000.00,
            ],
            [
                'number' => 'AWB-512-99014',
                'mode' => 'AIR',
                'customer' => $customer1,
                'carrier' => $carrierQatar,
                'origin' => $locSingapore,
                'destination' => $locFrankfurt,
                'status' => Shipment::STATUS_DELIVERED,
                'delay_status' => 'ON_TIME',
                'delay_hours' => 0,
                'consignee' => 'Bavaria Precision Instruments',
                'vessel' => 'QR-812F Cargo',
                'berth' => 'Bay 4',
                'weight' => 2100.00,
            ],
            [
                'number' => 'AT-2026-66230',
                'mode' => 'OCEAN',
                'customer' => $customer2,
                'carrier' => $carrierMsc,
                'origin' => $locLosAngeles,
                'destination' => $locTanjungPriok,
                'status' => Shipment::STATUS_IN_TRANSIT,
                'delay_status' => 'ON_TIME',
                'delay_hours' => 0,
                'consignee' => 'PT Nusantara Heavy Equipment',
                'vessel' => 'MSC Isabella',
                'berth' => 'Berth 304',
                'weight' => 88000.00,
            ],
            [
                'number' => 'AT-2026-11880',
                'mode' => 'OCEAN',
                'customer' => $customer1,
                'carrier' => $carrierMaersk,
                'origin' => $locSingapore,
                'destination' => $locRotterdam,
                'status' => Shipment::STATUS_IN_TRANSIT,
                'delay_status' => 'ON_TIME',
                'delay_hours' => 0,
                'consignee' => 'Rotterdam Coffee Importers NV',
                'vessel' => 'Maersk Mc-Kinney Moller II',
                'berth' => 'Berth 205',
                'weight' => 38000.00,
            ],
            [
                'number' => 'AWB-991-30219',
                'mode' => 'AIR',
                'customer' => $customer2,
                'carrier' => $carrierQatar,
                'origin' => $locFrankfurt,
                'destination' => $locSingapore,
                'status' => Shipment::STATUS_PICKED_UP,
                'delay_status' => 'ON_TIME',
                'delay_hours' => 0,
                'consignee' => 'SingaTech Aerospace Spares',
                'vessel' => 'QR-904F Freighter',
                'berth' => 'Gate S-02',
                'weight' => 1950.00,
            ],
            [
                'number' => 'AT-2026-44091',
                'mode' => 'OCEAN',
                'customer' => $customer1,
                'carrier' => $carrierEvergreen,
                'origin' => $locRotterdam,
                'destination' => $locSingapore,
                'status' => Shipment::STATUS_DELIVERED,
                'delay_status' => 'ON_TIME',
                'delay_hours' => 0,
                'consignee' => 'ASEAN Industrial Machinery',
                'vessel' => 'Ever Golden',
                'berth' => 'Berth 8',
                'weight' => 45000.00,
            ],
        ];

        foreach ($additionalData as $item) {
            $expectedDep = Carbon::now()->subDays(rand(3, 15));
            $expectedArr = (clone $expectedDep)->addDays(rand(5, 20));
            $estArr = (clone $expectedArr)->addHours($item['delay_hours']);

            $s = Shipment::create([
                'shipment_number' => $item['number'],
                'customer_id' => $item['customer']->id,
                'carrier_id' => $item['carrier']->id,
                'origin_id' => $item['origin']->id,
                'destination_id' => $item['destination']->id,
                'status' => $item['status'],
                'transport_mode' => $item['mode'],
                'expected_departure' => $expectedDep,
                'actual_departure' => $item['status'] !== Shipment::STATUS_CREATED ? $expectedDep : null,
                'expected_arrival' => $expectedArr,
                'estimated_arrival' => $estArr,
                'actual_arrival' => $item['status'] === Shipment::STATUS_DELIVERED ? $estArr : null,
                'delay_status' => $item['delay_status'],
                'delay_hours' => $item['delay_hours'],
                'consignee_name' => $item['consignee'],
                'vessel_or_flight' => $item['vessel'],
                'berth_number' => $item['berth'],
                'current_stage' => rand(1, 4),
                'total_stages' => 4,
            ]);

            ShipmentItem::create([
                'shipment_id' => $s->id,
                'item_name' => 'Commercial Palletized Consignment',
                'quantity' => rand(1, 10),
                'weight_kg' => $item['weight'],
                'volume_cbm' => round($item['weight'] / 350, 2),
            ]);

            TrackingEvent::create([
                'shipment_id' => $s->id,
                'location_id' => $item['origin']->id,
                'created_by' => $operator->id,
                'status' => $s->status,
                'event_time' => $expectedDep,
                'latitude' => $item['origin']->latitude,
                'longitude' => $item['origin']->longitude,
                'description' => "Consignment processed through {$item['origin']->name}",
            ]);
        }

        // Seed some in-app notifications
        Notification::create([
            'user_id' => $customer1->id,
            'shipment_id' => $shipment1->id,
            'title' => 'Suez Canal Delay Notice',
            'message' => 'Shipment AT-2026-B8910 is currently held at Suez Canal anchorage due to northbound queue congestion (+28h impact).',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $customer1->id,
            'shipment_id' => $shipment2->id,
            'title' => 'Flight Telemetry Updated',
            'message' => 'Flight QR-803F (AWB-772-91024) is on track, expected arrival 21:30 UTC at Frankfurt Airport.',
            'is_read' => true,
        ]);
    }
}
