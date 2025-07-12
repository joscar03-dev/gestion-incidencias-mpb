<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Dispositivo;
use App\Models\CategoriaDispositivo;
use App\Models\Area;
use App\Models\User;
use Carbon\Carbon;

class DispositivoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener categorías, áreas y usuarios existentes
        $categorias = CategoriaDispositivo::all();
        $areas = Area::all();
        $usuarios = User::all();

        // Si no hay áreas, crear algunas básicas
        if ($areas->isEmpty()) {
            $areasData = [
                ['nombre' => 'Sistemas', 'descripcion' => 'Departamento de Sistemas e IT'],
                ['nombre' => 'Administración', 'descripcion' => 'Área Administrativa'],
                ['nombre' => 'Recursos Humanos', 'descripcion' => 'Departamento de RRHH'],
                ['nombre' => 'Contabilidad', 'descripcion' => 'Área Contable'],
                ['nombre' => 'Ventas', 'descripcion' => 'Departamento de Ventas'],
                ['nombre' => 'Soporte Técnico', 'descripcion' => 'Mesa de Ayuda'],
            ];

            foreach ($areasData as $areaData) {
                Area::firstOrCreate(['nombre' => $areaData['nombre']], $areaData);
            }
            $areas = Area::all();
        }

        // Dispositivos por categoría
        $dispositivosPorCategoria = [
            'Computadoras de Escritorio' => [
                ['nombre' => 'Dell OptiPlex 7090', 'descripcion' => 'PC Escritorio Intel i7-11700, 16GB RAM, 512GB SSD'],
                ['nombre' => 'HP ProDesk 600 G6', 'descripcion' => 'Micro Desktop Intel i5-10500, 8GB RAM, 256GB SSD'],
                ['nombre' => 'Lenovo ThinkCentre M720q', 'descripcion' => 'Tiny Desktop Intel i5-9400T, 8GB RAM, 256GB SSD'],
                ['nombre' => 'Dell Precision 3650', 'descripcion' => 'Workstation Intel Xeon W-1270, 32GB RAM, 1TB SSD'],
                ['nombre' => 'HP EliteDesk 800 G6', 'descripcion' => 'Desktop Tower Intel i7-10700, 16GB RAM, 512GB SSD'],
                ['nombre' => 'ASUS ExpertCenter D7', 'descripcion' => 'Mini PC Intel i5-11400, 8GB RAM, 256GB SSD'],
                ['nombre' => 'Acer Veriton X2665G', 'descripcion' => 'Small Form Factor Intel i3-10100, 4GB RAM, 128GB SSD'],
                ['nombre' => 'MSI Pro DP21', 'descripcion' => 'Desktop PC AMD Ryzen 5 5600G, 16GB RAM, 512GB SSD'],
                ['nombre' => 'Dell OptiPlex 3090', 'descripcion' => 'Ultra Small Form Factor Intel i5-11500T, 8GB RAM'],
                ['nombre' => 'HP ProDesk 405 G6', 'descripcion' => 'SFF Desktop AMD Ryzen 5 PRO 4650G, 8GB RAM, 256GB SSD'],
            ],

            'Laptops y Portátiles' => [
                ['nombre' => 'Dell Latitude 5520', 'descripcion' => 'Laptop Empresarial 15.6", Intel i7-1165G7, 16GB RAM, 512GB SSD'],
                ['nombre' => 'HP EliteBook 840 G8', 'descripcion' => 'Ultrabook 14", Intel i5-1135G7, 8GB RAM, 256GB SSD'],
                ['nombre' => 'Lenovo ThinkPad T14', 'descripcion' => 'Business Laptop 14", AMD Ryzen 7 PRO 4750U, 16GB RAM'],
                ['nombre' => 'ASUS ZenBook Pro 15', 'descripcion' => 'Laptop Premium 15.6", Intel i7-11370H, 32GB RAM, 1TB SSD'],
                ['nombre' => 'MacBook Pro 16"', 'descripcion' => 'Apple M1 Pro, 16GB RAM, 512GB SSD, macOS Monterey'],
                ['nombre' => 'HP ZBook Studio G8', 'descripcion' => 'Mobile Workstation 15.6", Intel i9-11950H, 32GB RAM'],
                ['nombre' => 'Dell XPS 13', 'descripcion' => 'Ultrabook 13.3", Intel i7-1195G7, 16GB RAM, 512GB SSD'],
                ['nombre' => 'Lenovo Legion 5 Pro', 'descripcion' => 'Gaming Laptop 16", AMD Ryzen 7 5800H, RTX 3070, 16GB RAM'],
                ['nombre' => 'Microsoft Surface Laptop 4', 'descripcion' => 'Laptop 13.5", AMD Ryzen 7 4980U, 16GB RAM, 512GB SSD'],
                ['nombre' => 'Acer TravelMate P6', 'descripcion' => 'Business Laptop 14", Intel i7-1165G7, 16GB RAM, 1TB SSD'],
            ],

            'Tablets y Dispositivos Móviles' => [
                ['nombre' => 'iPad Pro 12.9"', 'descripcion' => 'Tablet Apple M1, 128GB, Wi-Fi + Cellular, iPadOS'],
                ['nombre' => 'Samsung Galaxy Tab S8', 'descripcion' => 'Tablet Android 11", Snapdragon 8 Gen 1, 8GB RAM, 128GB'],
                ['nombre' => 'Microsoft Surface Pro 8', 'descripcion' => 'Tablet 2-en-1 13", Intel i5-1135G7, 8GB RAM, 256GB SSD'],
                ['nombre' => 'iPad Air 5ta Gen', 'descripcion' => 'Tablet Apple M1, 10.9", 64GB Wi-Fi, Touch ID'],
                ['nombre' => 'Samsung Galaxy Tab A8', 'descripcion' => 'Tablet Android 10.5", Unisoc Tiger T618, 4GB RAM, 64GB'],
                ['nombre' => 'Lenovo Tab P11 Pro', 'descripcion' => 'Tablet Android 11.5", MediaTek Kompanio 1300T, 6GB RAM'],
                ['nombre' => 'Amazon Fire HD 10', 'descripcion' => 'Tablet 10.1", Octa-core 2.0 GHz, 3GB RAM, 32GB'],
                ['nombre' => 'Huawei MatePad Pro', 'descripcion' => 'Tablet 12.6", Kirin 9000E, 8GB RAM, 256GB, HarmonyOS'],
                ['nombre' => 'ASUS ZenPad 3S 10', 'descripcion' => 'Tablet Android 9.7", MediaTek MT8176, 4GB RAM, 64GB'],
                ['nombre' => 'Xiaomi Pad 5', 'descripcion' => 'Tablet Android 11", Snapdragon 860, 6GB RAM, 128GB'],
            ],

            'Servidores' => [
                ['nombre' => 'Dell PowerEdge R750', 'descripcion' => 'Servidor Rack 2U, Intel Xeon Silver 4314, 64GB RAM, 2x1TB SSD'],
                ['nombre' => 'HP ProLiant DL380 Gen10', 'descripcion' => 'Servidor 2U, Intel Xeon Gold 5218, 128GB RAM, RAID Controller'],
                ['nombre' => 'Lenovo ThinkSystem SR650', 'descripcion' => 'Servidor 2U, Intel Xeon Gold 6248R, 64GB RAM, Hot-swap'],
                ['nombre' => 'Dell PowerEdge T340', 'descripcion' => 'Servidor Torre, Intel Xeon E-2224, 16GB RAM, 4x2TB HDD'],
                ['nombre' => 'HPE ProLiant ML350 Gen10', 'descripcion' => 'Servidor Torre, Intel Xeon Silver 4210R, 32GB RAM'],
                ['nombre' => 'IBM Power System S922', 'descripcion' => 'Servidor POWER9, 64GB RAM, AIX/Linux, Enterprise'],
                ['nombre' => 'Supermicro SuperServer', 'descripcion' => 'Servidor 1U, Intel Xeon E-2278G, 32GB ECC RAM'],
                ['nombre' => 'ASUS RS500A-E10', 'descripcion' => 'Servidor AMD EPYC 7002, 128GB RAM, PCIe 4.0'],
            ],

            'Equipos de Red' => [
                ['nombre' => 'Cisco Catalyst 2960-X', 'descripcion' => 'Switch 48 puertos Gigabit, PoE+, Stackable'],
                ['nombre' => 'HPE Aruba 2530-48G', 'descripcion' => 'Switch Manageable 48 puertos, VLAN, QoS'],
                ['nombre' => 'Ubiquiti UniFi Dream Machine', 'descripcion' => 'Router/Firewall/Controller todo-en-uno, Wi-Fi 6'],
                ['nombre' => 'Cisco ASA 5506-X', 'descripcion' => 'Firewall de seguridad, VPN, 8 puertos, IPS'],
                ['nombre' => 'Netgear ProSafe M4300-52G', 'descripcion' => 'Switch L3 48 puertos Gigabit, Stackable'],
                ['nombre' => 'TP-Link Omada EAP660 HD', 'descripcion' => 'Access Point Wi-Fi 6, 4x4 MU-MIMO, PoE+'],
                ['nombre' => 'Fortinet FortiGate 60F', 'descripcion' => 'Firewall UTM, 10 puertos, VPN SSL, SD-WAN'],
                ['nombre' => 'D-Link DGS-1100-24P', 'descripcion' => 'Switch PoE 24 puertos, Smart Managed, VLAN'],
                ['nombre' => 'Ubiquiti EdgeRouter 4', 'descripcion' => 'Router Enterprise, 3 puertos Gigabit, SFP+'],
                ['nombre' => 'Cisco Meraki MX68', 'descripcion' => 'Security Appliance Cloud-managed, SD-WAN'],
            ],

            'Almacenamiento' => [
                ['nombre' => 'Synology DiskStation DS1821+', 'descripcion' => 'NAS 8 bahías, AMD Ryzen V1500B, 4GB RAM, RAID'],
                ['nombre' => 'QNAP TS-464', 'descripcion' => 'NAS 4 bahías, Intel Celeron N5105, 8GB RAM, 2.5GbE'],
                ['nombre' => 'WD My Cloud EX2 Ultra', 'descripcion' => 'NAS Personal 2 bahías, ARM Cortex-A15, RAID 1'],
                ['nombre' => 'Dell EMC PowerVault ME4012', 'descripcion' => 'Array SAN iSCSI, 12 slots SAS/SATA, Controlador dual'],
                ['nombre' => 'HP MSA 2050', 'descripcion' => 'Storage Array SAN, 24 slots SFF, FC/iSCSI'],
                ['nombre' => 'Seagate Exos X18 16TB', 'descripcion' => 'Disco Duro Empresarial SATA 7200rpm, CMR'],
                ['nombre' => 'Samsung 980 PRO 2TB', 'descripcion' => 'SSD NVMe M.2, PCIe 4.0, 7000MB/s lectura'],
                ['nombre' => 'Buffalo TeraStation 5410DN', 'descripcion' => 'NAS 4 bahías, Intel Atom C3338, 8GB RAM'],
            ],

            'Monitores y Pantallas' => [
                ['nombre' => 'Dell UltraSharp U2720Q', 'descripcion' => 'Monitor 27" 4K IPS, USB-C, 99% sRGB, Height Adjustable'],
                ['nombre' => 'HP EliteDisplay E243', 'descripcion' => 'Monitor 24" Full HD IPS, Pivot, VESA, Business'],
                ['nombre' => 'LG 34WN780-B', 'descripcion' => 'Monitor Ultrawide 34" QHD, IPS, USB-C 60W, Ergonómico'],
                ['nombre' => 'ASUS ProArt PA278QV', 'descripcion' => 'Monitor 27" QHD, 100% sRGB, Calman Verified, Delta E<2'],
                ['nombre' => 'BenQ SW271', 'descripcion' => 'Monitor Profesional 27" 4K, 99% Adobe RGB, Hardware Calibration'],
                ['nombre' => 'Samsung Odyssey G7 32"', 'descripcion' => 'Monitor Gaming 32" QHD, 240Hz, 1ms, HDR600, Curvo'],
                ['nombre' => 'Acer Predator XB273K', 'descripcion' => 'Monitor Gaming 27" 4K, 144Hz, G-SYNC, HDR400'],
                ['nombre' => 'ViewSonic VP2468', 'descripcion' => 'Monitor Profesional 24" Full HD, 100% sRGB, Pivot'],
                ['nombre' => 'AOC 24G2', 'descripcion' => 'Monitor Gaming 24" Full HD, 144Hz, 1ms, IPS, FreeSync'],
                ['nombre' => 'Philips 276E8VJSB', 'descripcion' => 'Monitor 27" 4K IPS, Ultra Slim, Flicker-Free'],
            ],

            'Teclados y Mouse' => [
                ['nombre' => 'Logitech MX Keys', 'descripcion' => 'Teclado Inalámbrico Iluminado, USB-C, Multi-device'],
                ['nombre' => 'Microsoft Ergonomic Desktop', 'descripcion' => 'Kit Teclado y Mouse Ergonómico, Wireless, Split Layout'],
                ['nombre' => 'Corsair K95 RGB Platinum', 'descripcion' => 'Teclado Mecánico Gaming, Cherry MX Speed, RGB, Macro'],
                ['nombre' => 'Dell Premier KM717', 'descripcion' => 'Kit Teclado y Mouse Wireless, Elegante, Business'],
                ['nombre' => 'Logitech MX Master 3', 'descripcion' => 'Mouse Inalámbrico Avanzado, MagSpeed, USB-C, Ergonómico'],
                ['nombre' => 'Razer DeathAdder V3', 'descripcion' => 'Mouse Gaming, Sensor Focus Pro 30K, 90h batería'],
                ['nombre' => 'Apple Magic Keyboard', 'descripcion' => 'Teclado Inalámbrico, Lightning, Teclas tijera, macOS'],
                ['nombre' => 'HP 970 Programmable', 'descripcion' => 'Teclado Inalámbrico Programable, Teclas función, Business'],
                ['nombre' => 'Steelseries Apex 7', 'descripcion' => 'Teclado Mecánico Gaming, OLED Smart Display, RGB'],
                ['nombre' => 'Logitech G Pro X', 'descripcion' => 'Teclado Mecánico Gaming, GX Blue Clicky, Tenkeyless'],
            ],

            'Dispositivos de Audio' => [
                ['nombre' => 'Jabra Evolve2 85', 'descripcion' => 'Auriculares Bluetooth ANC, UC Optimized, 37h batería'],
                ['nombre' => 'Poly Voyager Focus 2', 'descripcion' => 'Auriculares Inalámbricos ANC, 19h batería, Smart Sensors'],
                ['nombre' => 'Bose QuietComfort 45', 'descripcion' => 'Auriculares Bluetooth ANC, 24h batería, TriPort'],
                ['nombre' => 'Sennheiser Momentum 3', 'descripcion' => 'Auriculares Premium Bluetooth, ANC, 17h batería'],
                ['nombre' => 'Logitech Zone Wireless', 'descripcion' => 'Auriculares UC Bluetooth, Teams Certified, Noise Cancelling'],
                ['nombre' => 'Sony WH-1000XM4', 'descripcion' => 'Auriculares Bluetooth ANC Premium, 30h batería, LDAC'],
                ['nombre' => 'Audio-Technica ATH-M50xBT', 'descripcion' => 'Auriculares Studio Bluetooth, 40mm drivers, 40h batería'],
                ['nombre' => 'HyperX Cloud Flight S', 'descripcion' => 'Auriculares Gaming Wireless, 7.1 Surround, 30h batería'],
                ['nombre' => 'Blue Yeti', 'descripcion' => 'Micrófono USB Condensador, Pickup patterns, Zero-latency'],
                ['nombre' => 'Rode PodMic', 'descripcion' => 'Micrófono Dinámico USB, Broadcast Quality, Internal Pop Shield'],
            ],

            'Impresoras' => [
                ['nombre' => 'HP LaserJet Pro M404dn', 'descripcion' => 'Impresora Láser Mono, 38ppm, Dúplex, Red, 350 hojas'],
                ['nombre' => 'Canon imageRUNNER ADVANCE DX C3835i', 'descripcion' => 'Multifuncional Color A3, 35ppm, Scan, Fax, Cloud'],
                ['nombre' => 'Epson EcoTank ET-4760', 'descripcion' => 'Multifuncional Tinta Continua, Wi-Fi, ADF, Dúplex'],
                ['nombre' => 'Brother HL-L3270CDW', 'descripcion' => 'Impresora Láser Color, Wi-Fi, Dúplex, 25ppm, Compacta'],
                ['nombre' => 'Xerox VersaLink C405', 'descripcion' => 'Multifuncional Color A4, 36ppm, ConnectKey, Mobile Print'],
                ['nombre' => 'HP OfficeJet Pro 9010e', 'descripcion' => 'Multifuncional Tinta, Wi-Fi, ADF, Dúplex, HP+ Ready'],
                ['nombre' => 'Ricoh SP 330DN', 'descripcion' => 'Impresora Láser Mono, 31ppm, Dúplex, Red, Compacta'],
                ['nombre' => 'Canon PIXMA G6020', 'descripcion' => 'Multifuncional Tinta Continua, Wi-Fi, ADF, Business'],
            ],

            'Escáneres y Digitalizadores' => [
                ['nombre' => 'Fujitsu ScanSnap iX1600', 'descripcion' => 'Escáner Documental ADF, 40ppm, Wi-Fi, Touch Screen'],
                ['nombre' => 'Epson WorkForce DS-780N', 'descripcion' => 'Escáner Documental Red, 45ppm, ADF 100 hojas, TWAIN'],
                ['nombre' => 'Canon DR-C225W', 'descripcion' => 'Escáner Compacto Wi-Fi, 25ppm, ADF 30 hojas, USB'],
                ['nombre' => 'Brother ADS-2700W', 'descripcion' => 'Escáner Escritorio Wi-Fi, 35ppm, ADF 50 hojas, Dúplex'],
                ['nombre' => 'HP ScanJet Pro 2500 f1', 'descripcion' => 'Escáner Plano/ADF, 20ppm, USB 3.0, TWAIN/ISIS'],
                ['nombre' => 'Kodak ScanStation 730EX', 'descripcion' => 'Escáner Departamental, 70ppm, ADF 200 hojas, Red'],
            ],

            'Teléfonos IP' => [
                ['nombre' => 'Cisco IP Phone 8851', 'descripcion' => 'Teléfono IP Color 5", 5 líneas, HD Audio, PoE'],
                ['nombre' => 'Yealink SIP-T54W', 'descripcion' => 'Teléfono IP Wi-Fi, 4.3" Color, 16 líneas, USB'],
                ['nombre' => 'Grandstream GRP2613', 'descripcion' => 'Teléfono IP 2.8" Color, 3 líneas, HD Audio, PoE'],
                ['nombre' => 'Poly VVX 411', 'descripcion' => 'Teléfono IP 4.3" Color, 12 líneas, HD Audio, Gigabit'],
                ['nombre' => 'Fanvil X5S', 'descripcion' => 'Teléfono IP 3.5" Color, 6 líneas, HD Audio, PoE'],
                ['nombre' => 'Snom D785', 'descripcion' => 'Teléfono IP 4.3" Color, 12 líneas, HD Audio, Gigabit'],
            ],

            'Equipos de Videoconferencia' => [
                ['nombre' => 'Logitech Rally Bar', 'descripcion' => 'Sistema Videoconferencia All-in-One, 4K, AI, USB'],
                ['nombre' => 'Poly Studio X30', 'descripcion' => 'Sistema Videoconferencia 4K, Dual Camera, Wi-Fi, Teams'],
                ['nombre' => 'Cisco Webex Room Kit', 'descripcion' => 'Sistema Videoconferencia Inteligente, 4K, AI, Cloud'],
                ['nombre' => 'Crestron Flex UC-M130-T', 'descripcion' => 'Sistema UC Nativo Teams, 4K Camera, Touch Control'],
                ['nombre' => 'AVer CAM520 Pro2', 'descripcion' => 'Cámara PTZ 4K, 18x Zoom, Auto-framing, USB 3.0'],
                ['nombre' => 'Jabra PanaCast 50', 'descripcion' => 'Cámara Panorámica 180°, 4K, AI, Room Solutions'],
            ],

            'Sistemas de Seguridad' => [
                ['nombre' => 'Hikvision DS-2CD2385G1-I', 'descripcion' => 'Cámara IP 8MP 4K, WDR, IR 30m, PoE, H.265+'],
                ['nombre' => 'Dahua DH-IPC-HFW4431R-Z', 'descripcion' => 'Cámara IP 4MP, Varifocal 2.7-13.5mm, IR 60m, PoE'],
                ['nombre' => 'Axis M3067-P', 'descripcion' => 'Cámara Domo Fija 6MP, WDR, IR, PoE+, Zipstream'],
                ['nombre' => 'Uniview IPC3232ER3-DV', 'descripcion' => 'Cámara IP 2MP, StarLight, IR 30m, PoE, Smart IR'],
                ['nombre' => 'Honeywell HC30WE5R2', 'descripcion' => 'Cámara Bullet 5MP, TDN, IR 40m, PoE, True WDR'],
                ['nombre' => 'Bosch FLEXIDOME IP 6000', 'descripcion' => 'Cámara Domo 2MP, IVA, WDR, Day/Night, PoE+'],
            ],

            'Smartphones Corporativos' => [
                ['nombre' => 'iPhone 14 Pro', 'descripcion' => 'Smartphone iOS 6.1", A16 Bionic, 128GB, 5G, Pro Camera'],
                ['nombre' => 'Samsung Galaxy S23', 'descripcion' => 'Smartphone Android 6.1", Snapdragon 8 Gen 2, 256GB, 5G'],
                ['nombre' => 'Google Pixel 7 Pro', 'descripcion' => 'Smartphone Android 6.7", Google Tensor G2, 128GB, 5G'],
                ['nombre' => 'OnePlus 11', 'descripcion' => 'Smartphone Android 6.7", Snapdragon 8 Gen 2, 256GB, 5G'],
                ['nombre' => 'Xiaomi 13 Pro', 'descripcion' => 'Smartphone Android 6.73", Snapdragon 8 Gen 2, 256GB, 5G'],
                ['nombre' => 'Huawei P60 Pro', 'descripcion' => 'Smartphone HarmonyOS 6.67", Snapdragon 8+ Gen 1, 256GB'],
            ],
        ];

        $estados = ['Disponible', 'Asignado', 'Reparación', 'Dañado'];
        $contador = 0;

        foreach ($categorias as $categoria) {
            $dispositivos = $dispositivosPorCategoria[$categoria->nombre] ?? [];

            // Si la categoría no tiene dispositivos predefinidos, crear genéricos
            if (empty($dispositivos)) {
                $dispositivos = [];
                for ($i = 1; $i <= 5; $i++) {
                    $dispositivos[] = [
                        'nombre' => $categoria->nombre . ' #' . $i,
                        'descripcion' => 'Dispositivo de ' . $categoria->nombre . ' modelo estándar #' . $i
                    ];
                }
            }

            foreach ($dispositivos as $index => $dispositivo) {
                $estado = $estados[array_rand($estados)];
                $area = $areas->random();
                $usuario = null;

                // Si está asignado, asignar un usuario
                if ($estado === 'Asignado' && $usuarios->count() > 0) {
                    $usuario = $usuarios->random();
                }

                // Generar número de serie único
                $numeroSerie = 'DEV-' . strtoupper(substr($categoria->nombre, 0, 3)) . '-' .
                              str_pad($contador + 1, 4, '0', STR_PAD_LEFT);

                // Fecha de compra aleatoria en los últimos 3 años
                $fechaCompra = Carbon::now()->subDays(rand(1, 1095));

                Dispositivo::create([
                    'nombre' => $dispositivo['nombre'],
                    'descripcion' => $dispositivo['descripcion'],
                    'categoria_id' => $categoria->id,
                    'numero_serie' => $numeroSerie,
                    'estado' => $estado,
                    'area_id' => $area->id,
                    'usuario_id' => $usuario?->id,
                    'fecha_compra' => $fechaCompra,
                ]);

                $contador++;
            }

            $this->command->info("✅ Dispositivos creados para categoría: {$categoria->nombre} (" . count($dispositivos) . " dispositivos)");
        }

        $this->command->info('🎉 Seeder de dispositivos completado exitosamente!');
        $this->command->info("📱 Total dispositivos creados: {$contador}");
        $this->command->info("📂 Categorías procesadas: " . $categorias->count());
        $this->command->info("🏢 Áreas utilizadas: " . $areas->count());

        // Mostrar estadísticas por estado
        foreach ($estados as $estado) {
            $count = Dispositivo::where('estado', $estado)->count();
            $this->command->info("   - {$estado}: {$count} dispositivos");
        }
    }
}
