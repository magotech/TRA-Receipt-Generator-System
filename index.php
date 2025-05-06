<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'receipt_db');
define('TAX_RATE', 0.18); // 18% VAT

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Africa/Dar_es_Salaam');

// Initialize database connection
try {
    $db = new PDO("mysql:host=".DB_HOST, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    // Create database if not exists
    $db->exec("CREATE DATABASE IF NOT EXISTS ".DB_NAME);
    $db->exec("USE ".DB_NAME);
    
    // Create tables with correct schema
    $db->exec("CREATE TABLE IF NOT EXISTS receipts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        shop_name VARCHAR(255) NOT NULL,
        address VARCHAR(255) NOT NULL,
        district_council VARCHAR(255) NOT NULL,
        contact VARCHAR(50) NOT NULL,
        tin VARCHAR(50) NOT NULL,
        serial_number VARCHAR(50) NOT NULL,
        uin VARCHAR(100) NOT NULL,
        tax_office VARCHAR(255) NOT NULL,
        receipt_number VARCHAR(50) NOT NULL,
        date DATE NOT NULL,
        time TIME NOT NULL,
        items JSON NOT NULL,
        total_excl_tax DECIMAL(12, 3) NOT NULL,
        tax_amount DECIMAL(12, 3) NOT NULL,
        total_incl_tax DECIMAL(12, 3) NOT NULL,
        payment_method VARCHAR(50) NOT NULL,
        verification_code VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Function to generate TIN (9 digits)
function generateTIN() {
    return str_pad(mt_rand(100000000, 999999999), 9, '0', STR_PAD_LEFT);
}

// Function to generate Serial Number (13 digits)
function generateSerialNumber() {
    return str_pad(mt_rand(1000000000000, 9999999999999), 13, '0', STR_PAD_LEFT);
}

// Function to generate UIN (28 alphanumeric characters)
function generateUIN() {
    $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $uin = '';
    for ($i = 0; $i < 28; $i++) {
        $uin .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $uin;
}

// Initialize variables
$error = '';
$receipt = [];
$currentInput = [
    'shop_name' => 'NREHT COFFEE SHOP',
    'address' => 'P.O.BOX 640 DOCOMA',
    'district_council' => 'DODOMA CITY COUNCIL',
    'contact' => '0743303408',
    'tin' => generateTIN(),
    'serial_number' => generateSerialNumber(),
    'uin' => generateUIN(),
    'tax_office' => 'TRX OFFICE DOCOMA',
    'receipt_number' => '1/'.mt_rand(1000,9999),
    'date' => date('Y-m-d'),
    'time' => date('H:i:s'),
    'payment_method' => 'CASH',
    'items' => [
        ['name' => 'JUICE', 'price' => '8000.000', 'tax_status' => 'inclusive']
    ]
];

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Store current input for repopulation
        $currentInput = $_POST;
        $currentInput['items'] = $_POST['items'] ?? [];
        
        // Process items and calculate totals
        $items = [];
        $totalExclTax = 0;
        $taxAmount = 0;
        
        foreach ($currentInput['items'] as $item) {
            if (empty($item['name']) || !isset($item['price']) || empty($item['price'])) {
                continue;
            }
            
            $price = (float)$item['price'];
            $items[] = [
                'name' => $item['name'],
                'price' => $price,
                'tax_status' => $item['tax_status'] ?? 'inclusive'
            ];
            
            if (($item['tax_status'] ?? 'inclusive') === 'inclusive') {
                $exclPrice = $price / (1 + TAX_RATE);
                $totalExclTax += $exclPrice;
                $taxAmount += $price - $exclPrice;
            } else {
                $totalExclTax += $price;
                $taxAmount += $price * TAX_RATE;
            }
        }
        
        if (empty($items)) {
            throw new Exception("Please add at least one valid item");
        }
        
        // Check for item overflow (max 10 items to fit 1/4 A4)
        if (count($items) > 10) {
            throw new Exception("Too many items for 1/4 A4 receipt. Maximum 10 items allowed.");
        }
        
        // Generate verification code
        $verificationCode = str_pad(mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
        
        // Save to database
        $stmt = $db->prepare("INSERT INTO receipts (
            shop_name, address, district_council, contact, tin, serial_number, uin, tax_office,
            receipt_number, date, time, items, total_excl_tax, tax_amount,
            total_incl_tax, payment_method, verification_code
        ) VALUES (
            :shop_name, :address, :district_council, :contact, :tin, :serial_number, :uin, :tax_office,
            :receipt_number, :date, :time, :items, :total_excl_tax, :tax_amount,
            :total_incl_tax, :payment_method, :verification_code
        )");
        
        $stmt->execute([
            ':shop_name' => $currentInput['shop_name'],
            ':address' => $currentInput['address'],
            ':district_council' => $currentInput['district_council'],
            ':contact' => $currentInput['contact'],
            ':tin' => $currentInput['tin'],
            ':serial_number' => $currentInput['serial_number'],
            ':uin' => $currentInput['uin'],
            ':tax_office' => $currentInput['tax_office'],
            ':receipt_number' => $currentInput['receipt_number'],
            ':date' => $currentInput['date'],
            ':time' => $currentInput['time'],
            ':items' => json_encode($items),
            ':total_excl_tax' => $totalExclTax,
            ':tax_amount' => $taxAmount,
            ':total_incl_tax' => $totalExclTax + $taxAmount,
            ':payment_method' => $currentInput['payment_method'],
            ':verification_code' => $verificationCode
        ]);
        
        $receiptId = $db->lastInsertId();
        header("Location: ?id=$receiptId");
        exit;
    } catch(Exception $e) {
        $error = $e->getMessage();
    }
}

// Get Receipt Data
if (isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM receipts WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $receipt = $stmt->fetch();
    if ($receipt) {
        $receipt['items'] = json_decode($receipt['items'], true);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt Generator</title>
    <link href="https://fonts.googleapis.com/css2?family=Bitmatrix&display=swap" rel="stylesheet">
    <style>
        @font-face {
            font-family: 'BitMatrix-H1';
            src: url('BitMatrix-H1.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
    <style>
        /* 1/4 A4 sizing (74mm width, dynamic height) */
        .receipt {
            width: 74mm;
            font-family: 'BitMatrix-H1', 'Courier', monospace;
            font-size: 11px;
            line-height: 1.3;
            background: transparent;
            padding: 5mm;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
            position: relative;
            overflow: hidden;
        }

        /* TRA Logo Background Pattern with 4 logos, gap, 2 logos, gap, repeating */
        .receipt::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('tralogo.png');
            background-repeat: repeat;
            background-size: 0.9774cm 1.0361cm; /* Logo size */
            opacity: 0.27;
            z-index: 0;
            /* 4 logos (25%), gap (25%), 2 logos (25%), gap (25%) */
            -webkit-mask-image: linear-gradient(90deg, 
                rgba(0,0,0,1) 0%, rgba(0,0,0,1) 25%, /* 4 logos */
                rgba(0,0,0,0) 25%, rgba(0,0,0,0) 50%, /* Gap */
                rgba(0,0,0,0.5) 50%, rgba(0,0,0,0.5) 75%, /* 2 logos */
                rgba(0,0,0,0) 75%, rgba(0,0,0,0) 100% /* Gap */);
            mask-image: linear-gradient(90deg, 
                rgba(0,0,0,1) 0%, rgba(0,0,0,1) 25%, 
                rgba(0,0,0,0) 25%, rgba(0,0,0,0) 50%, 
                rgba(0,0,0,0.5) 50%, rgba(0,0,0,0.5) 75%, 
                rgba(0,0,0,0) 75%, rgba(0,0,0,0) 100%);
        }

        /* Ensure content is readable over the background */
        .receipt > * {
            position: relative;
            z-index: 1;
            padding: 2px 4px;
            /* Semi-transparent background for text readability */
            background-color: rgba(255, 255, 255, 0.7);
        }

        /* Remove background for non-text elements */
        .dashed-line, #qrcode {
            background-color: transparent;
        }

        .receipt-line {
            margin: 2px 0;
            display: flex;
            justify-content: space-between;
        }
        
        .receipt-header {
            text-align: center;
            margin-bottom: 4px;
        }
        
        .receipt-footer {
            text-align: center;
            margin-top: 4px;
        }
        
        .dashed-line {
            border-top: 1px dashed #000;
            margin: 4px 0;
            position: relative;
            z-index: 1;
        }
        
        .verification-code {
            text-align: center;
            margin: 6px 0;
            font-weight: bold;
            letter-spacing: 1px;
            font-size: 12px;
        }
        
        .centered-section {
            text-align: center;
            margin: 4px 0;
        }
        
        .items-section {
            margin: 4px 0;
        }
        
        #qrcode {
            display: flex;
            justify-content: center;
            margin: 6px 0;
        }
        
        #qrcode img {
            width: 70px;
            height: 70px;
        }

        .tin-number {
            font-weight: bold;
            font-size: 12px;
            text-align: center;
        }

        .total-inclusive {
            font-weight: bold;
            font-size: 12px;
        }

        @media print {
            body * { visibility: hidden; }
            #receipt, #receipt * { visibility: visible; }
            #receipt {
                position: absolute;
                left: 0;
                top: 0;
                width: 74mm;
                box-shadow: none;
            }
        }
    </style>
</head>
<body class="bg-gray-100 p-4">
    <div class="max-w-6xl mx-auto">
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Input Form Section -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Receipt Details</h2>
                <form method="post" class="space-y-4">
                    <!-- Business Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1">Shop Name</label>
                            <input type="text" name="shop_name" value="<?= htmlspecialchars($currentInput['shop_name']) ?>" class="w-full p-2 border rounded" required>
                        </div>
                        <div>
                            <label class="block mb-1">Address</label>
                            <input type="text" name="address" value="<?= htmlspecialchars($currentInput['address']) ?>" class="w-full p-2 border rounded" required>
                        </div>
                        <div>
                            <label class="block mb-1">District Council</label>
                            <input type="text" name="district_council" value="<?= htmlspecialchars($currentInput['district_council']) ?>" class="w-full p-2 border rounded" required>
                        </div>
                        <div>
                            <label class="block mb-1">Contact</label>
                            <input type="text" name="contact" value="<?= htmlspecialchars($currentInput['contact']) ?>" class="w-full p-2 border rounded" required>
                        </div>
                        <div>
                            <label class="block mb-1">TIN</label>
                            <input type="text" name="tin" value="<?= htmlspecialchars($currentInput['tin']) ?>" class="w-full p-2 border rounded" required>
                        </div>
                        <div>
                            <label class="block mb-1">Serial Number</label>
                            <input type="text" name="serial_number" value="<?= htmlspecialchars($currentInput['serial_number']) ?>" class="w-full p-2 border rounded" required>
                        </div>
                        <div>
                            <label class="block mb-1">UIN</label>
                            <input type="text" name="uin" value="<?= htmlspecialchars($currentInput['uin']) ?>" class="w-full p-2 border rounded" required>
                        </div>
                        <div>
                            <label class="block mb-1">Tax Office</label>
                            <input type="text" name="tax_office" value="<?= htmlspecialchars($currentInput['tax_office']) ?>" class="w-full p-2 border rounded" required>
                        </div>
                    </div>

                    <!-- Receipt Info -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block mb-1">Receipt Number</label>
                            <input type="text" name="receipt_number" value="<?= htmlspecialchars($currentInput['receipt_number']) ?>" class="w-full p-2 border rounded" required>
                        </div>
                        <div>
                            <label class="block mb-1">Date</label>
                            <input type="date" name="date" value="<?= htmlspecialchars($currentInput['date']) ?>" class="w-full p-2 border rounded" required>
                        </div>
                        <div>
                            <label class="block mb-1">Time</label>
                            <input type="time" name="time" value="<?= htmlspecialchars($currentInput['time']) ?>" class="w-full p-2 border rounded" required>
                        </div>
                    </div>

                    <!-- Items Section -->
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Items</h3>
                        <div id="items-container" class="space-y-2">
                            <?php foreach ($currentInput['items'] as $index => $item): ?>
                                <div class="item-row grid grid-cols-12 gap-2 items-center">
                                    <input type="text" name="items[<?= $index ?>][name]" value="<?= htmlspecialchars($item['name']) ?>" class="col-span-5 p-2 border rounded" placeholder="Item name" required>
                                    <input type="number" step="0.001" name="items[<?= $index ?>][price]" value="<?= htmlspecialchars($item['price']) ?>" class="col-span-3 p-2 border rounded" placeholder="Price" required>
                                    <select name="items[<?= $index ?>][tax_status]" class="col-span-3 p-2 border rounded">
                                        <option value="exclusive" <?= ($item['tax_status'] ?? 'inclusive') === 'exclusive' ? 'selected' : '' ?>>Exclusive of Tax</option>
                                        <option value="inclusive" <?= ($item['tax_status'] ?? 'inclusive') === 'inclusive' ? 'selected' : '' ?>>Inclusive of Tax</option>
                                    </select>
                                    <button type="button" onclick="removeItem(this)" class="col-span-1 bg-red-500 text-white p-2 rounded">×</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="flex space-x-2 mt-2">
                            <button type="button" onclick="addItem()" class="bg-gray-500 text-white px-3 py-1 rounded">+ Add Item</button>
                            <button type="button" onclick="clearItems()" class="bg-red-500 text-white px-3 py-1 rounded">Clear All</button>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label class="block mb-1">Payment Method</label>
                        <select name="payment_method" class="w-full p-2 border rounded">
                            <option value="CASH" <?= ($currentInput['payment_method'] ?? 'CASH') === 'CASH' ? 'selected' : '' ?>>Cash</option>
                            <option value="CREDIT CARD" <?= ($currentInput['payment_method'] ?? '') === 'CREDIT CARD' ? 'selected' : '' ?>>Credit Card</option>
                            <option value="MOBILE PAYMENT" <?= ($currentInput['payment_method'] ?? '') === 'MOBILE PAYMENT' ? 'selected' : '' ?>>Mobile Payment</option>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">
                        Generate Receipt
                    </button>
                </form>
            </div>

            <!-- Receipt Preview Section -->
            <div class="space-y-4">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4">Receipt Preview</h2>
                    <div id="receipt" class="receipt">
                        <?php if (!empty($receipt)): ?>
                            <div class="receipt-footer">*** START OF LEGAL RECEIPT ****</div>
                            
                            <div class="centered-section">
                                <p class="font-bold"><?= strtoupper(htmlspecialchars($receipt['shop_name'])) ?></p>
                                <p><?= htmlspecialchars($receipt['address']) ?></p>
                                <p><?= htmlspecialchars($receipt['district_council']) ?></p>
                                <p><?= htmlspecialchars($receipt['contact']) ?></p>
                            </div>
                            
                            <div class="dashed-line"></div>
                            
                            <div class="tin-number">
                                <p>TIN <?= htmlspecialchars($receipt['tin']) ?></p>
                            </div>
                            
                            <div class="centered-section">
                                <p>SERIAL NUMBER <?= htmlspecialchars($receipt['serial_number']) ?></p>
                            </div>
                            
                            <div class="centered-section">
                                <p>UIN: <?= htmlspecialchars($receipt['uin']) ?></p>
                            </div>
                            
                            <div class="centered-section">
                                <p><?= htmlspecialchars($receipt['tax_office']) ?></p>
                            </div>
                            
                            <div class="dashed-line"></div>
                            
                            <div class="receipt-line">
                                <span>RECEIPT NUMBER <?= htmlspecialchars($receipt['receipt_number']) ?></span>
                            </div>
                            <div class="receipt-line">
                                <span>DATE <?= date('d-m-Y', strtotime($receipt['date'])) ?></span>
                                <span>TIME <?= htmlspecialchars($receipt['time']) ?></span>
                            </div>
                            <div class="receipt-line">
                                <span>ECR: 01</span>
                                <span>OP: 01</span>
                            </div>
                            
                            <div class="dashed-line"></div>
                            
                            <div class="items-section">
                                <?php foreach ($receipt['items'] as $item): ?>
                                    <div class="receipt-line">
                                        <span><?= strtoupper(htmlspecialchars($item['name'])) ?>........</span>
                                        <span><?= number_format($item['price'], 3, '.', '') ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="dashed-line"></div>
                            
                            <div class="receipt-line">
                                <span>TOTAL EXCLUSIVE OF TRX........</span>
                                <span><?= number_format($receipt['total_excl_tax'], 3, '.', '') ?></span>
                            </div>
                            
                            <div class="dashed-line"></div>
                            
                            <div class="receipt-line">
                                <span>TRX - 18.00%........</span>
                                <span><?= number_format($receipt['tax_amount'], 3, '.', '') ?></span>
                            </div>
                            
                            <div class="dashed-line"></div>
                            
                            <div class="receipt-line">
                                <span>TOTAL TRX........</span>
                                <span><?= number_format($receipt['tax_amount'], 3, '.', '') ?></span>
                            </div>
                            
                            <div class="dashed-line"></div>
                            
                            <div class="receipt-line total-inclusive">
                                <span>TOTAL INCLUSIVE OF TRX........</span>
                                <span><?= number_format($receipt['total_incl_tax'], 3, '.', '') ?></span>
                            </div>
                            
                            <div class="dashed-line"></div>
                            
                            <div class="receipt-line">
                                <span><?= htmlspecialchars($receipt['payment_method']) ?>........</span>
                                <span><?= number_format($receipt['total_incl_tax'], 3, '.', '') ?></span>
                            </div>
                            <div class="receipt-line">
                                <span>ITEMS NUMBER</span>
                                <span><?= count($receipt['items']) ?></span>
                            </div>
                            
                            <div class="dashed-line"></div>
                            
                            <div class="verification-code">RECEIPT VERIFICATION CODE</div>
                            <div class="verification-code"><?= htmlspecialchars($receipt['verification_code']) ?></div>
                            
                            <div id="qrcode"></div>
                            <script>
                                if (document.getElementById('qrcode')) {
                                    new QRCode(document.getElementById('qrcode'), {
                                        text: "https://www.tra.go.tz/verify/<?= htmlspecialchars($receipt['verification_code']) ?>",
                                        width: 70,
                                        height: 70,
                                        colorDark: "#000000",
                                        colorLight: "#ffffff",
                                        correctLevel: QRCode.CorrectLevel.H
                                    });
                                }
                            </script>
                            
                            <div class="dashed-line"></div>
                            
                            <div class="receipt-footer">*** END OF LEGAL RECEIPT ****</div>
                        <?php else: ?>
                            <p class="text-center text-gray-500">No receipt data available</p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-2 mt-4 justify-center">
                        <button onclick="printReceipt()" class="bg-blue-500 text-white px-4 py-2 rounded">Print</button>
                        <button onclick="downloadPDF()" class="bg-green-500 text-white px-4 py-2 rounded">Download PDF</button>
                        <button onclick="downloadJPG()" class="bg-purple-500 text-white px-4 py-2 rounded">Download JPG</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Define receipt data for JavaScript
        const receipt = <?php echo !empty($receipt) ? json_encode($receipt) : '{}'; ?>;

        // Item Management
        let itemCount = <?= count($currentInput['items']) ?>;
        
        function addItem() {
            const container = document.getElementById('items-container');
            const newRow = document.createElement('div');
            newRow.className = 'item-row grid grid-cols-12 gap-2 items-center';
            newRow.innerHTML = `
                <input type="text" name="items[${itemCount}][name]" class="col-span-5 p-2 border rounded" placeholder="Item name" required>
                <input type="number" step="0.001" name="items[${itemCount}][price]" class="col-span-3 p-2 border rounded" placeholder="Price" required>
                <select name="items[${itemCount}][tax_status]" class="col-span-3 p-2 border rounded">
                    <option value="exclusive">Exclusive of Tax</option>
                    <option value="inclusive" selected>Inclusive of Tax</option>
                </select>
                <button type="button" onclick="removeItem(this)" class="col-span-1 bg-red-500 text-white p-2 rounded">×</button>
            `;
            container.appendChild(newRow);
            itemCount++;
        }
        
        function removeItem(button) {
            button.closest('.item-row').remove();
        }
        
        function clearItems() {
            if (confirm('Are you sure you want to clear all items?')) {
                document.getElementById('items-container').innerHTML = '';
                itemCount = 0;
                addItem();
            }
        }
        
        // Receipt Actions
        function printReceipt() {
            window.print();
        }
        
        function downloadPDF() {
            const { jsPDF } = window.jspdf;
            const receiptElement = document.getElementById('receipt');
            
            // Define 1/4 A4 width and minimum height in mm
            const targetWidth = 74; // mm
            const minHeight = 105; // mm
            const dpi = 300; // Standard print DPI
            const mmToPx = dpi / 25.4; // Convert mm to pixels

            // Calculate pixel width
            const pixelWidth = targetWidth * mmToPx;

            // Temporarily set receipt width for capture
            const originalWidth = receiptElement.style.width;
            receiptElement.style.width = `${targetWidth}mm`;

            // Get actual content height in pixels
            const contentHeightPx = receiptElement.scrollHeight;
            // Convert to mm
            let contentHeightMm = contentHeightPx / mmToPx;
            // Ensure minimum height
            contentHeightMm = Math.max(contentHeightMm, minHeight);
            const pixelHeight = contentHeightMm * mmToPx;

            html2canvas(receiptElement, {
                scale: 4, // High resolution
                width: pixelWidth,
                height: pixelHeight,
                windowWidth: pixelWidth,
                windowHeight: pixelHeight,
                useCORS: true,
                logging: false
            }).then(canvas => {
                const imgData = canvas.toDataURL('image/png', 1.0); // Highest quality

                // Create PDF with exact dimensions
                const pdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: [targetWidth, contentHeightMm]
                });

                // Add image to PDF
                pdf.addImage(imgData, 'PNG', 0, 0, targetWidth, contentHeightMm, '', 'FAST');

                // Restore original width
                receiptElement.style.width = originalWidth;

                // Save the PDF
                pdf.save(`receipt_${receipt && receipt.receipt_number ? receipt.receipt_number : new Date().toISOString().replace(/[-:T]/g, '').slice(0, 14)}.pdf`);
            }).catch(error => {
                console.error('PDF generation failed:', error);
                alert('Failed to generate PDF. Please try again.');
            });
        }
        
        function downloadJPG() {
            const receiptElement = document.getElementById('receipt');
            
            // Define 1/4 A4 width and minimum height in mm
            const targetWidth = 74; // mm
            const minHeight = 105; // mm
            const dpi = 300; // Standard print DPI
            const mmToPx = dpi / 25.4; // Convert mm to pixels

            // Calculate pixel width
            const pixelWidth = targetWidth * mmToPx;

            // Temporarily set receipt width for capture
            const originalWidth = receiptElement.style.width;
            receiptElement.style.width = `${targetWidth}mm`;

            // Get actual content height in pixels
            const contentHeightPx = receiptElement.scrollHeight;
            // Convert to mm
            let contentHeightMm = contentHeightPx / mmToPx;
            // Ensure minimum height
            contentHeightMm = Math.max(contentHeightMm, minHeight);
            const pixelHeight = contentHeightMm * mmToPx;

            html2canvas(receiptElement, {
                scale: 4, // High resolution
                width: pixelWidth,
                height: pixelHeight,
                windowWidth: pixelWidth,
                windowHeight: pixelHeight,
                useCORS: true,
                logging: false
            }).then(canvas => {
                // Restore original width
                receiptElement.style.width = originalWidth;

                const link = document.createElement('a');
                link.download = `receipt_${receipt && receipt.receipt_number ? receipt.receipt_number : new Date().toISOString().replace(/[-:T]/g, '').slice(0, 14)}.jpg`;
                link.href = canvas.toDataURL('image/jpeg', 1.0); // Highest quality
                link.click();
            }).catch(error => {
                console.error('JPG generation failed:', error);
                alert('Failed to generate JPG. Please try again.');
            });
        }
    </script>
</body>
</html>